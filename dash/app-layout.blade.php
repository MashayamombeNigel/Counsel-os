<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'CounselOS') }}</title>

    @include('partials.head-fonts') {{-- see head_fonts_snippet.blade.php - place at resources/views/partials/head-fonts.blade.php --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background font-sans text-on-surface min-h-screen">

    {{-- Top nav --}}
    <nav class="bg-surface-container-lowest flex justify-between items-center w-full px-margin-x h-16 sticky top-0 z-50 shadow-sm border-b border-surface-variant">
        <div class="flex items-center gap-stack-md">
            <span class="text-headline-md font-bold text-secondary">CounselOS</span>
            <div class="hidden md:flex ml-10 items-center gap-6">
                <a href="{{ route('dashboard') }}"
                   class="text-body-md h-16 flex items-center {{ request()->routeIs('dashboard') ? 'text-secondary border-b-2 border-secondary' : 'text-on-surface-variant hover:bg-surface-container-low transition-colors duration-200 px-2' }}">
                    Dashboard
                </a>
                <a href="{{ route('matters.index') }}"
                   class="text-body-md h-16 flex items-center {{ request()->routeIs('matters.*') ? 'text-secondary border-b-2 border-secondary' : 'text-on-surface-variant hover:bg-surface-container-low transition-colors duration-200 px-2' }}">
                    Matters
                </a>
            </div>
        </div>
        <div class="flex items-center gap-stack-md">
            <form method="GET" action="{{ route('search') }}" class="relative hidden lg:block mr-4">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input name="q" class="bg-surface-container-low border-none rounded-full pl-10 pr-4 py-2 text-sm w-64 focus:ring-2 focus:ring-secondary/20"
                       placeholder="Search matters, docs..." type="text">
            </form>
            <div class="w-8 h-8 rounded-full overflow-hidden ml-2 border border-outline-variant bg-secondary-container flex items-center justify-center text-white text-xs font-bold">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
        </div>
    </nav>

    {{-- Sidebar --}}
    <aside class="fixed left-0 top-0 h-full flex-col pt-16 border-r border-surface-variant bg-surface w-64 hidden md:flex z-40">
        <div class="px-4 py-6 flex flex-col gap-2 overflow-y-auto flex-grow">
            <div class="flex items-center gap-3 mb-6 px-2">
                <div class="w-10 h-10 rounded-lg bg-secondary flex items-center justify-center text-white font-black text-xl">C</div>
                <div>
                    <div class="text-label-md font-semibold text-primary">CounselOS</div>
                    <div class="text-[10px] text-on-surface-variant uppercase tracking-wider">Matter Intelligence</div>
                </div>
            </div>

            <a href="{{ route('matters.create') }}"
               class="bg-secondary text-white font-semibold rounded-lg flex items-center gap-3 p-3 mb-6 shadow-sm hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined">add</span>
                <span class="text-label-md">New Matter</span>
            </a>

            @php
                $navItems = [
                    ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
                    ['label' => 'Clients', 'icon' => 'groups', 'route' => 'clients.index', 'active' => request()->routeIs('clients.*')],
                    ['label' => 'Matters', 'icon' => 'gavel', 'route' => 'matters.index', 'active' => request()->routeIs('matters.*')],
                ];
            @endphp

            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="rounded-lg flex items-center gap-stack-md p-stack-md mx-stack-sm mb-1 transition-transform duration-200 hover:translate-x-1
                          {{ $item['active'] ? 'bg-secondary-container text-on-secondary-container font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
                    <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                    <span class="text-label-md">{{ $item['label'] }}</span>
                </a>
            @endforeach

            {{--
                Documents and Tasks are intentionally left out of top-level
                nav for now - there's no standalone index route for either
                yet (both only exist nested inside a matter workspace).
                Add them here once those index views/routes exist, rather
                than linking to a page that doesn't exist.
            --}}
        </div>

        <div class="p-4 border-t border-surface-variant">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-on-surface-variant hover:bg-surface-container-high rounded-lg flex items-center gap-stack-md p-stack-md mx-stack-sm mb-1 transition-colors duration-200">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-label-md">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="md:ml-64 p-gutter max-w-container-max mx-auto">
        {{-- Backward-compatible header slot for pages that haven't been
             redesigned yet (Clients, Matters, etc still use the simple
             x-slot('header') pattern from before this shell change). --}}
        @isset($header)
            <header class="mb-stack-lg">
                {{ $header }}
            </header>
        @endisset

        @if (session('status'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg p-4">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg p-4">
                {{ session('error') }}
            </div>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
