@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <div
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-tertiary text-[12px] font-extrabold uppercase tracking-wider mb-3 border border-emerald-100">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                Vérification Officielle Anti-Fraude
            </div>
            <h1 class="text-3xl font-extrabold text-on-surface tracking-tight">
                Vérifier l'Authenticité d'un reçu
            </h1>
            <p class="text-[14px] text-text-muted mt-2">
                Entrez le code unique à 6 caractères inscrit sur votre reçu de versement Cypress Pay (ex: <code
                    class="font-bold text-cobalt-deep bg-slate-100 px-1.5 py-0.5 rounded">28B7S0</code>) pour certifier son
                enregistrement dans notre registre financier sécurisé.
            </p>
        </div>

        <!-- Composant Livewire Receipt Verifier -->
        <div class="max-w-xl mx-auto">
            @if(class_exists(\App\Livewire\ReceiptVerifier::class))
                @livewire('receipt-verifier', ['code' => $code ?? ''])
            @endif
        </div>

        <!-- Reassurance Sécurité -->
        <div
            class="max-w-xl mx-auto mt-12 p-6 rounded-2xl bg-surface-container-lowest border border-border-subtle text-[13px] text-text-muted">
            <h4 class="font-bold text-on-surface mb-2 flex items-center gap-1.5">
                <i data-lucide="lock" class="w-4.5 h-4.5 text-secondary"></i>
                Pourquoi cette vérification est-elle cruciale ?
            </h4>
            <p class="leading-relaxed">
                Pour protéger les acheteurs et lutter contre les faux démarcheurs, chaque encaissement effectué via nos
                agents commerciaux, en caisse agence ou par Mobile Money génère automatiquement une quittance numérique
                certifiée avec horodatage blockchain-ready. Ne payez aucun agent s'il ne vous fournit pas instantanément ce
                code officiel.
            </p>
        </div>
    </div>
@endsection