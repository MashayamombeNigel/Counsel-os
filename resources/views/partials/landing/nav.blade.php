<nav class="fixed top-0 w-full z-50 bg-[#0b1220]/55 backdrop-blur-md border-b border-white/10 transition-colors" x-data="{ mobileOpen: false }" aria-label="Primary">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 md:px-12 flex items-center justify-between h-16 md:h-20">
        <a href="{{ url('/') }}" class="flex items-center select-none min-w-0">
            <img src="{{ asset('images/logo/lockup-white.png') }}" alt="CounselOS" class="h-8 md:h-9 w-auto object-contain flex-shrink-0">
        </a>

        {{-- Desktop nav --}}
        <div class="hidden md:flex items-center gap-9">
            <a href="#how-it-works" class="text-white/65 hover:text-white transition-colors font-body text-sm font-medium">How it works</a>
            <a href="#features" class="text-white/65 hover:text-white transition-colors font-body text-sm font-medium">Features</a>
            <a href="#tech" class="text-white/65 hover:text-white transition-colors font-body text-sm font-medium">Tech Stack</a>
        </div>

        {{-- Auth actions --}}
        <div class="hidden md:flex items-center gap-3">
            @auth
                <a href="{{ url('/dashboard') }}"
                   class="inline-flex items-center gap-2 bg-white text-[#0b1220] font-body text-sm font-semibold px-5 py-2.5 hover:bg-white/90 transition-colors">
                    <span>Go to Dashboard</span>
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_forward</span>
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="text-white/75 font-body text-sm font-medium hover:text-white px-3 py-2 transition-colors">
                    Sign in
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-white text-[#0b1220] font-body text-sm font-semibold px-5 py-2.5 hover:bg-white/90 transition-colors">
                    Try Live Demo
                </a>
            @endauth
        </div>

        {{-- Mobile menu toggle --}}
        <button @click="mobileOpen = !mobileOpen"
                class="md:hidden w-11 h-11 flex items-center justify-center text-white flex-shrink-0"
                aria-label="Toggle menu"
                :aria-expanded="mobileOpen.toString()">
            <span class="material-symbols-outlined text-3xl" x-text="mobileOpen ? 'close' : 'menu'"></span>
        </button>
    </div>

    {{-- Mobile menu panel --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden border-t border-white/10 bg-[#0b1220]/95 backdrop-blur-md px-6 py-5 flex flex-col gap-1">
        <a @click="mobileOpen = false" href="#how-it-works" class="text-white/85 py-3 text-base font-medium border-b border-white/10">How it works</a>
        <a @click="mobileOpen = false" href="#features" class="text-white/85 py-3 text-base font-medium border-b border-white/10">Features</a>
        <a @click="mobileOpen = false" href="#tech" class="text-white/85 py-3 text-base font-medium border-b border-white/10">Tech Stack</a>

        <div class="pt-4 flex flex-col gap-3">
            @auth
                <a href="{{ url('/dashboard') }}" class="bg-white text-[#0b1220] text-center font-semibold text-base px-5 py-3.5">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="text-white/80 text-center font-medium text-base py-3">
                    Sign in
                </a>
                <a href="{{ route('login') }}" class="bg-white text-[#0b1220] text-center font-semibold text-base px-5 py-3.5">
                    Try Live Demo
                </a>
            @endauth
        </div>
    </div>
</nav>
