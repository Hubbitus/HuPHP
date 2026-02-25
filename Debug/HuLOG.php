<?php
declare(strict_types=1);
namespace Hubbitus\HuPHP\Debug;

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
* @property HuLOGSettings $settings Settings object
**/

use Hubbitus\HuPHP\Debug\HuLOGSettings;
use Hubbitus\HuPHP\Debug\HuLOGText;
use Hubbitus\HuPHP\Debug\IHuLOGFormatter;
use Hubbitus\HuPHP\Vars\NullClass;
use Hubbitus\HuPHP\Vars\IOutExtraData;
use Hubbitus\HuPHP\Vars\OutExtraDataCommon;
use Hubbitus\HuPHP\Vars\Settings\SettingsGet;

class HuLOG extends SettingsGet{//HubbitusLOG
	public $_level = 0;

	protected ?HuLOGText $lastLogText = null;
	protected $lastLogTime = null;

	protected $_sets = null;

	protected ?IHuLOGFormatter $formatter = null;

	public function __construct (HuLOGSettings|array|null $sets = null, ?IHuLOGFormatter $formatter = null){
		if (\is_array($sets)) {
			$this->_sets = new HuLOGSettings($sets);
		} elseif($sets !== null) {
			$this->_sets = $sets;
		} else {
			$this->_sets = new HuLOGSettings();
		}
		/** @phpstan-ignore property.notFound */
		$this->lastLogText = new HuLOGText ($this->settings->HuLOG_Text_settings);
		$this->formatter = $formatter ?? new HuLOGTextFormatter();
	}

	private function log_to_file($file='ERR'): void {
//	exec('echo -ne '.escapeshellarg($this->lastLogText->strToFile($this->lastLogText->settings->FILE)).' >> '.$this->settings->LOG_FILE_DIR.$this->settings->FILE_PREFIX.$file.' 2>&1');
		/** @phpstan-ignore property.notFound */
		$logDir = $this->settings->LOG_FILE_DIR;
		/** @phpstan-ignore property.notFound */
		$filePrefix = $this->settings->FILE_PREFIX;
		\file_put_contents(
			$logDir . $filePrefix . $file,
			$this->formatter->formatForFile($this->lastLogText),
			FILE_APPEND
		);
	}
	private function log_print(){
		echo $this->formatter->formatForPrint($this->lastLogText);
	}
	protected function makeLogString($log_string, $file, $type, $extra){
		$this->lastLogTime = time();
		$this->lastLogText->setSettingsArray(
			($extra instanceof NullClass) /* EXPLICIT check what $extra was provided! Null also possible value, what must be dumped, if it provided, It can't be ignored also as any other predefined value! **/
			?
			[
				'level'	=> \sprintf('% ' . (((int)$this->_level)*2) . 's', ' '),	//Отступ
				'type'	=> $type,			//Type-prefix
				'logText'	=> $log_string,	//Main text!
			]
			:
			[
				// Now auto or disabled
//-				'date'	=> date($this->_sets->DATE_TIME_FORMAT, $this->lastLogTime),//Дата-время
				'level'	=> \sprintf('% ' . (((int)$this->_level)*2) . 's', ' '),	//Отступ
				'type'	=> $type,			//Type-prefix
				'logText'	=> $log_string,	//Main text!
				'extra'	=> ( ($extra instanceof IOutExtraData) ? $extra : new OutExtraDataCommon($extra))	//Additional extra data
			]
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
	public function toLog($log_string, $file='ERR', $type='', $extra=null): void {
		/** @phpstan-ignore property.notFound */
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
	protected function writeLogs(int $to, string $file): void {
		if (($to & HuLOGSettings::LOG_TO_FILE) !== 0) $this->log_to_file($file);
		if (($to & HuLOGSettings::LOG_TO_PRINT) !== 0) $this->log_print();
	}
}