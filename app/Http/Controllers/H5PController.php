<?php

namespace App\Http\Controllers;

use App\Models\LessonContent;
use App\Models\H5PContent;
use App\Models\H5PUsage;
use App\Rules\H5PFileRule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class H5PController extends Controller
{
    /**
     * Display H5P content management page
     */
    public function index(): View
    {
        // Authorization handled by role:admin middleware

        $h5pContents = H5PContent::with(['usages.lessonContent.lesson', 'usages.course'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.h5p.index', compact('h5pContents'));
    }

    /**
     * Show H5P content creation form
     */
    public function create(): View
    {
        // Authorization handled by role:admin middleware

        return view('admin.h5p.create');
    }

    /**
     * Upload and create H5P content
     */
    public function store(Request $request): RedirectResponse
    {
        // Authorization handled by role:admin middleware

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'h5p_file' => ['required', 'file', 'max:50000', new H5PFileRule()], // 50MB max with custom H5P validation
        ]);
        
        // Store the uploaded file
        $h5pFile = $request->file('h5p_file');
        $filePath = $h5pFile->store('h5p/packages');
        
        // Create H5P content record
        $h5pContent = H5PContent::create([
            'title' => $validated['title'] ?: $h5pFile->getClientOriginalName(),
            'description' => $validated['description'],
            'file_path' => $filePath,
            'file_size' => $h5pFile->getSize(),
            'upload_status' => 'pending',
        ]);

        // Process the H5P package in background or immediately
        $success = $h5pContent->extractPackage();

        if ($success) {
            return redirect()->route('admin.h5p.index')
                ->with('success', 'H5P content uploaded and processed successfully.');
        } else {
            return redirect()->route('admin.h5p.index')
                ->with('error', 'H5P content uploaded but processing failed: ' . $h5pContent->error_message);
        }
    }

    /**
     * Embed H5P content for display
     */
    public function embed(H5PContent $h5pContent): View
    {
        // Check if H5P content is ready
        if (!$h5pContent->isReady()) {
            abort(404, 'H5P content is not ready for viewing');
        }

        // For direct H5P content viewing (preview mode)
        // Check if user has admin access for preview, or if it's embedded in a lesson they can access
        $user = auth()->user();
        
        // Allow admin users to preview any H5P content
        if ($user && $user->hasRole('admin')) {
            return view('h5p.embed', compact('h5pContent'));
        }
        
        // For non-admin users, check if they have access through a lesson
        $lessonContent = LessonContent::where('h5p_content_id', $h5pContent->id)->first();
        
        if (!$lessonContent) {
            // If no lesson content exists, only admins can view
            abort(403, 'Access denied');
        }
        
        // Check lesson access permissions
        if (!$lessonContent->lesson->hasAccess($user)) {
            abort(403, 'Access denied');
        }

        return view('h5p.embed', [
            'h5pContent' => $h5pContent,
            'lessonContent' => $lessonContent
        ]);
    }

    /**
     * Track H5P interactions (AJAX endpoint)
     */
    public function trackInteraction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content_id' => 'required|string',
            'lesson_id' => 'required|exists:lessons,id',
            'event_data' => 'required|array',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        // Store interaction data for analytics
        $this->storeH5PInteraction($user, $validated);

        // Check if this interaction indicates completion
        $this->checkH5PCompletion($user, $validated);

        return response()->json(['success' => true]);
    }

    /**
     * Get H5P libraries available
     */
    public function getLibraries(): JsonResponse
    {
        // Authorization handled by role:admin middleware

        // This would return available H5P libraries
        $libraries = $this->getAvailableH5PLibraries();

        return response()->json($libraries);
    }

    /**
     * Get available H5P content for lesson creator
     */
    public function getAvailableContent(): JsonResponse
    {
        // Authorization handled by role:admin middleware

        $h5pContents = H5PContent::where('is_active', true)
            ->where('upload_status', 'completed')
            ->select('id', 'title', 'description', 'content_type', 'thumbnail_path', 'file_size')
            ->orderBy('title')
            ->get()
            ->map(function ($content) {
                return [
                    'id' => $content->id,
                    'title' => $content->title,
                    'description' => $content->description,
                    'content_type' => $content->content_type,
                    'thumbnail_url' => $content->getThumbnailUrl(),
                    'file_size' => $content->getFormattedFileSize(),
                    'embed_url' => $content->getEmbedUrl(),
                ];
            });

        return response()->json($h5pContents);
    }

    /**
     * Get H5P content data for rendering
     */
    public function getContentData(H5PContent $h5pContent): JsonResponse
    {
        try {
            // Check if content is ready
            if (!$h5pContent->isReady()) {
                return response()->json([
                    'success' => false,
                    'message' => 'H5P content is not ready for viewing'
                ], 404);
            }

            // Check if extracted path exists
            if (!$h5pContent->extracted_path || !Storage::exists($h5pContent->extracted_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'H5P content files not found'
                ], 404);
            }

            // Read content.json
            $contentJsonPath = $h5pContent->extracted_path . '/content/content.json';
            if (!Storage::exists($contentJsonPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'H5P content data not found'
                ], 404);
            }

            $contentData = json_decode(Storage::get($contentJsonPath), true);
            if (!$contentData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid H5P content data'
                ], 422);
            }

            // Read h5p.json for metadata
            $h5pJsonPath = $h5pContent->extracted_path . '/h5p.json';
            $h5pMetadata = [];
            if (Storage::exists($h5pJsonPath)) {
                $h5pMetadata = json_decode(Storage::get($h5pJsonPath), true) ?: [];
            }

            return response()->json([
                'success' => true,
                'content' => $contentData,
                'metadata' => array_merge($h5pMetadata, [
                    'id' => $h5pContent->id,
                    'title' => $h5pContent->title,
                    'content_type' => $h5pContent->content_type,
                    'version' => $h5pContent->version,
                ])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading H5P content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show H5P content edit form
     */
    public function edit(H5PContent $h5pContent): View
    {
        // Authorization handled by role:admin middleware
        
        $h5pContent->load(['usages.lessonContent.lesson.course']);
        
        return view('admin.h5p.edit', compact('h5pContent'));
    }

    /**
     * Update H5P content
     */
    public function update(Request $request, H5PContent $h5pContent): RedirectResponse
    {
        // Authorization handled by role:admin middleware
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $h5pContent->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.h5p.index')
            ->with('success', 'H5P content updated successfully!');
    }

    /**
     * Delete H5P content
     */
    public function destroy(H5PContent $h5pContent): RedirectResponse
    {
        // Authorization handled by role:admin middleware

        // Check if content is being used
        if ($h5pContent->usages()->count() > 0) {
            return redirect()->route('admin.h5p.index')
                ->with('error', 'Cannot delete H5P content that is being used in lessons.');
        }

        // Clean up files
        if ($h5pContent->file_path && Storage::exists($h5pContent->file_path)) {
            Storage::delete($h5pContent->file_path);
        }

        if ($h5pContent->extracted_path && Storage::exists($h5pContent->extracted_path)) {
            Storage::deleteDirectory($h5pContent->extracted_path);
        }

        if ($h5pContent->thumbnail_path && Storage::exists($h5pContent->thumbnail_path)) {
            Storage::delete($h5pContent->thumbnail_path);
        }

        $h5pContent->delete();

        return redirect()->route('admin.h5p.index')
            ->with('success', 'H5P content deleted successfully.');
    }

    /**
     * Retry processing failed H5P content
     */
    public function retryProcessing(H5PContent $h5pContent): JsonResponse
    {
        // Authorization handled by role:admin middleware

        if ($h5pContent->upload_status !== 'failed') {
            return response()->json([
                'success' => false,
                'message' => 'Content is not in failed state'
            ]);
        }

        $success = $h5pContent->extractPackage();

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Processing started successfully' : 'Failed to start processing: ' . $h5pContent->error_message
        ]);
    }

    /**
     * Export H5P content
     */
    public function export(LessonContent $content): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('view', $content);

        if ($content->content_type !== 'h5p') {
            abort(404);
        }

        $h5pFile = $this->exportH5PContent($content->h5p_content_id);

        return response()->download($h5pFile, $content->content_data['title'] . '.h5p');
    }

    /**
     * Process uploaded H5P file
     */
    private function processH5PFile($file): array
    {
        try {
            // Create unique directory for this H5P content
            $h5pId = uniqid('h5p_');
            $extractPath = storage_path("app/h5p/{$h5pId}");

            // Extract H5P file (it's a zip file)
            $zip = new \ZipArchive();
            $result = $zip->open($file->getPathname());

            if ($result !== TRUE) {
                return ['success' => false, 'message' => 'Invalid H5P file format'];
            }

            // Create extraction directory
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            $zip->extractTo($extractPath);
            $zip->close();

            // Validate H5P structure
            if (!file_exists($extractPath . '/h5p.json')) {
                return ['success' => false, 'message' => 'Invalid H5P file: missing h5p.json'];
            }

            // Read H5P metadata
            $h5pJson = json_decode(file_get_contents($extractPath . '/h5p.json'), true);
            
            if (!$h5pJson) {
                return ['success' => false, 'message' => 'Invalid H5P metadata'];
            }

            // Read content metadata
            $contentJson = null;
            if (file_exists($extractPath . '/content/content.json')) {
                $contentJson = json_decode(file_get_contents($extractPath . '/content/content.json'), true);
            }

            return [
                'success' => true,
                'h5p_id' => $h5pId,
                'library' => $h5pJson['mainLibrary'] ?? 'Unknown',
                'metadata' => [
                    'title' => $h5pJson['title'] ?? 'Untitled',
                    'language' => $h5pJson['language'] ?? 'en',
                    'defaultLanguage' => $h5pJson['defaultLanguage'] ?? 'en',
                    'license' => $h5pJson['license'] ?? 'U',
                    'embedTypes' => $h5pJson['embedTypes'] ?? ['div'],
                    'preloadedDependencies' => $h5pJson['preloadedDependencies'] ?? [],
                ],
                'content' => $contentJson,
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error processing H5P file: ' . $e->getMessage()];
        }
    }

    /**
     * Store H5P interaction for analytics
     */
    private function storeH5PInteraction($user, array $data): void
    {
        // Store in progress table or analytics table
        // This could be extended to use a dedicated H5P interactions table
        
        $eventType = $data['event_data']['verb']['id'] ?? 'unknown';
        
        // Store basic interaction data
        $user->progress()->updateOrCreate(
            [
                'lesson_id' => $data['lesson_id'],
            ],
            [
                'status' => 'in_progress',
                'started_at' => now(),
            ]
        );

        // Store detailed interaction in lesson content analytics
        // This could be expanded to store more detailed xAPI data
    }

    /**
     * Check if H5P interaction indicates completion
     */
    private function checkH5PCompletion($user, array $data): void
    {
        $eventData = $data['event_data'];
        $verb = $eventData['verb']['id'] ?? '';

        // Check for completion verbs
        if (in_array($verb, ['completed', 'answered', 'experienced'])) {
            $score = $eventData['result']['score']['scaled'] ?? null;
            $success = $eventData['result']['success'] ?? false;

            // Mark lesson as completed if criteria met
            if ($success || $score >= 0.7) { // 70% success threshold
                $progress = $user->progress()->where('lesson_id', $data['lesson_id'])->first();
                
                if ($progress) {
                    $progress->markAsCompleted();
                }
            }
        }
    }

    /**
     * Get available H5P libraries
     */
    private function getAvailableH5PLibraries(): array
    {
        // This would scan for installed H5P libraries
        // For now, return common ones
        return [
            [
                'name' => 'H5P.InteractiveVideo',
                'title' => 'Interactive Video',
                'majorVersion' => 1,
                'minorVersion' => 22,
            ],
            [
                'name' => 'H5P.CoursePresentation',
                'title' => 'Course Presentation',
                'majorVersion' => 1,
                'minorVersion' => 24,
            ],
            [
                'name' => 'H5P.QuestionSet',
                'title' => 'Question Set',
                'majorVersion' => 1,
                'minorVersion' => 20,
            ],
            [
                'name' => 'H5P.MultiChoice',
                'title' => 'Multiple Choice',
                'majorVersion' => 1,
                'minorVersion' => 16,
            ],
            [
                'name' => 'H5P.DragQuestion',
                'title' => 'Drag and Drop',
                'majorVersion' => 1,
                'minorVersion' => 14,
            ],
        ];
    }

    /**
     * Export H5P content as downloadable file
     */
    private function exportH5PContent(string $h5pId): string
    {
        $sourcePath = storage_path("app/h5p/{$h5pId}");
        $exportFile = storage_path("app/temp/{$h5pId}.h5p");

        // Create temp directory if it doesn't exist
        $tempDir = dirname($exportFile);
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Create zip file
        $zip = new \ZipArchive();
        $zip->open($exportFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Add all files from source directory
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourcePath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return $exportFile;
    }

    /**
     * Clean up H5P files when content is deleted
     */
    private function cleanupH5PFiles(string $h5pId): void
    {
        $h5pPath = storage_path("app/h5p/{$h5pId}");
        
        if (file_exists($h5pPath)) {
            $this->deleteDirectory($h5pPath);
        }
    }

    /**
     * Recursively delete directory
     */
    private function deleteDirectory(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
}