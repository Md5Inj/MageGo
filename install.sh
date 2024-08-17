#!/bin/bash

# Variables
REPO="Md5Inj/MageGo"
INSTALL_DIR="$HOME/bin"
PHAR_NAME="magego.phar"
ALIAS_NAME="magego"
SHELL_CONFIG="$HOME/.bashrc"  # Default for Bash; change if using a different shell

# Create the installation directory if it doesn't exist
mkdir -p "${INSTALL_DIR}"

# Get the latest release tag from GitHub API
LATEST_TAG=$(curl -s "https://api.github.com/repos/${REPO}/releases/latest" | grep 'tag_name' | cut -d '"' -f 4)

# Construct the download URL for the PHAR file
DOWNLOAD_URL="https://github.com/${REPO}/releases/download/${LATEST_TAG}/${PHAR_NAME}"

# Download the latest PHAR file
echo "Downloading ${PHAR_NAME} from ${DOWNLOAD_URL}..."
curl -L -o "${INSTALL_DIR}/${PHAR_NAME}" "${DOWNLOAD_URL}"

# Make the PHAR file executable
chmod +x "${INSTALL_DIR}/${PHAR_NAME}"

# Add the installation directory to PATH if not already present
if ! echo "$PATH" | grep -q "${INSTALL_DIR}"; then
    echo "Adding ${INSTALL_DIR} to PATH..."
    echo "export PATH=\"$HOME/bin:\$PATH\"" >> "$SHELL_CONFIG"
    echo "export PATH=\"$HOME/bin:\$PATH\"" >> "$HOME/.bash_profile"  # Also add to .bash_profile for login shells
    source "$SHELL_CONFIG"
fi

# Add alias for the PHAR command if not already present
if ! grep -q "alias ${ALIAS_NAME}=" "$SHELL_CONFIG"; then
    echo "Adding alias for ${ALIAS_NAME}..."
    echo "alias ${ALIAS_NAME}='php ${INSTALL_DIR}/${PHAR_NAME}'" >> "$SHELL_CONFIG"
    echo "alias ${ALIAS_NAME}='php ${INSTALL_DIR}/${PHAR_NAME}'" >> "$HOME/.bash_profile"  # Also add to .bash_profile for login shells
    source "$SHELL_CONFIG"
fi

# Print instructions
echo "Installation complete!"
echo ""
echo "To finalize the setup, add the following line to your shell's startup script:"
echo ""
echo "export PATH=\"$HOME/bin:\$PATH\""
echo "alias ${ALIAS_NAME}='php ${INSTALL_DIR}/${PHAR_NAME}'"
echo ""
echo "This script has already added these lines to your $SHELL_CONFIG."
echo ""
echo "To apply the changes in your current terminal session, run:"
echo "source $SHELL_CONFIG"
echo ""
echo "You can now use the command: ${ALIAS_NAME}"
