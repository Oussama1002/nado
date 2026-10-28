<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.users.index', ['users' => User::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('admin.users.form', [
            'user' => new User,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()->min(4)],
        ]);

        $data['password'] = Hash::make($request->password);
        $user = User::create($data);

        $this->ensureRole($user, $data['role']);
        $user->assignRole($data['role']);

        return to_route('users.index')->with('success', 'user added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {

        return view('admin.users.form', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',  Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()->min(4)],
        ]);
        // Only change the password when a new one was actually typed; otherwise
        // the previous behaviour overwrote it with Hash::make(null) on every edit.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        $this->ensureRole($user, $data['role']);
        $user->syncRoles([$data['role']]);

        return to_route('users.index')->with('success', 'user edits successfully');
    }

    /**
     * Make sure the given role exists for the user's own guard before it is
     * assigned. The User model resolves to the "admin" guard, while the role
     * seeder created roles on the default guard — so a role could be missing
     * and Spatie would throw RoleDoesNotExist.
     */
    private function ensureRole(User $user, string $role): void
    {
        Role::firstOrCreate([
            'name'       => $role,
            'guard_name' => Guard::getDefaultName($user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return to_route('users.index')->with('success', 'user deleted successfully');
    }
}
