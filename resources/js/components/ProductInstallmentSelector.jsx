import React, { useState } from 'react';
import { Calendar, Banknote, ShoppingCart } from 'lucide-react';

export default function ProductInstallmentSelector({
    productId = 1,
    basePrice = 120000,
    cashPriceInitial,
    productName = "Produit",
    imageUrl = ""
}) {
    const [mode, setMode] = useState('credit'); // 'credit' ou 'cash'
    const [selectedDuration, setSelectedDuration] = useState(12);

    const cashPrice = cashPriceInitial || Math.round(basePrice * 0.93); // -7% CASH
    const savings = basePrice - cashPrice;

    const durations = [
        { months: 1, days: 30, label: "30 jours", freq: "jour", divisor: 30 },
        { months: 3, days: 90, label: "3 mois", freq: "mois", divisor: 3 },
        { months: 6, days: 180, label: "6 mois", freq: "mois", divisor: 6 },
        { months: 12, days: 360, label: "12 mois", freq: "mois", divisor: 12 },
        { months: 24, days: 720, label: "24 mois", freq: "mois", divisor: 24 }
    ];

    const currentPlan = durations.find(d => d.months === selectedDuration) || durations[3];
    const installmentAmount = Math.round(basePrice / currentPlan.divisor);

    return (
        <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            {/* Bascule Mode Cash vs Crédit */}
            <div className="flex bg-slate-100 p-1 rounded-xl">
                <button
                    type="button"
                    onClick={() => setMode('credit')}
                    className={`flex-1 py-2.5 rounded-lg text-[13px] font-extrabold flex items-center justify-center gap-1.5 transition-all ${
                        mode === 'credit'
                            ? 'bg-primary-container text-white shadow-sm'
                            : 'text-slate-600 hover:text-slate-900'
                    }`}
                >
                    <Calendar className="w-4 h-4" />
                    Paiement Échelonné (Crédit)
                </button>
                <button
                    type="button"
                    onClick={() => setMode('cash')}
                    className={`flex-1 py-2.5 rounded-lg text-[13px] font-extrabold flex items-center justify-center gap-1.5 transition-all ${
                        mode === 'cash'
                            ? 'bg-emerald-600 text-white shadow-sm'
                            : 'text-slate-600 hover:text-slate-900'
                    }`}
                >
                    <Banknote className="w-4 h-4" />
                    Paiement Cash (-7%)
                </button>
            </div>

            {mode === 'credit' ? (
                <div className="space-y-3.5">
                    <div>
                        <span className="text-[12px] font-bold text-slate-500 block mb-2">Choisir la durée de vos mensualités :</span>
                        <div className="grid grid-cols-5 gap-1.5">
                            {durations.map((d) => (
                                <button
                                    key={d.months}
                                    type="button"
                                    onClick={() => setSelectedDuration(d.months)}
                                    className={`py-2 px-1 rounded-lg text-center font-extrabold text-[12px] border transition-all ${
                                        selectedDuration === d.months
                                            ? 'bg-brand-red-subtle border-primary-container text-primary-container ring-1 ring-primary-container'
                                            : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'
                                    }`}
                                >
                                    {d.label}
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className="bg-brand-red-subtle/70 p-3.5 rounded-xl border border-primary-container/20 flex items-center justify-between">
                        <div>
                            <span className="text-[11px] font-bold text-slate-500 block">Votre versement :</span>
                            <div className="flex items-baseline gap-1 mt-0.5">
                                <span className="text-[26px] font-black text-primary-container leading-none">
                                    {installmentAmount.toLocaleString('fr-FR')}
                                </span>
                                <span className="text-[13px] font-bold text-slate-700">
                                    FCFA /{currentPlan.freq}
                                </span>
                            </div>
                        </div>
                        <div className="text-right">
                            <span className="text-[11px] text-emerald-600 font-bold block">0 FCFA d'avance</span>
                            <span className="text-[11px] text-slate-400">Total : {basePrice.toLocaleString('fr-FR')} FCFA</span>
                        </div>
                    </div>

                    <button
                        type="button"
                        onClick={() => window.addToCart({
                            id: productId,
                            nom: productName,
                            prix: basePrice,
                            monthly: installmentAmount,
                            img: imageUrl,
                            image: imageUrl,
                            qty: 1
                        })}
                        className="w-full bg-primary-container hover:bg-brand-red-hover text-white py-3.5 rounded-xl font-extrabold text-[14px] flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all"
                    >
                        <ShoppingCart className="w-5 h-5" />
                        Commander en {currentPlan.label} ({installmentAmount.toLocaleString('fr-FR')} FCFA/{currentPlan.freq})
                    </button>
                </div>
            ) : (
                <div className="space-y-3.5">
                    <div className="bg-emerald-50 p-3.5 rounded-xl border border-emerald-200 flex items-center justify-between">
                        <div>
                            <span className="text-[11px] font-bold text-emerald-700 block">Prix spécial au comptant (-7%) :</span>
                            <div className="flex items-baseline gap-1.5 mt-0.5">
                                <span className="text-[26px] font-black text-emerald-700 leading-none">
                                    {cashPrice.toLocaleString('fr-FR')}
                                </span>
                                <span className="text-[13px] font-bold text-slate-700">FCFA</span>
                            </div>
                        </div>
                        <div className="text-right">
                            <span className="bg-emerald-600 text-white text-[11px] font-extrabold px-2 py-0.5 rounded">
                                Économie : {savings.toLocaleString('fr-FR')} FCFA
                            </span>
                            <span className="text-[11px] text-slate-400 line-through block mt-1">
                                {basePrice.toLocaleString('fr-FR')} FCFA
                            </span>
                        </div>
                    </div>

                    <button
                        type="button"
                        onClick={() => window.addToCart({
                            id: productId,
                            nom: productName,
                            prix: cashPrice,
                            monthly: 0,
                            img: imageUrl,
                            image: imageUrl,
                            qty: 1
                        })}
                        className="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl font-extrabold text-[14px] flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all"
                    >
                        <Banknote className="w-5 h-5" />
                        Acheter cash à {cashPrice.toLocaleString('fr-FR')} FCFA (-7%)
                    </button>
                </div>
            )}
        </div>
    );
}
