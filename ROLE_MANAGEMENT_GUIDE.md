# 🔐 Role Management Guide

## Overview
Your LMS uses **two role systems** that must stay synchronized:
1. **Database `role` column** - stores user roles in the `users` table
2. **Spatie Permission system** - handles authentication middleware

## 🛠️ Available Commands

### 1. Sync All Users
```bash
php artisan sync:db-roles --force
```
Automatically fixes all users' role mismatches.

### 2. Sync Specific User
```bash
php artisan sync:db-roles --user=email@example.com --force
```
Fixes a specific user's role synchronization.

### 3. Interactive Sync (with confirmations)
```bash
php artisan sync:db-roles
```
Asks for confirmation before fixing each user.

## 🔄 How It Works

### Automatic Sync
The `User` model now has **automatic synchronization**:
- When `hasRole()` is called → Auto-syncs if needed
- When `hasAnyRole()` is called → Auto-syncs if needed
- Logs all auto-sync operations

### Manual Database Changes
If you manually change the database role:
1. **Run the sync command** → `php artisan sync:db-roles --user=email@example.com --force`
2. **User must re-login** → Sessions are cleared for security

## 🎯 Common Scenarios

### Scenario 1: Manual Database Update
```sql
-- You manually change role in database
UPDATE users SET role = 'admin' WHERE email = 'user@example.com';
```
**Solution:**
```bash
php artisan sync:db-roles --user=user@example.com --force
```

### Scenario 2: User Gets "Forbidden" Error
**Cause:** Role mismatch between database and Spatie
**Solution:**
```bash
php artisan sync:db-roles --user=user@example.com --force
```

### Scenario 3: User Can Access Wrong Areas
**Cause:** Cached sessions with old roles
**Solution:** Sync command automatically clears sessions

## 🔍 Troubleshooting

### Check User's Current State
```bash
php artisan sync:db-roles --user=email@example.com
```
Shows current database role vs Spatie roles.

### View All Users Status
```bash
php artisan sync:db-roles
```
Shows all users and their sync status.

## 🚀 Best Practices

1. **Use Admin Interface** → Always change roles via `/admin/users` (auto-syncs)
2. **Run Sync After Manual Changes** → If you change database directly
3. **Clear Sessions** → Users must re-login after role changes
4. **Monitor Logs** → Auto-sync operations are logged

## ⚠️ Important Notes

- **Spatie middleware** checks Spatie roles (not database role)
- **Auto-sync** happens on first role check after mismatch
- **Session clearing** prevents cached access with old roles
- **Invalid roles** default to 'student'

## 📝 Valid Roles
- `admin` - Full access to admin panel
- `instructor` - Can manage courses/lessons  
- `student` - Basic user access

---
**Remember:** Database role changes require sync command to update Spatie system!
