<section class="bg-white border-y border-[#e2e8f0]">
    <div class="max-w-[1200px] mx-auto px-6 py-20">
        <h2 class="font-headline text-2xl md:text-3xl font-bold text-[#191c1e] text-center mb-2">Built with real engineering discipline</h2>
        <p class="font-body text-base text-[#45464d] text-center mb-12 max-w-xl mx-auto">
            This is a portfolio project, not a company — so instead of client testimonials, here's what's actually verifiable.
        </p>

        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4 text-center">
            @php
                $trustPoints = [
                    ['icon' => 'verified', 'label' => 'Automated test suite', 'desc' => 'Pest, CI on every push'],
                    ['icon' => 'dns', 'label' => 'Postgres in CI', 'desc' => 'Not SQLite-masked bugs'],
                    ['icon' => 'code', 'label' => 'Open source', 'desc' => 'Full commit history on GitHub'],
                    ['icon' => 'gavel', 'label' => 'AI safety disclaimer', 'desc' => 'On every AI output, no exceptions'],
                ];
            @endphp
            @foreach ($trustPoints as $point)
                <div class="p-5">
                    <span class="material-symbols-outlined text-[#6d28d9] text-3xl mb-2 block">{{ $point['icon'] }}</span>
                    <div class="font-headline font-semibold text-base text-[#191c1e]">{{ $point['label'] }}</div>
                    <div class="font-body text-sm text-[#45464d]">{{ $point['desc'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
