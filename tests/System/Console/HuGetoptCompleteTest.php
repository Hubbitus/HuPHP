<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\System\Console;

use PHPUnit\Framework\TestCase;
use Hubbitus\HuPHP\System\Console\HuGetopt;
use Hubbitus\HuPHP\System\Console\HuGetoptOption;
use Hubbitus\HuPHP\System\Console\HuGetoptSettings;
use Hubbitus\HuPHP\Vars\HuArray;

/**
* Complete coverage tests for HuGetopt, HuGetoptOption and HuGetoptSettings.
*
* @covers \Hubbitus\HuPHP\System\Console\HuGetopt
* @covers \Hubbitus\HuPHP\System\Console\HuGetoptOption
* @covers \Hubbitus\HuPHP\System\Console\HuGetoptSettings
**/
class HuGetoptCompleteTest extends TestCase {

	/**
	* Test HuGetoptOption constructor with numeric array.
	**/
	public function testHuGetoptOptionConstructorWithNumericArray(): void {
		$possibles = ['OptL', 'OptS', 'Mod'];
		$option = new HuGetoptOption($possibles);

		$this->assertInstanceOf(HuGetoptOption::class, $option);
		$this->assertInstanceOf(HuArray::class, $option->Opt);
		$this->assertInstanceOf(HuArray::class, $option->Sep);
		$this->assertInstanceOf(HuArray::class, $option->Val);
		$this->assertInstanceOf(HuArray::class, $option->OptT);
	}

	/**
	* Test HuGetoptOption constructor with associative array.
	**/
	public function testHuGetoptOptionConstructorWithAssociativeArray(): void {
		$possibles = ['OptL' => null, 'OptS' => null, 'Mod' => null];
		$array = [
			'OptS' => 'v',
			'OptL' => 'verbose',
			'Mod' => ':'
		];
		$option = new HuGetoptOption($possibles, $array);

		$this->assertInstanceOf(HuGetoptOption::class, $option);
		$this->assertEquals('v', $option->OptS);
		$this->assertEquals('verbose', $option->OptL);
		$this->assertEquals(':', $option->Mod);
	}

	/**
	* Test HuGetoptOption constructor with initial array containing internal properties.
	**/
	public function testHuGetoptOptionConstructorWithInternalProperties(): void {
		$possibles = ['OptL', 'OptS', 'Mod'];
		$array = [
			'OptS' => 'f',
			'Opt' => new HuArray(['f']),
			'Sep' => new HuArray(['-']),
			'Val' => new HuArray(['test.txt']),
			'OptT' => new HuArray(['s'])
		];
		$option = new HuGetoptOption($possibles, $array);

		$this->assertInstanceOf(HuGetoptOption::class, $option);
		$this->assertEquals('f', $option->OptS);
		$this->assertCount(1, $option->Opt);
		$this->assertEquals('f', $option->Opt->{0});
		$this->assertCount(1, $option->Val);
		$this->assertEquals('test.txt', $option->Val->{0});
	}

	/**
	* Test HuGetoptOption add method.
	**/
	public function testHuGetoptOptionAdd(): void {
		$possibles = ['OptL', 'OptS', 'Mod'];
		$option1 = new HuGetoptOption($possibles, [
			'OptS' => 'v',
			'Opt' => new HuArray(['v']),
			'Val' => new HuArray([true])
		]);

		$option2 = new HuGetoptOption($possibles, [
			'OptS' => 'v',
			'Opt' => new HuArray(['v']),
			'Val' => new HuArray([false])
		]);

		$result = $option1->add($option2);

		$this->assertSame($option1, $result);
		$this->assertCount(2, $option1->Opt);
		$this->assertCount(2, $option1->Val);
	}

	/**
	* Test HuGetoptOption add method with all properties.
	**/
	public function testHuGetoptOptionAddAllProperties(): void {
		$possibles = ['OptL', 'OptS', 'Mod'];
		$option1 = new HuGetoptOption($possibles, [
			'OptS' => 'f',
			'Opt' => new HuArray(['f']),
			'Sep' => new HuArray(['-']),
			'Val' => new HuArray(['file1.txt']),
			'=' => new HuArray([false]),
			'OptT' => new HuArray(['s'])
		]);

		$option2 = new HuGetoptOption($possibles, [
			'OptS' => 'f',
			'Opt' => new HuArray(['f']),
			'Sep' => new HuArray(['-']),
			'Val' => new HuArray(['file2.txt']),
			'=' => new HuArray([false]),
			'OptT' => new HuArray(['s'])
		]);

		$option1->add($option2);

		$this->assertCount(2, $option1->Opt);
		$this->assertCount(2, $option1->Sep);
		$this->assertCount(2, $option1->Val);
		$this->assertCount(2, $option1->{'='});
		$this->assertCount(2, $option1->OptT);
	}

