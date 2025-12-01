# Project Philosophy

WP Sockets is a library designed to modernize WordPress Admin Page development by bridging the gap between PHP's robustness and React's interactivity.

## Core Principles

### 1. React-First, WordPress-Native
We believe admin pages should feel modern and responsive. WP Sockets uses standard WordPress packages (`@wordpress/components`, `@wordpress/element`, `@wordpress/data`) to ensure the UI feels native to the block editor ecosystem while providing a superior developer experience compared to traditional PHP-generated HTML.

### 2. Declarative Configuration
Developers should define *what* they want, not *how* to build it. Admin pages are defined via simple PHP arrays or filters. The library handles the complexity of menu registration, asset enqueueing, and React mounting.

### 3. The "Socket" Concept
A "Socket" is a pluggable unit of functionality—a field, a setting, or a UI component. Sockets are designed to be:
- **Composable**: Combine simple sockets to build complex interfaces.
- **Extensible**: Developers can register custom socket types.
- **Isolated**: Each socket manages its own state and logic, minimizing side effects.

### 4. Data Abstraction
The UI layer is decoupled from data persistence. Through "Data Helpers," the library supports various storage backends (Options API, User Meta, Custom Tables, REST API) without changing the component implementation. This allows the same UI to be reused in different contexts (e.g., global settings vs. user profile settings).

### 5. Conflict Resilience
In the WordPress ecosystem, plugins often conflict. WP Sockets is architected to:
- **Isolate Dependencies**: Uses a build process that prevents React version conflicts.
- **Scope Styles**: CSS is namespaced to avoid bleeding into other admin areas.
- **Handle Multi-Tenancy**: Multiple plugins can use the library simultaneously without stepping on each other's toes.

## Architecture

- **`wp-sockets-js`**: The React core, providing the component library and runtime.
- **`wp-sockets-composer`**: The PHP bridge, handling WordPress integration, autoloading, and asset management.
- **`wp-sockets-test-plugin`**: A reference implementation and testing ground.

## Vision
To become the standard "framework" for building complex, app-like WordPress admin interfaces with minimal boilerplate.
