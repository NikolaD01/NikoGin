# NikoGin - WordPress Plugin Generator

##  Introduction
NikoGin automates the creation of a structured WordPress plugin, ensuring best practices in directory setup, class generation, and Composer integration. It emphasizes:

- Dependency injection
- Service provider management
- Autoloading via Composer
- CLI-based plugin creation

##  Features
-  Generates a complete WordPress plugin structure.
-  Includes service providers for dependency management.
-  Uses Composer for autoloading.
- 🖥 Provides a CLI command to create a new plugin.

##  Installation
To install this package via Composer, run:
```sh
composer require nikogin/plugin-generator
```
---
### Notice !!!
Since **NikoGin** is still in development some things still needs to be polished,
for current use its best to install **NikoGin** inside **wp-content/plugins** folder and add to **.gitignore**,
when I manage to put it to composer, It will be moved as global package.

Important thing now is also that most bootstrap inside plugin will be needed to be done manually what does that mean ?
For example when you create multiple **Providers** you will need to manually add them to providers in **ProviderManager** inside **Core** of framework.

In future all of this will be covered for now just keep in mind that some things need manual setup. 
Enjoy programing :) 
---

##  Usage
### Create a New Plugin
Run the following command to generate a new WordPress plugin:
```sh
php nikogin create <PluginName> <PluginPrefix>
```

### Example:
```sh
php nikogin create MyPlugin MyPluginPrefix
```

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
php nikogin make:controller <Name> <Type> <Dir> 
```

Available Types:

There are three options for the `<Type>` argument:

    rest – Creates a REST API controller.
    menu – Creates a Menu controller.
    submenu – Creates a Submenu controller.

### Examples
#### 1. Example for Rest Controller directly from wp-content
```sh
php nikogin make:controller ExampleRestController rest example-plugin
```
#### 2. Example for Menu Controller from root of project
```sh
php nikogin make:controller ExampleMenuController menu wp-content/example-plugin
```
#### 3. Example for Submenu Controller 
```sh
php nikogin make:controller ExampleSubmenuController submenu example-plugin
```
### Description:
-  Creates a directory for Controller type if it's not created already.
-  Generates essential class logic
---

### Create a Migration

```sh
php nikogin make:migration <Name> <Dir> 
```

### Example 
```sh
php nikogin make:migration Example example-plugin 
```

### Description:
- Creates Migration with filled name and skeleton for creating schema
- name of table we be constructed based on wp-prefix_plugin-prefix_migration-name (wp_ep_example)
---

### Create a Provider 

```sh
php nikogin make:provider <Name> <Dir>
```

### Example

```sh
php nikogin make:provider Example example-plugin
```

### Description:

- Creates Provider which is automatically attached to ProviderManager
- Ability to override base register() method

---

### Create a Listener

```sh
php nikogin make:listener <Name> <Listener> <Dir> optional <Type> <Args> <Priorty>
```

### Example 

```sh
php nikogin make:listener PostSave save_post example action --args=2 --priority=10
```

### Description:

- Creates a Listener for Wordpress action or filter
- Has ability to define number of arguments and priority level
- Utilize handle method as callback on action/filter trigger

---

### Create a Cron

```sh
php nikogin make:cron <Name> <Dir>
```

### Example

```sh
php nikogin make:cron ExampleCron exampledir
```

### Description:

- Creates a Cron action for Wordpress

---

### Create a Repository

```sh
php nikogin make:repository <Name> <Table> <Dir>
```

### Example
```sh
php nikogin make:repository ExampleName example_table example/
```

### Description: 
- Creates a Repository for given table


---

## Incoming
This is list of incoming features and commands : \
**BE** 
- Jobs (Depending on WooCommerce Action Scheduler (subject to change), wordpress background processes)
- Middlewares (Package inside core with already done middlewares also ability to make new ones, this is for REST routing security)
- Controller (Rest Controller)
- Shortcodes (Command to create shortcodes nothing special)
- Wordpress component extension (Ability to easy extend any wordpress component as WpTable ) (Subject to change)
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

