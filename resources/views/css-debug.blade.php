<!DOCTYPE html>
<html>
<head>
    <title>CSS Debug - Railway</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .debug-section { background: white; margin: 10px 0; padding: 15px; border-radius: 5px; border-left: 4px solid #007cba; }
        .error { border-left-color: #dc3545; }
        .success { border-left-color: #28a745; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: 'Courier New', monospace; }
        .asset-test { margin: 10px 0; padding: 10px; background: #e9ecef; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 CSS Debug Information</h1>
    
    <div class="debug-section">
        <h3>Environment Variables</h3>
        <div class="code">
            APP_ENV: {{ config('app.env') }}<br>
            APP_URL: {{ config('app.url') }}<br>
            Asset URL: {{ config('app.asset_url') }}<br>
            Vite Hot Reload: {{ app()->environment('local') ? 'YES' : 'NO' }}
        </div>
    </div>

    <div class="debug-section">
        <h3>Vite Manifest Check</h3>
        <div class="code">
            @php
                $manifestPath = public_path('build/manifest.json');
                $manifestExists = file_exists($manifestPath);
            @endphp
            Manifest exists: {{ $manifestExists ? 'YES' : 'NO' }}<br>
            @if($manifestExists)
                @php $manifest = json_decode(file_get_contents($manifestPath), true); @endphp
                Manifest entries: {{ count($manifest) }}<br>
                CSS entry: {{ isset($manifest['resources/css/app.css']) ? 'YES' : 'NO' }}<br>
                @if(isset($manifest['resources/css/app.css']))
                    CSS file: {{ $manifest['resources/css/app.css']['file'] }}
                @endif
            @endif
        </div>
    </div>

    <div class="debug-section">
        <h3>Asset File Check</h3>
        @php
            $buildPath = public_path('build');
            $assetsPath = public_path('build/assets');
        @endphp
        <div class="code">
            Build directory exists: {{ is_dir($buildPath) ? 'YES' : 'NO' }}<br>
            Assets directory exists: {{ is_dir($assetsPath) ? 'YES' : 'NO' }}<br>
            @if(is_dir($assetsPath))
                Asset files: 
                @php $files = scandir($assetsPath); @endphp
                @foreach($files as $file)
                    @if($file !== '.' && $file !== '..')
                        <br>&nbsp;&nbsp;{{ $file }} ({{ number_format(filesize($assetsPath.'/'.$file)) }} bytes)
                    @endif
                @endforeach
            @endif
        </div>
    </div>

    <div class="debug-section">
        <h3>Generated URLs Test</h3>
        <div class="code">
            @php
                if($manifestExists && isset($manifest['resources/css/app.css'])) {
                    $cssFile = $manifest['resources/css/app.css']['file'];
                    $jsFile = $manifest['resources/js/app.js']['file'];
                    
                    $assetUrl = asset('build/' . $cssFile);
                    $secureAssetUrl = secure_asset('build/' . $cssFile);
                    $directUrl = config('app.url') . '/build/' . $cssFile;
                }
            @endphp
            @if(isset($cssFile))
                asset() URL: {{ $assetUrl }}<br>
                secure_asset() URL: {{ $secureAssetUrl }}<br>
                Direct URL: {{ $directUrl }}<br>
                config('app.url'): {{ config('app.url') }}<br>
                Request scheme: {{ request()->getScheme() }}<br>
                Request host: {{ request()->getHost() }}
            @endif
        </div>
    </div>

    <div class="debug-section">
        <h3>Direct Asset Tests</h3>
        @if($manifestExists && isset($manifest['resources/css/app.css']))
            @php 
                $cssFile = $manifest['resources/css/app.css']['file'];
                $cssPath = public_path('build/' . $cssFile);
                $cssUrl = asset('build/' . $cssFile);
            @endphp
            <div class="asset-test">
                <strong>CSS File Test:</strong><br>
                Path: {{ $cssPath }}<br>
                Exists: {{ file_exists($cssPath) ? 'YES' : 'NO' }}<br>
                URL: <a href="{{ $cssUrl }}" target="_blank">{{ $cssUrl }}</a><br>
                <button onclick="testCssLoad('{{ $cssUrl }}')">Test Load</button>
                <span id="css-result"></span>
            </div>
        @endif
    </div>

    <div class="debug-section">
        <h3>Live Tailwind Test</h3>
        <div style="padding: 10px; background: #007cba; color: white;" class="bg-blue-500 text-white p-4 rounded">
            This should be blue with white text if Tailwind is working
        </div>
        <div class="mt-4">
            <div class="bg-green-500 text-white p-2 rounded" style="margin-top: 10px;">Green test</div>
            <div class="bg-red-500 text-white p-2 rounded" style="margin-top: 10px;">Red test</div>
        </div>
    </div>

    <!-- Include the actual Vite directive for comparison -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        function testCssLoad(url) {
            const result = document.getElementById('css-result');
            result.innerHTML = ' Testing...';
            
            fetch(url)
                .then(response => {
                    if (response.ok) {
                        result.innerHTML = ' ✅ SUCCESS';
                        result.style.color = 'green';
                    } else {
                        result.innerHTML = ` ❌ FAILED (${response.status})`;
                        result.style.color = 'red';
                    }
                })
                .catch(error => {
                    result.innerHTML = ` ❌ ERROR: ${error.message}`;
                    result.style.color = 'red';
                });
        }
        
        // Auto-test Tailwind loading
        setTimeout(() => {
            const testEl = document.createElement('div');
            testEl.className = 'bg-blue-500';
            testEl.style.position = 'absolute';
            testEl.style.visibility = 'hidden';
            document.body.appendChild(testEl);
            
            const bgColor = window.getComputedStyle(testEl).backgroundColor;
            console.log('Tailwind test - bg-blue-500 color:', bgColor);
            
            document.body.removeChild(testEl);
        }, 1000);
    </script>
</body>
</html>
