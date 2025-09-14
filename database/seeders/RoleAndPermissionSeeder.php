<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create permissions
        $permissions = [
            // Course permissions
            'manage_courses',
            'create_courses',
            'edit_courses',
            'delete_courses',
            'view_courses',
            
            // Lesson permissions
            'manage_lessons',
            'create_lessons',
            'edit_lessons',
            'delete_lessons',
            'view_lessons',
            
            // User permissions
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_users',
            
            // Payment permissions
            'manage_payments',
            'view_payments',
            
            // Subscription permissions
            'manage_subscriptions',
            'view_subscriptions',
            
            // Category permissions
            'manage_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'view_categories',
            
            // Review permissions
            'manage_reviews',
            'create_reviews',
            'edit_reviews',
            'delete_reviews',
            'view_reviews',
            
            // Progress permissions
            'view_progress',
            'manage_progress',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor', 'guard_name' => 'web']);

        // Assign permissions to admin (all permissions)
        $adminRole->givePermissionTo(Permission::all());

        // Assign permissions to instructor
        $instructorRole->givePermissionTo([
            'manage_courses',
            'create_courses',
            'edit_courses',
            'view_courses',
            'manage_lessons',
            'create_lessons',
            'edit_lessons',
            'view_lessons',
            'view_progress',
            'view_reviews',
            'view_categories',
        ]);

        // Assign permissions to student
        $studentRole->givePermissionTo([
            'view_courses',
            'view_lessons',
            'create_reviews',
            'edit_reviews',
            'view_categories',
        ]);

        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@lms.test'],
            [
                'name' => 'LMS Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create default student user
        $student = User::firstOrCreate(
            ['email' => 'student@lms.test'],
            [
                'name' => 'Test Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );
        $student->assignRole('student');

        // Create default instructor user
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@lms.test'],
            [
                'name' => 'Test Instructor',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'email_verified_at' => now(),
            ]
        );
        $instructor->assignRole('instructor');
    }
}
