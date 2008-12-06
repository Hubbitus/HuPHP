#!/usr/bin/php
<?
include_once('Debug/debug.php');

dump::a('Just test');

$bt = backtrace::create();
$bt->printout();
?>