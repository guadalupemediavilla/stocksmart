<div id="modal-confirmar" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#0F172A]/60 p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="modal-confirmar-texto"
         class="w-full max-w-sm rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm">
        <p id="modal-confirmar-texto" class="text-sm font-medium text-[#1E293B]"></p>

        <div class="mt-5 flex justify-end gap-3">
            <button type="button" id="modal-confirmar-cancelar"
                    class="rounded-md border border-[#E2E8F0] bg-white px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">
                Cancelar
            </button>
            <button type="button" id="modal-confirmar-borrar"
                    class="rounded-md bg-slate-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700">
                Borrar
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('modal-confirmar');
    const texto = document.getElementById('modal-confirmar-texto');
    const btnCancelar = document.getElementById('modal-confirmar-cancelar');
    const btnBorrar = document.getElementById('modal-confirmar-borrar');

    let formPendiente = null;
    let disparador = null;

    // Delegación de eventos: funciona con cualquier formulario con data-confirmar,
    // incluso si se agrega a la página después (no hace falta re-enganchar nada)
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('form[data-confirmar]');
        if (!form) return;

        e.preventDefault();
        formPendiente = form;
        disparador = document.activeElement;
        texto.textContent = form.dataset.confirmar;
        abrir();
    });

    function abrir() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        btnCancelar.focus();
    }

    function cerrar() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        formPendiente = null;
        if (disparador) disparador.focus();
    }

    btnCancelar.addEventListener('click', cerrar);

    btnBorrar.addEventListener('click', function () {
        if (!formPendiente) return;
        // form.submit() no dispara el evento "submit" (a diferencia de requestSubmit),
        // así que el formulario se manda sin volver a pasar por el modal
        formPendiente.submit();
        cerrar();
    });

    // Tocar el fondo oscuro (no la tarjeta) cierra
    modal.addEventListener('click', function (e) {
        if (e.target === modal) cerrar();
    });

    // Esc cierra
    document.addEventListener('keydown', function (e) {
        if (modal.classList.contains('hidden')) return;
        if (e.key === 'Escape') cerrar();
    });
})();
</script>
