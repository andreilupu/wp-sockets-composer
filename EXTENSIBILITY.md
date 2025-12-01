# Extending WP Sockets JS

The WP Sockets JS library is designed to be highly extensible, allowing developers to add custom socket types, modify data handling, and filter the application behavior.

## 1. Custom Sockets

Sockets are the building blocks of your admin page. You can register custom socket types to handle specific data inputs or UI elements.

### Registration

Sockets are registered via WordPress filters. The filter hook is dynamic based on the App ID and the socket type.

**Filter Hook:** `{appId}Sockets.socketType{Type}`

- `{appId}`: The ID of your application (e.g., `my_plugin_settings`).
- `{Type}`: The type of the socket, capitalized (e.g., `ColorPicker`).

### Example: Registering a Color Picker Socket

```javascript
import { addFilter } from '@wordpress/hooks';
import { ColorPicker } from '@wordpress/components';

const RenderColorPicker = ( original, socket ) => {
    // 'socket' contains the configuration passed from PHP
    return (
        <div className="my-color-picker-socket">
            <label>{ socket.label }</label>
            <ColorPicker
                color={ socket.value }
                onChangeComplete={ ( color ) => {
                    // Handle change
                } }
            />
        </div>
    );
};

addFilter(
    'my_plugin_settingsSockets.socketTypeColorPicker',
    'my-plugin/register-color-picker',
    RenderColorPicker
);
```

In your PHP configuration:

```php
'sockets' => [
    [
        'id' => 'main_color',
        'type' => 'colorPicker', // Matches the filter name
        'label' => 'Main Color',
    ],
]
```

## 2. Data Helpers

Data Helpers manage how data is fetched and saved. By default, the library uses `useOptionDataHelper` which saves to the WordPress Options API. You can override this to save to User Meta, Custom Tables, or external APIs.

### Overriding the Data Helper

**Filter Hook:** `sockets_data_helper_{appId}`

### Example: Using User Meta

```javascript
import { addFilter } from '@wordpress/hooks';
import { useUserDataHelper } from '@wp-sockets/js'; // Assuming export

const replaceWithUserDataHelper = ( defaultHelper ) => {
    // You can return a custom hook or use one of the built-in ones
    return useUserDataHelper(); 
};

addFilter(
    'sockets_data_helper_my_plugin_settings',
    'my-plugin/use-user-meta',
    replaceWithUserDataHelper
);
```

## 3. Customizing the App

### Modes

Currently, the library supports `panel` and `tabs` modes. Future versions will allow registering custom modes via filters.

### Hooks

The library exports several hooks that you can use in your custom components:

- `useSocketSetup( appId )`: Checks if the app is ready.
- `useOptionDataHelper( appId )`: Hook for Options API.
- `useUserDataHelper( appId )`: Hook for User Meta API.
