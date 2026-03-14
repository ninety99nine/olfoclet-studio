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

# User that runs queue workers, cron, and owns storage (must match Supervisor/cron)
APP_USER="www-data"

echo "==> 1. Stopping queue workers..."
supervisorctl stop all

echo "==> 2. Pulling main..."
# Run git pull as root so it can always write to .git
cd "$APP_DIR" && git pull origin main

echo "==> 3. Running migrations..."
cd "$APP_DIR"
php artisan migrate --force

echo "==> 4. Resetting stuck processing locks..."
# This ensures no one is 'stuck' from the previous crash before we start workers
php artisan db:seed --class=ResetStuckProcessingLocks

echo "==> 5. Trimming Laravel logs (keep last 2 days, free disk)..."
find "$APP_DIR/storage/logs" -maxdepth 1 -name 'laravel-*.log' -mtime +2 -delete 2>/dev/null || true
# Truncate current log if huge (logrotate may not have run yet)
for f in "$APP_DIR/storage/logs"/laravel-*.log; do
  [ -f "$f" ] && [ "$(stat -c%s "$f" 2>/dev/null)" -gt 52428800 ] && : > "$f"
done

echo "==> 6. Clearing caches..."
cd "$APP_DIR"
php artisan cache:clear
php artisan config:clear
php artisan event:clear
php artisan route:clear
php artisan view:clear

echo "==> 7. Rebuilding caches..."
# 'optimize' automatically runs config:cache, route:cache, view:cache, and event:cache.
php artisan optimize

echo "==> 8. Fixing storage & bootstrap/cache permissions..."
# CRITICAL: After cache commands, hand ownership to APP_USER so workers/cron can write
chown -R "$APP_USER":"$APP_USER" "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

echo "==> 9. Pruning failed and stuck jobs..."
# We prune failed jobs older than 72 hours and batches older than 24 hours
sudo -u "$APP_USER" php artisan queue:prune-failed --hours=72
sudo -u "$APP_USER" php artisan queue:prune-batches --hours=24 --unfinished=72

echo "==> 10. Restarting queue workers (to load new code and avoid class mismatches)..."
cd "$APP_DIR"
sudo -u "$APP_USER" php artisan queue:restart

echo "==> 11. Reloading Supervisor and starting queue workers..."
supervisorctl reread
supervisorctl update
supervisorctl start all

echo "==> 12. Checking services..."
for svc in nginx mysql supervisor; do
  if systemctl is-active --quiet "$svc" 2>/dev/null; then
    echo "  $svc: running"
  else
    echo "  $svc: not running (run: systemctl status $svc)"
  fi
done

for f in /etc/init.d/php*-fpm; do
  [ -e "$f" ] && echo "  $(basename "$f"): $(systemctl is-active "$(basename "$f")" 2>/dev/null || echo '?')"
done

echo "==> 13. Verifying scheduler (no fatal error)..."
sudo -u "$APP_USER" php artisan schedule:run

echo "==> 14. Applying Logrotate Configuration..."
# Copy the config and enforce strict root permissions
cp "$APP_DIR/config/logrotate-telcoflo.conf" /etc/logrotate.d/telcoflo
chown root:root /etc/logrotate.d/telcoflo
chmod 644 /etc/logrotate.d/telcoflo

echo ""
echo "Deploy finished. Check the site: http://105.235.242.227"
echo "Note: Ensure LOG_LEVEL=error (or warning) is set in your server's .env file."
