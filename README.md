# NikoGin - WordPress Plugin Generator

## Introduction
NikoGin is a command-line tool that automates the creation of a structured, modern WordPress plugin. It sets up a professional foundation with an emphasis on best practices, saving you hours of boilerplate work.

-   **Modern PHP:** Built with dependency injection and service providers.
-   **Autoloading:** Uses Composer for a clean, autoloaded class structure.
-   **Powerful CLI:** A robust command-line interface for generating plugins and their components.

## Features
-   Generates a complete WordPress plugin structure from a single command.
-   Includes commands for scaffolding controllers, cron events, migrations, providers, listeners, repositories, and shortcodes.
-   Enforces a clean architecture with a pre-configured service container.
-   Uses Composer for autoloading and dependency management.

---

## Installation

NikoGin is a global CLI tool. You install it once on your system and can use it anywhere to create new plugins.

### 1. Requirements

Before you install, ensure your system is set up for modern PHP development.
-   **PHP >= 8.2**
-   **Composer**
-   The following PHP extensions must be installed and enabled:
   -   `intl` (Required by Symfony dependencies for string normalization)
   -   `mbstring` (Required by Symfony dependencies for multi-byte string handling)
   -   `curl` (Required by Composer for downloading packages)
   -   `xml` (Required by Composer and many PHP packages)
   -   `zip` (Required by Composer for extracting packages)

> **For Debian/Ubuntu/Pop!_OS users:**
> If you need to set up a new PHP environment, you can use the `ppa:ondrej/php` repository.
> ```sh
> # Add the trusted PHP repository
> sudo add-apt-repository ppa:ondrej/php
> 
> sudo apt update
> 
> # Install PHP and all required extensions
> sudo apt install php8.2-cli php8.2-intl php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip
> ```

### 2. Global Installation
Since NikoGin is not yet on the public Packagist repository, you must install it directly from its private GitHub repository.

#### 2.1. Configure Composer
First, tell your global Composer installation where to find the package. This requires an SSH key configured with your GitHub account.

```sh
composer global config repositories.nikogin vcs git@github.com:NikolaD01/NikoGin.git
````

#### 2.2. Install the package
Now, run the require command using the specific branch you want to install. The dev- prefix is required.
```sh
# You can replace "main" with the branch you want to install (Keep in mind that "main" is the current stable branch.), e.g., "dev-feature/alpha-feature"
composer global require nikolad/nikogin:"dev-main"
```

Note: If this is your first time interacting with private GitHub repositories via Composer, you may be prompted to create and provide a Personal Access Token. If it is this first time doing this refer to the [Official GitHub documentation](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens#creating-a-personal-access-token-classic).

### 3. Update your system's PATH
To run the nikogin command directly, you must add Composer's global bin directory to your system's PATH.

#### 3.1. Find the exact path to the directory by running:

```sh
composer global config bin-dir --absolute
````
#### 3.2. Add this path to your shell's startup file (e.g., .zshrc, .bashrc, or .profile).
```sh
export PATH="$(composer global config bin-dir --absolute):$PATH"
```

#### 3.3. After adding the previously mentioned path to your shell's startup you need to launch a new terminal instance so the change takes effect.
#### Alternately you can run the following command so you see the changes immediately in the same terminal instance:
**NOTE**: If you are using a different shell (zshell, sh etc.) source the startup file that matches the shell you are using.

```sh
source ~/.bashrc
```

### 4. Verify the installation
After completing the above steps, verify that the installation was successful by running the following command in your terminal:

```sh
nikogin --version
```

### 5. Keeping NikoGin updated
Since the project is still in the development phase and there are frequent changes,
you will need to pull the latest changes and bug fixes periodically. You can easily do this
by running Composer's global `update` command:

```sh
composer global update nikolad/nikogin
```

##  Usage
### Create a New Plugin
Run the following command to generate a new WordPress plugin:
```sh
nikogin create <PluginName> <PluginPrefix> <PathToWordpressRoot>
```
Note: if no path is provided it will default to current working directory.

