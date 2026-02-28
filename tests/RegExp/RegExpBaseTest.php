<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\RegExp;

use Hubbitus\HuPHP\RegExp\RegExpPcre;
use Hubbitus\HuPHP\Vars\HuArray;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * @covers \Hubbitus\HuPHP\RegExp\RegExpBase
 */
class RegExpBaseTest extends TestCase {
    private RegExpPcre $regexp;

    protected function setUp(): void {
        $this->regexp = new RegExpPcre('/test/', 'this is a test text');
    }

    public function testClassIsAbstract(): void {
        $reflection = new ReflectionClass('Hubbitus\\HuPHP\\RegExp\\RegExpBase');

        $this->assertTrue($reflection->isAbstract());
    }

    public function testConstructor(): void {
        $regexp = new RegExpPcre('/pattern/', 'sample text', 'replacement');

        $this->assertEquals('/pattern/', $regexp->getRegExp());
        $this->assertEquals('sample text', $regexp->getText());
    }

    public function testConstructorWithNullValues(): void {
        $regexp = new RegExpPcre();

        $this->assertNull($regexp->getRegExp());
        $this->assertNull($regexp->getText());
    }

    public function testSetRegExp(): void {
        $regexp = new RegExpPcre();
        $result = $regexp->setRegExp('/new_pattern/');

        $this->assertSame($regexp, $result);
        $this->assertEquals('/new_pattern/', $regexp->getRegExp());
    }

    public function testSetText(): void {
        $regexp = new RegExpPcre();
        $result = $regexp->setText('new text');

        $this->assertSame($regexp, $result);
        $this->assertEquals('new text', $regexp->getText());
    }

    public function testSetTextRef(): void {
        $regexp = new RegExpPcre();
        $text = 'reference text';
        $result = $regexp->setTextRef($text);

        $this->assertSame($regexp, $result);
        $this->assertEquals('reference text', $regexp->getText());

        // Modify original text and check if it reflects in regexp
        $text = 'modified text';
        $this->assertEquals('modified text', $regexp->getText());
    }

    public function testSetReplaceTo(): void {
        $regexp = new RegExpPcre();
        $result = $regexp->setReplaceTo('replacement');

        $this->assertSame($regexp, $result);
    }

    public function testSetWithAllParameters(): void {
        $regexp = new RegExpPcre();
        $result = $regexp->set('/pattern/', 'text', 'replacement');

        $this->assertSame($regexp, $result);
        $this->assertEquals('/pattern/', $regexp->getRegExp());
        $this->assertEquals('text', $regexp->getText());
    }

    public function testSetWithPartialParameters(): void {
        $regexp = new RegExpPcre('/existing/');
        $regexp->set(null, 'new text', null);

        $this->assertEquals('/existing/', $regexp->getRegExp());
        $this->assertEquals('new text', $regexp->getText());
    }

    public function testMatchCount(): void {
        $regexp = new RegExpPcre('/test/', 'test test test');
        $regexp->doMatchAll();

        $count = $regexp->matchCount();

        $this->assertEquals(3, $count);
    }

    public function testMatchCountTriggersDoMatchAll(): void {
        $regexp = new RegExpPcre('/test/', 'test test');

        $count = $regexp->matchCount();

        $this->assertEquals(2, $count);
    }

    public function testMatch(): void {
        $regexp = new RegExpPcre('/test/', 'this is a test');
        $regexp->doMatch();

        $match = $regexp->match(0);

        $this->assertIsArray($match);
        $this->assertEquals('test', $match[0]);
    }

    public function testMatchTriggersDoMatch(): void {
        $regexp = new RegExpPcre('/test/', 'this is a test');

        $match = $regexp->match(0);

        $this->assertIsArray($match);
    }

    public function testGetRegExp(): void {
        $regexp = new RegExpPcre('/pattern/');

        $this->assertEquals('/pattern/', $regexp->getRegExp());
    }

    public function testGetText(): void {
        $regexp = new RegExpPcre('/test/', 'sample text');

        $this->assertEquals('sample text', $regexp->getText());
    }

    public function testGetRegExpDelimiterStart(): void {
        $regexp = new RegExpPcre('/test/');

        $delimiter = $regexp->getRegExpDelimiterStart();

        $this->assertEquals('/', $delimiter);
    }

    public function testGetRegExpDelimiterStartWithArray(): void {
        $regexp = new RegExpPcre(['/test/', '/pattern/']);

        $delimiter = $regexp->getRegExpDelimiterStart(1);

        $this->assertEquals('/', $delimiter);
    }

    public function testGetRegExpDelimiterStartWithCustomDelimiter(): void {
        $regexp = new RegExpPcre('#test#');

        $delimiter = $regexp->getRegExpDelimiterStart();

        $this->assertEquals('#', $delimiter);
    }

    public function testGetRegExpDelimiterEnd(): void {
        $regexp = new RegExpPcre('/test/');

        $delimiter = $regexp->getRegExpDelimiterEnd();

        $this->assertEquals('/', $delimiter);
    }

    public function testGetRegExpDelimiterEndWithPairedDelimiter(): void {
        $regexp = new RegExpPcre('{test}');

        $delimiter = $regexp->getRegExpDelimiterEnd();

        $this->assertEquals('}', $delimiter);
    }

    public function testGetRegExpDelimiterEndWithAngleBrackets(): void {
        $regexp = new RegExpPcre('<test>');

        $delimiter = $regexp->getRegExpDelimiterEnd();

        $this->assertEquals('>', $delimiter);
    }

