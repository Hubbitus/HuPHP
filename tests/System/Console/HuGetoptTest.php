<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\System\Console;

use PHPUnit\Framework\TestCase;
use Hubbitus\HuPHP\System\Console\HuGetopt;
use Hubbitus\HuPHP\System\Console\HuGetoptSettings;
use Hubbitus\HuPHP\System\Console\HuGetoptArgumentRequiredException;

/**
* @covers \Hubbitus\HuPHP\System\Console\HuGetopt
*/
class HuGetoptTest extends TestCase {
	private HuGetopt $getopt;

	public function testConstructor(): void {
		$opts = [
			['v', 'verbose', ''],
			['f', 'file', ':'],
		];
		$getopt = new HuGetopt($opts);

		$this->assertInstanceOf(HuGetopt::class, $getopt);
	}

	public function testConstructorWithSettings(): void {
		$opts = [['h', 'help', '']];
		$settings = new HuGetoptSettings();
		$getopt = new HuGetopt($opts, $settings);

		$this->assertInstanceOf(HuGetopt::class, $getopt);
	}

	public function testSetOpts(): void {
		$opts = [
			['a', 'all', ''],
			['b', 'brief', ':'],
		];
		$getopt = new HuGetopt($opts);
		$result = $getopt->setOpts($opts);

		$this->assertSame($getopt, $result);
	}

	public function testGetOptByStrShort(): void {
		$opts = [['s', 'short', '']];
		$getopt = new HuGetopt($opts);

		$opt = $getopt->getOptByStr('s', 's');

		$this->assertInstanceOf('Hubbitus\HuPHP\System\Console\HuGetoptOption', $opt);
	}

	public function testGetOptByStrLong(): void {
		$opts = [['l', 'long', '']];
		$getopt = new HuGetopt($opts);

		$opt = $getopt->getOptByStr('long', 'l');

		$this->assertInstanceOf('Hubbitus\HuPHP\System\Console\HuGetoptOption', $opt);
	}

	public function testGetOptByStrAutoDetectShort(): void {
		$opts = [['x', 'xopt', '']];
		$getopt = new HuGetopt($opts);

		$opt = $getopt->getOptByStr('x');

		$this->assertInstanceOf('Hubbitus\HuPHP\System\Console\HuGetoptOption', $opt);
	}

	public function testGetOptByStrAutoDetectLong(): void {
		$opts = [['y', 'yopt', '']];
		$getopt = new HuGetopt($opts);

		$opt = $getopt->getOptByStr('yopt');

		$this->assertInstanceOf('Hubbitus\HuPHP\System\Console\HuGetoptOption', $opt);
	}

	public function testSetArgv(): void {
		$opts = [['t', 'test', '']];
		$getopt = new HuGetopt($opts);
		$argv = ['script.php', '-t', 'arg1'];

		$result = $getopt->setArgv($argv);

		$this->assertSame($getopt, $result);
	}

	public function testGetAlias(): void {
		$opts = [['g', 'getopt', '']];
		$getopt = new HuGetopt($opts);

		$opt = $getopt->get('g', 's');

		$this->assertInstanceOf('Hubbitus\HuPHP\System\Console\HuGetoptOption', $opt);
	}

	public function testGetListShortOpts(): void {
		$opts = [
			['a', 'alpha', ''],
			['b', 'beta', ''],
			['c', 'gamma', ''],
		];
		$getopt = new HuGetopt($opts);

		$shortOpts = $getopt->getListShortOpts();

		$this->assertIsArray($shortOpts);
		$this->assertArrayHasKey('a', $shortOpts);
		$this->assertArrayHasKey('b', $shortOpts);
		$this->assertArrayHasKey('c', $shortOpts);
	}

	public function testGetListLongOpts(): void {
		$opts = [
			['x', 'xlong', ''],
			['y', 'ylong', ''],
		];
		$getopt = new HuGetopt($opts);

		$longOpts = $getopt->getListLongOpts();

		$this->assertIsArray($longOpts);
		$this->assertArrayHasKey('xlong', $longOpts);
		$this->assertArrayHasKey('ylong', $longOpts);
	}

	public function testReadPHPArgv(): void {
		$opts = [['r', 'read', '']];
		$getopt = new HuGetopt($opts);

		$result = $getopt->readPHPArgv();

		$this->assertSame($getopt, $result);
	}

