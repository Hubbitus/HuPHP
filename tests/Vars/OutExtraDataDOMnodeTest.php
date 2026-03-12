<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

use Hubbitus\HuPHP\Vars\OutExtraDataDOMnode;
use PHPUnit\Framework\TestCase;

class OutExtraDataDOMnodeTest extends TestCase {
	public function testConstructor(): void {
		$dom = new \DOMDocument('1.0', 'utf-8');
		$element = $dom->createElement('test', 'value');

		$out = new OutExtraDataDOMnode($element);
		$this->assertInstanceOf(OutExtraDataDOMnode::class, $out);
	}

	public function testConstructorWithEncoding(): void {
		$dom = new \DOMDocument('1.0', 'utf-8');
		$element = $dom->createElement('test', 'value');

		$out = new OutExtraDataDOMnode($element, 'utf-8');
		$this->assertInstanceOf(OutExtraDataDOMnode::class, $out);
	}

	public function testStrForConsole(): void {
		$dom = new \DOMDocument('1.0', 'utf-8');
		$element = $dom->createElement('test', 'value');

		$out = new OutExtraDataDOMnode($element);
		$result = $out->strForConsole();
		$this->assertIsString($result);
		$this->assertStringContainsString('<test>', $result);
		$this->assertStringContainsString('value', $result);
	}

	public function testStrForFile(): void {
		$dom = new \DOMDocument('1.0', 'utf-8');
		$element = $dom->createElement('test', 'value');

		$out = new OutExtraDataDOMnode($element);
		$result = $out->strForFile();
		$this->assertIsString($result);
		$this->assertStringContainsString('<test>', $result);
	}

	public function testStrForWeb(): void {
		$dom = new \DOMDocument('1.0', 'utf-8');
		$element = $dom->createElement('test', 'value');

		$out = new OutExtraDataDOMnode($element);
		$result = $out->strForWeb();
		$this->assertIsString($result);
		$this->assertStringContainsString('<test>', $result);
	}

	public function testComplexDomNode(): void {
		$dom = new \DOMDocument('1.0', 'utf-8');
		$root = $dom->createElement('root');
		$child1 = $dom->createElement('child1', 'value1');
		$child2 = $dom->createElement('child2', 'value2');
		$root->appendChild($child1);
		$root->appendChild($child2);

		$out = new OutExtraDataDOMnode($root);
		$result = $out->strForConsole();
		$this->assertIsString($result);
		$this->assertStringContainsString('<root>', $result);
		$this->assertStringContainsString('<child1>', $result);
		$this->assertStringContainsString('value1', $result);
		$this->assertStringContainsString('<child2>', $result);
		$this->assertStringContainsString('value2', $result);
	}
}
