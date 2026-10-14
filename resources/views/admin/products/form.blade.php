@extends('layouts.admin')

@section('page-title', $isEdit ? "Modifier l'article : [{$product->reference}] {$product->nom}" : "Ajouter un nouvel article / kit")

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-1.5 text-[13px] font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <i data-lucide="arrow-left" class="w-4.5 h-4.5"></i>
            <span>Retour au catalogue</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-[13px] font-medium">
            <span class="font-bold block mb-1">Veuillez corriger les erreurs suivantes :</span>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm" x-data="productForm({{ $product->prix_vente ?? 0 }})">
        <form action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- 1. Référence & Informations Générales -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Réf / Code Produit *</label>
                    <input type="text" name="reference" value="{{ old('reference', $product->reference) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] font-mono font-bold uppercase" placeholder="Ex: N°731"/>
                    <span class="text-[10px] text-slate-400 mt-1 block">Identifiant unique (ex: N°019, N°731)</span>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Nom Complet de l'Article *</label>
                    <input type="text" name="nom" value="{{ old('nom', $product->nom) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Ex: Millennium Fontaine d'Eau Automatique avec Mini Frigo"/>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Catégorie *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] bg-white">
                        <option value="">-- Sélectionner une catégorie --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Marque / Fabricant</label>
                    <input type="text" name="marque" value="{{ old('marque', $product->marque) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Ex: Binatone, Samsung, Ilux, Super Wax..."/>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Stock Disponible *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 10) }}" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] font-bold" placeholder="10"/>
                </div>
            </div>

            <!-- 2. Tarification BNPL vs Cash (-7%) -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                <span class="text-xs font-black uppercase tracking-wider text-slate-900 block">Double Tarification Cypress Pay</span>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Prix Standard Vente (FCFA) *</label>
                        <input type="number" name="prix_vente" x-model="prixVente" @input="recalcCash()" required step="500" min="1000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[14px] font-black text-slate-900" placeholder="192000"/>
                        <span class="text-[10px] text-slate-400 mt-1 block">Base de calcul pour les échéanciers</span>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-emerald-800 mb-1">Prix Cash -7% Remise (FCFA)</label>
                        <input type="number" name="prix_cash" x-model="prixCash" step="50" min="500" class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 focus:border-emerald-500 outline-none text-[14px] font-black text-emerald-700 bg-white" placeholder="178560"/>
                        <span class="text-[10px] text-emerald-700 mt-1 block">Calculé automatiquement (-7% immédiat)</span>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1">Mensualité Indicative Dès (FCFA/m)</label>
                        <input type="number" name="mensualite_indicative" x-model="mensualite" step="100" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px] font-bold text-secondary bg-white" placeholder="8000"/>
                        <span class="text-[10px] text-slate-400 mt-1 block">Mention affichée sur les cartes</span>
                    </div>
                </div>
            </div>

            <!-- 3. Visuels & Galerie Multi-Images du Produit -->
            <div 
                class="p-5 sm:p-6 rounded-3xl bg-slate-50 border border-slate-200/90 space-y-5"
                x-data="productImagesManager({
                    initialPrimary: '{{ addslashes(old('image_principale', $product->image_principale ?? '')) }}',
                    initialGallery: {{ json_encode(old('galerie_images', $product->galerie_images ?? [])) }}
                })"
            >
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black uppercase tracking-wider text-slate-900">
                                Galerie Multi-Images de l'Article
                            </span>
                            <span 
                                class="px-2 py-0.5 rounded-full text-[10px] font-black"
                                :class="images.length > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                x-text="images.length + ' image(s)'"
                            ></span>
                        </div>
                        <span class="text-[11px] text-slate-500 block mt-0.5">
                            La première image est la couverture catalogue. Vous pouvez réordonner, recadrer ou changer l'image principale à tout moment.
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="openMediaPickerModal()"
                            class="px-4 py-2 rounded-xl bg-primary hover:bg-brand-red-hover text-white text-[12px] font-extrabold shadow-md transition-all flex items-center gap-1.5 cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-[18px]">add_photo_alternate</span>
                            <span>Ajouter des photos</span>
                        </button>
                    </div>
                </div>

                <!-- Inputs cachés pour soumission Laravel -->
                <input type="hidden" name="image_principale" :value="primaryImage" required />
                <template x-for="(img, idx) in secondaryImages" :key="idx">
                    <input type="hidden" name="galerie_images[]" :value="img" />
                </template>

                <!-- État Vide : Aucune image -->
                <div 
                    x-show="images.length === 0" 
                    @click="openMediaPickerModal()"
                    class="border-2 border-dashed border-slate-300 hover:border-primary/80 bg-white hover:bg-red-50/20 rounded-2xl p-8 text-center cursor-pointer transition-all flex flex-col items-center justify-center space-y-3 group"
                >
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 group-hover:bg-primary/10 text-slate-400 group-hover:text-primary flex items-center justify-center transition-all">
                        <span class="material-symbols-outlined text-[36px]">add_photo_alternate</span>
                    </div>
                    <div>
                        <span class="text-[13px] font-extrabold text-slate-800 block group-hover:text-primary">
                            Cliquez pour choisir ou téléverser les photos du produit
                        </span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">
                            Sélectionnez plusieurs images simultanément dans la médiathèque
                        </span>
                    </div>
                    <button type="button" class="px-4 py-2 rounded-xl bg-slate-100 group-hover:bg-primary group-hover:text-white text-slate-700 text-[11px] font-extrabold transition-all">
                        Parcourir la Médiathèque
                    </button>
                </div>

                <!-- Grille des Multiples Images du Produit -->
                <div x-show="images.length > 0" class="space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5">
                        <template x-for="(img, index) in images" :key="index">
                            <div 
                                class="group relative rounded-2xl border bg-white overflow-hidden shadow-xs flex flex-col transition-all duration-200"
                                :class="index === 0 ? 'ring-2 ring-primary border-primary' : 'border-slate-200 hover:border-slate-300 hover:shadow-md'"
                            >
                                <!-- Badge Image Principale (index 0) -->
                                <template x-if="index === 0">
                                    <div class="absolute top-2 left-2 z-10 px-2 py-0.5 rounded-md bg-primary text-white text-[9px] font-black uppercase tracking-wider shadow-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">star</span>
                                        <span>Principale</span>
                                    </div>
                                </template>

                                <!-- Ordre de la photo -->
                                <div class="absolute top-2 right-2 z-10 w-5 h-5 rounded-full bg-black/60 text-white text-[10px] font-black flex items-center justify-center backdrop-blur-xs">
                                    <span x-text="index + 1"></span>
                                </div>

                                <!-- Image Miniature -->
                                <div class="aspect-square w-full bg-slate-100 relative overflow-hidden flex items-center justify-center">
                                    <img 
                                        :src="img" 
                                        alt="Visuel produit" 
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        onerror="this.onerror=null; this.src='https://placehold.co/400x400/f1f5f9/94a3b8?text=Image';"
                                    />

                                    <!-- Overlay d'actions au survol -->
                                    <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                                        <!-- Actions du haut : Recadrer & Supprimer -->
                                        <div class="flex items-center justify-end gap-1">
                                            <button 
                                                type="button" 
                                                @click="cropImage(index)"
                                                class="w-7 h-7 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 flex items-center justify-center shadow-xs cursor-pointer"
                                                title="Recadrer au format 1:1"
                                            >
                                                <span class="material-symbols-outlined text-[15px]">crop</span>
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="removeImage(index)"
                                                class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-500 text-white flex items-center justify-center shadow-xs cursor-pointer"
                                                title="Retirer cette photo"
                                            >
                                                <span class="material-symbols-outlined text-[15px]">delete</span>
                                            </button>
                                        </div>

                                        <!-- Actions du bas : Réordonner (flèches) & Définir principale -->
                                        <div class="flex items-center justify-between gap-1 pt-1 border-t border-white/20">
                                            <div class="flex items-center gap-1">
                                                <button 
                                                    type="button" 
                                                    @click="moveImage(index, -1)" 
                                                    :disabled="index === 0"
                                                    class="w-6 h-6 rounded-md bg-white/20 hover:bg-white text-white hover:text-slate-900 disabled:opacity-20 flex items-center justify-center transition-colors cursor-pointer"
                                                    title="Déplacer à gauche"
                                                >
                                                    <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    @click="moveImage(index, 1)" 
                                                    :disabled="index === images.length - 1"
                                                    class="w-6 h-6 rounded-md bg-white/20 hover:bg-white text-white hover:text-slate-900 disabled:opacity-20 flex items-center justify-center transition-colors cursor-pointer"
                                                    title="Déplacer à droite"
                                                >
                                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                                </button>
                                            </div>

                                            <template x-if="index !== 0">
                                                <button 
                                                    type="button" 
                                                    @click="setAsPrimary(index)"
                                                    class="px-2 py-1 rounded-md bg-white text-slate-900 text-[10px] font-black uppercase tracking-wider hover:bg-emerald-500 hover:text-white transition-colors cursor-pointer"
                                                    title="Mettre en image principale"
                                                >
                                                    En 1er
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Légende sous miniature -->
                                <div class="p-2 text-center bg-white border-t border-slate-100">
                                    <span 
                                        class="text-[10px] font-bold block truncate"
                                        :class="index === 0 ? 'text-primary font-black' : 'text-slate-500'"
                                        x-text="index === 0 ? '⭐ Couverture' : 'Vue ' + (index + 1)"
                                    ></span>
                                </div>
                            </div>
                        </template>

                        <!-- Carte Ajouter une image supplémentaire -->
                        <div 
                            @click="openMediaPickerModal()"
                            class="border-2 border-dashed border-slate-200 hover:border-primary/80 bg-white hover:bg-red-50/20 rounded-2xl aspect-square flex flex-col items-center justify-center p-3 text-center cursor-pointer transition-all group"
                        >
                            <div class="w-10 h-10 rounded-xl bg-slate-100 group-hover:bg-primary/10 text-slate-400 group-hover:text-primary flex items-center justify-center transition-all mb-1">
                                <span class="material-symbols-outlined text-[22px]">add</span>
                            </div>
                            <span class="text-[11px] font-extrabold text-slate-700 group-hover:text-primary">Ajouter</span>
                            <span class="text-[9px] text-slate-400">Photo supl.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Descriptions -->
            <div class="space-y-4">
                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Description Courte</label>
                    <textarea name="description_courte" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Résumé en 1 à 2 phrases pour le catalogue...">{{ old('description_courte', $product->description_courte) }}</textarea>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1">Description Détaillée & Conditions</label>
                    <textarea name="description_complete" rows="4" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary outline-none text-[13px]" placeholder="Caractéristiques complètes, garanties constructeur, contenu du pack...">{{ old('description_complete', $product->description_complete) }}</textarea>
                </div>
            </div>

            <!-- 5. Toggles de Mise en Avant -->
            <div class="pt-4 border-t border-slate-100">
                <span class="text-xs font-black uppercase tracking-wider text-slate-900 block mb-3">Mise en avant & Statuts</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[12px] font-bold">
                        <input type="checkbox" name="is_kit" value="1" {{ old('is_kit', $product->is_kit) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500"/>
                        <span>📦 Kit Maison</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[12px] font-bold">
                        <input type="checkbox" name="is_flash" value="1" {{ old('is_flash', $product->is_flash) ? 'checked' : '' }} class="rounded text-red-600 focus:ring-red-500"/>
                        <span>⚡ Vente Flash</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[12px] font-bold">
                        <input type="checkbox" name="is_vedette" value="1" {{ old('is_vedette', $product->is_vedette) ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-400"/>
                        <span>⭐ Coup de Cœur</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer text-[12px] font-bold">
                        <input type="checkbox" name="en_stock" value="1" {{ old('en_stock', $product->en_stock ?? true) ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500"/>
                        <span>✅ En Stock</span>
                    </label>
                </div>
            </div>

            <!-- Boutons de soumission -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-[13px] font-bold transition-colors">
                    Annuler
                </a>
                <button type="submit" class="bg-primary hover:bg-brand-red-hover text-white text-[13px] font-extrabold px-6 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                    <i data-lucide="save" class="w-4.5 h-4.5"></i>
                    <span>{{ $isEdit ? 'Mettre à jour l\'article' : 'Enregistrer au catalogue' }}</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
function productForm(initialPrix) {
    return {
        prixVente: initialPrix || '{{ old('prix_vente', $product->prix_vente ?? 0) }}',
        prixCash: '{{ old('prix_cash', $product->prix_cash ?? 0) }}',
        mensualite: '{{ old('mensualite_indicative', $product->mensualite_indicative ?? 0) }}',

        recalcCash() {
            const v = Number(this.prixVente) || 0;
            this.prixCash = Math.round(v * 0.93);
            this.mensualite = Math.round(v / 12);
        }
    };
}

function productImagesManager(config) {
    let initialList = [];
    if (config.initialPrimary && config.initialPrimary.trim()) {
        initialList.push(config.initialPrimary.trim());
    }
    if (Array.isArray(config.initialGallery)) {
        config.initialGallery.forEach(img => {
            if (img && typeof img === 'string' && img.trim() && !initialList.includes(img.trim())) {
                initialList.push(img.trim());
            }
        });
    }

    return {
        images: initialList,

        get primaryImage() {
            return this.images[0] || '';
        },

        get secondaryImages() {
            return this.images.slice(1);
        },

        openMediaPickerModal() {
            if (window.CypressMediaPicker) {
                window.CypressMediaPicker.open({
                    type: 'image',
                    multiple: true,
                    title: 'Choisir les photos de l\'article',
                    onSelect: (selected) => {
                        const items = Array.isArray(selected) ? selected : [selected];
                        items.forEach(media => {
                            if (media && media.url && !this.images.includes(media.url)) {
                                this.images.push(media.url);
                            }
                        });
                    }
                });
            }
        },

        setAsPrimary(index) {
            if (index <= 0 || index >= this.images.length) return;
            const item = this.images.splice(index, 1)[0];
            this.images.unshift(item);
        },

        moveImage(index, direction) {
            const targetIndex = index + direction;
            if (targetIndex < 0 || targetIndex >= this.images.length) return;
            const temp = this.images[index];
            this.images[index] = this.images[targetIndex];
            this.images[targetIndex] = temp;
        },

        removeImage(index) {
            this.images.splice(index, 1);
        },

        cropImage(index) {
            const targetUrl = this.images[index];
            if (!targetUrl) return;

            if (window.CypressMediaPicker) {
                window.CypressMediaPicker.open({
                    type: 'image',
                    title: 'Recadrer la photo ' + (index === 0 ? '(Couverture)' : '(Vue ' + (index + 1) + ')'),
                    onSelect: (croppedMedia) => {
                        if (croppedMedia && croppedMedia.url) {
                            this.images[index] = croppedMedia.url;
                        }
                    }
                });

                // Tenter d'ouvrir directement le recadreur sur cette image
                setTimeout(() => {
                    const picker = document.querySelector('#cypress-media-picker-modal');
                    if (picker && picker._x_dataStack && picker._x_dataStack[0]) {
                        picker._x_dataStack[0].openCropModal({
                            id: 0,
                            name: 'Photo produit ' + (index + 1),
                            url: targetUrl,
                            type: 'image'
                        });
                    }
                }, 250);
            }
        }
    };
}
</script>
@endsection
