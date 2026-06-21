#!/bin/sh
set -eu

composer install --no-dev --optimize-autoloader
npm install --no-audit --no-fund
npm run build