    public function testGetRegExpDelimiterEndWithParentheses(): void {
        $regexp = new RegExpPcre('(test)');

        $delimiter = $regexp->getRegExpDelimiterEnd();

        $this->assertEquals(')', $delimiter);
    }

    public function testGetRegExpDelimiterEndWithSquareBrackets(): void {
        $regexp = new RegExpPcre('[test]');

        $delimiter = $regexp->getRegExpDelimiterEnd();

        $this->assertEquals(']', $delimiter);
    }

    public function testGetRegExpBody(): void {
        $regexp = new RegExpPcre('/test/i');

        $body = $regexp->getRegExpBody();

        $this->assertEquals('test', $body);
    }

    public function testGetRegExpBodyWithCustomDelimiter(): void {
        $regexp = new RegExpPcre('#test#im');

        $body = $regexp->getRegExpBody();

        $this->assertEquals('test', $body);
    }

    public function testGetRegExpModifiers(): void {
        $regexp = new RegExpPcre('/test/im');

        $modifiers = $regexp->getRegExpModifiers();

        $this->assertEquals('im', $modifiers);
    }

    public function testGetRegExpModifiersWithoutModifiers(): void {
        $regexp = new RegExpPcre('/test/');

        $modifiers = $regexp->getRegExpModifiers();

        $this->assertEquals('', $modifiers);
    }

    public function testGetMatches(): void {
        $regexp = new RegExpPcre('/test/', 'test test');
        $regexp->doMatchAll();

        $matches = $regexp->getMatches();

        $this->assertIsArray($matches);
        $this->assertCount(2, $matches[0]);
    }

    public function testGetMatchesWithIndex(): void {
        $regexp = new RegExpPcre('/(test)/', 'test text');
        $regexp->doMatchAll();

        $matches = $regexp->getMatches(1);

        $this->assertIsArray($matches);
    }

    public function testGetMatchesWithNullIndex(): void {
        $regexp = new RegExpPcre('/test/', 'test');
        $regexp->doMatchAll();

        $matches = $regexp->getMatches(null);

        $this->assertIsArray($matches);
    }

    public function testGetHuMatches(): void {
        $regexp = new RegExpPcre('/test/', 'test test');
        $regexp->doMatchAll();

        $huMatches = $regexp->getHuMatches();

        $this->assertInstanceOf(HuArray::class, $huMatches);
    }

    public function testGetHuMatchesWithIndex(): void {
        $regexp = new RegExpPcre('/(test)/', 'test');
        $regexp->doMatchAll();

        $huMatches = $regexp->getHuMatches(1);

        $this->assertInstanceOf(HuArray::class, $huMatches);
    }

    public function testPaireddelimetersProperty(): void {
        $reflection = new ReflectionClass('Hubbitus\\HuPHP\\RegExp\\RegExpBase');
        $property = $reflection->getProperty('paireddelimeters');
        $property->setAccessible(true);

        $instance = new RegExpPcre();
        $delimiters = $property->getValue($instance);

        $this->assertIsArray($delimiters);
        $this->assertArrayHasKey('{', $delimiters);
        $this->assertEquals('}', $delimiters['{']);
        $this->assertArrayHasKey('<', $delimiters);
        $this->assertEquals('>', $delimiters['<']);
        $this->assertArrayHasKey('(', $delimiters);
        $this->assertEquals(')', $delimiters['(']);
        $this->assertArrayHasKey('[', $delimiters);
        $this->assertEquals(']', $delimiters['[']);
    }

    public function testAbstractMethodsExist(): void {
        $reflection = new ReflectionClass('Hubbitus\\HuPHP\\RegExp\\RegExpBase');

        $methods = $reflection->getMethods(ReflectionMethod::IS_ABSTRACT);

        $abstractMethodNames = array_map(fn($method) => $method->getName(), $methods);

        $this->assertContains('test', $abstractMethodNames);
        $this->assertContains('doMatch', $abstractMethodNames);
        $this->assertContains('doMatchAll', $abstractMethodNames);
        $this->assertContains('replace', $abstractMethodNames);
        $this->assertContains('split', $abstractMethodNames);
    }

    public function testImplementsIRegExp(): void {
        $reflection = new ReflectionClass('Hubbitus\\HuPHP\\RegExp\\RegExpBase');

        $this->assertTrue($reflection->implementsInterface('Hubbitus\\HuPHP\\RegExp\\IRegExp'));
    }

    public function testExtendsHuClass(): void {
        $reflection = new ReflectionClass('Hubbitus\\HuPHP\\RegExp\\RegExpBase');

        $this->assertTrue($reflection->isSubclassOf('Hubbitus\\HuPHP\\Vars\\HuClass'));
    }

    public function testProtectedProperties(): void {
        $reflection = new ReflectionClass('Hubbitus\\HuPHP\\RegExp\\RegExpBase');

        $properties = [
            'sourceText',
            'RegExp',
            'matchCount',
            'matches',
            'replaceTo',
            'replaceRes',
        ];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isProtected(), "Property $propertyName should be protected");
        }
    }

    public function testMatchesValidPropertyDefaultValue(): void {
        $reflection = new ReflectionClass('Hubbitus\\HuPHP\\RegExp\\RegExpBase');
        $property = $reflection->getProperty('matchesValid');

        $this->assertFalse($property->getDefaultValue());
    }

    public function testReplaceValidPropertyDefaultValue(): void {
        $reflection = new ReflectionClass('Hubbitus\\HuPHP\\RegExp\\RegExpBase');
        $property = $reflection->getProperty('replaceValid');

        // Property has no explicit default value, so it's null until set
        $this->assertNull($property->getDefaultValue());
    }
}
