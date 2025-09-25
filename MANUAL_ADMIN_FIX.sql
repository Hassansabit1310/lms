-- Manual Admin Access Fix for Railway Database
-- Run this in Railway's MySQL interface or phpMyAdmin

-- 1. Update user to have admin role and verified email
UPDATE users 
SET 
    role = 'admin',
    email_verified_at = NOW()
WHERE email = 'your-email@domain.com';

-- 2. Check if the user exists and has admin role
SELECT id, name, email, role, email_verified_at, created_at 
FROM users 
WHERE role = 'admin';

-- 3. If using Spatie Permission, sync the role
-- You'll need to insert into model_has_roles table
-- First, get the user ID and admin role ID:

-- Get admin role ID
SELECT id FROM roles WHERE name = 'admin';

-- Get your user ID  
SELECT id FROM users WHERE email = 'your-email@domain.com';

-- Then insert the role assignment (replace USER_ID and ROLE_ID with actual values)
-- INSERT INTO model_has_roles (role_id, model_type, model_id) 
-- VALUES (ROLE_ID, 'App\\Models\\User', USER_ID)
-- ON DUPLICATE KEY UPDATE role_id = ROLE_ID;
