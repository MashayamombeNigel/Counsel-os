<footer class="bg-[#e0e3e5] border-t border-[#c6c6cd] py-10 px-6 md:px-12">
    <div class="max-w-[1440px] mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo/lockup-navy.png') }}" alt="CounselOS" class="h-8 w-auto object-contain">
        </div>
        <div class="flex items-center gap-6 font-body text-sm text-[#45464d]">
            <a href="https://github.com/YOUR_USERNAME/counselos" class="hover:text-[#6d28d9] transition-colors">GitHub</a>
            <a href="{{ route('login') }}" class="hover:text-[#6d28d9] transition-colors">Sign in</a>
        </div>
    </div>
    <p class="max-w-[1440px] mx-auto font-body text-xs text-[#76859b] mt-6">
        CounselOS is a portfolio project — a workflow and document review assistant. AI-generated insights are for professional review support only and do not constitute legal advice.
    </p>
</footer>
