<div
    x-data="{
        h: {{ $hours }},
        m: {{ $minutes }},
        s: {{ $seconds }},
        init() {
            setInterval(() => {
                if (this.s > 0) {
                    this.s--;
                } else {
                    this.s = 59;
                    if (this.m > 0) {
                        this.m--;
                    } else {
                        this.m = 59;
                        if (this.h > 0) this.h--;
                    }
                }
            }, 1000);
        }
    }"
    class="flex items-center gap-1.5 bg-surface-container-lowest px-4 py-2 rounded-lg shadow-xs border border-border-subtle"
>
    <span class="font-label-sm text-label-sm text-text-muted uppercase">Fin dans :</span>
    <div class="flex items-center gap-1 font-headline-sm text-headline-sm text-primary-container font-extrabold">
        <span class="bg-brand-red-subtle px-2 py-0.5 rounded" x-text="h.toString().padStart(2, '0')">08</span>:
        <span class="bg-brand-red-subtle px-2 py-0.5 rounded" x-text="m.toString().padStart(2, '0')">42</span>:
        <span class="bg-brand-red-subtle px-2 py-0.5 rounded" x-text="s.toString().padStart(2, '0')">15</span>
    </div>
</div>
