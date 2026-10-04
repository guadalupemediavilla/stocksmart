@extends('layouts.app')

@section('title', 'Agregar empleado · StockSmart')

@section('content')
    <a href="/empleados" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver a empleados</a>

    <h1 class="mt-4 text-2xl font-bold tracking-tight text-[#0F172A]">Agregar empleado</h1>

    <div class="mt-6 max-w-2xl rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm">
        <form method="POST" action="/empleados" class="flex flex-col gap-5">
            @csrf

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-[#1E293B]">Nombre</label>
                    <input type="text" name="nombre" required
                           class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-2 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#1E293B]">Apellido</label>
                    <input type="text" name="apellido" required
                           class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-2 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Mail</label>
                <input type="email" name="mail" required
                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-2 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Contraseña</label>
                <div class="relative mt-1">
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-md border border-[#E2E8F0] py-2 pl-3 pr-11 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488] [&::-ms-reveal]:hidden">
                    <button type="button" onclick="togglePassword()" aria-label="Mostrar contraseña"
                            class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-3 text-slate-400 transition-colors hover:text-slate-600 focus:outline-none focus-visible:text-[#0D9488]">
                        <svg data-icon="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg data-icon="hide" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#1E293B]">Rol</label>
                <select name="rol" required
                        class="mt-1 w-full rounded-md border border-[#E2E8F0] bg-white px-3 py-2 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                    <option value="empleado">Empleado</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="mt-2 flex items-center gap-4">
                <button type="submit"
                        class="rounded-md bg-[#0F172A] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                    Guardar
                </button>
                <a href="/empleados" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const boton = event.currentTarget;
        const visible = input.type === 'password';

        input.type = visible ? 'text' : 'password';
        boton.querySelector('[data-icon="show"]').classList.toggle('hidden', visible);
        boton.querySelector('[data-icon="hide"]').classList.toggle('hidden', !visible);
        boton.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
    }
    </script>
@endsection
