<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

/**
* Log text formatter class.
*
* Log text formatter class extending HuError.
* Used to format and output log messages with customizable format settings.
*
* @package Debug
* @subpackage HuLOG
**/
class HuLOGText extends HuError {
	/**
	* Constructor.
	*
	* @param HuLOGTextSettings|null $sets Initial settings. If null, default settings are used.
	**/
	public function __construct(?HuLOGTextSettings $sets = null){
		parent::__construct($sets ?? new HuLOGTextSettings());
	}
}
