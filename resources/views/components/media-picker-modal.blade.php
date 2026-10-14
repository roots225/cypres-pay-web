{{-- Modale Universelle du Gestionnaire de Médias & Outil de Rognage Pay --}}
<div id="cypress-media-picker-modal" x-data="cypressMediaPicker()" x-init="init()" x-show="isOpen" x-cloak
    class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;" @keydown.escape.window="close()">
    <!-- Overlay d'arrière-plan avec flou -->
    <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" @click="close()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-3 sm:p-6 flex items-center justify-center">
        <!-- Conteneur principal de la Modale -->
        <div x-show="isOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-6xl h-[90vh] bg-white rounded-3xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden"
            @click.stop>

            <!-- ══════════════════════════════════════════════════════════
              HEADER DE LA MODALE
            ══════════════════════════════════════════════════════════ -->
            <div
                class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-md">
                        <i data-lucide="image" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold tracking-tight" x-text="modalTitle">Médiathèque Cypress Pay
                        </h2>
                        <div class="flex items-center gap-2 text-[11px] text-slate-300">
                            <span x-show="typeFilter !== 'all'"
                                class="px-2 py-0.5 rounded-full bg-white/10 font-bold text-amber-300">
                                Type filtré : <span class="capitalize" x-text="typeFilter"></span> uniquement
                            </span>
                            <span x-show="multiple"
                                class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 font-bold">
                                Sélection multiple autorisée
                            </span>
                            <span class="text-slate-400">Tous types de fichiers supportés</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="close()"
                        class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer"
                        title="Fermer (Échap)">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════
              ONGLETS & BARRE D'OUTILS
            ══════════════════════════════════════════════════════════ -->
            <div
                class="px-6 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 shrink-0">
                <!-- Onglets -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-200/70 rounded-xl text-[12px] font-bold">
                    <button @click="activeTab = 'library'"
                        :class="activeTab === 'library' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="grid" class="w-4 h-4"></i>
                        <span>Bibliothèque</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600"
                            x-text="totalItems">0</span>
                    </button>
                    <button @click="activeTab = 'upload'"
                        :class="activeTab === 'upload' ? 'bg-white text-primary shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                        <span>Téléverser des fichiers</span>
                    </button>
                </div>

                <!-- Filtres et Recherche (uniquement visible sur l'onglet Bibliothèque) -->
                <div x-show="activeTab === 'library'" class="flex items-center gap-2.5 flex-1 max-w-xl justify-end">
                    <!-- Recherche instantanée -->
                    <div class="relative flex-1 max-w-xs">
                        <i data-lucide="search"
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4.5 h-4.5"></i>
                        <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Rechercher par nom..."
                            class="w-full pl-9 pr-3 py-1.5 rounded-xl border border-slate-200 bg-white text-[12px] font-medium outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all" />
                        <button x-show="searchQuery" @click="searchQuery = ''; fetchMedias()"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <!-- Sélecteur de type (si non verrouillé) -->
                    <template x-if="!lockedType">
                        <select x-model="typeFilter" @change="fetchMedias()"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-[12px] font-bold text-slate-700 outline-none focus:border-primary cursor-pointer">
                            <option value="all">Tous les fichiers</option>
                            <option value="image">Images uniquement</option>
                            <option value="document">Documents (PDF, Doc...)</option>
                            <option value="video">Vidéos</option>
                            <option value="audio">Audios</option>
                            <option value="archive">Archives (Zip...)</option>
                        </select>
                    </template>

                    <!-- Tri -->
                    <select x-model="sortBy" @change="fetchMedias()"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-[12px] font-bold text-slate-700 outline-none focus:border-primary cursor-pointer hidden sm:block">
                        <option value="latest">Récents d'abord</option>
                        <option value="oldest">Anciens d'abord</option>
                        <option value="name_asc">Nom (A → Z)</option>
                        <option value="size_desc">Plus volumineux</option>
                    </select>

                    <button @click="fetchMedias()"
                        class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-200/60 rounded-lg transition-colors cursor-pointer"
                        title="Actualiser la liste">
                        <i data-lucide="rotate-cw" class="w-4.5 h-4.5" :class="{ 'animate-spin': loading }"></i>
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════
              CONTENU : ONGLETS BIBLIOTHÈQUE / TÉLÉVERSEMENT
            ══════════════════════════════════════════════════════════ -->
            <div class="flex-1 flex overflow-hidden">

                <!-- ────────────────────────────────────────────────────────
                  ONGLET 1 : GRILLE DE LA BIBLIOTHÈQUE & DÉTAILS
                ──────────────────────────────────────────────────────── -->
                <div x-show="activeTab === 'library'" class="flex-1 flex overflow-hidden">

                    <!-- Zone de la grille -->
                    <div class="flex-1 p-5 overflow-y-auto space-y-4">

                        <!-- Zone de dépôt rapide en haut de grille -->
                        <div @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false"
                            @drop.prevent="handleQuickDrop($event)"
                            :class="dragOver ? 'border-primary bg-primary/5 scale-[1.01]' : 'border-dashed border-slate-200 hover:border-slate-300 bg-slate-50/50'"
                            class="border-2 rounded-2xl p-4 transition-all text-center flex items-center justify-center gap-3 cursor-pointer group"
                            @click="$refs.quickFileInput.click()">
                            <input type="file" x-ref="quickFileInput" class="hidden" :accept="getAcceptAttribute()"
                                :multiple="true" @change="uploadFiles($event.target.files)" />
                            <div
                                class="w-9 h-9 rounded-xl bg-white shadow-sm flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                            </div>
                            <div class="text-left">
                                <span class="text-[12px] font-extrabold text-slate-800 block">
                                    Déposer un nouveau fichier ici ou <span
                                        class="text-primary underline">parcourir</span>
                                </span>
                                <span class="text-[11px] text-slate-400 block"
                                    x-text="typeFilter !== 'all' ? 'Seuls les fichiers de type ' + typeFilter + ' sont autorisés' : 'Images, documents PDF, vidéos, audios acceptés'"></span>
                            </div>
                        </div>

                        <!-- État de chargement -->
                        <div x-show="loading && medias.length === 0" class="py-20 text-center space-y-3">
                            <div
                                class="w-12 h-12 border-3 border-primary/20 border-t-primary rounded-full animate-spin mx-auto">
                            </div>
                            <span class="text-[13px] font-bold text-slate-500 block">Chargement de la
                                médiathèque...</span>
                        </div>

                        <!-- État vide -->
                        <div x-show="!loading && medias.length === 0" class="py-20 text-center space-y-3">
                            <div
                                class="w-16 h-16 rounded-3xl bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                <i data-lucide="folder-open" class="w-9 h-9"></i>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-800">Aucun fichier trouvé</h3>
                            <p class="text-[12px] text-slate-400 max-w-sm mx-auto">
                                Aucun média ne correspond à vos critères de recherche ou de filtre. Téléversez un
                                nouveau fichier pour commencer !
                            </p>
                            <button @click="activeTab = 'upload'"
                                class="mt-2 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-[12px] font-bold shadow-md hover:bg-brand-red-hover transition-all cursor-pointer">
                                <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                                <span>Téléverser maintenant</span>
                            </button>
                        </div>

                        <!-- Grille des médias -->
                        <div x-show="medias.length > 0"
                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                            <template x-for="item in medias" :key="item.id">
                                <div @click="toggleSelect(item)"
                                    class="group relative rounded-2xl border transition-all duration-200 cursor-pointer overflow-hidden flex flex-col bg-white"
                                    :class="isSelected(item) ? 'ring-2 ring-primary border-primary shadow-lg scale-[1.02]' : 'border-slate-200 hover:border-slate-300 hover:shadow-md'">
                                    <!-- Coche de sélection en haut à droite -->
                                    <div x-show="isSelected(item)"
                                        class="absolute top-2 right-2 z-10 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center shadow-md animate-scale-in">
                                        <i data-lucide="check" class="w-4 h-4 font-black"></i>
                                    </div>

                                    <!-- Zone d'aperçu média -->
                                    <div
                                        class="aspect-square w-full bg-slate-100 relative overflow-hidden flex items-center justify-center">
                                        <!-- Cas Image -->
                                        <template x-if="item.type === 'image'">
                                            <img :src="item.url" :alt="item.alt || item.name"
                                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                                loading="lazy"
                                                onerror="this.onerror=null; this.src='https://placehold.co/400x400/f1f5f9/94a3b8?text=Image';" />
                                        </template>

                                        <!-- Cas Document / Vidéo / Audio / Autre -->
                                        <template x-if="item.type !== 'image'">
                                            <div
                                                class="flex flex-col items-center justify-center p-4 text-center space-y-1">
                                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-sm"
                                                    :class="{
                                                        'bg-red-500': item.extension === 'pdf',
                                                        'bg-blue-500': item.type === 'document' && item.extension !== 'pdf',
                                                        'bg-purple-500': item.type === 'video',
                                                        'bg-amber-500': item.type === 'audio',
                                                        'bg-orange-500': item.type === 'archive',
                                                        'bg-slate-500': item.type === 'other'
                                                    }">
                                                    <i :data-lucide="item.icon || 'file'" class="w-6 h-6"></i>
                                                </div>
                                                <span
                                                    class="text-[10px] font-black uppercase tracking-wider text-slate-500"
                                                    x-text="item.extension || item.type"></span>
                                            </div>
                                        </template>

                                        <!-- Badge de type en bas à gauche de la miniature -->
                                        <div
                                            class="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-sm text-white text-[9px] font-bold uppercase tracking-wider">
                                            <span x-text="item.extension || item.type"></span>
                                        </div>
                                    </div>

                                    <!-- Métadonnées succinctes -->
                                    <div
                                        class="p-2.5 bg-white border-t border-slate-100 flex-1 flex flex-col justify-between">
                                        <span class="text-[12px] font-bold text-slate-800 truncate block"
                                            :title="item.name" x-text="item.name"></span>
                                        <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                                            <span x-text="item.size_human"></span>
                                            <span x-text="item.width ? item.width + '×' + item.height : ''"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Bouton Charger Plus (Pagination) -->
                        <div x-show="hasMorePages" class="text-center pt-4">
                            <button @click="loadMore()" :disabled="loadingMore"
                                class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[12px] font-bold transition-all inline-flex items-center gap-2 cursor-pointer">
                                <i data-lucide="rotate-cw" class="w-4 h-4" :class="{ 'animate-spin': loadingMore }"></i>
                                <span x-text="loadingMore ? 'Chargement...' : 'Afficher plus de fichiers'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- ──────────────────────────────────────────────────
                      PANNEAU LATÉRAL : DÉTAILS DU MÉDIA SÉLECTIONNÉ
                    ────────────────────────────────────────────────── -->
                    <div x-show="selectedMedia"
                        class="w-80 border-l border-slate-200 bg-slate-50 p-5 overflow-y-auto shrink-0 flex flex-col justify-between space-y-5">
                        <div class="space-y-4">
                            <!-- Header détails -->
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Détails du
                                    média</span>
                                <button @click="selectedMedia = null" class="text-slate-400 hover:text-slate-600">
                                    <i data-lucide="x" class="w-4.5 h-4.5"></i>
                                </button>
                            </div>

                            <!-- Aperçu grand format -->
                            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden p-2 shadow-sm">
                                <template x-if="selectedMedia && selectedMedia.type === 'image'">
                                    <img :src="selectedMedia.url" :alt="selectedMedia.name"
                                        class="w-full max-h-48 object-contain rounded-xl mx-auto" />
                                </template>
                                <template x-if="selectedMedia && selectedMedia.type !== 'image'">
                                    <div class="py-8 text-center space-y-2">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center mx-auto">
                                            <i :data-lucide="selectedMedia.icon || 'file'" class="w-8 h-8"></i>
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-600 uppercase"
                                            x-text="selectedMedia.mime_type"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Bouton spécial : Recadrer l'image (si image) -->
                            <template x-if="selectedMedia && selectedMedia.type === 'image'">
                                <button @click="openCropModal(selectedMedia)"
                                    class="w-full py-2.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-extrabold text-[12px] shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                                    <i data-lucide="crop" class="w-4.5 h-4.5"></i>
                                    <span>Recadrer / Rogner l'image</span>
                                </button>
                            </template>

                            <!-- Informations techniques -->
                            <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 space-y-2 text-[11px]">
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Nom du fichier :</span>
                                    <span class="font-bold text-slate-700 truncate max-w-[140px]"
                                        :title="selectedMedia?.original_name || selectedMedia?.file_name"
                                        x-text="selectedMedia?.file_name"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Poids :</span>
                                    <span class="font-bold text-slate-700" x-text="selectedMedia?.size_human"></span>
                                </div>
                                <template x-if="selectedMedia?.width">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Dimensions :</span>
                                        <span class="font-bold text-slate-700"
                                            x-text="selectedMedia?.width + ' × ' + selectedMedia?.height + ' px'"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Type :</span>
                                    <span class="font-bold text-slate-700 uppercase"
                                        x-text="selectedMedia?.extension || selectedMedia?.type"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Date d'ajout :</span>
                                    <span class="font-bold text-slate-700"
                                        x-text="formatDate(selectedMedia?.created_at)"></span>
                                </div>
                            </div>

                            <!-- Formulaire de modification rapide métadonnées -->
                            <div class="space-y-2.5">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Titre / Nom du
                                        fichier</label>
                                    <input type="text" x-model="selectedMedia.name"
                                        @change="updateMediaMetadata(selectedMedia)"
                                        class="w-full px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-[12px] font-medium outline-none focus:border-primary" />
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Texte alternatif (Alt
                                        SEO)</label>
                                    <input type="text" x-model="selectedMedia.alt"
                                        @change="updateMediaMetadata(selectedMedia)"
                                        placeholder="Description pour l'accessibilité..."
                                        class="w-full px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-[12px] font-medium outline-none focus:border-primary" />
                                </div>

                                <!-- Copier le lien public -->
                                <button @click="copyUrl(selectedMedia.url)"
                                    class="w-full py-2 px-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-[11px] transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                    <i data-lucide="copy" class="w-4 h-4"></i>
                                    <span>Copier l'URL publique</span>
                                </button>
                            </div>
                        </div>

                        <!-- Actions destructives / Supprimer -->
                        <div class="pt-3 border-t border-slate-200">
                            <button @click="deleteMedia(selectedMedia)"
                                class="w-full py-2 px-3 rounded-xl text-red-600 hover:bg-red-50 border border-red-200 font-bold text-[11px] transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                <span>Supprimer ce média</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ────────────────────────────────────────────────────────
                  ONGLET 2 : ZONE D'UPLOAD DÉDIÉE (DRAG & DROP GRAND FORMAT)
                ──────────────────────────────────────────────────────── -->
                <div x-show="activeTab === 'upload'"
                    class="flex-1 p-8 overflow-y-auto flex flex-col items-center justify-center">
                    <div class="max-w-xl w-full text-center space-y-6">

                        <div @dragover.prevent="dragOverTab = true" @dragleave.prevent="dragOverTab = false"
                            @drop.prevent="handleTabDrop($event)"
                            :class="dragOverTab ? 'border-primary bg-primary/5 scale-[1.02]' : 'border-dashed border-slate-300 hover:border-primary bg-slate-50'"
                            class="border-3 rounded-3xl p-10 transition-all cursor-pointer flex flex-col items-center justify-center space-y-4 shadow-sm"
                            @click="$refs.tabFileInput.click()">
                            <input type="file" x-ref="tabFileInput" class="hidden" :accept="getAcceptAttribute()"
                                :multiple="true" @change="uploadFiles($event.target.files)" />

                            <div
                                class="w-20 h-20 rounded-3xl bg-primary/10 text-primary flex items-center justify-center shadow-inner">
                                <i data-lucide="upload-cloud" class="w-10 h-10"></i>
                            </div>

                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">
                                    Glissez-déposez vos fichiers ici
                                </h3>
                                <p class="text-[12px] text-slate-500 mt-1">
                                    Ou cliquez pour sélectionner depuis votre appareil
                                </p>
                            </div>

                            <!-- Formats acceptés -->
                            <div class="flex flex-wrap items-center justify-center gap-1.5 pt-2">
                                <span
                                    class="px-2.5 py-1 rounded-full bg-slate-200/80 text-[10px] font-bold text-slate-700">Images
                                    (PNG, JPG, WEBP, SVG)</span>
                                <span
                                    class="px-2.5 py-1 rounded-full bg-slate-200/80 text-[10px] font-bold text-slate-700">PDF
                                    & Docs</span>
                                <span
                                    class="px-2.5 py-1 rounded-full bg-slate-200/80 text-[10px] font-bold text-slate-700">Vidéos
                                    & Audios</span>
                                <span
                                    class="px-2.5 py-1 rounded-full bg-slate-200/80 text-[10px] font-bold text-slate-700">Max
                                    50 Mo</span>
                            </div>
                        </div>

                        <!-- Barres de progression d'upload -->
                        <div x-show="uploading"
                            class="p-5 rounded-2xl bg-white border border-slate-200 shadow-md space-y-3 text-left">
                            <div class="flex items-center justify-between text-[12px] font-extrabold text-slate-800">
                                <span>Téléversement en cours...</span>
                                <span x-text="uploadProgress + '%'"></span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-primary transition-all duration-300"
                                    :style="'width: ' + uploadProgress + '%'"></div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════════
              FOOTER DE LA MODALE : VALIDATION DE LA SÉLECTION
            ══════════════════════════════════════════════════════════ -->
            <div class="px-6 py-4 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2 text-[12px]">
                    <span class="text-slate-500">Sélection :</span>
                    <span class="font-extrabold text-slate-900" x-text="selectedCount + ' élément(s)'">0
                        élément(s)</span>
                    <template x-if="selectedCount > 0">
                        <button @click="clearSelection()"
                            class="text-primary hover:underline text-[11px] font-bold ml-2">Désélectionner tout</button>
                    </template>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="close()"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-[12px] font-bold transition-all cursor-pointer">
                        Annuler
                    </button>
                    <button @click="confirmSelection()" :disabled="selectedCount === 0"
                        class="px-6 py-2.5 rounded-xl bg-primary hover:bg-brand-red-hover disabled:opacity-40 disabled:cursor-not-allowed text-white text-[13px] font-extrabold shadow-md transition-all flex items-center gap-2 cursor-pointer">
                        <i data-lucide="check-circle-2" class="w-4.5 h-4.5"></i>
                        <span
                            x-text="multiple ? 'Insérer la sélection (' + selectedCount + ')' : 'Choisir ce fichier'"></span>
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════
              SOUS-MODALE : OUTIL DE RECADRAGE & ROGNAGE (CROPPER.JS)
            ══════════════════════════════════════════════════════════ -->
            <div x-show="cropModalOpen" x-cloak
                class="absolute inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex flex-col overflow-hidden"
                style="display: none;">
                <!-- Crop Header -->
                <div
                    class="px-6 py-3.5 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="crop" class="w-5 h-5 text-amber-400"></i>
                        <div>
                            <h3 class="text-sm font-extrabold">Outil de Rognage & Recadrage Haute Définition</h3>
                            <span class="text-[11px] text-slate-400">Ajustez le cadre ou choisissez un format standard
                                pour Cypres Pay</span>
                        </div>
                    </div>
                    <button @click="closeCropModal()"
                        class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center">
                        <i data-lucide="x" class="w-4.5 h-4.5"></i>
                    </button>
                </div>

                <!-- Crop Canvas Zone -->
                <div class="flex-1 p-6 flex items-center justify-center overflow-hidden bg-slate-950/50 relative">
                    <div class="max-w-3xl max-h-[60vh] w-full flex items-center justify-center">
                        <img x-ref="cropImageElement" src="" alt="Aperçu recadrage"
                            class="max-h-[60vh] max-w-full block" />
                    </div>
                </div>

                <!-- Crop Controls Bar -->
                <div
                    class="px-6 py-4 bg-slate-900 border-t border-slate-800 flex flex-wrap items-center justify-between gap-4">
                    <!-- Ratios prédéfinis -->
                    <div
                        class="flex items-center gap-1.5 p-1 bg-slate-800 rounded-xl text-[11px] font-bold text-slate-300">
                        <button @click="setCropRatio(1)"
                            :class="cropRatio === 1 ? 'bg-primary text-white' : 'hover:bg-slate-700'"
                            class="px-3 py-1.5 rounded-lg transition-all"
                            title="Format carré idéal pour les fiches produits">
                            1:1 (Carré Produit)
                        </button>
                        <button @click="setCropRatio(4/3)"
                            :class="cropRatio === 4/3 ? 'bg-primary text-white' : 'hover:bg-slate-700'"
                            class="px-3 py-1.5 rounded-lg transition-all">
                            4:3 (Catalogue)
                        </button>
                        <button @click="setCropRatio(16/9)"
                            :class="cropRatio === 16/9 ? 'bg-primary text-white' : 'hover:bg-slate-700'"
                            class="px-3 py-1.5 rounded-lg transition-all">
                            16:9 (Bannière / Kit)
                        </button>
                        <button @click="setCropRatio(NaN)"
                            :class="isNaN(cropRatio) ? 'bg-primary text-white' : 'hover:bg-slate-700'"
                            class="px-3 py-1.5 rounded-lg transition-all">
                            Libre
                        </button>
                    </div>

                    <!-- Contrôles Zoom / Rotation -->
                    <div class="flex items-center gap-2">
                        <button @click="rotateCropper(-90)"
                            class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white"
                            title="Rotation 90° gauche">
                            <i data-lucide="rotate-ccw" class="w-4.5 h-4.5"></i>
                        </button>
                        <button @click="rotateCropper(90)"
                            class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white"
                            title="Rotation 90° droite">
                            <i data-lucide="rotate-cw" class="w-4.5 h-4.5"></i>
                        </button>
                        <button @click="zoomCropper(0.1)"
                            class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white" title="Zoom avant">
                            <i data-lucide="zoom-in" class="w-4.5 h-4.5"></i>
                        </button>
                        <button @click="zoomCropper(-0.1)"
                            class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white" title="Zoom arrière">
                            <i data-lucide="zoom-out" class="w-4.5 h-4.5"></i>
                        </button>
                        <button @click="resetCropper()"
                            class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white" title="Réinitialiser">
                            <i data-lucide="rotate-ccw" class="w-4.5 h-4.5"></i>
                        </button>
                    </div>

                    <!-- Actions de sauvegarde -->
                    <div class="flex items-center gap-3">
                        <button @click="closeCropModal()"
                            class="px-4 py-2 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-[12px] font-bold">
                            Annuler
                        </button>
                        <button @click="saveCroppedImage(true)" :disabled="savingCrop"
                            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-[12px] font-extrabold flex items-center gap-1.5 shadow-md">
                            <i data-lucide="copy" class="w-4 h-4"></i>
                            <span>Enregistrer une copie</span>
                        </button>
                        <button @click="saveCroppedImage(false)" :disabled="savingCrop"
                            class="px-5 py-2 rounded-xl bg-primary hover:bg-brand-red-hover text-white text-[12px] font-extrabold flex items-center gap-1.5 shadow-md">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span x-text="savingCrop ? 'Traitement...' : 'Remplacer & Valider'"></span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Notification Toast Globale pour le Media Picker --}}
