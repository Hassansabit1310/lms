<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\H5PController;
use App\Models\H5PContent;

try {
    echo "Testing H5P Content Data API...\n\n";
    
    $h5pContent = H5PContent::find(1);
    if (!$h5pContent) {
        echo "H5P content with ID 1 not found\n";
        exit(1);
    }
    
    echo "H5P Content Found:\n";
    echo "- ID: {$h5pContent->id}\n";
    echo "- Title: {$h5pContent->title}\n";
    echo "- Status: {$h5pContent->upload_status}\n";
    echo "- Extracted Path: {$h5pContent->extracted_path}\n";
    echo "- Is Ready: " . ($h5pContent->isReady() ? 'Yes' : 'No') . "\n\n";
    
    // Test the controller method directly
    $controller = new H5PController();
    $response = $controller->getContentData($h5pContent);
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ API Response Success!\n";
        echo "Content Type: " . $data['metadata']['mainLibrary'] . "\n";
        echo "Title: " . $data['metadata']['title'] . "\n";
        
        if (isset($data['content']['presentation']['slides'])) {
            $slideCount = count($data['content']['presentation']['slides']);
            echo "Slides: {$slideCount}\n";
            echo "First slide preview: " . substr(json_encode($data['content']['presentation']['slides'][0]), 0, 100) . "...\n";
        }
    } else {
        echo "❌ API Response Failed:\n";
        echo "Message: " . $data['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
