<?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @version 2.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-12-06 17:20 ver 2.0 to 2.1
*	Correct argtype Array and String for log (FORMAT_FILE) output. Make it same as for console
**/


/**
* Helper to more flexibility show large amount of data (long strings, dump of arrays etc.)
*
* @param string	$shortVar
* @param string	$longVar
* @return string
*
**/
function backtrace__printout_WEB_helper($shortVar, $longVar, $innerTagStart = '<textarea', $innerTagEnd='</textarea>'){
return '\'<span title="'.$longVar.'"
 onclick=\\\'this.bakonclick=this.onclick; this.onclick=null; var ttt = this.innerHTML; this.innerHTML="'.$innerTagStart.' style=\"color: green; width: 50em; height: 7em; overflow: auto\" ondblclick=\"this.parentNode.onclick=this.parentNode.bakonclick; var ttt=this.parentNode.title; this.parentNode.title=( this.defaultValue ? this.defaultValue : this.innerHTML); this.parentNode.innerHTML = ttt; \">" + this.title + "'.$innerTagEnd.'"; this.title = ttt;\\\'>'.$shortVar.'</span>\'';
}#f backtrace__printout_WEB_helper

/** For format description see {@link HuFormat class} **/
$GLOBALS['__CONFIG']['backtrace::printout'] = array(
	'FORMAT_WEB'	=> array(
		'A:::' => array(
			"<div style='text-align: left; font-family: monospace; padding-left:2em'>\n<b style='color: brown'>Backtrace:</b><br />",
			array(
				'I:::call' => array(
					'A:::' =>	array(
						'<b style=\'color: green\'>call:</b> ',
						array('sn:::class', '<span style=\'color: purple\'>', '</span>'),
						array('sn:::type', '<span style=\'color: brown\'>', '</span>'),
						array('sn:::function', '<span style=\'color: magenta\'>', '</span>'),
						array('E:::args', '\'(\'.$var->formatArgs().\')\''),
						"<br />\n",

						'&rArr;&rArr;<b style=\'color: red\'>file:</b> ',
						array('sn:::file', '<u>', '</u>'),
						' - ',
						array('sn:::line', '<b style=\'background-color: orange\'>', '</b>'),
						"<br />\n"
					),
				),

			),
			"</div>\n",
		),

		'argtypes'	=> array(
			'integer'	=> array('v:::'),//As is
			'double'	=> array('v:::'),
			'string'	=> array('E:::', backtrace__printout_WEB_helper('\\\'\'.htmlspecialchars(substr($var, 0, 32)).((($sl = strlen($var)) < 32) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}', '\'.htmlspecialchars($var).\'')),
			'array'	=> array('E:::', backtrace__printout_WEB_helper('\'.\'Array(\'.sizeof($var).\')\'.\'', '\'.htmlspecialchars(dump::byOutType(OS::OUT_TYPE_BROWSER, $var, null, true)).\'', '<div style=\"display: table; border: thick dashed green; border-top: none\"', '</div>')),
			'object'	=> array('E:::', '\'Object(\'.get_class($var).\')\''),
			'resource'=> array('E:::', '\'Resource(\'.strstr($var, \'#\').\')\''),
			'boolean'	=> array('E:::', '$var ? \'True\' : \'False\''),
			'NULL'	=> 'Null',
			'default'	=> array('n:::', 'Unknown (', ')'),
		),
	),

	'FORMAT_CONSOLE'	=> array(
		'A:::' => array(
			"\033[1mBacktrace:\033[0m\n",
			array(
				'I:::call' => array(
					'A:::' =>	array(
						"\033[32mcall:\033[0m ",
						array('sn:::class', "\033[35m", "\033[0m"),
						array('sn:::type', "\033[33m", "\033[0m"),
						array('sn:::function', "\033[35;1m", "\033[0m"),
						array('E:::args', '"\033[33m(\033[0m".$var->formatArgs()."\033[33m)\033[0m"'),
						"\n",

						"\t->\033[31;1mfile: \033[0m",
						array('sp:::file', '%s', '__vAr__'),
						':',
						array('sn:::line', "\033[43;1m", "\033[0m"),
						"\n",
					),
				),
			),
		),
	),
	'FORMAT_FILE'	=> array(
		'A:::' => array(
			"Backtrace:\n",
			array(
				'I:::call' => array(
					'A:::' =>	array(
						'call: ',
						array('s:::class'),
						array('s:::type'),
						array('s:::function'),
						array('E:::args', '\'(\'.$var->formatArgs().\')\''),
						"\n",

						'file: ',
						array('s:::file'),
						':',
						array('s:::line'),
						"\n",
					),
				),
			),
		),
	),
);
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']	=  $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB']['argtypes'];
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes']	=& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB']['argtypes'];
#Difference in argTypes
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes']['string']
	= $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']['string']
	= array('E:::', '\'\\\'\'.htmlspecialchars(substr($var, 0, 28)).((($sl = strlen($var)) < 28) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}\'');
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes']['array']
	= $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']['array']
	= array('E:::', '\'Array(\'.count($var).\')\'');

$GLOBALS['__CONFIG']['backtrace::printout'][OS::OUT_TYPE_BROWSER]		=& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB'];
$GLOBALS['__CONFIG']['backtrace::printout'][OS::OUT_TYPE_CONSOLE]		=& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']; 
$GLOBALS['__CONFIG']['backtrace::printout'][OS::OUT_TYPE_FILE]			=& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE'];
?>