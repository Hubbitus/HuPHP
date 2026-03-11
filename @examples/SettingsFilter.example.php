<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Examples;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilter;
use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;

include_once(__DIR__ . '/../vendor/autoload.php');

$filter = new SettingsFilter(['foo', 'bar'], ['foo' => 5, 'bar' => 'Test string']);

// Then register 2 filters:
// GET filter multiplies value by 10
$getFilterNo = $filter->addFilterGet(new SettingsFilterBase('foo', function(&$name, &$val) {
	$val = $val * 10;
	return $val;
}));

// SET filters: replace '-' with '--' and trim whitespace
$filter->addFilterSet(new SettingsFilterBase('bar', function(&$name, &$val) {
	$val = \str_replace('-', '--', $val);
	return $val;
}));
$filter->addFilterSet(new SettingsFilterBase('bar', function(&$name, &$val) {
	$val = \trim($val);
	return $val;
}));

echo $filter->foo . "\n"; // Prints: 50

$filter->bar = '   This-is-test    ';
echo '[' . $filter->bar . ']' . "\n"; // Prints: [This--is--test]

