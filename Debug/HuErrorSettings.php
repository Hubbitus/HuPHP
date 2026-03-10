<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\System\OutputType;
use Hubbitus\HuPHP\Vars\Settings\Settings;

/**
* HuError settings class.
*
* @property array<mixed> $WEB (OutputType) Format for web output
* @property array<mixed> $CONSOLE (OutputType::) Format for console output
* @property array<mixed> $FILE OutputType Format for file output
* @property bool $AUTO_DATE Auto-update date flag
* @property string $DATE_FORMAT Date format string
**/
class HuErrorSettings extends Settings {
	// Defaults
	public function __construct(array $array = []){
		parent::__construct($array);
		$this->initDefaults();
	}

	/**
	* Initialize default settings.
	*
	* @return void
	*/
	protected function initDefaults(): void {
		$this->__SETS = [];
		$this->__SETS[OutputType::WEB->name] = [];
		$this->__SETS[OutputType::CONSOLE->name] = [];
		$this->__SETS[OutputType::FILE->name] = [];
		$this->__SETS['AUTO_DATE'] = true;
		$this->__SETS['DATE_FORMAT'] = 'Y-m-d H:i:s';
	}

	/**
	* Get default web format.
	*
	* @return array<mixed>
	*/
	public function getDefaultWebFormat(): array {
		return $this->__SETS[OutputType::WEB->name];
	}

	/**
	* Get default console format.
	*
	* @return array<mixed>
	*/
	public function getDefaultConsoleFormat(): array {
		return $this->__SETS[OutputType::CONSOLE->name];
	}

	/**
	* Get default file format.
	*
	* @return array<mixed>
	*/
	public function getDefaultFileFormat(): array {
		return $this->__SETS[OutputType::FILE->name];
	}

	/**
	* Check if auto date is enabled.
	*
	* @return bool
	*/
	public function isAutoDateEnabled(): bool {
		return (bool)$this->__SETS['AUTO_DATE'];
	}

	/**
	* Get date format string.
	*
	* @return string
	*/
	public function getDateFormat(): string {
		return $this->__SETS['DATE_FORMAT'];
	}
}
