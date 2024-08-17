# MageGo

This script deploys custom Magerun2 commands to a Magento installation. It checks if the current directory is the Magento root, extracts necessary files, and places them in the correct locations.

This script provides the possibility to set up with one click:
- Grunt
- Xdebug
- SSH
- Coding standards (PHPCS, PHPMD)

## Usage

1. **Install MageGo**
2. **Run it**
   ```bash
   php ./magego.phar

## Commands
### --help
- Displays a list of available commands or detailed help for a specific command.
### -vvv
- Enable verbose output for commands, providing more detailed logs and error messages.
### self-update
- Updates the **MageGo** to the latest version.
### --version
- Shows current version
### deploy
- Deploy custom commands packaged within a PHAR (PHP Archive) file to a Magento application. It extracts the PHAR file contents to a specified directory and verifies if the current directory is a Magento root directory before proceeding with the extraction.
### outdated
- Check the script for updates and reports if a newer version is available.
### install:coding-standards
- Install coding standards tools (PHPCS, PHPMD) into an LXC container.
- **Available options**:
  - `-b <container_name>`: **Required**. The name of the LXC container where the coding standards tools will be installed.
### configure:xdebug
- Configures Xdebug for PHP on an LXC container. 
- **Available options**
  - `-b <container_name>`: **Required**. The name of the LXC container where the xdebug should be configured.
### configure:ssh
- Configure SSH access to an LXC container by adding a private key.
- **Available options**
  - `-b <container_name>`: **Required**. The name of the LXC container where SSH will be configured.
  - `-u <ssh_user>`: **Optional**. The name of the SSH user to configure. Default is `www-data`.
  - `--keyPath <key_path>`: **Optional**. The path to the private key file to be added to the container.
### configure:grunt
- Configures Grunt for the Magento installation in an LXC container.
- **Available options**
  - `-b <container_name>`: **Required**. The name of the LXC container where Grunt will be configured.
  - `--magentoDir <magento_directory>`: **Optional**. The Magento root directory inside the container. Default is `/var/www/source`.



### Custom Commands for MageRun2

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

- `db:install`: Installs a database from `MAGENTO_ROOT/dumps` directory with one click.
- `db:init`: Installs a fresh database and runs post-import actions.
  - Behavior:
    - Activates maintenance mode.
    - Executes database installation and sanitization.
    - Installs user-related configurations.
    - Deactivates maintenance mode.
- `db:sanitize`: Sanitizes database entries as per the defined rules.
    - Behavior:
        - Initiate the database sanitization process. 
        - Custom actions for sanitization can be added in the command implementation.
- 
