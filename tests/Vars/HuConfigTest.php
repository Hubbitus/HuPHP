<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

use Hubbitus\HuPHP\Vars\HuConfig;
use Hubbitus\HuPHP\Vars\HuArray;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\HuConfig
 */
class HuConfigTest extends TestCase {
    protected function setUp(): void {
        // Initialize global config for tests
        if (!isset($GLOBALS['__CONFIG'])) {
            $GLOBALS['__CONFIG'] = [];
        }
    }

    protected function tearDown(): void {
        // Clean up global config after tests
        $GLOBALS['__CONFIG'] = [];
    }

    public function testConstructorInitializesWithEmptyConfig(): void {
        $config = new HuConfig();

        $this->assertInstanceOf(HuConfig::class, $config);
    }

    public function testConstructorInitializesWithConfigData(): void {
        $GLOBALS['__CONFIG'] = [
            'test_key' => 'test_value',
            'another_key' => 123
        ];

        $config = new HuConfig();

        $this->assertInstanceOf(HuConfig::class, $config);
    }

    public function testGetRawReturnsPropertyValue(): void {
        $GLOBALS['__CONFIG'] = [
            'test_key' => 'test_value'
        ];

        $config = new HuConfig();
        $value = $config->getRaw('test_key');

        $this->assertEquals('test_value', $value);
    }

    public function testGetRawThrowsExceptionForNonExistentProperty(): void {
        $this->expectException(\Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException::class);

        $GLOBALS['__CONFIG'] = ['existing_key' => 'value'];

        $config = new HuConfig();
        // Call twice - first time tries to include file, second time throws exception
        $config->getRaw('non_existent_key');
        $config->getRaw('non_existent_key');
    }

    public function testGetRawWithNoThrowReturnsNullForNonExistentProperty(): void {
        $config = new HuConfig();
        $value = $config->getRaw('non_existent_key', true);

        $this->assertNull($value);
    }

    public function testMagicGetReturnsPropertyValue(): void {
        $GLOBALS['__CONFIG'] = [
            'test_key' => 'test_value'
        ];

        $config = new HuConfig();
        $value = $config->__get('test_key');

        $this->assertEquals('test_value', $value);
    }

    public function testMagicGetReturnsHuArrayForArrayValues(): void {
        $GLOBALS['__CONFIG'] = [
            'test_array' => ['key1' => 'value1', 'key2' => 'value2']
        ];

        $config = new HuConfig();
        $value = $config->__get('test_array');

        $this->assertInstanceOf(HuArray::class, $value);
    }

    public function testMagicGetAllowsPropertyAccessSyntax(): void {
        $GLOBALS['__CONFIG'] = [
            'test_key' => 'test_value'
        ];

        $config = new HuConfig();
        $value = $config->test_key;

        $this->assertEquals('test_value', $value);
    }

    public function testGetPropertyReturnsValue(): void {
        $GLOBALS['__CONFIG'] = [
            'test_key' => 'test_value'
        ];

        $config = new HuConfig();
        $value = $config->getProperty('test_key');

        $this->assertEquals('test_value', $value);
    }

    public function testGetPropertyThrowsExceptionForNonExistentProperty(): void {
        $this->expectException(\Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException::class);

        $GLOBALS['__CONFIG'] = ['existing_key' => 'value'];

        $config = new HuConfig();
        // Call twice - first time tries to include file, second time throws exception
        $config->getProperty('non_existent_key');
        $config->getProperty('non_existent_key');
    }

    public function testGetPropertyWithNoThrowReturnsNullForNonExistentProperty(): void {
        $config = new HuConfig();
        $value = $config->getProperty('non_existent_key', true);

        $this->assertNull($value);
    }
}
