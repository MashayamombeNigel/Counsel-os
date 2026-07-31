<header class="pt-36 pb-16 px-6 md:px-12 max-w-[1440px] mx-auto overflow-hidden">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="z-10">
            <div class="inline-flex items-center gap-2 bg-[#6d28d9]/10 text-[#6d28d9] px-3 py-1.5 rounded-full mb-6 border border-[#6d28d9]/20 max-w-full">
                <span class="material-symbols-outlined text-[16px] flex-shrink-0">verified_user</span>
                <span class="font-body text-[11px] sm:text-xs font-semibold uppercase tracking-wider">AI-Native Document Intelligence</span>
            </div>

            <h1 class="font-display text-3xl sm:text-4xl lg:text-6xl font-extrabold text-[#191c1e] mb-6 leading-[1.15] tracking-tight">
                Turn every legal document into
                <span class="text-[#6d28d9] underline decoration-[#ddd6fe] decoration-wavy underline-offset-4">
                    structured intelligence.
                </span>
            </h1>

            <p class="font-body text-lg text-[#45464d] mb-8 max-w-xl leading-relaxed">
                Extract risks, obligations, and deadlines automatically. CounselOS processes complex legal language into actionable data, saving small legal teams hours on manual review.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                <button @click="isModalOpen = true" class="bg-[#6d28d9] text-white px-8 py-4 rounded font-semibold text-base hover:bg-[#5b21b6] transition-all shadow-md hover:shadow-lg active:scale-95 flex items-center gap-2">
                    <span>See a Real Example</span>
                    <span class="material-symbols-outlined">rocket_launch</span>
                </button>
                <a href="#how-it-works" class="border border-[#131b2e] text-[#131b2e] px-8 py-4 rounded font-semibold text-base hover:bg-[#eceef0] transition-all flex items-center gap-2">
                    <span>See how it works</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_downward</span>
                </a>
            </div>

            {{-- Honest, verifiable claims only - no compliance
                 certifications that don't exist, no OCR claim (this
                 product explicitly doesn't support OCR, by design). --}}
            <div class="mt-8 flex flex-wrap items-center gap-6 text-xs text-[#76777d]">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[#6d28d9] text-base">check_circle</span>
                    Open source on GitHub
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[#6d28d9] text-base">check_circle</span>
                    Tested with CI on every push
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[#6d28d9] text-base">check_circle</span>
                    Try it now - no signup wall
                </span>
            </div>
        </div>

        {{-- Illustrative preview card - built from real UI components/
             copy patterns, labeled honestly, clicking it opens the
             modal which itself links through to the real product. --}}
        <div class="relative mt-4 lg:mt-0">
            <div @click="isModalOpen = true" class="glass-card rounded-xl p-5 shadow-2xl rotate-1 hover:rotate-0 lg:translate-x-4 transition-all duration-300 cursor-pointer group hover:border-[#6d28d9]/40 relative z-20">
                <div class="absolute top-3 right-14 bg-[#131b2e] text-white text-[10px] font-semibold px-2.5 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                    Click to see a full example
                </div>

                <div class="flex items-center justify-between border-b border-[#c6c6cd]/50 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#6d28d9] text-2xl">description</span>
                        <span class="font-body font-semibold text-lg text-[#191c1e]">Sample_Commercial_Lease.pdf</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-[#ba1a1a]"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-[#6d28d9]"></div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="ai-accent-border bg-[#6d28d9]/5 p-4 rounded-r-md border-y border-r border-[#6d28d9]/20">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="material-symbols-outlined text-[#6d28d9] text-[18px]">auto_awesome</span>
                            <span class="font-body text-xs font-bold text-[#6d28d9] uppercase tracking-wider">AI INSIGHT: TERMINATION RISK</span>
                        </div>
                        <p class="font-body text-sm text-[#191c1e] leading-snug">
                            The lease does not specify a valid delivery method for termination notice - a real ambiguity that could create a dispute.
                        </p>
                    </div>

                    <div class="border border-[#c6c6cd] rounded-md p-4 bg-white/60">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-body text-xs font-semibold text-[#45464d] uppercase tracking-wider">DEADLINE DETECTED</span>
                            <span class="bg-[#ffdad6] text-[#93000a] px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider">HIGH PRIORITY</span>
                        </div>
                        <p class="font-display font-bold text-xl text-[#191c1e] mb-0.5">Dec 1, 2026</p>
                        <p class="font-body text-xs text-[#45464d]">Renewal notice deadline, 90 days before lease expiration.</p>
                    </div>
                </div>
            </div>

            <div class="absolute -top-10 -right-10 w-72 h-72 bg-[#6d28d9]/15 rounded-full blur-3xl -z-10 pointer-events-none"></div>
        </div>
    </div>
</header>
