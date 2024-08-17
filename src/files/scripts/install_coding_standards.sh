#!/bin/bash

# Check if container name is provided
if [ -z "$1" ]; then
    echo "Usage: $0 <LXC_CONTAINER_NAME> [DIRECTORY_NAME]"
    exit 1
fi

# Variables
LXC_CONTAINER_NAME="$1"
DIRECTORY_NAME="${2:-codesniff}"  # Default to folder "codesniff" if other directory not provided

# Full path for the directory
FULL_DIRECTORY_PATH="/var/www/$DIRECTORY_NAME"

# Create the directory in the LXC container
lxc exec $LXC_CONTAINER_NAME --env HOME=/var/www --user 33 --group 33 -- bash -c "mkdir -p $FULL_DIRECTORY_PATH"

# Initialize a Composer project with default values
lxc exec $LXC_CONTAINER_NAME --env HOME=/var/www --user 33 --group 33 -- bash -c "cd $FULL_DIRECTORY_PATH && composer init --no-interaction --name=\"your/package\" --description=\"A default package\" --author=\"Your Name <your.email@example.com>\" --license=\"MIT\" --require=\"php:^7.4|^8.0\""

# Add the plugin to allowed plugins
lxc exec $LXC_CONTAINER_NAME --env HOME=/var/www --user 33 --group 33 -- bash -c "composer config --global allow-plugins.dealerdirect/phpcodesniffer-composer-installer true"

# Install required Composer packages
lxc exec $LXC_CONTAINER_NAME --env HOME=/var/www --user 33 --group 33 -- bash -c "cd $FULL_DIRECTORY_PATH && composer require dealerdirect/phpcodesniffer-composer-installer"
lxc exec $LXC_CONTAINER_NAME --env HOME=/var/www --user 33 --group 33 -- bash -c "cd $FULL_DIRECTORY_PATH && composer require magento/magento-coding-standard"
lxc exec $LXC_CONTAINER_NAME --env HOME=/var/www --user 33 --group 33 -- bash -c "cd $FULL_DIRECTORY_PATH && composer require phpmd/phpmd"

# Set up aliases for phpcs and phpmd
lxc exec $LXC_CONTAINER_NAME --env HOME=/var/www --user 33 --group 33 -- bash -c "echo 'alias phpcs=\"/var/www/$DIRECTORY_NAME/vendor/bin/phpcs --standard=Magento2\"' >> ~/.bashrc"
lxc exec $LXC_CONTAINER_NAME --env HOME=/var/www --user 33 --group 33 -- bash -c "echo 'alias phpmd=\"/var/www/$DIRECTORY_NAME/vendor/bin/phpmd\"' >> ~/.bashrc"

# Reload bashrc to apply aliases immediately
lxc exec $LXC_CONTAINER_NAME --env HOME=/var/www --user 33 --group 33 -- bash -c "source ~/.bashrc"

echo "Setup completed in container $LXC_CONTAINER_NAME. Setup done in the $FULL_DIRECTORY_PATH folder. Aliases 'phpcs' and 'phpmd' have been added. You can run 'phpcs <path>' to specify the path."
