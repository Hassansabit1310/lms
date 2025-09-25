<?php

// TEMPORARY ADMIN FIX ROUTE - REMOVE AFTER FIXING ADMIN ACCESS
// Add this to routes/web.php temporarily

Route::get('/emergency-admin-fix', function() {
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    $user = auth()->user();
    
    try {
        // Step 1: Ensure roles exist
        if (!Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
            Spatie\Permission\Models\Role::create(['name' => 'admin']);
        }
        if (!Spatie\Permission\Models\Role::where('name', 'student')->exists()) {
            Spatie\Permission\Models\Role::create(['name' => 'student']);
        }
        if (!Spatie\Permission\Models\Role::where('name', 'instructor')->exists()) {
            Spatie\Permission\Models\Role::create(['name' => 'instructor']);
        }
        
        // Step 2: Fix current user
        $user->update([
            'role' => 'admin',
            'email_verified_at' => now()
        ]);
        
        // Step 3: Sync Spatie roles
        $user->syncRoles(['admin']);
        
        // Step 4: Verify the fix
        $user = $user->fresh();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Admin access fixed!',
            'user' => [
                'email' => $user->email,
                'database_role' => $user->role,
                'spatie_roles' => $user->getRoleNames()->toArray(),
                'has_admin_role' => $user->hasRole('admin'),
                'email_verified' => !is_null($user->email_verified_at),
            ],
            'next_step' => 'Try accessing /admin/dashboard now',
            'admin_url' => route('admin.dashboard')
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Fix failed: ' . $e->getMessage(),
            'suggestion' => 'Try the manual SQL approach'
        ]);
    }
})->middleware('auth')->name('emergency.admin.fix');
