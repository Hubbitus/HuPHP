<?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuLOG
* @version 2.0.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-05-30 23:19
*
* @uses settings
* @uses NullClass
* @uses commonOutExtraData
* @uses HuError
* @uses OS
**/

include_once('macroses/REQUIRED_VAR.php');
include_once('macroses/EMPTY_STR.php');

class HuLOG_settings extends settings{
	const LOG_TO_FILE	= OS::OUT_TYPE_FILE; // To file
	const LOG_TO_PRINT	= OS::OUT_TYPE_PRINT; // To stdout (print, echo)
	// Unfortunetly PHP does NOT support computed value of constants
	//const LOG_TO_BOTH	= OS::OUT_TYPE_FILE + OS::OUT_TYPE_PRINT;	//to both
	const LOG_TO_BOTH	= 12; // to both

protected $__SETS = array(
	'FILE_PREFIX'		=> 'log_',
	'LOG_FILE_DIR'		=> './log/',

	'LOG_TO_ACS'		=> self::LOG_TO_BOTH,
	'LOG_TO_ERR'		=> self::LOG_TO_BOTH,

	/** In SUBarray in order not to generate extra Entity
	'HuLOG_Text_settings' => array(
		// Here may be overwritten defaults settings. {@see HuLOG_text_settings}
	)
	*/
);
}#c HuLOG_settings

class HuLOG_text extends HuError{
	/**
	* Constructor.
	*
	* @param Object(HuLOG_text_settings)|array	$sets	Initial settings.
	*	If HuLOG_text_settings assigned AS IS, if array MERGED with defaults and overwrite
	*	presented settings!
	**/
	public function __construct( /* HuLOG_text_settings | array */ $sets){
		if (is_array($sets) and !empty($sets)){ //MERGE, NOT overwrite!
			$this->_sets = new HuLOG_text_settings();
			$this->_sets->mergeSettingsArray($sets);
		}
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new HuLOG_text_settings();//default
	}#__c
}#c HuLOG_text

class HuLOG_text_settings extends HuError_settings{
	protected $__SETS = array(
		/**
		* @see HuError::updateDate()
		*/
		'AUTO_DATE'		=> true,
		'DATE_FORMAT'		=> 'Y-m-d H:i:s:',

		/** Header for 'extra'-data, which may be present */
		'EXTRA_HEADER'		=> 'Extra info',

		/** In format {@link settings::getString()} */
		'FORMAT_CONSOLE'	=> array(	//Формат вывода для отладки
			array('date', "\033[36m", "\033[0m"),
			'level',
			array('type', "\033[1m", "\033[0m: ", ''),//Bold
			'logText',
			array('extra', "\n"),
			"\n"
		),
		'FORMAT_WEB'		=> array(
			array('date', "<b>", "</b>"),
			'level',
			array('type', "<b>", "</b>: ", ''),
			'logText',
			array('extra', "<br\\>\n"),
			"<br\\>\n"
		),
		'FORMAT_FILE'		=> array(
			'date',
			'level',
			array('type', '', ': ', ''),
			'logText',
			array('extra', "\n"),
			"\n"
		)
	);
}#c HuLOG_text_settings

class HuLOG extends get_settings{//HubbitusLOG :) log занял давно, для совместимости старого кода не заменяю имя!
	public $_level = 0;//Для установки уровней вложенности логовых сообщений в файле

	protected $lastLogText /*HuLOG_text*/= null;
	protected $lastLogTime = null;

	protected $_sets = null;

	function __construct (/* HuLOG_settings OR array*/ $sets = null){
		if (is_array($sets)) $this->_sets = new HuLOG_settings((array)$sets);
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new HuLOG_settings();//Default
		$this->lastLogText = new HuLOG_text ($this->settings->HuLOG_Text_settings);
	}

	private function log_to_file($file='ERR'){
//	exec('echo -ne '.escapeshellarg($this->lastLogText->strToFile($this->lastLogText->settings->FORMAT_FILE)).' >> '.$this->settings->LOG_FILE_DIR.$this->settings->FILE_PREFIX.$file.' 2>&1');
		file_put_contents(
			$this->settings->LOG_FILE_DIR.$this->settings->FILE_PREFIX.$file,
			$this->lastLogText->strToFile($this->lastLogText->settings->FORMAT_FILE),
			FILE_APPEND
		);
	}#m log_to_file

	private function log_print(){
		echo $this->lastLogText->strToPrint();
	}#m log_print

	protected function makeLogString($log_string, $file, $type, $extra){
		$this->lastLogTime = time();
		$this->lastLogText->setSettingsArray(
			($extra instanceof NullClass) /* EXPLICIT check what $extra was provided! Null also possible value, what must be dumped, if it provided, It can't be ignored also as any other predefined value! **/
			?
			array(
				'level'	=> sprintf('% ' . (((int)$this->_level)*2) . 's', ' '),	//Отступ
				'type'	=> $type,			//Type-prefix
				'logText'	=> $log_string,	//Main text!
			)
			:
			array(
				// Now auto or disabled
//-				'date'	=> date($this->_sets->DATE_TIME_FORMAT, $this->lastLogTime),//Дата-время
				'level'	=> sprintf('% ' . (((int)$this->_level)*2) . 's', ' '),	//Отступ
				'type'	=> $type,			//Type-prefix
				'logText'	=> $log_string,	//Main text!
				'extra'	=> ( ($extra instanceof outExtraData) ? $extra : new commonOutExtraData($extra))	//Additional extra data
			)
		);
	}

	/**
	* Main method to log messages
	*
	* @param $log_string - собственно строка в лог, сразу после даты
	* @param $file:
	*	* ERR - Ошибки
	*	* ACS - Доступ (ACesS)
	* @param $extra - Любая дополнительная переменная, информация, комментарии...
	**/
	public function toLog($log_string, $file='ERR', $type='', $extra=null){
		if ( ! ($to = $this->settings->getProperty('LOG_TO_'.$file)) ){
			//От себя (HuLOG) пишем в лог
			$to = HuLOG_Settings::LOG_TO_BOTH;
			$file = 'ERR';
			$this->makeLogString('НЕ задан файл, куда логгить и как!', $file, 'HuLOG', null);
			$this->writeLogs($to, $file);
		}

		/**
		* In PHP 5.1.6 without temporary variable $func_num_args we got error:
		* Fatal error: func_num_args(): Can't be used as a function parameter in /home/www/_shareDInclude_/_2.0_/Debug/HuLOG.php on line 162
		**/
		$func_num_args = func_num_args();
		$this->makeLogString($log_string, $file, $type, ($func_num_args > 3 ? $extra : new NullClass) );
		$this->writeLogs($to, $file);
	}#m toLog

	protected function writeLogs($to, $file){
		if ( $to & HuLOG_Settings::LOG_TO_FILE ) $this->log_to_file($file);
		if ( $to & HuLOG_Settings::LOG_TO_PRINT ) $this->log_print();
	}#m writeLogs
}//c HuLOG
?>