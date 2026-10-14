<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCartController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFormulaController;
use App\Http\Controllers\Admin\AdminKycController;
use App\Http\Controllers\Admin\AdminMediaController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminReceiptController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SimulatorController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Cypres Pay Côte d'Ivoire
|--------------------------------------------------------------------------
*/

// ── Pages Publiques Principales
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalogue', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalogue/{slug}', [CatalogController::class, 'index'])->name('catalog.category');
Route::get('/kits-maison', [CatalogController::class, 'kits'])->name('kits');
Route::get('/flash-ventes', [CatalogController::class, 'flash'])->name('flash');
Route::get('/produits/{id}-{slug?}', [ProductController::class, 'show'])->name('product.show');

// ── Moteur Financier & Outils Publics
Route::get('/simulateur', [SimulatorController::class, 'index'])->name('simulator');
Route::get('/verifier', [VerificationController::class, 'index'])->name('verifier');
Route::get('/comment-ca-marche', function () {
    return view('how-it-works');
})->name('how-it-works');

// ── Panier & Sauvegarde Temps Réel
Route::prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/reprendre/{token}', [CartController::class, 'resume'])->name('resume');
});

Route::prefix('cart')->name('cart.api.')->group(function () {
    Route::get('/data', [CartController::class, 'data'])->name('data');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::post('/step', [CartController::class, 'updateStep'])->name('step');
    Route::post('/sync', [CartController::class, 'sync'])->name('sync');
});

// ── Commande & Checkout BNPL
Route::get('/commander', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/commander', [CheckoutController::class, 'process'])->name('checkout.process');

// ── Authentification & Démo Login
Route::get('/connexion', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/connexion', [AuthController::class, 'login'])->name('login.post');
Route::get('/demo-login/{role}', [AuthController::class, 'demoLogin'])->name('login.demo');
Route::match(['get', 'post'], '/deconnexion', [AuthController::class, 'logout'])->name('logout');
Route::get('/inscription', function () {
    return view('auth.register');
})->name('register');

// ── Portails Spécialisés (Client, Commercial, Caisse)
Route::prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'client'])->name('dashboard');
});

Route::prefix('commercial')->name('commercial.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'commercial'])->name('dashboard');
});

Route::prefix('caisse')->name('caisse.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'caisse'])->name('dashboard');
    Route::post('/encaissement', [DashboardController::class, 'encaissement'])->name('encaissement');
});

// ══════════════════════════════════════════════════════════════════
// ── TABLEAU DE BORD ADMINISTRATEUR (Réservé au rôle 'admin')
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Vue d'ensemble & KPIs
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. Gestion Séparée des Utilisateurs par Type (Clients, Commerciaux, Caissiers, Admins)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/clients', [AdminUserController::class, 'clients'])->name('clients');
        Route::get('/commerciaux', [AdminUserController::class, 'commerciaux'])->name('commerciaux');
        Route::get('/caissiers', [AdminUserController::class, 'caissiers'])->name('caissiers');
        Route::get('/admins', [AdminUserController::class, 'admins'])->name('admins');
        Route::get('/creer', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}/editer', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });

    // 3. Gestion du Catalogue Produits
    Route::prefix('produits')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/creer', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('/{product}/editer', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [AdminProductController::class, 'update'])->name('update');
        Route::patch('/{product}/toggle/{field}', [AdminProductController::class, 'toggle'])->name('toggle');
        Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy');
    });

    // 4. Gestion des Catégories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
        Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
    });

    // 5. Gestion des Formules BNPL (Échéances & Taux)
    Route::prefix('formules')->name('formulas.')->group(function () {
        Route::get('/', [AdminFormulaController::class, 'index'])->name('index');
        Route::post('/', [AdminFormulaController::class, 'store'])->name('store');
        Route::put('/{formula}', [AdminFormulaController::class, 'update'])->name('update');
        Route::patch('/{formula}/toggle', [AdminFormulaController::class, 'toggle'])->name('toggle');
        Route::delete('/{formula}', [AdminFormulaController::class, 'destroy'])->name('destroy');
    });

    // 6. Gestion des Commandes & Échéanciers
    Route::prefix('commandes')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/statut', [AdminOrderController::class, 'updateStatus'])->name('update_status');
        Route::post('/echeance/{installment}/regler', [AdminOrderController::class, 'markInstallmentPaid'])->name('mark_installment_paid');
    });

    // 6b. Gestion des Paniers & Paniers Abandonnés
    Route::prefix('paniers-abandonnes')->name('carts.')->group(function () {
        Route::get('/', [AdminCartController::class, 'index'])->name('index');
        Route::get('/{cart}', [AdminCartController::class, 'show'])->name('show');
        Route::post('/{cart}/relance-wa', [AdminCartController::class, 'markWhatsAppSent'])->name('whatsapp');
        Route::post('/{cart}/convertir', [AdminCartController::class, 'convertToOrder'])->name('convert');
        Route::delete('/{cart}', [AdminCartController::class, 'destroy'])->name('destroy');
    });

    // 7. Gestion des Dossiers KYC (Validation / Rejet)
    Route::prefix('kyc')->name('kyc.')->group(function () {
        Route::get('/', [AdminKycController::class, 'index'])->name('index');
        Route::post('/{kyc}/valider', [AdminKycController::class, 'validateProfile'])->name('validate');
        Route::post('/{kyc}/rejeter', [AdminKycController::class, 'rejectProfile'])->name('reject');
    });

    // 8. Gestion des Quittances & Reçus de Paiement (Codes 6 caractères)
    Route::prefix('quittances')->name('receipts.')->group(function () {
        Route::get('/', [AdminReceiptController::class, 'index'])->name('index');
        Route::post('/', [AdminReceiptController::class, 'store'])->name('store');
        Route::patch('/{receipt}/toggle', [AdminReceiptController::class, 'toggleValid'])->name('toggle');
    });

    // 9. Gestion Universelle des Médias & Fichiers
    Route::prefix('medias')->name('medias.')->group(function () {
        Route::get('/', [AdminMediaController::class, 'index'])->name('index');
        Route::post('/upload', [AdminMediaController::class, 'store'])->name('upload');
        Route::post('/{media}/crop', [AdminMediaController::class, 'crop'])->name('crop');
        Route::put('/{media}', [AdminMediaController::class, 'update'])->name('update');
        Route::delete('/{media}', [AdminMediaController::class, 'destroy'])->name('destroy');
        Route::post('/batch-delete', [AdminMediaController::class, 'batchDestroy'])->name('batch_delete');
        Route::get('/stats', [AdminMediaController::class, 'stats'])->name('stats');
    });

    // 10. Profil Administrateur
    Route::get('/profil', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [AdminProfileController::class, 'update'])->name('profile.update');
});
