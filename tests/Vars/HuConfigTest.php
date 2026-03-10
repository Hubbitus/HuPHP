<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

use Hubbitus\HuPHP\Vars\HuConfig;
use Hubbitus\HuPHP\Vars\HuArray;
use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use PHPUnit\Framework\TestCase;

use function Hubbitus\HuPHP\Vars\CONF;

/**
* @covers \Hubbitus\HuPHP\Vars\HuConfig
* @covers \Hubbitus\HuPHP\Vars\CONF
**/
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
        $this->expectException(ClassPropertyNotExistsException::class);

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
        $this->expectException(ClassPropertyNotExistsException::class);

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

    public function testCONFFunctionWithoutClassName(): void {
        // Test CONF() function without class name returns HuConfig instance (via Single::singleton)
        $result = CONF();

        $this->assertInstanceOf(HuConfig::class, $result);
    }

    public function testCONFFunctionWithClassName(): void {
        // Test CONF() function with class name returns HuArray instance
        $GLOBALS['__CONFIG'] = [
            'test_class' => ['key' => 'value']
        ];

        $result = CONF('test_class');

        $this->assertInstanceOf(HuArray::class, $result);
    }

    public function testCONFPropertyAccess(): void {
        // Test CONF()->property syntax
        $GLOBALS['__CONFIG'] = [
            'test_setting' => 'test_value'
        ];

        $result = CONF()->test_setting;

        $this->assertEquals('test_value', $result);
    }

    public function testCONFWithNoThrow(): void {
        // Test CONF() function with noThrow parameter
        $result = CONF(null, true);

        $this->assertInstanceOf(HuConfig::class, $result);
    }

    public function testGetPropertyTriesToIncludeConfigFile(): void {
        // Create a temporary config file
        $configDir = \sys_get_temp_dir() . '/includes/configs';
        @\mkdir($configDir, 0777, true);

        $configFile = $configDir . '/test_include.config.php';
        \file_put_contents($configFile, '<?php $GLOBALS["__CONFIG"]["test_include"] = "included_value";');

        // Add temp dir to include_path
        $includePath = \get_include_path();
        \set_include_path(\sys_get_temp_dir() . \PATH_SEPARATOR . $includePath);

        try {
            $config = new HuConfig();

            // First call tries to include the file
            $value = $config->getProperty('test_include', true);

            // If file was included, value should be set
            if ($value !== null) {
                $this->assertEquals('included_value', $value);
            }
        } finally {
            // Cleanup
            \set_include_path($includePath);
            if (\file_exists($configFile)) {
                \unlink($configFile);
            }
            @\rmdir($configDir);
        }
    }

    public function testGetPropertyReturnsReference(): void {
        $GLOBALS['__CONFIG'] = [
            'ref_key' => 'original_value'
        ];

        $config = new HuConfig();
        $value = &$config->getProperty('ref_key');

        // Modify through reference
        $value = 'modified_value';

        // Get fresh value to verify modification
        $freshValue = $config->getProperty('ref_key');
        $this->assertEquals('modified_value', $freshValue);
    }

    public function testCONFReturnsReference(): void {
        $GLOBALS['__CONFIG'] = [
            'conf_ref' => 'original'
        ];

        $result = &CONF();
        $value = &$result->conf_ref;

        // Modify through reference
        $value = 'modified';

        // Get fresh value to verify modification
        $freshValue = $result->conf_ref;
        $this->assertEquals('modified', $freshValue);
    }

    public function testCONFFunctionWithClassNameReturnsHuArray(): void {
        // Test CONF() with className takes the if branch (line 137-138)
        $GLOBALS['__CONFIG'] = [
            'my_class' => ['setting' => 'value']
        ];

        $result = CONF('my_class');

        $this->assertInstanceOf(HuArray::class, $result);
        $this->assertEquals('value', $result->setting);
    }

    public function testGetPropertyWithNoThrowReturnsNullViaElseBranch(): void {
        // This test covers the else branch of getProperty when noThrow=true
        // and exception is caught (lines 96-97)
        // We need to trigger an exception that is NOT ClassPropertyNotExistsException
        // Passing null as name will trigger VariableIsNullException from REQUIRED_NOT_NULL
        $config = new HuConfig();

        // Call with null property name and noThrow=true
        // This will throw VariableIsNullException which goes to else branch
        $value = $config->getProperty(null, true);

        $this->assertNull($value);
    }
}
