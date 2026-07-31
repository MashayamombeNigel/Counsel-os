<section class="py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        @php
            $detailFeatures = [
                ['icon' => 'support_agent', 'title' => 'Research Assistant', 'desc' => 'Ask questions about your matter and get answers grounded in your own uploaded documents - not general knowledge.'],
                ['icon' => 'checklist', 'title' => 'Task Conversion', 'desc' => 'Turn an AI-flagged deadline into a trackable task in one click, with the source document linked.'],
                ['icon' => 'timeline', 'title' => 'Activity Timeline', 'desc' => 'A complete audit trail of every upload, status change, and AI action on a matter.'],
            ];
        @endphp
        @foreach ($detailFeatures as $feature)
            <div class="bg-white rounded-xl border border-[#e2e8f0] p-6 hover-lift">
                <span class="material-symbols-outlined text-[#6d28d9] text-2xl mb-4 block">{{ $feature['icon'] }}</span>
                <h3 class="font-headline font-bold text-lg text-[#191c1e] mb-2">{{ $feature['title'] }}</h3>
                <p class="font-body text-sm text-[#45464d] leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-[#e2e8f0] p-6 md:p-8 grid md:grid-cols-[1fr_auto] gap-6 items-center">
        <div>
            <span class="material-symbols-outlined text-[#6d28d9] text-2xl mb-4 block">visibility</span>
            <h3 class="font-headline font-bold text-lg text-[#191c1e] mb-2">Operational Visibility</h3>
            <p class="font-body text-sm text-[#45464d] leading-relaxed max-w-md">
                An internal admin panel gives a bird's-eye view of every matter's workload, with failed processing jobs surfaced and retryable in one click - nothing silently stalls.
            </p>
        </div>

        {{-- Honest callout in place of a fabricated usage statistic -
             this is a portfolio project with a demo dataset, not a
             product with real customer usage data to report. --}}
        <div class="bg-[#f5f3ff] rounded-lg px-6 py-5 border border-[#ddd6fe] min-w-[260px]">
            <p class="font-body text-[10px] font-bold text-[#6d28d9] uppercase tracking-wider mb-2">What's actually verifiable</p>
            <ul class="space-y-1.5 font-body text-sm text-[#45464d]">
                <li class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#6d28d9] text-base">check</span>
                    Full source on GitHub
                </li>
                <li class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#6d28d9] text-base">check</span>
                    Tested in CI on every push
                </li>
                <li class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#6d28d9] text-base">check</span>
                    Live demo, no gatekeeping
                </li>
            </ul>
        </div>
    </div>
</section>
