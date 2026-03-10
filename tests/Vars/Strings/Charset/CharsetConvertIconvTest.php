<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars\Strings\Charset;

use Hubbitus\HuPHP\Vars\Strings\Charset\CharsetConvert;
use Hubbitus\HuPHP\Vars\Strings\Charset\CharsetConvertIconv;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use PHPUnit\Framework\TestCase;

/**
* Tests for CharsetConvertIconv class.
*
* @covers \Hubbitus\HuPHP\Vars\Strings\Charset\CharsetConvertIconv
* @covers \Hubbitus\HuPHP\Vars\Strings\Charset\CharsetConvert
**/
class CharsetConvertIconvTest extends TestCase {

    public function testConstructor(): void {
        $converter = new CharsetConvertIconv('Hello', 'UTF-8', 'UTF-8');

        $this->assertInstanceOf(CharsetConvertIconv::class, $converter);
    }

    public function testConstructorWithTextOnly(): void {
        $converter = new CharsetConvertIconv('Hello');

        $this->assertInstanceOf(CharsetConvertIconv::class, $converter);
        $this->assertEquals('UTF-8', $converter->getOutEnc());
    }

    public function testConstructorThrowsExceptionWithoutText(): void {
        $this->expectException(VariableRequiredException::class);

        new CharsetConvertIconv(null, 'UTF-8', 'UTF-8');
    }

    public function testConvertWithValidEncoding(): void {
        $converter = new CharsetConvertIconv('Hello', 'UTF-8', 'UTF-8');
        $converter->convert();

        $this->assertEquals('Hello', $converter->getResult());
    }

    public function testConvertFromUTF8ToCP1251(): void {
        $text = 'Привет';
        $converter = new CharsetConvertIconv($text, 'UTF-8', 'CP1251');

        $result = $converter->getResult();

        $this->assertIsString($result);
        $this->assertEquals('Привет', iconv('CP1251', 'UTF-8', $result));
    }

    public function testConvertFromCP1251ToUTF8(): void {
        $text = iconv('UTF-8', 'CP1251', 'Привет');
        $converter = new CharsetConvertIconv($text, 'CP1251', 'UTF-8');

        $result = $converter->getResult();

        $this->assertEquals('Привет', $result);
    }

    public function testConvertThrowsExceptionWithInvalidInEncoding(): void {
        $converter = new CharsetConvertIconv('Hello', null, 'UTF-8');

        $this->expectException(VariableRequiredException::class);

        $converter->convert();
    }

    public function testStaticConvMethod(): void {
        $result = CharsetConvertIconv::conv('Hello', 'UTF-8', 'UTF-8');

        $this->assertIsString($result);
        $this->assertEquals('Hello', $result);
    }

    public function testStaticConvWithConversion(): void {
        $text = 'Привет';
        $result = CharsetConvertIconv::conv($text, 'UTF-8', 'CP1251');

        $this->assertIsString($result);
        $this->assertEquals('Привет', iconv('CP1251', 'UTF-8', $result));
    }

    public function testSetInEnc(): void {
        $converter = new CharsetConvertIconv('Hello');
        $result = $converter->setInEnc('CP1251');

        $this->assertSame($converter, $result);
        $this->assertEquals('CP1251', $converter->getInEnc());
    }

    public function testGetInEnc(): void {
        $converter = new CharsetConvertIconv('Hello', 'CP1251', 'UTF-8');

        $this->assertEquals('CP1251', $converter->getInEnc());
    }

    public function testSetOutEnc(): void {
        $converter = new CharsetConvertIconv('Hello');
        $result = $converter->setOutEnc('CP1251');

        $this->assertSame($converter, $result);
        $this->assertEquals('CP1251', $converter->getOutEnc());
    }

    public function testGetOutEnc(): void {
        $converter = new CharsetConvertIconv('Hello', 'UTF-8', 'CP1251');

        $this->assertEquals('CP1251', $converter->getOutEnc());
    }

    public function testSetText(): void {
        $converter = new CharsetConvertIconv('Hello');
        $result = $converter->setText('World');

        $this->assertSame($converter, $result);
        $this->assertEquals('World', $converter->getText());
    }

    public function testGetText(): void {
        $converter = new CharsetConvertIconv('Test Text');

        $this->assertEquals('Test Text', $converter->getText());
    }

    public function testGetResultTriggersConvertIfEmpty(): void {
        $converter = new CharsetConvertIconv('Hello', 'UTF-8', 'UTF-8');

        $result = $converter->getResult();

        $this->assertEquals('Hello', $result);
    }

    public function testToString(): void {
        $converter = new CharsetConvertIconv('Hello', 'UTF-8', 'UTF-8');

        $this->assertEquals('Hello', (string)$converter);
    }

    public function testConvertWithSpecialCharacters(): void {
        $text = 'Hello @#$%^&*() World!';
        $converter = new CharsetConvertIconv($text, 'UTF-8', 'UTF-8');

        $result = $converter->getResult();

        $this->assertEquals($text, $result);
    }

    public function testConvertWithUnicodeCharacters(): void {
        $text = 'こんにちは世界';
        $converter = new CharsetConvertIconv($text, 'UTF-8', 'UTF-8');

        $result = $converter->getResult();

        $this->assertEquals($text, $result);
    }

    public function testConvertWithEmojiCharacters(): void {
        $text = 'Hello 🌍 World!';
        $converter = new CharsetConvertIconv($text, 'UTF-8', 'UTF-8');

        $result = $converter->getResult();

        $this->assertEquals($text, $result);
    }

    public function testInheritsFromCharsetConvert(): void {
        $converter = new CharsetConvertIconv('Hello');

        $this->assertInstanceOf(CharsetConvert::class, $converter);
    }

    /**
    * Test conversion with invalid input encoding throws ValueError.
    *
    * mb_convert_encoding() throws ValueError for invalid encodings (PHP 8.0+)
    **/
    public function testConvertWithInvalidInputEncoding(): void {
        $this->expectException(\ValueError::class);

        new CharsetConvertIconv('Test text', 'INVALID-ENCODING-XYZ', 'UTF-8');
    }

    /**
    * Test conversion with invalid output encoding throws ValueError.
    *
    * mb_convert_encoding() throws ValueError for invalid encodings (PHP 8.0+)
    **/
    public function testConvertWithInvalidOutputEncoding(): void {
        $this->expectException(\ValueError::class);

        new CharsetConvertIconv('Hello', 'UTF-8', 'NONEXISTENT');
    }
}
