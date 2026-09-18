{{-- Aviso fijo arriba al centro: aparece solo cuando el navegador pierde la conexión a internet. --}}
<div id="offline-banner" class="fixed inset-x-0 top-0 z-[200] hidden justify-center px-4 pt-3">
    <div class="flex items-center gap-2 rounded-full bg-marca-rojo px-4 py-2 text-xs font-semibold text-marca-blanco shadow-lg sm:text-sm">
        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 5.64a9 9 0 010 12.72M5.64 5.64a9 9 0 000 12.72M9 15a3 3 0 016 0m-9.9-6.36L2 6m20 0l-3.1 2.64M12 21h.01"/>
        </svg>
        No hay conexión a internet
    </div>
</div>
<script>
    (function () {
        var banner = document.getElementById('offline-banner');
        if (!banner) return;

        function sync() {
            banner.classList.toggle('hidden', navigator.onLine);
            banner.classList.toggle('flex', !navigator.onLine);
        }

        window.addEventListener('online', sync);
        window.addEventListener('offline', sync);
        sync();
    })();
</script>