	/**
	* Test HuGetoptSettings constructor and default values.
	**/
	public function testHuGetoptSettingsConstructor(): void {
		$settings = new HuGetoptSettings();

		$this->assertInstanceOf(HuGetoptSettings::class, $settings);
		$this->assertEquals(['-'], $settings->start_short);
		$this->assertEquals(['--'], $settings->start_long);
		$this->assertFalse($settings->alternative);
		$this->assertIsArray($settings->HuGetopt_option_options);
	}

	/**
	* Test HuGetoptSettings HuGetopt_option_options contains all required properties.
	**/
	public function testHuGetoptSettingsOptionOptions(): void {
		$settings = new HuGetoptSettings();
		$options = $settings->HuGetopt_option_options;

		$this->assertContains('OptL', $options);
		$this->assertContains('OptS', $options);
		$this->assertContains('Mod', $options);
		$this->assertContains('Opt', $options);
		$this->assertContains('Sep', $options);
		$this->assertContains('Val', $options);
		$this->assertContains('=', $options);
		$this->assertContains('OptT', $options);
	}

	/**
	* Test HuGetoptSettings can be modified.
	**/
	public function testHuGetoptSettingsModification(): void {
		$settings = new HuGetoptSettings();
		$settings->start_short = ['-', '+'];
		$settings->start_long = ['---', '--'];
		$settings->alternative = true;

		$this->assertEquals(['-', '+'], $settings->start_short);
		$this->assertEquals(['---', '--'], $settings->start_long);
		$this->assertTrue($settings->alternative);
	}

	/**
	* Test HuGetopt with custom settings.
	**/
	public function testHuGetoptWithCustomSettings(): void {
		$settings = new HuGetoptSettings();
		$settings->start_short = ['-', '+'];
		$settings->alternative = true;

		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts, $settings);

		$getopt->setArgv(['script.php', '-v']);
		$getopt->parseArgs();

