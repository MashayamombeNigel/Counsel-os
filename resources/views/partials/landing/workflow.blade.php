<section id="how-it-works" class="py-20 px-6 md:px-12 max-w-[1440px] mx-auto">
    <div class="mb-12">
        <span class="font-body text-xs font-bold text-[#6d28d9] uppercase tracking-widest">WORKFLOW</span>
        <h2 class="font-headline text-3xl md:text-4xl font-bold text-[#191c1e] mt-2 tracking-tight">Precision at every step</h2>
    </div>

    <div id="features" class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
        @php
            $steps = [
                ['icon' => 'folder_open', 'title' => '1. Create Matter', 'desc' => 'Organize documents by client and matter for a clean, focused workspace.'],
                ['icon' => 'upload_file', 'title' => '2. Upload', 'desc' => 'Upload PDFs or Word docs directly into the matter.'],
                ['icon' => 'psychology', 'title' => '3. Get Insights', 'desc' => 'AI extracts structured data, identifies risks, and flags key dates automatically.'],
                ['icon' => 'task_alt', 'title' => '4. Act', 'desc' => 'Convert a flagged deadline into a task, or ask a grounded research question.'],
            ];
        @endphp

        @foreach ($steps as $step)
            <div @click="isModalOpen = true" class="group cursor-pointer p-6 rounded-lg bg-white border border-[#c6c6cd]/50 hover:border-[#6d28d9] hover-lift transition-all">
                <div class="w-12 h-12 rounded bg-[#131b2e] text-white flex items-center justify-center mb-6 transition-colors group-hover:bg-[#6d28d9]">
                    <span class="material-symbols-outlined text-2xl">{{ $step['icon'] }}</span>
                </div>
                <h3 class="font-headline font-semibold text-lg text-[#191c1e] mb-2 group-hover:text-[#6d28d9] transition-colors">{{ $step['title'] }}</h3>
                <p class="font-body text-sm text-[#45464d] leading-relaxed">{{ $step['desc'] }}</p>
            </div>
        @endforeach
    </div>
</section>
