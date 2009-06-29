<?
include_once('autoload.php');
// As it is in progress, autoload don't known about new classes:
#include_once('settings_filter.php');
#include_once('settings_filter_filter.php');

$filt = new settings_filter(array('foo', 'bar'), array('foo' => 5, 'bar' => 'Test string'));
//Then regsister 2 filters:
$getFilterNo = $filt->addFilterGet(new settings_filter_base('foo', create_function('$name, $val', 'return $val * 10;')));
$filt->addFilterSet(new settings_filter_base('bar', create_function('$name, $val', 'return str_replace("-", "--", $val);')));
$filt->addFilterSet(new settings_filter_base('bar', create_function('$name, $val', 'return trim($val);')));

echo $filt->foo . "\n"; //Prints: 50
$filt->bar = '   This-is-test    ';
echo '[' . $filt->bar . ']' . "\n"; //Prints: [This--is--test]
	/* IS NOT IMPLEMENTED YET
	// If for property does not register any filters with 'private' field {@see ::add[GS]etFilter()} methods parameter $private.
	// You may request its raw value:
	try{
	echo $filt->getRaw('foo'); //Prints: 5
	}
	catch(settings_filter_exception $e){
	echo 'Request Raw value does not possible due to registered private filters';
	}
	*/
//  You may want to delete prevouse added filter:
$filt->delFilterGet('foo', $getFilterNo);
echo $filt->foo . "\n"; //Prints: 5
?>