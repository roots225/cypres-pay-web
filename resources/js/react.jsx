import React from 'react';
import { createRoot } from 'react-dom/client';
import BnplSimulator from './components/BnplSimulator';
import DigitalSignatureCanvas from './components/DigitalSignatureCanvas';
import ProductInstallmentSelector from './components/ProductInstallmentSelector';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Montage du simulateur BNPL (sur la page /simulateur ou dans la modale)
    const simulatorEl = document.getElementById('react-bnpl-simulator');
    if (simulatorEl) {
        const root = createRoot(simulatorEl);
        const amount = parseInt(simulatorEl.dataset.amount || "150000", 10);
        root.render(<BnplSimulator initialAmount={amount} />);
    }

    // 2. Montage du canvas de signature numérique (sur la page de validation de commande / KYC)
    const signatureEl = document.getElementById('react-digital-signature');
    if (signatureEl) {
        const root = createRoot(signatureEl);
        const contractId = signatureEl.dataset.contractId || "CP-CONTRAT-2026";
        root.render(<DigitalSignatureCanvas contractId={contractId} />);
    }

    // 3. Montage du sélecteur d'échéances sur la fiche produit
    const productSelectorEl = document.getElementById('react-product-installment-selector');
    if (productSelectorEl) {
        const root = createRoot(productSelectorEl);
        const productId = parseInt(productSelectorEl.dataset.productId || "1", 10);
        const price = parseInt(productSelectorEl.dataset.creditPrice || productSelectorEl.dataset.price || "120000", 10);
        const cashPrice = parseInt(productSelectorEl.dataset.cashPrice || Math.round(price * 0.93), 10);
        const name = productSelectorEl.dataset.productName || productSelectorEl.dataset.name || "Article Cypress Pay";
        const image = productSelectorEl.dataset.imageUrl || "";
        root.render(<ProductInstallmentSelector productId={productId} basePrice={price} cashPriceInitial={cashPrice} productName={name} imageUrl={image} />);
    }
});
