@extends('layouts.admin')

@section('title', 'Paniers')
@section('header_title', 'Gestion des Paniers')
@section('page-title', 'Gestion des Paniers')

@section('content')
    <div class="space-y-6" x-data="adminCartsManager()">
        <!-- ══════════════════════════════════════════════════════════════
                                  EN-TÊTE & ACTIONS RAPIDES
                                ══════════════════════════════════════════════════════════════ -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    </span>
                    Paniers
                </h1>
                <p class="text-[13px] text-slate-500 mt-1">
                    Suivez en temps réel les paniers enregistrés en base, analysez les étapes de décrochage et relancez les
                    clients via WhatsApp & SMS.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.carts.index') }}"
                    class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-[13px] font-bold flex items-center gap-2 transition-colors cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-slate-500"></i>
                    <span>Actualiser</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div
                class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-bold flex items-center gap-2.5">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div
                class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-[13px] font-bold flex items-center gap-2.5">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- ══════════════════════════════════════════════════════════════
                                  KPIS DU FUNNEL DE CONVERSION
                                ══════════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Paniers Abandonnés -->
            <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Paniers Abandonnés</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="shopping-bag" class="w-4.5 h-4.5"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span
                        class="text-3xl font-black text-slate-900">{{ number_format($abandonedCount, 0, ',', ' ') }}</span>
                    <span class="text-[12px] font-extrabold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">
                        {{ $abandonmentRate }}% taux d'abandon
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 block mt-1">Non finalisés depuis > 15 min</span>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-500"></div>
            </div>

            <!-- 2. Chiffre d'affaires Perdu -->
            <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Valeur Potentielle
                        Perdue</span>
                    <span class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                        <i data-lucide="trending-down" class="w-4.5 h-4.5"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1">
                    <span
                        class="text-2xl sm:text-3xl font-black text-red-600">{{ number_format($lostRevenue, 0, ',', ' ') }}</span>
                    <span class="text-xs font-bold text-slate-500">FCFA</span>
                </div>
                <span class="text-[11px] text-slate-400 block mt-1">Montant total des paniers en sommeil</span>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-red-500"></div>
            </div>

            <!-- 3. Paniers Récupérés -->
            <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Paniers Récupérés</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-4.5 h-4.5"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span
                        class="text-3xl font-black text-emerald-600">{{ number_format($recoveredCount, 0, ',', ' ') }}</span>
                    <span class="text-[12px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                        {{ number_format($recoveredRevenue, 0, ',', ' ') }} FCFA
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 block mt-1">Convertis suite aux relances</span>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500"></div>
            </div>

            <!-- 4. Paniers Actifs en cours -->
            <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">En direct (Actifs)</span>
                    <span class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i data-lucide="zap" class="w-4.5 h-4.5"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-sky-600">{{ number_format($activeCount, 0, ',', ' ') }}</span>
                    <span
                        class="flex items-center gap-1 text-[11px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-md">
                        <span class="w-2 h-2 rounded-full bg-sky-500 animate-ping"></span>
                        En navigation
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 block mt-1">Activité dans les 15 dernières minutes</span>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-sky-500"></div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
                                  ANALYSE DE L'ÉTAPE DU DÉCROCHAGE (FUNNEL)
                                ══════════════════════════════════════════════════════════════ -->
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs">
            <h3 class="text-[14px] font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <i data-lucide="git-commit" class="w-4 h-4 text-primary"></i>
                Où les clients abandonnent-ils le parcours de commande ?
            </h3>

            @php
                $totalStepAbandons = max(1, array_sum($stepBreakdown));
                $pctCart = round(($stepBreakdown['cart'] / $totalStepAbandons) * 100);
                $pctAddr = round(($stepBreakdown['address'] / $totalStepAbandons) * 100);
                $pctShip = round(($stepBreakdown['shipping'] / $totalStepAbandons) * 100);
                $pctPay = round(($stepBreakdown['payment'] / $totalStepAbandons) * 100);
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <a href="{{ route('admin.carts.index', ['status' => 'abandoned', 'step' => 'cart']) }}"
                    class="p-4 rounded-2xl border transition-all cursor-pointer {{ $stepFilter === 'cart' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-slate-200/80 hover:bg-slate-50' }}">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-1">
                        <span>1. Panier & Offre</span>
                        <span class="text-slate-800 font-extrabold">{{ $pctCart }}%</span>
                    </div>
                    <div class="text-xl font-black text-slate-900">{{ $stepBreakdown['cart'] }}</div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full" style="width: {{ $pctCart }}%"></div>
                    </div>
                </a>

                <a href="{{ route('admin.carts.index', ['status' => 'abandoned', 'step' => 'address']) }}"
                    class="p-4 rounded-2xl border transition-all cursor-pointer {{ $stepFilter === 'address' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-slate-200/80 hover:bg-slate-50' }}">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-1">
                        <span>2. Coordonnées & Ville</span>
                        <span class="text-slate-800 font-extrabold">{{ $pctAddr }}%</span>
                    </div>
                    <div class="text-xl font-black text-slate-900">{{ $stepBreakdown['address'] }}</div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-blue-500 h-full rounded-full" style="width: {{ $pctAddr }}%"></div>
                    </div>
                </a>

                <a href="{{ route('admin.carts.index', ['status' => 'abandoned', 'step' => 'shipping']) }}"
                    class="p-4 rounded-2xl border transition-all cursor-pointer {{ $stepFilter === 'shipping' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-slate-200/80 hover:bg-slate-50' }}">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-1">
                        <span>3. Mode de Livraison</span>
                        <span class="text-slate-800 font-extrabold">{{ $pctShip }}%</span>
                    </div>
                    <div class="text-xl font-black text-slate-900">{{ $stepBreakdown['shipping'] }}</div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-purple-500 h-full rounded-full" style="width: {{ $pctShip }}%"></div>
                    </div>
                </a>

                <a href="{{ route('admin.carts.index', ['status' => 'abandoned', 'step' => 'payment']) }}"
                    class="p-4 rounded-2xl border transition-all cursor-pointer {{ $stepFilter === 'payment' ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-slate-200/80 hover:bg-slate-50' }}">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-1">
                        <span>4. Écran de Paiement</span>
                        <span class="text-slate-800 font-extrabold">{{ $pctPay }}%</span>
                    </div>
                    <div class="text-xl font-black text-slate-900">{{ $stepBreakdown['payment'] }}</div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-red-500 h-full rounded-full" style="width: {{ $pctPay }}%"></div>
                    </div>
                </a>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
                                  BARRE DE FILTRES & RECHERCHE
                                ══════════════════════════════════════════════════════════════ -->
        <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <form method="GET" action="{{ route('admin.carts.index') }}"
                class="flex flex-col md:flex-row gap-3 items-center justify-between">
                <!-- Onglets d'état -->
                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-2xl overflow-x-auto w-full md:w-auto">
                    <a href="{{ route('admin.carts.index', ['status' => 'abandoned', 'step' => $stepFilter]) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'abandoned' ? 'bg-white text-slate-900 shadow-xs font-extrabold' : 'text-slate-600 hover:text-slate-900' }}">
                        Abandonnés ({{ $abandonedCount }})
                    </a>
                    <a href="{{ route('admin.carts.index', ['status' => 'active', 'step' => $stepFilter]) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'active' ? 'bg-white text-slate-900 shadow-xs font-extrabold' : 'text-slate-600 hover:text-slate-900' }}">
                        En cours ({{ $activeCount }})
                    </a>
                    <a href="{{ route('admin.carts.index', ['status' => 'converted', 'step' => $stepFilter]) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'converted' ? 'bg-white text-slate-900 shadow-xs font-extrabold' : 'text-slate-600 hover:text-slate-900' }}">
                        Convertis ({{ $convertedCount }})
                    </a>
                    <a href="{{ route('admin.carts.index', ['status' => 'all', 'step' => $stepFilter]) }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $filterStatus === 'all' ? 'bg-white text-slate-900 shadow-xs font-extrabold' : 'text-slate-600 hover:text-slate-900' }}">
                        Tous les paniers
                    </a>
                </div>

                <!-- Champ de recherche & Soumission -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <input type="hidden" name="status" value="{{ $filterStatus }}" />
                    @if($stepFilter)
                        <input type="hidden" name="step" value="{{ $stepFilter }}" />
                    @endif
                    <div class="relative w-full md:w-72">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Nom, tél, ville, token..."
                            class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:border-primary outline-none bg-slate-50/50 focus:bg-white transition-all" />
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold cursor-pointer transition-colors">
                        Filtrer
                    </button>
                    @if($search || $stepFilter)
                        <a href="{{ route('admin.carts.index', ['status' => $filterStatus]) }}"
                            class="px-3 py-2 text-slate-500 hover:text-red-600 text-xs font-bold transition-colors"
                            title="Effacer les filtres">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
                                  TABLEAU DES PANIERS
                                ══════════════════════════════════════════════════════════════ -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-[13px]">
                    <thead>
                        <tr
                            class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-black uppercase tracking-wider text-slate-400">
                            <th class="py-3.5 px-5">Client / Contact</th>
                            <th class="py-3.5 px-4">Articles du Panier</th>
                            <th class="py-3.5 px-4">Étape d'abandon</th>
                            <th class="py-3.5 px-4 text-right">Valeur Totale</th>
                            <th class="py-3.5 px-4">Dernière Activité</th>
                            <th class="py-3.5 px-4">Statut</th>
                            <th class="py-3.5 px-5 text-right">Actions Relance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($carts as $c)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- 1. Client / Contact -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 font-black text-xs flex items-center justify-center shrink-0 border border-slate-200">
                                            {{ substr($c->nom_client_complet, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-slate-900 truncate flex items-center gap-1.5">
                                                <span>{{ $c->nom_client_complet }}</span>
                                                @if($c->user_id)
                                                    <span
                                                        class="px-1.5 py-0.5 rounded text-[9px] font-black bg-blue-50 text-blue-700 border border-blue-200">Compte</span>
                                                @else
                                                    <span
                                                        class="px-1.5 py-0.5 rounded text-[9px] font-black bg-slate-100 text-slate-600">Invité</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                                @if($c->telephone_client)
                                                    <span class="font-mono font-bold text-slate-700 flex items-center gap-1">
                                                        <i data-lucide="phone" class="w-3 h-3 text-slate-400"></i>
                                                        {{ $c->telephone_client }}
                                                    </span>
                                                @else
                                                    <span class="italic text-slate-400">Tél non renseigné</span>
                                                @endif
                                                @if($c->shipping_city)
                                                    <span>· {{ $c->shipping_city }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Articles du panier -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex -space-x-2 overflow-hidden shrink-0">
                                            @foreach($c->items->take(3) as $it)
                                                <img src="{{ $it->product ? $it->product->image_url : '' }}"
                                                    title="{{ $it->product ? $it->product->nom : 'Article' }} (Qté: {{ $it->quantite }})"
                                                    class="w-8 h-8 rounded-lg object-contain bg-white border border-slate-200 p-0.5 ring-2 ring-white shrink-0" />
                                            @endforeach
                                        </div>
                                        <div class="text-[12px] min-w-0">
                                            <span class="font-bold text-slate-800 block truncate max-w-[180px]">
                                                {{ $c->items->first()?->product?->nom ?? 'Panier vide' }}
                                            </span>
                                            <span class="text-[11px] text-slate-500">
                                                {{ $c->items_count }} article(s) au total
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. Étape d'abandon -->
                                <td class="py-4 px-4">
                                    @php
                                        $stepBadge = match ($c->step) {
                                            'cart' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'address' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'shipping' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'payment' => 'bg-red-50 text-red-700 border-red-200',
                                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-xl text-[11px] font-extrabold border {{ $stepBadge }} inline-block">
                                        {{ $c->step_label }}
                                    </span>
                                    @if($c->payment_method)
                                        <span class="text-[10px] text-slate-500 block mt-1">Canal :
                                            {{ $c->payment_method_label }}</span>
                                    @endif
                                </td>

                                <!-- 4. Valeur totale -->
                                <td class="py-4 px-4 text-right">
                                    <div class="font-extrabold text-slate-900 text-[14px]">
                                        {{ number_format($c->total, 0, ',', ' ') }} <span
                                            class="text-xs font-semibold text-slate-500">FCFA</span>
                                    </div>
                                    @if($c->type_achat === 'credit' && $c->monthly_amount > 0)
                                        <span class="text-[11px] font-bold text-primary block">
                                            {{ number_format($c->monthly_amount, 0, ',', ' ') }} FCFA/mois
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-emerald-600 block">Comptant (-7%)</span>
                                    @endif
                                </td>

                                <!-- 5. Dernière activité -->
                                <td class="py-4 px-4">
                                    <div class="text-[12px] font-bold text-slate-700">
                                        {{ $c->last_activity_at ? $c->last_activity_at->diffForHumans() : 'Récemment' }}
                                    </div>
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        {{ $c->last_activity_at ? $c->last_activity_at->format('d/m/Y H:i') : '' }}
                                    </span>
                                </td>

                                <!-- 6. Statut -->
                                <td class="py-4 px-4">
                                    @if($c->status === 'converted')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 flex items-center gap-1 w-fit">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            Converti
                                        </span>
                                    @elseif($c->status === 'active' && $c->last_activity_at >= now()->subMinutes(15))
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black bg-sky-100 text-sky-800 flex items-center gap-1 w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-600 animate-ping"></span>
                                            En direct
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 flex items-center gap-1 w-fit">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            Abandonné
                                        </span>
                                    @endif

                                    @if($c->recovery_wa_sent_at)
                                        <span class="text-[10px] text-emerald-600 font-bold block mt-1 flex items-center gap-1">
                                            <i data-lucide="message-circle" class="w-3 h-3"></i> Relancé WA
                                        </span>
                                    @endif
                                </td>

                                <!-- 7. Actions de Relance -->
                                <td class="py-4 px-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Bouton WhatsApp -->
                                        @if($c->telephone_client)
                                            <a href="{{ $c->whats_app_recovery_url }}" target="_blank"
                                                @click="recordWhatsAppSent({{ $c->id }})"
                                                title="Relancer sur WhatsApp avec lien de reprise 1-clic"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-bold flex items-center gap-1 transition-all shadow-xs cursor-pointer">
                                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                                <span>WhatsApp</span>
                                            </a>
                                        @endif

                                        <!-- Détail / Inspection -->
                                        <a href="{{ route('admin.carts.show', $c) }}" title="Inspecter le panier"
                                            class="p-1.5 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        <!-- Convertir en commande manuellement -->
                                        @if($c->status !== 'converted')
                                            <form action="{{ route('admin.carts.convert', $c) }}" method="POST"
                                                onsubmit="return confirm('Convertir ce panier en commande validée ?')">
                                                @csrf
                                                <button type="submit" title="Convertir manuellement en commande"
                                                    class="p-1.5 rounded-xl text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors cursor-pointer">
                                                    <i data-lucide="check-check" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Supprimer -->
                                        <form action="{{ route('admin.carts.destroy', $c) }}" method="POST"
                                            onsubmit="return confirm('Supprimer définitivement ce panier ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Supprimer"
                                                class="p-1.5 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center text-slate-500">
                                    <div
                                        class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                                    </div>
                                    <p class="font-extrabold text-[15px] text-slate-800">Aucun panier trouvé</p>
                                    <p class="text-[12px] text-slate-400 mt-1">Tous les paniers ont été convertis ou aucun ne
                                        correspond à vos filtres.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($carts->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $carts->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function adminCartsManager() {
            return {
                recordWhatsAppSent(cartId) {
                    fetch(`/admin/paniers-abandonnes/${cartId}/relance-wa`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }).catch(() => { });
                }
            };
        }
    </script>
@endsection