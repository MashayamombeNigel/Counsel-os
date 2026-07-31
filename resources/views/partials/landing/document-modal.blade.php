<div x-show="isModalOpen" style="display: none;" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div @click.outside="isModalOpen = false" class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-[#c6c6cd]">
        <div class="bg-[#131b2e] text-white p-5 flex items-center justify-between border-b border-white/10">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[#ddd6fe] text-2xl">description</span>
                <div>
                    <h3 class="font-headline font-bold text-lg leading-tight">Sample_Commercial_Lease.pdf</h3>
                    <p class="font-body text-xs text-[#7c839b]">Commercial Lease · Analyzed by Gemini</p>
                </div>
            </div>
            <button @click="isModalOpen = false" class="text-[#7c839b] hover:text-white transition-colors" aria-label="Close">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-4 flex-1 bg-[#f7f9fb]">
            <div class="ai-accent-border bg-[#6d28d9]/5 p-5 rounded-r-lg border-y border-r border-[#6d28d9]/20">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#6d28d9] text-xl">auto_awesome</span>
                        <span class="font-headline text-sm font-bold text-[#6d28d9] uppercase tracking-wider">AI INSIGHT: TERMINATION RISK</span>
                    </div>
                    <span class="bg-[#ffdad6] text-[#93000a] text-[10px] font-bold px-2.5 py-0.5 rounded uppercase">HIGH</span>
                </div>
                <p class="font-headline font-semibold text-base text-[#191c1e] mb-1">Termination notice ambiguity</p>
                <p class="font-body text-sm text-[#45464d] leading-relaxed">The lease does not specify a valid delivery method for termination notice, which could create a dispute over whether notice was properly given.</p>
            </div>

            <div class="border border-[#c6c6cd] rounded-md p-4 bg-white">
                <div class="flex justify-between items-center mb-1">
                    <span class="font-body text-xs font-semibold text-[#45464d] uppercase tracking-wider">Deadline detected</span>
                    <span class="bg-[#ffdad6] text-[#93000a] px-2 py-0.5 rounded text-[10px] font-extrabold uppercase">High priority</span>
                </div>
                <p class="font-display font-bold text-xl text-[#191c1e] mb-0.5">Dec 1, 2026</p>
                <p class="font-body text-xs text-[#45464d]">Renewal notice deadline — 90 days before lease expiration.</p>
            </div>

            <p class="font-body text-xs text-[#76859b] italic">
                This is a real example from the app's demo dataset, not a mockup — log in below to see the full matter, including the research assistant and task conversion.
            </p>
        </div>

        <div class="bg-[#eceef0] p-4 border-t border-[#c6c6cd] flex justify-between items-center">
            <span class="font-body text-xs text-[#45464d]">From the CounselOS demo dataset</span>
            <a href="{{ route('login') }}" class="bg-[#6d28d9] text-white px-5 py-2 rounded text-xs font-semibold hover:bg-[#5b21b6] transition-colors flex items-center gap-1.5">
                Sign in and try it yourself
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>
</div>
