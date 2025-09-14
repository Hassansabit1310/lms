<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SyncDatabaseRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:db-roles {--user=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync database role column with Spatie permission roles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Syncing database roles with Spatie roles...');
        
        // Get specific user or all users
        if ($this->option('user')) {
            $users = User::where('email', $this->option('user'))->get();
            if ($users->isEmpty()) {
                $this->error("❌ User with email '{$this->option('user')}' not found!");
                return 1;
            }
        } else {
            $users = User::all();
        }

        $fixed = 0;
        $errors = 0;

        $this->line("\n📊 Checking " . $users->count() . " user(s)...\n");

        foreach ($users as $user) {
            $dbRole = $user->role ?? 'student';
            $spatieRoles = $user->getRoleNames()->toArray();
            
            // Check if sync is needed
            $needsSync = false;
            
            if (empty($spatieRoles)) {
                $needsSync = true;
                $reason = "No Spatie roles assigned";
            } elseif (count($spatieRoles) > 1) {
                $needsSync = true;
                $reason = "Multiple Spatie roles: " . implode(', ', $spatieRoles);
            } elseif (!in_array($dbRole, $spatieRoles)) {
                $needsSync = true;
                $reason = "Mismatch - DB: {$dbRole}, Spatie: " . implode(', ', $spatieRoles);
            } else {
                $reason = "✅ Already synced";
            }

            $this->line("👤 {$user->email}");
            $this->line("   📂 DB Role: {$dbRole}");
            $this->line("   🔐 Spatie: " . (empty($spatieRoles) ? 'NONE' : implode(', ', $spatieRoles)));
            $this->line("   📝 Status: {$reason}");

            if ($needsSync) {
                if ($this->option('force') || $this->confirm("   🔧 Fix this user?", true)) {
                    try {
                        // Validate role
                        if (!in_array($dbRole, ['admin', 'student', 'instructor'])) {
                            $this->error("   ❌ Invalid role '{$dbRole}', setting to 'student'");
                            $dbRole = 'student';
                            $user->update(['role' => 'student']);
                        }

                        // Sync Spatie roles
                        $user->syncRoles([$dbRole]);
                        
                        // Clear user sessions if role changed significantly
                        if (in_array('admin', $spatieRoles) && $dbRole !== 'admin') {
                            $this->clearUserSessions($user);
                            $this->line("   🧹 Cleared user sessions");
                        }
                        
                        $this->info("   ✅ Fixed! Now both systems show: {$dbRole}");
                        $fixed++;
                        
                    } catch (\Exception $e) {
                        $this->error("   ❌ Error: " . $e->getMessage());
                        $errors++;
                    }
                } else {
                    $this->line("   ⏭️  Skipped");
                }
            }
            
            $this->line("");
        }

        // Summary
        $this->info("📈 Summary:");
        $this->line("   ✅ Users fixed: {$fixed}");
        $this->line("   ❌ Errors: {$errors}");
        $this->line("   👥 Total checked: " . $users->count());

        if ($fixed > 0) {
            $this->info("\n🎉 Sync completed! Users with role changes must login again.");
        }

        return 0;
    }

    /**
     * Clear user sessions
     */
    private function clearUserSessions(User $user)
    {
        try {
            $sessionPath = storage_path('framework/sessions');
            if (is_dir($sessionPath)) {
                $files = glob($sessionPath . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $content = file_get_contents($file);
                        if (strpos($content, $user->id) !== false || strpos($content, $user->email) !== false) {
                            unlink($file);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Session clearing failed, but continue
        }
    }
}