### Example:
```sh
nikogin create 'My First PopArt plugin' PA '~/PhpStormProjects/project123'
```

Note: NikoGin uses kebab-case for the name of the root plugin directory, so in our case
'My First PopArt plugin' will be created in my-first-popart-plugin directory.

### Description:
-  Creates a structured directory for the plugin.
-  Generates essential classes like:
    - `Plugin.php`
    - `ServiceProviderManager.php`
    - Other necessary components.
-  Automatically runs `composer install` to set up dependencies.
---

### Create a Controller

```sh
nikogin make:controller <Name> <Type> <Dir> 
```

Available Types:

There are three options for the `<Type>` argument:

    rest – Creates a REST API controller.
    menu – Creates a Menu controller.
    submenu – Creates a Submenu controller.

### Examples
#### 1. Example for Rest Controller directly from wp-content
```sh
nikogin make:controller ExampleRestController rest example-plugin
```
#### 2. Example for Menu Controller from root of project
```sh
nikogin make:controller ExampleMenuController menu wp-content/example-plugin
```
#### 3. Example for Submenu Controller 
```sh
nikogin make:controller ExampleSubmenuController submenu example-plugin
```
### Description:
-  Creates a directory for Controller type if it's not created already.
-  Generates essential class logic
---

### Create a Migration

```sh
nikogin make:migration <Name> <Dir> 
```

### Example 
```sh
nikogin make:migration Example example-plugin 
```

### Description:
- Creates Migration with filled name and skeleton for creating schema
- name of table we be constructed based on wp-prefix_plugin-prefix_migration-name (wp_ep_example)
---

### Create a Provider 

```sh
nikogin make:provider <Name> <Dir>
```

### Example

```sh
nikogin make:provider Example example-plugin
```

### Description:

- Creates Provider which is automatically attached to ProviderManager
- Ability to override base register() method

---

### Create a Listener

```sh
nikogin make:listener <Name> <Listener> <Dir> optional <Type> <Args> <Priorty>
```

### Example 

```sh
nikogin make:listener PostSave save_post example action --args=2 --priority=10
```

### Description:

- Creates a Listener for Wordpress action or filter
- Has ability to define number of arguments and priority level
- Utilize handle method as callback on action/filter trigger

---

### Create a Cron

```sh
nikogin make:cron <Name> <Dir>
```

### Example

```sh
nikogin make:cron ExampleCron exampledir
```

### Description:

- Creates a Cron action for Wordpress

---

### Create a Repository

```sh
nikogin make:repository <Name> <Table> <Dir>
```

### Example
```sh
nikogin make:repository ExampleName example_table example/
```

### Description: 
- Creates a Repository for given table


---

### Create a Shortcode

```sh
nikogin make:shortcode <Name> <Action> <Dir>
```

### Example
```sh
nikogin make:shortcode ExampleName example_action example/
```

### Description:
- Creates a Shortcode for given action

---

## Incoming
This is list of incoming features and commands : \
**BE** 
- Jobs (Depending on WooCommerce Action Scheduler (subject to change), WordPress background processes)
- Middlewares (Package inside core with already done middlewares also ability to make new ones, this is for REST routing security)
- Controller (Rest Controller)
- WordPress component extension (Ability to easily extend any WordPress component as WpTable ) (Subject to change)
- Commands (Extend WP CLI, create commands)
- Seeders
- Support Elements ( as Symfony d/dd etc ... ) 
- API Foundation Support \
**FE** \
Idea here is when we want to create new plugin, we can have starter kits or none.
With starter kits we can choose how we will make dashboards and what would be used as bundler.
For example goal is that user can choose react, twig or base php , so for react we can use wp-scripts,
for twig or php we can use vite with typescript , and at end user can choose vanilla js without bundlers 
---

## Contributing
Contributions are welcome! Feel free to submit issues and pull requests.

## License
This project is licensed under the MIT License.

---
 **Stay Updated:** Follow updates and improvements to the package. Happy coding! 
 
