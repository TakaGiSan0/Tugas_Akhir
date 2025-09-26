<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\peserta;
use App\Models\training_record;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;



class EmployeeController extends Controller
{
    public function index(Request $request)
    {
       
        $deptFilter = $request->input('dept', []); 
        $searchQuery = $request->input('searchQuery');

        if (is_string($deptFilter)) {
            $deptFilter = explode(',', $deptFilter); 
        }

       
        $uniqueDepts = Peserta::select('dept')->distinct()->pluck('dept')->toArray(); 

        $user = Auth::user();

        $query = Peserta::byDept()
            ->select("id", "badge_no", "employee_name", "dept", "position")
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(function ($subQuery) use ($searchQuery) {
                    $subQuery->where('badge_no', 'like', "%{$searchQuery}%")
                        ->orWhere('employee_name', 'like', "%{$searchQuery}%");
                });
            })
            ->when(!empty($deptFilter), function ($query) use ($deptFilter) {
                $query->whereIn('dept', $deptFilter);
            })
            ->orderBy('employee_name', 'asc');




        $peserta_records = $query->paginate(10);


        return view('content.employee', [
            'peserta_records' => $peserta_records,
            'deptFilter' => $deptFilter, 
            'searchQuery' => $searchQuery,
            'uniqueDepts' => $uniqueDepts, 
        ]);
    }

    /**
     * Show the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $peserta = Peserta::with([
            'trainingRecords' => function ($query) {
                $query->where('status', 'Completed')
                    ->select([
                        'training_records.id as training_id',
                        'training_records.training_name',
                        'training_records.doc_ref',
                        'training_records.trainer_name',
                        'training_records.rev',
                        'training_records.station',
                        'training_records.category_id',
                        'training_records.date_start',
                        'training_records.date_end',
                    ])
                    ->withPivot('level', 'final_judgement')
                    ->with(['trainingCategory:id,name']);
            }
        ])->findOrFail($id);

        if (!$peserta) {
            return response()->json(['error' => 'Peserta not found'], 404);
        }

        
        $all_records = $peserta
            ->trainingRecords() 
            ->with(['trainingCategory:id,name'])
            ->where('status', 'Completed')
            ->get()
            ->map(function ($record) {
                return [
                    'training_name' => $record->training_name,
                    'doc_ref' => $record->doc_ref,
                    'job_skill' => $record->job_skill,
                    'trainer_name' => $record->trainer_name,
                    'rev' => $record->rev,
                    'station' => $record->station,
                    'skill_code' => $record->skill_code,
                    'date_formatted' => $record->formatted_date_range, 
                    'category_id' => $record->category_id,
                    'level' => $record->pivot->level,
                    'final_judgement' => $record->pivot->final_judgement,
                    'training_category' => $record->trainingCategory ? $record->trainingCategory->name : null,
                ];
            });

       
        $grouped_records = $all_records->groupBy('category_id');

        
        if ($all_records->isEmpty()) {
            $grouped_records = null;
        }

        return response()->json([
            'peserta' => $peserta,
            'grouped_records' => $grouped_records,
        ]);
    }

    public function downloadPdf($id)
    {
        
        $peserta = Peserta::with(['trainingRecords' => function ($query) {
            $query->withPivot('level', 'final_judgement');
        }])->findOrFail($id);

        if (!$peserta) {
            return response()->json(['error' => 'Peserta not found'], 404);
        }

        $all_records = $peserta
            ->trainingRecords() 
            ->with(['trainingCategory:id,name'])
            ->get();

        $grouped_records = $all_records->groupBy('category_id');

        if ($all_records->isEmpty()) {
            $grouped_records = null;
        }

        $pdf = PDF::loadView('pdf.training_employee', [
            'peserta' => $peserta,
            'grouped_records' => $grouped_records,
        ]);

        return $pdf->download($peserta->employee_name  . ' Training Record.pdf');
    }
}
