@extends('layouts.app')

@section('title', 'Agregar categoría · StockSmart')

@section('content')
    <a href="/categorias" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver a categorías</a>

    <h1 class="mt-4 text-2xl font-bold tracking-tight text-[#0F172A]">Agregar categoría</h1>

    <div class="mt-6 max-w-md rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm">
        <form method="POST" action="/categorias" class="flex flex-col gap-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Nombre</label>
                <input type="text" name="nombre" required
                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-2 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="rounded-md bg-[#0F172A] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                    Guardar
                </button>
                <a href="/categorias" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
