# NikoGin - WordPress Plugin Generator

## 🚀 Introduction
NikoGin automates the creation of a structured WordPress plugin, ensuring best practices in directory setup, class generation, and Composer integration. It emphasizes:

- Dependency injection
- Service provider management
- Autoloading via Composer
- CLI-based plugin creation

## ✨ Features
- 📂 Generates a complete WordPress plugin structure.
- 🔧 Includes service providers for dependency management.
- 📜 Uses Composer for autoloading.
- 🖥️ Provides a CLI command to create a new plugin.

## 📥 Installation
To install this package via Composer, run:
```sh
composer require nikogin/plugin-generator
```

## 🔧 Usage
### Create a New Plugin
Run the following command to generate a new WordPress plugin:
```sh
php nikogin create <PluginName> <PluginPrefix>
```

### Example:
```sh
php nikogin create MyPlugin MyPluginPrefix
```

### What This Command Does:
- 📁 Creates a structured directory for the plugin.
- 🏗️ Generates essential classes like:
    - `Plugin.php`
    - `ServiceProviderManager.php`
    - Other necessary components.
- 📦 Automatically runs `composer install` to set up dependencies.

## 🛠️ Contributing
Contributions are welcome! Feel free to submit issues and pull requests.

## 📄 License
This project is licensed under the MIT License.

---
🔗 **Stay Updated:** Follow updates and improvements to the package. Happy coding! 🚀

