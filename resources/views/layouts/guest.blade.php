
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EduVerse') }} - Your Learning Universe</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

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
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen relative overflow-hidden">
            <!-- Animated Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600"></div>
            <div class="absolute inset-0 bg-black opacity-20"></div>
            
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
            
            <!-- Floating Elements -->
            <div class="absolute top-20 left-10 w-32 h-32 bg-yellow-400 rounded-full opacity-10 animate-float"></div>
            <div class="absolute top-40 right-20 w-24 h-24 bg-pink-400 rounded-full opacity-15 animate-pulse"></div>
            <div class="absolute bottom-20 left-20 w-20 h-20 bg-green-400 rounded-full opacity-20 animate-bounce"></div>
            <div class="absolute bottom-40 right-40 w-16 h-16 bg-blue-400 rounded-full opacity-25 animate-ping"></div>
            
            <div class="relative min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8">
                <!-- Logo/Brand -->
                <div class="mb-8">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/30 group-hover:scale-110 transition-transform shadow-2xl">
                            <span class="text-white font-black text-2xl">E</span>
                        </div>
                        <div class="text-center">
                            <div class="font-black text-3xl text-white">
                                EduVerse
                            </div>
                            <div class="text-blue-100 text-sm font-medium">
                                Your Learning Universe
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Auth Form Container -->
                <div class="w-full max-w-md">
                    <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-md shadow-2xl rounded-3xl border border-white/20 dark:border-gray-700/50 overflow-hidden">
                        <div class="px-8 py-10">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
                
                <!-- Trust Indicators -->
                <div class="mt-8 flex flex-wrap justify-center items-center gap-6 text-white/80 text-sm">
                    <div class="flex items-center space-x-2">
                        <span class="text-green-400">✓</span>
                        <span>Secure & Encrypted</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-yellow-400">⭐</span>
                        <span>Trusted by 10k+ Students</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-blue-400">🎓</span>
                        <span>Expert Instructors</span>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
