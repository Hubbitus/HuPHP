#!/usr/bin/php
<?
include_once('Debug/debug.php');
include_once('RegExp/RegExp_pcre.php');
#sed#include_once('Vars/HuArray.php');

//Base example from: http://ru.php.net/manual/ru/tokenizer.examples.php
/*
* T_ML_COMMENT does not exist in PHP 5.
* The following three lines define it in order to
* preserve backwards compatibility.
*
* The next two lines define the PHP 5 only T_DOC_COMMENT,
* which we will mask as T_ML_COMMENT for PHP 4.
*/
	if ( !defined('T_ML_COMMENT') ){
	define('T_ML_COMMENT', T_COMMENT);
	} elseif( !defined('T_DOC_COMMENT') ){
	define('T_DOC_COMMENT', T_ML_COMMENT);
	}

//$source = file_get_contents('example.php');
$source = file_get_contents(($inputfile = isset($argv[1]) ? $argv[1] : 'php://stdin')); #$argv[2] optionnaly part of DIRm which must be stripped
$tokens = token_get_all($source);

//dump::a($source, 'Original:');

$class_started = false;
$curly_open = 0;

$res = '';

foreach ($tokens as $token) {
	if (is_string($token)){// simple 1-character token
	//echo '===' . $token . "===\n";
		if ( '{' == $token ){
			if ($class_started){
			++$curly_open;
			continue;
			}
		}
		elseif ( $class_started and '}' == $token ){
		--$curly_open;
			if (0 == $curly_open){
			$class_started = false;
			continue;
			}
		}
		elseif (!$class_started){
		$res .= $token;
		}
	} else {
	// token array
	list($id, $text) = $token;
	switch ($id){
		case T_COMMENT:
		case T_ML_COMMENT: // we've defined this
		case T_DOC_COMMENT: // and this
		// no action on comments
		break;

		case T_CLASS:
		$class_started = true;
		break;

		default: // anything else -> output "as is" if it not class definition
			if (!$class_started){
			$res .= $text;
			}
		}
	}
}

//Echo only function names. one per line
//RegExp for function name got from (in Russian is absent): http://ru.php.net/manual/en/functions.user-defined.php
//dump::a(classCreate('RegExp_pcre', '#function\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\(#i', $res)->doMatchAll()->getHuMatches(1));
$m = classCreate('RegExp_pcre', '#function\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\(#i', $res)->doMatchAll()->getHuMatches(1);
	if ($m->count()) //Check for the \n
//	echo($m->implode("\n") . ':' . $inputfile . "\n");
	echo($m->walk(create_function('&$item', "\$item = \"\t\t'\$item'\t=> '" . RegExp_pcre::create('#^' . RegExp_pcre::quote(@$argv[2]) . '#', $inputfile)->replace() . "',\";"))->implode("\n") . "\n");
?>