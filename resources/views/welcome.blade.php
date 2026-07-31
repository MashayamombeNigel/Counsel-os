<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CounselOS | Precision Legal Intelligence</title>
    <meta name="description" content="CounselOS turns uploaded legal documents into structured, searchable insights — summaries, risks, obligations, and deadlines — inside a matter-scoped workspace built for small legal teams.">

    @include('partials.head-fonts')
    @include('partials.landing.landing-fonts')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .ai-accent-border { border-left: 4px solid #6d28d9; }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border: 1px solid #E2E8F0;
        }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0px 4px 20px rgba(15, 23, 42, 0.05);
        }
    </style>
</head>
<body class="bg-[#f7f9fb] text-[#191c1e] antialiased font-body selection:bg-[#ddd6fe]" x-data="{ isModalOpen: false }">

    @include('partials.landing.nav')
    @include('partials.landing.hero')
    @include('partials.landing.workflow')
    @include('partials.landing.features-detail')
    @include('partials.landing.engineering-discipline')
    @include('partials.landing.tech')
    @include('partials.landing.final-cta')
    @include('partials.landing.footer')
    @include('partials.landing.document-modal')

</body>
</html>