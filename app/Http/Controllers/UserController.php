<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\peserta;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userLogin = auth('web')->user();

        $user = User::with('pesertaLogin')
            ->select('users.*')
            ->leftJoin('pesertas', 'pesertas.user_id_login', '=', 'users.id')
            ->byUserRole($userLogin)
            ->orderBy('pesertas.employee_name', 'asc')
            ->paginate(10);


        $message = $user->isEmpty() ? 'No results found for your search.' : '';

        $user->getCollection()->map(function ($user_data) use ($userLogin) {

            $user_data->can_be_edited = false;

            if ($userLogin->role === 'Super Admin' && optional($userLogin->pesertaLogin)->dept === 'IT') {
                $user_data->can_be_edited = true;
            } elseif ($userLogin->role === 'Admin' && optional($userLogin->pesertaLogin)) {
                if ($user_data->role === 'User') {
                    $user_data->can_be_edited = true;
                }
            } elseif ($userLogin->role === 'Super Admin') {
                if ($user_data->role === 'Admin' || $user_data-> role === 'User') {
                    $user_data->can_be_edited = true;
                }
            }


            return $user_data; 
        });

        return response()->view(
            'user.index',
            [
                'user' => $user,
                'message' => $message,

            ],
            200,
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pesertaTanpaUser = Peserta::whereNull('user_id_login')->get();

        return view('user.create', [
            'pesertaTanpaUser' => $pesertaTanpaUser,
            'user' => auth('')->user(),
        ])->with('hideSidebar', true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(
            [
                'employee_name' => 'required|string|max:255',
                'user' => 'required|string|max:255|unique:users,user',
                'password' => 'required|string',

            ],
            [
                'user.unique' => 'A user with this name already exists.',
            ],
        );

        $selectedPeserta = Peserta::where('employee_name', $validatedData['employee_name'])->first();

       
        if (!$selectedPeserta) {
            return back()->withErrors(['employe_name' => 'Peserta yang dipilih tidak valid.']);
        }

        $user = new User();

        $user->user = $validatedData['user'];

        $userRole = auth('')->user()->role;


        if ($userRole === 'Admin') {
            $user->role = 'User';
        } elseif ($userRole === 'Super Admin') {
            $user->role = $request->input('role'); 
        } else {
            abort(403, 'Unauthorized action.');
        }

        $user->password = bcrypt($validatedData['password']);

        $user->save();

        $selectedPeserta->user_id_login = $user->id;
        $selectedPeserta->save();

        return redirect()->route('user.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       
        return response()->json([], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $user = User::with('pesertaLogin')->findOrFail($id);

        $pesertaAvailableForSelection = Peserta::select('id', 'employee_name', 'badge_no', 'user_id_login')
            ->whereNull('user_id_login')
            ->orWhere('user_id_login', $user->id)
            ->get();

        return view('user.edit', [
            'user' => $user,
            'pesertaAvailableForSelection' => $pesertaAvailableForSelection,
        ])->with('hideSidebar', true);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id); 
        $validatedData = $request->validate(
            [
                'employee_name' => 'required|string|max:255', 
                'user' => 'required',
                'string',
                'max:255',
                'password' => 'nullable|string',
            ],
            [
                'user.unique' => 'A user with this name already exists.',
            ],
        );

        $newSelectedPeserta = Peserta::where('employee_name', $validatedData['employee_name'])->first();

        if (!$newSelectedPeserta) {
            return back()->withErrors(['employee_name' => 'Peserta yang dipilih tidak valid.'])->withInput();
        }

        $oldPeserta = $user->peserta;

        if ($oldPeserta && $oldPeserta->id !== $newSelectedPeserta->id) {
            $oldPeserta->user_id_login = null; 
            $oldPeserta->save(); 
        }

        $newSelectedPeserta->user_id_login = $user->id;
        $newSelectedPeserta->save(); 

        $user->user = $validatedData['user']; 

        if ($request->filled('password')) {
            $user->password = bcrypt($validatedData['password']);
        }

        $userRole = auth('')->user()->role;

        if ($userRole === 'Admin') {
            if ($user->role !== 'Super Admin') {
                $user->role = 'user';
            }
        } elseif ($userRole === 'Super Admin') {
            $user->role = $request->input('role', 'user'); 
        } else {
            abort(403, 'Unauthorized action.'); 
        }

        $user->save();  

        return redirect()->route('user.index')->with('success', 'User updated successfully.'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('user.index')->with('success', 'User successfully deleted.');
    }
}
