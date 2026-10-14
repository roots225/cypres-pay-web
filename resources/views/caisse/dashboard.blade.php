@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête Caisse Agence -->
    <div class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 border border-border-subtle shadow-sm mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-tertiary font-black text-2xl flex items-center justify-center shrink-0">
                <i data-lucide="circle-dollar-sign" class="w-8 h-8"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-on-surface">
                        Guichet Caisse & Quittances Numériques
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-tertiary">
                        Caisse Ouverte
                    </span>
                </div>
                <p class="text-[12px] text-text-muted mt-1">
                    Opérateur : <strong class="text-on-surface">{{ $caissier?->nom_complet ?? 'Caissier Principal Agence Yopougon' }}</strong> · Émission officielle de quittances infalsifiables.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="bg-emerald-50 border border-emerald-200 px-5 py-3 rounded-2xl text-right">
                <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Total Encaissé Aujourd'hui</span>
                <span class="text-2xl font-black text-emerald-700">{{ number_format($todayTotal ?? 0, 0, ',', ' ') }} FCFA</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white hover:bg-red-50 text-red-600 hover:text-red-700 border border-red-200 hover:border-red-300 text-[13px] font-bold px-3.5 py-3.5 rounded-2xl transition-all flex items-center gap-1.5 cursor-pointer shadow-xs" title="Fermer la session de caisse">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="hidden sm:inline">Déconnexion</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Message de Succès / Émission de Quittance -->
    @if(session('success'))
        <div class="mb-8 p-5 rounded-2xl bg-emerald-500 text-white shadow-lg flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle-2" class="w-7 h-7"></i>
                <span class="text-[14px] font-bold">{{ session('success') }}</span>
            </div>
            <a href="{{ route('verifier') }}" class="px-4 py-2 bg-white text-emerald-800 rounded-xl text-[12px] font-black hover:bg-emerald-50 transition-colors shrink-0">
                Tester au vérificateur
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Formulaire d'Encaissement Rapide (5 cols) -->
        <div class="lg:col-span-5 bg-surface-container-lowest p-6 sm:p-7 rounded-3xl border border-border-subtle shadow-sm">
            <h2 class="text-base font-extrabold text-on-surface mb-2 flex items-center gap-2">
                <i data-lucide="banknote" class="w-5 h-5 text-secondary"></i>
                Enregistrer un Versement Client
            </h2>
            <p class="text-[12px] text-text-muted mb-5">
                Renseignez le numéro de commande et le montant reçu. Un code certifié à 6 caractères sera généré instantanément.
            </p>

            <form action="{{ route('caisse.encaissement') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">ID ou Réf Commande *</label>
                    <input type="number" name="order_id" required min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px] font-mono" placeholder="Ex: 1"/>
                    <span class="text-[10px] text-text-muted mt-1 block">Identifiant de commande du client (ex: 1 pour commande démo)</span>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Montant Encaissé (FCFA) *</label>
                    <input type="number" name="montant" required step="500" min="1000" class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[14px] font-bold" placeholder="Ex: 25000"/>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Mode d'Encaissement *</label>
                    <select name="canal" class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px] bg-white">
                        <option value="especes">Espèces (Billets au guichet)</option>
                        <option value="wave">Wave (Terminal caisse)</option>
                        <option value="orange_money">Orange Money (Terminal caisse)</option>
                        <option value="mtn_momo">MTN Mobile Money</option>
                        <option value="cheque">Chèque certifié</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Référence Transaction / N° Bordereau</label>
                    <input type="text" name="reference_externe" class="w-full px-3.5 py-2.5 rounded-xl border border-border-subtle focus:border-primary outline-none text-[13px]" placeholder="Ex: TX-WV-99381"/>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[13px] py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer mt-4">
                    <i data-lucide="receipt" class="w-4.5 h-4.5"></i>
                    <span>Valider & Générer Quittance Officielle</span>
                </button>
            </form>
        </div>

        <!-- Dernières Quittances Émises (7 cols) -->
        <div class="lg:col-span-7 bg-surface-container-lowest p-6 sm:p-7 rounded-3xl border border-border-subtle shadow-sm">
            <h2 class="text-base font-extrabold text-on-surface mb-4 flex items-center justify-between">
                <span>Dernières Quittances & Reçus Émis</span>
                <span class="text-[12px] text-text-muted">{{ $recentReceipts->count() }} récents</span>
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-[12px]">
                    <thead class="text-[11px] uppercase text-text-muted border-b border-border-subtle">
                        <tr>
                            <th class="pb-2.5">Code Reçu</th>
                            <th class="pb-2.5">Date & Heure</th>
                            <th class="pb-2.5">Montant</th>
                            <th class="pb-2.5">Canal</th>
                            <th class="pb-2.5 text-right">Contrôle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse($recentReceipts as $rc)
                            <tr>
                                <td class="py-3 font-mono font-black text-primary-container text-[13px]">
                                    {{ $rc->code_recu }}
                                </td>
                                <td class="py-3 text-text-muted">
                                    {{ $rc->date_paiement->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 font-bold text-on-surface">
                                    {{ number_format($rc->montant, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="py-3 capitalize text-text-muted">
                                    {{ str_replace('_', ' ', $rc->canal) }}
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('verifier', ['code' => $rc->code_recu]) }}" target="_blank" class="inline-flex items-center gap-1 text-secondary font-bold hover:underline">
                                        <span>Vérifier</span>
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-text-muted">Aucune quittance émise aujourd'hui.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
