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

# Capture the original user who ran the sudo command (e.g., 'sysop')
ORIGINAL_USER="${SUDO_USER:-$(whoami)}"

echo "==> 1. Stopping queue workers..."
supervisorctl stop all

echo "==> 2. Pulling main..."
# Run git pull as the original user to prevent the .git folder from becoming root-owned
cd "$APP_DIR" && sudo -u "$ORIGINAL_USER" git pull origin main

echo "==> 3. Clearing caches..."
cd "$APP_DIR"
php artisan cache:clear
php artisan config:clear
php artisan event:clear
php artisan route:clear
php artisan view:clear

echo "==> 4. Rebuilding caches..."
# 'optimize' automatically runs config:cache, route:cache, view:cache, and event:cache.
php artisan optimize

echo "==> 5. Fixing storage & bootstrap/cache permissions..."
# CRITICAL FIX: This MUST happen AFTER the cache commands so the newly
# generated files are handed back to the web server and cron user!
chown -R "$ORIGINAL_USER":www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

echo "==> 6. Pruning failed and stuck jobs..."
# Run as the original user so artisan doesn't accidentally spawn root-owned log/cache files
sudo -u "$ORIGINAL_USER" php artisan queue:prune-failed --hours=72
sudo -u "$ORIGINAL_USER" php artisan queue:prune-batches --hours=24 --unfinished=72

echo "==> 7. Reloading Supervisor and starting queue workers..."
supervisorctl reread
supervisorctl update  # Added update to actually apply any new config changes
supervisorctl start all

echo "==> 8. Checking services..."
for svc in nginx mysql supervisor; do
  if systemctl is-active --quiet "$svc" 2>/dev/null; then
    echo "  $svc: running"
  else
    echo "  $svc: not running (run: sudo systemctl status $svc)"
  fi
done

for f in /etc/init.d/php*-fpm; do
  [ -e "$f" ] && echo "  $(basename "$f"): $(systemctl is-active "$(basename "$f")" 2>/dev/null || echo '?')"
done

echo "==> 9. Verifying scheduler (no fatal error)..."
# Run as the original user to accurately test if the Cron permissions are correct
sudo -u "$ORIGINAL_USER" php artisan schedule:run

echo ""
echo "Deploy finished. Check the site: http://105.235.242.227"
echo ""
echo "One-time: To cap Laravel logs at 100MB and keep 3 rotations, install logrotate:"
echo "  sudo cp $APP_DIR/config/logrotate-telcoflo.conf /etc/logrotate.d/telcoflo"
echo "On the server .env set LOG_LEVEL=error (or warning) for production."
