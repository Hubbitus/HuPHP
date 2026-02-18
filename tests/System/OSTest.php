<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\System;

use Hubbitus\HuPHP\System\OS;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\System\OS
 */
class OSTest extends TestCase
{
    public function testGetOutTypeReturnsBrowserWhenUserAgentSet(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
        
        $result = OS::getOutType();
        
        $this->assertEquals(OS::OUT_TYPE_BROWSER, $result);
        
        unset($_SERVER['HTTP_USER_AGENT']);
    }

    public function testGetOutTypeReturnsConsoleWhenUserAgentNotSet(): void
    {
        unset($_SERVER['HTTP_USER_AGENT']);
        
        $result = OS::getOutType();
        
        $this->assertEquals(OS::OUT_TYPE_CONSOLE, $result);
    }

    public function testGetOutTypeConstants(): void
    {
        $this->assertEquals(1, OS::OUT_TYPE_BROWSER);
        $this->assertEquals(2, OS::OUT_TYPE_CONSOLE);
        $this->assertEquals(4, OS::OUT_TYPE_PRINT);
        $this->assertEquals(8, OS::OUT_TYPE_FILE);
        $this->assertEquals(16, OS::OUT_TYPE_WAP);
    }

    public function testPhpSapiNameReturnsString(): void
    {
        $result = OS::phpSapiName();
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testPhpSapiNameReturnsValidSapi(): void
    {
        $result = OS::phpSapiName();
        
        $this->assertContains($result, OS::$SAPIs);
    }

    public function testIsIncludeableReturnsTrueForExistingFile(): void
    {
        $result = OS::is_includeable(__FILE__);
        
        $this->assertTrue($result);
    }

    public function testIsIncludeableReturnsFalseForNonExistingFile(): void
    {
        $result = OS::is_includeable('/non/existing/file.php');
        
        $this->assertFalse($result);
    }

    public function testIsPathAbsoluteReturnsTrueForUnixAbsolutePath(): void
    {
        $result = OS::isPathAbsolute('/var/www/file.php');
        
        $this->assertTrue($result);
    }

    public function testIsPathAbsoluteReturnsFalseForUnixRelativePath(): void
    {
        $result = OS::isPathAbsolute('var/www/file.php');
        
        $this->assertFalse($result);
    }

    public function testIsPathAbsoluteReturnsTrueForWindowsAbsolutePath(): void
    {
        // Test Windows absolute paths (work on any platform)
        $result = OS::isPathAbsolute('C:\\Windows\\file.php');
        $this->assertTrue($result, 'Windows drive letter path should be absolute');
        
        $result = OS::isPathAbsolute('D:\\Program Files\\app.exe');
        $this->assertTrue($result, 'Windows drive letter path with spaces should be absolute');
        
        // Test Windows UNC paths
        $result = OS::isPathAbsolute('\\\\server\\share\\file.txt');
        $this->assertTrue($result, 'Windows UNC path should be absolute');
    }

    public function testIsPathAbsoluteReturnsTrueForStreamWrappers(): void
    {
        $result = OS::isPathAbsolute('php://stdout');
        
        $this->assertTrue($result);
    }

    public function testIsPathAbsoluteReturnsTrueForHttpStream(): void
    {
        $result = OS::isPathAbsolute('http://example.com/file.php');
        
        $this->assertTrue($result);
    }
}
