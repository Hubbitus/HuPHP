<?
/**
 * Database abstraction layer.
 * Documented AFTER creation, in progress.
 * @package Database
 * @subpackage DBError
 * @version 2.0b
 * @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
 * @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
 */

/*-inc
include_once('Debug/HuError.php');
*/
/**
* @uses HuError
**/

class DBError extends HuError{
	/**
	* Constructor.
	* @param Object(DBError_settings)|array	$sets	Initial settings.
	*	If DBError_settings assigned AS IS, if array MERGED with defaults and overwrite
	*	presented settings!
	*/
	public function __construct( /* DBError_settings | array */ $sets){
		if (is_array($sets) and !empty($sets)){ #MERGE, NOT overwrite!
		$this->_sets = new DBError_settings();
		$this->_sets->mergeSettingsArray($sets);
		}
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new DBError_settings();#default
	}#__c
}

class DBError_settings extends HuError_settings{
//Defaults
protected $__SETS = array(
	//Aka-Constants
	'TXT_queryFailed' 	=> 'SQL Query failed',
	'TXT_cantConnect'	=> 'Could not connect to DB',
	'TXT_noDBselected'	=> 'Can not change database',

	/**
	* @see HuError::updateDate()
	*/
	'AUTO_DATE'		=> true,
	'DATE_FORMAT'		=> 'Y-m-d H:i:s: ',

	/** Header for 'extra'-data, which may be present */
	'EXTRA_HEADER'		=> 'Extra info',
/////////////////////////////////////////////////////////////////////////////////////////////
	#In format settings::getString(array)
	#To out in Browser
	'FORMAT_WEB'	=> array (
			array('TXT_queryFailed', "\n<br \><u><b>", "</b></u>:\n<br \>", ''),
		'server_message',
			array('errNo', ' (', ')'),
			array ('server_messageS', "<br \>\n<b>", '</b>'),
		"\n<br><u>On query:</u> ",
			array('Query', '<pre style="color: red">', '</pre>'),
			array('bt', "<br \>\n")
	),
	#In CLI-mode
	'FORMAT_CONSOLE'	=> array (
			array('TXT_queryFailed', "\033[1m", "\033[0m:\n", ''),
		'server_message',
			array('errNo', '(', ')'),
			array('server_messageS', "\n\033[31;1m", "\033[0m"),
		"\n\033[4;1mOn query:\033[0m ",
			array('Query', "\033[31m", "\033[0m"),
			array('bt', "\n")
	),
	#Primarly for logs
	'FORMAT_FILE'	=> array (
			array('TXT_queryFailed', '', ":\n"),
		'server_message',
			array('errNo', '(', ')'),
			array('server_messageS', "\n", ""),
		"\nOn query:",
			array('Query', "=>", "<="),
			array('bt', "\n")
	),
);

/*
protected $static_settings = array('query_failed', 'could_not_connect', 'no_DB_selected');
	**
	* Clear 'nonstatic' 
	* @param
	* @return
	/
	function clear(){
		foreach ($this->__SETS as $key => $sets){
			if (!in_array($key, $this->static_settings))
			$this->__SETS[$key] = null;
		}
	}
*/
}#c DBError_settings
?>