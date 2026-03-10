<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

use Hubbitus\HuPHP\Exceptions\SerializeException;

class SerializedData {
	private array $__data = [];

	public function __construct(?string $serializedStr = null) {
		if ($serializedStr !== null) {
			$unSerialized = @\unserialize($serializedStr);
			if ($unSerialized === false && $serializedStr !== 'b:0;') {
				throw new SerializeException('Error happened in deserialization');
			}
			$this->__data = $unSerialized;
		}
	}

	public function __get($name): mixed {
		return $this->__data[$name] ?? null;
	}

	public function __set($name, $val): void {
		$this->__data[$name] = $val;
	}

	public function __isset($name): bool {
		return isset($this->__data[$name]);
	}

	public function __unset($name): void {
		unset($this->__data[$name]);
	}

	public function __toString(): string {
		return \serialize($this->__data);
	}

	public function toString(): string {
		return $this->__toString();
	}
}
