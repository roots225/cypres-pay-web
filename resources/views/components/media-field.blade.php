@props([
    'name',
    'label' => 'Média',
    'value' => null,
    'type' => 'image', // 'image', 'document', 'video', 'all'
    'required' => false,
    'help' => null,
    'aspectRatio' => 'square', // 'square', 'video', 'auto'
])

<div 
    x-data="cypressMediaField({
        initialValue: '{{ addslashes($value ?? '') }}',
        type: '{{ $type }}'
    })"
    class="space-y-2"
>
    <!-- Label -->
    <div class="flex items-center justify-between">
        <label class="block text-[12px] font-bold text-slate-700">
            {{ $label }}
            @if($required) <span class="text-red-500">*</span> @endif
        </label>
        <button 
            type="button" 
            @click="manualUrl = !manualUrl" 
            class="text-[11px] text-slate-400 hover:text-slate-600 transition-colors"
        >
            <span x-text="manualUrl ? 'Sélecteur visuel' : 'Saisir une URL directe'"></span>
        </button>
    </div>

    <!-- Champ input caché ou texte -->
    <input 
        type="hidden" 
        name="{{ $name }}" 
        x-model="currentValue" 
        @if($required) required @endif
    />

    <!-- Saisie URL directe manuelle alternative -->
    <div x-show="manualUrl" x-cloak class="space-y-1">
        <input 
            type="text" 
            x-model="currentValue" 
            placeholder="https://... ou /storage/media/..." 
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] bg-white"
        />
        <span class="text-[10px] text-slate-400 block">Lien direct vers l'image ou le document</span>
    </div>

    <!-- Sélecteur Visuel Haute Fidélité -->
    <div x-show="!manualUrl">
        <!-- État 1 : Aucun média sélectionné -->
        <template x-if="!currentValue">
            <div 
                @click="openPicker()"
                class="border-2 border-dashed border-slate-200 hover:border-primary/60 bg-slate-50/60 hover:bg-red-50/20 rounded-2xl p-6 transition-all duration-200 text-center cursor-pointer group flex flex-col items-center justify-center space-y-3"
            >
                <div class="w-14 h-14 rounded-2xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-slate-400 group-hover:text-primary group-hover:scale-105 transition-all">
                    <i data-lucide="{{ $type === 'image' ? 'image-plus' : 'file-up' }}" class="w-8 h-8"></i>
                </div>
                <div>
                    <span class="text-[13px] font-extrabold text-slate-800 block group-hover:text-primary transition-colors">
                        Choisir ou téléverser {{ $type === 'image' ? 'une image' : 'un fichier' }}
                    </span>
                    <span class="text-[11px] text-slate-400 block mt-0.5">
                        Glisser-déposer, sélectionner dans la médiathèque ou recadrer
                    </span>
                </div>
                <button 
                    type="button" 
                    class="px-4 py-1.5 rounded-xl bg-white border border-slate-200 group-hover:border-primary/40 text-[11px] font-bold text-slate-700 group-hover:text-primary shadow-xs transition-colors"
                >
                    Parcourir les médias
                </button>
            </div>
        </template>

        <!-- État 2 : Média sélectionné avec aperçu interactif -->
        <template x-if="currentValue">
            <div class="rounded-2xl border border-slate-200/90 bg-white p-3 shadow-xs space-y-3">
                <div class="flex items-center gap-3">
                    <!-- Miniature -->
                    <div class="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 shrink-0 relative flex items-center justify-center">
                        <template x-if="isImage(currentValue)">
                            <img 
                                :src="currentValue" 
                                alt="Aperçu sélection" 
                                class="w-full h-full object-cover"
                                onerror="this.onerror=null; this.src='https://placehold.co/100x100/f1f5f9/94a3b8?text=Image';"
                            />
                        </template>
                        <template x-if="!isImage(currentValue)">
                            <i data-lucide="file-text" class="w-7 h-7 text-slate-400"></i>
                        </template>
                    </div>

                    <!-- Infos & Liens -->
                    <div class="flex-1 min-w-0">
                        <span class="text-[12px] font-extrabold text-slate-900 block truncate" x-text="getFileName(currentValue)"></span>
                        <a 
                            :href="currentValue" 
                            target="_blank" 
                            class="text-[10px] text-primary hover:underline font-semibold block truncate" 
                            x-text="currentValue"
                        ></a>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Média prêt pour publication</span>
                    </div>

                    <!-- Actions rapides -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button 
                            type="button" 
                            @click="openPicker()" 
                            class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                            title="Remplacer le média"
                        >
                            <i data-lucide="refresh-cw" class="w-4.5 h-4.5"></i>
                        </button>

                        <button 
                            type="button" 
                            @click="removeValue()" 
                            class="p-2 rounded-xl text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors"
                            title="Retirer ce fichier"
                        >
                            <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="text-slate-400">Pour modifier le fichier ou le remplacer :</span>
                    <button 
                        type="button" 
                        @click="openPicker()" 
                        class="font-bold text-primary hover:underline inline-flex items-center gap-1"
                    >
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        <span>Ouvrir la médiathèque</span>
                    </button>
                </div>
            </div>
        </template>
    </div>

    @if($help)
        <span class="text-[10px] text-slate-400 mt-1 block">{{ $help }}</span>
    @endif
</div>

<script>
function cypressMediaField(config) {
    return {
        currentValue: config.initialValue || '',
        type: config.type || 'image',
        manualUrl: false,

        openPicker() {
            if (window.CypressMediaPicker) {
                window.CypressMediaPicker.open({
                    type: this.type,
                    title: this.type === 'image' ? 'Sélectionner une image de produit' : 'Sélectionner un fichier',
                    onSelect: (media) => {
                        this.currentValue = media.url;
                    }
                });
            } else {
                alert('Le gestionnaire de médias est en cours d\'initialisation...');
            }
        },

        removeValue() {
            this.currentValue = '';
        },

        isImage(url) {
            if (!url) return false;
            return /\.(jpg|jpeg|png|webp|gif|svg|avif)($|\?)/i.test(url) || url.startsWith('data:image/');
        },

        getFileName(url) {
            if (!url) return '';
            try {
                const parts = url.split('/');
                return parts[parts.length - 1] || url;
            } catch (e) {
                return url;
            }
        }
    };
}
</script>
