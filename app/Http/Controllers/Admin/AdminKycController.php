<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycProfile;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminKycController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('statut', 'en_attente');
        $query = KycProfile::with('user');

        if ($status !== 'all') {
            $query->where('statut', $status);
        }

        $profiles = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'en_attente' => KycProfile::where('statut', 'en_attente')->count(),
            'valide' => KycProfile::where('statut', 'valide')->count(),
            'rejete' => KycProfile::where('statut', 'rejete')->count(),
            'all' => KycProfile::count(),
        ];

        return view('admin.kyc.index', compact('profiles', 'status', 'counts'));
    }

    public function validateProfile(Request $request, KycProfile $kyc)
    {
        $kyc->update([
            'statut' => 'valide',
            'valide_le' => now(),
            'motif_rejet' => null,
        ]);

        // Mettre à jour automatiquement les commandes en attente KYC pour ce client
        Order::where('user_id', $kyc->user_id)
            ->where('statut', 'en_attente_kyc')
            ->update(['statut' => 'valide']);

        return back()->with('success', "Le dossier KYC de {$kyc->user->nom_complet} a été validé avec succès. Les commandes associées sont débloquées.");
    }

    public function rejectProfile(Request $request, KycProfile $kyc)
    {
        $validated = $request->validate([
            'motif_rejet' => 'required|string|max:255',
        ]);

        $kyc->update([
            'statut' => 'rejete',
            'motif_rejet' => $validated['motif_rejet'],
        ]);

        return back()->with('success', "Le dossier KYC de {$kyc->user->nom_complet} a été rejeté (Motif : {$validated['motif_rejet']}).");
    }
}
