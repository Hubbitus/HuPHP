<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Database;

/**
 * Database abstraction layer.
 * Documented AFTER creation, in progress.
 *
 * @package Database
 * @subpackage DBError
 * @version 2.0b
 * @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
 * @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
 *
 * @uses HuError
 **/

use Hubbitus\HuPHP\Debug\HuError;

class DatabaseError extends HuError {
	/**
	 * Constructor.
	 *
	 * @param DatabaseError|array|DatabaseErrorSettings $sets Initial settings.
	 * If DBError_settings assigned AS IS, if array MERGED with defaults and overwrite
	 * presented settings!
	 **/
	public function __construct(DatabaseError|array|DatabaseErrorSettings $sets) {
		// Initialize parent with null to set up base structure
		parent::__construct(null);

		if (\is_array($sets) and !empty($sets)) { // MERGE, NOT overwrite!
			$this->_sets = new DatabaseErrorSettings();
			$this->_sets->mergeSettingsArray($sets);
			// Sync with __SETS for property access via __get
			$this->__SETS = array_merge($this->__SETS, $this->_sets->__SETS);
		} elseif ($sets instanceof DatabaseErrorSettings) {
			$this->_sets = $sets;
			// Sync with __SETS for property access via __get
			$this->__SETS = array_merge($this->__SETS, $sets->__SETS);
		} elseif ($sets instanceof DatabaseError) {
			$this->_sets = $sets->_sets;
			// Sync with __SETS for property access via __get
			$this->__SETS = array_merge($this->__SETS, $sets->_sets->__SETS);
		} else {
			$this->_sets = new DatabaseErrorSettings(); // default
			// Sync with __SETS for property access via __get
			$this->__SETS = array_merge($this->__SETS, $this->_sets->__SETS);
		}
	}
}
