<?php
declare(strict_types=1);

/**
* Autoloader for HuPHP framework
* This file is included via composer autoloader
**/

// Define a constant for the base directory
if (!defined('HUPHP_BASE_DIR')) {
    define('HUPHP_BASE_DIR', __DIR__);
}

// Include all macro function files to ensure they are available globally
$macroFiles = [
    'macroses/ASSIGN_IF.php',
    'macroses/DEFINED_CLASS.php',
    'macroses/EMPTY_INT.php',
    'macroses/EMPTY_STR.php',
    'macroses/EMPTY_VAR.php',
    'macroses/EMPTY_callback.php',
    'macroses/INT_CHECK_RANGE.php',
    'macroses/ISSET_VAR.php',
    'macroses/IS_SET.php',
    'macroses/REQUIRED_NOT_NULL.php',
    'macroses/REQUIRED_VAR.php',
    'macroses/SWAP.php',
    'macroses/eecho.php',
    'macroses/exit_count.php',
    'macroses/unicode_ucfirst.php',
    'macroses/unicode_wordwrap.php',
    'macroses/uniord.php'
];

foreach ($macroFiles as $file) {
    $filePath = __DIR__ . '/' . $file;
    if (file_exists($filePath)) {
        require_once $filePath;
    }
}

// Register the autoloader
spl_autoload_register(function ($class) {
    // Handle classes in the Hubbitus\HuPHP namespace using PSR-4
    if (strpos($class, 'Hubbitus\\HuPHP\\') !== 0) {
        // If it's not our namespace, return to let other autoloaders handle it
        return;
    }

    $relative_class = substr($class, 14); // Length of 'Hubbitus\HuPHP\'

    // Convert namespace to file path
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
    $file = __DIR__ . DIRECTORY_SEPARATOR . $file;

    if (file_exists($file)) {
        require_once $file;
    }
});


// For backward compatibility, also register a fallback for legacy includes
// This handles the case where files use relative includes like 'file_base.php'
if (!function_exists('legacy_include_resolver')) {
    function legacy_include_resolver($filename) {
        $full_path = __DIR__ . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($full_path)) {
            return $full_path;
        }

        // If the file doesn't exist with the original casing, try to find it
        $dir = dirname($full_path);
        $basename = basename($full_path);
        $files = scandir($dir);

        foreach ($files as $file) {
            if (strtolower($file) === strtolower($basename)) {
                return $dir . DIRECTORY_SEPARATOR . $file;
            }
        }

        return $full_path; // Return original if not found
    }

    // Override include_once to handle legacy includes
    function include_once_legacy($filename) {
        $resolved_path = legacy_include_resolver($filename);
        return include_once $resolved_path;
    }

    // Override require_once to handle legacy includes
    function require_once_legacy($filename) {
        $resolved_path = legacy_include_resolver($filename);
        return require_once $resolved_path;
    }
}