# 🚂 Install Railway CLI

## For Windows (PowerShell):

```powershell
# Using Scoop (Recommended)
scoop install railway

# OR using npm
npm install -g @railway/cli

# OR direct download
curl -fsSL https://railway.app/install.sh | sh
```

## For macOS:

```bash
# Using Homebrew
brew install railway/railway/railway

# OR using npm
npm install -g @railway/cli

# OR using curl
curl -fsSL https://railway.app/install.sh | sh
```

## For Linux:

```bash
# Using npm
npm install -g @railway/cli

# OR using curl
curl -fsSL https://railway.app/install.sh | sh
```

## Login and Usage:

```bash
# Login to Railway
railway login

# Link to your project
railway link

# Run commands
railway run php artisan admin:fix-access your-email@domain.com

# View logs
railway logs

# Deploy
railway up
```

## Alternative: Use Railway Dashboard

If CLI installation fails, use the Railway Dashboard:
1. Go to railway.app → Your Project
2. Click "Deployments" → Latest deployment
3. Look for build logs to see if admin fix ran automatically
4. Use the debug route: `/debug/admin-access`
