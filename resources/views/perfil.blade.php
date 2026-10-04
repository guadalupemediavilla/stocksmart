@extends('layouts.app')

@section('title', 'Mi perfil · StockSmart')

@section('content')
<a href="/home" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver al inicio</a>

    <div class="max-w-2xl overflow-hidden rounded-lg border border-[#E2E8F0] bg-white shadow-sm">
        <div class="flex items-center gap-4 border-b border-[#E2E8F0] p-6">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#0F172A] text-lg font-semibold uppercase text-white">
                {{ mb_substr(Auth::user()->nombre, 0, 1) }}{{ mb_substr(Auth::user()->apellido, 0, 1) }}
            </div>

            <h1 class="min-w-0 break-words text-2xl font-bold tracking-tight text-[#0F172A]">{{ Auth::user()->nombre }}</h1>
        </div>

        <dl class="divide-y divide-[#E2E8F0] text-sm">
            <div class="flex flex-col gap-1 px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="font-medium text-slate-500">Nombre</dt>
                <dd class="break-words text-[#0F172A] sm:col-span-2">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</dd>
            </div>

            <div class="flex flex-col gap-1 px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="font-medium text-slate-500">Mail</dt>
                <dd class="break-words text-[#0F172A] sm:col-span-2">{{ Auth::user()->mail }}</dd>
            </div>

            <div class="flex flex-col gap-1 px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                <dt class="font-medium text-slate-500">Rol</dt>
                <dd class="sm:col-span-2">
                    @if (Auth::user()->rol === 'admin')
                        <span class="rounded-full bg-[#0F172A] px-2.5 py-0.5 text-xs font-medium text-white">Admin</span>
                    @else
                        <span class="rounded-full border border-[#E2E8F0] bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ ucfirst(Auth::user()->rol) }}</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>
@endsection
