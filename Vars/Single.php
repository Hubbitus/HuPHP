<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

use function Hubbitus\HuPHP\Vars\CONF;

/**
* Singleton pattern.
* Sure I'm aware what Singleton may be anti-pattern. But usage it in the frameworks or small scripts is affordable in many scenarios.
* Example from http://ru2.php.net/manual/ru/language.oop5.patterns.php, modified
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created ?2008-05-30 13:22
**/
class Single {
    /** @var array<string, object> Hold an instance of the class */
    private static array $instance = [];

    /**
    * A private constructor; prevents direct creation of object
    **/
    final protected function __construct() {
        echo 'I am constructed. But can\'t be called :)';
    }

    /**
    * The main singleton static method
    * All call must be: Single::singleton('ClassName'). Or by its short alias: Single::def('ClassName')
    *
    * @param string $className Class name to provide Singleton instance for it.
    * @return object Singleton instance of the class
    **/
    public static function &singleton(string $className): object {
        $args = \func_get_args();
        \array_shift($args); // Remove class name

        $hash = $className . '_' . self::hash($args);
        if (!isset(self::$instance[$hash])) {
            /*
            Using Reflection to instantiate class with any args.
            See http://ru2.php.net/manual/ru/function.call-user-func-array.php, comment of richard_harrison at rjharrison dot org
            */
            $reflectionObj = new \ReflectionClass($className);

            // Use Reflection to create a new instance, using the $args
            if ([] === $args) {
                self::$instance[$hash] = $reflectionObj->newInstance();
            } else {
                try {
                    self::$instance[$hash] = $reflectionObj->newInstanceArgs($args);
                } catch (\ReflectionException $e) {
                    // Fallback for classes without constructor that accepts arguments
                    self::$instance[$hash] = $reflectionObj->newInstance();
                }
            }
        }

        return self::$instance[$hash];
    }

    /**
    * The default configured. Short alias for {@see ::singleton()}
    *
    * @param string $className Class name to provide Singleton instance for it.
    * @return object Singleton instance of the class
    **/
    public static function &def(string $className): object {
        $config = CONF()->getRaw($className, true);
        // If config is null, call singleton without arguments
        if (null === $config) {
            return self::singleton($className);
        }
        return self::singleton($className, $config);
    }

    /**
    * Prevent users to clone the instance
    * Note: This protects Single subclasses, but singleton() returns instances
    * of other classes (not Single), so this is not called in typical usage.
    **/
    public function __clone() {
        throw new \LogicException('Clone is not allowed.');
    }

    /**
    * Provide simple way of hashing objects and array
    *
    * @param mixed $param
    * @return string
    **/
    public static function hash(mixed $param): string {
        return \md5(\http_build_query($param));
    }
}
