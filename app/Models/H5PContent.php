<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class H5PContent extends Model
{
    use HasFactory;

    protected $table = 'h5_p_contents';

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'extracted_path',
        'file_size',
        'content_type',
        'metadata',
        'thumbnail_path',
        'version',
        'is_active',
        'upload_status',
        'error_message',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Get the usage records for this H5P content
     */
    public function usages(): HasMany
    {
        return $this->hasMany(H5PUsage::class, 'h5p_content_id');
    }

    /**
     * Get the lesson contents that use this H5P content
     */
    public function lessonContents()
    {
        return $this->belongsToMany(LessonContent::class, 'h5_p_usages', 'h5p_content_id', 'lesson_content_id');
    }

    /**
     * Get the courses that use this H5P content
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'h5_p_usages', 'h5p_content_id', 'course_id');
    }

    /**
     * Extract H5P package and parse metadata
     */
    public function extractPackage(): bool
    {
        try {
            $this->update(['upload_status' => 'processing']);

            $zip = new ZipArchive;
            $filePath = Storage::path($this->file_path);
            
            if ($zip->open($filePath) === TRUE) {
                // Create extraction directory
                $extractPath = 'h5p/extracted/' . $this->id;
                $extractFullPath = Storage::path($extractPath);
                
                if (!file_exists($extractFullPath)) {
                    mkdir($extractFullPath, 0755, true);
                }
                
                // Extract all files
                $zip->extractTo($extractFullPath);
                $zip->close();
                
                // Parse h5p.json for metadata
                $h5pJsonPath = $extractFullPath . '/h5p.json';
                if (file_exists($h5pJsonPath)) {
                    $h5pData = json_decode(file_get_contents($h5pJsonPath), true);
                    
                    // Parse content.json for additional info
                    $contentJsonPath = $extractFullPath . '/content/content.json';
                    $contentData = [];
                    if (file_exists($contentJsonPath)) {
                        $contentData = json_decode(file_get_contents($contentJsonPath), true);
                    }
                    
                    // Safely extract version info
                    $version = '1.0';
                    if (isset($h5pData['coreApi']) && is_array($h5pData['coreApi'])) {
                        $majorVersion = $h5pData['coreApi']['majorVersion'] ?? '1';
                        $minorVersion = $h5pData['coreApi']['minorVersion'] ?? '0';
                        $version = $majorVersion . '.' . $minorVersion;
                    }
                    
                    // Safely extract title
                    $extractedTitle = $this->title;
                    if (!$extractedTitle) {
                        if (isset($contentData['metadata']['title'])) {
                            $extractedTitle = $contentData['metadata']['title'];
                        } elseif (isset($h5pData['title'])) {
                            $extractedTitle = $h5pData['title'];
                        } else {
                            $extractedTitle = 'Untitled H5P Content';
                        }
                    }
                    
                    // Update model with extracted data
                    $this->update([
                        'extracted_path' => $extractPath,
                        'content_type' => $h5pData['mainLibrary'] ?? 'Unknown',
                        'version' => $version,
                        'metadata' => array_merge($h5pData, ['content' => $contentData]),
                        'upload_status' => 'completed',
                        'title' => $extractedTitle,
                    ]);
                    
                    // Generate thumbnail
                    $this->generateThumbnail();
                    
                    return true;
                } else {
                    throw new \Exception('Invalid H5P package: h5p.json not found');
                }
            } else {
                throw new \Exception('Could not open H5P package');
            }
        } catch (\Exception $e) {
            $this->update([
                'upload_status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Generate thumbnail for H5P content
     */
    public function generateThumbnail(): void
    {
        try {
            // Look for icon files in the extracted content
            $extractPath = Storage::path($this->extracted_path);
            $possibleIcons = ['icon.svg', 'icon.png', 'icon.jpg', 'icon.jpeg'];
            
            foreach ($possibleIcons as $iconFile) {
                $iconPath = $extractPath . '/' . $iconFile;
                if (file_exists($iconPath)) {
                    $thumbnailPath = 'h5p/thumbnails/' . $this->id . '.' . pathinfo($iconFile, PATHINFO_EXTENSION);
                    Storage::copy($this->extracted_path . '/' . $iconFile, $thumbnailPath);
                    $this->update(['thumbnail_path' => $thumbnailPath]);
                    return;
                }
            }
            
            // If no icon found, create a default thumbnail
            $this->createDefaultThumbnail();
        } catch (\Exception $e) {
            // Silently fail thumbnail generation
            \Log::warning('Failed to generate H5P thumbnail: ' . $e->getMessage());
        }
    }

    /**
     * Create a default thumbnail
     */
    private function createDefaultThumbnail(): void
    {
        // Create a simple default thumbnail (you can enhance this)
        $thumbnailPath = 'h5p/thumbnails/' . $this->id . '.png';
        
        // For now, just set a placeholder path
        // In a real implementation, you'd generate an image here
        $this->update(['thumbnail_path' => $thumbnailPath]);
    }

    /**
     * Get the full URL for the H5P content
     */
    public function getEmbedUrl(): string
    {
        return route('h5p.embed', $this->id);
    }

    /**
     * Get the thumbnail URL
     */
    public function getThumbnailUrl(): string
    {
        if ($this->thumbnail_path && Storage::exists($this->thumbnail_path)) {
            return Storage::url($this->thumbnail_path);
        }
        
        // Return default H5P icon
        return asset('images/h5p-default.svg');
    }

    /**
     * Check if content is ready for use
     */
    public function isReady(): bool
    {
        return $this->upload_status === 'completed' && $this->is_active;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