<div id="cypress-media-toast"
    class="fixed bottom-6 right-6 z-[1000] px-4 py-3 rounded-2xl bg-slate-900 text-white text-[13px] font-bold shadow-2xl border border-slate-700 flex items-center gap-2.5 transition-all duration-300 opacity-0 pointer-events-none translate-y-4">
    <i data-lucide="check-circle-2" class="text-emerald-400 w-5 h-5" id="cypress-toast-icon"></i>
    <span id="cypress-toast-message">Action effectuée</span>
</div>

<script>
    /**
     * Gestionnaire d'état Alpine.js pour la Modale Media Picker Cypress Pay
     */
    function cypressMediaPicker() {
        return {
            isOpen: false,
            activeTab: 'library', // 'library' | 'upload'
            modalTitle: 'Médiathèque Cypress Pay',
            typeFilter: 'all', // 'all', 'image', 'document', 'video', etc.
            lockedType: false, // true si le parent a imposé un type spécifique
            multiple: false,
            onSelectCallback: null,

            medias: [],
            selectedMedias: [],
            selectedMedia: null, // Média actuellement inspecté dans le volet latéral
            currentPage: 1,
            hasMorePages: false,
            totalItems: 0,
            loading: false,
            loadingMore: false,

            searchQuery: '',
            sortBy: 'latest',
            dragOver: false,
            dragOverTab: false,
            uploading: false,
            uploadProgress: 0,

            // Cropper
            cropModalOpen: false,
            cropperInstance: null,
            mediaToCrop: null,
            cropRatio: 1, // Carré par défaut pour e-commerce
            savingCrop: false,

            init() {
                // Enregistrement de l'API globale pour ouverture programmatique
                window.CypressMediaPicker = {
                    open: (options = {}) => {
                        this.openWith(options);
                    },
                    close: () => {
                        this.close();
                    }
                };
            },

            openWith(options = {}) {
                this.modalTitle = options.title || 'Médiathèque Cypress Pay';
                this.multiple = Boolean(options.multiple);
                this.onSelectCallback = typeof options.onSelect === 'function' ? options.onSelect : null;

                if (options.type && options.type !== 'all') {
                    this.typeFilter = options.type;
                    this.lockedType = true;
                } else {
                    this.typeFilter = 'all';
                    this.lockedType = false;
                }

                this.selectedMedias = [];
                this.selectedMedia = null;
                this.activeTab = 'library';
                this.isOpen = true;
                this.fetchMedias();
            },

            close() {
                this.isOpen = false;
                this.closeCropModal();
            },

            getAcceptAttribute() {
                if (this.typeFilter === 'image') return 'image/*';
                if (this.typeFilter === 'document') return '.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt';
                if (this.typeFilter === 'video') return 'video/*';
                if (this.typeFilter === 'audio') return 'audio/*';
                return '*/*';
            },

            async fetchMedias(page = 1) {
                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        format: 'json',
                        page: page,
                        type: this.typeFilter,
                        q: this.searchQuery,
                        sort: this.sortBy,
                        per_page: 24
                    });

                    const res = await fetch(`{{ route('admin.medias.index') }}?${params.toString()}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    if (data.success) {
                        this.medias = data.data;
                        this.currentPage = data.pagination.current_page;
                        this.hasMorePages = data.pagination.has_more;
                        this.totalItems = data.pagination.total;
                        this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                    }
                } catch (err) {
                    console.error('Erreur chargement médias:', err);
                    this.showToast('Erreur lors du chargement des fichiers', 'error');
                } finally {
                    this.loading = false;
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                }
            },

            async loadMore() {
                if (!this.hasMorePages || this.loadingMore) return;
                this.loadingMore = true;
                try {
                    const nextPage = this.currentPage + 1;
                    const params = new URLSearchParams({
                        format: 'json',
                        page: nextPage,
                        type: this.typeFilter,
                        q: this.searchQuery,
                        sort: this.sortBy,
                        per_page: 24
                    });

                    const res = await fetch(`{{ route('admin.medias.index') }}?${params.toString()}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    if (data.success) {
                        this.medias = [...this.medias, ...data.data];
                        this.currentPage = data.pagination.current_page;
                        this.hasMorePages = data.pagination.has_more;
                    }
                } catch (err) {
                    console.error(err);
                } finally {
                    this.loadingMore = false;
                }
            },

            toggleSelect(item) {
                this.selectedMedia = item;

                if (!this.multiple) {
                    this.selectedMedias = [item];
                    return;
                }

                const index = this.selectedMedias.findIndex(m => m.id === item.id);
                if (index > -1) {
                    this.selectedMedias.splice(index, 1);
                } else {
                    this.selectedMedias.push(item);
                }
            },

            isSelected(item) {
                return this.selectedMedias.some(m => m.id === item.id);
            },

            get selectedCount() {
                return this.selectedMedias.length;
            },

            clearSelection() {
                this.selectedMedias = [];
                this.selectedMedia = null;
            },

            confirmSelection() {
                if (this.selectedMedias.length === 0) return;

                if (this.onSelectCallback) {
                    if (this.multiple) {
                        this.onSelectCallback(this.selectedMedias);
                    } else {
                        this.onSelectCallback(this.selectedMedias[0]);
                    }
                }

                this.close();
                this.showToast('Média sélectionné avec succès !');
            },

            // Upload de fichiers
            handleQuickDrop(e) {
                this.dragOver = false;
                if (e.dataTransfer && e.dataTransfer.files) {
                    this.uploadFiles(e.dataTransfer.files);
                }
            },

            handleTabDrop(e) {
                this.dragOverTab = false;
                if (e.dataTransfer && e.dataTransfer.files) {
                    this.uploadFiles(e.dataTransfer.files);
                }
            },

            async uploadFiles(fileList) {
                if (!fileList || fileList.length === 0) return;

                // Vérification de type si verrouillé
                if (this.typeFilter === 'image') {
                    for (let f of fileList) {
                        if (!f.type.startsWith('image/')) {
                            this.showToast('Seules les images sont autorisées.', 'error');
                            return;
                        }
                    }
                }

                const formData = new FormData();
                for (let i = 0; i < fileList.length; i++) {
                    formData.append('files[]', fileList[i]);
                }
                formData.append('_token', '{{ csrf_token() }}');

                this.uploading = true;
                this.uploadProgress = 20;

                try {
                    const interval = setInterval(() => {
                        if (this.uploadProgress < 90) this.uploadProgress += 15;
                    }, 150);

                    const res = await fetch(`{{ route('admin.medias.upload') }}`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: formData
                    });

                    clearInterval(interval);
                    this.uploadProgress = 100;

                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message || 'Téléversement réussi !');
                        await this.fetchMedias(1);
                        this.activeTab = 'library';

                        // Sélectionner automatiquement le premier fichier téléversé
                        if (data.data && data.data.length > 0) {
                            this.toggleSelect(data.data[0]);
                        }
                    } else {
                        this.showToast(data.message || 'Erreur lors du téléversement.', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    this.showToast('Erreur serveur lors de l\'upload.', 'error');
                } finally {
                    setTimeout(() => {
                        this.uploading = false;
                        this.uploadProgress = 0;
                    }, 500);
                }
            },

            // Recadrage avec Cropper.js
            openCropModal(media) {
                if (!media || media.type !== 'image') return;
                this.mediaToCrop = media;
                this.cropModalOpen = true;

                this.$nextTick(() => {
                    const imageEl = this.$refs.cropImageElement;
                    imageEl.src = media.url;

                    if (this.cropperInstance) {
                        this.cropperInstance.destroy();
                    }

                    this.cropperInstance = new Cropper(imageEl, {
                        aspectRatio: this.cropRatio,
                        viewMode: 2,
                        responsive: true,
                        restore: false,
                        autoCropArea: 0.9,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                });
            },

            closeCropModal() {
                if (this.cropperInstance) {
                    this.cropperInstance.destroy();
                    this.cropperInstance = null;
                }
                this.cropModalOpen = false;
                this.mediaToCrop = null;
            },

            setCropRatio(ratio) {
                this.cropRatio = ratio;
                if (this.cropperInstance) {
                    this.cropperInstance.setAspectRatio(ratio);
                }
            },

            rotateCropper(degrees) {
                if (this.cropperInstance) this.cropperInstance.rotate(degrees);
            },

            zoomCropper(ratio) {
                if (this.cropperInstance) this.cropperInstance.zoom(ratio);
            },

            resetCropper() {
                if (this.cropperInstance) this.cropperInstance.reset();
            },

            async saveCroppedImage(saveAsNew = false) {
                if (!this.cropperInstance || !this.mediaToCrop || this.savingCrop) return;

                this.savingCrop = true;
                try {
                    // Obtenir le canvas haute qualité
                    const canvas = this.cropperInstance.getCroppedCanvas({
                        maxWidth: 2048,
                        maxHeight: 2048,
                        fillColor: '#ffffff',
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high',
                    });

                    const base64Data = canvas.toDataURL('image/jpeg', 0.92);

                    const res = await fetch(`/admin/medias/${this.mediaToCrop.id}/crop`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            cropped_image: base64Data,
                            save_as_new: saveAsNew
                        })
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message || 'Image recadrée avec succès !');
                        this.closeCropModal();
                        await this.fetchMedias(1);

                        // Sélectionner la nouvelle image recadrée
                        if (data.media) {
                            this.selectedMedia = data.media;
                            this.selectedMedias = [data.media];
                        }
                    } else {
                        this.showToast(data.message || 'Erreur lors du rognage.', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    this.showToast('Erreur serveur lors de la sauvegarde du rognage.', 'error');
                } finally {
                    this.savingCrop = false;
                }
            },

            // Métadonnées & Suppression
            async updateMediaMetadata(media) {
                try {
                    await fetch(`/admin/medias/${media.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            name: media.name,
                            alt: media.alt,
                            caption: media.caption
                        })
                    });
                    this.showToast('Informations mises à jour.');
                } catch (err) {
                    console.error(err);
                }
            },

            async deleteMedia(media) {
                if (!confirm(`Confirmez-vous la suppression définitive du média "${media.name}" ?`)) return;

                try {
                    const res = await fetch(`/admin/medias/${media.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message || 'Média supprimé.');
                        this.medias = this.medias.filter(m => m.id !== media.id);
                        this.selectedMedias = this.selectedMedias.filter(m => m.id !== media.id);
                        this.selectedMedia = null;
                    }
                } catch (err) {
                    console.error(err);
                    this.showToast('Erreur lors de la suppression.', 'error');
                }
            },

            copyUrl(url) {
                navigator.clipboard.writeText(url).then(() => {
                    this.showToast('URL copiée dans le presse-papier !');
                });
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const d = new Date(dateString);
                return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
            },

            showToast(message, type = 'success') {
                const toast = document.getElementById('cypress-media-toast');
                const toastMsg = document.getElementById('cypress-toast-message');
                const toastIcon = document.getElementById('cypress-toast-icon');

                if (!toast) return;

                toastMsg.textContent = message;
                if (type === 'error') {
                    toastIcon.setAttribute('data-lucide', 'alert-circle');
                    toastIcon.className = 'text-red-400 w-5 h-5';
                } else {
                    toastIcon.setAttribute('data-lucide', 'check-circle-2');
                    toastIcon.className = 'text-emerald-400 w-5 h-5';
                }
                if (window.lucide) {
                    window.lucide.createIcons();
                }

                toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                toast.classList.add('opacity-100', 'translate-y-0');

                setTimeout(() => {
                    toast.classList.remove('opacity-100', 'translate-y-0');
                    toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                }, 3000);
            }
        };
    }
</script>