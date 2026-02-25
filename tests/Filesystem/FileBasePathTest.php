<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Filesystem;

use Hubbitus\HuPHP\Filesystem\FileBase;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Filesystem\FileBase
 */
class FileBasePathTest extends TestCase {
    private string $testDir;
    private string $testFile;

    protected function setUp(): void {
        $this->testDir = sys_get_temp_dir() . '/huphp_path_' . uniqid();
        mkdir($this->testDir, 0777, true);
        $this->testFile = $this->testDir . '/file.txt';
        file_put_contents($this->testFile, 'data');
    }

    protected function tearDown(): void {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
        if (is_dir($this->testDir)) {
            rmdir($this->testDir);
        }
    }

    public function testPathAndRawPathRelative(): void {
        $relative = 'relative.txt';
        $file = new FileBase();
        $file->setPath($relative);
        $this->assertEquals($relative, $file->rawPath());
        $this->assertNotEquals($relative, $file->path()); // should be absolute
        $this->assertStringContainsString(getcwd(), $file->path());
    }

    public function testPathAndRawPathAbsolute(): void {
        $file = new FileBase($this->testFile);
        $this->assertSame($this->testFile, $file->rawPath());
        $this->assertSame(realpath($this->testFile), $file->path());
    }
}
