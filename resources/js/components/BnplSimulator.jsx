import React, { useState, useMemo } from 'react';
import { Calculator, Tag, Wallet, BadgeCheck, ShoppingBag } from 'lucide-react';

export default function BnplSimulator({ initialAmount = 150000, maxAmount = 3000000, minAmount = 50000 }) {
    const [mode, setMode] = useState('prix'); // 'prix' ou 'budget'
    const [amount, setAmount] = useState(initialAmount);
    const [budget, setBudget] = useState(25000);
    const [selectedFormulaId, setSelectedFormulaId] = useState(4); // 12 mois par défaut

    const formulas = [
        { id: 1, name: "Paiement Comptant (Cash)", durationMonths: 0, installments: 1, freq: "cash", label: "En 1 fois", discount: 7 },
        { id: 2, name: "30 jours", durationMonths: 1, installments: 30, freq: "daily", label: "/jour", discount: 0 },
        { id: 3, name: "90 jours (3 mois)", durationMonths: 3, installments: 3, freq: "monthly", label: "/mois", discount: 0 },
        { id: 4, name: "180 jours (6 mois)", durationMonths: 6, installments: 6, freq: "monthly", label: "/mois", discount: 0 },
        { id: 5, name: "12 mois (1 an)", durationMonths: 12, installments: 12, freq: "monthly", label: "/mois", discount: 0 },
        { id: 6, name: "18 mois", durationMonths: 18, installments: 18, freq: "monthly", label: "/mois", discount: 0 },
        { id: 7, name: "24 mois (2 ans)", durationMonths: 24, installments: 24, freq: "monthly", label: "/mois", discount: 0 }
    ];

    const currentFormula = formulas.find(f => f.id === selectedFormulaId) || formulas[4];

    // Calculs en mode "Prix du produit"
    const calculation = useMemo(() => {
        if (currentFormula.freq === 'cash') {
            const discountedPrice = Math.round(amount * (1 - currentFormula.discount / 100));
            return {
                downpayment: discountedPrice,
                installmentAmount: 0,
                totalPaid: discountedPrice,
                savings: amount - discountedPrice,
                periodCount: 1,
                label: 'Comptant à la commande'
            };
        }

        // Apport initial recommandé (0% à 10% selon barème)
        const downpayment = 0;
        const financed = amount - downpayment;
        const installmentAmount = Math.round(financed / currentFormula.installments);
        const totalPaid = downpayment + (installmentAmount * currentFormula.installments);

        return {
            downpayment,
            installmentAmount,
            totalPaid,
            savings: 0,
            periodCount: currentFormula.installments,
            label: currentFormula.label
        };
    }, [amount, currentFormula]);

    // Calculs en mode "Budget mensuel"
    const budgetCalculation = useMemo(() => {
        const capacity = budget * (currentFormula.durationMonths || 1);
        return {
            maxProductPrice: capacity,
            recommendedMonthly: budget,
            duration: currentFormula.name
        };
    }, [budget, currentFormula]);

    return (
        <div className="bg-surface-container-lowest rounded-2xl shadow-xl p-6 md:p-8 border border-border-subtle max-w-4xl mx-auto">
            {/* Titre & Sélecteur de Mode */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-border-subtle">
                <div className="flex items-center gap-3">
                    <div className="w-12 h-12 rounded-xl bg-brand-red-subtle flex items-center justify-center text-primary-container shrink-0">
                        <Calculator className="w-7 h-7" />
                    </div>
                    <div>
                        <h3 className="font-extrabold text-[20px] text-on-surface">Simulateur BNPL Intelligent</h3>
                        <p className="text-[13px] text-text-muted">Ajustez vos critères pour adapter vos versements sans frais cachés</p>
                    </div>
                </div>

                <div className="flex bg-slate-100 p-1 rounded-xl self-start md:self-auto">
                    <button
                        type="button"
                        onClick={() => setMode('prix')}
                        className={`px-4 py-2 rounded-lg text-[13px] font-bold transition-all flex items-center gap-1.5 ${
                            mode === 'prix' ? 'bg-primary-container text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'
                        }`}
                    >
                        <Tag className="w-4 h-4" />
                        J'ai un prix d'article
                    </button>
                    <button
                        type="button"
                        onClick={() => setMode('budget')}
                        className={`px-4 py-2 rounded-lg text-[13px] font-bold transition-all flex items-center gap-1.5 ${
                            mode === 'budget' ? 'bg-cobalt-deep text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'
                        }`}
                    >
                        <Wallet className="w-4 h-4" />
                        J'ai un budget mensuel
                    </button>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                {/* Colonne Paramètres & Durées */}
                <div className="lg:col-span-7 flex flex-col gap-6">
                    {mode === 'prix' ? (
                        <div>
                            <div className="flex justify-between items-baseline mb-2">
                                <label className="text-[14px] font-bold text-slate-800">Prix de l'article souhaité :</label>
                                <span className="text-[22px] font-extrabold text-primary-container">
                                    {amount.toLocaleString('fr-FR')} FCFA
                                </span>
                            </div>
                            <input
                                type="range"
                                min={minAmount}
                                max={maxAmount}
                                step="5000"
                                value={amount}
                                onChange={(e) => setAmount(Number(e.target.value))}
                                className="w-full h-2.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary-container mb-3"
                            />
                            <div className="flex justify-between text-[11px] font-semibold text-slate-400">
                                <span>{minAmount.toLocaleString('fr-FR')} FCFA</span>
                                <span>1 500 000 FCFA</span>
                                <span>{maxAmount.toLocaleString('fr-FR')} FCFA</span>
                            </div>
                        </div>
                    ) : (
                        <div>
                            <div className="flex justify-between items-baseline mb-2">
                                <label className="text-[14px] font-bold text-slate-800">Mon budget mensuel maximum :</label>
                                <span className="text-[22px] font-extrabold text-secondary">
                                    {budget.toLocaleString('fr-FR')} FCFA/m
                                </span>
                            </div>
                            <input
                                type="range"
                                min="10000"
                                max="250000"
                                step="2500"
                                value={budget}
                                onChange={(e) => setBudget(Number(e.target.value))}
                                className="w-full h-2.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-secondary mb-3"
                            />
                            <div className="flex justify-between text-[11px] font-semibold text-slate-400">
                                <span>10 000 FCFA/m</span>
                                <span>120 000 FCFA/m</span>
                                <span>250 000 FCFA/m</span>
                            </div>
                        </div>
                    )}

                    {/* Sélection de la Durée / Formule */}
                    <div>
                        <label className="text-[14px] font-bold text-slate-800 block mb-3">
                            Formule de remboursement :
                        </label>
                        <div className="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            {formulas.map((f) => (
                                <button
                                    key={f.id}
                                    type="button"
                                    onClick={() => setSelectedFormulaId(f.id)}
                                    className={`p-3 rounded-xl text-left border transition-all ${
                                        selectedFormulaId === f.id
                                            ? 'border-primary-container bg-brand-red-subtle shadow-sm ring-2 ring-primary-container/20'
                                            : 'border-slate-200 bg-white hover:border-slate-300'
                                    }`}
                                >
                                    <div className="flex items-center justify-between">
                                        <span className={`text-[13px] font-extrabold ${selectedFormulaId === f.id ? 'text-primary-container' : 'text-slate-800'}`}>
                                            {f.name}
                                        </span>
                                        {f.discount > 0 && (
                                            <span className="bg-emerald-600 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded">
                                                -7%
                                            </span>
                                        )}
                                    </div>
                                    <span className="text-[11px] text-slate-500 block mt-0.5">
                                        {f.freq === 'cash' ? 'Règlement 100% Cash' : `${f.installments} échéances`}
                                    </span>
                                </button>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Colonne Récapitulatif BNPL Financier */}
                <div className="lg:col-span-5 bg-gradient-to-b from-slate-50 to-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span className="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">
                            Synthèse de votre échéancier
                        </span>
                        <h4 className="text-[17px] font-extrabold text-cobalt-deep mb-4">
                            {currentFormula.name}
                        </h4>

                        {mode === 'prix' ? (
                            <div className="space-y-3.5 mb-6">
                                <div className="flex justify-between items-center text-[13px]">
                                    <span className="text-slate-600">Valeur initiale de l'article :</span>
                                    <span className="font-bold text-slate-800">{amount.toLocaleString('fr-FR')} FCFA</span>
                                </div>
                                {currentFormula.discount > 0 ? (
                                    <div className="flex justify-between items-center text-[13px] text-emerald-700 bg-emerald-50 p-2.5 rounded-lg">
                                        <span className="font-semibold">Remise Cash (-7%) :</span>
                                        <span className="font-extrabold">-{calculation.savings.toLocaleString('fr-FR')} FCFA</span>
                                    </div>
                                ) : (
                                    <div className="flex justify-between items-center text-[13px]">
                                        <span className="text-slate-600">Apport initial requis :</span>
                                        <span className="font-bold text-emerald-600">0 FCFA (Sans avance obligatoire)</span>
                                    </div>
                                )}
                                <div className="pt-3 border-t border-slate-200">
                                    <span className="text-[12px] font-semibold text-slate-500 block">
                                        {currentFormula.freq === 'cash' ? 'Montant net à régler :' : 'Votre versement périodique :'}
                                    </span>
                                    <div className="flex items-baseline gap-1 mt-1">
                                        <span className="text-[32px] font-black text-primary-container leading-none">
                                            {currentFormula.freq === 'cash'
                                                ? calculation.downpayment.toLocaleString('fr-FR')
                                                : calculation.installmentAmount.toLocaleString('fr-FR')}
                                        </span>
                                        <span className="text-[15px] font-extrabold text-slate-700">
                                            FCFA {calculation.label}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        ) : (
                            <div className="space-y-4 mb-6">
                                <div className="bg-cobalt-subtle p-3 rounded-xl border border-secondary/20">
                                    <span className="text-[12px] text-secondary font-bold block mb-0.5">Votre capacité d'équipement totale :</span>
                                    <span className="text-[28px] font-black text-cobalt-deep">
                                        {budgetCalculation.maxProductPrice.toLocaleString('fr-FR')} FCFA
                                    </span>
                                </div>
                                <p className="text-[12.5px] text-slate-600 leading-relaxed">
                                    Avec un budget de <strong>{budget.toLocaleString('fr-FR')} FCFA/mois</strong> sur <strong>{currentFormula.name}</strong>, vous pouvez acquérir jusqu'à <strong>{budgetCalculation.maxProductPrice.toLocaleString('fr-FR')} FCFA</strong> de produits aujourd'hui.
                                </p>
                            </div>
                        )}
                    </div>

                    {/* Avantages & Bouton d'action */}
                    <div className="pt-4 border-t border-slate-200 space-y-3">
                        <div className="flex items-center gap-2 text-[12px] text-emerald-700 font-semibold">
                            <BadgeCheck className="w-4.5 h-4.5 text-emerald-600 shrink-0" />
                            <span>Validation sous 1h chrono sans relevé bancaire</span>
                        </div>
                        <a
                            href="/catalogue"
                            className="w-full bg-primary-container hover:bg-brand-red-hover text-white py-3.5 rounded-xl font-bold text-[14px] text-center flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg"
                        >
                            <ShoppingBag className="w-5 h-5" />
                            Parcourir les produits compatibles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    );
}
