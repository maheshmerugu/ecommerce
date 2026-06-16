#!/bin/bash
# cPanel post-pull deployment script for Laravel
# Runs from repository root (cPanel Git Version Control)

set -u

# Exact cPanel deployment path for account p865jltij03r
DEPLOYPATH="/home/p865jltij03r/public_html/fourwheels.co.in"

# Fallback: auto-detect from script location if hardcoded path doesn't exist
if [ ! -d "$DEPLOYPATH" ]; then
    DEPLOYPATH="$(cd "$(dirname "$0")/.." && pwd)"
fi

cd "$DEPLOYPATH"

LOG_FILE="$DEPLOYPATH/storage/logs/cpanel-deploy.log"
mkdir -p "$DEPLOYPATH/storage/framework/sessions" \
         "$DEPLOYPATH/storage/framework/views" \
         "$DEPLOYPATH/storage/framework/cache/data" \
         "$DEPLOYPATH/storage/logs" \
         "$DEPLOYPATH/bootstrap/cache"

exec >> "$LOG_FILE" 2>&1
echo ""
echo "========== Deploy started: $(date) =========="
echo "DEPLOYPATH: $DEPLOYPATH"

# ── Find PHP binary (cPanel EA-PHP) ──
PHP_BIN=""
for candidate in \
    /usr/local/bin/ea-php83 \
    /usr/local/bin/ea-php82 \
    /usr/local/bin/ea-php81 \
    /usr/local/bin/ea-php80 \
    /opt/cpanel/ea-php83/root/usr/bin/php \
    /opt/cpanel/ea-php82/root/usr/bin/php \
    /opt/cpanel/ea-php81/root/usr/bin/php \
    /usr/bin/ea-php82 \
    /usr/bin/php \
    php
do
    if [ -x "$candidate" ]; then
        PHP_BIN="$candidate"
        break
    fi
    resolved=$(command -v "$candidate" 2>/dev/null || true)
    if [ -n "$resolved" ] && [ -x "$resolved" ]; then
        PHP_BIN="$resolved"
        break
    fi
done

if [ -z "$PHP_BIN" ]; then
    echo "ERROR: PHP binary not found"
    exit 1
fi
echo "PHP: $PHP_BIN ($($PHP_BIN -v | head -1))"

# ── Find Composer ──
COMPOSER_BIN=""
for candidate in /usr/local/bin/composer /usr/bin/composer composer composer2; do
    resolved=$(command -v "$candidate" 2>/dev/null || true)
    if [ -n "$resolved" ] && [ -x "$resolved" ]; then
        COMPOSER_BIN="$resolved"
        break
    fi
done

if [ -z "$COMPOSER_BIN" ]; then
    echo "ERROR: Composer not found"
    exit 1
fi
echo "Composer: $COMPOSER_BIN"

# ── Environment file (only create if missing; never overwrite) ──
if [ ! -f "$DEPLOYPATH/.env" ]; then
    echo "Creating .env from .env.example"
    cp -n "$DEPLOYPATH/.env.example" "$DEPLOYPATH/.env"
    "$PHP_BIN" "$DEPLOYPATH/artisan" key:generate --force
    echo "WARNING: New .env created — update DB credentials in cPanel File Manager"
else
    echo ".env already exists — skipping key:generate"
fi

# ── Composer dependencies ──
echo "Running composer install..."
"$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction --working-dir="$DEPLOYPATH"

# ── Laravel artisan tasks ──
echo "Running migrations..."
"$PHP_BIN" "$DEPLOYPATH/artisan" migrate --force || echo "WARN: migrate failed (check .env DB settings)"

echo "Storage link..."
"$PHP_BIN" "$DEPLOYPATH/artisan" storage:link 2>/dev/null || true

if [ ! -e "$DEPLOYPATH/public/storage" ]; then
    echo "WARN: public/storage missing — uploads will not be web-accessible"
else
    echo "public/storage link OK"
fi

echo "Clearing all caches (config, route, view, app)..."
"$PHP_BIN" "$DEPLOYPATH/artisan" config:clear  || true
"$PHP_BIN" "$DEPLOYPATH/artisan" route:clear   || true
"$PHP_BIN" "$DEPLOYPATH/artisan" view:clear    || true
"$PHP_BIN" "$DEPLOYPATH/artisan" cache:clear   || true

echo "Rebuilding caches..."
"$PHP_BIN" "$DEPLOYPATH/artisan" config:cache  || true
"$PHP_BIN" "$DEPLOYPATH/artisan" route:cache   || true
"$PHP_BIN" "$DEPLOYPATH/artisan" view:cache    || true

# ── Permissions ──
chmod -R 775 "$DEPLOYPATH/storage" "$DEPLOYPATH/bootstrap/cache" 2>/dev/null || true

# ── Reset git working tree so next cPanel deploy is not blocked ──
# ("No uncommitted changes exist on the checked-out branch")
if [ -d "$DEPLOYPATH/.git" ]; then
    git -C "$DEPLOYPATH" reset --hard HEAD 2>/dev/null || true
    git -C "$DEPLOYPATH" clean -fd \
        -e .env \
        -e vendor \
        -e storage \
        -e node_modules \
        2>/dev/null || true
    echo "Git working tree cleaned"
fi

echo "========== Deploy finished: $(date) =========="
exit 0
