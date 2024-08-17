#!/bin/bash

# Function to parse JSON without jq
parse_json() {
    echo "$1" | sed -e 's/^[ \t]*//' -e 's/[ \t]*$//' -e 's/^[^{]*{//' -e 's/}[^}]*$//' -e 's/[^:]*:[ \t]*"*\([^"]*\)"*\s*$/\1/'
}

# Function to check if nvm is installed
check_nvm() {
    if [ -d "$HOME/.nvm" ]; then
        # Source nvm script
        export NVM_DIR="$HOME/.nvm"
        [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" # This loads nvm
        [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" # This loads nvm bash_completion

        # Check if nvm is available
        if command -v nvm &> /dev/null; then
            return 0
        else
            return 1
        fi
    else
        return 1
    fi
}

# Function to extract the theme name from registration.php
extract_theme_name() {
    local file_path="$1"

    # Remove all spaces and line breaks from the file content
    local php_code=$(sed ':a;N;$!ba;s/[[:space:]]//g' "$file_path")

    # Extract the theme name using sed
    local theme_name=$(echo "$php_code" | sed -n "s/.*ComponentRegistrar::register(ComponentRegistrar::THEME,'frontend\/\([^']*\)',__DIR__);.*/\1/p")

    # Output the extracted theme name
    echo "$theme_name"
}

# Function to list themes in a given directory, excluding specific patterns
list_themes() {
    local dir=$1
    local temp_file=$(mktemp)
    find "$dir" -type f -name 'registration.php' -print0 | while IFS= read -r -d '' reg_file; do
        # Exclude test directories and Magento themes
        if [[ ! "$reg_file" =~ (/dev/tests|/Test/Unit/) ]]; then
            theme_name=$(extract_theme_name "$reg_file")
            if [ -n "$theme_name" ]; then
                # Exclude Magento default themes
                if [[ ! "$theme_name" =~ ^(Magento\/(luma|blank)|adminhtml\/Magento\/(backend|spectrum)) ]]; then
                    echo "$theme_name" >> "$temp_file"
                fi
            fi
        fi
    done

    serialized_array=$(tr '\n' ' ' < "$temp_file")
    rm "$temp_file"
}

# Check if nvm is installed
if ! check_nvm; then
    echo "nvm not found. Installing nvm..."

    # Install nvm
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.0/install.sh | bash

    # Add nvm to bash profile
    echo 'export NVM_DIR="$HOME/.nvm"' >> ~/.bash_profile
    echo '[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" # This loads nvm' >> ~/.bash_profile
    echo '[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" # This loads nvm bash_completion' >> ~/.bash_profile

    # Source bash profile
    source ~/.bash_profile

    # Install Node.js LTS version
    nvm install --lts
else
    echo "nvm is already installed."
fi

# Source nvm in the current shell
source ~/.bash_profile

# Check if the current directory is a Magento directory
if [ ! -d "app" ] || [ ! -d "vendor" ] || [ ! -f "bin/magento" ]; then
    echo "Current directory is not a Magento root directory. Please navigate to the Magento root directory and try again."
    exit 1
fi

# Rename grunt-config.json.sample to grunt-config.json
if [ -f "grunt-config.json.sample" ]; then
    mv grunt-config.json.sample grunt-config.json
    echo "Renamed grunt-config.json.sample to grunt-config.json."
else
    echo "grunt-config.json.sample not found."
fi

# Rename package.json.sample to package.json
if [ -f "package.json.sample" ]; then
    mv package.json.sample package.json
    echo "Renamed package.json.sample to package.json."
else
    echo "package.json.sample not found."
fi

# Rename Gruntfile.js.sample to Gruntfile.js
if [ -f "Gruntfile.js.sample" ]; then
    mv Gruntfile.js.sample Gruntfile.js
    echo "Renamed Gruntfile.js.sample to Gruntfile.js."
else
    echo "Gruntfile.js.sample not found."
fi

# Install npm packages
echo "Installing npm packages..."
npm install

# Install grunt globally
echo "Installing grunt globally..."
npm install -g grunt

# Create .js file for themes based on grunt-config.json
if [ -f "grunt-config.json" ]; then
    # Extract the themes file path from grunt-config.json
    themesFile=$(grep -oP '"themes"\s*:\s*"\K[^"]+' grunt-config.json)

    if [ -z "$themesFile" ]; then
        echo "No 'themes' key found in grunt-config.json."
        exit 1
    fi

    # Append .js to the themes file path
    themesJsFile="${themesFile}.js"

    echo "Themes file specified: $themesJsFile"

    # Create the .js file if it doesn't exist
    if [ ! -f "$themesJsFile" ]; then
        echo "Creating $themesJsFile"
        mkdir -p "$(dirname "$themesJsFile")"
        # Write some default JavaScript content
        echo > "$themesJsFile"

        # List themes in app/design/frontend, excluding specific patterns
        themes_dir_app="app/design/frontend"
        if [ -d "$themes_dir_app" ]; then
            list_themes "$themes_dir_app"
        else
            echo "Themes directory $themes_dir_app does not exist."
            exit 1
        fi

        IFS=' ' read -r -a themesArray <<< "$serialized_array"

        # Write array as JavaScript module.exports to file
        {
            echo "module.exports = {"
            for theme in "${themesArray[@]}"; do
                # Extract the theme name after the slash
                theme_name_after_slash=$(echo "$theme" | awk -F '/' '{print $2}')

                # Print the formatted JavaScript object with specific files
                echo "    ${theme_name_after_slash}: {"
                echo "        area: 'frontend',"
                echo "        name: '${theme}',"
                echo "        locale: 'en_US',"
                echo "        files: ["
                echo "            'css/styles-m',"
                echo "            'css/styles-l'"
                echo "        ],"
                echo "        dsl: 'less'"
                echo "    },"
            done
            echo "};"
        } > "$themesJsFile"
    else
        echo "$themesJsFile already exists."
    fi
else
    echo "grunt-config.json not found. Skipping theme file creation."
fi

echo "Setup complete."
exit 0
