<div class="max-w-2xl mx-auto bg-surface-container-lowest p-6 md:p-8 rounded-2xl shadow-xl border border-border-subtle">
    <div class="text-center mb-6">
        <div
            class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 shadow-xs">
            <i data-lucide="shield-check" class="w-8 h-8"></i>
        </div>
        <h2 class="font-headline-lg text-headline-lg font-bold text-on-surface">Vérifier un reçu de versement</h2>
        <p class="text-text-muted text-body-md mt-1">
            Contrôlez l'authenticité et la bonne imputation de votre paiement effectué auprès d'un commercial ou en
            caisse.
        </p>
    </div>

    <!-- Formulaire Code à 6 caractères -->
    <form wire:submit.prevent="verify" class="mb-6">
        <label class="block font-bold text-on-surface text-[14px] mb-2">
            Code de quittance à 6 caractères :
        </label>
        <div class="flex">
            <input wire:model="code" type="text" placeholder="Ex: 28B7S0" maxlength="8"
                class="flex-1 bg-canvas-background p-3.5 py-1 rounded-xl rounded-r-none border-r-0 font-mono text-[18px] font-black tracking-widest uppercase text-on-surface outline-none border-2 border-border-subtle focus:border-primary-container" />
            <button type="submit"
                class="bg-primary-container hover:bg-brand-red-hover text-on-primary font-bold text-[14px] px-3 py-2 rounded-xl rounded-l-none transition-all shadow-md flex items-center gap-1.5 shrink-0">
                <i data-lucide="search" class="w-4.5 h-4.5"></i>
                Vérifier
            </button>
        </div>
        <span class="text-[12px] text-text-muted block mt-2">
            Code alphanumérique à 6 caractères délivré par SMS ou sur votre reçu papier.
        </span>
    </form>

    <!-- Résultat : Message d'erreur -->
    @if($errorMessage)
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start gap-3 animate-cp-slide">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <div class="text-[13px] leading-relaxed">
                {{ $errorMessage }}
            </div>
        </div>
    @endif

    <!-- Résultat : Fiche Certifiée Conforme -->
    @if($receiptDetails)
        <div
            class="p-6 rounded-2xl bg-gradient-to-b from-emerald-50/60 to-white border-2 border-emerald-500 shadow-md animate-cp-slide space-y-4">
            <div class="flex items-center justify-between pb-3 border-emerald-200">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-600"></i>
                    <div>
                        <span class="text-[12px] font-extrabold uppercase text-emerald-700 block">Attestation
                            Numérique</span>
                        <span class="text-[16px] font-black text-on-surface">Reçu certifié authentique</span>
                    </div>
                </div>
                <span class="font-mono text-[14px] font-black bg-emerald-600 text-white px-2.5 py-1 rounded-md">
                    {{ $receiptDetails['code'] }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-[13px]">
                <div>
                    <span class="text-text-muted text-[11px] font-semibold uppercase block">Montant Encaissé</span>
                    <span class="text-[20px] font-black text-emerald-700">{{ $receiptDetails['montant'] }}</span>
                </div>
                <div>
                    <span class="text-text-muted text-[11px] font-semibold uppercase block">Date & Heure</span>
                    <span class="font-bold text-on-surface">{{ $receiptDetails['date'] }}</span>
                </div>
                <div>
                    <span class="text-text-muted text-[11px] font-semibold uppercase block">Moyen de règlement</span>
                    <span class="font-bold text-on-surface flex items-center gap-1 mt-0.5">
                        <i data-lucide="banknote" class="w-4 h-4 text-emerald-600"></i>
                        {{ $receiptDetails['canal_label'] }}
                    </span>
                </div>
                <div>
                    <span class="text-text-muted text-[11px] font-semibold uppercase block">N° Commande</span>
                    <span class="font-mono font-bold text-cobalt-deep">{{ $receiptDetails['commande'] }}</span>
                </div>
                <div>
                    <span class="text-text-muted text-[11px] font-semibold uppercase block">Bénéficiaire</span>
                    <span class="font-bold text-on-surface">{{ $receiptDetails['client'] }}</span>
                </div>
                <div>
                    <span class="text-text-muted text-[11px] font-semibold uppercase block">Guichet / Agent</span>
                    <span class="font-bold text-on-surface">{{ $receiptDetails['agent'] }}</span>
                </div>
            </div>

            <div class="pt-3 border-t border-emerald-100 flex items-center justify-between">
                <span class="text-[11px] text-text-muted flex items-center gap-1">
                    <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-600"></i>
                    Immutabilité certifiée par Cypres Pay CI
                </span>
                <button type="button" onclick="window.print()"
                    class="text-[12px] font-bold text-emerald-700 hover:underline flex items-center gap-1">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Imprimer le reçu
                </button>
            </div>
        </div>
    @endif
</div>