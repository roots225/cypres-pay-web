<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentReceipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * 1. Gestion séparée : CLIENTS
     */
    public function clients(Request $request)
    {
        $search = $request->get('q');
        $query = User::where('role', 'client')->with(['kycProfile', 'orders']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ville', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(15)->withQueryString();
        $totalClients = User::where('role', 'client')->count();

        return view('admin.users.clients', compact('clients', 'search', 'totalClients'));
    }

    /**
     * 2. Gestion séparée : COMMERCIAUX & APPORTEURS
     */
    public function commerciaux(Request $request)
    {
        $search = $request->get('q');
        $query = User::where('role', 'commercial');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('own_referral_code', 'like', "%{$search}%");
            });
        }

        $commerciaux = $query->latest()->paginate(15)->withQueryString();

        // Calcul des métriques pour chaque commercial (clients parrainés, volume, commissions)
        foreach ($commerciaux as $com) {
            $clientCount = User::where('ref_code', $com->own_referral_code)->count();
            $clientIds = User::where('ref_code', $com->own_referral_code)->pluck('id');
            $salesVolume = (int) Order::whereIn('user_id', $clientIds)->sum('montant_total');
            $com->referred_clients_count = $clientCount;
            $com->sales_volume = $salesVolume;
            $com->commission_earned = (int) round($salesVolume * 0.03);
        }

        $totalCommerciaux = User::where('role', 'commercial')->count();

        return view('admin.users.commerciaux', compact('commerciaux', 'search', 'totalCommerciaux'));
    }

    /**
     * 3. Gestion séparée : CAISSIERS D'AGENCE
     */
    public function caissiers(Request $request)
    {
        $search = $request->get('q');
        $query = User::where('role', 'caissier');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('commune', 'like', "%{$search}%");
            });
        }

        $caissiers = $query->latest()->paginate(15)->withQueryString();

        foreach ($caissiers as $cai) {
            $cai->receipts_count = PaymentReceipt::where('agent_id', $cai->id)->count();
            $cai->total_collected = (int) PaymentReceipt::where('agent_id', $cai->id)->sum('montant');
            $cai->today_collected = (int) PaymentReceipt::where('agent_id', $cai->id)
                ->whereDate('created_at', today())
                ->sum('montant');
        }

        $totalCaissiers = User::where('role', 'caissier')->count();

        return view('admin.users.caissiers', compact('caissiers', 'search', 'totalCaissiers'));
    }

    /**
     * 4. Gestion séparée : ADMINISTRATEURS
     */
    public function admins(Request $request)
    {
        $search = $request->get('q');
        $query = User::where('role', 'admin');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->latest()->paginate(15)->withQueryString();
        $totalAdmins = User::where('role', 'admin')->count();

        return view('admin.users.admins', compact('admins', 'search', 'totalAdmins'));
    }

    /**
     * Formulaire de création d'un utilisateur
     */
    public function create(Request $request)
    {
        $role = $request->get('role', 'client');
        $commerciaux = User::where('role', 'commercial')->get();

        return view('admin.users.form', [
            'user' => new User(['role' => $role]),
            'isEdit' => false,
            'role' => $role,
            'commerciaux' => $commerciaux,
        ]);
    }

    /**
     * Enregistrement d'un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:15|unique:users,telephone',
            'role' => 'required|in:client,commercial,caissier,admin',
            'password' => 'required|string|min:6',
            'ville' => 'nullable|string|max:100',
            'commune' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:255',
            'ref_code' => 'nullable|string|max:50',
            'own_referral_code' => 'nullable|string|max:50|unique:users,own_referral_code',
        ]);

        // Auto-génération de code pour les commerciaux si omis
        if ($validated['role'] === 'commercial' && empty($validated['own_referral_code'])) {
            $validated['own_referral_code'] = 'COM-' . strtoupper(Str::random(5));
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return $this->redirectAfterUserAction($user->role, "L'utilisateur {$user->nom_complet} a été créé avec succès.");
    }

    /**
     * Formulaire d'édition d'un utilisateur
     */
    public function edit(User $user)
    {
        $commerciaux = User::where('role', 'commercial')->where('id', '!=', $user->id)->get();

        return view('admin.users.form', [
            'user' => $user,
            'isEdit' => true,
            'role' => $user->role,
            'commerciaux' => $commerciaux,
        ]);
    }

    /**
     * Mise à jour d'un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'telephone' => ['required', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:client,commercial,caissier,admin',
            'password' => 'nullable|string|min:6',
            'ville' => 'nullable|string|max:100',
            'commune' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:255',
            'ref_code' => 'nullable|string|max:50',
            'own_referral_code' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return $this->redirectAfterUserAction($user->role, "Le compte de {$user->nom_complet} a été mis à jour.");
    }

    /**
     * Suppression d'un utilisateur
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte administrateur.');
        }

        $role = $user->role;
        $name = $user->nom_complet;
        $user->delete();

        return $this->redirectAfterUserAction($role, "L'utilisateur {$name} a été supprimé.");
    }

    protected function redirectAfterUserAction(string $role, string $message)
    {
        $route = match ($role) {
            'commercial' => 'admin.users.commerciaux',
            'caissier' => 'admin.users.caissiers',
            'admin' => 'admin.users.admins',
            default => 'admin.users.clients',
        };

        return redirect()->route($route)->with('success', $message);
    }
}
