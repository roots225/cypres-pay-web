import React, { useRef, useState, useEffect } from 'react';
import { PenTool, RotateCw, BadgeCheck } from 'lucide-react';

export default function DigitalSignatureCanvas({ onSaveSignature, contractId = "CP-CONTRAT-2026" }) {
    const canvasRef = useRef(null);
    const [isDrawing, setIsDrawing] = useState(false);
    const [hasSignature, setHasSignature] = useState(false);

    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#081B4B';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    }, []);

    const getPos = (e) => {
        const canvas = canvasRef.current;
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: clientX - rect.left,
            y: clientY - rect.top
        };
    };

    const startDrawing = (e) => {
        e.preventDefault();
        setIsDrawing(true);
        const { x, y } = getPos(e);
        const ctx = canvasRef.current.getContext('2d');
        ctx.beginPath();
        ctx.moveTo(x, y);
    };

    const draw = (e) => {
        if (!isDrawing) return;
        e.preventDefault();
        const { x, y } = getPos(e);
        const ctx = canvasRef.current.getContext('2d');
        ctx.lineTo(x, y);
        ctx.stroke();
        setHasSignature(true);
    };

    const stopDrawing = () => {
        setIsDrawing(false);
    };

    const clearCanvas = () => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        setHasSignature(false);
    };

    const handleConfirm = () => {
        if (!hasSignature) return;
        const dataUrl = canvasRef.current.toDataURL('image/png');
        if (onSaveSignature) {
            onSaveSignature(dataUrl);
        } else {
            alert("Signature électronique enregistrée pour le contrat " + contractId);
        }
    };

    return (
        <div className="bg-white p-6 rounded-2xl shadow-lg border border-slate-200 max-w-lg mx-auto">
            <div className="flex items-center justify-between mb-3">
                <div className="flex items-center gap-2">
                    <PenTool className="w-5 h-5 text-primary-container" />
                    <h4 className="font-extrabold text-[16px] text-slate-800">Signature Électronique du Contrat</h4>
                </div>
                <span className="text-[11px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-500">
                    {contractId}
                </span>
            </div>

            <p className="text-[12.5px] text-slate-500 mb-4 leading-normal">
                Veuillez signer dans le cadre ci-dessous avec votre doigt ou stylet pour certifier votre engagement de paiement échelonné.
            </p>

            <div className="relative border-2 border-dashed border-slate-300 rounded-xl overflow-hidden bg-slate-50 mb-4">
                <canvas
                    ref={canvasRef}
                    width={440}
                    height={180}
                    className="w-full touch-none cursor-crosshair bg-white"
                    onMouseDown={startDrawing}
                    onMouseMove={draw}
                    onMouseUp={stopDrawing}
                    onMouseLeave={stopDrawing}
                    onTouchStart={startDrawing}
                    onTouchMove={draw}
                    onTouchEnd={stopDrawing}
                />
                {!hasSignature && (
                    <div className="absolute inset-0 flex items-center justify-center pointer-events-none text-slate-300 font-bold text-[14px]">
                        Signer ici (Doigt ou stylet)
                    </div>
                )}
            </div>

            <div className="flex items-center justify-between gap-3">
                <button
                    type="button"
                    onClick={clearCanvas}
                    className="px-4 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 font-bold text-[13px] flex items-center gap-1.5 transition-colors"
                >
                    <RotateCw className="w-4 h-4" />
                    Effacer
                </button>

                <button
                    type="button"
                    disabled={!hasSignature}
                    onClick={handleConfirm}
                    className={`px-5 py-2.5 rounded-lg font-bold text-[13px] flex items-center gap-1.5 transition-all shadow-sm ${
                        hasSignature
                            ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-200'
                            : 'bg-slate-200 text-slate-400 cursor-not-allowed'
                    }`}
                >
                    <BadgeCheck className="w-4.5 h-4.5" />
                    Valider ma signature
                </button>
            </div>
        </div>
    );
}
