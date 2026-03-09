<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;
use Hubbitus\HuPHP\System\OutputType;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Vars\OutExtraDataCommon;
use Hubbitus\HuPHP\Vars\Settings\Settings;
use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\Vars\IOutExtraData;

/**
* Debug and backtrace toolkit.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2008-05-31 03:19
*
* @property HuErrorSettings $settings Settings object
* @property string $date Current date for logging
* @property string $DATE Current date for logging (alias)
**/
class HuError extends Settings implements IOutExtraData {
	protected ?HuErrorSettings $_sets = null;

	/** @var OutputType Current output type */
	public OutputType $_curTypeOut = OutputType::CONSOLE;

	public function __construct(?HuErrorSettings $sets = null){
		parent::__construct();
		$this->_sets = Vars::firstMeaning($sets, new HuErrorSettings());
	}

	/**
	* Due to absent multiple inheritance in PHP, just copy/paste from class {@see SettingsGet} @TODO switch to use traits.
	* Overloading to provide ref on settings without change possibility.
	* In this case change settings is allowed, but change full settings object - not!
	*
	* @param string $name
	* @return mixed Object of settings.
	**/
	public function &__get ($name): mixed {
		if ('settings' === $name) {
			return $this->_sets; // @TODO refactor to be it more static
		}

		if ('date' === $name || 'DATE' === $name) {
			if ($this->getProperty('date') === null) $this->updateDate();
			$t = $this->getProperty('date');
			return $t;
		}

		/**
		* Set properties is implicit and NOT returned reference by default.
		* But for 'settings' we want opposite reference. Without compatibility of functions
		* overload by type arguments - is only way silently ignore Notice: Only variable references should be returned by reference
		**/
		$t = $this->getProperty($name);
		return $t;
	}

	/**
	* String to print into file.
	*
	* @param array<mixed>|string|null $format If @format not-empty use it for formatting result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, OutputType::FILE setting used.
	*	And if it settings empty (or not exists) too, just using dump::log() for all filled fields.
	* @return string
	**/
	public function strForFile(array|string|null $format = null): string {
		$this->_curTypeOut = OutputType::FILE;
		$format = Vars::firstMeaning($format, @$this->settings->FILE);
		if ($format !== null && $format !== []){
			return $this->getString($format);
		}
		else {
			return Dump::log($this->__SETS, null, true);
		}
	}

	/**
	* String to print into user browser.
	*
	* @param array<mixed>|string|null $format If @format not-empty use it for formatting result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, OutputType::WEB setting used.
	*	And if it settings empty (or not exists) too, just using dump::w() for all filled fields.
	* @return string
	**/
	public function strForWeb(array|string|null $format = null): string {
		$this->_curTypeOut = OutputType::WEB;
		$format = Vars::firstMeaning($format, @$this->settings->WEB);
		return ($format !== null && $format !== [])
			? $this->getString($format)
			: Dump::w($this->__SETS, null, true);
	}

	/**
	* String to print on console.
	*
	* @param array<mixed>|string|null $format If @format not-empty use it for formatting result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, OutputType::CONSOLE setting used.
	*	And if it settings empty (or not exists) too, just using dump::c() for all filled fields.
	* @return string
	**/
	public function strForConsole(array|string|null $format = null): string {
		$this->_curTypeOut = OutputType::CONSOLE;
		$format = Vars::firstMeaning($format, @$this->settings->CONSOLE);
		return ($format !== null && $format !== [])
			? $this->getString($format)
			: Dump::c($this->__SETS, null, true);
	}

	/**
	* String to print. automatically detect Web or Console. Detect by {@link OS::getOutType()}
	*	and invoke appropriate ::strToWeb() or ::strToConsole()
	*
	* @param array<mixed>|string|null $format	If @format not-empty use it for formatting result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strForPrint(array|string|null $format = null): string {
		return OutExtraDataCommon::strForPrintBase($this, $format);
	}

	/**
	* Convert to string by type.
	*
	* @param OutputType $type Output type enum
	* @param array<mixed>|string|null $format	If @format not-empty use it for formatting result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	* @throws VariableRangeException
	**/
	public function strByOutType(OutputType $type, array|string|null $format = null): string {
		return OutExtraDataCommon::strByOutTypeBase($this, $type, $format);
	}

