@extends('layouts.admin')

@section('title', 'Détail du Panier #' . $cart->id)
@section('header_title', 'Panier #' . $cart->id . ' — ' . $cart->nom_client_complet)

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.carts.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Panier #{{ $cart->id }}</h1>
                    @if($cart->status === 'converted')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800">Converti</span>
                    @elseif($cart->status === 'abandoned')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-100 text-amber-800">Abandonné</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-sky-100 text-sky-800">En cours</span>
                    @endif
                </div>
                <p class="text-[12px] text-slate-500 mt-0.5">Session Token : <code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 font-mono">{{ $cart->session_token }}</code></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($cart->telephone_client)
                <a href="{{ $cart->whats_app_recovery_url }}" target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-extrabold flex items-center gap-1.5 transition-all shadow-xs cursor-pointer">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    Relancer sur WhatsApp
                </a>
            @endif

            @if($cart->status !== 'converted')
                <form action="{{ route('admin.carts.convert', $cart) }}" method="POST" onsubmit="return confirm('Convertir ce panier en commande ?')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold flex items-center gap-1.5 transition-all cursor-pointer">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Convertir en Commande
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Grille de détail : Colonne Gauche (Articles + Funnel) et Colonne Droite (Client + Coordonnées) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Gauche (8 colonnes) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Liste des articles -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="package" class="w-5 h-5 text-primary"></i>
                    Articles enregistrés dans le panier ({{ $cart->items_count }})
                </h3>

                <div class="divide-y divide-slate-100">
                    @foreach($cart->items as $item)
                        <div class="py-4 flex items-center gap-4">
                            <img src="{{ $item->product ? $item->product->image_url : '' }}" 
                                 class="w-16 h-16 object-contain bg-slate-50 border border-slate-200 rounded-2xl p-1 shrink-0"/>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-extrabold text-slate-900 text-[14px] truncate">
                                    {{ $item->product ? $item->product->nom : 'Produit supprimé' }}
                                </h4>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-3">
                                    <span>Quantité : <strong class="text-slate-800">{{ $item->quantite }}</strong></span>
                                    <span>Prix unitaire : <strong class="text-slate-800">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} FCFA</strong></span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="font-black text-slate-900 text-[15px]">
                                    {{ number_format($item->total_prix, 0, ',', ' ') }} FCFA
                                </div>
                                @if($item->mensualite_unitaire > 0)
                                    <div class="text-[11px] font-bold text-primary">
                                        {{ number_format($item->total_mensualite, 0, ',', ' ') }} FCFA/m
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Récapitulatif financier -->
                <div class="mt-6 pt-5 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Sous-total articles</span>
                        <span class="font-bold text-slate-800">{{ number_format($cart->subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if($cart->discount_amount > 0)
                        <div class="flex justify-between text-emerald-600 font-bold">
                            <span>Remise Comptant (-7%)</span>
                            <span>- {{ number_format($cart->discount_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-500">
                        <span>Frais de livraison ({{ $cart->shipping_method_label }})</span>
                        <span class="font-bold text-slate-800">{{ number_format($cart->shipping_cost, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-slate-900 font-black text-base pt-3 border-t border-slate-200">
                        <span>Montant Global Total</span>
                        <span class="text-primary">{{ number_format($cart->total, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Parcours & Étape d'abandon -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="git-branch" class="w-5 h-5 text-secondary"></i>
                    Parcours & Diagnostic du Décrochage
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-center">
                    <div class="p-3 rounded-2xl border {{ $cart->step === 'cart' ? 'bg-amber-50 border-amber-300 ring-2 ring-amber-400' : 'bg-slate-50 border-slate-200' }}">
                        <span class="text-[10px] font-black uppercase text-slate-400 block">Étape 1</span>
                        <span class="text-xs font-extrabold text-slate-800">Panier & Offre</span>
                        @if($cart->step === 'cart')
                            <span class="mt-1 text-[10px] font-black text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full inline-block">Arrêt ici</span>
                        @endif
                    </div>
                    <div class="p-3 rounded-2xl border {{ $cart->step === 'address' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-400' : 'bg-slate-50 border-slate-200' }}">
                        <span class="text-[10px] font-black uppercase text-slate-400 block">Étape 2</span>
                        <span class="text-xs font-extrabold text-slate-800">Coordonnées</span>
                        @if($cart->step === 'address')
                            <span class="mt-1 text-[10px] font-black text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full inline-block">Arrêt ici</span>
                        @endif
                    </div>
                    <div class="p-3 rounded-2xl border {{ $cart->step === 'shipping' ? 'bg-purple-50 border-purple-300 ring-2 ring-purple-400' : 'bg-slate-50 border-slate-200' }}">
                        <span class="text-[10px] font-black uppercase text-slate-400 block">Étape 3</span>
                        <span class="text-xs font-extrabold text-slate-800">Mode Livraison</span>
                        @if($cart->step === 'shipping')
                            <span class="mt-1 text-[10px] font-black text-purple-700 bg-purple-100 px-2 py-0.5 rounded-full inline-block">Arrêt ici</span>
                        @endif
                    </div>
                    <div class="p-3 rounded-2xl border {{ $cart->step === 'payment' ? 'bg-red-50 border-red-300 ring-2 ring-red-400' : 'bg-slate-50 border-slate-200' }}">
                        <span class="text-[10px] font-black uppercase text-slate-400 block">Étape 4</span>
                        <span class="text-xs font-extrabold text-slate-800">Paiement</span>
                        @if($cart->step === 'payment')
                            <span class="mt-1 text-[10px] font-black text-red-700 bg-red-100 px-2 py-0.5 rounded-full inline-block">Arrêt ici</span>
                        @endif
                    </div>
                </div>

                <div class="mt-5 p-4 rounded-2xl bg-slate-50 text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Créé le :</span>
                        <span class="font-bold text-slate-800">{{ $cart->created_at->format('d/m/Y à H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Dernière activité :</span>
                        <span class="font-bold text-slate-800">{{ $cart->last_activity_at ? $cart->last_activity_at->diffForHumans() : 'N/A' }}</span>
                    </div>
                    @if($cart->recovery_wa_sent_at)
                        <div class="flex justify-between text-emerald-700 font-bold">
                            <span>Dernière relance WhatsApp :</span>
                            <span>{{ $cart->recovery_wa_sent_at->format('d/m/Y à H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Droite (4 colonnes) : Informations Contact & Livraison -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Client & Contact -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-emerald-600"></i>
                    Coordonnées du Client
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold">Nom complet</span>
                        <span class="text-slate-900 font-extrabold text-[14px]">{{ $cart->nom_client_complet }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Numéro de téléphone</span>
                        @if($cart->telephone_client)
                            <a href="tel:{{ $cart->telephone_client }}" class="text-slate-900 font-mono font-bold text-[14px] hover:text-primary transition-colors flex items-center gap-1.5 mt-0.5">
                                <i data-lucide="phone-call" class="w-3.5 h-3.5 text-slate-400"></i>
                                {{ $cart->telephone_client }}
                            </a>
                        @else
                            <span class="italic text-slate-400">Non renseigné</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Email</span>
                        <span class="text-slate-800 font-semibold">{{ $cart->email_client ?: 'Non renseigné' }}</span>
                    </div>
                </div>
            </div>

            <!-- Adresse & Livraison -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <h3 class="text-base font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="truck" class="w-5 h-5 text-amber-500"></i>
                    Informations de Livraison
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold">Ville & Commune</span>
                        <span class="text-slate-900 font-bold text-[13px]">
                            {{ $cart->shipping_city ?: 'Abidjan' }}
                            @if($cart->shipping_commune) ({{ $cart->shipping_commune }}) @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Adresse précise / Quartier</span>
                        <p class="text-slate-800 mt-0.5">{{ $cart->shipping_address ?: 'Non précisé' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Mode de livraison choisi</span>
                        <span class="px-2 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-800 mt-0.5 inline-block">
                            {{ $cart->shipping_method_label }}
                        </span>
                    </div>
                    @if($cart->customer_notes)
                        <div>
                            <span class="text-slate-400 block font-semibold">Notes du client</span>
                            <p class="italic text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 mt-1">{{ $cart->customer_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lien direct de reprise de panier -->
            <div class="bg-gradient-to-br from-cobalt-deep to-slate-900 p-6 rounded-3xl text-white shadow-md">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-300 flex items-center gap-1.5 mb-2">
                    <i data-lucide="link" class="w-4 h-4 text-emerald-400"></i>
                    Lien direct de reprise
                </h4>
                <p class="text-[11px] text-slate-300 mb-3">
                    Ce lien permet au client de restaurer instantanément son panier sur n'importe quel appareil (mobile, ordinateur, tablette).
                </p>
                <div class="flex items-center gap-2 bg-white/10 p-2 rounded-xl">
                    <input type="text" readonly value="{{ $cart->resume_url }}" id="resume-link-input"
                           class="w-full bg-transparent text-[11px] font-mono text-slate-200 outline-none truncate"/>
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('resume-link-input').value); alert('Lien copié !');"
                            class="px-2.5 py-1 bg-white text-slate-900 rounded-lg text-[10px] font-extrabold hover:bg-slate-100 transition-colors shrink-0">
                        Copier
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
