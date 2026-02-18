<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Images;

use PHPUnit\Framework\TestCase;
use Hubbitus\HuPHP\Images\GDimage;

/**
* @covers \Hubbitus\HuPHP\Images\GDimage
*/
class GDimageTest extends TestCase {
	private GDimage $gdimage;

	protected function setUp(): void {
		if (!extension_loaded('gd')) {
			$this->markTestSkipped('GD extension is not available');
		}
		$this->gdimage = new GDimage();
	}

	public function testConstructor(): void {
		$gd = new GDimage();
		$this->assertInstanceOf(GDimage::class, $gd);
	}

	public function testGetCapabs(): void {
		$capabs = $this->gdimage->getCapabs();

		$this->assertIsArray($capabs);
		$this->assertArrayHasKey('GD Version', $capabs);
	}

	public function testCreateTrueColor(): void {
		$this->gdimage->createTrueColor(100, 100);

		$this->assertEquals(100, $this->gdimage->width());
		$this->assertEquals(100, $this->gdimage->height());
	}

	public function testCreateTrueColorWithDifferentSizes(): void {
		$this->gdimage->createTrueColor(200, 150);

		$this->assertEquals(200, $this->gdimage->width());
		$this->assertEquals(150, $this->gdimage->height());
	}

	public function testWidth(): void {
		$this->gdimage->createTrueColor(300, 200);

		$this->assertEquals(300, $this->gdimage->width());
	}

	public function testHeight(): void {
		$this->gdimage->createTrueColor(150, 250);

		$this->assertEquals(250, $this->gdimage->height());
	}

	public function testGetResource(): void {
		$this->gdimage->createTrueColor(50, 50);

		$resource = $this->gdimage->getResource();

		$this->assertIsResource($resource);
	}

	public function testImgINITFromString(): void {
		// Create a simple PNG image string
		$img = imagecreatetruecolor(10, 10);
		ob_start();
		imagepng($img);
		$imageString = ob_get_clean();
		imagedestroy($img);

		$this->gdimage->imgFromString($imageString);

		$this->assertEquals(10, $this->gdimage->width());
		$this->assertEquals(10, $this->gdimage->height());
	}

	public function testFill(): void {
		$this->gdimage->createTrueColor(50, 50);

		$this->gdimage->fill('FF0000'); // Red

		$resource = $this->gdimage->getResource();
		$this->assertIsResource($resource);
	}

	public function testFillWithDifferentColors(): void {
		$this->gdimage->createTrueColor(30, 30);

		$this->gdimage->fill('00FF00'); // Green
		$this->gdimage->fill('0000FF'); // Blue

		$resource = $this->gdimage->getResource();
		$this->assertIsResource($resource);
	}

	public function testImgFREE(): void {
		$this->gdimage->createTrueColor(100, 100);
		$this->gdimage->imgFREE();

		// After freeing, width and height should be null
		$this->assertNull($this->gdimage->width());
	}

	public function testUPLcheckDefaultValues(): void {
		$this->assertIsArray($this->gdimage->UPLcheck);
		$this->assertArrayHasKey('maxIMGsize', $this->gdimage->UPLcheck);
		$this->assertArrayHasKey('maxIMGwidth', $this->gdimage->UPLcheck);
		$this->assertArrayHasKey('maxIMGheight', $this->gdimage->UPLcheck);
		$this->assertArrayHasKey('typeAllow', $this->gdimage->UPLcheck);
	}

	public function testUPLcheckMaxIMGsize(): void {
		$this->assertEquals(204800, $this->gdimage->UPLcheck['maxIMGsize']);
	}

	public function testUPLcheckMaxIMGwidth(): void {
		$this->assertEquals(1024, $this->gdimage->UPLcheck['maxIMGwidth']);
	}

	public function testUPLcheckMaxIMGheight(): void {
		$this->assertEquals(768, $this->gdimage->UPLcheck['maxIMGheight']);
	}

	public function testUPLcheckTypeAllow(): void {
		$this->assertIsArray($this->gdimage->UPLcheck['typeAllow']);
		$this->assertContains('image/jpeg', $this->gdimage->UPLcheck['typeAllow']);
		$this->assertContains('image/png', $this->gdimage->UPLcheck['typeAllow']);
		$this->assertContains('image/gif', $this->gdimage->UPLcheck['typeAllow']);
	}

	public function testParseUserColorWithHash(): void {
		$this->gdimage->createTrueColor(50, 50);

		// Test that color parsing doesn't throw exception
		$this->gdimage->fill('#FF0000');

		$resource = $this->gdimage->getResource();
		$this->assertIsResource($resource);
	}

	public function testParseUserColorWithoutHash(): void {
		$this->gdimage->createTrueColor(50, 50);

		$this->gdimage->fill('00FF00');

		$resource = $this->gdimage->getResource();
		$this->assertIsResource($resource);
	}

