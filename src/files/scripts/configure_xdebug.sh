#!/bin/bash

# Check if container name is provided
if [ -z "$1" ]; then
    echo "Usage: $0 <LXC_CONTAINER_NAME>"
    exit 1
fi

# Variables
LXC_CONTAINER_NAME="$1"
HOST_MACHINE_IP=$(hostname -I | awk '{print $1}')  # Get the host machine's IP

# Function to determine the HTTP server
get_http_server() {
    if lxc exec $LXC_CONTAINER_NAME -- systemctl is-active --quiet apache2; then
        echo "apache2"
    elif lxc exec $LXC_CONTAINER_NAME -- systemctl is-active --quiet nginx; then
        echo "nginx"
    else
        echo "unknown"
    fi
}

# Function to determine the PHP version
get_php_version() {
    lxc exec $LXC_CONTAINER_NAME -- php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;'
}

# Determine the PHP version
PHP_VERSION=$(get_php_version)

# Determine the PHP-FPM service based on the current PHP version
PHP_FPM_SERVICE="php$PHP_VERSION-fpm"

# Check if the PHP-FPM service is running
if lxc exec $LXC_CONTAINER_NAME -- systemctl is-active --quiet $PHP_FPM_SERVICE; then
    XDEBUG_INI_PATH="/etc/php/$PHP_VERSION/fpm/conf.d/20-xdebug.ini"
else
    XDEBUG_INI_PATH="/etc/php/$PHP_VERSION/mods-available/xdebug.ini"
    PHP_FPM_SERVICE=""
fi

# Update xdebug.ini in the LXC container
lxc exec $LXC_CONTAINER_NAME -- bash -c "echo -e '\
zend_extension=xdebug.so\n\
xdebug.mode=debug\n\
xdebug.client_host=$HOST_MACHINE_IP\n\
xdebug.idekey=XDEBUG\n\
xdebug.log_level=7\n\
xdebug.log=/var/www/source/var/log/xdebug.log\n\
xdebug.start_with_request=yes\n\
xdebug.discover_client_host=false\n\
xdebug.client_port=9003' > $XDEBUG_INI_PATH"

# Reload the PHP-FPM service if it's running
if [ -n "$PHP_FPM_SERVICE" ]; then
    lxc exec $LXC_CONTAINER_NAME -- systemctl reload $PHP_FPM_SERVICE
fi

# Determine the HTTP server and reload it if recognized
HTTP_SERVER=$(get_http_server)
if [ "$HTTP_SERVER" != "unknown" ]; then
    lxc exec $LXC_CONTAINER_NAME -- systemctl reload $HTTP_SERVER
fi

echo "Xdebug settings updated and services reloaded in container $LXC_CONTAINER_NAME."
