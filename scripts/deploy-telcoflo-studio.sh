#!/usr/bin/env bash
#
# Deploy script for TelcoFlo Studio on the server.
# Run on the server after SSH (requires sudo password):
#
#   cd /var/www/telcoflo_studio && sudo bash scripts/deploy-telcoflo-studio.sh
#
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="${APP_DIR:-$(cd "$SCRIPT_DIR/.." && pwd)}"

echo "==> 1. Stopping queue workers..."
sudo supervisorctl stop all

echo "==> 2. Pulling main..."
# Pull as root (script runs under sudo) so .git stays writable; fix ownership of writable dirs only
cd "$APP_DIR" && git pull origin main
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true

echo "==> 3. Clearing caches..."
cd "$APP_DIR" && sudo php artisan cache:clear
sudo php artisan config:clear
sudo php artisan event:clear
sudo php artisan route:clear
sudo php artisan view:clear

echo "==> 4. Rebuilding caches..."
sudo php artisan config:cache
sudo php artisan event:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo php artisan optimize

echo "==> 5. Starting queue workers..."
sudo supervisorctl start all

echo "==> 6. Checking services..."
for svc in nginx mysql supervisord supervisor; do
  if sudo systemctl is-active --quiet "$svc" 2>/dev/null; then
    echo "  $svc: running"
  else
    echo "  $svc: not running (run: sudo systemctl status $svc)"
  fi
done
for f in /etc/init.d/php*-fpm; do
  [ -e "$f" ] && echo "  $(basename "$f"): $(sudo systemctl is-active "$(basename "$f")" 2>/dev/null || echo '?')"
done

echo "==> 7. Verifying scheduler (no fatal error)..."
cd "$APP_DIR" && php artisan schedule:run

echo ""
echo "Deploy finished. Check the site: http://105.235.242.227"
