<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;
use App\Models\Lesson;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get categories
        $webDev = Category::where('slug', 'web-development')->first();
        $uiux = Category::where('slug', 'ui-ux-design')->first();
        $marketing = Category::where('slug', 'marketing')->first();
        $dataAnalysis = Category::where('slug', 'data-analysis')->first();

        $courses = [
            [
                'title' => 'Complete Web Development Bootcamp',
                'description' => 'Learn HTML, CSS, JavaScript, PHP, and Laravel from scratch. Build real-world projects and become a full-stack developer.',
                'short_description' => 'Master web development with HTML, CSS, JavaScript, PHP, and Laravel',
                'price' => 99.99,
                'is_free' => false,
                'category_id' => $webDev?->id,
                'slug' => 'complete-web-development-bootcamp',
                'status' => 'published',
                'level' => 'beginner',
                'duration_minutes' => 2400,
                'lessons' => [
                    ['title' => 'Introduction to Web Development', 'type' => 'youtube', 'content' => 'https://www.youtube.com/watch?v=UB1O30fR-EE', 'is_free' => true, 'order' => 1, 'duration_minutes' => 30],
                    ['title' => 'HTML Fundamentals', 'type' => 'youtube', 'content' => 'https://www.youtube.com/watch?v=qz0aGYrrlhU', 'is_free' => true, 'order' => 2, 'duration_minutes' => 60],
                    ['title' => 'CSS Basics and Styling', 'type' => 'text', 'content' => 'Learn the fundamentals of CSS including selectors, properties, and layouts.', 'is_free' => false, 'order' => 3, 'duration_minutes' => 90],
                    ['title' => 'JavaScript Introduction', 'type' => 'code', 'content' => '// Your first JavaScript program\nconsole.log("Hello, World!");\n\n// Variables and functions\nlet name = "Student";\nfunction greet(name) {\n    return "Hello, " + name + "!";\n}', 'is_free' => false, 'order' => 4, 'duration_minutes' => 120],
                ]
            ],
            [
                'title' => 'Modern JavaScript ES6+',
                'description' => 'Master modern JavaScript features including arrow functions, async/await, destructuring, and more.',
                'short_description' => 'Learn modern JavaScript ES6+ features and best practices',
                'price' => 49.99,
                'is_free' => false,
                'category_id' => $webDev?->id,
                'slug' => 'modern-javascript-es6',
                'status' => 'published',
                'level' => 'intermediate',
                'duration_minutes' => 800,
                'lessons' => [
                    ['title' => 'ES6 Overview', 'type' => 'youtube', 'content' => 'https://www.youtube.com/watch?v=WZQc7RUAg18', 'is_free' => true, 'order' => 1, 'duration_minutes' => 25],
                    ['title' => 'Arrow Functions and Template Literals', 'type' => 'code', 'content' => '// Arrow functions\nconst add = (a, b) => a + b;\n\n// Template literals\nconst message = `Hello, ${name}!`;', 'is_free' => false, 'order' => 2, 'duration_minutes' => 45],
                ]
            ],
            [
                'title' => 'Free HTML & CSS Course',
                'description' => 'Learn the basics of HTML and CSS completely free. Perfect for beginners starting their web development journey.',
                'short_description' => 'Free introduction to HTML and CSS for beginners',
                'price' => 0,
                'is_free' => true,
                'category_id' => $webDev?->id,
                'slug' => 'free-html-css-course',
                'status' => 'published',
                'level' => 'beginner',
                'duration_minutes' => 300,
                'lessons' => [
                    ['title' => 'What is HTML?', 'type' => 'text', 'content' => 'HTML (HyperText Markup Language) is the standard markup language for creating web pages.', 'is_free' => true, 'order' => 1, 'duration_minutes' => 20],
                    ['title' => 'Basic HTML Structure', 'type' => 'code', 'content' => '<!DOCTYPE html>\n<html>\n<head>\n    <title>My First Page</title>\n</head>\n<body>\n    <h1>Hello World!</h1>\n</body>\n</html>', 'is_free' => true, 'order' => 2, 'duration_minutes' => 30],
                    ['title' => 'CSS Introduction', 'type' => 'text', 'content' => 'CSS (Cascading Style Sheets) is used to style and layout web pages.', 'is_free' => true, 'order' => 3, 'duration_minutes' => 25],
                ]
            ],
            [
                'title' => 'UI/UX Design Fundamentals',
                'description' => 'Learn the principles of user interface and user experience design. Create beautiful and functional designs.',
                'short_description' => 'Master UI/UX design principles and best practices',
                'price' => 79.99,
                'is_free' => false,
                'category_id' => $uiux?->id,
                'slug' => 'ui-ux-design-fundamentals',
                'status' => 'published',
                'level' => 'beginner',
                'duration_minutes' => 1200,
                'lessons' => [
                    ['title' => 'Design Principles Overview', 'type' => 'youtube', 'content' => 'https://www.youtube.com/watch?v=a5KYlHNKQB8', 'is_free' => true, 'order' => 1, 'duration_minutes' => 40],
                    ['title' => 'Color Theory and Typography', 'type' => 'text', 'content' => 'Understanding color psychology and typography principles for effective design.', 'is_free' => false, 'order' => 2, 'duration_minutes' => 60],
                ]
            ],
            [
                'title' => 'Digital Marketing Mastery',
                'description' => 'Complete guide to digital marketing including SEO, social media, email marketing, and paid advertising.',
                'short_description' => 'Comprehensive digital marketing course for all levels',
                'price' => 129.99,
                'is_free' => false,
                'category_id' => $marketing?->id,
                'slug' => 'digital-marketing-mastery',
                'status' => 'published',
                'level' => 'intermediate',
                'duration_minutes' => 1800,
                'lessons' => [
                    ['title' => 'Digital Marketing Introduction', 'type' => 'youtube', 'content' => 'https://www.youtube.com/watch?v=bixR-KIJKYM', 'is_free' => true, 'order' => 1, 'duration_minutes' => 35],
                    ['title' => 'SEO Fundamentals', 'type' => 'text', 'content' => 'Search Engine Optimization strategies to improve your website ranking.', 'is_free' => false, 'order' => 2, 'duration_minutes' => 90],
                ]
            ],
        ];

        foreach ($courses as $courseData) {
            $lessons = $courseData['lessons'];
            unset($courseData['lessons']);

            $course = Course::create($courseData);

            foreach ($lessons as $lessonData) {
                $lessonData['course_id'] = $course->id;
                Lesson::create($lessonData);
            }
        }
    }
}
