<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

/**
* Log text formatter class.
*
* Log text formatter class extending HuError.
* Used to format and output log messages with customizable format settings.
**/
class HuLOGText extends HuError {
	/**
	* Constructor.
	*
	* @param HuLOGTextSettings|array|null	$sets	Initial settings.
	*	If HuLOG_text_settings assigned AS IS, if array MERGED with defaults and overwrite
	*	presented settings!
	**/
	public function __construct( /* HuLOG_text_settings | array */ $sets = null){
		parent::__construct();
		if (\is_array($sets) && $sets !== []){ //MERGE, NOT overwrite!
			$this->_sets = new HuLOGTextSettings();
			$this->_sets->mergeSettingsArray($sets);
		}
		elseif($sets !== null) $this->_sets = $sets;
		else $this->_sets = new HuLOGTextSettings();//default
	}
}
