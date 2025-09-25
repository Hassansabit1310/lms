<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if(app()->environment('production'))
            {{-- Production: Use manifest to load assets directly --}}
            @php
                $manifestPath = public_path('build/manifest.json');
                if (file_exists($manifestPath)) {
                    $manifest = json_decode(file_get_contents($manifestPath), true);
                    $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
                    $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
                }
            @endphp
            @if(isset($cssFile))
                <link rel="stylesheet" href="{{ secure_asset('build/' . $cssFile) }}">
            @endif
            @if(isset($jsFile))
                <script type="module" src="{{ secure_asset('build/' . $jsFile) }}"></script>
            @endif
        @else
            {{-- Development: Use Vite --}}
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        
        <!-- Styles Stack -->
        @stack('styles')
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-slate-100 to-zinc-100">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="border-b border-gray-200/60 shadow-sm">
                    {{ $header }}
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        
        <!-- Additional Scripts -->
        @stack('scripts')
    </body>
</html>
