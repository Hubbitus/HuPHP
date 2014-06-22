#!/usr/bin/php
<?
require('autoload.php');
dump::a('Just test');

$bt = backtrace::create();
$bt->printout();
?>