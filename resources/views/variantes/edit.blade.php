@extends('layouts.app')

@section('title', 'Editar variante · StockSmart')

@section('content')
    <a href="/productos/{{ $variante->producto->id }}" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver al producto</a>

    <h1 class="mt-4 text-2xl font-bold tracking-tight text-[#0F172A]">Editar variante</h1>
    <p class="mt-1 text-sm text-slate-500">{{ $variante->producto->nombre }}</p>

    <div class="mt-6 max-w-2xl rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm">
        <form method="POST" action="/variantes/{{ $variante->id }}" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf
            @method('PUT')

            {{-- SKU --}}
            <div>
                <label class="block text-sm font-medium text-[#1E293B]">SKU</label>
                <input type="text" name="sku" value="{{ $variante->sku }}" required
                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-2 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
            </div>

            {{-- Características --}}
            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Características</label>

                @if (count($plantilla) > 0)
                    <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($plantilla as $campo)
                            <div>
                                <label class="block text-xs font-medium text-slate-500">{{ $campo }}</label>
                                <input type="hidden" name="atributo_nombre[]" value="{{ $campo }}">
                                <input type="text" name="atributo_valor[]" value="{{ $atributos[$campo] ?? '' }}"
                                       placeholder="Dejalo vacío si no aplica"
                                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-2 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-1 text-xs text-slate-400">
                        Este producto no tiene características definidas.
                        <a href="/productos/{{ $variante->producto->id }}/edit" class="font-medium text-[#0D9488] hover:underline">Definilas desde "Editar producto"</a>.
                    </p>
                @endif
            </div>

            {{-- Fotos actuales --}}
            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Fotos actuales</label>

                @if ($variante->fotos->count() > 0)
                    <p class="mt-0.5 text-xs text-slate-400">Tocá ✕ para marcar una foto para borrar. Se borra recién cuando guardás.</p>
                    <div class="mt-2 grid grid-cols-3 gap-2 sm:grid-cols-4">
                        @foreach ($variante->fotos as $foto)
                            <div class="foto-existente relative aspect-square overflow-hidden rounded-md border border-[#E2E8F0]">
                                <img src="{{ asset('storage/' . $foto->ruta) }}" alt=""
                                     data-ampliable
                                     data-fotos='@json($variante->fotos->map(fn($f) => asset("storage/" . $f->ruta)))'
                                     class="h-full w-full cursor-zoom-in object-cover transition-opacity">

                                <input type="checkbox" name="eliminar_fotos[]" value="{{ $foto->id }}" class="hidden">

                                <div class="aviso-borrar pointer-events-none absolute inset-0 hidden items-center justify-center bg-white/70 text-xs font-medium text-[#D97706]">
                                    Se va a borrar
                                </div>

                                <button type="button" onclick="marcarParaBorrar(this)"
                                        class="absolute right-1 top-1 rounded bg-white/90 px-1.5 text-xs font-medium text-slate-600 hover:text-slate-900">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-1 text-xs text-slate-400">Esta variante todavía no tiene fotos.</p>
                @endif
            </div>

            {{-- Fotos nuevas --}}
            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Agregar fotos nuevas</label>
                <p class="mt-0.5 text-xs text-slate-400">Desde el celu podés sacar con la cámara o elegir de la galería.</p>

                <label class="mt-2 inline-flex cursor-pointer items-center gap-2 rounded-md border border-[#0D9488] px-3 py-1.5 text-sm font-medium text-[#0D9488] transition-colors hover:bg-[#0D9488]/10">
                    + Agregar fotos
                    <input type="file" id="fotos-input" name="fotos[]" accept="image/*" multiple class="hidden">
                </label>

                <div id="fotos-preview" class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-4"></div>
                <p id="fotos-error" class="mt-1 text-xs text-[#D97706]"></p>
            </div>

            <div class="mt-2 flex items-center gap-4">
                <button type="submit"
                        class="rounded-md bg-[#0F172A] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                    Guardar cambios
                </button>
                <a href="/productos/{{ $variante->producto->id }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        function marcarParaBorrar(boton) {
            const contenedor = boton.closest('.foto-existente');
            const checkbox = contenedor.querySelector('input[type="checkbox"]');
            const imagen = contenedor.querySelector('img');
            const aviso = contenedor.querySelector('.aviso-borrar');

            checkbox.checked = !checkbox.checked;

            imagen.classList.toggle('opacity-40', checkbox.checked);
            aviso.classList.toggle('hidden', !checkbox.checked);
            aviso.classList.toggle('flex', checkbox.checked);
            boton.textContent = checkbox.checked ? '↺' : '✕';
        }

        let fotosElegidas = [];
        const fotosInput = document.getElementById('fotos-input');
        const MAX_MB = 5;

        fotosInput.addEventListener('change', function () {
            const errorEl = document.getElementById('fotos-error');
            errorEl.textContent = '';

            for (const archivo of fotosInput.files) {
                if (!archivo.type.startsWith('image/')) {
                    errorEl.textContent = 'Solo se pueden subir imágenes.';
                    continue;
                }
                if (archivo.size > MAX_MB * 1024 * 1024) {
                    errorEl.textContent = `"${archivo.name}" pesa más de ${MAX_MB} MB.`;
                    continue;
                }
                fotosElegidas.push(archivo);
            }
            sincronizarFotos();
        });

        function quitarFoto(indice) {
            fotosElegidas.splice(indice, 1);
            sincronizarFotos();
        }

        function sincronizarFotos() {
            const dt = new DataTransfer();
            fotosElegidas.forEach(f => dt.items.add(f));
            fotosInput.files = dt.files;

            const preview = document.getElementById('fotos-preview');
            preview.innerHTML = '';
            fotosElegidas.forEach((archivo, i) => {
                const div = document.createElement('div');
                div.className = 'relative aspect-square overflow-hidden rounded-md border border-[#E2E8F0]';
                div.innerHTML = `
                    <img src="${URL.createObjectURL(archivo)}" data-ampliable class="h-full w-full cursor-zoom-in object-cover">
                    <button type="button" onclick="quitarFoto(${i})"
                            class="absolute right-1 top-1 rounded bg-white/90 px-1.5 text-xs font-medium text-slate-600 hover:text-slate-900">
                        ✕
                    </button>
                `;
                preview.appendChild(div);
            });
        }
    </script>
@endsection