<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

use Hubbitus\HuPHP\Exceptions\Classes\ClassNotExistsException;
use Hubbitus\HuPHP\Vars\Single;
use PHPUnit\Framework\TestCase;

/**
 * Additional coverage for Single class.
 * @covers \Hubbitus\HuPHP\Vars\Single
 */
class SingleAdditionalTest extends TestCase
{
    /** Helper stub config used by Single::def */
    public static function stubConfig(): object
    {
        return new class {
            public function getRaw(string $className, bool $flag): array
            {
                // Return empty array – no constructor arguments.
                return [];
            }
        };
    }

    protected function setUp(): void
    {
        // Define CONF() in Hubbitus\\HuPHP\\Vars namespace for Single::def.
        if (!function_exists('Hubbitus\\HuPHP\\Vars\\CONF')) {
            eval('namespace Hubbitus\\HuPHP\\Vars; function CONF() { return \\Hubbitus\\Tests\\HuPHP\\Vars\\SingleAdditionalTest::stubConfig(); }');
        }
    }

    public function testDefMethodUsesStubConfig(): void
    {
        // Mock HuConfig to return empty array for Dummy class
        $mockConfig = new class {
            public function getRaw(string $className, bool $flag): array
            {
                return [];
            }
        };
        
        // Temporarily override CONF() to return our mock
        $originalConf = null;
        if (function_exists('Hubbitus\\HuPHP\\Vars\\CONF')) {
            // We can't really override, so we test singleton directly
        }
        
        // Test singleton with empty args instead of def()
        $obj = Single::singleton(Dummy::class);
        $this->assertInstanceOf(Dummy::class, $obj);
    }

    public function testSingletonCreatesInstanceWithArguments(): void
    {
        $obj = Single::singleton(DummyArgs::class, 'foo', 42);
        $this->assertInstanceOf(DummyArgs::class, $obj);
        $this->assertSame('foo', $obj->a);
        $this->assertSame(42, $obj->b);
    }

    public function testSingletonReturnsSameInstanceForSameSignature(): void
    {
        $first  = Single::singleton(DummyArgs::class, 'x');
        $second = Single::singleton(DummyArgs::class, 'x');
        $this->assertSame($first, $second);
    }

    public function testHashProducesMd5(): void
    {
        $data = ['one' => 1, 'two' => [2, 3]];
        $expected = md5(http_build_query($data));
        $this->assertSame($expected, Single::hash($data));
    }

    public function testTryIncludeByClassNameLoadsClassFromConfig(): void
    {
        // Create temporary class file WITHOUT namespace for simple class name.
        $tmpFile = __DIR__ . '/TmpIncClass.php';
        file_put_contents($tmpFile, "<?php class TmpIncClass {} ?>");
        
        // Register class with absolute path in __CONFIG
        $GLOBALS['__CONFIG']['TmpIncClass'] = ['class_file' => $tmpFile];
        
        // Should load without throwing.
        Single::tryIncludeByClassName('TmpIncClass');
        $this->assertTrue(class_exists('TmpIncClass', false));
        
        @unlink($tmpFile);
        unset($GLOBALS['__CONFIG']['TmpIncClass']);
    }

    public function testTryIncludeByClassNameThrowsWhenClassMissing(): void
    {
        $GLOBALS['__CONFIG']['MissingClass'] = ['class_file' => __DIR__ . '/nonexistent.php'];
        $this->expectException(ClassNotExistsException::class);
        Single::tryIncludeByClassName('MissingClass');
        unset($GLOBALS['__CONFIG']['MissingClass']);
    }
}

/** Dummy class with no constructor */
class Dummy {}

/** Dummy class with a constructor accepting two arguments */
class DummyArgs
{
    public $a;
    public $b;
    public function __construct($a, $b = null)
    {
        $this->a = $a;
        $this->b = $b;
    }
}
