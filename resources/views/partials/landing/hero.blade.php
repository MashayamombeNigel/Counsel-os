<section class="relative min-h-[100svh] flex items-end sm:items-center overflow-hidden bg-[#0b1220]" aria-labelledby="hero-heading">
    {{-- Full-bleed background video --}}
    <div class="absolute inset-0" aria-hidden="true">
        <video
            class="h-full w-full object-cover"
            autoplay
            muted
            loop
            playsinline
            preload="metadata"
            poster="{{ asset('images/hero/counselos-hero.jpg') }}"
        >
            <source src="{{ asset('videos/hero/hero.mp4') }}" type="video/mp4">
        </video>
        {{-- Clean premium overlay: soft vignette for readability, not glass/neon --}}
        <div class="absolute inset-0 bg-[#0b1220]/55"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0b1220]/80 via-[#0b1220]/45 to-[#0b1220]/20"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220]/70 via-transparent to-[#0b1220]/35"></div>
    </div>

    <div class="relative z-10 w-full max-w-[1440px] mx-auto px-6 sm:px-8 md:px-12 pt-28 pb-16 md:pb-24">
        <div class="max-w-xl">
            <p class="font-body text-[11px] sm:text-xs font-medium uppercase tracking-[0.18em] text-white/55 mb-6">
                Legal operating system
            </p>

            <h1 id="hero-heading" class="font-serif text-[2.5rem] sm:text-5xl xl:text-[3.5rem] text-white leading-[1.08] tracking-[-0.02em]">
                Precision for every matter.
            </h1>

            <p class="font-body text-base sm:text-lg text-white/70 mt-6 mb-10 max-w-[36ch] leading-relaxed">
                Structured insights from every document—risks, obligations, and deadlines, organized by matter.
            </p>

            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <button
                    type="button"
                    @click="isModalOpen = true"
                    class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white text-[#0b1220] font-body text-sm font-semibold tracking-wide hover:bg-white/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-colors"
                >
                    See a Real Example
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_forward</span>
                </button>
                <a
                    href="#how-it-works"
                    class="inline-flex items-center justify-center gap-2 px-7 py-3.5 border border-white/35 text-white font-body text-sm font-medium tracking-wide hover:border-white/70 hover:bg-white/5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-colors"
                >
                    See how it works
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_downward</span>
                </a>
            </div>

            <div class="mt-14 pt-8 border-t border-white/15">
                <p class="font-body text-[11px] uppercase tracking-[0.16em] text-white/45 mb-4">
                    Built for small legal teams
                </p>
                <ul class="flex flex-wrap gap-x-8 gap-y-3 font-body text-sm text-white/60">
                    <li class="flex items-center gap-2">
                        <span class="block w-1 h-1 rounded-full bg-white/50" aria-hidden="true"></span>
                        Open source on GitHub
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="block w-1 h-1 rounded-full bg-white/50" aria-hidden="true"></span>
                        CI on every push
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="block w-1 h-1 rounded-full bg-white/50" aria-hidden="true"></span>
                        Live demo, no signup wall
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
