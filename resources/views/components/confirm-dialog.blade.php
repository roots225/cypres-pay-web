<!-- ══════════════════════════════════════════════════════════════════
     COMPOSANT REUTILISABLE MODERNE : DIALOG DE CONFIRMATION
     Usage:
       1) window.confirmAction({
            title: 'Titre',
            message: 'Description...',
            confirmText: 'Confirmer',
            cancelText: 'Annuler',
            type: 'danger' | 'warning' | 'info',
            icon: 'trash-2' | 'log-out' | 'alert-triangle',
            onConfirm: () => { ... } OU form: document.getElementById('mon-form')
          });
       2) Sur n'importe quel bouton ou formulaire avec data-confirm="Message..."
══════════════════════════════════════════════════════════════════ -->
<div 
    x-data="confirmDialogComponent()"
    x-init="init()"
    @open-confirm-dialog.window="show($event.detail)"
    @keydown.escape.window="if (open) cancel()"
    class="relative z-[100]"
    style="display: none;"
    x-show="open"
>
    <!-- Overlay d'arrière-plan avec flou moderne -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        @click="cancel()"
    ></div>

    <!-- Conteneur centré du Dialog -->
    <div class="fixed inset-0 z-[101] overflow-y-auto flex items-center justify-center p-4 sm:p-6 text-center">
        <div 
            x-show="open"
            x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-250"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-3"
            class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 sm:p-7 text-left shadow-2xl border border-slate-100 transition-all"
            @click.stop
        >
            <!-- Bouton de fermeture discret en haut à droite -->
            <button 
                type="button" 
                @click="cancel()" 
                class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition-colors p-1.5 rounded-xl hover:bg-slate-100 focus:outline-none"
            >
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <!-- Icône & Indicateur d'état -->
            <div class="flex items-start gap-4">
                <div 
                    class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xs"
                    :class="{
                        'bg-red-50 text-red-600 ring-8 ring-red-50/60': type === 'danger',
                        'bg-amber-50 text-amber-600 ring-8 ring-amber-50/60': type === 'warning',
                        'bg-blue-50 text-blue-600 ring-8 ring-blue-50/60': type === 'info'
                    }"
                >
                    <i :data-lucide="icon" class="w-6 h-6"></i>
                </div>

                <div class="flex-1 pr-6">
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug" x-text="title"></h3>
                    <p class="text-[13px] text-slate-500 mt-1.5 leading-relaxed" x-text="message"></p>
                </div>
            </div>

            <!-- Boutons d'Action -->
            <div class="mt-7 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button 
                    type="button" 
                    @click="cancel()"
                    class="px-4 py-2.5 rounded-xl text-[13px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer focus:outline-none"
                    x-text="cancelText"
                ></button>

                <button 
                    type="button" 
                    @click="confirm()"
                    :disabled="loading"
                    class="px-5 py-2.5 rounded-xl text-[13px] font-extrabold text-white transition-all flex items-center gap-2 cursor-pointer shadow-md focus:outline-none disabled:opacity-60"
                    :class="{
                        'bg-red-600 hover:bg-red-700 shadow-red-600/25': type === 'danger',
                        'bg-amber-600 hover:bg-amber-700 shadow-amber-600/25': type === 'warning',
                        'bg-blue-600 hover:bg-blue-700 shadow-blue-600/25': type === 'info'
                    }"
                >
                    <template x-if="loading">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    </template>
                    <span x-text="confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDialogComponent() {
    return {
        open: false,
        title: 'Confirmation requise',
        message: 'Êtes-vous sûr de vouloir effectuer cette action ?',
        confirmText: 'Confirmer',
        cancelText: 'Annuler',
        type: 'danger',
        icon: 'alert-triangle',
        loading: false,
        onConfirmCallback: null,
        formToSubmit: null,

        init() {
            // Exposer au scope global
            window.showConfirmDialog = this.show.bind(this);
            window.confirmAction = this.show.bind(this);

            // Écouteur global pour intercepter les boutons ou formulaires marqués avec data-confirm
            document.addEventListener('click', (e) => {
                const target = e.target.closest('[data-confirm]');
                if (!target) return;

                // Si c'est un bouton de formulaire
                const form = target.closest('form');
                const message = target.getAttribute('data-confirm');
                const title = target.getAttribute('data-confirm-title') || 'Confirmation requise';
                const confirmText = target.getAttribute('data-confirm-button') || 'Confirmer';
                const type = target.getAttribute('data-confirm-type') || 'danger';
                const icon = target.getAttribute('data-confirm-icon') || (type === 'danger' ? 'trash-2' : 'alert-triangle');

                e.preventDefault();
                e.stopPropagation();

                this.show({
                    title: title,
                    message: message,
                    confirmText: confirmText,
                    type: type,
                    icon: icon,
                    form: form
                });
            }, true);

            // Intercepteur automatique pour tous les formulaires avec onsubmit="return confirm(...)"
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!form || form._cpConfirmed) return;

                const onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Extraire le message
                    const match = onsubmitAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
                    const message = match ? match[1] : 'Êtes-vous sûr de vouloir exécuter cette opération ?';
                    const isDelete = message.toLowerCase().includes('supprim') || message.toLowerCase().includes('révoc');

                    this.show({
                        title: isDelete ? 'Confirmation de suppression' : 'Confirmation requise',
                        message: message,
                        confirmText: isDelete ? 'Oui, supprimer' : 'Confirmer',
                        cancelText: 'Annuler',
                        type: isDelete ? 'danger' : 'warning',
                        icon: isDelete ? 'trash-2' : 'alert-triangle',
                        form: form
                    });
                }
            }, true);
        },

        show(options = {}) {
            this.title = options.title || 'Confirmation requise';
            this.message = options.message || 'Cette action ne pourra pas être annulée.';
            this.confirmText = options.confirmText || 'Confirmer';
            this.cancelText = options.cancelText || 'Annuler';
            this.type = options.type || 'danger';
            this.icon = options.icon || (this.type === 'danger' ? 'alert-triangle' : (this.type === 'warning' ? 'alert-circle' : 'info'));
            this.onConfirmCallback = typeof options.onConfirm === 'function' ? options.onConfirm : null;
            this.formToSubmit = options.form || null;
            this.loading = false;
            this.open = true;

            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        },

        confirm() {
            this.loading = true;

            if (this.onConfirmCallback) {
                try {
                    const result = this.onConfirmCallback();
                    if (result instanceof Promise) {
                        result.finally(() => {
                            this.open = false;
                            this.loading = false;
                        });
                        return;
                    }
                } catch (e) {
                    console.error('Erreur dans le callback de confirmation:', e);
                }
                this.open = false;
                this.loading = false;
            } else if (this.formToSubmit) {
                let f = this.formToSubmit;
                if (typeof f === 'string') {
                    f = document.getElementById(f);
                }
                if (f && typeof f.submit === 'function') {
                    f._cpConfirmed = true;
                    // Supprimer l'attribut onsubmit s'il existe pour éviter la double popup native
                    f.removeAttribute('onsubmit');
                    f.submit();
                } else {
                    this.open = false;
                    this.loading = false;
                }
            } else {
                this.open = false;
                this.loading = false;
            }
        },

        cancel() {
            this.open = false;
            this.loading = false;
            this.onConfirmCallback = null;
            this.formToSubmit = null;
        }
    };
}
</script>
