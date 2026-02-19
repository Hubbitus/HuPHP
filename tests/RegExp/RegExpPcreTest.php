<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\RegExp;

use Hubbitus\HuPHP\RegExp\RegExpPcre;
use PHPUnit\Framework\TestCase;

class RegExpPcreTest extends TestCase
{
	public function testConstructor(): void
	{
		$regexp = new RegExpPcre('/test/');
		$this->assertInstanceOf(RegExpPcre::class, $regexp);
		$this->assertEquals('/test/', $regexp->getRegExp());
	}

	public function testConstructorWithText(): void
	{
		$regexp = new RegExpPcre('/test/', 'this is a test text');
		$this->assertEquals('this is a test text', $regexp->getText());
	}

	public function testConstructorWithReplaceTo(): void
	{
		$regexp = new RegExpPcre('/test/', 'this is a test text', 'replaced');
		$this->assertEquals('this is a replaced text', $regexp->replace());
	}

	public function testSetRegExp(): void
	{
		$regexp = new RegExpPcre();
		$result = $regexp->setRegExp('/pattern/');
		$this->assertSame($regexp, $result);
		$this->assertEquals('/pattern/', $regexp->getRegExp());
	}

	public function testSetText(): void
	{
		$regexp = new RegExpPcre();
		$result = $regexp->setText('sample text');
		$this->assertSame($regexp, $result);
		$this->assertEquals('sample text', $regexp->getText());
	}

	public function testSetReplaceTo(): void
	{
		$regexp = new RegExpPcre();
		$result = $regexp->setReplaceTo('replacement');
		$this->assertSame($regexp, $result);
	}

	public function testSet(): void
	{
		$regexp = new RegExpPcre();
		$result = $regexp->set('/pattern/', 'text', 'replacement');
		$this->assertSame($regexp, $result);
		$this->assertEquals('/pattern/', $regexp->getRegExp());
		$this->assertEquals('text', $regexp->getText());
	}

	public function testTest(): void
	{
		$regexp = new RegExpPcre('/test/', 'this is a test');
		$result = $regexp->test();
		$this->assertEquals(1, $result);
	}

	public function testTestNoMatch(): void
	{
		$regexp = new RegExpPcre('/xyz/', 'this is a test');
		$result = $regexp->test();
		$this->assertEquals(0, $result);
	}

	public function testDoMatch(): void
	{
		$regexp = new RegExpPcre('/test/', 'this is a test');
		$regexp->doMatch();
		$matches = $regexp->getMatches();
		// preg_match returns [0 => full match, 1..n => groups]
		$this->assertIsArray($matches);
	}

	public function testDoMatchAll(): void
	{
		$regexp = new RegExpPcre('/test/', 'test test test');
		$regexp->doMatchAll();
		$matches = $regexp->getMatches();
		// preg_match_all returns [0 => [all full matches], 1..n => [groups]]
		$this->assertCount(3, $matches[0]);
	}

	public function testMatch(): void
	{
		$regexp = new RegExpPcre('/test/', 'this is a test');
		$result = $regexp->match(0);
		$this->assertIsArray($result);
	}

	public function testMatchCount(): void
	{
		$regexp = new RegExpPcre('/test/', 'test test test');
		$regexp->doMatchAll();
		$matches = $regexp->getMatches();
		$this->assertCount(3, $matches[0]);
	}

	public function testReplace(): void
	{
		$regexp = new RegExpPcre('/test/', 'test text test', 'replaced');
		$result = $regexp->replace();
		$this->assertEquals('replaced text replaced', $result);
	}

	public function testReplaceWithLimit(): void
	{
		$regexp = new RegExpPcre('/test/', 'test test test', 'replaced');
		$result = $regexp->replace(1);
		$this->assertEquals('replaced test test', $result);
	}

	public function testSplit(): void
	{
		$regexp = new RegExpPcre('/,/', 'a,b,c');
		$result = $regexp->split(-1, 0);
		$this->assertEquals(['a', 'b', 'c'], $result->getMatches());
	}

	public function testSplitWithLimit(): void
	{
		$regexp = new RegExpPcre('/,/', 'a,b,c,d');
		$result = $regexp->split(3, 0);
		$this->assertEquals(['a', 'b', 'c,d'], $result->getMatches());
	}

	public function testGetMatches(): void
	{
		$regexp = new RegExpPcre('/\btest\b/', 'test text test');
		$regexp->doMatchAll();
		$matches = $regexp->getMatches();
		$this->assertIsArray($matches);
		$this->assertCount(2, $matches[0]);
	}

	public function testGetMatchesWithIndex(): void
	{
		$regexp = new RegExpPcre('/(test)/', 'test text test');
		$regexp->doMatchAll();
		$matches = $regexp->getMatches(1);
		$this->assertIsArray($matches);
	}

	public function testGetRegExpDelimiterStart(): void
	{
		$regexp = new RegExpPcre('/test/');
		$delimiter = $regexp->getRegExpDelimiterStart();
		$this->assertEquals('/', $delimiter);
	}

	public function testGetRegExpDelimiterEnd(): void
	{
		$regexp = new RegExpPcre('/test/');
		$delimiter = $regexp->getRegExpDelimiterEnd();
		$this->assertEquals('/', $delimiter);
	}

	public function testGetRegExpBody(): void
	{
		$regexp = new RegExpPcre('/test/i');
		$body = $regexp->getRegExpBody();
		$this->assertEquals('test', $body);
	}

	public function testGetRegExpModifiers(): void
	{
		$regexp = new RegExpPcre('/test/im');
		$modifiers = $regexp->getRegExpModifiers();
		$this->assertEquals('im', $modifiers);
	}

	public function testQuote(): void
	{
		$quoted = RegExpPcre::quote('test.com');
		$this->assertEquals('test\\.com', $quoted);
	}

	public function testQuoteWithDelimiter(): void
	{
		$quoted = RegExpPcre::quote('test/test', '/');
		$this->assertEquals('test\\/test', $quoted);
	}

	public function testQuoteArray(): void
	{
		$toQuote = ['test.', 'hello+', 'world*'];
		$result = RegExpPcre::quote($toQuote);
		$this->assertIsArray($result);
		$this->assertContains('test\.', $result);
	}
}
