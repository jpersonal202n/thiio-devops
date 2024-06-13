#!/bin/sh
echo "start....."
# Check if the random service is running
if ! nc -z th_random_http_service 80; then
    echo "Random service is not running, removing /thiio configuration."
    sed -i '/location \/thiio {/,/}/d' /etc/nginx/conf.d/default.conf
fi

# Start Nginx
exec nginx -g 'daemon off;'
