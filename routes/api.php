<?php

use App\Models\Product;
use App\Services\PaymentReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Cypres Pay
|--------------------------------------------------------------------------
*/

// Endpoint de recherche instantanée (compatible avec app.js)
Route::get('/search', function (Request $request) {
    $q = trim($request->get('q', ''));
    if (strlen($q) < 2) {
        return response()->json(['results' => []]);
    }

    $results = Product::where('nom', 'like', "%{$q}%")
        ->orWhere('reference', 'like', "%{$q}%")
        ->orWhere('marque', 'like', "%{$q}%")
        ->take(8)
        ->get()
        ->map(function ($p) {
            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'reference' => $p->reference,
                'marque' => $p->marque,
                'prix_vente' => $p->prix_vente,
                'is_kit' => $p->is_kit,
                'image' => $p->image_principale,
                'url' => route('product.show', ['id' => $p->id, 'slug' => $p->slug]),
            ];
        });

    return response()->json(['results' => $results]);
});

// Endpoint de vérification de reçu à 6 caractères
Route::get('/verifier-recu/{code}', function ($code, PaymentReceiptService $service) {
    $receipt = $service->verifyReceipt($code);
    if (!$receipt) {
        return response()->json(['valid' => false, 'message' => 'Code de reçu introuvable.'], 404);
    }

    return response()->json([
        'valid' => true,
        'code' => $receipt->code_recu,
        'montant' => $receipt->montant,
        'montant_formate' => $receipt->montant_formate,
        'canal' => $receipt->canal,
        'date' => $receipt->date_versement->toIso8601String(),
        'commande' => $receipt->order->numero_commande ?? null,
    ]);
});
