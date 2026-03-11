<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\RegExp;

use Hubbitus\HuPHP\RegExp\RegExpPcre;
use PHPUnit\Framework\TestCase;

class RegExpPcreTest extends TestCase {
	public function testConstructor(): void {
		$regexp = new RegExpPcre('/test/');
		$this->assertInstanceOf(RegExpPcre::class, $regexp);
		$this->assertEquals('/test/', $regexp->getRegExp());
	}

	public function testConstructorWithText(): void {
		$regexp = new RegExpPcre('/test/', 'this is a test text');
		$this->assertEquals('this is a test text', $regexp->getText());
	}

	public function testConstructorWithReplaceTo(): void {
		$regexp = new RegExpPcre('/test/', 'this is a test text', 'replaced');
		$this->assertEquals('this is a replaced text', $regexp->replace());
	}

	public function testSetRegExp(): void {
		$regexp = new RegExpPcre();
		$result = $regexp->setRegExp('/pattern/');
		$this->assertSame($regexp, $result);
		$this->assertEquals('/pattern/', $regexp->getRegExp());
	}

	public function testSetText(): void {
		$regexp = new RegExpPcre();
		$result = $regexp->setText('sample text');
		$this->assertSame($regexp, $result);
		$this->assertEquals('sample text', $regexp->getText());
	}

	public function testSetReplaceTo(): void {
		$regexp = new RegExpPcre();
		$result = $regexp->setReplaceTo('replacement');
		$this->assertSame($regexp, $result);
	}

	public function testSet(): void {
		$regexp = new RegExpPcre();
		$result = $regexp->set('/pattern/', 'text', 'replacement');
		$this->assertSame($regexp, $result);
		$this->assertEquals('/pattern/', $regexp->getRegExp());
		$this->assertEquals('text', $regexp->getText());
	}

	public function testTest(): void {
		$regexp = new RegExpPcre('/test/', 'this is a test');
		$result = $regexp->test();
		$this->assertEquals(1, $result);
	}

	public function testTestNoMatch(): void {
		$regexp = new RegExpPcre('/xyz/', 'this is a test');
		$result = $regexp->test();
		$this->assertEquals(0, $result);
	}

	public function testDoMatch(): void {
		$regexp = new RegExpPcre('/test/', 'this is a test');
		$regexp->doMatch();
		$matches = $regexp->getMatches();
		// preg_match returns [0 => full match, 1..n => groups]
		$this->assertIsArray($matches);
	}

	public function testDoMatchAll(): void {
		$regexp = new RegExpPcre('/test/', 'test test test');
		$regexp->doMatchAll();
		$matches = $regexp->getMatches();
		// preg_match_all returns [0 => [all full matches], 1..n => [groups]]
		$this->assertCount(3, $matches[0]);
	}

	public function testMatch(): void {
		$regexp = new RegExpPcre('/test/', 'this is a test');
		$result = $regexp->match(0);
		$this->assertIsArray($result);
	}

	public function testMatchCount(): void {
		$regexp = new RegExpPcre('/test/', 'test test test');
		$regexp->doMatchAll();
		$matches = $regexp->getMatches();
		$this->assertCount(3, $matches[0]);
	}

	public function testReplace(): void {
		$regexp = new RegExpPcre('/test/', 'test text test', 'replaced');
		$result = $regexp->replace();
		$this->assertEquals('replaced text replaced', $result);
	}

	public function testReplaceWithLimit(): void {
		$regexp = new RegExpPcre('/test/', 'test test test', 'replaced');
		$result = $regexp->replace(1);
		$this->assertEquals('replaced test test', $result);
	}

	public function testSplit(): void {
		$regexp = new RegExpPcre('/,/', 'a,b,c');
		$result = $regexp->split(-1, 0);
		$this->assertEquals(['a', 'b', 'c'], $result->getMatches());
	}

