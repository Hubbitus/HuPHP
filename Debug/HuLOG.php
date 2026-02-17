<?php
declare(strict_types=1);

/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuLOG
* @version 2.0.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-05-30 23:19
**/

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\HuLOGSettings;
use Hubbitus\HuPHP\Debug\HuLOGText;
use Hubbitus\HuPHP\Vars\NullClass;
use Hubbitus\HuPHP\Vars\IOutExtraData;
use Hubbitus\HuPHP\Vars\OutExtraDataCommon;
use Hubbitus\HuPHP\Vars\Settings\SettingsGet;

class HuLOG extends SettingsGet{//HubbitusLOG :) log занял давно, для совместимости старого кода не заменяю имя!
	public $_level = 0;//Для установки уровней вложенности логовых сообщений в файле

	protected ?HuLOGText $lastLogText = null;
	protected $lastLogTime = null;

	protected $_sets = null;

	public function __construct (HuLOGSettings|array $sets = null){
		if (\is_array($sets)) $this->_sets = new HuLOGSettings((array)$sets);
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new HuLOGSettings();//Default
		$this->lastLogText = new HuLOGText ($this->settings->HuLOG_Text_settings);
	}

	private function log_to_file($file='ERR'){
//	exec('echo -ne '.escapeshellarg($this->lastLogText->strToFile($this->lastLogText->settings->FORMAT_FILE)).' >> '.$this->settings->LOG_FILE_DIR.$this->settings->FILE_PREFIX.$file.' 2>&1');
		file_put_contents(
			$this->settings->LOG_FILE_DIR.$this->settings->FILE_PREFIX.$file,
			$this->lastLogText->strToFile($this->lastLogText->settings->FORMAT_FILE),
			FILE_APPEND
		);
	}
	private function log_print(){
		echo $this->lastLogText->strToPrint();
	}
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
				'extra'	=> ( ($extra instanceof IOutExtraData) ? $extra : new OutExtraDataCommon($extra))	//Additional extra data
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
			$to = HuLOGSettings::LOG_TO_BOTH;
			$file = 'ERR';
			$this->makeLogString('Does not provided file for log and flavour!', $file, 'HuLOG', null);
			$this->writeLogs($to, $file);
		}

		/**
		* In PHP 5.1.6 without temporary variable $func_num_args we got error:
		* Fatal error: func_num_args(): Can't be used as a function parameter in /home/www/_shareDInclude_/_2.0_/Debug/HuLOG.php on line 162
		**/
		$func_num_args = func_num_args();
		$this->makeLogString($log_string, $file, $type, ($func_num_args > 3 ? $extra : new NullClass) );
		$this->writeLogs($to, $file);
	}
	protected function writeLogs($to, $file){
		if ( $to & HuLOGSettings::LOG_TO_FILE ) $this->log_to_file($file);
		if ( $to & HuLOGSettings::LOG_TO_PRINT ) $this->log_print();
	}
}