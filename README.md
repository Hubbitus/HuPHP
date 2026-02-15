# HuPHP

[![Minimum PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Modern PHP framework supporting PHP 8.0+ for common tasks like debugging, logging, configuration management and more.

**Migration Notice:** This version uses PSR-4 autoloading and namespaces (`Hubbitus\HuPHP\`). All classes are loaded automatically via Composer. Legacy includes have been removed.

## Features

- **Dump** - Advanced variable dumping with automatic caller argument name parsing (unique feature!)
- **HuArray** - Enhanced array manipulation with OOP syntax
- **HuLog** - Flexible logging facility with auto-configuration
- **Vars** - Domain-oriented programming interfaces
- **Macroses** - Helper functions for checks and assertions
- **Database Abstraction** - Supports MSSQL, MySQL, SQLite with charset conversion
- **RegExp** - OOP wrapper for POSIX and PCRE regular expressions
- **Filesystem** - System-agnostic file operations
- **Process** - Execute and manage system processes
- **Template Engine** - Built-in template processor
- **Exception Hierarchy** - Comprehensive exception system

## Requirements

- PHP 8.3 or higher
- Composer (for installation)

## Installation

### Via Composer (Recommended)

```bash
composer require hubbitus/huphp
```

### Manual Installation

If you're not using Composer, you can manually include the autoloader:

```php
require_once __DIR__ . '/HuPHP.autoload.php';
```

**Note:** Composer installation is strongly recommended as it handles all dependencies and autoloading automatically.

## Usage

All classes are autoloaded via Composer following PSR-4 standard under `Hubbitus\HuPHP\` namespace.

### Basic Example

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Hubbitus\HuPHP\Debug\Dump;
use Hubbitus\HuPHP\Vars\HuArray;

$var1 = 77;
$arr = [1, 2];
$ha = new HuArray($arr);
$testArray = [$var1, $arr, $ha, 777];

Dump::a($testArray); // Console output with automatic argument name detection!
```

Output (in console):
```
$testArray: Array(4){
  [0] => int(77)
  [1] => Array(2){
    [0] => int(1)
    [1] => int(2)
  }
  [2] => class HuArray#1 (1) {
    protected $__SETS =>
    Array(2){
      [0] => int(1)
      [1] => int(2)
    }
  }
  [3] => int(777)
}
```

**Note:** The automatic caller argument name parsing is a unique feature - you see `$testArray` as variable name without any extra code!

### Logging

```php
use Hubbitus\HuPHP\Debug\HuLOG;

$log = new HuLOG();
$log->toLog('This is an info message', 'ACS'); // Log to access log
```

### Configuration Management

```php
use Hubbitus\HuPHP\Vars\HuConfig;

$config = HuConfig::singleton();
$value = $config->someSetting; // Access via magic __get
```

### Database

```php
use Hubbitus\HuPHP\Database\DatabaseMySQL;

$db = new DatabaseMySQL([
    'host' => 'localhost',
    'user' => 'username',
    'pass' => 'password',
    'name' => 'database'
]);

$db->connect();
$result = $db->query('SELECT * FROM table');
```

### Macro Functions

The framework provides helper functions (macroses) that are automatically available after autoload:

```php
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;
use function Hubbitus\HuPHP\Macroses\EMPTY_STR;
use function Hubbitus\HuPHP\Macroses\ISSET_VAR;

// Example: ensure variable is set
$value = REQUIRED_VAR($someVariable, 'Variable name');

// Example: empty string check
$str = EMPTY_STR($input, 'default value');
```

**Note:** Macro functions can be used without explicit `use function` statements as they are loaded globally via Composer's `files` autoload, but importing them with `use function` is recommended for clarity and IDE support.

## Building Distributions

The framework can be built into single-file or PHAR distributions:

```bash
# Build all packages (raw, phar, single file)
./.tools/regenerate.all
```

Built packages will be placed in `.tools/Packages/build/`.

## Developer Documentation

Full API documentation can be generated with phpDocumentor:

```bash
phpdoc -d . -t docs
```

## Examples

Check the `@examples/` directory for usage examples:

- `HuFormat.example.php` - Formatting examples
- `HuGetopt.example.php` - Command-line option parsing
- `Consts.example.php` - Constants management
- `SettingsFilter.example.php` - Settings filtering
- `MultipleInheritance.example.php` - Multiple inheritance via traits
- `try-examples.php` - Various try-catch examples

## Project Structure

```
HuPHP/
├── Database/          # Database abstraction layer
├── Debug/             # Dump, logging, backtrace
├── Exceptions/        # Exception hierarchy
├── Filesystem/        # File operations
├── Macroses/          # Helper functions
├── RegExp/            # Regular expressions
├── System/            # System utilities (OS, Process, Console)
├── User/              # User management (basic)
├── Vars/              # Variables, Settings, HuArray, HuConfig
├── images/            # Image processing
├── template/          # Template engine (deprecated)
├── @examples/         # Example scripts
├── HuPHP.autoload.php # Autoloader (legacy support)
├── composer.json      # Composer configuration
└── README.md          # This file
```

## Migration from Old Versions

This version uses **namespaces** and **PSR-4 autoloading**. If you're upgrading from pre-Composer version:

1. **Replace legacy includes** with Composer autoloader:
   ```php
   // Old:
   include('HuPHP.autoinclude.php');

   // New:
   require_once __DIR__ . '/vendor/autoload.php';
   ```

2. **Update class references** - all classes now use `Hubbitus\HuPHP\` namespace:
   ```php
   // Old:
   $db = new DatabaseMySQL();

   // New:
   use Hubbitus\HuPHP\Database\DatabaseMySQL;
   $db = new DatabaseMySQL();
   ```

3. **Replace manual includes** with `use` statements:
   ```php
   // Old:
   include_once('macroses/REQUIRED_VAR.php');
   $value = REQUIRED_VAR($var);

   // New (recommended):
   use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;
   $value = REQUIRED_VAR($var);

   // Or (still works due to Composer 'files' autoload):
   $value = REQUIRED_VAR($var);
   ```

4. **Macro functions** are automatically loaded globally via Composer's `files` autoload (see `HuPHP.autoload.php`). However, using `use function` is recommended for better IDE support and code clarity.

5. **Deprecated features**: The old template engine in `template/` directory is deprecated. Consider migrating to modern template engines like Twig or Blade.

See `@examples/` directory for updated code examples.

## License

MIT License. See LICENSE file for details.

## Contributing

Issues and pull requests are welcome on GitHub.

---

**Note:** Some parts of the framework (like the old template engine) are deprecated but kept for backward compatibility. Consider them for future removal.
