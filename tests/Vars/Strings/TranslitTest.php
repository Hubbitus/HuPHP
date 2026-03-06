<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Strings;

use Hubbitus\HuPHP\Vars\Strings\Translit;
use PHPUnit\Framework\TestCase;

class TranslitTest extends TestCase {
	public function testTranslitSimple(): void {
		$result = Translit::translit('Привет');
		$this->assertEquals('Privet', $result);
	}

	public function testTranslitMixed(): void {
		$result = Translit::translit('Привет World');
		$this->assertEquals('Privet World', $result);
	}

	public function testTranslitAllUpperCase(): void {
		$result = Translit::translit('ПРИВЕТ');
		$this->assertEquals('PRIVET', $result);
	}

	public function testTranslitAllLowerCase(): void {
		$result = Translit::translit('привет');
		$this->assertEquals('privet', $result);
	}

	public function testTranslitWithSpecialChars(): void {
		$result = Translit::translit('Щука');
		$this->assertEquals('Schuka', $result);
	}

	public function testTranslitWithYo(): void {
		$result = Translit::translit('Ёлка');
		$this->assertEquals('Jolka', $result);
	}

	public function testTranslitWithSmallYo(): void {
		$result = Translit::translit('ёлка');
		$this->assertEquals('jolka', $result);
	}

	public function testTranslitWithCh(): void {
		$result = Translit::translit('Чай');
		$this->assertEquals('Chaj', $result);
	}

	public function testTranslitWithSh(): void {
		$result = Translit::translit('Шар');
		$this->assertEquals('Shar', $result);
	}

	public function testTranslitWithZh(): void {
		$result = Translit::translit('Жук');
		$this->assertEquals('Zhuk', $result);
	}

	public function testTranslitWithYu(): void {
		$result = Translit::translit('Юра');
		$this->assertEquals('Jura', $result);
	}

	public function testTranslitWithYa(): void {
		$result = Translit::translit('Яма');
		$this->assertEquals('Jama', $result);
	}

	public function testTranslitWithSoftSign(): void {
		$result = Translit::translit('Мягкий');
		$this->assertEquals('Mjagkij', $result);
	}

	public function testTranslitWithHardSign(): void {
		$result = Translit::translit('Подъезд');
		$this->assertEquals('Podezd', $result);
	}

	public function testTranslitWithY(): void {
		$result = Translit::translit('Ыгры');
		$this->assertEquals('Ygry', $result);
	}

	public function testTranslitWithE(): void {
		$result = Translit::translit('Эхо');
		$this->assertEquals('Jeho', $result);
	}

	public function testTranslitEmptyString(): void {
		$result = Translit::translit('');
		$this->assertEquals('', $result);
	}

	public function testTranslitOnlyLatin(): void {
		$result = Translit::translit('Hello World');
		$this->assertEquals('Hello World', $result);
	}

	public function testTranslitNumbers(): void {
		$result = Translit::translit('Тест123');
		$this->assertEquals('Test123', $result);
	}
}
