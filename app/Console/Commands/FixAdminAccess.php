<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class FixAdminAccess extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:fix-access {email?}';

    /**
     * The console command description.
     */
    protected $description = 'Fix admin access issues by ensuring roles and permissions are properly set up';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing Admin Access Issues...');
        
        // Step 1: Ensure roles exist
        $this->info('📋 Step 1: Ensuring roles exist...');
        $this->ensureRolesExist();
        
        // Step 2: Fix user roles
        $email = $this->argument('email');
        if ($email) {
            $this->info("👤 Step 2: Fixing access for user: {$email}");
            $this->fixUserAccess($email);
        } else {
            $this->info('👥 Step 2: Fixing access for all admin users...');
            $this->fixAllAdminUsers();
        }
        
        // Step 3: Summary
        $this->showSummary();
        
        $this->info('✅ Admin access fix completed!');
    }
    
    private function ensureRolesExist()
    {
        $roles = ['admin', 'instructor', 'student'];
        
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $this->line("  ✓ Role '{$roleName}' exists");
        }
    }
    
    private function fixUserAccess($email)
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ User with email '{$email}' not found!");
            
            if ($this->confirm('Would you like to create an admin user with this email?')) {
                $name = $this->ask('Enter the user name', 'Admin');
                $password = $this->secret('Enter password (default: password)', 'password');
                
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'admin',
                    'email_verified_at' => now(), // Auto-verify
                ]);
                
                $this->info("✅ Created admin user: {$email}");
            } else {
                return;
            }
        }
        
        // Fix the user
        $this->fixSingleUser($user);
    }
    
    private function fixAllAdminUsers()
    {
        $adminUsers = User::where('role', 'admin')->get();
        
        if ($adminUsers->isEmpty()) {
            $this->warn('⚠️ No admin users found in database!');
            
            if ($this->confirm('Would you like to create a default admin user?')) {
                $email = $this->ask('Enter admin email', 'admin@example.com');
                $this->fixUserAccess($email);
            }
            return;
        }
        
        foreach ($adminUsers as $user) {
            $this->fixSingleUser($user);
        }
    }
    
    private function fixSingleUser(User $user)
    {
        $this->line("  🔧 Fixing user: {$user->email}");
        
        // Ensure database role is set
        if (empty($user->role)) {
            $user->update(['role' => 'admin']);
            $this->line("    ✓ Set database role to 'admin'");
        }
        
        // Ensure email is verified
        if (!$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
            $this->line("    ✓ Marked email as verified");
        }
        
        // Sync Spatie roles
        $user->syncRoles(['admin']);
        $this->line("    ✓ Synced Spatie permission role to 'admin'");
        
        // Verify the fix
        $hasRole = $user->fresh()->hasRole('admin');
        $this->line("    " . ($hasRole ? "✅ VERIFIED: User has admin role" : "❌ FAILED: User still doesn't have admin role"));
    }
    
    private function showSummary()
    {
        $this->info('📊 Summary:');
        
        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        $verifiedAdmins = User::where('role', 'admin')->whereNotNull('email_verified_at')->count();
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Users', $totalUsers],
                ['Admin Users (DB)', $adminUsers],
                ['Verified Admins', $verifiedAdmins],
                ['Available Roles', Role::count()],
            ]
        );
        
        $this->info('🔗 Admin Dashboard URL: ' . config('app.url') . '/admin/dashboard');
        
        if ($adminUsers > 0) {
            $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();
            $this->info('👨‍💼 Admin Users: ' . implode(', ', $adminEmails));
        }
    }
}
