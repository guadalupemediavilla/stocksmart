<nav class="relative border-b border-[#E2E8F0] bg-[#0F172A]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10 xl:px-16">
        <div class="grid h-16 grid-cols-2 items-center sm:grid-cols-3 lg:h-20">
            <a href="/home" class="text-lg font-bold tracking-tight text-white sm:text-xl lg:text-2xl xl:text-3xl">
                StockSmart
            </a>

            @auth
                <div class="hidden justify-center sm:flex">
                    <span class="text-sm font-semibold uppercase tracking-wide text-white">
                        {{ Auth::user()->nombre }}
                        @if (Auth::user()->rol === 'admin')
                            <span class="ml-1 rounded-full bg-white/10 px-2 py-0.5 text-xs font-medium normal-case text-white">Admin</span>
                        @endif
                    </span>
                </div>

                <div class="flex items-center justify-end gap-6">
                    <!-- Botón hamburguesa (mobile) -->
                    <label for="navbar-toggle" class="cursor-pointer p-2 text-white sm:hidden">
                        <span class="sr-only">Abrir menú</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </label>
                    <input type="checkbox" id="navbar-toggle" class="peer hidden">

                    <!-- Menú (desktop) -->
                    <div class="hidden items-center gap-6 sm:flex">
                        <a href="/home" title="Inicio" class="text-slate-200 transition-colors hover:text-[#0D9488]">
                            <span class="sr-only">Inicio</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75 12 3l9 6.75V21a.75.75 0 0 1-.75.75H15a.75.75 0 0 1-.75-.75v-5.25a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 21V9.75Z" />
                            </svg>
                        </a>

                        <a href="/perfil" class="text-sm font-medium text-slate-200 transition-colors hover:text-[#0D9488]">
                            Mi perfil
                        </a>

                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-slate-200 transition-colors hover:text-[#0D9488]">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>

        @auth
            <!-- Menú (mobile, desplegable) -->
            <div class="absolute inset-x-0 top-16 hidden flex-col gap-4 border-b border-white/10 bg-[#0F172A] px-4 py-4 peer-checked:flex sm:hidden">
                <span class="text-sm font-semibold uppercase tracking-wide text-slate-300">
                    {{ Auth::user()->nombre }}
                    @if (Auth::user()->rol === 'admin')
                        <span class="ml-1 rounded-full bg-white/10 px-2 py-0.5 text-xs font-medium normal-case text-white">Admin</span>
                    @endif
                </span>

                <a href="/home" class="text-sm font-medium text-slate-200 transition-colors hover:text-[#0D9488]">
                    Inicio
                </a>

                <a href="/perfil" class="text-sm font-medium text-slate-200 transition-colors hover:text-[#0D9488]">
                    Mi perfil
                </a>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-slate-200 transition-colors hover:text-[#0D9488]">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        @endauth
    </div>
</nav>