<div id="lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#0F172A]/90 p-4">
    <button type="button" id="lb-cerrar" aria-label="Cerrar"
            class="absolute right-4 top-4 rounded-md px-3 py-1 text-2xl text-white/80 transition-colors hover:bg-white/10 hover:text-white">
        ✕
    </button>

    <button type="button" id="lb-anterior" aria-label="Anterior"
            class="absolute left-2 top-1/2 -translate-y-1/2 rounded-md px-3 py-2 text-4xl text-white/80 transition-colors hover:bg-white/10 hover:text-white sm:left-6">
        ‹
    </button>

    <img id="lb-imagen" src="" alt="" class="max-h-[85vh] max-w-full rounded-md object-contain shadow-lg">

    <button type="button" id="lb-siguiente" aria-label="Siguiente"
            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md px-3 py-2 text-4xl text-white/80 transition-colors hover:bg-white/10 hover:text-white sm:right-6">
        ›
    </button>

    <p id="lb-contador" class="absolute bottom-4 left-1/2 -translate-x-1/2 text-sm font-medium text-white/70"></p>
</div>

<script>
(function () {
    const lightbox  = document.getElementById('lightbox');
    const imagen    = document.getElementById('lb-imagen');
    const contador  = document.getElementById('lb-contador');
    const anterior  = document.getElementById('lb-anterior');
    const siguiente = document.getElementById('lb-siguiente');

    let fotos = [];
    let actual = 0;

    // Escucha clics en TODA la página: sirve también para imágenes creadas después con JS (previews)
    document.addEventListener('click', function (e) {
        const el = e.target.closest('[data-ampliable]');
        if (!el) return;

        const src = el.currentSrc || el.src;
        fotos = el.dataset.fotos ? JSON.parse(el.dataset.fotos) : [src];
        actual = Math.max(0, fotos.indexOf(src));
        abrir();
    });

    function mostrar() {
        imagen.src = fotos[actual];
        const varias = fotos.length > 1;
        anterior.classList.toggle('hidden', !varias);
        siguiente.classList.toggle('hidden', !varias);
        contador.textContent = varias ? `${actual + 1} / ${fotos.length}` : '';
    }

    function abrir() {
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        document.body.style.overflow = 'hidden';
        mostrar();
    }

    function cerrar() {
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        document.body.style.overflow = '';
        imagen.src = '';
    }

    function mover(paso) {
        actual = (actual + paso + fotos.length) % fotos.length;
        mostrar();
    }

    document.getElementById('lb-cerrar').addEventListener('click', cerrar);
    anterior.addEventListener('click', () => mover(-1));
    siguiente.addEventListener('click', () => mover(1));

    // Tocar el fondo oscuro (no la foto) cierra
    lightbox.addEventListener('click', e => { if (e.target === lightbox) cerrar(); });

    // Teclado
    document.addEventListener('keydown', e => {
        if (lightbox.classList.contains('hidden')) return;
        if (e.key === 'Escape') cerrar();
        if (e.key === 'ArrowLeft' && fotos.length > 1) mover(-1);
        if (e.key === 'ArrowRight' && fotos.length > 1) mover(1);
    });

    // Deslizar con el dedo en el celu
    let inicioX = null;
    lightbox.addEventListener('touchstart', e => { inicioX = e.touches[0].clientX; });
    lightbox.addEventListener('touchend', e => {
        if (inicioX === null || fotos.length < 2) return;
        const diferencia = e.changedTouches[0].clientX - inicioX;
        if (Math.abs(diferencia) > 50) mover(diferencia < 0 ? 1 : -1);
        inicioX = null;
    });
})();
</script>