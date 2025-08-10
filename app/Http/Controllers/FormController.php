<?php

namespace App\Http\Controllers;

use App\Models\Escape;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\training_record;
use App\Models\category;
use App\Models\peserta;
use App\Models\training_skill;
use App\Models\training_comment;

use Barryvdh\DomPDF\Facade\pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('search');
        $selectedYear = $request->input('year');

        $years = Training_Record::selectRaw('YEAR(date_end) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $user = auth('')->user();

        $training_records = Training_Record::with('latestComment')
            ->byUserRole($user)
            ->when($searchQuery, function ($query, $searchQuery) {
                return $query->where('training_name', 'like', "%{$searchQuery}%");
            })
            ->when($selectedYear, function ($query, $selectedYear) {
                return $query->whereYear('date_start', $selectedYear);
            })
            ->orderByRaw("
        CASE 
            WHEN status = 'Waiting Approval' THEN 0 
            WHEN status = 'Pending' THEN 1 
            ELSE 2 
        END
    ")
            ->orderBy('date_start', 'desc')
            ->orderBy('date_end', 'desc')
            ->paginate(10);

        $jobskill = training_skill::select('id', 'job_skill', 'skill_code')->get();
        return view('dashboard.index', compact('training_records', 'years', 'searchQuery', 'selectedYear', 'jobskill'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userRole = auth('')->user()->role;

        if (!in_array($userRole, ['Super Admin', 'Admin'])) {
            abort(403, 'Unauthorized action.');
        }


        $categories = category::all();
        $peserta = peserta::all();

        return view('form.form', compact('categories', 'peserta'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules(), $this->validationMessages());


        if (auth()->guard()->user()->role === 'Super Admin') {
            $status = 'Pending';
        } elseif (auth()->guard()->user()->role === 'Admin') {
            $status = 'Waiting Approval';
        }

        if (strtotime($data['date_start']) > strtotime($data['date_end'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['date_start' => 'Start date tidak boleh lebih besar dari End date.']);
        }

        $skillCodes = $request->input('skill_codes', []);

        $trainingSkillIds = Training_Skill::whereIn('skill_code', $skillCodes)->pluck('id')->toArray();


        $filePath = null; 
        if ($request->hasFile('attachment')) {
            $pdfFile = $request->file('attachment');

            $fileName = str_replace(' ', '+', $pdfFile->getClientOriginalName());

            try {
                $filePath = $pdfFile->storeAs('attachment', $fileName, 'public');
                Log::info('File berhasil disimpan: ' . $filePath);
            } catch (\Exception $e) {
                Log::error('File upload error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal mengunggah file. Silakan coba lagi.');
            }
        }

        $minutes = $data['training_duration']; 

        $hours = floor($minutes / 60);  
        $remainingMinutes = $minutes % 60;  

        $formattedTime = sprintf("%d:%02d", $hours, $remainingMinutes);


        $trainingRecord = Training_Record::create([
            'training_name' => $data['training_name'],
            'doc_ref' => $data['doc_ref'],
            'trainer_name' => $data['trainer_name'],
            'rev' => $data['rev'],
            'station' => $data['station'],
            'date_start' => $data['date_start'],
            'date_end' => $data['date_end'],
            'training_duration' => $formattedTime,
            'category_id' => $data['category_id'],
            'status' => $status,
            'attachment' => $filePath,
            'user_id' => auth('web')->id(),
        ]);

        $trainingRecord->comments()->create([
            'comment' => null,
            'approval' => 'Pending',
        ]);

        $trainingRecord->training_Skills()->sync($trainingSkillIds);

        $participants = $data['participants'] ?? [];

        if (!empty($participants)) {
            foreach ($participants as $participant) {
                $peserta = Peserta::where('badge_no', $participant['badge_no'])->first();
                if ($peserta) {
                    $trainingRecord->pesertas()->attach($peserta->id, [
                        'level' => $participant['level'],
                        'final_judgement' => $participant['final_judgement'],
                        'license' => $participant['license'],
                        'theory_result' => $participant['theory_result'],
                        'practical_result' => $participant['practical_result'],
                    ]);
                }

                $hasilPeserta = DB::table('hasil_peserta')
                    ->where('peserta_id', $peserta->id)
                    ->where('training_record_id', $trainingRecord->id)
                    ->latest('id')
                    ->first();

               
            }
        }

        return redirect()->route('dashboard.index')->with('success', 'Training successfully created.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $record = Training_Record::select('status', 'user_id', 'attachment')->with('user')->findOrFail($id);

        $history = training_comment::where('training_record_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($history->isEmpty()) {
            Log::info('History tidak ditemukan untuk ID: ' . $id);
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $lastComment = $history->last();

        $pathToFile = $record->attachment; 

        $attachmentUrl = $pathToFile
            ? asset("storage/" . ($pathToFile))
            : null;

        return response()->json([
            'history' => $history,
            'comment' => $lastComment->comment,
            'approval' => $lastComment->approval,
            'status' => $record->status,
            'attachment' => $attachmentUrl,
            'requestor_name' => $record->user->user ?? 'Unknown User',
            'created_at' => $record->created_at?->format('d M Y H:i'),
            'updated_at' => $record->updated_at?->format('d M Y H:i'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $trainingRecord = Training_Record::with([
            'pesertas',
            'training_skills' => function ($query) {
                $query->withTrashed();
            }
        ])->findOrFail($id);
        $time = $trainingRecord->training_duration;

        if (!empty($time) && str_contains($time, ':')) {
            $parts = explode(':', $time);
            if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                list($hours, $minutes) = $parts;
                $minutesInTotal = ($hours * 60) + $minutes;
                $formattedTime = $minutesInTotal;
            } else {
                $formattedTime = 0;
            }
        } else {
            $formattedTime = 0;
        }

        $participants = $trainingRecord->pesertas;
        $skill_code = $trainingRecord->training_skills;

        $categories = Category::all();

        return view('form.edit', compact('trainingRecord', 'categories', 'participants', 'formattedTime', 'skill_code'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $data = $request->validate($this->validationRules());
        $trainingRecord = Training_Record::findOrFail($id);

        $currentAttachmentPath = $trainingRecord->attachment;

        if ($request->hasFile('attachment')) {
            $pdfFile = $request->file('attachment');

            $originalName = $pdfFile->getClientOriginalName();
            $fileName = str_replace(' ', '+', $originalName); 

            try {
                $newFilePath = $pdfFile->storeAs('attachment', $fileName, 'public');

                if ($newFilePath && $currentAttachmentPath) {
                    if (Storage::disk('public')->exists($currentAttachmentPath)) {
                        Storage::disk('public')->delete($currentAttachmentPath);
                        Log::info('File lama berhasil dihapus: ' . $currentAttachmentPath);
                    } else {
                        Log::warning('File lama tidak ditemukan untuk dihapus: ' . $currentAttachmentPath);
                    }
                }
                $trainingRecord->attachment = $newFilePath; 

            } catch (\Exception $e) {
                Log::error('File upload error saat update: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal mengunggah file baru. Silakan coba lagi.');
            }
        }
        $skillCodes = $request->input('skill_codes', []);

        $trainingSkillIds = Training_Skill::whereIn('skill_code', $skillCodes)->pluck('id')->toArray();
        $minutes = $request->input('training_duration'); 

        $hours = floor($minutes / 60); 
        $remainingMinutes = $minutes % 60;  

        $formattedTime = sprintf("%d:%02d", $hours, $remainingMinutes);

        $trainingRecord->update([
            'training_name' => $data['training_name'],
            'doc_ref' => $data['doc_ref'],
            'trainer_name' => $data['trainer_name'],
            'rev' => $data['rev'],
            'station' => $data['station'],
            'date_start' => $data['date_start'],
            'date_end' => $data['date_end'],
            'category_id' => $data['category_id'],
            'attachment' => $trainingRecord->attachment,
            'training_duration' => $formattedTime
        ]);

        $trainingRecord->training_Skills()->sync($trainingSkillIds);


        $pesertaToSync = [];
        

        foreach ($data['participants'] as $participant) {
            $peserta = Peserta::where('badge_no', $participant['badge_no'])->first();

            if ($peserta) {
                $pesertaToSync[$peserta->id] = [
                    'level' => $participant['level'],
                    'final_judgement' => $participant['final_judgement'],
                    'license' => $participant['license'],
                    'theory_result' => $participant['theory_result'],
                    'practical_result' => $participant['practical_result'],
                ];

            }
        }

        $trainingRecord->pesertas()->sync($pesertaToSync);

        $relevantHasilPeserta = DB::table('hasil_peserta')
            ->where('training_record_id', $trainingRecord->id)
            ->get();

      
        return redirect()->route('dashboard.index')->with('success', 'Training succesfully updated.');
    }

    public function updateComment(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:255',
            'approval' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);


        $trainingRecord = Training_Record::findOrFail($id);

    
        $trainingRecord->status = $validated['status'];
        $trainingRecord->save();

        
        $comment = training_comment::create([
            'training_record_id' => $trainingRecord->id,
            'approval' => $validated['approval'],
            'comment' => $validated['comment'],

        ]);


        return redirect()->back()->with('success', 'Comment Succesfully Updated.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $trainingRecord = training_record::findOrFail($id);

    
        $this->authorize('delete', $trainingRecord);

    
        $trainingRecord->delete();


        return redirect()->route('dashboard.index')->with('success', 'Training succesfully deleted.');
    }

    private function validationRules()
    {
        return [
            'training_name' => 'required|string|max:255',
            'doc_ref' => 'required|string|max:255',

            'trainer_name' => 'required|string|max:255',
            'rev' => 'required|string|max:255',
            'station' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'training_duration' => 'required|integer',
            'attachment' => 'required|file|mimes:pdf|max:2048',
            'category_id' => 'required|integer|exists:categories,id',
            'participants.*.badge_no' => 'max:255',
            'participants.*.employee_name' => 'max:255',
            'participants.*.dept' => 'max:255',
            'participants.*.position' => 'max:255',
            'participants.*.level' => 'max:255',
            'participants.*.final_judgement' => 'max:255',
            'participants.*.license' => 'nullable|max:255',
            'participants.*.theory_result' => 'max:255',
            'participants.*.practical_result' => 'max:255',
        ];
    }

    private function validationMessages()
    {
        return [
            'doc_ref.unique' => 'Pelatihan sudah ada.',
        ];
    }

    public function jobs_skill_store(Request $request)
    {
        $request->validate([
            'job_skill' => 'required|string|max:255',
            'skill_code' => 'required|string|max:100',
        ]);

        training_skill::create([
            'job_skill' => $request->job_skill,
            'skill_code' => $request->skill_code,
        ]);

        return redirect()->back()->with('success', 'Job Skill successfully created');
    }

    public function jobs_skill_destroy($id)
    {
        $trainingSkill = Training_Skill::find($id);

        if (!$trainingSkill) {
            return redirect()->route('dashboard.index')->with('error', 'Skill tidak ditemukan.');
        }

        $trainingSkill->delete(); 

        return redirect()->route('dashboard.index')->with('success', 'Job Skill Succesfully Deleted. (soft deleted).');
    }

    public function getJobSkill($skillCode)
    {
        $skill = Training_Skill::where('skill_code', $skillCode)->first();

        if ($skill) {
            return response()->json([
                'job_skill' => $skill->job_skill,
                'id' => $skill->id
            ]);
        }

        return response()->json([
            'job_skill' => null,
            'id' => null
        ], 404);
    }
}
