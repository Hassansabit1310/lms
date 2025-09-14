<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $h5pContent->title ?? 'H5P Content' }} - {{ config('app.name', 'LMS') }}</title>

    <!-- H5P Core CSS and JS -->
    <link rel="stylesheet" href="https://h5p.org/sites/all/modules/h5p/library/styles/h5p.css">
    <link rel="stylesheet" href="https://h5p.org/sites/all/modules/h5p/library/styles/h5p-confirmation-dialog.css">
    <link rel="stylesheet" href="https://h5p.org/sites/all/modules/h5p/library/styles/h5p-core-button.css">
    
    <!-- Tailwind CSS for basic styling -->
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: #f8fafc;
        }
        
        .h5p-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .h5p-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .h5p-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .h5p-meta {
            opacity: 0.9;
            margin-top: 8px;
            font-size: 0.9rem;
        }
        
        .h5p-content {
            padding: 30px;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .h5p-placeholder {
            text-center;
            color: #6b7280;
        }
        
        .h5p-placeholder i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }
        
        .h5p-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            color: #dc2626;
        }
        
        .h5p-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 0.9rem;
            color: #1e40af;
        }
        
        .h5p-frame {
            width: 100%;
            border: none;
            border-radius: 8px;
            background: white;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="h5p-container">
        <!-- Header -->
        <div class="h5p-header">
            <h1 class="h5p-title">{{ $h5pContent->title ?? 'H5P Interactive Content' }}</h1>
            <div class="h5p-meta">
                @if($h5pContent->content_type)
                    <span>{{ $h5pContent->content_type }}</span>
                @endif
                @if($h5pContent->version)
                    <span>• Version {{ $h5pContent->version }}</span>
                @endif
                @if($h5pContent->file_size)
                    <span>• {{ $h5pContent->getFormattedFileSize() }}</span>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="h5p-content">
            @if($h5pContent->upload_status === 'completed' && $h5pContent->extracted_path)
                @if($h5pContent->extracted_path && Storage::exists($h5pContent->extracted_path))
                    <!-- H5P Content Display -->
                    <div id="h5p-content-{{ $h5pContent->id }}" class="h5p-content-area" style="width: 100%;">
                        <!-- Loading indicator -->
                        <div id="h5p-loading" class="text-center">
                            <div class="loading-spinner"></div>
                            <p style="margin-top: 15px; color: #6b7280;">Loading interactive content...</p>
                        </div>
                        
                        <!-- H5P content will be inserted here -->
                        <div id="h5p-container-{{ $h5pContent->id }}" style="display: none;"></div>
                    </div>
                    
                    <!-- Development Info (only visible to admins) -->
                    @if(auth()->user() && auth()->user()->hasRole('admin'))
                        <div class="h5p-info">
                            <strong>📁 Debug Info (Admin Only):</strong><br>
                            <small>
                                Content ID: {{ $h5pContent->id }}<br>
                                Extracted Path: {{ $h5pContent->extracted_path }}<br>
                                Content Type: {{ $h5pContent->content_type }}<br>
                                Upload Status: {{ $h5pContent->upload_status }}
                            </small>
                        </div>
                    @endif
                @else
                    <div class="h5p-error">
                        <h3>⚠️ Content Not Available</h3>
                        <p>The H5P content files could not be found or are corrupted.</p>
                        @if(auth()->user() && auth()->user()->hasRole('admin'))
                            <p><small>Path: {{ $h5pContent->extracted_path }}</small></p>
                        @endif
                    </div>
                @endif
            @elseif($h5pContent->upload_status === 'processing')
                <div class="h5p-placeholder">
                    <div class="loading-spinner"></div>
                    <h3>🔄 Processing Content</h3>
                    <p>This H5P content is currently being processed. Please try again in a few moments.</p>
                </div>
            @elseif($h5pContent->upload_status === 'failed')
                <div class="h5p-error">
                    <h3>❌ Processing Failed</h3>
                    <p>This H5P content failed to process correctly.</p>
                    @if($h5pContent->error_message)
                        <p><small>Error: {{ $h5pContent->error_message }}</small></p>
                    @endif
                    @if(auth()->user() && auth()->user()->hasRole('admin'))
                        <p>
                            <a href="{{ route('admin.h5p.index') }}" class="text-blue-600 hover:text-blue-800">
                                ↩️ Return to H5P Management
                            </a>
                        </p>
                    @endif
                </div>
            @else
                <div class="h5p-placeholder">
                    <i class="fas fa-puzzle-piece"></i>
                    <h3>📦 H5P Content</h3>
                    <p>Interactive content is being prepared...</p>
                </div>
            @endif
        </div>
    </div>

    @if($h5pContent->upload_status === 'completed' && $h5pContent->extracted_path)
        <!-- H5P Content Renderer -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const loadingElement = document.getElementById('h5p-loading');
                const containerElement = document.getElementById('h5p-container-{{ $h5pContent->id }}');
                
                // Load H5P content data
                fetch('/h5p/content-data/{{ $h5pContent->id }}')
                    .then(response => response.json())
                    .then(data => {
                        loadingElement.style.display = 'none';
                        containerElement.style.display = 'block';
                        
                        if (data.success) {
                            renderH5PContent(containerElement, data.content, data.metadata);
                        } else {
                            containerElement.innerHTML = `
                                <div class="h5p-error">
                                    <h3>❌ Content Loading Failed</h3>
                                    <p>${data.message || 'Unable to load H5P content'}</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        loadingElement.style.display = 'none';
                        containerElement.style.display = 'block';
                        containerElement.innerHTML = `
                            <div class="h5p-error">
                                <h3>❌ Network Error</h3>
                                <p>Failed to load H5P content: ${error.message}</p>
                            </div>
                        `;
                    });
            });
            
            function renderH5PContent(container, content, metadata) {
                if (metadata.mainLibrary === 'H5P.CoursePresentation') {
                    renderCoursePresentation(container, content, metadata);
                } else {
                    // Generic H5P content renderer
                    renderGenericH5P(container, content, metadata);
                }
            }
            
            function renderCoursePresentation(container, content, metadata) {
                const presentation = content.presentation;
                let currentSlide = 0;
                
                const presentationHTML = `
                    <div class="h5p-course-presentation">
                        <div class="slide-container">
                            <div id="slide-content"></div>
                        </div>
                        <div class="slide-navigation">
                            <button id="prev-btn" class="nav-btn" onclick="previousSlide()">← Previous</button>
                            <span id="slide-counter">1 / ${presentation.slides.length}</span>
                            <button id="next-btn" class="nav-btn" onclick="nextSlide()">Next →</button>
                        </div>
                    </div>
                    
                    <style>
                        .h5p-course-presentation {
                            max-width: 800px;
                            margin: 0 auto;
                        }
                        
                        .slide-container {
                            background: white;
                            min-height: 400px;
                            border-radius: 8px;
                            padding: 40px;
                            margin-bottom: 20px;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        }
                        
                        .slide-navigation {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding: 0 20px;
                        }
                        
                        .nav-btn {
                            background: #667eea;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 6px;
                            cursor: pointer;
                            font-weight: 500;
                            transition: background 0.2s;
                        }
                        
                        .nav-btn:hover:not(:disabled) {
                            background: #5a67d8;
                        }
                        
                        .nav-btn:disabled {
                            background: #d1d5db;
                            cursor: not-allowed;
                        }
                        
                        #slide-counter {
                            font-weight: 500;
                            color: #374151;
                        }
                        
                        .slide-content h1, .slide-content h2, .slide-content h3 {
                            color: #1f2937;
                            margin-bottom: 15px;
                        }
                        
                        .slide-content h1 { font-size: 2rem; }
                        .slide-content h2 { font-size: 1.5rem; }
                        .slide-content h3 { font-size: 1.25rem; }
                        
                        .slide-content p {
                            color: #4b5563;
                            line-height: 1.6;
                            margin-bottom: 15px;
                        }
                        
                        .slide-content ul, .slide-content ol {
                            color: #4b5563;
                            line-height: 1.6;
                            margin-left: 20px;
                        }
                        
                        .slide-content pre {
                            background: #f3f4f6;
                            padding: 20px;
                            border-radius: 6px;
                            overflow-x: auto;
                            margin: 15px 0;
                        }
                        
                        .slide-content code {
                            font-family: 'Courier New', monospace;
                            color: #dc2626;
                        }
                    </style>
                `;
                
                container.innerHTML = presentationHTML;
                
                // Store slides data globally for navigation
                window.h5pSlides = presentation.slides;
                window.currentSlide = 0;
                
                // Render first slide
                renderSlide(0);
            }
            
            function renderSlide(slideIndex) {
                const slides = window.h5pSlides;
                const slide = slides[slideIndex];
                const slideContent = document.getElementById('slide-content');
                const slideCounter = document.getElementById('slide-counter');
                const prevBtn = document.getElementById('prev-btn');
                const nextBtn = document.getElementById('next-btn');
                
                // Clear previous content
                slideContent.innerHTML = '';
                
                // Render slide elements
                slide.elements.forEach(element => {
                    if (element.action && element.action.params) {
                        const elementDiv = document.createElement('div');
                        elementDiv.className = 'slide-element';
                        
                        if (element.action.library.startsWith('H5P.AdvancedText')) {
                            elementDiv.innerHTML = element.action.params.text;
                        } else if (element.action.library.startsWith('H5P.MultiChoice')) {
                            elementDiv.innerHTML = renderMultiChoice(element.action.params);
                        } else {
                            elementDiv.innerHTML = `<p><em>Interactive element: ${element.action.library}</em></p>`;
                        }
                        
                        slideContent.appendChild(elementDiv);
                    }
                });
                
                // Update navigation
                slideCounter.textContent = `${slideIndex + 1} / ${slides.length}`;
                prevBtn.disabled = slideIndex === 0;
                nextBtn.disabled = slideIndex === slides.length - 1;
                
                window.currentSlide = slideIndex;
            }
            
            function renderMultiChoice(params) {
                let html = `<div class="multi-choice">`;
                if (params.question) {
                    html += `<h3>${params.question}</h3>`;
                }
                
                if (params.answers) {
                    html += `<div class="choices">`;
                    params.answers.forEach((answer, index) => {
                        html += `
                            <label class="choice-option">
                                <input type="radio" name="choice" value="${index}">
                                <span>${answer.text || answer}</span>
                            </label>
                        `;
                    });
                    html += `</div>`;
                }
                
                html += `</div>`;
                return html;
            }
            
            function previousSlide() {
                if (window.currentSlide > 0) {
                    renderSlide(window.currentSlide - 1);
                }
            }
            
            function nextSlide() {
                if (window.currentSlide < window.h5pSlides.length - 1) {
                    renderSlide(window.currentSlide + 1);
                }
            }
            
            function renderGenericH5P(container, content, metadata) {
                container.innerHTML = `
                    <div class="h5p-generic">
                        <h3>📚 ${metadata.title || 'H5P Content'}</h3>
                        <p>Content Type: ${metadata.mainLibrary}</p>
                        <div class="content-preview">
                            <pre>${JSON.stringify(content, null, 2)}</pre>
                        </div>
                    </div>
                `;
            }
        </script>
    @endif

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>
