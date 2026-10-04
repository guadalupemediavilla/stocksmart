@extends('layouts.app')

@section('title', 'Nuevo producto · StockSmart')

@section('content')
    <a href="/productos" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver a productos</a>

    <h1 class="mt-4 text-2xl font-bold tracking-tight text-[#0F172A]">Nuevo producto</h1>

    <div class="mt-6 max-w-2xl rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm">
        <form method="POST" action="/productos" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Nombre</label>
                <input type="text" name="nombre" required
                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-2 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Descripción</label>
                <input type="text" name="descripcion"
                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-2 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Fotos del producto</label>
                

                <label class="mt-2 inline-flex cursor-pointer items-center gap-2 rounded-md border border-[#0D9488] px-3 py-1.5 text-sm font-medium text-[#0D9488] transition-colors hover:bg-[#0D9488]/10">
                    + Agregar fotos
                    <input type="file" id="fotos-input" name="fotos[]" accept="image/*" multiple class="hidden">
                </label>

                <div id="fotos-preview" class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-4"></div>
                <p id="fotos-error" class="mt-1 text-xs text-[#D97706]"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Categorías</label>
                <div id="categorias-container" class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach($categorias as $categoria)
                        <label class="flex items-center gap-2 rounded-md border border-[#E2E8F0] px-3 py-2 text-sm text-[#1E293B]">
                            <input type="checkbox" name="id_categoria[]" value="{{ $categoria->id }}" class="accent-[#0D9488]">
                            {{ $categoria->nombre }}
                        </label>
                    @endforeach
                </div>

                <div class="mt-3 flex gap-2">
                    <input type="text" id="nueva-categoria-nombre" placeholder="Nombre de una categoría nueva"
                           class="flex-1 rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                    <button type="button" onclick="crearCategoria()"
                            class="rounded-md border border-[#0D9488] px-3 py-1.5 text-sm font-medium text-[#0D9488] transition-colors hover:bg-[#0D9488]/10">
                        + Agregar categoría
                    </button>
                </div>
                <p id="categoria-error" class="mt-1 text-xs text-[#D97706]"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Atributos del producto</label>
                <p class="mt-0.5 text-xs text-slate-400">Definí qué características van a tener las variantes de este producto (ej: Talle, Color, Tamaño).</p>

                <div id="plantilla-container" class="mt-2 flex flex-col gap-2"></div>

                <button type="button" onclick="agregarCampoPlantilla()"
                        class="mt-3 rounded-md border border-[#0D9488] px-3 py-1.5 text-sm font-medium text-[#0D9488] transition-colors hover:bg-[#0D9488]/10">
                    + Agregar campo
                </button>
            </div>

            <div class="mt-2 flex items-center gap-4">
                <button type="submit"
                        class="rounded-md bg-[#0F172A] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                    Guardar
                </button>
                <a href="/productos" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        // ===== FOTOS =====
        // Guardamos acá todas las fotos elegidas, para que no se pisen entre una elección y otra
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
            // Volvemos a cargar en el input la lista completa (las viejas + las nuevas)
            const dt = new DataTransfer();
            fotosElegidas.forEach(f => dt.items.add(f));
            fotosInput.files = dt.files;

            // Dibujamos las miniaturas
            const preview = document.getElementById('fotos-preview');
            preview.innerHTML = '';
            fotosElegidas.forEach((archivo, i) => {
                const div = document.createElement('div');
                div.className = 'relative aspect-square overflow-hidden rounded-md border border-[#E2E8F0]';
                div.innerHTML = `
                    <img src="${URL.createObjectURL(archivo)}" class="h-full w-full object-cover">
                    <button type="button" onclick="quitarFoto(${i})"
                            class="absolute right-1 top-1 rounded bg-white/90 px-1.5 text-xs font-medium text-slate-600 hover:text-slate-900">
                        ✕
                    </button>
                `;
                preview.appendChild(div);
            });
        }

        // ===== PLANTILLA DE ATRIBUTOS =====
        function agregarCampoPlantilla() {
            const container = document.getElementById('plantilla-container');
            const fila = document.createElement('div');
            fila.className = 'plantilla-fila flex flex-wrap items-end gap-2';
            fila.innerHTML = `
                <input type="text" name="plantilla_nombre[]"
                       class="w-48 rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                <button type="button" onclick="quitarCampoPlantilla(this)"
                        class="text-sm font-medium text-slate-400 hover:text-slate-600">
                    Quitar
                </button>
            `;
            container.appendChild(fila);
        }

        function quitarCampoPlantilla(boton) {
            boton.closest('.plantilla-fila').remove();
        }

        // ===== CATEGORÍA AL VUELO =====
        function crearCategoria() {
            const input = document.getElementById('nueva-categoria-nombre');
            const nombre = input.value.trim();
            const errorEl = document.getElementById('categoria-error');
            errorEl.textContent = '';

            if (nombre === '') {
                errorEl.textContent = 'Escribí un nombre antes de agregar.';
                return;
            }

            fetch('/categorias/ajax', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ nombre: nombre }),
            })
            .then(response => response.json())
            .then(categoria => {
                const container = document.getElementById('categorias-container');
                const nuevoLabel = document.createElement('label');
                nuevoLabel.className = 'flex items-center gap-2 rounded-md border border-[#E2E8F0] px-3 py-2 text-sm text-[#1E293B]';
                nuevoLabel.innerHTML = `
                    <input type="checkbox" name="id_categoria[]" value="${categoria.id}" class="accent-[#0D9488]" checked>
                    ${categoria.nombre}
                `;
                container.appendChild(nuevoLabel);
                input.value = '';
            })
            .catch(() => {
                errorEl.textContent = 'No se pudo crear la categoría. Probá de nuevo.';
            });
        }
    </script>
@endsection