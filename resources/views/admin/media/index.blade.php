@extends('layouts.admin')

@section('page-title', 'Médiathèque & Gestionnaire de Fichiers')

@section('content')
<div 
    x-data="adminMediaManager()" 
    x-init="init()"
    class="space-y-6"
>
    <!-- ══════════════════════════════════════════════════════════════
      1. KPIs & STATISTIQUES DE STOCKAGE
    ══════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total Médias -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase">Fichiers</span>
                <i data-lucide="folder" class="w-4.5 h-4.5 text-slate-400"></i>
            </div>
            <div class="text-xl font-black text-slate-900">{{ $stats['total_count'] ?? 0 }}</div>
            <span class="text-[10px] text-slate-400 mt-0.5 block">{{ $stats['total_size_human'] ?? '0 o' }} occupés</span>
        </div>

        <!-- Images -->
        <a 
            href="{{ route('admin.medias.index', ['type' => 'image']) }}"
            class="bg-white p-4 rounded-2xl border transition-all cursor-pointer {{ $type === 'image' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 hover:border-emerald-300' }}"
        >
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-emerald-700 uppercase">Images</span>
                <i data-lucide="image" class="w-4.5 h-4.5 text-emerald-500"></i>
            </div>
            <div class="text-xl font-black text-slate-900">{{ $stats['types']['image']['count'] ?? 0 }}</div>
            <span class="text-[10px] text-emerald-600 font-bold mt-0.5 block">{{ $stats['types']['image']['size_human'] ?? '0 o' }}</span>
        </a>

        <!-- Documents / PDF -->
        <a 
            href="{{ route('admin.medias.index', ['type' => 'document']) }}"
            class="bg-white p-4 rounded-2xl border transition-all cursor-pointer {{ $type === 'document' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-slate-200/80 hover:border-blue-300' }}"
        >
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-blue-700 uppercase">Documents</span>
                <i data-lucide="file-text" class="w-4.5 h-4.5 text-blue-500"></i>
            </div>
            <div class="text-xl font-black text-slate-900">{{ $stats['types']['document']['count'] ?? 0 }}</div>
            <span class="text-[10px] text-blue-600 font-bold mt-0.5 block">{{ $stats['types']['document']['size_human'] ?? '0 o' }}</span>
        </a>

        <!-- Vidéos -->
        <a 
            href="{{ route('admin.medias.index', ['type' => 'video']) }}"
            class="bg-white p-4 rounded-2xl border transition-all cursor-pointer {{ $type === 'video' ? 'border-purple-500 ring-2 ring-purple-500/20' : 'border-slate-200/80 hover:border-purple-300' }}"
        >
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-purple-700 uppercase">Vidéos</span>
                <i data-lucide="video" class="w-4.5 h-4.5 text-purple-500"></i>
            </div>
            <div class="text-xl font-black text-slate-900">{{ $stats['types']['video']['count'] ?? 0 }}</div>
            <span class="text-[10px] text-purple-600 font-bold mt-0.5 block">{{ $stats['types']['video']['size_human'] ?? '0 o' }}</span>
        </a>

        <!-- Audios -->
        <a 
            href="{{ route('admin.medias.index', ['type' => 'audio']) }}"
            class="bg-white p-4 rounded-2xl border transition-all cursor-pointer {{ $type === 'audio' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/80 hover:border-amber-300' }}"
        >
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-amber-700 uppercase">Audios</span>
                <i data-lucide="music" class="w-4.5 h-4.5 text-amber-500"></i>
            </div>
            <div class="text-xl font-black text-slate-900">{{ $stats['types']['audio']['count'] ?? 0 }}</div>
            <span class="text-[10px] text-amber-600 font-bold mt-0.5 block">{{ $stats['types']['audio']['size_human'] ?? '0 o' }}</span>
        </a>

        <!-- Archives & Autres -->
        <a 
            href="{{ route('admin.medias.index', ['type' => 'archive']) }}"
            class="bg-white p-4 rounded-2xl border transition-all cursor-pointer {{ $type === 'archive' ? 'border-orange-500 ring-2 ring-orange-500/20' : 'border-slate-200/80 hover:border-orange-300' }}"
        >
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-orange-700 uppercase">Archives</span>
                <i data-lucide="archive" class="w-4.5 h-4.5 text-orange-500"></i>
            </div>
            <div class="text-xl font-black text-slate-900">{{ $stats['types']['archive']['count'] ?? 0 }}</div>
            <span class="text-[10px] text-orange-600 font-bold mt-0.5 block">{{ $stats['types']['archive']['size_human'] ?? '0 o' }}</span>
        </a>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
      2. BARRE D'OUTILS PRINCIPALE & FILTRES
    ══════════════════════════════════════════════════════════════ -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            
            <!-- Filtres par Type de Média (Boutons Pilles) -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none text-[12px] font-bold">
                <a 
                    href="{{ route('admin.medias.index') }}" 
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 {{ empty($type) || $type === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Tous les fichiers
                </a>
                <a 
                    href="{{ route('admin.medias.index', ['type' => 'image', 'q' => $search]) }}" 
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $type === 'image' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <i data-lucide="image" class="w-4 h-4"></i>
                    <span>Images</span>
                </a>
                <a 
                    href="{{ route('admin.medias.index', ['type' => 'document', 'q' => $search]) }}" 
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $type === 'document' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Documents (PDF...)</span>
                </a>
                <a 
                    href="{{ route('admin.medias.index', ['type' => 'video', 'q' => $search]) }}" 
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $type === 'video' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <i data-lucide="video" class="w-4 h-4"></i>
                    <span>Vidéos</span>
                </a>
                <a 
                    href="{{ route('admin.medias.index', ['type' => 'audio', 'q' => $search]) }}" 
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $type === 'audio' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <i data-lucide="music" class="w-4 h-4"></i>
                    <span>Audios</span>
                </a>
                <a 
                    href="{{ route('admin.medias.index', ['type' => 'archive', 'q' => $search]) }}" 
                    class="px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5 {{ $type === 'archive' ? 'bg-orange-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <i data-lucide="archive" class="w-4 h-4"></i>
                    <span>Archives</span>
                </a>
            </div>

            <!-- Boutons d'Action Principaux -->
            <div class="flex items-center gap-2.5 shrink-0">
                <!-- Suppression groupée -->
                <button 
                    x-show="selectedIds.length > 0" 
                    @click="batchDelete()"
                    class="px-3.5 py-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-[12px] font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-xs"
                >
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Supprimer (<span x-text="selectedIds.length"></span>)</span>
                </button>

                <!-- Bouton Téléverser -->
                <button 
                    @click="$refs.mainFileInput.click()"
                    class="px-4 py-2 rounded-xl bg-primary hover:bg-brand-red-hover text-white text-[12px] font-extrabold shadow-md transition-all flex items-center gap-2 cursor-pointer"
                >
                    <i data-lucide="image-plus" class="w-4.5 h-4.5"></i>
                    <span>Téléverser des fichiers</span>
                </button>
                <input 
                    type="file" 
                    x-ref="mainFileInput" 
                    class="hidden" 
                    multiple 
                    @change="uploadFiles($event.target.files)"
                />
            </div>
        </div>

        <!-- Recherche et Tri -->
        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form action="{{ route('admin.medias.index') }}" method="GET" class="relative w-full sm:max-w-md">
                @if($type) <input type="hidden" name="type" value="{{ $type }}"> @endif
                @if($folder) <input type="hidden" name="folder" value="{{ $folder }}"> @endif
                <i data-lucide="search" class="w-4.5 h-4.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search }}" 
                    placeholder="Rechercher par nom, texte alt ou description..." 
                    class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 text-[12px] font-medium outline-none focus:border-primary focus:bg-white transition-all"
                />
            </form>

            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end text-[12px]">
                <form action="{{ route('admin.medias.index') }}" method="GET" class="flex items-center gap-2">
                    @if($type) <input type="hidden" name="type" value="{{ $type }}"> @endif
                    @if($search) <input type="hidden" name="q" value="{{ $search }}"> @endif
                    <label class="text-slate-400 font-bold text-[11px] hidden sm:inline">Trier par :</label>
                    <select 
                        name="sort" 
                        onchange="this.form.submit()" 
                        class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-[12px] font-bold text-slate-700 outline-none focus:border-primary cursor-pointer"
                    >
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Ajout le plus récent</option>
                        <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Anciens d'abord</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Nom (A → Z)</option>
                        <option value="size_desc" {{ $sort === 'size_desc' ? 'selected' : '' }}>Taille (Plus volumineux)</option>
                        <option value="size_asc" {{ $sort === 'size_asc' ? 'selected' : '' }}>Taille (Plus léger)</option>
                    </select>
                </form>

                <!-- Toggle Vue Grille / Vue Liste -->
                <div class="flex items-center p-1 bg-slate-100 rounded-xl">
                    <button 
                        @click="viewMode = 'grid'" 
                        :class="viewMode === 'grid' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-400 hover:text-slate-700'"
                        class="p-1 rounded-lg transition-all flex items-center justify-center"
                        title="Vue grille"
                    >
                        <i data-lucide="layout-grid" class="w-4.5 h-4.5"></i>
                    </button>
                    <button 
                        @click="viewMode = 'list'" 
                        :class="viewMode === 'list' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-400 hover:text-slate-700'"
                        class="p-1 rounded-lg transition-all flex items-center justify-center"
                        title="Vue liste détaillée"
                    >
                        <i data-lucide="list" class="w-4.5 h-4.5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
      3. ZONE DE DÉPÔT DRAG & DROP
    ══════════════════════════════════════════════════════════════ -->
    <div 
        @dragover.prevent="dragOver = true"
        @dragleave.prevent="dragOver = false"
        @drop.prevent="handleDrop($event)"
        :class="dragOver ? 'border-primary bg-primary/5 scale-[1.005]' : 'border-dashed border-slate-300 hover:border-slate-400 bg-white'"
        class="border-2 rounded-3xl p-6 transition-all text-center flex flex-col items-center justify-center space-y-2 cursor-pointer shadow-xs group"
        @click="$refs.mainFileInput.click()"
    >
        <div class="w-12 h-12 rounded-2xl bg-slate-100 group-hover:bg-primary/10 text-slate-500 group-hover:text-primary flex items-center justify-center transition-all">
            <i data-lucide="upload-cloud" class="w-6 h-6"></i>
        </div>
        <div>
            <span class="text-[13px] font-extrabold text-slate-800 block">
                Glissez-déposez n'importe quel fichier ici pour l'ajouter à Cypress Pay
            </span>
            <span class="text-[11px] text-slate-400 block mt-0.5">
                Supporte PNG, JPG, WEBP, SVG, PDF, DOC, MP4, MP3, ZIP... (Max 50 Mo par fichier)
            </span>
        </div>
    </div>

    <!-- Barre de progression d'upload -->
    <div x-show="uploading" x-cloak class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-[12px] font-bold text-slate-800">
            <span>Téléversement en cours...</span>
            <span x-text="uploadProgress + '%'"></span>
        </div>
        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
            <div class="h-full bg-primary transition-all duration-300" :style="'width: ' + uploadProgress + '%'"></div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
      4. AFFICHAGE DES MÉDIAS (GRILLE OU LISTE)
    ══════════════════════════════════════════════════════════════ -->
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- Colonne Principale : Liste ou Grille -->
        <div class="flex-1 w-full space-y-4">
            
            @if($medias->isEmpty())
                <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center space-y-4 shadow-xs">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <i data-lucide="folder-open" class="w-9 h-9"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Aucun média dans cette catégorie</h3>
                        <p class="text-[12px] text-slate-500 max-w-sm mx-auto mt-1">
                            Commencez par téléverser des images pour vos produits, des pièces justificatives ou vos fichiers marketing.
                        </p>
                    </div>
                    <button 
                        @click="$refs.mainFileInput.click()"
                        class="px-5 py-2.5 rounded-xl bg-primary text-white font-extrabold text-[12px] shadow-md hover:bg-brand-red-hover transition-all inline-flex items-center gap-2 cursor-pointer"
                    >
                        <i data-lucide="image-plus" class="w-4.5 h-4.5"></i>
                        <span>Ajouter un premier fichier</span>
                    </button>
                </div>
            @else
                
                <!-- VUE GRILLE -->
                <div x-show="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3.5">
                    @foreach($medias as $media)
                        <div 
                            class="group relative rounded-2xl border transition-all duration-200 bg-white overflow-hidden flex flex-col"
                            :class="{
                                'ring-2 ring-primary border-primary shadow-md': selectedDetail?.id === {{ $media->id }},
                                'border-slate-200/90 hover:border-slate-300 hover:shadow-md': selectedDetail?.id !== {{ $media->id }}
                            }"
                        >
                            <!-- Checkbox sélection groupée -->
                            <div class="absolute top-2 left-2 z-10">
                                <input 
                                    type="checkbox" 
                                    value="{{ $media->id }}" 
                                    x-model="selectedIds"
                                    class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-300 bg-white/90 shadow-sm cursor-pointer"
                                />
                            </div>

                            <!-- Zone Visuelle -->
                            <div 
                                @click="selectMediaDetail({{ json_encode($media) }})"
                                class="aspect-square w-full bg-slate-100 relative overflow-hidden flex items-center justify-center cursor-pointer"
                            >
                                @if($media->isImage())
                                    <img 
                                        src="{{ $media->url }}" 
                                        alt="{{ $media->alt ?? $media->name }}" 
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.src='https://placehold.co/400x400/f1f5f9/94a3b8?text=Image';"
                                    />
                                @else
                                    <div class="flex flex-col items-center justify-center p-4 text-center space-y-1">
                                        <div class="w-12 h-12 rounded-2xl {{ $media->badge_color['bg'] }} flex items-center justify-center shadow-xs">
                                            <i data-lucide="{{ $media->icon }}" class="w-6 h-6"></i>
                                        </div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">{{ $media->extension ?: $media->type }}</span>
                                    </div>
                                @endif

                                <!-- Badge type en bas à gauche -->
                                <div class="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-sm text-white text-[9px] font-bold uppercase tracking-wider">
                                    {{ $media->extension ?: $media->type }}
                                </div>

                                <!-- Actions rapides au survol -->
                                <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 p-2">
                                    <a 
                                        href="{{ $media->url }}" 
                                        target="_blank" 
                                        class="p-2 rounded-xl bg-white/90 hover:bg-white text-slate-800 transition-transform hover:scale-110 shadow-sm flex items-center justify-center"
                                        title="Voir en taille réelle"
                                        @click.stop
                                    >
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                    @if($media->isImage())
                                        <button 
                                            @click.stop="openCropForMedia({{ json_encode($media) }})"
                                            class="p-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 transition-transform hover:scale-110 shadow-sm flex items-center justify-center"
                                            title="Recadrer / Rogner cette image"
                                        >
                                            <i data-lucide="crop" class="w-4 h-4"></i>
                                        </button>
                                    @endif

                                    <button 
                                        @click.stop="copyLink('{{ $media->url }}')"
                                        class="p-2 rounded-xl bg-white/90 hover:bg-white text-slate-800 transition-transform hover:scale-110 shadow-sm flex items-center justify-center"
                                        title="Copier le lien public"
                                    >
                                        <i data-lucide="copy" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Métadonnées de carte -->
                            <div 
                                @click="selectMediaDetail({{ json_encode($media) }})"
                                class="p-3 bg-white border-t border-slate-100 flex-1 flex flex-col justify-between cursor-pointer"
                            >
                                <span class="text-[12px] font-extrabold text-slate-800 truncate block" title="{{ $media->name }}">
                                    {{ $media->name }}
                                </span>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                                    <span>{{ $media->size_human }}</span>
                                    <span>{{ $media->dimensions_formatted ?: $media->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- VUE LISTE -->
                <div x-show="viewMode === 'list'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[12px]">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                                    <th class="py-3 px-4 w-10">
                                        <input 
                                            type="checkbox" 
                                            @change="toggleSelectAll($event)"
                                            class="rounded text-primary focus:ring-primary border-slate-300"
                                        />
                                    </th>
                                    <th class="py-3 px-4">Fichier & Nom</th>
                                    <th class="py-3 px-4">Type</th>
                                    <th class="py-3 px-4">Taille</th>
                                    <th class="py-3 px-4">Dimensions</th>
                                    <th class="py-3 px-4">Date d'ajout</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($medias as $media)
                                    <tr 
                                        @click="selectMediaDetail({{ json_encode($media) }})"
                                        class="hover:bg-slate-50/80 transition-colors cursor-pointer"
                                        :class="selectedDetail?.id === {{ $media->id }} ? 'bg-primary/5 font-bold' : ''"
                                    >
                                        <td class="py-3 px-4" @click.stop>
                                            <input 
                                                type="checkbox" 
                                                value="{{ $media->id }}" 
                                                x-model="selectedIds"
                                                class="rounded text-primary focus:ring-primary border-slate-300"
                                            />
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 shrink-0 flex items-center justify-center">
                                                    @if($media->isImage())
                                                        <img src="{{ $media->url }}" class="w-full h-full object-cover" />
                                                    @else
                                                        <i data-lucide="{{ $media->icon }}" class="w-5 h-5 text-slate-500"></i>
                                                    @endif
                                                </div>
                                                <div class="truncate max-w-xs">
                                                    <span class="font-extrabold text-slate-900 block truncate">{{ $media->name }}</span>
                                                    <span class="text-[10px] text-slate-400 block truncate">{{ $media->file_name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 uppercase text-[10px] font-extrabold text-slate-600">
                                            <span class="px-2 py-0.5 rounded-md {{ $media->badge_color['bg'] }}">
                                                {{ $media->extension ?: $media->type }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-slate-600 font-semibold">{{ $media->size_human }}</td>
                                        <td class="py-3 px-4 text-slate-400">{{ $media->dimensions_formatted ?: '—' }}</td>
                                        <td class="py-3 px-4 text-slate-500">{{ $media->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="py-3 px-4 text-right" @click.stop>
                                            <div class="flex items-center justify-end gap-1">
                                                @if($media->isImage())
                                                    <button 
                                                        @click="openCropForMedia({{ json_encode($media) }})"
                                                        class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 flex items-center justify-center"
                                                        title="Recadrer"
                                                    >
                                                        <i data-lucide="crop" class="w-4.5 h-4.5"></i>
                                                    </button>
                                                @endif
                                                <button 
                                                    @click="copyLink('{{ $media->url }}')"
                                                    class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 flex items-center justify-center"
                                                    title="Copier le lien"
                                                >
                                                    <i data-lucide="copy" class="w-4.5 h-4.5"></i>
                                                </button>
                                                <button 
                                                    @click="deleteSingle({{ $media->id }}, '{{ addslashes($media->name) }}')"
                                                    class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 flex items-center justify-center"
                                                    title="Supprimer"
                                                >
                                                    <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Laravel -->
                <div class="pt-4">
                    {{ $medias->links() }}
                </div>
            @endif

        </div>

        <!-- Colonne Latérale : Volet Inspecteur Média Sélectionné -->
        <div 
            x-show="selectedDetail" 
            x-cloak
            class="w-full lg:w-88 bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs shrink-0 space-y-4"
        >
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Inspecteur de fichier</span>
                <button @click="selectedDetail = null" class="text-slate-400 hover:text-slate-600 flex items-center justify-center">
                    <i data-lucide="x" class="w-4.5 h-4.5"></i>
                </button>
            </div>

            <!-- Aperçu grand format -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden p-2 text-center">
                <template x-if="selectedDetail?.type === 'image'">
                    <img 
                        :src="selectedDetail?.url" 
                        :alt="selectedDetail?.name" 
                        class="w-full max-h-56 object-contain rounded-xl mx-auto"
                    />
                </template>
                <template x-if="selectedDetail?.type !== 'image'">
                    <div class="py-10 text-center space-y-2">
                        <div class="w-16 h-16 rounded-2xl bg-white shadow-xs text-slate-700 flex items-center justify-center mx-auto">
                            <i :data-lucide="selectedDetail?.icon || 'file'" class="w-9 h-9"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600 uppercase block" x-text="selectedDetail?.mime_type"></span>
                    </div>
                </template>
            </div>

            <!-- Bouton Rognage Rapide -->
            <template x-if="selectedDetail?.type === 'image'">
                <button 
                    @click="openCropForMedia(selectedDetail)"
                    class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-[12px] shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <i data-lucide="crop" class="w-4.5 h-4.5"></i>
                    <span>Recadrer cette image</span>
                </button>
            </template>

            <!-- Fiche technique -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 text-[11px]">
                <div class="flex justify-between">
                    <span class="text-slate-400">Nom fichier :</span>
                    <span class="font-bold text-slate-700 truncate max-w-[150px]" :title="selectedDetail?.file_name" x-text="selectedDetail?.file_name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Taille :</span>
                    <span class="font-bold text-slate-700" x-text="selectedDetail?.size_human"></span>
                </div>
                <template x-if="selectedDetail?.dimensions_formatted">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Dimensions :</span>
                        <span class="font-bold text-slate-700" x-text="selectedDetail?.dimensions_formatted"></span>
                    </div>
                </template>
                <div class="flex justify-between">
                    <span class="text-slate-400">Type MIME :</span>
                    <span class="font-bold text-slate-700" x-text="selectedDetail?.mime_type"></span>
                </div>
            </div>

            <!-- Formulaire de modification immédiate -->
            <div class="space-y-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Titre / Nom lisible</label>
                    <input 
                        type="text" 
                        x-model="selectedDetail.name" 
                        @change="saveDetail(selectedDetail)"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-[12px] font-medium outline-none focus:border-primary"
                    />
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Texte alternatif (Alt SEO)</label>
                    <input 
                        type="text" 
                        x-model="selectedDetail.alt" 
                        @change="saveDetail(selectedDetail)"
                        placeholder="Description pour accessibilité..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-[12px] font-medium outline-none focus:border-primary"
                    />
                </div>

                <!-- Boutons d'action -->
                <div class="space-y-2 pt-2">
                    <button 
                        @click="copyLink(selectedDetail?.url)"
                        class="w-full py-2 px-3 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-[12px] transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <i data-lucide="copy" class="w-4 h-4"></i>
                        <span>Copier l'URL publique</span>
                    </button>

                    <a 
                        :href="selectedDetail?.url" 
                        download
                        class="w-full py-2 px-3 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-[12px] transition-all flex items-center justify-center gap-1.5"
                    >
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Télécharger sur mon PC</span>
                    </a>

                    <button 
                        @click="deleteSingle(selectedDetail.id, selectedDetail.name)"
                        class="w-full py-2 px-3 rounded-xl text-red-600 hover:bg-red-50 border border-red-200 font-bold text-[12px] transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span>Supprimer définitivement</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
function adminMediaManager() {
    return {
        viewMode: 'grid', // 'grid' | 'list'
        dragOver: false,
        uploading: false,
        uploadProgress: 0,
        selectedIds: [],
        selectedDetail: null,

        init() {
            // initialisation
        },

        selectMediaDetail(media) {
            this.selectedDetail = media;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        handleDrop(e) {
            this.dragOver = false;
            if (e.dataTransfer && e.dataTransfer.files) {
                this.uploadFiles(e.dataTransfer.files);
            }
        },

        async uploadFiles(files) {
            if (!files || files.length === 0) return;

            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            formData.append('_token', '{{ csrf_token() }}');

            this.uploading = true;
            this.uploadProgress = 15;

            const interval = setInterval(() => {
                if (this.uploadProgress < 90) this.uploadProgress += 15;
            }, 120);

            try {
                const res = await fetch(`{{ route('admin.medias.upload') }}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                });
                clearInterval(interval);
                this.uploadProgress = 100;

                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Erreur lors du téléversement.');
                }
            } catch (err) {
                console.error(err);
                alert('Erreur réseau lors du téléversement.');
            } finally {
                this.uploading = false;
            }
        },

        openCropForMedia(media) {
            if (window.CypressMediaPicker) {
                window.CypressMediaPicker.open({
                    type: 'image',
                    title: 'Recadrer : ' + media.name,
                    onSelect: () => {
                        window.location.reload();
                    }
                });
                // Déclencher directement le modal de rognage
                setTimeout(() => {
                    const pickerComponent = document.querySelector('#cypress-media-picker-modal');
                    if (pickerComponent && pickerComponent._x_dataStack) {
                        pickerComponent._x_dataStack[0].openCropModal(media);
                    }
                }, 300);
            }
        },

        async saveDetail(media) {
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
            } catch (err) {
                console.error(err);
            }
        },

        deleteSingle(id, name) {
            window.confirmAction({
                title: 'Supprimer ce fichier',
                message: `Supprimer définitivement "${name}" de la médiathèque ? Cette action est irréversible.`,
                confirmText: 'Oui, supprimer',
                type: 'danger',
                icon: 'trash-2',
                onConfirm: async () => {
                    try {
                        const res = await fetch(`/admin/medias/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.location.reload();
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Erreur lors de la suppression.');
                    }
                }
            });
        },

        batchDelete() {
            window.confirmAction({
                title: 'Suppression groupée',
                message: `Supprimer définitivement ces ${this.selectedIds.length} fichiers de la médiathèque ? Cette action est irréversible.`,
                confirmText: 'Oui, tout supprimer',
                type: 'danger',
                icon: 'trash-2',
                onConfirm: async () => {
                    try {
                        const res = await fetch(`{{ route('admin.medias.batch_delete') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.location.reload();
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Erreur lors de la suppression groupée.');
                    }
                }
            });
        },

        toggleSelectAll(e) {
            if (e.target.checked) {
                this.selectedIds = @json($medias->pluck('id'));
            } else {
                this.selectedIds = [];
            }
        },

        copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                alert('Lien copié dans le presse-papier !');
            });
        }
    };
}
</script>
@endsection
