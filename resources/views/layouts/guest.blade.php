<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CounselOS') }}</title>

        @include('partials.head-fonts')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-on-surface antialiased bg-background">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-6">
                <a href="/">
                    <img src="{{ asset('images/logo/lockup-navy.png') }}" alt="CounselOS" class="h-14 w-auto object-contain">
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-surface-container-lowest shadow-md overflow-hidden sm:rounded-2xl border border-outline-variant">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
