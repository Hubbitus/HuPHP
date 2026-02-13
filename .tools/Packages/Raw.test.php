#!/usr/bin/php
<?php
declare(strict_types=1);

require('autoload.php');
Dump::a('Just test');

$bt = Backtrace::create();
$bt->printout();
