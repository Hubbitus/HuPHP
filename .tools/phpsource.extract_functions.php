#!/usr/bin/php
<?
//define('AUTOLOAD_DEBUG', true);
//include('autoload.php');

/*
* Map regeneration in progrees by this script, so, we must include all explicit!
* Futhermore - we must do it with al descending includes in reverted mode (leaf first)!
* this all to do not use autoinclde mechanisms!
**/
include_once('Exceptions/BaseException.php');
include_once('Exceptions/variables.php');
include_once('Exceptions/classes.php');
include_once('Vars/HuClass.php');
include_once('Debug/debug.php');

include_once('Vars/Settings/settings.php');
include_once('Vars/HuArray.php');
include_once('RegExp/RegExp_base.php');
include_once('RegExp/RegExp_pcre.php');
/**
* @uses classCreate()
* @uses RegExp_pcre
**/

$_skip_functions = array(
	'myErrorHandler'		# In mssql_database to catch errors. Hack
	,'backtrace__printout_WEB_helper'
	,'file_get_contents'	# In template old backward capability.
);

###############################################################################################

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
$source = file_get_contents(($inputfile = isset($argv[1]) ? $argv[1] : 'php://stdin')); #$argv[2] optionnaly part of DIR, which must be stripped
$tokens = token_get_all($source);

$class_started = false;
$curly_open = 0;

$res = '';

/**
* We want cut off all Classes, and comments
**/
foreach ($tokens as $token){
//	if (is_array($token)){
//	$token['const_mame'] = array_keys(consts::getNameByValue($token[0], '', '/^T_/', false));
////	$token['const_mame'] = token_name($token[0]);
//	}

	if (is_string($token)){// simple 1-character token
		if ( '{' == $token ){ #All in code
			if ($class_started){
			++$curly_open;
			continue;
			}
			else $res .= $token;
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
		case T_ML_COMMENT:	// we've defined this
		case T_DOC_COMMENT:	// and this
		continue; // no action on comments
		break;

		case T_CLASS:
		case T_INTERFACE: #Do not distinguish for our purpose
		$class_started = true;
		break;

		case T_CURLY_OPEN: //All "{" in double quotes
		case T_DOLLAR_OPEN_CURLY_BRACES: //Variables in text like "value of A={$obj->val}"
			if ($class_started){
			++$curly_open;
			continue;
			}
			else $res .= $text;
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
$m = classCreate('RegExp_pcre', '#function\s+\&?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\(#i', $res)
		->doMatchAll()
			->getHuMatches(1)
				->filter(create_function('&$func', 'global $_skip_functions; return ! in_array($func, $_skip_functions);'));
	if ($m->count()) //Check for the \n
	echo(
		$m
		->walk(
			create_function(
				'&$item'
				,"\$item = \"\t\t'\$item'\t=> '" . classCreate('RegExp_pcre', '#^' . RegExp_pcre::quote(@$argv[2]) . '#', $inputfile)->replace() . "',\";"
			)
		)
		->implode("\n") . "\n"
	);
?>