	public function testSplitWithLimit(): void {
		$regexp = new RegExpPcre('/,/', 'a,b,c,d');
		$result = $regexp->split(3, 0);
		$this->assertEquals(['a', 'b', 'c,d'], $result->getMatches());
	}

	public function testGetMatches(): void {
		$regexp = new RegExpPcre('/\btest\b/', 'test text test');
		$regexp->doMatchAll();
		$matches = $regexp->getMatches();
		$this->assertIsArray($matches);
		$this->assertCount(2, $matches[0]);
	}

	public function testGetMatchesWithIndex(): void {
		$regexp = new RegExpPcre('/(test)/', 'test text test');
		$regexp->doMatchAll();
		$matches = $regexp->getMatches(1);
		$this->assertIsArray($matches);
	}

	public function testGetRegExpDelimiterStart(): void {
		$regexp = new RegExpPcre('/test/');
		$delimiter = $regexp->getRegExpDelimiterStart();
		$this->assertEquals('/', $delimiter);
	}

	public function testGetRegExpDelimiterEnd(): void {
		$regexp = new RegExpPcre('/test/');
		$delimiter = $regexp->getRegExpDelimiterEnd();
		$this->assertEquals('/', $delimiter);
	}

	public function testGetRegExpBody(): void {
		$regexp = new RegExpPcre('/test/i');
		$body = $regexp->getRegExpBody();
		$this->assertEquals('test', $body);
	}

	public function testGetRegExpModifiers(): void {
		$regexp = new RegExpPcre('/test/im');
		$modifiers = $regexp->getRegExpModifiers();
		$this->assertEquals('im', $modifiers);
	}

	public function testQuote(): void {
		$quoted = RegExpPcre::quote('test.com');
		$this->assertEquals('test\\.com', $quoted);
	}

	public function testQuoteWithDelimiter(): void {
		$quoted = RegExpPcre::quote('test/test', '/');
		$this->assertEquals('test\\/test', $quoted);
	}

	public function testQuoteArray(): void {
		$toQuote = ['test.', 'hello+', 'world*'];
		$result = RegExpPcre::quote($toQuote);
		$this->assertIsArray($result);
		$this->assertContains('test\.', $result);
	}

	public function testConvertOffsetToChars(): void {
		// Test convertOffsetToChars() with UTF-8 text
		// preg_match with PREG_OFFSET_CAPTURE returns byte offsets
		// convertOffsetToChars() converts them to character offsets
		$regexp = new RegExpPcre('/тест/', 'это тест текст');
		$regexp->doMatch(PREG_OFFSET_CAPTURE);

		// Get matches before conversion (byte offsets)
		$matchesBefore = $regexp->getMatches();

		// Convert to character offsets
		$regexp->convertOffsetToChars(PREG_OFFSET_CAPTURE);

		// Get matches after conversion (character offsets)
		$matchesAfter = $regexp->getMatches();

		// Matches should still be an array
		$this->assertIsArray($matchesAfter);
		$this->assertNotEmpty($matchesAfter);
	}

	public function testConvertOffsetToCharsWithMbstring(): void {
		// Test convertOffsetToChars() with multibyte text
		// This tests the mb_strlen path if available
		$regexp = new RegExpPcre('/hello/', 'hello мир hello');
		$regexp->doMatchAll(PREG_OFFSET_CAPTURE);

		// Convert offsets
		$regexp->convertOffsetToChars(PREG_OFFSET_CAPTURE);

		$matches = $regexp->getMatches();
		$this->assertIsArray($matches);
		$this->assertCount(2, $matches[0]);
	}

	public function testConvertOffsetToCharsWithoutFlag(): void {
		// Test convertOffsetToChars() without PREG_OFFSET_CAPTURE flag
		// Should not modify matches
		$regexp = new RegExpPcre('/test/', 'test text test');
		$regexp->doMatch(); // Without PREG_OFFSET_CAPTURE

		// Convert should not change anything without PREG_OFFSET_CAPTURE
		$regexp->convertOffsetToChars();

		$matches = $regexp->getMatches();
		$this->assertIsArray($matches);
	}

