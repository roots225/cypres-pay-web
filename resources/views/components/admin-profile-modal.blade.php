<!-- ══════════════════════════════════════════════════════════════════
     MODALE DE GESTION DU PROFIL ADMINISTRATEUR
══════════════════════════════════════════════════════════════════ -->
<div 
    x-data="{ 
        profileOpen: false,
        activeTab: 'info',
        changePassword: false 
    }"
    @open-admin-profile.window="profileOpen = true"
    @keydown.escape.window="if (profileOpen) profileOpen = false"
    class="relative z-[90]"
    style="display: none;"
    x-show="profileOpen"
>
    <!-- Overlay d'arrière-plan -->
    <div 
        x-show="profileOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        @click="profileOpen = false"
    ></div>

    <!-- Conteneur centré de la modale -->
    <div class="fixed inset-0 z-[91] overflow-y-auto flex items-center justify-center p-4 sm:p-6 text-center">
        <div 
            x-show="profileOpen"
            x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-250"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-3"
            class="relative w-full max-w-lg transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl border border-slate-100 transition-all"
            @click.stop
        >
            <!-- En-tête de la modale -->
            <div class="bg-gradient-to-r from-cobalt-deep to-slate-900 px-6 py-6 text-white flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center font-black text-lg border border-white/20 shadow-inner">
                        {{ substr(auth()->user()->prenom ?? 'A', 0, 1) }}{{ substr(auth()->user()->nom ?? 'D', 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-black tracking-tight leading-tight">Mon Profil Administrateur</h2>
                        <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-400 font-semibold mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ auth()->user()->email }} · Super Admin
                        </span>
                    </div>
                </div>

                <button 
                    type="button" 
                    @click="profileOpen = false" 
                    class="text-white/70 hover:text-white transition-colors p-2 rounded-xl hover:bg-white/10 focus:outline-none"
                >
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Formulaire de mise à jour -->
            <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 sm:p-7 space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Prénom *</label>
                        <div class="relative">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                            <input 
                                type="text" 
                                name="prenom" 
                                value="{{ old('prenom', auth()->user()->prenom) }}" 
                                required 
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-[13px] font-medium transition-all"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Nom de famille *</label>
                        <div class="relative">
                            <i data-lucide="user-check" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                            <input 
                                type="text" 
                                name="nom" 
                                value="{{ old('nom', auth()->user()->nom) }}" 
                                required 
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-[13px] font-medium transition-all"
                            />
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Adresse Email *</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email', auth()->user()->email) }}" 
                            required 
                            class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-[13px] font-medium transition-all"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Numéro de Téléphone *</label>
                    <div class="relative">
                        <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                        <input 
                            type="tel" 
                            name="telephone" 
                            value="{{ old('telephone', auth()->user()->telephone) }}" 
                            required 
                            class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none text-[13px] font-medium transition-all"
                        />
                    </div>
                </div>

                <!-- Section Modification du Mot de Passe -->
                <div class="pt-3 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="changePassword = !changePassword" 
                        class="flex items-center justify-between w-full text-left py-1 text-[13px] font-bold text-slate-700 hover:text-primary transition-colors focus:outline-none"
                    >
                        <span class="flex items-center gap-2">
                            <i data-lucide="key-round" class="w-4 h-4 text-primary"></i>
                            <span>Modifier le mot de passe de connexion</span>
                        </span>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="changePassword ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="changePassword" x-collapse class="mt-4 space-y-3.5 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div>
                            <label class="block text-[12px] font-bold text-slate-700 mb-1">Mot de passe actuel</label>
                            <input 
                                type="password" 
                                name="current_password" 
                                placeholder="Requis uniquement en cas de changement"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white focus:border-primary outline-none text-[13px]"
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Nouveau mot de passe</label>
                                <input 
                                    type="password" 
                                    name="new_password" 
                                    placeholder="Min. 6 caractères"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white focus:border-primary outline-none text-[13px]"
                                />
                            </div>

                            <div>
                                <label class="block text-[12px] font-bold text-slate-700 mb-1">Confirmation</label>
                                <input 
                                    type="password" 
                                    name="new_password_confirmation" 
                                    placeholder="Confirmer"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white focus:border-primary outline-none text-[13px]"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'Action -->
                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="profileOpen = false" 
                        class="px-4 py-2.5 rounded-xl text-[13px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer"
                    >
                        Annuler
                    </button>

                    <button 
                        type="submit" 
                        class="px-5 py-2.5 rounded-xl text-[13px] font-extrabold text-white bg-primary hover:bg-brand-red-hover shadow-md shadow-primary/20 transition-all flex items-center gap-2 cursor-pointer"
                    >
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Enregistrer les modifications</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
