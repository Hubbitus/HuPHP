<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\Gentime;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\Gentime
 */
class GentimeTest extends TestCase {
	public function testConstructor(): void {
		$gentime = new Gentime();

		$this->assertInstanceOf(Gentime::class, $gentime);
	}

	public function testStart(): void {
		$gentime = new Gentime();

		$gentime->start();

		$this->assertNotNull($gentime->time_start);
	}

	public function testStop(): void {
		$gentime = new Gentime();

		$gentime->start();
		$time = $gentime->stop();

		$this->assertIsString($time);
		$this->assertMatchesRegularExpression('/^\d+\.\d+$/', $time);
	}

	public function testBench(): void {
		$gentime = new Gentime();

		ob_start();
		$gentime->bench('$a = 1 + 1;', 10);
		$output = ob_get_clean();

		$this->assertIsString($output);
	}
}