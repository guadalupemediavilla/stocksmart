@extends('layouts.guest')

@section('title', 'StockSmart')

@section('content')
    @include('partials.brand')

    <div class="mt-10 flex justify-center">
        @auth
            <a href="/home"
               class="inline-flex items-center justify-center rounded-md bg-[#0F172A] px-6 py-3 text-base font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                Ir al inicio
            </a>
        @else
            <a href="/login"
               class="inline-flex items-center justify-center rounded-md bg-[#0F172A] px-6 py-3 text-base font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                Iniciar sesión
            </a>
        @endauth
    </div>
@endsection