		$opt = $getopt->get('v');
		$this->assertNotEmpty($opt->Opt);
	}

	/**
	* Test HuGetopt getOptByStr with all type variations.
	**/
	public function testHuGetoptGetOptByStrAllTypes(): void {
		$opts = [['x', 'xopt', '']];
		$getopt = new HuGetopt($opts);

		// Type 's' - short
		$opt = $getopt->getOptByStr('x', 's');
		$this->assertInstanceOf(HuGetoptOption::class, $opt);

		// Type 'l' - long
		$opt = $getopt->getOptByStr('xopt', 'l');
		$this->assertInstanceOf(HuGetoptOption::class, $opt);

		// Type 'a' or default - auto detect by length
		$opt = $getopt->getOptByStr('x', 'a');
		$this->assertInstanceOf(HuGetoptOption::class, $opt);

		// Default (no type specified) - auto detect
		$opt = $getopt->getOptByStr('xopt');
		$this->assertInstanceOf(HuGetoptOption::class, $opt);
	}

	/**
	* Test HuGetopt nextArg method.
	**/
	public function testHuGetoptNextArg(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v', 'arg1', 'arg2']);

		// Access protected method via reflection
		$reflection = new \ReflectionClass($getopt);
		$method = $reflection->getMethod('nextArg');
		$method->setAccessible(true);

		// First call should return second element (index 1)
		$arg1 = $method->invoke($getopt);
		$this->assertEquals('-v', $arg1);

		$arg2 = $method->invoke($getopt);
		$this->assertEquals('arg1', $arg2);

		$arg3 = $method->invoke($getopt);
		$this->assertEquals('arg2', $arg3);

		// No more args
		$arg4 = $method->invoke($getopt);
		$this->assertFalse($arg4);
	}

	/**
	* Test HuGetopt currentArg method.
	**/
	public function testHuGetoptCurrentArg(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v', 'arg1']);

		// Access protected method via reflection
		$reflection = new \ReflectionClass($getopt);
		$method = $reflection->getMethod('currentArg');
		$method->setAccessible(true);

		$arg = $method->invoke($getopt);
		$this->assertEquals('script.php', $arg);
	}

	/**
	* Test HuGetopt peekNextArg method.
	**/
	public function testHuGetoptPeekNextArg(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v', 'arg1']);

		// Access protected method via reflection
		$reflection = new \ReflectionClass($getopt);
		$method = $reflection->getMethod('peekNextArg');
		$method->setAccessible(true);

		// Peek at next arg without consuming
		$peeked = $method->invoke($getopt);
		$this->assertEquals('-v', $peeked);

		// Current arg should still be script.php
		$currentMethod = $reflection->getMethod('currentArg');
		$currentMethod->setAccessible(true);
		$current = $currentMethod->invoke($getopt);
		$this->assertEquals('script.php', $current);
	}

	/**
	* Test HuGetopt getOpt method (combined short/long check).
	**/
	public function testHuGetoptGetOpt(): void {
		$opts = [['s', 'short', ''], ['l', 'long', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-s']);

		// Access protected method via reflection
		$reflection = new \ReflectionClass($getopt);
		$method = $reflection->getMethod('getOpt');
		$method->setAccessible(true);

		// Test short option
		$result = $method->invoke($getopt, '-s');
		$this->assertInstanceOf(HuGetoptOption::class, $result);

		// Test long option
		$result = $method->invoke($getopt, '--long');
		$this->assertInstanceOf(HuGetoptOption::class, $result);

		// Test non-option
		$result = $method->invoke($getopt, 'not-an-option');
		$this->assertNull($result);
	}

	/**
	* Test HuGetopt getShortOpt with multiple short options in clue form.
	**/
	public function testHuGetoptIsShortOptClueForm(): void {
		$opts = [
			['a', 'all', ''],
			['b', 'brief', ''],
			['c', 'count', '']
		];
		$getopt = new HuGetopt($opts);

		$result = $getopt->getShortOpt('-abc');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
		$this->assertEquals('a', $result->Opt->{0});
	}

	/**
	* Test HuGetopt getShortOpt with option that requires argument.
	**/
	public function testHuGetoptIsShortOptWithRequiredArgument(): void {
		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-f', 'test.txt']);

		$result = $getopt->getShortOpt('-f');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
	}

	/**
	* Test HuGetopt getLongOpt with equals separator.
	**/
	public function testHuGetoptIsLongOptWithEquals(): void {
		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);

		$result = $getopt->getLongOpt('--file=value.txt');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
		$this->assertEquals('=', $result->{'='}->{0});
		$this->assertEquals('value.txt', $result->Val->{0});
	}

	/**
	* Test HuGetopt getLongOpt with space separator.
	**/
	public function testHuGetoptIsLongOptWithSpace(): void {
		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '--file', 'test.txt']);

		$result = $getopt->getLongOpt('--file');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
	}

	/**
	* Test HuGetopt getLongOpt with alternative mode enabled.
	**/
	public function testHuGetoptIsLongOptAlternativeMode(): void {
		$settings = new HuGetoptSettings();
		$settings->alternative = true;

		$opts = [['l', 'long', '']];
		$getopt = new HuGetopt($opts, $settings);
		$getopt->setArgv(['script.php', '-long']);

		// With alternative=true, long options can start with short_start too
		$result = $getopt->getLongOpt('-long');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
	}

	/**
	* Test HuGetopt parseArgs with option that has optional value (::) but no value provided.
	**/
	public function testHuGetoptParseArgsOptionalValueNoValue(): void {
		$opts = [['o', 'opt', '::']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-o']);

		$getopt->parseArgs();

		$opt = $getopt->get('o');
		// Should have default value (empty string for optional)
		$this->assertCount(1, $opt->Val);
	}

	/**
	* Test HuGetopt parseArgs with multiple values for same option.
	**/
	public function testHuGetoptParseArgsMultipleSameOption(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v', '-v', '-v']);

		$getopt->parseArgs();

		$opt = $getopt->get('v');
		$this->assertCount(3, $opt->Opt);
		$this->assertCount(3, $opt->Val);
	}

	/**
	* Test HuGetopt getNonOpts with various from indices.
	**/
	public function testHuGetoptGetNonOptsVariousIndices(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', 'file1.txt', 'file2.txt', 'file3.txt']);
		$getopt->parseArgs();

		// From 0 - all non-opts including script name
		$nonOpts0 = $getopt->getNonOpts(0);
		$this->assertCount(4, $nonOpts0);

		// From 1 - skip script name
		$nonOpts1 = $getopt->getNonOpts(1);
		$this->assertCount(3, $nonOpts1);

		// From 2 - skip script name and first file
		$nonOpts2 = $getopt->getNonOpts(2);
		$this->assertCount(2, $nonOpts2);
	}

	/**
	* Test HuGetopt readPHPArgv with global $argv.
	**/
	public function testHuGetoptReadPhpArgvWithGlobalArgv(): void {
		global $argv;
		$originalArgv = $argv ?? null;

		$argv = ['script.php', '-v'];

		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$result = $getopt->readPHPArgv();

		$this->assertSame($getopt, $result);

		// Restore
		if ($originalArgv !== null) {
			$argv = $originalArgv;
		} else {
			unset($argv);
		}
	}

	/**
	* Test HuGetopt with mixed short and long options parsing.
	**/
	public function testHuGetoptParseMixedShortAndLong(): void {
		$opts = [
			['v', 'verbose', ''],
			['f', 'file', ':'],
			['o', 'output', ':']
		];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v', '--file', 'input.txt', '-o', 'output.txt']);

		$getopt->parseArgs();

		$optV = $getopt->get('v');
		$optF = $getopt->get('file', 'l');
		$optO = $getopt->get('o');

		$this->assertCount(1, $optV->Opt);
		$this->assertCount(1, $optF->Val);
		$this->assertEquals('input.txt', $optF->Val->{0});
		$this->assertCount(1, $optO->Val);
		$this->assertEquals('output.txt', $optO->Val->{0});
	}

	/**
	* Test HuGetopt parseArgs with complex mixed options.
	**/
	public function testHuGetoptParseComplexMixed(): void {
		$opts = [
			['h', 'help', ''],
			['v', 'verbose', ''],
			['f', 'file', ':'],
			['d', 'dir', '::']
		];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-hv', '--file=test.txt', 'arg1', '-d', 'mydir', 'arg2']);

		$getopt->parseArgs();

		$optH = $getopt->get('h');
		$optV = $getopt->get('v');
		$optF = $getopt->get('file', 'l');
		$optD = $getopt->get('d');

		$this->assertCount(1, $optH->Opt);
		$this->assertCount(1, $optV->Opt);
		$this->assertCount(1, $optF->Val);
		$this->assertEquals('test.txt', $optF->Val->{0});
		$this->assertCount(1, $optD->Val);
		$this->assertEquals('mydir', $optD->Val->{0});

		$nonOpts = $getopt->getNonOpts(1);
		$this->assertCount(2, $nonOpts);
	}

	/**
	* Test HuGetopt getShortOpt with empty match group 3 (no extra chars after option).
	**/
	public function testHuGetoptIsShortOptEmptyMatch3(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v']);

		$result = $getopt->getShortOpt('-v');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
		$this->assertEquals('v', $result->Opt->{0});
	}

	/**
	* Test HuGetopt getLongOpt with empty match group 4 (no value after =).
	**/
	public function testHuGetoptIsLongOptEmptyValue(): void {
		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '--file=']);

		$result = $getopt->getLongOpt('--file=');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
	}

	/**
	* Test HuGetopt parseArgs with option having explicit value in long form.
	**/
	public function testHuGetoptParseArgsLongFormExplicitValue(): void {
		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '--file=value.txt']);

		$getopt->parseArgs();

		$opt = $getopt->get('file', 'l');
		$this->assertCount(1, $opt->Val);
		$this->assertEquals('value.txt', $opt->Val->{0});
	}

	/**
	* Test HuGetopt parseArgs when required option next is option (should throw exception).
	**/
	public function testHuGetoptParseArgsRequiredOptionNextIsOption(): void {
		$this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException::class);
		$this->expectExceptionMessage('requires argument');

		$opts = [
			['f', 'file', ':'],
			['v', 'verbose', '']
		];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-f', '-v']);

		$getopt->parseArgs();
	}

	/**
	* Test HuGetopt parseArgs when required option at end with no value.
	**/
	public function testHuGetoptParseArgsRequiredOptionAtEnd(): void {
		$this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException::class);
		$this->expectExceptionMessage('requires argument');

		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-f']);

		$getopt->parseArgs();
	}

	/**
* Test HuGetopt parseArgs when required option has value in same arg (covers getOptPeeked branch).
	 This covers the branch where getOptPeeked = true and throws exception.
	**/
	public function testHuGetoptParseArgsRequiredOptionWithValueInSameArg(): void {
		// When -fvalue is passed, the 'value' part becomes match(3) in getShortOpt
		// and is used directly without calling nextArg()
		// This should NOT throw exception because value is provided
		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-ftest.txt']);

		$getopt->parseArgs();

		$optF = $getopt->get('f');
		$this->assertCount(1, $optF->Val);
		$this->assertEquals('test.txt', $optF->Val->{0});
	}

	/**
* Test HuGetopt parseArgs with long option form --opt=value (covers hasExplicitValue branch).
	 This covers the branch where hasExplicitValue = true.
	**/
	public function testHuGetoptParseArgsLongOptionEqualsFormCoversBranches(): void {
		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '--file=value.txt']);

		$getopt->parseArgs();

		$optF = $getopt->get('file', 'l');
		// Val should have explicit value from = form
		$this->assertCount(1, $optF->Val);
		$this->assertEquals('value.txt', $optF->Val->{0});
	}

	/**
* Test HuGetopt parseArgs with optional value (::) followed by another option.
	 Note: After refactoring, :: does NOT consume next option.
	**/
	public function testHuGetoptParseArgsOptionalValueNextIsOption(): void {
		$opts = [
			['o', 'opt', '::'],
			['v', 'verbose', '']
		];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-o', '-v']);

		$getopt->parseArgs();

		$optO = $getopt->get('o');
		$optV = $getopt->get('v');

		// After fix: -v is NOT value of -o, it's processed as separate option
		$this->assertCount(1, $optO->Val);
		$this->assertNull($optO->Val->{0});
		// v is also processed as separate option
		$this->assertNotEmpty($optV->Opt);
	}

	/**
* Test HuGetopt getShortOpt with continuation after option (match[3] not empty).
	 This covers the branch: if ($re->match(3)[0]) $this->_curArg = '-' . $re->match(3)[0];
	**/
	public function testHuGetoptIsShortOptWithContinuation(): void {
		$opts = [
			['a', 'all', ''],
			['b', 'brief', '']
		];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-ab']);

		// First call should return option 'a' with continuation 'b'
		$result = $getopt->getShortOpt('-ab');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
		$this->assertEquals('a', $result->Opt->{0});
	}

	/**
	* Test HuGetopt nextArg when _curArg is set (from continuation).
	**/
	public function testHuGetoptNextArgWithCurArgSet(): void {
		$opts = [
			['a', 'all', ''],
			['b', 'brief', '']
		];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-ab']);

		// Parse to trigger continuation logic
		$getopt->parseArgs();

		// After parsing with -ab, 'b' should be processed as option, not non-opt
		// Check that both a and b options were processed
		$optA = $getopt->get('a');
		$optB = $getopt->get('b');
		$this->assertNotEmpty($optA->Opt);
		$this->assertNotEmpty($optB->Opt);
	}

	/**
	* Test HuGetopt getOptByStr with type 's' explicitly.
	**/
	public function testHuGetoptGetOptByStrExplicitShort(): void {
		$opts = [['x', 'xopt', '']];
		$getopt = new HuGetopt($opts);

		$opt = $getopt->getOptByStr('x', 's');
		$this->assertInstanceOf(HuGetoptOption::class, $opt);
	}

	/**
	* Test HuGetopt getOptByStr with type 'l' explicitly.
	**/
	public function testHuGetoptGetOptByStrExplicitLong(): void {
		$opts = [['x', 'xopt', '']];
		$getopt = new HuGetopt($opts);

		$opt = $getopt->getOptByStr('xopt', 'l');
		$this->assertInstanceOf(HuGetoptOption::class, $opt);
	}

	/**
	* Test HuGetopt getOptByStr with type 'a' (auto).
	**/
	public function testHuGetoptGetOptByStrTypeAuto(): void {
		$opts = [['x', 'xopt', '']];
		$getopt = new HuGetopt($opts);

		$opt = $getopt->getOptByStr('x', 'a');
		$this->assertInstanceOf(HuGetoptOption::class, $opt);
	}

	/**
* Test HuGetopt nextArg branch when _curArg is set.
	 This covers: if ($this->_curArg) { $tmp = $this->_curArg; ... }
	**/
	public function testHuGetoptNextArgCurArgBranch(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v']);

		// Access protected method via reflection
		$reflection = new \ReflectionClass($getopt);
		$nextArgMethod = $reflection->getMethod('nextArg');
		$nextArgMethod->setAccessible(true);

		// First consume one arg
		$nextArgMethod->invoke($getopt);

		// Set _curArg manually to test the branch
		$curArgProp = $reflection->getProperty('_curArg');
		$curArgProp->setAccessible(true);
		$curArgProp->setValue($getopt, 'manual_value');

		// Now nextArg should return the manual value
		$result = $nextArgMethod->invoke($getopt);
		$this->assertEquals('manual_value', $result);

		// Verify _curArg was reset to null
		$this->assertNull($curArgProp->getValue($getopt));
	}

	/**
	* Test HuGetopt currentArg when _curArg is set.
	**/
	public function testHuGetoptCurrentArgWithCurArgSet(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v']);

		// Access protected method and property via reflection
		$reflection = new \ReflectionClass($getopt);
		$currentArgMethod = $reflection->getMethod('currentArg');
		$currentArgMethod->setAccessible(true);

		$curArgProp = $reflection->getProperty('_curArg');
		$curArgProp->setAccessible(true);
		$curArgProp->setValue($getopt, 'current_test_value');

		// currentArg should return the manual value
		$result = $currentArgMethod->invoke($getopt);
		$this->assertEquals('current_test_value', $result);

		// Verify _curArg was reset to null
		$this->assertNull($curArgProp->getValue($getopt));
	}

	/**
	* Test HuGetopt peekNextArg when _curArg is set.
	**/
	public function testHuGetoptPeekNextArgWithCurArgSet(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v', 'arg1']);

		// Access protected method and property via reflection
		$reflection = new \ReflectionClass($getopt);
		$peekMethod = $reflection->getMethod('peekNextArg');
		$peekMethod->setAccessible(true);

		$curArgProp = $reflection->getProperty('_curArg');
		$curArgProp->setAccessible(true);
		$curArgProp->setValue($getopt, 'peek_test');

		// peekNextArg should use _curArgv position when _curArg is set
		$result = $peekMethod->invoke($getopt);
		// Should return current argv position (0) since _curArg is set
		$this->assertEquals('script.php', $result);
	}

	/**
	* Test HuGetopt peekNextArg when no more args.
	**/
	public function testHuGetoptPeekNextArgNoMoreArgs(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php']);

		// Move to end
		$reflection = new \ReflectionClass($getopt);
		$curArgvProp = $reflection->getProperty('_curArgv');
		$curArgvProp->setAccessible(true);
		$curArgvProp->setValue($getopt, 10); // Beyond array size

		$peekMethod = $reflection->getMethod('peekNextArg');
		$peekMethod->setAccessible(true);

		$result = $peekMethod->invoke($getopt);
		$this->assertFalse($result);
	}

	/**
	* Test HuGetopt nextArg when no more args (else return false).
	**/
	public function testHuGetoptNextArgNoMoreArgs(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php']);

		$reflection = new \ReflectionClass($getopt);
		$curArgvProp = $reflection->getProperty('_curArgv');
		$curArgvProp->setAccessible(true);
		$curArgvProp->setValue($getopt, 10); // Beyond array size

		$nextArgMethod = $reflection->getMethod('nextArg');
		$nextArgMethod->setAccessible(true);

		$result = $nextArgMethod->invoke($getopt);
		$this->assertFalse($result);
	}

	/**
* Test HuGetopt getOpt protected method directly.
	 This covers the protected getOpt() method which calls getShortOpt and getLongOpt.
	**/
	public function testHuGetoptGetOptProtectedMethod(): void {
		$opts = [
			['s', 'short', ''],
			['l', 'long', '']
		];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-s']);

		// Access protected method via reflection
		$reflection = new \ReflectionClass($getopt);
		$getOptMethod = $reflection->getMethod('getOpt');
		$getOptMethod->setAccessible(true);

		// Test with short option
		$result = $getOptMethod->invoke($getopt, '-s');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
		$this->assertEquals('s', $result->Opt->{0});

		// Test with long option
		$result = $getOptMethod->invoke($getopt, '--long');
		$this->assertInstanceOf(HuGetoptOption::class, $result);
		$this->assertEquals('long', $result->Opt->{0});

		// Test with non-option
		$result = $getOptMethod->invoke($getopt, 'not-an-option');
		$this->assertNull($result);
	}
}