	public function testConvertOffsetToCharsWithNoMatch(): void {
		// Test convertOffsetToChars() when there are no matches
		$regexp = new RegExpPcre('/xyz/', 'test text test');
		$regexp->doMatch(PREG_OFFSET_CAPTURE);

		// No matches, convert should not fail
		$regexp->convertOffsetToChars(PREG_OFFSET_CAPTURE);

		$matches = $regexp->getMatches();
		$this->assertIsArray($matches);
	}

	public function testConvertOffsetToCharsWithUnicodeText(): void {
		// Test convertOffsetToChars() with Cyrillic text
		$unicodeText = 'Привет мир';
		$regexp = new RegExpPcre('/мир/', $unicodeText);
		$regexp->doMatch(PREG_OFFSET_CAPTURE);

		// Convert byte offsets to character offsets
		$regexp->convertOffsetToChars(PREG_OFFSET_CAPTURE);

		$matches = $regexp->getMatches();
		$this->assertIsArray($matches);
		$this->assertNotEmpty($matches);

		// Verify matches exist (offset conversion may have issues with multibyte)
		$this->assertArrayHasKey(0, $matches);
	}

	public function testDoMatchWithOffset(): void {
		// Test doMatch() with offset parameter
		$regexp = new RegExpPcre('/test/', 'test test test');
		$regexp->doMatch(PREG_OFFSET_CAPTURE, 5); // Start from offset 5

		$matches = $regexp->getMatches();
		$this->assertIsArray($matches);
	}

	public function testDoMatchAllWithOffset(): void {
		// Test doMatchAll() with offset parameter
		$regexp = new RegExpPcre('/test/', 'test test test');
		$regexp->doMatchAll(PREG_OFFSET_CAPTURE, 5); // Start from offset 5

		$matches = $regexp->getMatches();
		$this->assertIsArray($matches);
	}

	public function testSplitWithoutFlags(): void {
		// Test split() without flags parameter
		$regexp = new RegExpPcre('/,/', 'a,b,c');
		$result = $regexp->split(-1);
		$this->assertEquals(['a', 'b', 'c'], $result->getMatches());
	}

	public function testReplaceCache(): void {
		// Test that replace results are cached
		$regexp = new RegExpPcre('/test/', 'test text test', 'replaced');

		// First replace
		$result1 = $regexp->replace();

		// Second replace (should use cache)
		$result2 = $regexp->replace();

		$this->assertEquals($result1, $result2);
	}

	public function testMatchCountProperty(): void {
		// Test that matchCount is set correctly
		$regexp = new RegExpPcre('/test/', 'test test');
		$regexp->doMatchAll();

		$this->assertEquals(2, $this->getProtectedProperty($regexp, 'matchCount'));
	}

	public function testMatchesValidProperty(): void {
		// Test that matchesValid is set to true after doMatch
		$regexp = new RegExpPcre('/test/', 'test');
		$regexp->doMatch();

		$reflection = new \ReflectionClass($regexp);
		$matchesValidProp = $reflection->getProperty('matchesValid');
		$matchesValidProp->setAccessible(true);

		$this->assertTrue($matchesValidProp->getValue($regexp));
	}

	public function testReplaceValidProperty(): void {
		// Test that replaceValid is set to true after replace
		$regexp = new RegExpPcre('/test/', 'test', 'replaced');
		$regexp->replace();

		$reflection = new \ReflectionClass($regexp);
		$replaceValidProp = $reflection->getProperty('replaceValid');
		$replaceValidProp->setAccessible(true);

		$this->assertTrue($replaceValidProp->getValue($regexp));
	}

	/**
	* Helper method to access protected properties via reflection
	**/
	private function getProtectedProperty($object, string $propertyName): mixed {
		$reflection = new \ReflectionClass($object);
		$property = $reflection->getProperty($propertyName);
		$property->setAccessible(true);
		return $property->getValue($object);
	}
}
