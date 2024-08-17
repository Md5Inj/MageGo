#!/bin/bash

# Variables
LXC_CONTAINER_NAME=$1
SSH_USER=${2:-www-data}
SSH_KEY_PATH=${3:-~/.ssh/id_rsa}
SSH_PUB_KEY_PATH="${SSH_KEY_PATH}.pub"

# Check if lxc command exists
if ! command -v lxc &> /dev/null
then
    echo "lxc command not found. Please install LXC."
    exit 1
fi

# Check if the SSH key files exist
if [ ! -f "$SSH_KEY_PATH" ]; then
    echo "SSH private key not found at $SSH_KEY_PATH."
    exit 1
fi

if [ ! -f "$SSH_PUB_KEY_PATH" ]; then
    echo "SSH public key not found at $SSH_PUB_KEY_PATH."
    exit 1
fi

# Get the user's home directory in the container
USER_HOME=$(lxc exec "$LXC_CONTAINER_NAME" -- bash -c "eval echo ~$SSH_USER")

# Create the .ssh directory in the user's home directory if it doesn't exist
lxc exec "$LXC_CONTAINER_NAME" -- mkdir -p "$USER_HOME/.ssh"

# Copy the public key to the container's authorized_keys
lxc file push "$SSH_PUB_KEY_PATH" "$LXC_CONTAINER_NAME$USER_HOME/.ssh/authorized_keys"

# Copy the private key to the container's .ssh directory
lxc file push "$SSH_KEY_PATH" "$LXC_CONTAINER_NAME$USER_HOME/.ssh/id_rsa"

# Set appropriate permissions
lxc exec "$LXC_CONTAINER_NAME" -- chown -R "$SSH_USER":"$SSH_USER" "$USER_HOME/.ssh"
lxc exec "$LXC_CONTAINER_NAME" -- chmod 700 "$USER_HOME/.ssh"
lxc exec "$LXC_CONTAINER_NAME" -- chmod 600 "$USER_HOME/.ssh/authorized_keys"
lxc exec "$LXC_CONTAINER_NAME" -- chmod 600 "$USER_HOME/.ssh/id_rsa"

# Add ssh-agent and ssh-add command to .bash_profile of the user
lxc exec "$LXC_CONTAINER_NAME" -- bash -c "echo 'eval \$(ssh-agent -s) && ssh-add $USER_HOME/.ssh/id_rsa' >> $USER_HOME/.bash_profile"

# Start ssh-agent with the -s option and capture its output to source the environment variables
lxc exec "$LXC_CONTAINER_NAME" -- bash -c "eval \$(ssh-agent -s) && ssh-add $USER_HOME/.ssh/id_rsa"

# Restart the SSH service in the container
lxc exec "$LXC_CONTAINER_NAME" bash -- service ssh restart

echo "SSH key authentication has been set up for $SSH_USER on $LXC_CONTAINER_NAME."

