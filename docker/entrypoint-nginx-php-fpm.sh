#!/bin/sh
set -e
# PHP-FPM in background; nginx stays foreground as main process
php-fpm -D
exec nginx -g 'daemon off;'
