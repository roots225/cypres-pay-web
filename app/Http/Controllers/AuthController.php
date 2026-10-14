<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Affiche la page de connexion
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion par Téléphone ou Email
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identifiant' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifiant = trim($credentials['identifiant']);
        $field = filter_var($identifiant, FILTER_VALIDATE_EMAIL) ? 'email' : 'telephone';

        // Nettoyage de l'indicatif +225 si présent
        if ($field === 'telephone') {
            $identifiant = preg_replace('/^\+?225/', '', $identifiant);
            $identifiant = preg_replace('/\s+/', '', $identifiant);
        }

        $remember = $request->boolean('remember', true);

        if (Auth::attempt([$field => $identifiant, 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            return $this->redirectBasedOnRole($user)
                ->with('success', "Bienvenue, {$user->prenom} ! Connexion réussie.");
        }

        return back()
            ->withInput($request->only('identifiant', 'remember'))
            ->with('error', 'Identifiant ou mot de passe incorrect.');
    }

    /**
     * Connexion Démo Rapide en 1-clic pour les tests
     */
    public function demoLogin(string $role)
    {
        $allowedRoles = ['admin', 'commercial', 'caissier', 'client'];
        if (! in_array($role, $allowedRoles)) {
            abort(404);
        }

        $user = User::where('role', $role)->first();
        if (! $user) {
            return redirect()->route('login')->with('error', "Aucun compte démo trouvé pour le rôle {$role}.");
        }

        Auth::login($user);
        request()->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', "Connecté en tant que " . ucfirst($role) . " ({$user->nom_complet})");
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Redirection automatique selon le rôle de l'utilisateur
     */
    protected function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'commercial' => redirect()->route('commercial.dashboard'),
            'caissier' => redirect()->route('caisse.dashboard'),
            default => redirect()->route('client.dashboard'),
        };
    }
}
