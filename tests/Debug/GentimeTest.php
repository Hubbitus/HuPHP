<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\Gentime;
use PHPUnit\Framework\TestCase;

/**
* Test for Gentime class.
* @covers \Hubbitus\HuPHP\Debug\Gentime
**/
class GentimeTest extends TestCase {
    public function testClassInstantiation(): void {
        $gentime = new Gentime();
        $this->assertInstanceOf(Gentime::class, $gentime);
    }

    public function testStartMethod(): void {
        $gentime = new Gentime();
        $result = $gentime->start();
        $this->assertNull($result);
    }

    public function testStartSetsTimeStart(): void {
        $gentime = new Gentime();
        $gentime->start();
        $this->assertNotNull($gentime->time_start);
        $this->assertIsFloat($gentime->time_start);
    }

    public function testStopMethod(): void {
        $gentime = new Gentime();
        $gentime->start();
        $result = $gentime->stop();
        $this->assertIsString($result);
    }

    public function testStopReturnsFloatString(): void {
        $gentime = new Gentime();
        $gentime->start();
        $result = $gentime->stop();
        $this->assertIsNumeric($result);
    }

    public function testStopReturnsNonNegativeValue(): void {
        $gentime = new Gentime();
        $gentime->start();
        $result = $gentime->stop();
        $this->assertGreaterThanOrEqual(0, (float) $result);
    }

    public function testStartStopSequence(): void {
        $gentime = new Gentime();
        $gentime->start();
        usleep(10000); // Sleep for 10ms
        $result = $gentime->stop();
        $this->assertGreaterThan(0, (float) $result);
    }

    public function testMultipleStartCalls(): void {
        $gentime = new Gentime();
        $gentime->start();
        $time1 = $gentime->time_start;

        usleep(1000);
        $gentime->start();
        $time2 = $gentime->time_start;

        $this->assertNotEquals($time1, $time2);
    }

    public function testMultipleStopCalls(): void {
        $gentime = new Gentime();
        $gentime->start();
        $result1 = $gentime->stop();
        $result2 = $gentime->stop();

        $this->assertIsString($result1);
        $this->assertIsString($result2);
    }

    public function testTimeStartPropertyIsPublic(): void {
        $gentime = new Gentime();
        $this->assertTrue(property_exists($gentime, 'time_start'));

        $reflection = new \ReflectionProperty($gentime, 'time_start');
        $this->assertTrue($reflection->isPublic());
    }

    public function testTimeStartBeforeStart(): void {
        $gentime = new Gentime();
        $this->assertNull($gentime->time_start);
    }

    public function testTimeStartAfterStart(): void {
        $gentime = new Gentime();
        $gentime->start();
        $this->assertNotNull($gentime->time_start);
    }

    public function testStopWithoutStart(): void {
        $gentime = new Gentime();
        $result = $gentime->stop();
        $this->assertIsString($result);
    }

    public function testBenchMethodExists(): void {
        $gentime = new Gentime();
        $this->assertTrue(method_exists($gentime, 'bench'));
    }

    public function testBenchWithSimpleCode(): void {
        $gentime = new Gentime();
        $code = '$x = 1 + 1;';

        ob_start();
        $gentime->bench($code, 10);
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertStringContainsString('Average time', $output);
    }

    public function testBenchWithIterationCount(): void {
        $gentime = new Gentime();
        $code = '$x = 1;';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchWithEmptyCode(): void {
        $gentime = new Gentime();
        $code = '';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchWithFunctionCall(): void {
        $gentime = new Gentime();
        $code = 'strlen("test");';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertIsString($output);
        $this->assertStringContainsString('Average time', $output);
    }

    public function testBenchWithLoop(): void {
        $gentime = new Gentime();
        $code = 'for($i=0;$i<10;$i++) { $x = $i; }';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchOutputsMaxTime(): void {
        $gentime = new Gentime();
        $code = '$x = 1;';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertStringContainsString('Maximum time seconds', $output);
    }

    public function testBenchOutputsMinTime(): void {
        $gentime = new Gentime();
        $code = '$x = 1;';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertStringContainsString('Minimum', $output);
    }

    public function testBenchWithDefaultIterations(): void {
        $gentime = new Gentime();
        $code = '$x = 1;';

        ob_start();
        $gentime->bench($code);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchWithComplexCode(): void {
        $gentime = new Gentime();
        $code = '$arr = range(1, 100); sort($arr);';

        ob_start();
        $gentime->bench($code, 10);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchPreservesOutput(): void {
        $gentime = new Gentime();
        $code = 'echo "test output";';

        ob_start();
        $gentime->bench($code, 1);
        $output = ob_get_clean();

        $this->assertStringContainsString('test output', $output);
    }

    public function testBenchReturnsNothing(): void {
        $gentime = new Gentime();
        $code = '$x = 1;';

        ob_start();
        $result = $gentime->bench($code, 5);
        ob_end_clean();

        $this->assertNull($result);
    }

    public function testStartStopMultipleTimes(): void {
        $gentime = new Gentime();

        for ($i = 0; $i < 3; $i++) {
            $gentime->start();
            usleep(1000);
            $result = $gentime->stop();
            $this->assertIsString($result);
        }
    }

    public function testTimeStartIsFloat(): void {
        $gentime = new Gentime();
        $gentime->start();
        $this->assertIsFloat($gentime->time_start);
    }

    public function testStopResultFormat(): void {
        $gentime = new Gentime();
        $gentime->start();
        $result = $gentime->stop();

        // Result should be a formatted float string
        $this->assertMatchesRegularExpression('/^\d+\.\d+$/', $result);
    }

    public function testBenchWithInvalidIterations(): void {
        $gentime = new Gentime();
        $code = '$x = 1;';

        ob_start();
        $gentime->bench($code, 0);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchWithNegativeIterations(): void {
        $gentime = new Gentime();
        $code = '$x = 1;';

        ob_start();
        $gentime->bench($code, -1);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchWithLargeIterations(): void {
        $gentime = new Gentime();
        $code = '$x = 1;';

        ob_start();
        $gentime->bench($code, 100);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchWithVariableAssignment(): void {
        $gentime = new Gentime();
        $code = '$var = "hello world";';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchWithMathOperations(): void {
        $gentime = new Gentime();
        $code = '$result = sin(45) + cos(45);';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchWithStringConcatenation(): void {
        $gentime = new Gentime();
        $code = '$str = "hello" . " " . "world";';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }

    public function testBenchWithArrayOperations(): void {
        $gentime = new Gentime();
        $code = '$arr = [1,2,3]; array_push($arr, 4);';

        ob_start();
        $gentime->bench($code, 5);
        $output = ob_get_clean();

        $this->assertIsString($output);
    }
}
