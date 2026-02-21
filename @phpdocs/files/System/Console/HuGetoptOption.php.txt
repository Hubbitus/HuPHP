<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System\Console;

use Hubbitus\HuPHP\Vars\HuArray;
use Hubbitus\HuPHP\Vars\Settings\SettingsCheck;

/**
* Like this (examples, structure):
*	||| First "Availability"
*	|-> OptL	=> 'long'
*	|-> OptS	=> 's'
*	|-> Mod		=> ':'
*	||| Then, 'real'. This fields filled only after parse user input. Each array, because more then once may be presents in CL.
*	|-> Opt		=> HyArray('s')		// Opt present, parsed from user-input.
*	|-> Sep		=> HuArray('-')		// '--'
*	|-> Val		=> HuArray('Text')	//Or just true if interesting only present it or not.
*	||| Misc
*	|-> =		=> HuArray(false)
*	|-> 'OptT'	=> HuArray('s', 'l')//OptType
**/
class HuGetoptOption extends SettingsCheck {

	/**
	* @inheritdoc
	**/
	public function __construct(array $possibles, array $array = null){
		parent::__construct($possibles, $array);
		foreach (['Opt', 'Sep', 'Val', '=', 'OptT'] as $k){
			if ( !isset($this->{$k}) ) $this->setSetting($k, new HuArray());
		}
	}

	/**
	* Add parsed option in values HuArrays (Opt, Sep, Val, =, OptT)
	*
	* @param HuGetoptOption $toAdd
	* @return	&$this
	**/
	public function add(HuGetoptOption $toAdd){
		foreach (['Opt', 'Sep', 'Val', '=', 'OptT'] as $k){
			$this->{$k}->pushHuArray($toAdd->{$k});
		}
		return $this;
	}
}
