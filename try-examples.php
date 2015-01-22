#!/bin/env php
<?
include('autoload.php');

/*
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=windows-1251"/>
</head>
<body>
*/
//include('template/template_class.php');
//include('Debug/debug.php');
//include('Debug/Phar/HuDebug.phar');
//include_once('autoload.php');


################################################################################################
/*
//БАГ с обращением к строке по ключу
$ttt[1] = 'Pupkin';
dump($ttt[1]['name']);
*/
################################################################################################
/*
dump('Это temp.php');
// the message
$message = "Line 1\nLine 2\nLine 3";
// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70);
// Send
$ttt = mail('pahan@hubbitus.spb.su', 'My Subject', $message);
dump($ttt);
*/
################################################################################################
/*
//c_dump($_ENV['LANG']);
//preg_match('/\.(.*)/', $_ENV['LANG'], $m);
//c_dump($m);

include_once('RegExp/RegExp_pcre.php');
$r = RegExp_pcre::create('/\.(.*)/', $_ENV['LANG']);
dump::a($r->match(1));
dump::a(RegExp_pcre::create('/\.(.*)/', $_ENV['LANG'], 'QAZ')->replace());
//c_dump(RegExp_pcre::getMatch('/\.(.*)/', $_ENV['LANG'], 1));
*/
################################################################################################
/*ternary - binary operator.
//$t ='';
$tt = 'test text';
$ttt = @$t ?: $tt;
c_dump($ttt);
*/
################################################################################################
/*
include_once('Database/database_where.php');
$w = array(
	array('ID' => 1),					#ID=1   # Operator '=' is default. Field is key, value
	array('ID.1.1', 1.1),
	array('ID_second', '1', 'q:>='),			#ID>='1'     # Operator get explicit, owervise '='. One
	array('ID_third', '1', 'BEtWEEN', 20),		#ID BEtWEEN 1 AND 10   # Special case, ternary operator
	'OR',
	array('ID' => array (2, '<=')),		#ID <= 2     # As 1-2, but 2 argument - array. Warning
	array('ID' => array (2, 'BEtWEEN', 15)),#ID BEtWEEN 1 AND 15
	"{Simple instruction}",
	'and',
	'MailNo'	=> array(1, '<'),
	'USER'	=> 5,
	'USERID'	=> array(5),
	array('Name', '[ABC]%', 'q:LIKE'),		#"Name LIKE '[ABC]%'"
	array('Name of field', '[ABC]%', 'qe:LIKE'),
);
$dw = new database_where($w, '`');
c_dump($dw->getSQL(), '$dw->getSQL()');
*/
################################################################################################
/*
include_once('file_base.php');
$f = new file_base();
#								  14 17
#							  10 12 15
#					 012345 6789 11 13 16
//$f->setContentFromString("Line1\nLine2\nLine3\n");
$f->setNames('/var/www/vkontakte.nov.su/include/configs/main_config.php')->loadContent();
c_dump($f->getLineByOffset(50) + 1);

//c_dump($f->getLineByOffset(11));
//c_dump($f->getOffsetByLine(1));
exit();
*/
################################################################################################
/*
//$text = 'Test text';
$text = array('Test', 'Text', 'In', 'Array');
c_dump($text[2], '$text[2]');
c_dump($text{2}, '$text{2}');
exit();
*/
################################################################################################
/*
include_once('file_base.php');
include_once('RegExp/RegExp_pcre.php');
$f = new file_base('temp.php.text');
//$f = new file_base('temp.php.tmp');
$f->loadContent();
//$r = new RegExp_pcre('/text to/i', $f->getBLOB());
$r = new RegExp_pcre('/что он в/iu', $f->getBLOB());
//$r = new RegExp_pcre('/it\s(is)\son/xms', $f->getBLOB());

//$r = new RegExp_pcre('/\bt\s*\((.*?)\s*\)/xms', $f->getBLOB());
//$r = new RegExp_pcre('/function\st\(/xmsu', $f->getBLOB());
$r->doMatchAll(PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
c_dump($r->getMatches());
$m = $r->match(0);
c_dump($m,'$m');
//c_dump(mb_strlen(substr($f->getBLOB(), 0, $m[0][1])));

/*Whith PREG_OFFSET_CAPTURE preg_match* returns bytes offset!!!! nor chars!!!!
So, recalculate it in chars is several methods:
1) Using utf8_decode. See http://ru2.php.net/manual/ru/function.strlen.php comment "chernyshevsky at hotmail dot com"
2) And using mb_strlen http://ru2.php.net/manual/ru/function.preg-match.php comment "chuckie"
I using combination of its. And it independent of the presence mbstring extension!
*/
//c_dump(strlen(utf8_decode(substr($f->getBLOB(), 0, $m[0][1]))));
//exit();
################################################################################################
/*
include_once('Consts/consts.php');
c_dump(consts::get_regexp('', '#.*?MAX|MIN.*?#i'));
exit();
*/
################################################################################################
/*
class A{
	static function staticmethod(){
	callParams();
	}

	function method($arg){
	callParams();
	}
}#c A

//A::staticmethod();
//$a = new A;
//$a->method(@$methodArg);

function t($tempArg, $arg1 = 'arg1', $arg2 = 'arg2,arg,2', $arg3='arg3', $arg4='arg4'){
callParams(1);
}
#
function tttt(){
return '3ARG';
}

$tt = 7;
$ttt = array('QWERtY', 'qaz' => 'QAZ');
$testVar = 'Has test contenT';
$the = '_THE_';
//t(5);

t($tt,
	$ttt[0]
	,$ttt['qaz']
				,tttt()//Test function Call
				,

				"exampleFunc() call\" $testVar, in ${the} string too")
;
*/
################################################################################################
/*
include_once('macroses/REQUIRED_VAR.php');

////////////////
include_once('Debug/HuLOG.php');#For defined constants
$__CONFIG['HuLog'] = array(
	'FILE_PREFIX'		=> 'log_',
	'LOG_FILE_DIR'		=> './',

	'LOG_TO_ACS' => HuLOG_settings::LOG_TO_BOTH,
	'LOG_TO_ERR' => HuLOG_settings::LOG_TO_BOTH,

	// In SUBarray in order not to generate extra Entity
	'HuLOG_Text_settings'	=> array(
		'EXTRA_HEADER'	=> null,	#NOT false!
	),
);
////////////////
include_once('Debug/backtrace_out.class.php');

try{
$ttt = REQUIRED_VAR($t);
}
catch(VariableRequiredException $vre){
//Direct output:

#Work:
#dump::a($vre->varName());
#$vre->bt->printout();

#(Was!) NOT work:
#$vre->bt->printout();
#dump::a($vre->varName());
#exit();

//Direct output without logging and only in 1 presentation:
echo '0) Требуется переменная: ' . $vre->varName() . "\n" . $vre->bt->printout(true, null, OS::OUT_TYPE_FILE);
#Direct, with autoselect appropriate output type:
echo '1) Требуется переменная: ' . $vre->varName() . "\n" . $vre->bt->printout(true);
//rely on __toString magick method, which is invoke ->printout(). Be warned - implicit casting to string is required if we want pass it anywhere, if echoed - it is not needed.!
echo '2) Требуется переменная: ' . $vre->varName() . "\n" . $vre->bt;
////Single::def('HuLog')->toLog('Database error, '.$dbe->getMessage(), 'ERR', 'db', Single::def(__db)->getError());
Single::def('HuLog')->toLog('Требуется переменная: ' . $vre->varName(), 'ERR', 'var', new backtrace_out($vre->bt));
}
exit();
*/
################################################################################################
//include_once('Tokenizer.php');
//dump::c(Tokenizer::trimQuotes('"Test text\''), 'Test Tokenizer::trimQuotes');
################################################################################################
/*
dump::auto('Test', 'TEST');
$ttt = 'QWERTY';
dump::auto('Test2');
dump::auto($ttt);
#c_dump($ttt);#NOT correct in this form. Do NOT use old shorthands.
$tt = array('arg0' => 0, '1' => 'arg1');
echo dump::log($tt);
echo dump::log($tt, 'eto $tt');
dump::a($tt);
*/
################################################################################################
//dump::c(mb_strlen('Test'));
################################################################################################
/*
dump::a(php_sapi_name());
dump::a(PHP_SAPI);
dump::a(@$_SERVER['HTTP_USER_AGENT']);
dump::a(@get_browser());
*/
################################################################################################
/*
include_once('HuError.php');
$t = new stdClass();
$t->prop1 = 'PROP1';
$t->prop2 = 'PROP2';
$t->prop77 = 'PROP77';

$e = new HuError();
$e->setFromArray(
	array(
		'Beda' => 'Ой бяда-бяда-бяда',
		'ErrType' => 'fatal',
		'Severity' => 77,
		'extra'	=>& $t
	)
);

//$e->setSetting('date', 777);
//echo $e->date;

//echo $e->strToConsole()."\n";
//echo $e->strToWeb()."\n";
//echo $e->strToFile()."\n";
//echo $e->strToPrint()."\n";
//dump::a($e->Beda);
//echo $e;
*/
################################################################################################
/* http://ru2.php.net/manual/ru/language.operators.comparison.php
$t = 0;
$tt = 'text()';
//$tt = "13";
var_dump((string)$t == $tt);
exit();
*/
################################################################################################
/*
class A{
function tt($a1, $a2, $a3){
return $this->t($a1, $a2, $a3);
}

protected function t($arg1, $tt, $t3 = null, $t4 = null){
dump::a($arg1);

dump::a(func_num_args());
dump::a(func_get_args());
dump::a(@func_get_arg(0));
//dump::a(func_get_arg(1));
//dump::a(func_get_arg(2));
}
//$b = new backtrace();
//$b = backtrace::create();
//backtrace::create(null, 0)->dump();
//backtrace::create()->dump();

}#f
$a = new A;
$a->tt('arg1', 'arg2', null);
*/
################################################################################################
/*
class A{
public $publicProperty = 'publicProperty_VALUE';
protected $protectedProperty = 'protectedProperty_VALUE';
private $privateProperty = 'privateProperty_VALUE';

	public function __get($name){
		if ($name == '__getExist') return '__getExistProperty_VALUE';
	}

	public function __isset($varname) {
	echo "ZOMG! isset was called on my $varname!\n";
		if ($varname == '__getExist') return true;
	}
}
$a = new A;

var_dump('$a->publicProperty', $a->publicProperty);
var_dump('isset($a->publicProperty)', isset($a->publicProperty));
var_dump('$a->protectedProperty', $a->protectedProperty);
var_dump('isset($a->protectedProperty)', isset($a->protectedProperty));
var_dump('$a->privateProperty', $a->privateProperty);
var_dump('isset($a->privateProperty)', isset($a->privateProperty));
var_dump('$a->__getExist', $a->__getExist);
var_dump('isset($a->__getExist)', isset($a->__getExist));
var_dump('$a->__getNOT_Exist', $a->__getNOT_Exist);
var_dump('isset($a->__getNOT_Exist)', isset($a->__getNOT_Exist));
*/
################################################################################################
/*
interface X {}
class A {}
class B extends A {}
class C extends B {}
class D implements X {}
class E {};

$a = new A;
$b = new B;
$c = new C;
$d = new D;

dump::a($a instanceof A);	#true
dump::a($a instanceof E);	#false
dump::a($b instanceof A);	#true
dump::a($c instanceof A);	#true
dump::a($d instanceof X);	#true
*/
################################################################################################
/*
class A{
	public function __construct(){
	return $this;
	}

	public function aa(){
	echo 'Function aa'."\n";
	}
}

$a = new A;
$a->aa();

//new A()->aa();	//NOT worked :(
try{
	//echo (string)$a;	// NOT cathable!!! :( , but named: PHP Catchable fatal error
	if (!is_object($a)) print $a;
}
catch (Exception $e){ echo 'Esceprion catched!'."\n"; }

$t = 'string';
dump::a($t instanceof A); //Not, and without errors and warnings

//var_dump(spl_object_hash(new stdClass()), spl_object_hash(new stdClass()));
*/
################################################################################################
/*
//dump::a(DIRECTORY_SEPARATOR);
include_once('System/OS.php');

echo "temp.php 0\n";
//include('Not existent file');
//dump::a(@require('include.php'));
//dump::a(@include('include.php'));
dump::a(OS::is_includeable('include.php', false));
echo "temp.php 1\n";
//echo TTT;
*/
################################################################################################
//echo strlen( ( !(!@$str && (include_once('ttt.php')) || true) ?: $str ) )."\n";
################################################################################################
/*
$t = 7;
//Not Worked :( $ttt =& $tt =& $t;
$tt =& $t;
$ttt =& $t;
*/
################################################################################################
/*
class A{
public $t = 'tt';

	function __get($name){
	return 'ProP: '.$name.';';
	}
}

$a = new A;
dump::a($a->t);
dump::a($a->None);
dump::a($a->{1});
dump::a($a->{'Это русский текст в качестве propertyname'});
*/
################################################################################################
/*
include_once('Debug/HuFormat.php');

//$val = '<b>bold text</b>';
$val = array(
	'text' => 'Test text',
	'string' =>'<b>bold text</b>'
);


$f = array(
	'string'	=> array(
		'ae',	//Modifyer
//		'$var'//As Is
		'htmlspecialchars(substr($var, 0, 64)).((strlen($var) < 64) ?: \'...\')'
	)
);

//$hf = new HuFormat($f, $val);
$hf = new HuFormat;
$hf->set($f, $val);
echo $hf->getStr();
////////////////
$bt = new backtrace;
$bt->printout();
*/
################################################################################################
/*
$t = array('one', 'two');
$tt = array('one' => 'ONE', 'two' => 'TWO');
$ttt = array();
dump::a($t[0]);
dump::a(@$tt[0]);
dump::a(@$ttt[0]);
dump::a($t[key($t)]);
dump::a($tt[key($tt)]);
dump::a(@$ttt[key($ttt)]);
*/
################################################################################################
/*
include_once('System/Class.php');
//class A{
class A extends HuClass{
const className = 'A';
	function aa(){
	dump::a('Method ' . __METHOD__);
	}
}

class B extends HuClass{
//const className = 'B';

	function __construct(){
	$var = 'init';
	}
}

//Work
//$a = new A();
//$a->aa();


//(new A)->aa();
//A::__construct()->aa();
//A::A()->aa();

//A::create()->aa();
B::create();
*/
################################################################################################
/*
function test($t){
dump::a((boolean)@$t);
dump::a(is_null(@$t));
dump::a(empty($t));
}

echo "''\n";
test('');
echo "null\n";
test(null);
echo "0\n";
test(0);
echo "'0'\n";
test('0');
*/
################################################################################################
/*
dump::a($argv);

include_once('Vars/HuArray.php');
//$arg = '--what="Test text"';
$arg = '--what=';
//$arg = '--what "Test text"';

$start_long = array('--');
$optsL = array('what');

$re = new RegExp_pcre(
	( $reg = '/^('.implode('|', RegExp_pcre::quote($start_long)).')('.implode('|', $optsL).
	')(=|\b)(.*)/' ),
	$arg);
$re->doMatch();
dump::a($reg);
dump::a($re->getMatches());
dump::a($re->match(0));

// dump::a($re->match(0)[1]); //Error!
//dump::a(HuArray::create($re->match(0))->{0});
//dump::a(RegExp_pcre::quote($arr));
//dump::a(RegExp_pcre::quote($arr[2]));
*/
################################################################################################
/*
function f1(){
echo 'f1'."\n";
//return false;
return 'f1';
}

function f2(){
echo 'f2'."\n";
return 'f2';
}

function f_or(){
//$t = array( 1, 2, 3);
//return @$tt || @$t;	//BOOL
return ( ($r =& f1()) ? $r : f2());
}

dump::a(f_or());
*/
################################################################################################
/*
class A{
public $publicvar = '7';

	function getRef(){
	return $this->publicvar;
	}

	function &__get($var){
	dump::a($var);
		if ('_last_' == $var) return $this->publicvar;
	}#m __get

	function __set($var, $value){
	dump::a($var, '__set');
		if ('_last_' == $var){
		//$this->publicvar = $value;
		$ref =& $this->getRef();
		$ref = 78;
		}
	}#m __set
}#c A

$a = new A;
dump::a($a);
dump::a($a->_last_);
$ttt = $a->_last_ = 77;
//$a->qwerty = 777;
//$a->publicvar = 88;
dump::a($a);
dump::a($a->_last_);
dump::a($ttt);
*/
################################################################################################
/*
dump::a($argv);
$cont = file_get_contents('php://stdin');
dump::a($cont);

$ttt = array($argv[4]);
dump::a($ttt);
array_walk($ttt, create_function('&$v', 'eval(\'$v ="\' . addcslashes($v, \'"\') . \'";\');'));
dump::a($ttt);

eval('$to = "'.addcslashes($ttt[0], '"').'";');
dump::a($to);

//$res = preg_replace($argv[2], $argv[4], $cont);
$res = preg_replace($argv[2], $to, $cont);
dump::a($res);
//#For string-eval esape characters like \n, \t, \r and other!
//eval('$nRes = "'.addcslashes($res, '"').'";');
//dump::a($nRes);

//$t = 'n';
//$tt = "=\\$t=";
//dump::a($tt);
*/
################################################################################################
/*
$srcArr = array (
	"start_perms"	=> "",
	"reply_perms"	=> "",
	"read_perms"	=> "*",
	"upload_perms"	=> "",
	"show_perms"	=> "*",
	"download_perms" => ""
);
dump::a($srcArr);

xdebug_var_dump($srcArr);
*/
################################################################################################
/*
$length = 10000;

$direct = str_pad('', $length, '-=').'END';
//dump::a($direct);
eval('$evalresult = str_pad("", '.$length.', "-=")."END";');
//dump::a($evalresult);

//$ttt = 'LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPS09LT0tPQ==';
//dump::a($ttt);
//dump::a(base64_decode($ttt));
*/
################################################################################################
/*
phpinfo();
dump::a(0 | 2);
dump::a(ini_get('xdebug.overload_var_dumpQ'));
dump::a((bool)ini_get('xdebug.overload_var_dumpQ'));
*/
################################################################################################
/*
include_once('phar:///var/www/_SHARED_/Debug/Phar/HuDebug.phar/Debug/backtrace.php');

function f( $arg0 ){
$bt = backtrace::create()->printout();
}
f(6);

//backtrace::create()->printout();
*/
################################################################################################
/*
include_once('Strings/charset/iconv_charset_convert.php');
//error_reporting(E_ALL ^ E_NOTICE);
$testText = '����';
//echo iconv('CP1251_TTT', 'UTF-8', '����') . "\n";
//echo iconv_charset_convert::conv($testText, 'CP1251', 'UTF-8') . "\n";
//echo iconv_charset_convert::conv($testText, 'CP1251') . "\n";
echo iconv_charset_convert::conv($testText, 'CP1251_TTT') . "\n";
*/
################################################################################################
/*
//function is_set($what, &$where){
//	if (is_string($where) and !is_numeric($what)) return false;
//	else return isset($where[$what]);
//}
include_once('macroses/IS_SET.php');

$str = "Some text, NOT array";
dump::a(isset($str["qwerty"]));	//Always TRUE!!!
dump::a(is_set('qwerty', $str));	//Always TRUE!!!
dump::a(isset($str[5]));		//Work
dump::a(is_set(5, $str));	//Work
dump::a(isset($str[500]));	//Work
dump::a(is_set(500, $str));	//Work
dump::a(isset($str[-5]));	//Work
dump::a(is_set(-5, $str));	//Work
dump::a(isset($str{5}));		//Work
dump::a(isset($str{-5}));	//Work
*/
################################################################################################
//$argv[1] = '/var/www/_SHARED_/template/template_class_2.1.php';
#$argv[1] = '/home/pasha/NetBeansProjects/temp/web/example.php';
#include('/var/www/_SHARED_/.tools/phpsource.extract_functions.php');
################################################################################################
/*
include_once('Vars/HuArray.php');
$ha = new HuArray(
	array(
		'one' => 1
		,'two' => 2
		,'arr' => array(0, 11, 22, 777)
	)
);
dump::a($ha->one);
dump::a($ha->arr);					// Result Array (raw, as is)!
dump::a($ha->hu('arr'));				// Result HuArray (only if result had to be array, as is otherwise)!!! Original modified in place!
dump::a($ha->hu('arr')->hu(2));		// Property access. Alse as any HuArray methods like walk(), filter() and any other.
dump::a($ha->{'hu://arr'}->{'hu://2'});	// Alternate method. Fully equivalent of line before. Just another form.
*/
################################################################################################
//include_once('Vars/HuArray.php');
$testArray = array(0, 11, 22, 777);
$ha = new HuArray( $testArray );
dump::a($ha, 'Original HuArray');

dump::a( $ha->filterByKeys(array(0,1)) );

$ha->setSettingsArray( $testArray );
dump::a($ha, 'Renewed to original');

dump::a( $ha->filterOutByKeys(array(0,2)) );

$ha->setSettingsArray( $testArray );
dump::a($ha, 'Renewed to original');

dump::a( $ha->filterKeysCallback( create_function('&$key', 'return ( $key % 2 );') ) );