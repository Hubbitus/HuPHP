<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings;

/**
* Entity of "filter". In most cases only calling $callback on provided pair references $name/$value in method apply().
* But it is quite powerful. Children of this basic class may provide any service such as: non-deterministic
*	state-based results, based on time or amount of call results (f.e. Apply filter only 3 times!?), depend
*	on any global environment etc...
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2009-06-29
**/
class SettingsFilterBase {
	public $propName;
	private $callback_;

	public function __construct(string $propName, $callback) {
		$this->propName = $propName;
		$this->callback_ = $callback;
	}

	/**
	* In simplest variant - just direct apply provided callback.
	* $name and $value provided as reference, so, user can change both as want.
	* It is useful to jungle and add additional filters by name. F.e. set GET filter like "UC:name"
	* to return uppercase value of "name", o rename option on set time etc.
	*
	* @param mixed $name Reference to name of option.
	* @param mixed $value Reference to new value of option
	* @return mixed Returns what user callback return.
	**/
	public function apply(&$name, &$value): mixed {
		/*
		* call_user_func_array to pass reference, what is not allowed in call_user_func.
		* Solution found in man, see Example1 http://ru2.php.net/call_user_func
		**/
		return \call_user_func_array($this->callback_, [&$name, &$value]);
	}
}
