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
- **Macro** - Static utility classes for variables and Unicode operations
- **RegExp** - OOP wrapper for POSIX and PCRE regular expressions
- **Filesystem** - System-agnostic file operations
- **Process** - Execute and manage system processes
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

If you're not using Composer, you can use Composer's generated autoloader:

```php
require_once __DIR__ . '/vendor/autoload.php';
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

### Macro Classes

The framework provides static utility classes for common operations:

```php
use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\Macro\Unicode;
use Hubbitus\HuPHP\System\OS;

// Example: ensure variable is not empty
$value = Vars::requiredNotEmpty($someVariable, 'Variable name');

// Example: get first non-empty string
$str = Vars::firstMeaningString($input1, $input2, 'default value');

// Example: Unicode operations
$capitalized = Unicode::ucfirst('привет'); // 'Привет'
$wrapped = Unicode::wordwrap($text, 75, "\n");

// Example: System utilities
OS::err('Error message'); // Write to stderr
```

**Available classes:**
- `Hubbitus\HuPHP\Macro\Vars` - Variable utilities (firstMeaning, firstMeaningString, surround, requiredNotEmpty, requiredNotNull, swap, isset)
- `Hubbitus\HuPHP\Macro\Unicode` - Unicode operations (ucfirst, wordwrap, ord, chr)
- `Hubbitus\HuPHP\System\OS` - Extended with system utilities (err, hitCount, exitCount)

## Building Distributions

The framework can be built into packages:

```bash
# Build all packages (raw, phar, single file)
./.tools/regenerate.all
```

Built packages will be placed in `.tools/Packages/build/`.

**Note:** Building requires additional tools and is primarily for maintainers.

## Developer Documentation

Full API documentation is available in the `@phpdocs/` directory (generated with phpDocumentor):

```bash
# Open in browser
xdg-open @phpdocs/index.html  # Linux
open @phpdocs/index.html      # macOS
start @phpdocs\index.html     # Windows
```

To regenerate documentation:

```bash
./.tools/generate-phpdoc.sh
```

## Examples

Check the `@examples/` directory for usage examples:

- `HuFormat.example.php` - Formatting examples
- `HuGetopt.example.php` - Command-line option parsing
- `Consts.example.php` - Constants management
- `SettingsFilter.example.php` - Settings filtering
- `MultipleInheritance.example.php` - Multiple inheritance via traits
- `try-examples.php` - Various try-catch examples
- `EMPTY_STR.example.php` - Empty string macro examples

## Project Structure

```
HuPHP/
├── Debug/             # Dump, logging, backtrace
├── Exceptions/        # Exception hierarchy
├── Filesystem/        # File operations
├── Macro/             # Static utility classes (Vars, Unicode)
├── RegExp/            # Regular expressions
├── System/            # System utilities (OS, Process, Console)
├── Vars/              # Variables, Settings, HuArray, HuConfig
├── @examples/         # Example scripts
├── tests/             # PHPUnit tests
├── @phpdocs/          # Generated API documentation
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
   include_once('Debug/Dump.php');
   Dump::a($variable);

   // New:
   use Hubbitus\HuPHP\Debug\Dump;
   Dump::a($variable);
   ```

3. **Replace legacy macro functions** with new static classes:
   ```php
   // Old (removed):
   include_once('macroses/REQUIRED_VAR.php');
   $value = REQUIRED_VAR($var);
   
   include_once('macroses/EMPTY_STR.php');
   $str = EMPTY_STR($var1, $var2);
   
   include_once('macroses/SWAP.php');
   SWAP($a, $b);

   // New (recommended):
   use Hubbitus\HuPHP\Macro\Vars;
   
   $value = Vars::requiredNotEmpty($var);
   $str = Vars::firstMeaningString($var1, $var2);
   Vars::swap($a, $b); // Swaps values by reference
   ```

4. **New Macro classes** provide type-safe static methods:
   - `Hubbitus\HuPHP\Macro\Vars` - Variable utilities (firstMeaning, firstMeaningString, surround, requiredNotEmpty, requiredNotNull, swap, isset)
   - `Hubbitus\HuPHP\Macro\Unicode` - Unicode operations (ucfirst, wordwrap, ord, chr)
   - `Hubbitus\HuPHP\System\OS` - Extended with system utilities (err, hitCount, exitCount)

5. **Removed features**: The old Template engine (`Templating/`) has been removed. Use modern template engines like Twig or League Plates instead.

See `@examples/` directory for updated code examples.

## License

MIT License. See LICENSE file for details.

## Testing

The framework includes comprehensive PHPUnit tests to ensure reliability and maintainability.

### Running Tests

To run all tests:

```bash
./tests/run-tests.sh
```

This script runs PHPUnit with coverage reporting enabled.

### Coverage Reporting

After running tests, coverage reports are generated in the `build/` directory:
- `build/coverage/` - HTML coverage report (open `build/coverage/index.html` in browser)
- `build/coverage.txt` - Text-based coverage summary

### Requirements for Testing

- PHP 8.3+ with Xdebug extension enabled
- Composer dependencies installed (`composer install`)

### Writing Tests

Tests should be placed in `tests/` directory following the same structure as the source code.

Example test file structure:
```
tests/
├── Debug/
│   └── BacktraceTest.php
├── Exceptions/
│   └── BaseExceptionTest.php
├── Filesystem/
│   └── FileInMemoryTest.php
├── Macro/
│   └── MacrosTest.php
├── RegExp/
│   └── RegExpPcreTest.php
├── System/
│   └── ProcessTest.php
└── Vars/
    └── NullClassTest.php
```

Each test class should extend `PHPUnit\Framework\TestCase` and use strict types declaration.

### Current Coverage Status

**As of this version, the framework has 100% test coverage:**

- **Classes:** 100.00% (56/56)
- **Methods:** 100.00% (419/419)
- **Lines:** 100.00% (1771/1771)

All tests pass with zero warnings, deprecations, or risky tests.

### Test Script Options

The `tests/run-tests.sh` script supports the following options:
- `--help` - Show help information
- `--no-coverage` - Skip coverage report generation
- `--verbose` - Enable verbose output

Example:
```bash
./tests/run-tests.sh --verbose
```

## Static Analysis (PHPStan)

The project uses [PHPStan](https://phpstan.org/) for static analysis to catch bugs and type errors before runtime.

### Running PHPStan

```bash
# Using the lint script
./tests/lint.sh

# Or directly via Composer
composer phpstan

# Or directly via PHPStan
./vendor/bin/phpstan analyse
```

### Configuration

PHPStan is configured in `phpstan.neon` at the project root. Current configuration:
- **Level**: 5 (balanced strictness)
- **Paths**: Analyzes `Debug/` directory (gradually expanding)
- **Bootstrap**: Uses Composer's autoloader for class loading

### Understanding PHPStan Output

PHPStan will report errors with:
- 🪪 Error code for reference
- 💡 Tips for fixing issues
- Line numbers and file paths

Example error:
```
------ -----------------------------------------------------------------------
  Line   HuError.php
 ------ -----------------------------------------------------------------------
  80     Access to an undefined property
         Hubbitus\HuPHP\Debug\HuError::$settings.
         🪪  property.notFound
 ------ -----------------------------------------------------------------------
```

### Ignoring Legacy Code

Some legacy code may have PHPStan errors that require significant refactoring. These are temporarily ignored in `phpstan.neon` with the intention to fix them gradually.


## Contributing

Issues and pull requests are welcome on GitHub.