	public function testGetNonOpts(): void {
		$opts = [['n', 'nonopt', '']];
		$getopt = new HuGetopt($opts);

		$nonOpts = $getopt->getNonOpts();

		$this->assertInstanceOf('Hubbitus\HuPHP\Vars\HuArray', $nonOpts);
	}

	public function testGetNonOptsWithFromIndex(): void {
		$opts = [['i', 'index', '']];
		$getopt = new HuGetopt($opts);

		$nonOpts = $getopt->getNonOpts(1);

		$this->assertInstanceOf('Hubbitus\HuPHP\Vars\HuArray', $nonOpts);
	}

	public function testGetShortOpt(): void {
		$opts = [['s', 'short', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-s']);

		$result = $getopt->getShortOpt('-s');

		$this->assertInstanceOf('Hubbitus\HuPHP\System\Console\HuGetoptOption', $result);
	}

	public function testGetShortOptWithSequence(): void {
		$opts = [
			['a', 'all', ''],
			['b', 'brief', ''],
			['c', 'count', ''],
		];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-abc']);

		$result = $getopt->getShortOpt('-abc');

		$this->assertInstanceOf('Hubbitus\HuPHP\System\Console\HuGetoptOption', $result);
	}

	public function testGetShortOptNotOption(): void {
		$opts = [['x', 'xopt', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', 'not-an-option']);

		$result = $getopt->getShortOpt('not-an-option');

		$this->assertNull($result);
	}

	public function testGetLongOpt(): void {
		$opts = [['l', 'long', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '--long']);

		$result = $getopt->getLongOpt('--long');

		$this->assertInstanceOf('Hubbitus\HuPHP\System\Console\HuGetoptOption', $result);
	}

	public function testGetLongOptWithValue(): void {
		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '--file=test.txt']);

		$result = $getopt->getLongOpt('--file=test.txt');

		$this->assertInstanceOf('Hubbitus\HuPHP\System\Console\HuGetoptOption', $result);
	}

	public function testGetLongOptNotOption(): void {
		$opts = [['y', 'yopt', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', 'not-long-option']);

		$result = $getopt->getLongOpt('not-long-option');

		$this->assertNull($result);
	}

	public function testParseArgsSimple(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-v']);

		$getopt->parseArgs();

		$opt = $getopt->get('v');
		$this->assertNotNull($opt);
	}

	public function testParseArgsWithValue(): void {
		$opts = [['f', 'file', ':']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-f', 'test.txt']);

		$getopt->parseArgs();

		$opt = $getopt->get('f');
		$this->assertNotNull($opt);
	}

	public function testParseArgsLongOption(): void {
		$opts = [['h', 'help', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '--help']);

		$getopt->parseArgs();

		$opt = $getopt->get('help', 'l');
		$this->assertNotNull($opt);
	}

	public function testParseArgsNonOptionArguments(): void {
		$opts = [['o', 'option', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', 'file1.txt', 'file2.txt']);

		$getopt->parseArgs();

		$nonOpts = $getopt->getNonOpts(1);
		$this->assertGreaterThan(0, $nonOpts->count());
	}

	public function testSettingsAccess(): void {
		$opts = [['t', 'test', '']];
		$getopt = new HuGetopt($opts);

		$settings = $getopt->sets();

		$this->assertInstanceOf(HuGetoptSettings::class, $settings);
	}

	public function testSettingsDefaultValues(): void {
		$opts = [['d', 'default', '']];
		$getopt = new HuGetopt($opts);

		$this->assertEquals(['-'], $getopt->sets()->start_short);
		$this->assertEquals(['--'], $getopt->sets()->start_long);
	}

	public function testParseArgsWithDoubleDash(): void {
		$opts = [['v', 'verbose', '']];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '--', '-v', 'file.txt']);

		$getopt->parseArgs();

		$nonOpts = $getopt->getNonOpts(1);
		$this->assertGreaterThan(0, $nonOpts->count());
	}

	public function testMultipleOptionsParsing(): void {
		$opts = [
			['a', 'all', ''],
			['b', 'brief', ''],
			['c', 'count', ''],
		];
		$getopt = new HuGetopt($opts);
		$getopt->setArgv(['script.php', '-a', '-b', '-c']);

		$getopt->parseArgs();

		$this->assertNotNull($getopt->get('a'));
		$this->assertNotNull($getopt->get('b'));
		$this->assertNotNull($getopt->get('c'));
	}
}
