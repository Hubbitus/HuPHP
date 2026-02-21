<?php
declare(strict_types=1);

/**
* Database abstraction layer.
* Documented AFTER creation, in progress.
* @package Database
* @subpackage DBError
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @uses HuError
**/
namespace Hubbitus\HuPHP\Database;

use Hubbitus\HuPHP\Debug\HuError;

class DatabaseError extends HuError{
	/**
	* Constructor.
	* @param DatabaseError|array|DatabaseErrorSettings	$sets	Initial settings.
	*	If DBError_settings assigned AS IS, if array MERGED with defaults and overwrite
	*	presented settings!
	**/
	public function __construct(DatabaseError|array|DatabaseErrorSettings $sets){
		if (\is_array($sets) and !empty($sets)){ // MERGE, NOT overwrite!
			$this->_sets = new DatabaseErrorSettings();
			$this->_sets->mergeSettingsArray($sets);
		}
		elseif($sets instanceof DatabaseErrorSettings) {
			$this->_sets = $sets;
		}
		elseif($sets instanceof DatabaseError) {
			$this->_sets = $sets->_sets;
		}
		else {
			$this->_sets = new DatabaseErrorSettings();// default
		}
	}
}