	public function testEnlargeNoEnlargeNeeded(): void {
		$this->gdimage->createTrueColor(100, 100);

		$result = $this->gdimage->enlarge(50, 50, 'FFFFFF');

		$this->assertTrue($result);
		// Image should remain the same size
		$this->assertEquals(100, $this->gdimage->width());
	}

	public function testEnlargeWidthOnly(): void {
		$this->gdimage->createTrueColor(50, 50);

		$this->gdimage->enlarge(100, 50, 'FFFFFF', 'center', 'center');

		$this->assertEquals(100, $this->gdimage->width());
		$this->assertEquals(50, $this->gdimage->height());
	}

	public function testEnlargeHeightOnly(): void {
		$this->gdimage->createTrueColor(50, 50);

		$this->gdimage->enlarge(50, 100, '000000', 'left', 'top');

		$this->assertEquals(50, $this->gdimage->width());
		$this->assertEquals(100, $this->gdimage->height());
	}

	public function testEnlargeBothDimensions(): void {
		$this->gdimage->createTrueColor(30, 30);

		$this->gdimage->enlarge(100, 100, 'CCCCCC', 'center', 'center');

		$this->assertEquals(100, $this->gdimage->width());
		$this->assertEquals(100, $this->gdimage->height());
	}

	public function testEnlargeAlignmentLeft(): void {
		$this->gdimage->createTrueColor(20, 20);

		$this->gdimage->enlarge(100, 100, 'FFFFFF', 'left', 'center');

		$this->assertEquals(100, $this->gdimage->width());
		$this->assertEquals(100, $this->gdimage->height());
	}

	public function testEnlargeAlignmentRight(): void {
		$this->gdimage->createTrueColor(20, 20);

		$this->gdimage->enlarge(100, 100, 'FFFFFF', 'right', 'center');

		$this->assertEquals(100, $this->gdimage->width());
		$this->assertEquals(100, $this->gdimage->height());
	}

	public function testEnlargeAlignmentTop(): void {
		$this->gdimage->createTrueColor(20, 20);

		$this->gdimage->enlarge(100, 100, 'FFFFFF', 'center', 'top');

		$this->assertEquals(100, $this->gdimage->width());
		$this->assertEquals(100, $this->gdimage->height());
	}

	public function testEnlargeAlignmentBottom(): void {
		$this->gdimage->createTrueColor(20, 20);

		$this->gdimage->enlarge(100, 100, 'FFFFFF', 'center', 'bottom');

		$this->assertEquals(100, $this->gdimage->width());
		$this->assertEquals(100, $this->gdimage->height());
	}

	public function testMultipleInstances(): void {
		$gd1 = new GDimage();
		$gd2 = new GDimage();

		$this->assertInstanceOf(GDimage::class, $gd1);
		$this->assertInstanceOf(GDimage::class, $gd2);
	}

	public function testResizeWithDefaultParameters(): void {
		$source = new GDimage();
		$source->createTrueColor(100, 100);

		$this->gdimage->createTrueColor(50, 50);

		$this->gdimage->resize($source, 0, 0, 0, 0, 50, 50);

		$this->assertEquals(50, $this->gdimage->width());
		$this->assertEquals(50, $this->gdimage->height());
	}

	public function testPreviewLandscape(): void {
		$this->gdimage->createTrueColor(200, 100);

		$this->gdimage->preview(100, 100);

		$this->assertLessThanOrEqual(100, $this->gdimage->width());
		$this->assertLessThanOrEqual(100, $this->gdimage->height());
	}

	public function testPreviewPortrait(): void {
		$this->gdimage->createTrueColor(100, 200);

		$this->gdimage->preview(100, 100);

		$this->assertLessThanOrEqual(100, $this->gdimage->width());
		$this->assertLessThanOrEqual(100, $this->gdimage->height());
	}

	public function testPreviewSquare(): void {
		$this->gdimage->createTrueColor(150, 150);

		$this->gdimage->preview(100, 100);

		$this->assertEquals(100, $this->gdimage->width());
		$this->assertEquals(100, $this->gdimage->height());
	}

	public function testConvertToPNG(): void {
		$this->gdimage->createTrueColor(50, 50);

		$string = $this->gdimage->getString('PNG');

		$this->assertIsString($string);
		$this->assertNotEmpty($string);
	}

	public function testConvertToJPG(): void {
		$this->gdimage->createTrueColor(50, 50);

		$string = $this->gdimage->getString('JPG');

		$this->assertIsString($string);
		$this->assertNotEmpty($string);
	}

	public function testConvertToGIF(): void {
		$this->gdimage->createTrueColor(50, 50);

		$string = $this->gdimage->getString('GIF');

		$this->assertIsString($string);
		$this->assertNotEmpty($string);
	}
}
