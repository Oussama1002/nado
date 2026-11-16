<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Modules administrables : clé (= permission "access.<clé>") => libellé.
     * Un utilisateur « member » ne voit/accède qu'aux modules cochés ; un
     * « admin » a tout (voir le helper admin_can()).
     */
    public const MODULES = [
        'annonces'    => 'Annonces',
        'demandes'    => 'Demandes',
        'clients'     => 'Clients',
        'ventes'      => 'Ventes',
        'reprises'    => 'Reprises',
        'finances'    => 'Financement',
        'encheres'    => 'Auto-Enchère',
        'estimations' => 'Estimations',
        'legalchecks' => 'Situation légale',
        'catalogue'   => 'Catalogue (marques, modèles, villes, couleurs)',
        'contenu'     => 'Contenu & médias (articles, stories, sliders, radio…)',
        'publicite'   => 'Publicité',
        'discussions' => 'Discussions',
        'users'       => 'Utilisateurs (admin)',
        'settings'    => 'Réglages',
    ];

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
            'user'        => new User,
            'modules'     => self::MODULES,
            'userModules' => [],
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
            'modules'   => ['nullable', 'array'],
            'modules.*' => ['string', Rule::in(array_keys(self::MODULES))],
        ]);

        $data['password'] = Hash::make($request->password);
        $user = User::create(collect($data)->except('modules')->all());

        $this->ensureRole($user, $data['role']);
        $user->assignRole($data['role']);
        $this->syncModules($user, $request->input('modules', []));

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
            'user'        => $user,
            'modules'     => self::MODULES,
            'userModules' => $this->currentModules($user),
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
            'modules'   => ['nullable', 'array'],
            'modules.*' => ['string', Rule::in(array_keys(self::MODULES))],
        ]);
        // Only change the password when a new one was actually typed; otherwise
        // the previous behaviour overwrote it with Hash::make(null) on every edit.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update(collect($data)->except('modules')->all());

        $this->ensureRole($user, $data['role']);
        $user->syncRoles([$data['role']]);
        $this->syncModules($user, $request->input('modules', []));

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
     * Les clés de modules actuellement autorisées pour un utilisateur
     * (dérivées de ses permissions "access.<clé>").
     */
    private function currentModules(User $user): array
    {
        return $user->getPermissionNames()
            ->filter(fn ($p) => str_starts_with($p, 'access.'))
            ->map(fn ($p) => substr($p, strlen('access.')))
            ->values()
            ->all();
    }

    /**
     * Remplace les permissions d'accès d'un utilisateur par la liste de modules
     * cochés. Les permissions sont créées au besoin sur le guard de l'utilisateur
     * (admin), comme ensureRole() le fait pour les rôles.
     */
    private function syncModules(User $user, array $modules): void
    {
        $guard   = Guard::getDefaultName($user);
        $modules = array_values(array_intersect($modules, array_keys(self::MODULES)));

        $permissions = array_map(function ($module) use ($guard) {
            return Permission::firstOrCreate([
                'name'       => 'access.'.$module,
                'guard_name' => $guard,
            ]);
        }, $modules);

        $user->syncPermissions($permissions);
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
