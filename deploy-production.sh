#!/bin/bash
# ==============================================================================
# BITSI Dispatch – Production Deployment Script
# ==============================================================================
# Run this script on your production server after pulling the latest code.
#
# Usage:
#   chmod +x deploy-production.sh
#   ./deploy-production.sh
#
# Prerequisites:
#   - PHP 8.2+ with required extensions
#   - Composer 2.x
#   - Node.js 20+ & npm
#   - MySQL 8+ (or configured database)
#   - Web server (Apache/Nginx) configured to serve public/
# ==============================================================================

set -euo pipefail

echo "🚀 Starting BITSI Dispatch deployment..."

# 1. Copy environment file if not exists
if [ ! -f .env ]; then
    echo "📄 Creating .env from .env.example..."
    cp .env.example .env
    echo "⚠️  Please edit .env with your production credentials!"
    echo "   Then re-run this script."
    exit 1
fi

# 2. Put application into maintenance mode
echo "🔧 Enabling maintenance mode..."
php artisan down --retry=60

# 3. Pull latest code (if using git)
if [ -d .git ]; then
    echo "📥 Pulling latest changes..."
    git pull origin main
fi

# 4. Install PHP dependencies (no dev)
echo "📦 Installing Composer dependencies..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# 5. Install & build frontend assets
echo "🎨 Installing npm dependencies..."
npm ci --omit=dev
echo "🔨 Building frontend assets..."
npm run build

# 6. Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# 7. Clear & rebuild all Laravel caches
echo "⚡ Optimizing Laravel..."
php artisan optimize
php artisan view:cache
php artisan route:cache
php artisan config:cache
php artisan event:cache

# 8. Create storage symlink
echo "🔗 Creating storage symlink..."
php artisan storage:link --force

# 9. Restart queue worker
echo "🔄 Restarting queue worker..."
php artisan queue:restart

# 10. Take application out of maintenance mode
echo "✅ Disabling maintenance mode..."
php artisan up

echo "============================================"
echo "✅ BITSI Dispatch deployed successfully!"
echo "============================================"
echo ""
echo "📌 Post-deployment checks:"
echo "   - Verify site loads at APP_URL"
echo "   - Check /storage symlink works"
echo "   - Run: php artisan about"
echo "   - Monitor logs: tail -f storage/logs/laravel.log"