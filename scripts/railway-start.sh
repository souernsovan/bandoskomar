#!/bin/sh
set -eu

# Railway provides PORT at runtime. Normalize it so Laravel's serve command
# always receives a numeric port, even if the variable is missing or malformed.
PORT="${PORT:-8000}"
case "$PORT" in
    ''|*[!0-9]*)
        PORT=8000
        ;;
esac

exec php artisan serve --host=0.0.0.0 --port="$PORT"
