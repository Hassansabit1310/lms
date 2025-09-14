<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Programming',
                'description' => 'Learn programming languages and software development',
                'slug' => 'programming',
                'children' => [
                    ['name' => 'Web Development', 'description' => 'HTML, CSS, JavaScript, and frameworks'],
                    ['name' => 'Mobile Development', 'description' => 'iOS, Android, and cross-platform development'],
                    ['name' => 'Backend Development', 'description' => 'Server-side programming and APIs'],
                    ['name' => 'Database', 'description' => 'SQL, NoSQL, and database design'],
                ]
            ],
            [
                'name' => 'Design',
                'description' => 'Graphic design, UI/UX, and creative skills',
                'slug' => 'design',
                'children' => [
                    ['name' => 'UI/UX Design', 'description' => 'User interface and user experience design'],
                    ['name' => 'Graphic Design', 'description' => 'Visual design and branding'],
                    ['name' => 'Web Design', 'description' => 'Website layout and visual design'],
                ]
            ],
            [
                'name' => 'Business',
                'description' => 'Business skills and entrepreneurship',
                'slug' => 'business',
                'children' => [
                    ['name' => 'Marketing', 'description' => 'Digital marketing and promotion strategies'],
                    ['name' => 'Finance', 'description' => 'Financial planning and investment'],
                    ['name' => 'Management', 'description' => 'Project and team management'],
                ]
            ],
            [
                'name' => 'Data Science',
                'description' => 'Data analysis, machine learning, and AI',
                'slug' => 'data-science',
                'children' => [
                    ['name' => 'Machine Learning', 'description' => 'ML algorithms and implementation'],
                    ['name' => 'Data Analysis', 'description' => 'Statistical analysis and visualization'],
                    ['name' => 'AI', 'description' => 'Artificial intelligence and neural networks'],
                ]
            ],
            [
                'name' => 'Personal Development',
                'description' => 'Self-improvement and life skills',
                'slug' => 'personal-development',
                'children' => [
                    ['name' => 'Productivity', 'description' => 'Time management and efficiency'],
                    ['name' => 'Communication', 'description' => 'Public speaking and interpersonal skills'],
                    ['name' => 'Leadership', 'description' => 'Leadership and team building'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'slug' => $categoryData['slug'],
            ]);

            if (isset($categoryData['children'])) {
                foreach ($categoryData['children'] as $childData) {
                    Category::create([
                        'name' => $childData['name'],
                        'description' => $childData['description'],
                        'slug' => Str::slug($childData['name']),
                        'parent_id' => $category->id,
                    ]);
                }
            }
        }
    }
}
