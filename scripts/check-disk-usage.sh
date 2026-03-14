#!/usr/bin/env bash
#
# Run on the server to find where disk is used and why.
# Usage: cd /var/www/telcoflo_studio && bash scripts/check-disk-usage.sh
# For full scan (slower): bash scripts/check-disk-usage.sh --full
#
set -e

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
FULL_SCAN="${1:-}"

echo "=============================================="
echo "DISK USAGE DIAGNOSTIC - $(date -Iseconds 2>/dev/null || date)"
echo "=============================================="
echo ""

echo "--- 1. Overall disk usage (df) ---"
df -h
echo ""

echo "--- 2. Mount point for app (often root /) ---"
df -h "$APP_DIR" 2>/dev/null || df -h /
echo ""

echo "--- 3. Total size of project directory ---"
du -sh "$APP_DIR" 2>/dev/null || true
echo ""

echo "--- 4. Top-level folders in project (one level) ---"
du -sh "$APP_DIR"/*/ 2>/dev/null | sort -rh
echo ""

echo "--- 5. Storage breakdown (logs, framework, app) ---"
for d in logs framework framework/cache framework/sessions framework/views app app/public; do
  [ -d "$APP_DIR/storage/$d" ] && du -sh "$APP_DIR/storage/$d" 2>/dev/null
done
echo ""

echo "--- 6. Largest files in storage/logs ---"
find "$APP_DIR/storage/logs" -maxdepth 1 -type f 2>/dev/null | while read -r f; do
  du -h "$f"
done | sort -rh
echo ""

echo "--- 7. Laravel log file sizes (if any) ---"
ls -la "$APP_DIR/storage/logs"/laravel*.log 2>/dev/null || echo "(no laravel log files)"
ls -la "$APP_DIR/storage/logs"/ 2>/dev/null
echo ""

echo "--- 8. Queue / cache / session counts (approx) ---"
[ -d "$APP_DIR/storage/framework/cache/data" ] && echo "Cache entries: $(find "$APP_DIR/storage/framework/cache/data" -type f 2>/dev/null | wc -l)"
[ -d "$APP_DIR/storage/framework/sessions" ] && echo "Session files: $(find "$APP_DIR/storage/framework/sessions" -type f 2>/dev/null | wc -l)"
echo ""

if [ "$FULL_SCAN" = "--full" ]; then
  echo "--- 9. Full tree (largest dirs under project, may be slow) ---"
  du -hx "$APP_DIR" 2>/dev/null | sort -rh | head -40
else
  echo "--- 9. Largest dirs under storage (quick) ---"
  du -h "$APP_DIR/storage" 2>/dev/null | sort -rh | head -20
  echo ""
  echo "Run with --full to see full project tree (slower)."
fi

echo "--- 10. System log size (if readable) ---"
for d in /var/log /var/log/nginx /var/log/mysql; do
  [ -d "$d" ] && sudo du -sh "$d" 2>/dev/null || true
done
echo ""

echo "--- 11. Log-related .env (why logs may be huge) ---"
if [ -f "$APP_DIR/.env" ]; then
  grep -E '^LOG_CHANNEL=|^LOG_LEVEL=|^BROADCAST_DRIVER=' "$APP_DIR/.env" 2>/dev/null || true
  echo "  -> LOG_LEVEL=debug + LOG_CHANNEL=daily/single/stack = lots of log lines."
  echo "  -> BROADCAST_DRIVER=log writes broadcast events to the log."
else
  echo "  (.env not found or not readable)"
fi
echo ""

echo "=============================================="
echo "WHERE & WHY:"
echo "  - Sections 5–7: Laravel logs (storage/logs). Big if LOG_LEVEL=debug or heavy traffic."
echo "  - Section 8: Cache/sessions (usually small unless misconfigured)."
echo "  - Section 10: System logs (nginx, mysql, syslog) can fill /var."
echo "  - Set LOG_LEVEL=error and LOG_CHANNEL=daily in .env to reduce growth."
echo "=============================================="
