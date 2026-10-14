@extends('layouts.app')

@section('title', 'Finaliser ma Commande — Cypress Pay CI')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12" x-data="checkoutMultiStepApp()">
    <!-- ══════════════════════════════════════════════════════════════
      EN-TÊTE CHECKOUT
    ══════════════════════════════════════════════════════════════ -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-on-surface tracking-tight flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-2xl bg-primary-container text-white flex items-center justify-center shadow-xs">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </span>
            Tunnel de Commande Sécurisé Cypress Pay
        </h1>
        <p class="text-[13px] text-text-muted mt-1">
            Chaque étape est sauvegardée en direct. Retrouvez votre commande sur n'importe quel appareil en toute fluidité.
        </p>
    </div>

    <!-- Stepper de Progression Moderne (4 Étapes) -->
    <div class="mb-10 bg-surface-container-lowest p-4 rounded-3xl border border-border-subtle shadow-xs">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
            <!-- Étape 1 -->
            <button type="button" @click="goToStep(1)" 
                    class="flex items-center gap-3 p-3 rounded-2xl transition-all text-left cursor-pointer"
                    :class="currentStep === 1 ? 'bg-primary-container text-white font-bold shadow-sm' : (currentStep > 1 ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-50 text-slate-500')">
                <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black shrink-0"
                      :class="currentStep === 1 ? 'bg-white text-primary' : (currentStep > 1 ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700')">
                    <template x-if="currentStep > 1"><i data-lucide="check" class="w-4 h-4"></i></template>
                    <template x-if="currentStep <= 1"><span>1</span></template>
                </span>
                <div class="min-w-0">
                    <span class="text-[10px] uppercase tracking-wider block font-black opacity-80">Étape 1</span>
                    <span class="text-xs font-extrabold truncate block">Panier & Formule</span>
                </div>
            </button>

            <!-- Étape 2 -->
            <button type="button" @click="goToStep(2)" 
                    class="flex items-center gap-3 p-3 rounded-2xl transition-all text-left cursor-pointer"
                    :class="currentStep === 2 ? 'bg-primary-container text-white font-bold shadow-sm' : (currentStep > 2 ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-50 text-slate-500')">
                <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black shrink-0"
                      :class="currentStep === 2 ? 'bg-white text-primary' : (currentStep > 2 ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700')">
                    <template x-if="currentStep > 2"><i data-lucide="check" class="w-4 h-4"></i></template>
                    <template x-if="currentStep <= 2"><span>2</span></template>
                </span>
                <div class="min-w-0">
                    <span class="text-[10px] uppercase tracking-wider block font-black opacity-80">Étape 2</span>
                    <span class="text-xs font-extrabold truncate block">Adresse & Ville</span>
                </div>
            </button>

            <!-- Étape 3 -->
            <button type="button" @click="goToStep(3)" 
                    class="flex items-center gap-3 p-3 rounded-2xl transition-all text-left cursor-pointer"
                    :class="currentStep === 3 ? 'bg-primary-container text-white font-bold shadow-sm' : (currentStep > 3 ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-50 text-slate-500')">
                <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black shrink-0"
                      :class="currentStep === 3 ? 'bg-white text-primary' : (currentStep > 3 ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700')">
                    <template x-if="currentStep > 3"><i data-lucide="check" class="w-4 h-4"></i></template>
                    <template x-if="currentStep <= 3"><span>3</span></template>
                </span>
                <div class="min-w-0">
                    <span class="text-[10px] uppercase tracking-wider block font-black opacity-80">Étape 3</span>
                    <span class="text-xs font-extrabold truncate block">Mode Livraison</span>
                </div>
            </button>

            <!-- Étape 4 -->
            <button type="button" @click="goToStep(4)" 
                    class="flex items-center gap-3 p-3 rounded-2xl transition-all text-left cursor-pointer"
                    :class="currentStep === 4 ? 'bg-primary-container text-white font-bold shadow-sm' : 'bg-slate-50 text-slate-500'">
                <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-black shrink-0"
                      :class="currentStep === 4 ? 'bg-white text-primary' : 'bg-slate-200 text-slate-700'">
                    <span>4</span>
                </span>
                <div class="min-w-0">
                    <span class="text-[10px] uppercase tracking-wider block font-black opacity-80">Étape 4</span>
                    <span class="text-xs font-extrabold truncate block">Paiement & Signature</span>
                </div>
            </button>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-[13px] font-bold flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-main-form" @submit="handleSubmit($event)">
        @csrf
        <input type="hidden" name="cart_data" id="cart_data_input" value=""/>
        <input type="hidden" name="digital_signature" id="signature_input" value=""/>
        <input type="hidden" name="type_achat" :value="typeAchat"/>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- ══════════════════════════════════════════════════════════════
              COLONNE GAUCHE : ÉTAPES DYNAMIQUES DU FORMULAIRE (8 cols)
            ══════════════════════════════════════════════════════════════ -->
            <div class="lg:col-span-8 space-y-6">

                <!-- ── ÉTAPE 1 : RÉCAPITULATIF DU PANIER & CHOIX DE LA FORMULE ── -->
                <div x-show="currentStep === 1" class="space-y-6" x-transition>
                    <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-xs">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border-subtle">
                            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-primary-container text-white text-xs font-black flex items-center justify-center">1</span>
                                Vérification des Articles
                            </h2>
                            <a href="{{ route('cart.index') }}" class="text-xs font-bold text-primary hover:underline">
                                Modifier le panier
                            </a>
                        </div>

                        <!-- Liste des articles -->
                        <div class="divide-y divide-border-subtle">
                            <template x-for="item in items" :key="item.id">
                                <div class="py-3 flex items-center gap-3">
                                    <img :src="item.image || 'https://via.placeholder.com/60'" 
                                         class="w-14 h-14 object-contain rounded-xl bg-slate-50 border border-border-subtle p-1 shrink-0"/>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-extrabold text-xs text-on-surface truncate" x-text="item.nom"></h4>
                                        <div class="text-[11px] text-text-muted mt-0.5">
                                            Qté : <strong class="text-slate-800" x-text="item.qty"></strong> · 
                                            Prix : <strong class="text-slate-800" x-text="Number(item.prix).toLocaleString('fr-FR') + ' FCFA'"></strong>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="font-black text-xs text-on-surface" x-text="(item.prix * item.qty).toLocaleString('fr-FR') + ' FCFA'"></div>
                                        <div class="text-[10px] font-bold text-primary" x-text="(item.monthly * item.qty).toLocaleString('fr-FR') + ' FCFA/m'"></div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="items.length === 0" class="py-8 text-center text-text-muted text-xs">
                                Votre panier est vide. <a href="{{ route('catalog') }}" class="text-primary font-bold">Ajouter des articles</a>
                            </div>
                        </div>
                    </div>

                    <!-- Choix du Mode d'acquisition : Crédit BNPL vs Comptant -->
                    <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-xs">
                        <h2 class="text-base font-bold text-on-surface mb-3 flex items-center gap-2">
                            <i data-lucide="percent" class="w-5 h-5 text-primary"></i>
                            Option d'acquisition
                        </h2>

                        <div class="grid grid-cols-2 gap-3 mb-5">
                            <label class="p-3.5 rounded-2xl border cursor-pointer transition-all flex items-center gap-3"
                                   :class="typeAchat === 'credit' ? 'border-primary bg-brand-red-subtle/30 ring-1 ring-primary' : 'border-border-subtle hover:bg-slate-50'">
                                <input type="radio" name="type_achat_choice" value="credit" x-model="typeAchat" @change="saveStepAutosave(1)" class="text-primary focus:ring-primary"/>
                                <div>
                                    <span class="font-black text-xs text-on-surface block">Paiement Échelonné BNPL</span>
                                    <span class="text-[11px] text-text-muted">Sans avance ou apport minime</span>
                                </div>
                            </label>

                            <label class="p-3.5 rounded-2xl border cursor-pointer transition-all flex items-center gap-3"
                                   :class="typeAchat === 'cash' ? 'border-emerald-600 bg-emerald-50 ring-1 ring-emerald-600' : 'border-border-subtle hover:bg-slate-50'">
                                <input type="radio" name="type_achat_choice" value="cash" x-model="typeAchat" @change="saveStepAutosave(1)" class="text-emerald-600 focus:ring-emerald-600"/>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-black text-xs text-on-surface">Paiement Comptant</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800">-7%</span>
                                    </div>
                                    <span class="text-[11px] text-text-muted">Règlement intégral direct</span>
                                </div>
                            </label>
                        </div>

                        <!-- Formules BNPL (affichées si mode crédit) -->
                        <div x-show="typeAchat === 'credit'" class="space-y-3">
                            <label class="block text-xs font-bold text-slate-700">Sélectionnez votre formule de remboursement :</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($formulas as $f)
                                    @if($f->frequence !== 'cash')
                                        <label class="relative flex items-start gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all hover:bg-slate-50"
                                               :class="selectedFormula == {{ $f->id }} ? 'border-primary bg-brand-red-subtle/30 ring-1 ring-primary' : 'border-border-subtle'">
                                            <input type="radio" name="formula_id" value="{{ $f->id }}" 
                                                   @change="selectFormula({{ $f->id }}, {{ $f->taux_majoration }}, {{ $f->pourcentage_apport }}, '{{ $f->frequence }}', {{ $f->duree_mois }})"
                                                   class="mt-1 text-primary focus:ring-primary"
                                                   {{ ($cart->formula_id == $f->id || $loop->first) ? 'checked' : '' }}/>
                                            <div class="flex-1 text-xs">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-extrabold text-on-surface">{{ $f->nom }}</span>
                                                    <span class="text-[11px] font-bold text-text-muted">{{ $f->duree_mois }} mois</span>
                                                </div>
                                                <p class="text-[11px] text-text-muted mt-0.5">{{ $f->description }}</p>
                                                <div class="mt-1.5 text-[11px] font-bold text-secondary">
                                                    Apport initial : {{ $f->pourcentage_apport }}%
                                                </div>
                                            </div>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Continuer vers Étape 2 -->
                    <div class="flex justify-end">
                        <button type="button" @click="nextStep(2)" :disabled="items.length === 0"
                                class="px-6 py-3.5 bg-primary-container hover:bg-brand-red-hover text-white rounded-2xl font-extrabold text-xs flex items-center gap-2 transition-all shadow-md cursor-pointer disabled:opacity-50">
                            <span>Continuer vers les Coordonnées</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- ── ÉTAPE 2 : COORDONNÉES CLIENT & ADRESSE DE LIVRAISON ── -->
                <div x-show="currentStep === 2" class="space-y-6" x-transition>
                    <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-xs">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border-subtle">
                            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-primary-container text-white text-xs font-black flex items-center justify-center">2</span>
                                Informations Bénéficiaire & Destination CI
                            </h2>
                            <span class="text-[11px] text-emerald-600 font-bold flex items-center gap-1">
                                <i data-lucide="cloud-check" class="w-3.5 h-3.5"></i> Sauvegarde en direct
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Prénom *</label>
                                <input type="text" name="prenom" x-model="formData.prenom" @input.debounce.800ms="saveStepAutosave(2)"
                                       required class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-xs" 
                                       placeholder="Ex: Kouamé"/>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nom de famille *</label>
                                <input type="text" name="nom" x-model="formData.nom" @input.debounce.800ms="saveStepAutosave(2)"
                                       required class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-xs" 
                                       placeholder="Ex: Yao"/>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Numéro Mobile Money (10 chiffres) *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs font-bold text-text-muted">+225</span>
                                    <input type="tel" name="telephone" x-model="formData.telephone" @input.debounce.800ms="saveStepAutosave(2)"
                                           required maxlength="10" 
                                           class="w-full pl-14 pr-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-xs font-mono font-bold" 
                                           placeholder="07 00 00 00 00"/>
                                </div>
                                <span class="text-[10px] text-text-muted mt-1 block">Numéro pour le débit et les notifications de livraison (Wave, Orange, MTN)</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Email (facultatif)</label>
                                <input type="email" name="email" x-model="formData.email" @input.debounce.800ms="saveStepAutosave(2)"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-xs" 
                                       placeholder="contact@exemple.ci"/>
                            </div>

                            <!-- Ville de livraison en Côte d'Ivoire -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Ville de livraison en Côte d'Ivoire *</label>
                                <select name="ville" x-model="formData.ville" @change="saveStepAutosave(2)"
                                        required class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-xs bg-white">
                                    <option value="Abidjan">Abidjan (Toutes communes)</option>
                                    <option value="Bouaké">Bouaké</option>
                                    <option value="Yamoussoukro">Yamoussoukro</option>
                                    <option value="San-Pédro">San-Pédro</option>
                                    <option value="Korhogo">Korhogo</option>
                                    <option value="Daloa">Daloa</option>
                                    <option value="Man">Man</option>
                                    <option value="Gagnoa">Gagnoa</option>
                                    <option value="Soubré">Soubré</option>
                                    <option value="Abengourou">Abengourou</option>
                                    <option value="Grand-Bassam">Grand-Bassam</option>
                                </select>
                            </div>

                            <!-- Commune si Abidjan -->
                            <div x-show="formData.ville === 'Abidjan'">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Commune d'Abidjan *</label>
                                <select name="commune" x-model="formData.commune" @change="saveStepAutosave(2)"
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-xs bg-white">
                                    <option value="Cocody">Cocody (Angré, Riviera, 2 Plateaux)</option>
                                    <option value="Yopougon">Yopougon (Maroc, Niangon, Sideci, Toits Rouges)</option>
                                    <option value="Marcory">Marcory (Zone 4, Biétry, Anoumabo)</option>
                                    <option value="Koumassi">Koumassi (Remblais, Prodomo)</option>
                                    <option value="Plateau">Plateau (Centre des Affaires)</option>
                                    <option value="Treichville">Treichville</option>
                                    <option value="Port-Bouët">Port-Bouët (Vridi, Gonzagueville)</option>
                                    <option value="Adjamé">Adjamé (Mirador, 220 Logements)</option>
                                    <option value="Attécoubé">Attécoubé</option>
                                    <option value="Abobo">Abobo (Gare, Avocatier, Belle-Ville)</option>
                                    <option value="Bingerville">Bingerville</option>
                                    <option value="Songon">Songon</option>
                                </select>
                            </div>

                            <!-- Adresse précise / Quartier -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Quartier, Rue & Repère précis *</label>
                                <input type="text" name="adresse" x-model="formData.adresse" @input.debounce.800ms="saveStepAutosave(2)"
                                       required class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-xs" 
                                       placeholder="Ex: Yopougon Maroc, face pharmacie Bel Air, carrefour Oasis"/>
                            </div>

                            <!-- Instructions pour le livreur -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Instructions pour le livreur (facultatif)</label>
                                <textarea name="notes" x-model="formData.notes" @input.debounce.800ms="saveStepAutosave(2)" rows="2"
                                          class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-xs" 
                                          placeholder="Ex: Appeler avant d'arriver, portail noir à côté de la boulangerie..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Précédent / Suivant -->
                    <div class="flex items-center justify-between">
                        <button type="button" @click="goToStep(1)" class="px-5 py-3 border border-border-subtle rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                            ← Retour au panier
                        </button>
                        <button type="button" @click="nextStep(3)" class="px-6 py-3.5 bg-primary-container hover:bg-brand-red-hover text-white rounded-2xl font-extrabold text-xs flex items-center gap-2 transition-all shadow-md cursor-pointer">
                            <span>Continuer vers le Mode de Livraison</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- ── ÉTAPE 3 : MODE DE LIVRAISON ── -->
                <div x-show="currentStep === 3" class="space-y-6" x-transition>
                    <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-xs">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border-subtle">
                            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-primary-container text-white text-xs font-black flex items-center justify-center">3</span>
                                Choisissez votre Mode de Livraison
                            </h2>
                            <span class="text-xs font-bold text-slate-500" x-text="'Destination : ' + formData.ville"></span>
                        </div>

                        <div class="space-y-3">
                            <!-- Option 1 : Express Abidjan -->
                            <label class="p-4 rounded-2xl border cursor-pointer transition-all flex items-start gap-4 hover:bg-slate-50"
                                   :class="shippingMethod === 'express_abidjan' ? 'border-primary bg-brand-red-subtle/30 ring-1 ring-primary' : 'border-border-subtle'">
                                <input type="radio" name="shipping_method" value="express_abidjan" x-model="shippingMethod" @change="setShipping('express_abidjan', 2000)" class="mt-1 text-primary focus:ring-primary"/>
                                <div class="flex-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-sm text-on-surface">Livraison Express Domicile & Bureau (Abidjan)</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-800">24h - 48h</span>
                                        </div>
                                        <span class="font-black text-sm text-on-surface">2 000 FCFA</span>
                                    </div>
                                    <p class="text-[11px] text-text-muted mt-1">
                                        Remise directe en main propre par nos coursiers certifiés Cypress Pay avec vérification du colis.
                                    </p>
                                </div>
                            </label>

                            <!-- Option 2 : Expédition Intérieur CI -->
                            <label class="p-4 rounded-2xl border cursor-pointer transition-all flex items-start gap-4 hover:bg-slate-50"
                                   :class="shippingMethod === 'interieur_ci' ? 'border-primary bg-brand-red-subtle/30 ring-1 ring-primary' : 'border-border-subtle'">
                                <input type="radio" name="shipping_method" value="interieur_ci" x-model="shippingMethod" @change="setShipping('interieur_ci', 3500)" class="mt-1 text-primary focus:ring-primary"/>
                                <div class="flex-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-sm text-on-surface">Expédition Régions & Villes Intérieur CI</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800">48h - 72h</span>
                                        </div>
                                        <span class="font-black text-sm text-on-surface">3 500 FCFA</span>
                                    </div>
                                    <p class="text-[11px] text-text-muted mt-1">
                                        Envoi sécurisé via compagnie partenaire (UTB, TSR, La Poste CI) avec numéro de suivi colis par SMS.
                                    </p>
                                </div>
                            </label>

                            <!-- Option 3 : Retrait Gratuit Agence -->
                            <label class="p-4 rounded-2xl border cursor-pointer transition-all flex items-start gap-4 hover:bg-slate-50"
                                   :class="shippingMethod === 'retrait_agence' ? 'border-emerald-600 bg-emerald-50 ring-1 ring-emerald-600' : 'border-border-subtle'">
                                <input type="radio" name="shipping_method" value="retrait_agence" x-model="shippingMethod" @change="setShipping('retrait_agence', 0)" class="mt-1 text-emerald-600 focus:ring-emerald-600"/>
                                <div class="flex-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-sm text-on-surface">Retrait Gratuit au Guichet Agence</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">GRATUIT</span>
                                        </div>
                                        <span class="font-black text-sm text-emerald-700">0 FCFA</span>
                                    </div>
                                    <p class="text-[11px] text-text-muted mt-1">
                                        Venez récupérer votre commande à notre agence Cypress Pay Yopougon Maroc ou Cocody Angré sans aucun frais.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Précédent / Suivant -->
                    <div class="flex items-center justify-between">
                        <button type="button" @click="goToStep(2)" class="px-5 py-3 border border-border-subtle rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                            ← Retour aux coordonnées
                        </button>
                        <button type="button" @click="nextStep(4)" class="px-6 py-3.5 bg-primary-container hover:bg-brand-red-hover text-white rounded-2xl font-extrabold text-xs flex items-center gap-2 transition-all shadow-md cursor-pointer">
                            <span>Continuer vers le Paiement</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- ── ÉTAPE 4 : MÉTHODES DE PAIEMENT & SIGNATURE ── -->
                <div x-show="currentStep === 4" class="space-y-6" x-transition>
                    <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-xs">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border-subtle">
                            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-primary-container text-white text-xs font-black flex items-center justify-center">4</span>
                                Méthode de Paiement Sécurisée
                            </h2>
                            <span class="text-xs font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                                Cryptage SSL 256-bit
                            </span>
                        </div>

                        <!-- Grille des Méthodes de Paiement -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                            <!-- 1. Wave CI -->
                            <label class="p-3.5 rounded-2xl border cursor-pointer hover:border-provider-wave flex items-center gap-3 transition-all"
                                   :class="canalPaiement === 'wave' ? 'border-provider-wave bg-blue-50/50 ring-1 ring-provider-wave' : 'border-border-subtle'">
                                <input type="radio" name="canal_paiement" value="wave" x-model="canalPaiement" @change="saveStepAutosave(4)" class="text-provider-wave"/>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-0.5 rounded bg-provider-wave text-white text-[11px] font-black">Wave CI</span>
                                        <span class="text-[10px] font-bold text-emerald-600">0% frais</span>
                                    </div>
                                    <span class="text-[11px] text-text-muted mt-0.5 block">Paiement instantané mobile</span>
                                </div>
                            </label>

                            <!-- 2. Orange Money CI -->
                            <label class="p-3.5 rounded-2xl border cursor-pointer hover:border-provider-orange flex items-center gap-3 transition-all"
                                   :class="canalPaiement === 'orange_money' ? 'border-provider-orange bg-orange-50/50 ring-1 ring-provider-orange' : 'border-border-subtle'">
                                <input type="radio" name="canal_paiement" value="orange_money" x-model="canalPaiement" @change="saveStepAutosave(4)" class="text-provider-orange"/>
                                <div class="flex-1 min-w-0">
                                    <span class="px-2 py-0.5 rounded bg-provider-orange text-white text-[11px] font-black inline-block">Orange Money</span>
                                    <span class="text-[11px] text-text-muted mt-0.5 block">Push notification ou code #144#</span>
                                </div>
                            </label>

                            <!-- 3. MTN MoMo -->
                            <label class="p-3.5 rounded-2xl border cursor-pointer hover:border-provider-mtn flex items-center gap-3 transition-all"
                                   :class="canalPaiement === 'mtn_momo' ? 'border-provider-mtn bg-amber-50/50 ring-1 ring-provider-mtn' : 'border-border-subtle'">
                                <input type="radio" name="canal_paiement" value="mtn_momo" x-model="canalPaiement" @change="saveStepAutosave(4)" class="text-provider-mtn"/>
                                <div class="flex-1 min-w-0">
                                    <span class="px-2 py-0.5 rounded bg-provider-mtn text-slate-900 text-[11px] font-black inline-block">MTN MoMo</span>
                                    <span class="text-[11px] text-text-muted mt-0.5 block">Validation sur mobile *133#</span>
                                </div>
                            </label>

                            <!-- 4. Moov Money -->
                            <label class="p-3.5 rounded-2xl border cursor-pointer hover:border-blue-600 flex items-center gap-3 transition-all"
                                   :class="canalPaiement === 'moov_money' ? 'border-blue-600 bg-blue-50/50 ring-1 ring-blue-600' : 'border-border-subtle'">
                                <input type="radio" name="canal_paiement" value="moov_money" x-model="canalPaiement" @change="saveStepAutosave(4)" class="text-blue-600"/>
                                <div class="flex-1 min-w-0">
                                    <span class="px-2 py-0.5 rounded bg-blue-600 text-white text-[11px] font-black inline-block">Moov Money CI</span>
                                    <span class="text-[11px] text-text-muted mt-0.5 block">Validation sécurisée code *155#</span>
                                </div>
                            </label>

                            <!-- 5. Carte Bancaire Visa / Mastercard -->
                            <label class="p-3.5 rounded-2xl border cursor-pointer hover:border-purple-600 flex items-center gap-3 transition-all"
                                   :class="canalPaiement === 'card' ? 'border-purple-600 bg-purple-50/50 ring-1 ring-purple-600' : 'border-border-subtle'">
                                <input type="radio" name="canal_paiement" value="card" x-model="canalPaiement" @change="saveStepAutosave(4)" class="text-purple-600"/>
                                <div class="flex-1 min-w-0">
                                    <span class="px-2 py-0.5 rounded bg-purple-700 text-white text-[11px] font-black inline-block">Visa / Mastercard</span>
                                    <span class="text-[11px] text-text-muted mt-0.5 block">Débit carte sécurisé 3D-Secure</span>
                                </div>
                            </label>

                            <!-- 6. PayPal -->
                            <label class="p-3.5 rounded-2xl border cursor-pointer hover:border-sky-600 flex items-center gap-3 transition-all"
                                   :class="canalPaiement === 'paypal' ? 'border-sky-600 bg-sky-50/50 ring-1 ring-sky-600' : 'border-border-subtle'">
                                <input type="radio" name="canal_paiement" value="paypal" x-model="canalPaiement" @change="saveStepAutosave(4)" class="text-sky-600"/>
                                <div class="flex-1 min-w-0">
                                    <span class="px-2 py-0.5 rounded bg-sky-600 text-white text-[11px] font-black inline-block">PayPal</span>
                                    <span class="text-[11px] text-text-muted mt-0.5 block">Paiement international garanti</span>
                                </div>
                            </label>

                            <!-- 7. Cash à la livraison (Contre-remboursement) -->
                            <label class="p-3.5 rounded-2xl border cursor-pointer hover:border-emerald-700 flex items-center gap-3 transition-all"
                                   :class="canalPaiement === 'cash_delivery' ? 'border-emerald-700 bg-emerald-50/60 ring-1 ring-emerald-700' : 'border-border-subtle'">
                                <input type="radio" name="canal_paiement" value="cash_delivery" x-model="canalPaiement" @change="saveStepAutosave(4)" class="text-emerald-700"/>
                                <div class="flex-1 min-w-0">
                                    <span class="px-2 py-0.5 rounded bg-emerald-700 text-white text-[11px] font-black inline-block">Cash à la livraison</span>
                                    <span class="text-[11px] text-text-muted mt-0.5 block">Payez en espèces au livreur</span>
                                </div>
                            </label>

                            <!-- 8. Guichet Agence Cypress Pay -->
                            <label class="p-3.5 rounded-2xl border cursor-pointer hover:border-slate-800 flex items-center gap-3 transition-all"
                                   :class="canalPaiement === 'caisse_agence' ? 'border-slate-800 bg-slate-100 ring-1 ring-slate-800' : 'border-border-subtle'">
                                <input type="radio" name="canal_paiement" value="caisse_agence" x-model="canalPaiement" @change="saveStepAutosave(4)" class="text-slate-800"/>
                                <div class="flex-1 min-w-0">
                                    <span class="px-2 py-0.5 rounded bg-slate-800 text-white text-[11px] font-black inline-block">Caisse Agence</span>
                                    <span class="text-[11px] text-text-muted mt-0.5 block">Espèces à Yopougon ou Cocody</span>
                                </div>
                            </label>
                        </div>

                        <!-- Signature Numérique du Contrat BNPL (React Canvas ou Signature Pad) -->
                        <div class="mt-8 pt-6 border-t border-border-subtle">
                            <h3 class="text-base font-extrabold text-on-surface mb-2 flex items-center gap-2">
                                <i data-lucide="edit-3" class="w-5 h-5 text-primary"></i>
                                Signature Électronique de la Commande
                            </h3>
                            <p class="text-xs text-text-muted mb-4">
                                Signez dans l'encadré ci-dessous avec votre doigt ou curseur pour certifier l'exactitude de vos coordonnées et confirmer votre souscription.
                            </p>

                            <!-- Montage composant React ou Canvas de signature -->
                            <div id="react-digital-signature" data-contract-id="CP-CMD-{{ date('Y') }}"></div>
                        </div>
                    </div>

                    <!-- Navigation Précédent & Bouton Final de Confirmation -->
                    <div class="flex items-center justify-between">
                        <button type="button" @click="goToStep(3)" class="px-5 py-3 border border-border-subtle rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                            ← Retour à la livraison
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                                class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-sm flex items-center gap-2 transition-all shadow-lg hover:shadow-xl cursor-pointer disabled:opacity-50">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            <span x-text="isSubmitting ? 'Validation en cours...' : 'Confirmer et Payer ma commande'"></span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════════════
              COLONNE DROITE : RÉCAPITULATIF FINANCIER & LIVE TOTALS (4 cols)
            ══════════════════════════════════════════════════════════════ -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-surface-container-lowest p-6 rounded-3xl border border-border-subtle shadow-sm sticky top-40 space-y-4">
                    <h3 class="text-base font-extrabold text-on-surface pb-3 border-b border-border-subtle flex items-center justify-between">
                        <span>Récapitulatif</span>
                        <span class="text-xs font-bold text-primary" x-text="items.length + ' article(s)'"></span>
                    </h3>

                    <!-- Mini Liste -->
                    <div class="space-y-2.5 max-h-48 overflow-y-auto pr-1 text-xs">
                        <template x-for="item in items" :key="item.id">
                            <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                <div class="truncate pr-2">
                                    <span class="font-bold text-slate-800 truncate block" x-text="item.nom"></span>
                                    <span class="text-[11px] text-text-muted" x-text="item.qty + ' x ' + Number(item.prix).toLocaleString('fr-FR') + ' FCFA'"></span>
                                </div>
                                <span class="font-extrabold text-slate-900 shrink-0" x-text="(item.prix * item.qty).toLocaleString('fr-FR') + ' FCFA'"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Calculs Financiers Dynamiques -->
                    <div class="pt-3 border-t border-border-subtle space-y-2 text-xs">
                        <div class="flex justify-between text-text-muted">
                            <span>Sous-total articles :</span>
                            <span class="font-bold text-on-surface" x-text="Number(totalRaw).toLocaleString('fr-FR') + ' FCFA'"></span>
                        </div>

                        <div class="flex justify-between text-emerald-600 font-bold" x-show="typeAchat === 'cash'">
                            <span>Remise Comptant (-7%) :</span>
                            <span x-text="'- ' + Number(cashDiscount).toLocaleString('fr-FR') + ' FCFA'"></span>
                        </div>

                        <div class="flex justify-between text-text-muted">
                            <span>Frais de livraison :</span>
                            <span class="font-bold text-slate-800" x-text="shippingCost === 0 ? 'Gratuit' : Number(shippingCost).toLocaleString('fr-FR') + ' FCFA'"></span>
                        </div>

                        <div class="pt-3 border-t border-border-subtle flex justify-between items-baseline">
                            <span class="font-extrabold text-sm text-on-surface">Montant Global :</span>
                            <div class="text-right">
                                <span class="text-xl font-black text-on-surface" x-text="Number(totalWithShipping).toLocaleString('fr-FR')"></span>
                                <span class="text-xs font-bold text-slate-500"> FCFA</span>
                            </div>
                        </div>

                        <!-- Affichage spécifique BNPL -->
                        <div x-show="typeAchat === 'credit'" class="p-3.5 rounded-2xl bg-brand-red-subtle/40 border border-primary/20 space-y-1.5 mt-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-600 font-semibold">Apport initial requis :</span>
                                <span class="font-black text-secondary" x-text="Number(downpaymentAmount).toLocaleString('fr-FR') + ' FCFA'"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-primary font-bold">Mensualité estimée :</span>
                                <span class="text-sm font-black text-primary" x-text="Number(monthlyAmount).toLocaleString('fr-FR') + ' FCFA/m'"></span>
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-text-muted pt-1 border-t border-primary/10">
                                <span>Durée choisie :</span>
                                <span class="font-bold text-slate-700" x-text="durationMonths + ' mois'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Reprise multi-appareils -->
                    <div class="p-3 rounded-xl bg-slate-50 text-[11px] text-slate-500 flex items-center gap-2">
                        <i data-lucide="smartphone" class="w-4 h-4 text-slate-400 shrink-0"></i>
                        <span>Panier synchronisé en direct. Reprenez sur PC ou smartphone à tout moment.</span>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function checkoutMultiStepApp() {
    return {
        currentStep: 1,
        items: [],
        typeAchat: '{{ $cart->type_achat ?? "credit" }}',
        selectedFormula: {{ $cart->formula_id ?? ($formulas->first()?->id ?? 1) }},
        formulaRate: {{ $cart->formula?->taux_majoration ?? ($formulas->first()?->taux_majoration ?? 0) }},
        formulaDownpaymentPct: {{ $cart->formula?->pourcentage_apport ?? ($formulas->first()?->pourcentage_apport ?? 0) }},
        durationMonths: {{ $cart->formula?->duree_mois ?? ($formulas->first()?->duree_mois ?? 12) }},
        shippingMethod: '{{ $cart->shipping_method ?? "express_abidjan" }}',
        shippingCost: {{ $cart->shipping_cost ?? 2000 }},
        canalPaiement: '{{ $cart->payment_method ?? "wave" }}',
        isSubmitting: false,

        formData: {
            prenom: '{{ addslashes($cart->customer_firstname ?? "") }}',
            nom: '{{ addslashes($cart->customer_lastname ?? "") }}',
            telephone: '{{ addslashes($cart->customer_phone ?? "") }}',
            email: '{{ addslashes($cart->customer_email ?? "") }}',
            ville: '{{ addslashes($cart->shipping_city ?? "Abidjan") }}',
            commune: '{{ addslashes($cart->shipping_commune ?? "Cocody") }}',
            adresse: '{{ addslashes($cart->shipping_address ?? "") }}',
            notes: '{{ addslashes($cart->customer_notes ?? "") }}'
        },

        init() {
            // 1. Charger les articles depuis la base de données
            this.loadCartFromDatabase();

            // Restaurer l'étape enregistrée dans le panier
            const savedStep = '{{ $cart->step }}';
            if (savedStep === 'address') this.currentStep = 2;
            else if (savedStep === 'shipping') this.currentStep = 3;
            else if (savedStep === 'payment') this.currentStep = 4;
        },

        async loadCartFromDatabase() {
            try {
                const res = await fetch('/cart/data', { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                if (json.success && json.cart) {
                    if (json.cart.items && json.cart.items.length > 0) {
                        this.items = json.cart.items;
                    } else if (window.CypressCart) {
                        // Si le panier DB était vide, tenter de synchroniser depuis le localStorage client
                        const localItems = window.CypressCart.getItems();
                        if (localItems.length > 0) {
                            await this.syncLocalItemsToDb(localItems);
                        }
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        async syncLocalItemsToDb(localItems) {
            try {
                const res = await fetch('/cart/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ items: localItems })
                });
                const json = await res.json();
                if (json.success && json.cart) {
                    this.items = json.cart.items;
                }
            } catch (e) {}
        },

        get totalRaw() {
            return this.items.reduce((sum, i) => sum + (Number(i.prix) * (i.qty || 1)), 0);
        },

        get cashDiscount() {
            return this.typeAchat === 'cash' ? Math.round(this.totalRaw * 0.07) : 0;
        },

        get totalWithShipping() {
            return Math.max(0, this.totalRaw - this.cashDiscount + this.shippingCost);
        },

        get downpaymentAmount() {
            if (this.typeAchat === 'cash') return this.totalWithShipping;
            return Math.round((this.totalRaw * this.formulaDownpaymentPct) / 100);
        },

        get monthlyAmount() {
            if (this.typeAchat === 'cash') return 0;
            const financed = Math.max(0, this.totalRaw - this.downpaymentAmount);
            const withRate = financed * (1 + (this.formulaRate / 100));
            return Math.round(withRate / (this.durationMonths || 12));
        },

        selectFormula(id, rate, downPct, freq, months) {
            this.selectedFormula = id;
            this.formulaRate = rate;
            this.formulaDownpaymentPct = downPct;
            this.durationMonths = months;
            this.saveStepAutosave(1);
        },

        setShipping(method, cost) {
            this.shippingMethod = method;
            this.shippingCost = cost;
            this.saveStepAutosave(3);
        },

        goToStep(stepNumber) {
            this.saveStepAutosave(this.currentStep);
            this.currentStep = stepNumber;
            window.scrollTo({ top: 120, behavior: 'smooth' });
            if (window.refreshLucideIcons) window.refreshLucideIcons();
        },

        nextStep(targetStep) {
            // Validation simple avant d'avancer
            if (this.currentStep === 2) {
                if (!this.formData.prenom || !this.formData.nom || !this.formData.telephone || !this.formData.adresse) {
                    alert('Veuillez renseigner votre prénom, nom, téléphone et adresse pour continuer.');
                    return;
                }
            }
            this.goToStep(targetStep);
        },

        async saveStepAutosave(stepNumber) {
            const stepNames = { 1: 'cart', 2: 'address', 3: 'shipping', 4: 'payment' };
            const stepName = stepNames[stepNumber] || 'cart';

            const payload = {
                step: stepName,
                data: {
                    type_achat: this.typeAchat,
                    formula_id: this.selectedFormula,
                    customer_firstname: this.formData.prenom,
                    customer_lastname: this.formData.nom,
                    customer_phone: this.formData.telephone,
                    customer_email: this.formData.email,
                    shipping_city: this.formData.ville,
                    shipping_commune: this.formData.commune,
                    shipping_address: this.formData.adresse,
                    customer_notes: this.formData.notes,
                    shipping_method: this.shippingMethod,
                    shipping_cost: this.shippingCost,
                    payment_method: this.canalPaiement
                }
            };

            try {
                await fetch('/cart/step', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            } catch (e) {
                console.warn('Autosave cart step failed:', e);
            }
        },

        handleSubmit(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Votre panier est vide.');
                return;
            }

            if (!this.formData.prenom || !this.formData.nom || !this.formData.telephone || !this.formData.adresse) {
                e.preventDefault();
                alert('Veuillez compléter vos coordonnées à l\'étape 2.');
                this.goToStep(2);
                return;
            }

            this.isSubmitting = true;
            document.getElementById('cart_data_input').value = JSON.stringify(this.items);

            // Vider le panier client après soumission réussie
            if (window.CypressCart) {
                window.CypressCart.clear();
            }
        }
    };
}
</script>
@endsection
