<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\Gentime;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\Gentime
 */
class GentimeTest extends TestCase {
	private Gentime $gentime;

	protected function setUp(): void {
		$this->gentime = new Gentime();
	}

	public function testStartStop(): void {
		$this->gentime->start();
		usleep(10000); // 10ms
		$time = $this->gentime->stop();

		// stop() returns string from sprintf
		$this->assertIsString($time);
		$this->assertGreaterThan(0, (float)$time);
		$this->assertLessThan(1, (float)$time); // Should be less than 1 second
	}

	public function testStartStopMultipleTimes(): void {
		$this->gentime->start();
		usleep(5000);
		$time1 = $this->gentime->stop();

		$this->gentime->start();
		usleep(10000);
		$time2 = $this->gentime->stop();

		$this->assertGreaterThan($time1, $time2);
	}

	public function testStopReturnsFloatString(): void {
		$this->gentime->start();
		$time = $this->gentime->stop();

		// stop() uses sprintf with %f, should return string representation of float
		$this->assertIsString($time);
		$this->assertMatchesRegularExpression('/^\d+\.\d+$/', $time);
	}
}
