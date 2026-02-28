<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

use Hubbitus\HuPHP\Vars\Settings\Settings;
use function Hubbitus\HuPHP\Macroses\EMPTY_VAR;
use function Hubbitus\HuPHP\Macroses\REQUIRED_NOT_NULL;
use Hubbitus\HuPHP\Exceptions\variables\VariableIsNullException;

/**
* Class to provide OOP interface to array operations.
*
* @package Vars
* @version 1.2.4
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-09-22 17:55 ver 1.1 to 1.1.1
*
* @uses REQUIRED_NOT_NULL()
* @uses VariableIsNullException
* @uses settings
**/
class HuArray extends Settings implements \Iterator, \ArrayAccess, \Countable, \JsonSerializable {
	private const string HU_SCHEME = 'hu://';

	/**
	* Constructor.
	*
	* @param ?array $array Initial value
	**/
	public function __construct(?array $array = null){
		parent::__construct($array ?? []);
	}

	/**
	* Push values.
	*
	* @param	mixed	$var.
	* @param	array	$params any amount of vars (First explicitly to make mandatory one at once)
	* @return	&$this
	**/
	public function &push($var): static {
		//On old PHP got error: PHP Fatal error:  func_get_args(): Can't be used as a function parameter in /home/_SHARED_/Vars/HuArray.php on line 58
		//call_user_func_array('array_push', array_merge(array(0 => &$this->__SETS), func_get_args()));
		//Do the same with temp var:
		$args = func_get_args();
		\call_user_func_array('array_push', \array_merge([0 => &$this->__SETS], $args));
		return $this;
	}

	/**
	* Push array of values.
	*
	* @param 	array	$arr
	* @return	&$this
	**/
	public function &pushArray(array $arr): static {
		if ($arr)
			foreach ($arr as $value)
				$this->__SETS[] = $value;
		return $this;
	}

	/**
	* Push values from Object(HuArray).
	*
	* @param 	mixed	$var.
	* @return	$this->pushArray()
	**/
	public function &pushHuArray(HuArray $arr): static {
		return $this->pushArray($arr->getArray());
	}

	/**
	* Return last element in array. Reference, direct-editable!!
	*
	* @return &mixed
	**/
	public function &last(): mixed {
		\end($this->__SETS);
		return $this->__SETS[\key($this->__SETS)];
	}

	/**
	* Return Array representation (cast to (array)).
	*
	* @return	array
	**/
	public function getArray(): array {
		return $this->__SETS;
	}

	/**
	* {@see http://php.net/array_slice}
	*
	* @param	integer	$offset
	*	If offset is positive, the sequence will start at that offset in the array. If offset is negative, the sequence will start that far from the end of the array.
	* @param	integer	$length
	*	If length is positive, the sequence will have up to length elements. If length is negative, the sequence will stop that many elements from the end of the array. If omitted, the sequence will include all elements from offset to the end.
	* @param	boolean	$preserve_keys
	*	Note that by default array keys are reset. You can override this behavior by setting preserve_keys to TRUE.
	* @return HuArray
	**/
	public function getSlice($offset, $length = null, $preserve_keys = false): static {
		return new HuArray(\array_slice($this->__SETS, $offset, EMPTY_VAR($length, \sizeof($this->__SETS)), $preserve_keys));
	}

	/**
	* Overload to return reference.
	*
	* @param	mixed	$name
	* @return	&mixed
	* @throws VariableIsNullException
	**/
	#[\Override]
	public function &getProperty($name): mixed {
		$key = REQUIRED_NOT_NULL($name);
		return $this->__SETS[$key];
	}

