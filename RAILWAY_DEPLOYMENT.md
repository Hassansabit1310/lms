# Railway Deployment Guide for Laravel LMS

## 🚀 Quick Deployment Steps

**CRITICAL:** Railway keeps using PHP 8.4 despite constraints. We now have two approaches:

### Approach A: Docker (RECOMMENDED)
Use the included `Dockerfile` which forces PHP 8.3:

1. **Railway will auto-detect the Dockerfile**
2. **Docker image uses PHP 8.3-cli specifically**
3. **No version conflicts possible**

### Approach B: Nixpacks (Alternative)
Use the `nixpacks.toml` configuration.

### 1. **Environment Variables (Set in Railway Dashboard)**

```bash
# PHP Version Control (Important!)
NIXPACKS_PHP_VERSION=8.3

# Application
APP_NAME="Laravel LMS"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app.railway.app

# Database (Railway MySQL Plugin)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 2. **Generate App Key**

Run locally first:
```bash
php artisan key:generate --show
```
Copy the output and set as `APP_KEY` in Railway.

### 3. **Database Setup**

1. **Add MySQL Plugin** in Railway dashboard
2. **Set environment variables** using the MySQL connection details
3. **Run migrations** via Railway CLI or deploy trigger:
   ```bash
   php artisan migrate --force
   ```

### 4. **Build & Deploy**

Railway will automatically:
- ✅ **Detect Laravel app** (via `composer.json`)
- ✅ **Use PHP 8.3** (constrained: `"php": ">=8.0 <8.4"`)
- ✅ **Install dependencies**: `composer install --no-dev --optimize-autoloader`
- ✅ **Start server**: `php artisan serve --host=0.0.0.0 --port=$PORT`

**Critical fix:** Railway was using PHP 8.4 which isn't supported by some dependencies. Now forced to use PHP 8.3.

### 4a. **Alternative: Manual Start Command**

If auto-detection doesn't work, set this in Railway's service settings:

**Start Command:**
```
php artisan serve --host=0.0.0.0 --port=$PORT
```

**Build Command (if needed):**
```
composer install --no-dev --optimize-autoloader
```

### 5. **File Permissions**

Ensure these directories are writable:
- `storage/`
- `bootstrap/cache/`

### 6. **Production Optimizations**

The deployment includes:
- ✅ Composer optimized autoloader
- ✅ Configuration caching
- ✅ Route caching
- ✅ View caching
- ✅ PHP 8.1 runtime

### 7. **Troubleshooting**

**Common Issues:**

1. **500 Error**: Check `APP_KEY` is set and valid
2. **Database Connection**: Verify MySQL plugin variables
3. **File Permissions**: Check storage directory permissions
4. **PHP Version**: Now fixed to use PHP 8.1

**Debug Commands:**
```bash
# Check environment
php artisan env

# Test database connection
php artisan migrate:status

# Clear all caches
php artisan optimize:clear
```

### 8. **Post-Deployment**

1. **Create admin user**:
   ```bash
   php artisan tinker
   User::create([
       'name' => 'Admin',
       'email' => 'admin@example.com',
       'password' => Hash::make('password123')
   ]);
   ```

2. **Test features**:
   - Login system
   - Course creation
   - Matter.js animations
   - Runnable code blocks

## 🔒 Security Notes

- `APP_DEBUG=false` in production
- Use strong `APP_KEY`
- Secure database credentials
- Enable HTTPS (Railway provides this automatically)

## 📊 Monitoring

Railway provides:
- Application logs
- Performance metrics
- Uptime monitoring
- Automatic SSL certificates

Your Laravel LMS is now ready for production! 🎉
