<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

use Hubbitus\HuPHP\Vars\Single;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Vars\Single::def
* @requires CONF function
**/
class SingleDefTest extends TestCase {
    protected function setUp(): void {
        // Clear singleton instances between tests
        $reflection = new \ReflectionClass(Single::class);
        $property = $reflection->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null, []);

        // Setup test configuration for TestConfigClass used in testDefWithConfigArgs
        // This allows testing the config argument passing mechanism
        if (!isset($GLOBALS['__CONFIG'])) {
            $GLOBALS['__CONFIG'] = [];
        }
        // Use fully qualified class name as key
        $GLOBALS['__CONFIG']['Hubbitus\\Tests\\HuPHP\\Vars\\TestConfigClass'] = [
            'config_arg1',
            'config_arg2'
        ];
    }

    public function testDefReturnsSingletonInstance(): void {
        $obj = Single::def(\stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $obj);
    }

    public function testDefReturnsSameInstance(): void {
        $obj1 = Single::def(\stdClass::class);
        $obj2 = Single::def(\stdClass::class);
        $this->assertSame($obj1, $obj2);
    }

    public function testDefWithEmptyConfig(): void {
        $obj = Single::def(\ArrayObject::class);
        $this->assertInstanceOf(\ArrayObject::class, $obj);
    }

    public function testDefIsStaticMethod(): void {
        $reflection = new \ReflectionMethod(Single::class, 'def');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function testDefWithConfigArgs(): void {
        // When def() is called, CONF()->getRaw() returns an array
        // This array is passed as a SINGLE argument to singleton()
        // So TestConfigClass receives the array as first constructor argument
        $obj = Single::def(TestConfigClass::class);
        $this->assertInstanceOf(TestConfigClass::class, $obj);
        // The config array is passed as single argument
        $this->assertIsArray($obj->arg1);
        $this->assertSame(['config_arg1', 'config_arg2'], $obj->arg1);
    }
}

class TestConfigClass {
    public $arg1;
    public $arg2;
    public function __construct($arg1 = null, $arg2 = null) {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }
}