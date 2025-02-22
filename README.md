# NikoGin

Introduction

This package automates the creation of a structured WordPress plugin, including directory setup, class generation, and Composer integration. It follows best practices, including dependency injection and service provider management.

Features

Generates a complete WordPress plugin structure.

Includes service providers for dependency management.

Uses Composer for autoloading.

Provides a CLI command to create a new plugin.

Supports activation, deactivation, and uninstallation hooks.

Installation

To use this package, first install it via Composer:

composer require nikogin/plugin-generator

Usage

Create a New Plugin

Run the following command to generate a new plugin:

php nikogin create <PluginName> <PluginPrefix>

Example : 

php nikogin create MyPlugin MyPluginPrefix

This command will:

Create a structured directory for the plugin.

Generate necessary classes such as Plugin.php, ServiceProviderManager.php, and more.

Automatically run composer install at the end of the process.
