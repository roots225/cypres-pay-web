<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    /**
     * Affiche la page ou données du profil administrateur
     */
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Met à jour le profil de l'administrateur connecté
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'telephone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], [
            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'new_password.min' => 'Le nouveau mot de passe doit comporter au moins 6 caractères.',
            'new_password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ]);

        // Si l'utilisateur souhaite changer de mot de passe
        if (!empty($validated['new_password'])) {
            if (empty($validated['current_password']) || !Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])->withInput();
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->prenom = $validated['prenom'];
        $user->nom = $validated['nom'];
        $user->email = $validated['email'];
        $user->telephone = $validated['telephone'];
        $user->save();

        return back()->with('success', 'Vos informations de profil administrateur ont été mises à jour avec succès.');
    }
}