	/**
	* Overload to return reference.
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	#[\Override]
	public function &__get(string $name): mixed {
		/**
		* Needed name, because $var->last() = 'NewVal' produce error, even if value returned by reference:
		* PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		**/
		if ('_last_' == $name) {
			return $this->last();
		}
		/*
		* Short form of ::hu. To allow constructions like:
		* $obj->{'hu://varName'}->{'hu://0'};
		* instead of directly:
		* $obj->hu('varName')->hu(0);
		* As you like
		**/
		elseif( self::HU_SCHEME == substr($name, 0, strlen(self::HU_SCHEME)) ) {
			return $this->hu( substr($name, strlen(self::HU_SCHEME)) );
		}
		else {
			return $this->getProperty($name);
		}
	}

	/**
	* Like standard {@see __get()}, but if returned value is regular array, convert it into HuArray and return reference to it.
	* @example:
	* $ha = new HuArray(
	*	array(
	*		'one' => 1
	*		,'two' => 2
	*		,'arr' => array(0, 11, 22, 777)
	*	)
	* );
	* Dump::a($ha->one);
	* Dump::a($ha->arr);					// Result Array (raw, as is)!
	* Dump::a($ha->hu('arr'));				// Result HuArray (only if result had to be array, as is otherwise)!!! Original modified in place!
	* Dump::a($ha->hu('arr')->hu(2));			// Property access. Also as any HuArray methods like walk(), filter() and any other.
	* Dump::a($ha->{'hu://arr'}->{'hu://2'});	// Alternative method ({@see ::__get()}). Another, form.
	* Also this form is allow writing:
	* $ha->{'hu://arr'} = 'Qwerty';
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	public function &hu($name): mixed {
		if (\is_array($this->$name)) $this->$name = new HuArray($this->$name);
		return $this->getProperty($name);
	}

	/**
	* Allow change value by short direct form->settingName = 'qwerty';
	*
	* @param	string	$name
	* @param	mixed	$value
	**/
	#[\Override]
	public function __set($name, $value): void {
		/**
		* Needed name, because $var->last() = 'NewVal' produce error, even if value returned by reference:
		* PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		**/
		if ('_last_' == $name){
			// Direct assignment to last element
			\end($this->__SETS);
			$key = \key($this->__SETS);
			$this->__SETS[$key] = $value;
			return;
		}
		elseif( self::HU_SCHEME == substr($name, 0, strlen(self::HU_SCHEME)) ) {
			// Short form hu:// - convert to HuArray if needed and return
			$key = substr($name, strlen(self::HU_SCHEME));
			if (\is_array($this->__SETS[$key] ?? null)) {
				$this->__SETS[$key] = new HuArray($this->__SETS[$key]);
			}
			$this->__SETS[$key] = $value;
			return;
		}
		else{
			$key = $name;
		}
		$this->__SETS[$key] = $value;
	}

	/**
	* Apply callback function to each element.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function walk($callback): static {
		array_walk($this->__SETS, $callback);
		return $this;
	}

	/**
	* Filter array, using callback. If the callback function returns true, the current value from input is returned into the result
	* array. Array keys are preserved and NOT reindexed.
	*
	* @param	callback	$callback
	* @return	static
	**/
	public function filter($callback): static {
		return new static(array_filter($this->__SETS, $callback));
	}

	/**
	* Filter array by keys and leave only mentioned in $keys array
	*
	* @param	array	$keys
	* @return	&$this
	**/
	public function &filterByKeys(array $keys): static {
		$this->__SETS = \array_intersect_key($this->__SETS, \array_flip($keys));
		return $this;
	}

	/**
	* Filter array by keys and leave only NOT mentioned in $keys array (opposite to method {@see ::filterByKeys()})
	*
	* Implementation idea taken from: http://ru.php.net/array_filter comment of niehztog
	*
	* @param	array	$keys
	* @return	&$this
	**/
	public function &filterOutByKeys(array $keys): static{
		$this->__SETS = \array_diff_key( $this->__SETS, \array_flip($keys) );
		return $this;
	}

	/**
	* Similar to {@see ::filer()} except of operate by keys instead of values.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function &filterKeysCallback($callback): static {
		$this->__SETS = \array_filter($this->__SETS, $callback, \ARRAY_FILTER_USE_KEY);
		return $this;
	}

	/**
	* Implode to the string using provided delimiter.
	*
	* @param	string=''	$delim
	* @return	string
	**/
	public function implode($delim = ''): string {
		return implode($delim, $this->__SETS);
	}

	/**
	* Return number of elements
	*
	* @return	int
	**/
	public function count(): int {
		return \count($this->__SETS);
	}

	/**
	* Iteratively reduce the array to a single value using a callback function.
	* @link http://ru.php.net/array_reduce
	*
	* @param	callback	$callback
	* @param	integer	$initial
	* @return	mixed
	**/
	public function reduce($callback, $initial = 0){
		return array_reduce($this->__SETS, $callback, $initial);
	}

	/** Implementation of {@see \Iterator} methods **/

	#[\Override]
	public function rewind(): void {
		\reset($this->__SETS);
	}

	#[\Override]
	public function current(): mixed {
		return \current($this->__SETS);
	}

	#[\Override]
	public function key(): int|string|null {
		return \key($this->__SETS);
	}

	#[\Override]
	public function next(): void {
		\next($this->__SETS);
	}

	#[\Override]
	public function valid(): bool {
		return \current($this->__SETS) !== false;
	}
	/** /Implementation of {@see \Iterator} methods **/

	public function toArray(): array {
		return $this->__SETS;
	}

	public function toJson(int $options = 0): string {
		return \json_encode($this->__SETS, $options);
	}

	public static function fromJson(string $json): static {
		return new static(\json_decode($json, true));
	}

	public function isEmpty(): bool {
		return empty($this->__SETS);
	}

	public function getByKey($key): mixed {
		return $this->__SETS[$key] ?? null;
	}

	public function &setByKey($key, $value): static {
		$this->__SETS[$key] = $value;
		return $this;
	}

	public function getByIndex(int $index): mixed {
		$keys = \array_keys($this->__SETS);
		return $this->__SETS[$keys[$index]] ?? null;
	}

	public function &setByIndex(int $index, $value): static {
		$keys = \array_keys($this->__SETS);
		if (isset($keys[$index])) {
			$this->__SETS[$keys[$index]] = $value;
		}
		return $this;
	}

	public function has($key): bool {
		return isset($this->__SETS[$key]);
	}

	public function first(): mixed {
		if (empty($this->__SETS)) {
			return null;
		}
		return \reset($this->__SETS);
	}

	public function &append($value): static {
		$this->__SETS[] = $value;
		return $this;
	}

	public function &prepend($value): static {
		\array_unshift($this->__SETS, $value);
		return $this;
	}

	public function pop(): mixed {
		return \array_pop($this->__SETS);
	}

	public function shift(): mixed {
		return \array_shift($this->__SETS);
	}

	public function &unshift($value): static {
		\array_unshift($this->__SETS, $value);
		return $this;
	}

	public function keys(): static {
		return new static(\array_keys($this->__SETS));
	}

	public function values(): static {
		return new static(\array_values($this->__SETS));
	}

	public function flip(): static {
		return new static(\array_flip($this->__SETS));
	}

	public function reverse(bool $preserve_keys = false): static {
		return new static(\array_reverse($this->__SETS, $preserve_keys));
	}

	public function chunk(int $size): static {
		return new static(\array_chunk($this->__SETS, $size));
	}

	public function slice(int $offset, ?int $length = null, bool $preserve_keys = false): static {
		return new static(\array_slice($this->__SETS, $offset, $length, $preserve_keys));
	}

	public function splice(int $offset, ?int $length = null, array $replacement = []): static {
		$spliced = \array_splice($this->__SETS, $offset, $length ?? \count($this->__SETS), $replacement);
		return new static($spliced);
	}

	public function &merge($arr): static {
		if ($arr instanceof static) {
			$arr = $arr->toArray();
		}
		$this->__SETS = \array_merge($this->__SETS, $arr);
		return $this;
	}

	public function diff($arr): static {
		if ($arr instanceof static) {
			$arr = $arr->toArray();
		}
		return new static(\array_diff($this->__SETS, $arr));
	}

	public function udiff($arr): static {
		if ($arr instanceof static) {
			$arr = $arr->toArray();
		}
		return new static(\array_values(\array_udiff($this->__SETS, $arr, fn($a, $b) => $a <=> $b)));
	}

	public function intersect($arr): static {
		if ($arr instanceof static) {
			$arr = $arr->toArray();
		}
		return new static(\array_values(\array_intersect($this->__SETS, $arr)));
	}

	public function unique(): static {
		return new static(\array_values(\array_unique($this->__SETS)));
	}

	public function map(callable $callback): static {
		return new static(\array_map($callback, $this->__SETS));
	}

	public function every(callable $callback): bool {
		foreach ($this->__SETS as $key => $value) {
			if (!$callback($value, $key)) {
				return false;
			}
		}
		return true;
	}

	public function some(callable $callback): bool {
		foreach ($this->__SETS as $key => $value) {
			if ($callback($value, $key)) {
				return true;
			}
		}
		return false;
	}

	public function find(callable $callback): mixed {
		foreach ($this->__SETS as $key => $value) {
			if ($callback($value, $key)) {
				return $value;
			}
		}
		return null;
	}

	public function search($search): int|false {
		return \array_search($search, $this->__SETS, true);
	}

	public function check($key): bool {
		return isset($this->__SETS[$key]);
	}

	public function &tap(callable $callback): static {
		$callback($this);
		return $this;
	}

	public function pipe(callable $callback): mixed {
		return $callback($this);
	}

	public function when($condition, callable $callback): static {
		if ($condition) {
			$result = $callback($this);
			return $result instanceof static ? $result : $this;
		}
		return $this;
	}

	public function unless($condition, callable $callback): static {
		if (!$condition) {
			$result = $callback($this);
			return $result instanceof static ? $result : $this;
		}
		return $this;
	}

	public function pluck($key): static {
		$result = [];
		foreach ($this->__SETS as $item) {
			if (\is_array($item) && isset($item[$key])) {
				$result[] = $item[$key];
			} elseif (\is_object($item) && isset($item->$key)) {
				$result[] = $item->$key;
			}
		}
		return new static($result);
	}

	public function flatten(int $depth = PHP_INT_MAX): static {
		$result = [];
		\array_walk_recursive($this->__SETS, function($a) use (&$result) {
			$result[] = $a;
		});
		return new static($result);
	}

	public function fill(int $start_index, int $count, $value): static {
		return new static(\array_fill($start_index, $count, $value));
	}

	public function pad(int $size, $value): static {
		return new static(\array_pad($this->__SETS, $size, $value));
	}

	public function column($column_key, $index_key = null): static {
		return new static(\array_column($this->__SETS, $column_key, $index_key));
	}

	public function combine($values): static {
		if ($values instanceof static) {
			$values = $values->toArray();
		}
		return new static(\array_combine($this->__SETS, $values));
	}

	public function assoc(): static {
		$result = [];
		$keys = \array_keys($this->__SETS);
		for ($i = 0; $i < \count($keys); $i += 2) {
			if (isset($keys[$i + 1])) {
				$result[$this->__SETS[$keys[$i]]] = $this->__SETS[$keys[$i + 1]];
			}
		}
		return new static($result);
	}

	public function &unsetByKey($key): static {
		unset($this->__SETS[$key]);
		return $this;
	}

	public function &unsetByIndex(int $index): static {
		$keys = \array_keys($this->__SETS);
		if (isset($keys[$index])) {
			unset($this->__SETS[$keys[$index]]);
		}
		return $this;
	}

	public function sort(?callable $callback = null): static {
		if ($callback) {
			\usort($this->__SETS, $callback);
		} else {
			\sort($this->__SETS);
		}
		return $this;
	}

	public function zip($arr): static {
		if ($arr instanceof static) {
			$arr = $arr->toArray();
		}
		return new static(\array_map(null, $this->__SETS, $arr));
	}

	public static function range($start, $end, $step = 1): static {
		return new static(\range($start, $end, $step));
	}

	public static function explode(string $delimiter, string $string): static {
		return new static(\explode($delimiter, $string));
	}

	/** Implementation of {@see \ArrayAccess} methods **/
	#[\Override]
	public function offsetExists($offset): bool {
		return isset($this->__SETS[$offset]);
	}

	#[\Override]
	public function offsetGet($offset): mixed {
		return $this->__SETS[$offset] ?? null;
	}

	#[\Override]
	public function offsetSet($offset, $value): void {
		if ($offset === null) {
			$this->__SETS[] = $value;
		} else {
			$this->__SETS[$offset] = $value;
		}
	}

	public function offsetUnset($offset): void {
		unset($this->__SETS[$offset]);
		}
	/** /Implementation of {@see \ArrayAccess} methods **/

	// String conversion
	public function __toString(): string {
		return \json_encode($this->__SETS);
	}

	#[\Override]
	// JsonSerializable interface method
	public function jsonSerialize(): array {
		return $this->__SETS;
	}
}
