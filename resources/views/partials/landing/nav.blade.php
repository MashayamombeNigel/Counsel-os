<nav class="fixed top-0 w-full z-50 bg-[#0b1220]/80 backdrop-blur-xl border-b border-white/10 transition-all" x-data="{ mobileOpen: false }">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 md:px-12 flex items-center justify-between h-16 md:h-20">
        <a href="{{ url('/') }}" class="flex items-center gap-3 select-none min-w-0">
            <img src="{{ asset('images/logo/lockup-white.png') }}" alt="CounselOS" class="h-8 md:h-9 w-auto object-contain flex-shrink-0">
            <span class="hidden lg:inline-block bg-[#a78bfa]/15 text-[#c4b5fd] text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wider flex-shrink-0">
                AI-Native Legal
            </span>
        </a>

        {{-- Desktop nav --}}
        <div class="hidden md:flex items-center gap-8">
            <a href="#how-it-works" class="text-white/70 hover:text-white transition-colors font-body text-sm font-medium">How it works</a>
            <a href="#features" class="text-white/70 hover:text-white transition-colors font-body text-sm font-medium">Features</a>
            <a href="#tech" class="text-white/70 hover:text-white transition-colors font-body text-sm font-medium">Tech Stack</a>
        </div>

        {{-- auth actions --}}
        <div class="hidden md:flex items-center gap-3">
            @auth
                <a href="{{ url('/dashboard') }}" class="bg-[#8b5cf6] text-white font-body text-sm font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-[#7c3aed] transition-all flex items-center gap-2">
                    <span>Go to Dashboard</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="text-white/80 font-body text-sm font-semibold hover:text-white px-4 py-2 rounded-lg transition-all duration-200">
                    Sign in
                </a>
                <a href="{{ route('login') }}" class="bg-[#8b5cf6] text-white font-body text-sm font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-[#7c3aed] active:scale-95 transition-all flex items-center gap-2">
                    <span>Try Live Demo</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            @endauth
        </div>

        {{-- Mobile menu toggle --}}
        <button @click="mobileOpen = !mobileOpen" class="md:hidden w-11 h-11 flex items-center justify-center text-white flex-shrink-0" aria-label="Toggle menu" :aria-expanded="mobileOpen">
            <span class="material-symbols-outlined text-3xl" x-text="mobileOpen ? 'close' : 'menu'"></span>
        </button>
    </div>

    {{-- Mobile menu panel --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden border-t border-white/10 bg-[#0b1220]/95 backdrop-blur-xl px-6 py-5 flex flex-col gap-1">
        <a @click="mobileOpen = false" href="#how-it-works" class="text-white/80 py-3 text-base font-medium border-b border-white/5">How it works</a>
        <a @click="mobileOpen = false" href="#features" class="text-white/80 py-3 text-base font-medium border-b border-white/5">Features</a>
        <a @click="mobileOpen = false" href="#tech" class="text-white/80 py-3 text-base font-medium border-b border-white/5">Tech Stack</a>

        <div class="pt-4 flex flex-col gap-3">
            @auth
                <a href="{{ url('/dashboard') }}" class="bg-[#8b5cf6] text-white text-center font-semibold text-base px-5 py-3.5 rounded-lg">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="text-white/80 text-center font-semibold text-base py-3">
                    Sign in
                </a>
                <a href="{{ route('login') }}" class="bg-[#8b5cf6] text-white text-center font-semibold text-base px-5 py-3.5 rounded-lg">
                    Try Live Demo
                </a>
            @endauth
        </div>
    </div>
</nav>
