#!/bin/sh
set -eu

php artisan migrate --force

if [ "${BOOTSTRAP_DEMO_ADMIN:-false}" = "true" ]; then
    php artisan db:seed --class=RenderDemoAdminSeeder --force
fi

exec apache2-foreground
