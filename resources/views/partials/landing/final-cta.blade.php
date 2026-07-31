<section class="bg-[#0b1220] py-24 px-6 md:px-12">
    <div class="max-w-[900px] mx-auto text-center">
        <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
            See it work on a real document.
        </h2>
        <p class="font-body text-base md:text-lg text-white/60 mb-10 max-w-lg mx-auto">
            No signup wall, no credit card. Log into the live demo and the matter workspace is already populated with an analyzed lease, a converted task, and a saved research answer.
        </p>

        <div class="mb-8">
            <p class="font-body text-[10px] font-bold text-white/50 uppercase tracking-widest mb-3">Public Demo Credentials</p>
            <div class="bg-white/5 border border-white/10 rounded-xl px-6 py-4 inline-flex flex-col sm:flex-row items-center gap-2 sm:gap-6 max-w-full">
                <span class="flex items-center gap-2 text-[#c4b5fd] font-mono text-sm break-all">
                    <span class="material-symbols-outlined text-[18px] flex-shrink-0">mail</span>
                    demo@counselos.test
                </span>
                <span class="hidden sm:block w-px h-5 bg-white/20"></span>
                <span class="flex items-center gap-2 text-[#c4b5fd] font-mono text-sm">
                    <span class="material-symbols-outlined text-[18px] flex-shrink-0">lock</span>
                    password
                </span>
            </div>
        </div>

        <div>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-[#ede9fe] text-[#4c1d95] px-8 py-4 rounded-lg font-display font-bold text-base hover:bg-white transition-all shadow-lg">
                Try the Live Demo Now
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
    </div>
</section>
