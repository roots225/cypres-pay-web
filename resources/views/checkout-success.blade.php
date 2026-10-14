@extends('layouts.app')

@section('title', 'Commande Confirmée — ' . $order->numero_commande)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-12 border border-border-subtle shadow-xl text-center">
        
        <!-- Icône de Confirmation -->
        <div class="w-20 h-20 rounded-3xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-sm">
            <i data-lucide="check-circle-2" class="w-10 h-10"></i>
        </div>

        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-black uppercase tracking-wider border border-emerald-200 inline-block">
            Commande Confirmée avec Succès
        </span>

        <h1 class="text-2xl sm:text-3xl font-black text-on-surface mt-3">
            Merci pour votre commande chez Cypress Pay CI !
        </h1>

        <p class="text-xs sm:text-sm text-text-muted mt-2 max-w-lg mx-auto">
            Votre commande <strong class="text-on-surface font-mono text-primary font-bold">{{ $order->numero_commande }}</strong> est enregistrée. Nos agents logistiques et de vérification préparent son expédition.
        </p>

        <!-- Grille Récapitulative : Paiement & Livraison -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8 text-left">
            <!-- 1. Paiement -->
            <div class="p-5 rounded-2xl bg-canvas-background border border-border-subtle space-y-3 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-border-subtle">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="credit-card" class="w-4 h-4 text-primary"></i>
                        Mode de Règlement
                    </span>
                    <span class="px-2 py-0.5 rounded font-black text-[11px] bg-slate-200 text-slate-800 uppercase">
                        {{ $paymentIntent['channel'] ?? $order->canal_paiement }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-text-muted">Montant à régler / Apport :</span>
                    <span class="font-black text-sm text-primary">
                        {{ number_format($order->apport_initial > 0 ? $order->apport_initial : $order->montant_total, 0, ',', ' ') }} FCFA
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-text-muted">Type de contrat :</span>
                    <span class="font-extrabold text-slate-800">
                        {{ $order->type_paiement === 'cash' ? 'Paiement Comptant (-7%)' : 'Crédit BNPL (' . $order->nombre_echeances . ' mois)' }}
                    </span>
                </div>

                <!-- Instructions spécifiques au canal -->
                <div class="p-3 rounded-xl bg-white border border-border-subtle mt-2">
                    <span class="font-bold text-slate-800 block mb-1">Instruction de paiement :</span>
                    <p class="text-slate-600 leading-relaxed text-[11px]">
                        {{ $paymentIntent['instructions'] ?? 'Veuillez finaliser le règlement auprès de votre opérateur.' }}
                    </p>

                    @if(!empty($paymentIntent['checkout_url']))
                        <a href="{{ $paymentIntent['checkout_url'] }}" target="_blank"
                           class="mt-3 px-4 py-2 bg-provider-wave hover:bg-blue-600 text-white rounded-xl font-bold text-xs inline-flex items-center gap-2 transition-all shadow-xs">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            Ouvrir l'application Wave pour payer
                        </a>
                    @endif
                </div>
            </div>

            <!-- 2. Livraison & Destinataire -->
            <div class="p-5 rounded-2xl bg-canvas-background border border-border-subtle space-y-3 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-border-subtle">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="truck" class="w-4 h-4 text-emerald-600"></i>
                        Destination de Livraison
                    </span>
                    <span class="font-bold text-slate-700">{{ $order->ville_livraison }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-text-muted">Bénéficiaire :</span>
                    <span class="font-bold text-slate-800">{{ $order->user ? $order->user->nom_complet : 'Client' }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-text-muted">Téléphone de contact :</span>
                    <span class="font-mono font-bold text-slate-800">+225 {{ $order->telephone_contact }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-text-muted">Adresse & Repère :</span>
                    <span class="font-semibold text-slate-800 text-right max-w-[200px] truncate">{{ $order->adresse_livraison }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-text-muted">Frais de livraison :</span>
                    <span class="font-bold text-slate-800">{{ number_format($order->frais_livraison, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <!-- Tableau d'Amortissement / Échéances si Crédit BNPL -->
        @if($order->installments->count() > 0)
            <div class="mt-8 text-left max-w-2xl mx-auto">
                <h4 class="font-extrabold text-sm text-on-surface mb-3 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-secondary"></i>
                    Calendrier Prévisionnel de vos Mensualités
                </h4>

                <div class="bg-canvas-background rounded-2xl border border-border-subtle overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-text-muted font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-3">Échéance</th>
                                <th class="p-3">Date limite</th>
                                <th class="p-3">Montant</th>
                                <th class="p-3 text-right">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @foreach($order->installments as $inst)
                                <tr>
                                    <td class="p-3 font-bold text-on-surface">Mensualité #{{ $inst->numero_echeance }}</td>
                                    <td class="p-3 text-text-muted">{{ $inst->date_echeance->format('d/m/Y') }}</td>
                                    <td class="p-3 font-black text-primary">{{ number_format($inst->montant_du, 0, ',', ' ') }} FCFA</td>
                                    <td class="p-3 text-right">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">À échoir</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Boutons d'Action & Support WhatsApp -->
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('client.dashboard') }}" class="w-full sm:w-auto bg-primary-container hover:bg-brand-red-hover text-white font-extrabold text-xs px-6 py-3.5 rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span>Suivre ma commande sur mon Espace</span>
            </a>

            <!-- Assistance WhatsApp avec référence pré-remplie -->
            <a href="https://wa.me/2250700000000?text={{ urlencode("Bonjour Cypress Pay, je viens de passer la commande " . $order->numero_commande . ". Pouvez-vous me confirmer la livraison ?") }}" 
               target="_blank"
               class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs px-6 py-3.5 rounded-2xl shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                <span>Contacter l'Assistance WhatsApp</span>
            </a>

            <a href="{{ route('home') }}" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-on-surface font-bold text-xs px-6 py-3.5 rounded-2xl transition-all">
                Retour à la boutique
            </a>
        </div>

    </div>
</div>

<script>
    // Vider le panier localStorage une fois la commande validée
    try {
        localStorage.removeItem('cypress_cart_v1');
        if (window.CypressCart) window.CypressCart.clear();
    } catch(e) {}
</script>
@endsection
