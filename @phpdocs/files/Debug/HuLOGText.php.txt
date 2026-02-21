<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

class HuLOGText extends HuError{
	/**
	* Constructor.
	*
	* @param HuLOGTextSettings|array	$sets	Initial settings.
	*	If HuLOG_text_settings assigned AS IS, if array MERGED with defaults and overwrite
	*	presented settings!
	**/
	public function __construct( /* HuLOG_text_settings | array */ $sets){
		if (is_array($sets) and !empty($sets)){ //MERGE, NOT overwrite!
			$this->_sets = new HuLOGTextSettings();
			$this->_sets->mergeSettingsArray($sets);
		}
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new HuLOGTextSettings();//default
	}
}
