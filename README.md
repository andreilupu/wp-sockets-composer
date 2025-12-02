# WP Sockets Core

A modern, React-powered framework for building WordPress Admin Pages. This library bridges the gap between PHP and React, allowing you to define admin interfaces declaratively while handling all the complexity of asset management, React mounting, and data persistence.

## Installation

```bash
composer require andreilupu/wp-sockets-core
```

## Examples

For complete working examples demonstrating both Composer and NPM integration methods, see the [wp-sockets-examples](https://github.com/andreilupu/wp-sockets-examples) repository.

## Usage

### 1. Initialize the Library

In your plugin's `init` hook (or `plugins_loaded`), initialize the library.

```php
use function WPSockets\Core\wp_sockets_init;

add_action( 'init', function() {
    // Basic initialization
    wp_sockets_init();
    
    // OR with custom assets URL (useful for symlinked local development)
    // wp_sockets_init( plugin_dir_url( __FILE__ ) . 'vendor/andreilupu/wp-sockets-core/assets' );
} );
```

### 2. Register an Admin Page

Use `wp_sockets_register_page` to define your admin page and its components ("sockets").

```php
use function WPSockets\Core\wp_sockets_register_page;

add_action( 'init', function() {
    wp_sockets_register_page( 'my-plugin-settings', [
        'page_title' => 'My Plugin Settings',
        'menu_title' => 'My Plugin',
        'capability' => 'manage_options',
        'mode'       => 'panel', // 'panel' or 'tabs'
        'sockets'    => [
            [
                'id'    => 'general_section',
                'type'  => 'group',
                'label' => 'General Settings',
                'children' => [
                    [
                        'id'    => 'api_key',
                        'type'  => 'text',
                        'label' => 'API Key',
                    ],
                    [
                        'id'    => 'enable_feature',
                        'type'  => 'checkbox', // Assuming checkbox type exists or will exist
                        'label' => 'Enable Feature',
                    ]
                ]
            ]
        ]
    ] );
} );
```

## Advanced Usage

### Custom Socket Types

You can register custom socket types using JavaScript hooks. See [EXTENSIBILITY.md](EXTENSIBILITY.md) for details.

### Data Helpers

By default, settings are saved to the WordPress Options API. You can override this to save to User Meta or custom tables.

## Requirements

- PHP >= 7.4
- WordPress >= 6.0
