<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Validity of an activation link, in days.
     */
    const ACTIVATION_LINK_TTL_DAYS = 7;

    /**
     * Display a listing of admins
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $this->manageableAdmins();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        $admins = $query->latest()->paginate(15);

        // Status options for filter
        $statuses = [
            (object)['id' => Admin::STATUS_ACTIVE, 'name' => 'Actif', 'slug' => 'active'],
            (object)['id' => Admin::STATUS_INACTIVE, 'name' => 'Inactif', 'slug' => 'inactive'],
            (object)['id' => Admin::STATUS_DEACTIVATED, 'name' => 'Désactivé', 'slug' => 'deactivated'],
        ];

        $roles = $this->roleOptions();

        return view('admins.index', compact('admins', 'statuses', 'roles'));
    }

    /**
     * Export admins data
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $query = $this->manageableAdmins();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        $admins = $query->latest()->get();

        $filename = 'administrateurs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($admins) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Nom',
                'Nom d\'utilisateur',
                'Email',
                'Téléphone',
                'Rôle',
                'Statut',
                'Date de création',
                'Dernière activité',
                'Activation envoyée le',
            ], ';');

            // CSV data
            foreach ($admins as $admin) {
                fputcsv($file, [
                    $admin->id,
                    $admin->name,
                    $admin->username,
                    $admin->email,
                    $admin->phone,
                    $admin->role_name,
                    $admin->status_name,
                    $admin->created_at ? $admin->created_at->format('d/m/Y H:i') : '',
                    $admin->last_activity_at ? $admin->last_activity_at->format('d/m/Y H:i') : 'Jamais',
                    $admin->activation_sent_at ? $admin->activation_sent_at->format('d/m/Y H:i') : '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new admin
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = $this->roleOptions();

        return view('admins.create', compact('roles'));
    }

    /**
     * Store a newly created admin in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:100', 'unique:admins,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(Auth::guard('admin')->user()->manageableRoles())],
        ]);

        // Generate a temporary password and slug
        $validated['password'] = Hash::make(Str::random(12));
        $validated['username'] = Admin::generateUniqueUsername($validated['name']);
        $validated['slug'] = (string) Str::orderedUuid();
        $validated['status'] = Admin::STATUS_INACTIVE;

        $admin = Admin::create($validated);

        return redirect()->route('admins.show', $admin)
            ->with('success', 'Administrateur créé avec succès. Vous pouvez maintenant envoyer le lien d\'activation.');
    }

    /**
     * Display the specified admin
     *
     * @param Admin $admin
     * @return \Illuminate\View\View
     */
    public function show(Admin $admin)
    {
        return view('admins.show', compact('admin'));
    }

    /**
     * Send activation link to admin
     *
     * @param Admin $admin
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendActivation(Admin $admin)
    {
        if ($admin->isActive()) {
            return redirect()->back()
                ->with('error', 'L\'administrateur est déjà actif.');
        }

        if ($admin->isDeactivated()) {
            return redirect()->back()
                ->with('error', 'Ce compte est désactivé : réactivez-le avant d\'envoyer un lien d\'activation.');
        }

        try {
            $admin->sendActivationEmail();
            
            return redirect()->back()
                ->with('success', 'Lien d\'activation envoyé avec succès à ' . $admin->email);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'envoi du lien d\'activation : ' . $e->getMessage());
        }
    }

    /**
     * Activate admin account via token
     *
     * @param string $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function activate(string $token)
    {
        $admin = $this->adminForActivationToken($token);

        if (!$admin) {
            return redirect()->route('login')
                ->with('error', 'Lien d\'activation invalide ou expiré.');
        }

        return view('admins.activate', compact('admin', 'token'));
    }

    /**
     * Process admin activation
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processActivation(Request $request, string $token)
    {
        $admin = $this->adminForActivationToken($token);

        if (!$admin) {
            return redirect()->route('login')
                ->with('error', 'Lien d\'activation invalide ou expiré.');
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin->update([
            'password' => Hash::make($validated['password']),
            'status' => Admin::STATUS_ACTIVE,
            'activation_token' => null,
            'activation_sent_at' => null,
        ]);

        return redirect()->route('login')
            ->with('success', 'Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter.');
    }

    /**
     * Deactivate admin account
     *
     * @param Admin $admin
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(Admin $admin)
    {
        if (!$admin->isActive()) {
            return redirect()->back()
                ->with('error', 'Seul un administrateur actif peut être désactivé.');
        }

        $admin->deactivate();

        return redirect()->back()
            ->with('success', 'Administrateur désactivé avec succès.');
    }

    /**
     * Reactivate admin account
     *
     * @param Admin $admin
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reactivate(Admin $admin)
    {
        // A never-activated admin must set a password through the activation link
        if (!$admin->isDeactivated()) {
            return redirect()->back()
                ->with('error', 'Seul un administrateur désactivé peut être réactivé.');
        }

        $admin->update(['status' => Admin::STATUS_ACTIVE]);

        return redirect()->back()
            ->with('success', 'Administrateur réactivé avec succès.');
    }

    /**
     * Find the admin an activation token belongs to: the link expires and only works
     * for an account that has never been activated.
     */
    private function adminForActivationToken(string $token): ?Admin
    {
        return Admin::where('activation_token', $token)
            ->where('status', Admin::STATUS_INACTIVE)
            ->where('activation_sent_at', '>=', now()->subDays(self::ACTIVATION_LINK_TTL_DAYS))
            ->first();
    }

    /**
     * Admins the current admin is allowed to see and manage.
     */
    private function manageableAdmins()
    {
        $current = Auth::guard('admin')->user();

        return Admin::query()
            ->whereIn('role', $current->manageableRoles())
            ->where('id', '!=', $current->id);
    }

    /**
     * Role options (filters and create form) limited to the roles the current admin can manage.
     */
    private function roleOptions(): array
    {
        $labels = Admin::ROLES;

        return collect(Auth::guard('admin')->user()->manageableRoles())
            ->map(fn ($role) => (object) ['id' => $role, 'name' => $labels[$role], 'slug' => $role])
            ->all();
    }

    /**
     * Remove the specified admin
     *
     * @param Admin $admin
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();

        return redirect()->route('admins.index')
            ->with('success', 'Administrateur supprimé avec succès.');
    }
}