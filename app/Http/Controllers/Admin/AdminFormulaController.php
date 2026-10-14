<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallmentFormula;
use Illuminate\Http\Request;

class AdminFormulaController extends Controller
{
    public function index()
    {
        $formulas = InstallmentFormula::orderBy('duree_mois')
            ->orderBy('pourcentage_apport')
            ->get();

        return view('admin.formulas.index', compact('formulas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'duree_mois' => 'required|integer|min:0',
            'nombre_echeances' => 'required|integer|min:1',
            'frequence' => 'required|in:quotidien,hebdomadaire,bimensuel,mensuel,cash',
            'pourcentage_apport' => 'required|integer|min:0|max:100',
            'taux_majoration' => 'required|numeric|min:0',
            'frais_dossier' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:255',
            'actif' => 'boolean',
        ]);

        $validated['frais_dossier'] = $validated['frais_dossier'] ?? 0;
        $validated['actif'] = $request->boolean('actif', true);

        InstallmentFormula::create($validated);

        return redirect()->route('admin.formulas.index')
            ->with('success', "La formule '{$validated['nom']}' a été créée.");
    }

    public function update(Request $request, InstallmentFormula $formula)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'duree_mois' => 'required|integer|min:0',
            'nombre_echeances' => 'required|integer|min:1',
            'frequence' => 'required|in:quotidien,hebdomadaire,bimensuel,mensuel,cash',
            'pourcentage_apport' => 'required|integer|min:0|max:100',
            'taux_majoration' => 'required|numeric|min:0',
            'frais_dossier' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:255',
            'actif' => 'boolean',
        ]);

        $validated['frais_dossier'] = $validated['frais_dossier'] ?? 0;
        $validated['actif'] = $request->boolean('actif');

        $formula->update($validated);

        return redirect()->route('admin.formulas.index')
            ->with('success', "La formule '{$formula->nom}' a été mise à jour.");
    }

    public function toggle(InstallmentFormula $formula)
    {
        $formula->actif = ! $formula->actif;
        $formula->save();

        $status = $formula->actif ? 'activée' : 'désactivée';
        return back()->with('success', "La formule '{$formula->nom}' a été {$status}.");
    }

    public function destroy(InstallmentFormula $formula)
    {
        if ($formula->orders()->count() > 0) {
            return back()->with('error', "Impossible de supprimer la formule '{$formula->nom}' car des commandes y sont déjà rattachées. Vous pouvez la désactiver.");
        }

        $nom = $formula->nom;
        $formula->delete();

        return redirect()->route('admin.formulas.index')
            ->with('success', "La formule '{$nom}' a été supprimée.");
    }
}