	/**
	* Detect appropriate print (to Web or Console) and return correct form
	*
	* @return string
	**/
	public function __toString(): string {
		return $this->strForPrint();
	}

	/**
	* Overload settings::setSetting() to handle auto-date
	*
	* @inheritdoc
	**/
	public function &setSetting($name, $value): static {
		parent::setSetting($name, $value);

		$this->updateDate();

		return $this;
	}

	/**
	* Overload settings::setSettingsArray() to handle auto date
	*
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr): void {
		parent::setSettingsArray($setArr);

		//Insert after update data
		$this->updateDate();
	}

	/**
	* Just alias for ::setSettingsArray()
	*
	* @param	array	$setArr
	**/
	public function setFromArray(array $setArr): void {
		$this->setSettingsArray($setArr);
	}

	/**
	* Overload settings::mergeSettingsArray() to handle auto date
	*
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr): void {
		//Insert BEFORE update data in merge. User data 'date' must overwrite auto, if present!
		$this->updateDate();

		parent::mergeSettingsArray($setArr);
	}

	/**
	* Just alias for ::mergeSettingsArray()
	*
	* @param array $setArr
	**/
	public function mergeFromArray(array $setArr): void {
		$this->mergeSettingsArray($setArr);
	}

	/**
	* If settings->AUTO_DATE == true and settings->DATE_FORMAT correctly provided - update current
	* date in ->date
	**/
	public function updateDate(): void {
		/** @phpstan-ignore booleanAnd.rightNotBoolean */
		if ($this->settings->AUTO_DATE && $this->settings->DATE_FORMAT) {
			parent::setSetting('date', \date($this->settings->DATE_FORMAT));
		}
	}

	/**
	* Overloading getString to separately handle 'extra'
	*
	* @inheritdoc
	**/
	public function formatField($field): string {
		if (\is_array($field)){
			 if(!isset($field[0])) {
				 $field = \array_values($field);
			 }
			/** @phpstan-ignore property.dynamicName */
			$fieldValue = @$this->{$field[0]};
		}
		else{
			$field = (array)$field;
			/** @phpstan-ignore property.dynamicName */
			$fieldValue = Vars::firstMeaning(@$this->{$field[0]}, $field[0]); //Setting by name, or it is just text
		}

		if ($fieldValue instanceof IOutExtraData){
			return Vars::surround($fieldValue->strByOutType($this->_curTypeOut), @$field[1], @$field[2], @$field[3] ?? '');
		}
		elseif($fieldValue instanceof Backtrace){
			return Vars::surround($fieldValue->printFormat(null, $this->_curTypeOut), @$field[1], @$field[2], @$field[3] ?? '');
		}
		else {
			return Vars::surround($fieldValue, @$field[1], @$field[2], @$field[3] ?? '');
		}
	}

	/** @var array Extra data storage */
	protected array $_extraData = [];

	/**
	* Add extra data to the error object.
	*
	* @param string $key Key for the extra data
	* @param mixed $value Value to store
	* @return void
	**/
	public function addExtra(string $key, mixed $value): void {
		$this->_extraData[$key] = $value;
	}

	/**
	* Get extra data by key.
	*
	* @param string $key Key to retrieve
	* @return mixed|null Extra data value or null if not found
	**/
	public function getExtra(string $key): mixed {
		return $this->_extraData[$key] ?? null;
	}

	/**
	* Clear all extra data.
	*
	* @return void
	**/
	public function clearExtra(): void {
		$this->_extraData = [];
	}

	/**
	* Get all extra data.
	*
	* @return array All extra data
	**/
	public function getAllExtra(): array {
		return $this->_extraData;
	}
}
