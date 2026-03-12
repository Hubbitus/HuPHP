<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Vars\IOutExtraData;
use Hubbitus\HuPHP\Vars\NullClass;
use Hubbitus\HuPHP\Vars\OutExtraDataCommon;

/**
* Debug and backtrace toolkit.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @property HuLOGSettings $settings Settings object
**/
class HuLOG { //HubbitusLOG
	public int $_level = 0;
	protected ?HuLOGText $lastLogText = null;
	protected ?int $lastLogTime = null;
	protected ?HuLOGSettings $_sets = null;
	protected ?IHuLOGFormatter $formatter = null;

	/**
	* Constructor.
	*
	* @param ?HuLOGSettings $sets Settings object. If null - instanced default.
	* @param ?IHuLOGFormatter $formatter Formatter object. If null - instanced default.
	**/
	public function __construct(?HuLOGSettings $sets = null, ?IHuLOGFormatter $formatter = null) {
		$this->_sets = $sets ?? new HuLOGSettings();
		// Create HuLOGText with default settings (HuLOG_Text_settings not used by default)
		$this->lastLogText = new HuLOGText(new HuLOGTextSettings());
		$this->formatter = $formatter ?? new HuLOGTextFormatter();
	}

	/**
	* Overloading to provide ref on settings object.
	* In this case change settings is allowed, but change full settings object - not!
	*
	* @param string $name
	* @return HuLOGSettings|null Object of settings.
	**/
	public function &__get(string $name): mixed {
		if ('settings' === $name) {
			return $this->_sets;
		}
		return null;
	}

	/**
	* Get settings object
	* @return HuLOGSettings|null
	**/
	public function &getSettings(): ?HuLOGSettings {
		return $this->_sets;
	}

	/**
	* Main method to log messages
	*
	* @param string $log_string - log string, immediately after date
	* @param string $file:
	*   * ERR - Errors
	*   * ACS - Access (ACcesS)
	* @param string $type
	* @param mixed $extra - Any additional variable, information, comments...
	**/
	public function toLog(string $log_string, string $file = 'ERR', string $type = '', mixed $extra = null): void {
		$to = $this->settings->getProperty('LOG_TO_' . $file);
		if ($to === null || $to === '' || $to === false) {
			//От себя (HuLOG) пишем в лог
			$to = HuLOGSettings::LOG_TO_BOTH;
			$file = 'ERR';
			$type = 'HuLOG';
			$extra = null;
			$this->makeLogString('Does not provided file for log and flavour!', $file, $type, $extra);
			$this->writeLogs($to, $file);
		}

		/**
		* In PHP 5.1.6 without temporary variable $func_num_args we got error:
		* Fatal error: func_num_args(): Can't be used as a function parameter in /home/www/_shareDInclude_/_2.0_/Debug/HuLOG.php on line 162
		**/
		$func_num_args = \func_num_args();
		$this->makeLogString($log_string, $file, $type, ($func_num_args > 3 ? $extra : new NullClass()));
		$this->writeLogs($to, $file);
	}

	/**
	* Make log string with timestamp and formatting
	*
	* @param string $log_string Log message text
	* @param string $file Log file identifier
	* @param string $type Type prefix
	* @param mixed $extra Extra data to log
	**/
	protected function makeLogString(string $log_string, string $file, string $type, mixed $extra): void {
		$this->lastLogTime = \time();
		$this->lastLogText->setSettingsArray(
			($extra instanceof NullClass) /* EXPLICIT check what $extra was provided! Null also possible value, what must be dumped, if it provided, It can't be ignored also as any other predefined value! **/
			?
			[
				'level'     => \sprintf('% ' . ($this->_level * 2) . 's', ' '),  //Indent
				'type'      => $type,            //Type-prefix
				'logText'   => $log_string,      //Main text!
			]
			:
			[
				// Now auto or disabled
				//-				'date'	=> date($this->_sets->DATE_TIME_FORMAT, $this->lastLogTime),//Дата-время
				'level'     => \sprintf('% ' . ($this->_level * 2) . 's', ' '),  //Indent
				'type'      => $type,            //Type-prefix
				'logText'   => $log_string,      //Main text!
				'extra'     => (($extra instanceof IOutExtraData) ? $extra : new OutExtraDataCommon($extra))  //Additional extra data
			]
		);
	}

	/**
	* Write logs to appropriate destinations
	*
	* @param int $to Destination flags
	* @param string $file File identifier
	**/
	protected function writeLogs(int $to, string $file): void {
		if (($to & HuLOGSettings::LOG_TO_FILE) !== 0) {
			$this->log_to_file($file);
		}
		if (($to & HuLOGSettings::LOG_TO_PRINT) !== 0) {
			$this->log_print();
		}
	}

	/**
	* Log to file
	*
	* @param string $file File identifier (default 'ERR')
	**/
	private function log_to_file(string $file = 'ERR'): void {
		/** @phpstan-ignore property.notFound */
		$logDir = $this->settings->LOG_FILE_DIR;
		/** @phpstan-ignore property.notFound */
		$filePrefix = $this->settings->FILE_PREFIX;
		\file_put_contents(
			$logDir . $filePrefix . $file,
			$this->formatter->formatForFile($this->lastLogText),
			\FILE_APPEND
		);
	}

	/**
	* Print log to output
	**/
	private function log_print(): void {
		echo $this->formatter->formatForPrint($this->lastLogText);
	}
}
