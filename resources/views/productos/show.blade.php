@extends('layouts.app')

@section('title', $producto->nombre . ' · StockSmart')

@section('content')
    <a href="/productos" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver a productos</a>

    {{-- ===================== CABECERA DEL PRODUCTO ===================== --}}
    <div class="mt-4 flex flex-col gap-6 md:flex-row md:items-start">

        {{-- Galería del producto --}}
        <div class="w-full shrink-0 md:w-72">
            @if ($producto->fotos->count() > 0)
                <div class="aspect-square overflow-hidden rounded-lg border border-[#E2E8F0] bg-white">
                    <img id="foto-producto-principal"
                         src="{{ asset('storage/' . $producto->fotos->first()->ruta) }}"
                         alt="{{ $producto->nombre }}"
                         data-ampliable
                         data-fotos='@json($producto->fotos->map(fn($f) => asset("storage/" . $f->ruta)))'
                         class="h-full w-full cursor-zoom-in object-cover">
                </div>

                @if ($producto->fotos->count() > 1)
                    <div class="mt-2 grid grid-cols-5 gap-2">
                        @foreach ($producto->fotos as $i => $foto)
                            <button type="button"
                                    onclick="cambiarFoto('foto-producto-principal', '{{ asset('storage/' . $foto->ruta) }}', this)"
                                    class="miniatura-foto-producto-principal aspect-square overflow-hidden rounded-md border-2 {{ $i === 0 ? 'border-[#0D9488]' : 'border-transparent' }} transition-colors hover:border-[#0D9488]/60">
                                <img src="{{ asset('storage/' . $foto->ruta) }}" alt="" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="flex aspect-square flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-[#E2E8F0] bg-slate-50 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21zM14.25 8.25h.008v.008h-.008V8.25z" />
                    </svg>
                    <span class="text-xs">Sin fotos</span>
                </div>
            @endif
        </div>

        {{-- Datos del producto --}}
        <div class="flex flex-1 flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-[#0F172A]">{{ $producto->nombre }}</h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-500">{{ $producto->descripcion }}</p>

                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($producto->categorias as $categoria)
                        <span class="rounded-full border border-[#E2E8F0] bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600">
                            {{ $categoria->nombre }}
                        </span>
                    @endforeach
                </div>
            </div>

            <a href="/productos/{{ $producto->id }}/edit"
               class="inline-flex shrink-0 items-center justify-center rounded-md border border-[#0D9488] px-4 py-2 text-sm font-medium text-[#0D9488] transition-colors hover:bg-[#0D9488]/10">
                Editar producto
            </a>
        </div>
    </div>

    {{-- ===================== VARIANTES ===================== --}}
    <h2 class="mt-10 text-lg font-semibold text-[#0F172A]">Variantes</h2>

    <div class="mt-4 flex flex-col gap-4">
        @foreach ($producto->variantes as $variante)
            @php
                $atributos = json_decode($variante->atributos, true) ?? [];
                $precioActual = $variante->precios->sortByDesc('fecha')->first();
                $stockActual = $variante->stocks->sortByDesc('fecha')->first();
                $stockBajo = $stockActual && $stockActual->cantidad <= 5;
                $idFoto = 'foto-variante-' . $variante->id;
            @endphp

            <div class="rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
                <div class="flex gap-4 sm:gap-5">

                    {{-- Foto de la variante (al costado) --}}
                    <div class="w-24 shrink-0 sm:w-32">
                        @if ($variante->fotos->count() > 0)
                            <div class="aspect-square overflow-hidden rounded-md border border-[#E2E8F0] bg-white">
                                     <img id="{{ $idFoto }}"
                                     src="{{ asset('storage/' . $variante->fotos->first()->ruta) }}"
                                     alt="Variante {{ $variante->sku }}"
                                     data-ampliable
                                     data-fotos='@json($variante->fotos->map(fn($f) => asset("storage/" . $f->ruta)))'
                                     class="h-full w-full cursor-zoom-in object-cover">
                            </div>

                            @if ($variante->fotos->count() > 1)
                                <div class="mt-1.5 grid grid-cols-4 gap-1">
                                    @foreach ($variante->fotos as $i => $foto)
                                        <button type="button"
                                                onclick="cambiarFoto('{{ $idFoto }}', '{{ asset('storage/' . $foto->ruta) }}', this)"
                                                class="miniatura-{{ $idFoto }} aspect-square overflow-hidden rounded border-2 {{ $i === 0 ? 'border-[#0D9488]' : 'border-transparent' }} transition-colors hover:border-[#0D9488]/60">
                                            <img src="{{ asset('storage/' . $foto->ruta) }}" alt="" class="h-full w-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="flex aspect-square items-center justify-center rounded-md border border-dashed border-[#E2E8F0] bg-slate-50 text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21zM14.25 8.25h.008v.008h-.008V8.25z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Datos de la variante --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-semibold text-[#0F172A]">SKU: {{ $variante->sku }}</span>
                            @foreach ($atributos as $clave => $valor)
                                <span class="rounded-full border border-[#E2E8F0] bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600">
                                    {{ $clave }}: {{ $valor }}
                                </span>
                            @endforeach
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-x-6 gap-y-1 text-sm">
                            <p>
                                <span class="text-slate-500">Precio:</span>
                                <span class="font-medium text-[#0F172A]">
                                    {{ $precioActual ? '$' . $precioActual->precio : 'Sin precio cargado' }}
                                </span>
                                @if ($precioActual && $dolar)
                                    <span class="text-slate-500">(USD ${{ number_format($precioActual->precio / $dolar['venta'], 2) }})</span>
                                @endif
                            </p>

                            <p class="flex items-center gap-2">
                                <span class="text-slate-500">Stock:</span>
                                @if ($stockBajo)
                                    <span class="rounded-full bg-[#D97706]/10 px-2 py-0.5 font-medium text-[#D97706]">
                                        {{ $stockActual->cantidad }} {{ $stockActual->cantidad == 1 ? 'unidad' : 'unidades' }} — stock bajo
                                    </span>
                                @else
                                    <span class="font-medium text-[#0F172A]">
                                        {{ $stockActual ? $stockActual->cantidad . ($stockActual->cantidad == 1 ? ' unidad' : ' unidades') : 'Sin stock cargado' }}
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="mt-4 flex flex-wrap items-end gap-3 border-t border-[#E2E8F0] pt-4">
                            <form method="POST" action="/variantes/{{ $variante->id }}/actualizar" class="flex flex-wrap items-end gap-3">
                                @csrf
                                <input type="hidden" name="id_producto" value="{{ $producto->id }}">

                                <div>
                                    <label class="block text-xs font-medium text-slate-500">Nuevo precio</label>
                                    <input type="number" step="0.01" name="precio" placeholder="sin cambios"
                                           class="mt-1 w-32 rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-500">Cantidad</label>
                                    <input type="number" name="cantidad" placeholder="sin cambios"
                                           class="mt-1 w-28 rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-500">Movimiento</label>
                                    <select name="movimiento"
                                            class="mt-1 rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                                        <option value="entrada">Entrada</option>
                                        <option value="salida">Salida</option>
                                    </select>
                                </div>

                                <button type="submit"
                                        class="rounded-md bg-[#0D9488] px-4 py-1.5 text-sm font-medium text-white transition-colors hover:bg-[#0D9488]/90">
                                    Actualizar
                                </button>
                            </form>

                            <div class="ml-auto flex items-center gap-4">
                                <a href="/variantes/{{ $variante->id }}/edit"
                                   class="text-sm font-medium text-[#0D9488] hover:underline">
                                    Editar variante
                                </a>

                                <form method="POST" action="/variantes/{{ $variante->id }}"
                                      onsubmit="return confirm('¿Seguro de que querés borrar esta variante?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-700 hover:underline">
                                        Borrar variante
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===================== AGREGAR VARIANTE ===================== --}}
    <div class="mt-8 rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
        <h3 class="text-lg font-semibold text-[#0F172A]">Agregar variante</h3>

        <form method="POST" action="/productos/{{ $producto->id }}/variantes" enctype="multipart/form-data" class="mt-4 flex flex-col gap-6">
            @csrf

            <div class="flex flex-wrap gap-4">
                <div class="min-w-[140px] flex-1">
                    <label class="block text-xs font-medium text-slate-500">SKU</label>
                    <input type="text" name="sku" required
                           class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                </div>

                <div class="min-w-[140px] flex-1">
                    <label class="block text-xs font-medium text-slate-500">Precio inicial</label>
                    <input type="number" step="0.01" name="precio" required
                           class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                </div>

                <div class="min-w-[140px] flex-1">
                    <label class="block text-xs font-medium text-slate-500">Stock inicial</label>
                    <input type="number" name="cantidad" required
                           class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                </div>

                @if (count($plantilla) > 0)
                    @foreach ($plantilla as $campo)
                        <div class="min-w-[140px] flex-1">
                            <label class="block text-xs font-medium text-slate-500">{{ $campo }}</label>
                            <input type="hidden" name="atributo_nombre[]" value="{{ $campo }}">
                            <input type="text" name="atributo_valor[]"
                                   class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                        </div>
                    @endforeach
                @else
                    <p class="w-full text-xs text-slate-400">
                        Este producto todavía no tiene atributos definidos.
                        <a href="/productos/{{ $producto->id }}/edit" class="font-medium text-[#0D9488] hover:underline">Definilos desde "Editar producto"</a> para poder cargarlos acá.
                    </p>
                @endif
            </div>

            {{-- Fotos de la nueva variante --}}
            <div>
                <label class="block text-xs font-medium text-slate-500">Fotos de la variante</label>
                <p class="mt-0.5 text-xs text-slate-400">Podés sacar con la cámara o elegir de la galería (ej: frente y espalda).</p>

                <label class="mt-2 inline-flex cursor-pointer items-center gap-2 rounded-md border border-[#0D9488] px-3 py-1.5 text-sm font-medium text-[#0D9488] transition-colors hover:bg-[#0D9488]/10">
                    + Agregar fotos
                    <input type="file" id="fotos-variante-input" name="fotos[]" accept="image/*" multiple class="hidden">
                </label>

                <div id="fotos-variante-preview" class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-6"></div>
                <p id="fotos-variante-error" class="mt-1 text-xs text-[#D97706]"></p>
            </div>

            <div>
                <button type="submit"
                        class="rounded-md bg-[#0F172A] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                    Agregar variante
                </button>
            </div>
        </form>
    </div>

    <script>
        // ===== GALERÍA: tocar una miniatura la pasa a la foto grande =====
        function cambiarFoto(idImagen, src, boton) {
            document.getElementById(idImagen).src = src;
            document.querySelectorAll('.miniatura-' + idImagen).forEach(b => {
                b.classList.remove('border-[#0D9488]');
                b.classList.add('border-transparent');
            });
            boton.classList.remove('border-transparent');
            boton.classList.add('border-[#0D9488]');
        }

        // ===== SELECTOR DE FOTOS DE LA NUEVA VARIANTE (se van sumando) =====
        let fotosVariante = [];
        const fotosVarianteInput = document.getElementById('fotos-variante-input');
        const MAX_MB = 5;

        fotosVarianteInput.addEventListener('change', function () {
            const errorEl = document.getElementById('fotos-variante-error');
            errorEl.textContent = '';

            for (const archivo of fotosVarianteInput.files) {
                if (!archivo.type.startsWith('image/')) {
                    errorEl.textContent = 'Solo se pueden subir imágenes.';
                    continue;
                }
                if (archivo.size > MAX_MB * 1024 * 1024) {
                    errorEl.textContent = `"${archivo.name}" pesa más de ${MAX_MB} MB.`;
                    continue;
                }
                fotosVariante.push(archivo);
            }
            sincronizarFotosVariante();
        });

        function quitarFotoVariante(indice) {
            fotosVariante.splice(indice, 1);
            sincronizarFotosVariante();
        }

        function sincronizarFotosVariante() {
            const dt = new DataTransfer();
            fotosVariante.forEach(f => dt.items.add(f));
            fotosVarianteInput.files = dt.files;

            const preview = document.getElementById('fotos-variante-preview');
            preview.innerHTML = '';
            fotosVariante.forEach((archivo, i) => {
                const div = document.createElement('div');
                div.className = 'relative aspect-square overflow-hidden rounded-md border border-[#E2E8F0]';
                div.innerHTML = `
                    <img src="${URL.createObjectURL(archivo)}" class="h-full w-full object-cover">
                    <button type="button" onclick="quitarFotoVariante(${i})"
                            class="absolute right-1 top-1 rounded bg-white/90 px-1.5 text-xs font-medium text-slate-600 hover:text-slate-900">
                        ✕
                    </button>
                `;
                preview.appendChild(div);
            });
        }
    </script>
@endsection