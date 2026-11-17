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

    /**
     * Actions autorisables par module (= permission "access.<module>.<clé>").
     * Mappées aux actions de route par le middleware ModuleAccess :
     *   explore → index/show (+ GET),  add → create/store,
     *   modify  → edit/update (+ POST/PUT/PATCH),  delete → destroy (+ DELETE).
     */
    public const ABILITIES = [
        'explore' => 'Explorer',
        'add'     => 'Ajouter',
        'modify'  => 'Modifier',
        'delete'  => 'Supprimer',
    ];

    public function __construct()
    {
        $this->middleware('module.access', ['only' => ['index', 'show', 'create', 'store', 'update', 'edit', 'destroy']]);
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
            'user'          => new User,
            'modules'       => self::MODULES,
            'abilities'     => self::ABILITIES,
            'userAbilities' => [],
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
            'abilities'     => ['nullable', 'array'],
            'abilities.*'   => ['array'],
            'abilities.*.*' => ['string', Rule::in(array_keys(self::ABILITIES))],
        ]);

        $data['password'] = Hash::make($request->password);
        $user = User::create(collect($data)->except('abilities')->all());

        $this->ensureRole($user, $data['role']);
        $user->assignRole($data['role']);
        $this->syncAbilities($user, $request->input('abilities', []));

        return to_route('admin.users.index')->with('success', 'user added successfully');
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
            'user'          => $user,
            'modules'       => self::MODULES,
            'abilities'     => self::ABILITIES,
            'userAbilities' => $this->currentAbilities($user),
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
            'abilities'     => ['nullable', 'array'],
            'abilities.*'   => ['array'],
            'abilities.*.*' => ['string', Rule::in(array_keys(self::ABILITIES))],
        ]);
        // Only change the password when a new one was actually typed; otherwise
        // the previous behaviour overwrote it with Hash::make(null) on every edit.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update(collect($data)->except('abilities')->all());

        $this->ensureRole($user, $data['role']);
        $user->syncRoles([$data['role']]);
        $this->syncAbilities($user, $request->input('abilities', []));

        return to_route('admin.users.index')->with('success', 'user edits successfully');
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
     * Actions actuellement autorisées par module pour un utilisateur, sous la
     * forme [module => [abilities]], pour (re)cocher le formulaire.
     *
     * - Permissions granulaires "access.<module>.<ability>" → telles quelles.
     * - Ancienne permission "access.<module>" SANS granulaire → accès complet
     *   (les 4 actions) : un « member » créé avant cette fonctionnalité garde
     *   tous ses droits tant qu'un admin ne l'a pas réédité.
     */
    private function currentAbilities(User $user): array
    {
        $granular   = []; // module => [abilities]
        $legacyBase = []; // module => true (ancienne perm "access.<module>")

        foreach ($user->getPermissionNames() as $perm) {
            if (! str_starts_with($perm, 'access.')) {
                continue;
            }
            $parts  = explode('.', substr($perm, strlen('access.')));
            $module = $parts[0];

            if (isset($parts[1])) {
                $granular[$module][] = $parts[1];
            } else {
                $legacyBase[$module] = true;
            }
        }

        $out = [];
        foreach (self::MODULES as $module => $label) {
            if (isset($granular[$module])) {
                // Ordonné et filtré selon ABILITIES, dédoublonné.
                $out[$module] = array_values(array_intersect(
                    array_keys(self::ABILITIES),
                    array_unique($granular[$module])
                ));
            } elseif (isset($legacyBase[$module])) {
                $out[$module] = array_keys(self::ABILITIES); // accès complet hérité
            }
        }

        return $out;
    }

    /**
     * Remplace les permissions d'accès d'un utilisateur par les actions cochées
     * (payload "abilities[module][] = explore|add|modify|delete").
     *
     * On n'écrit QUE des permissions granulaires "access.<module>.<ability>"
     * (jamais la base "access.<module>") : ainsi l'absence d'une action = refus
     * strict côté middleware, sans fallback hérité qui rouvrirait l'accès.
     * Les permissions sont créées au besoin sur le guard de l'utilisateur (admin).
     */
    private function syncAbilities(User $user, array $abilities): void
    {
        $guard = Guard::getDefaultName($user);
        $names = [];

        foreach ($abilities as $module => $abList) {
            if (! array_key_exists($module, self::MODULES) || ! is_array($abList)) {
                continue;
            }
            $abList = array_intersect($abList, array_keys(self::ABILITIES));
            foreach ($abList as $ability) {
                $names['access.'.$module.'.'.$ability] = true;
            }
        }

        $permissions = array_map(function ($name) use ($guard) {
            return Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => $guard,
            ]);
        }, array_keys($names));

        $user->syncPermissions($permissions);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return to_route('admin.users.index')->with('success', 'user deleted successfully');
    }
}
