<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

/**
* To explicit indicate what value not provided, also Null NOT provided too!
*
* This class does NOT allow dynamic properties (PHP 8.2+ default behavior).
* Magic methods __set/__get/__isset/__unset throw LogicException for any property access.
**/
class NullClass {
    /**
    * Prevent setting dynamic properties.
    *
    * @param string $name Property name
    * @param mixed $value Property value
    * @throws \LogicException Always thrown - dynamic properties not allowed
    **/
    public function __set(string $name, mixed $value): never {
        throw new \LogicException(\sprintf(
            'Cannot set dynamic property "%s" on %s',
            $name,
            self::class
        ));
    }

    /**
    * Prevent getting dynamic properties.
    *
    * @param string $name Property name
    * @throws \LogicException Always thrown - dynamic properties not allowed
    **/
    public function __get(string $name): never {
        throw new \LogicException(\sprintf(
            'Cannot get dynamic property "%s" on %s',
            $name,
            self::class
        ));
    }

    /**
    * Prevent checking dynamic properties with isset().
    *
    * @param string $name Property name
    * @return false Always returns false
    **/
    public function __isset(string $name): false {
        return false;
    }

    /**
    * Prevent unsetting dynamic properties.
    *
    * @param string $name Property name
    * @throws \LogicException Always thrown - dynamic properties not allowed
    **/
    public function __unset(string $name): never {
        throw new \LogicException(\sprintf(
            'Cannot unset dynamic property "%s" on %s',
            $name,
            self::class
        ));
    }
}
