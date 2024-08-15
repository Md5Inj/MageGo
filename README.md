# Magerun2 Custom Commands Deployer

This script deploys custom Magerun2 commands to a Magento installation. It checks if the current directory is the Magento root, extracts necessary files, and places them in the correct locations.

## Features

- **Magento Root Check**: Verifies that the script is run from the Magento root directory.
- **File Extraction**: Extracts files from the archive, excluding `index.php`, and preserves directory structure.
- **File Deployment**: Moves custom Magerun2 commands and configurations to their respective locations.

## Usage

1. **Navigate to Magento Root**: Ensure you are in the root directory of your Magento installation.
2. **Download commands deployer**: Download command deployer into Magento root
2. **Run the Script**: Execute the script to deploy custom Magerun2 commands.

   ```bash
   php ./deploy_commands.phar

## Custom Commands

- **`dev:git:branch`**: This command shelves current changes. Fetches data from the repository. Changes the current branch.
  1. **Enable Maintenance Mode**:
     - Runs `sys:maintenance --on` to enable maintenance mode before making changes.

  2. **Update Git Branch**:
     - **Stash Changes**: Executes `git stash` to save any uncommitted changes.
     - **Fetch Updates**: Runs `git fetch --all` to fetch all updates from the remote repository.
     - **Checkout Remote Branch**: Uses `git checkout -f origin/[branch]` to switch to the remote branch.
     - **Checkout Local Branch**: Executes `git checkout -f [branch]` to switch to the local branch.
     - **Pull Latest Changes**: Runs `git pull` to pull the latest changes from the remote branch.

  3. **Disable Maintenance Mode**:
     - Runs `sys:maintenance --off` to turn off maintenance mode after updates are complete.


- **`dev:deploy`**: This command automates the deployment process for Magento. It handles tasks such as deploying code updates, syncing files, and performing necessary deployment steps to ensure your Magento installation is up-to-date.
  1. **Upgrade Setup**:
     - Runs `setup:upgrade` to apply any database schema updates and configuration changes.

  2. **Compile Dependency Injection**:
     - Executes `setup:di:compile` to compile dependency injection configurations.


- **`dev:deploy:full`**: This comprehensive deployment command performs a full deployment of the Magento installation. It includes code updates, database migrations, and other essential tasks to ensure that the Magento environment is fully updated and functional.
  This command performs a full deployment of the Magento installation. It handles the following tasks:
  1. **Branch Detection and Switching**:
     - Executes the `dev:git:branch` command to switch to the specified Git branch. Defaults to `master` if no branch is provided.

  2. **Composer Installation**:
     - Runs `composer install` to ensure that all PHP dependencies are correctly installed.

  3. **User Installation**:
     - Executes the `dev:install:user` command to set up user accounts within Magento.

  4. **Magento Setup and Compilation**:
     - Runs `setup:upgrade` to apply any database schema changes.
     - Executes `setup:di:compile` to compile dependency injection configuration.


- **`dev:install:user`**: This command simplifies the creation and configuration of user accounts within Magento. It ensures that new users are properly set up with the necessary permissions and configurations, streamlining user management.
  1. **Delete Existing Admin User**:
     - Executes `admin:user:delete admin -f` to forcefully delete the existing admin user with the username `admin`.

  2. **Create New Admin User**:
     - Runs `admin:user:create` to create a new admin user with the following details:
        - **Username**: `admin`
        - **Email**: `admin@test.com`
        - **Password**: `admin123`
        - **First Name**: `Admin`
        - **Last Name**: `Admin`
