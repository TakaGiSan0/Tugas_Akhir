<?php

namespace App\Http\Controllers;

use App\Models\peserta;

use Illuminate\Http\Request;


class PesertaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('searchQuery');
        $query = Peserta::query();

        if (!empty($searchQuery)) {
            $query->where('badge_no', 'like', "%{$searchQuery}%")
                ->orWhere('employee_name', 'like', "%{$searchQuery}%");
        }

        $user = auth('')->user();

        $peserta = $query->with('user:id,user,updated_at') 
            
            ->select('id', 'badge_no', 'employee_name', 'dept', 'position', 'join_date', 'status', 'category_level','gender', 'user_id')
            ->orderBy('employee_name', 'asc')
            ->paginate(10);

        return view('peserta.index', [
            'peserta' => $peserta,
            'searchQuery' => $searchQuery, 
            'message' => $peserta->isEmpty() ? 'No data found.' : ''
        ]);
       
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('peserta.create')->with('hideSidebar', true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(
            [
                'badge_no' => 'required|string|max:255|unique:pesertas,badge_no',
                'employee_name' => 'required|string|max:255|unique:pesertas,employee_name',
                'dept' => 'required|string|max:255',
                'join_date' => 'required|date',
                'category_level' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'gender' => 'required|string|max:255',

            ],
            [
                'employee_name.unique' => 'Peserta dengan Nama ini sudah ada.',
                'badge_no.unique' => 'Peserta dengan Badge No ini sudah ada.',
            ],
        );

        $peserta = new peserta();

        $peserta->badge_no = $validatedData['badge_no'];
        $peserta->employee_name = $validatedData['employee_name'];
        $peserta->dept = $validatedData['dept'];
        $peserta->join_date = $validatedData['join_date'];
        $peserta->join_date = $validatedData['join_date'];
        $peserta->category_level = $validatedData['category_level'];
        $peserta->position = $validatedData['position'];
        $peserta->gender = $validatedData['gender'];
        $peserta->user_id = auth('')->id();

        $peserta->save();

        return redirect()->route('dashboard.peserta')->with('success', 'Employee succesfully created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Peserta $peserta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Peserta $peserta)
    {
        $this->authorize('update', $peserta);

        return view('peserta.edit', compact('peserta'))->with('hideSidebar', true);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Peserta $peserta)
    {

        $validated = $request->validate(
            [
                'badge_no' => 'required|string|max:255|regex:/^[A-Z0-9\-]+$/|unique:pesertas,badge_no,' . $peserta->id,
                'employee_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'dept' => 'required|string|max:255',
                'join_date' => 'required|date',
                'category_level' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'position' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'gender' => 'required|string|max:255',

            ],
            [
                'badge_no.regex' => 'Badge No may only contain uppercase letters, numbers, and hyphens.',
                'employee_name.regex' => 'Name may only contain letters.',
                'badge_no.unique' => 'A participant with this Badge No already exists.',
            ],
        );

        $peserta->update($validated);

        return redirect()->route('dashboard.peserta')->with('success', 'Employee succesfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $peserta = Peserta::findOrFail($id);

        $peserta->trainingRecords()->delete();

        $this->authorize('delete', $peserta);

        $peserta->delete();

        return redirect()->route('dashboard.peserta')->with('success', 'Employee succesfully deleted.');
    }

    public function getParticipantByBadgeNo($badge_no)
    {
        $participant = peserta::where('badge_no', $badge_no)->first();

        if ($participant) {
            return response()->json($participant);
        } else {
            return response()->json(['error' => 'Participant not found'], 404);
        }
    }
}
