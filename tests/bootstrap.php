<?php

declare(strict_types=1);

$rootDir = \dirname(__DIR__);

// Load composer autoload which includes all macroses and sets up PSR-4 autoloading
require_once $rootDir . '/vendor/autoload.php';

// Initialize global config array if not exists
// This is required for HuConfig class and Single::def() to work properly
if (!isset($GLOBALS['__CONFIG'])) {
	$GLOBALS['__CONFIG'] = [];
}

// Load HuConfig.php and CONF() function
// This is needed because CONF() is a global function in namespace, not autoloaded by PSR-4
require_once $rootDir . '/Vars/HuConfig.php';
require_once $rootDir . '/Vars/CONF.php';

// Load VariableStream.php to register 'var' stream wrapper
// This is needed for tests that use var:// protocol
require_once $rootDir . '/Vars/VariableStream.php';

// Load SingleDefTest.bootstrap.php to define CONF() function for SingleDefTest
// This is needed because SingleDefTest requires CONF() function to be defined
require_once $rootDir . '/tests/Vars/SingleDefTest.bootstrap.php';

