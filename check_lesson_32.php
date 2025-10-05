<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$lesson = \App\Models\Lesson::with('contents')->find(32);

if ($lesson) {
    echo "Lesson 32: {$lesson->title}\n";
    echo "Content blocks:\n";
    foreach($lesson->contents as $content) {
        echo "- ID: {$content->id}, Type: {$content->content_type}, Order: {$content->order}\n";
        if ($content->content_data) {
            echo "  Data: " . json_encode($content->content_data) . "\n";
        }
    }
} else {
    echo "Lesson 32 not found\n";
}
