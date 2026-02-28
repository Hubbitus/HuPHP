<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);

require_once $rootDir . '/HuPHP.autoload.php';

// Load macroses for tests
require_once $rootDir . '/macroses/REQUIRED_NOT_NULL.php';
require_once $rootDir . '/macroses/REQUIRED_VAR.php';
require_once $rootDir . '/macroses/EMPTY_VAR.php';

// Initialize global config array if not exists
// This is required for HuConfig class and Single::def() to work properly
if (!isset($GLOBALS['__CONFIG'])) {
    $GLOBALS['__CONFIG'] = [];
}

// Load HuConfig.php to register CONF() function
// This is needed because CONF() is a global function, not a class method,
// so it won't be autoloaded by PSR-4
require_once $rootDir . '/Vars/HuConfig.php';

