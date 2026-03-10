<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\System\Console;

use Hubbitus\HuPHP\System\Console\HuGetopt;
use Hubbitus\HuPHP\System\Console\HuGetoptSettings;
use PHPUnit\Framework\TestCase;

/**
* Integration tests for HuGetopt class.
*
* @covers \Hubbitus\HuPHP\System\Console\HuGetopt
**/
class HuGetoptNewTest extends TestCase {
    public function testHuGetoptClassExists(): void {
        $this->assertTrue(class_exists(HuGetopt::class));
    }

    public function testConstructorWithValidSettings(): void {
        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        $this->assertInstanceOf(HuGetopt::class, $getopt);
    }

    public function testSetArgv(): void {
        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        $argv = ['script.php', '-v', '-f', 'test.txt'];
        $result = $getopt->setArgv($argv);

        $this->assertSame($getopt, $result);
    }

    public function testGetListShortOpts(): void {
        $settings = new HuGetoptSettings();
        $opts = [
            ['a', 'all', ''],
            ['b', 'brief', ':'],
        ];
        $getopt = new HuGetopt($opts, $settings);

        $shortOpts = $getopt->getListShortOpts();

        $this->assertIsArray($shortOpts);
        $this->assertArrayHasKey('a', $shortOpts);
        $this->assertArrayHasKey('b', $shortOpts);
    }

    public function testGetListLongOpts(): void {
        $settings = new HuGetoptSettings();
        $opts = [
            ['a', 'all', ''],
            ['b', 'brief', ':'],
        ];
        $getopt = new HuGetopt($opts, $settings);

        $longOpts = $getopt->getListLongOpts();

        $this->assertIsArray($longOpts);
        $this->assertArrayHasKey('all', $longOpts);
        $this->assertArrayHasKey('brief', $longOpts);
    }

    public function testGetNonOptsReturnsHuArray(): void {
        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        $nonOpts = $getopt->getNonOpts();

        $this->assertInstanceOf(\Hubbitus\HuPHP\Vars\HuArray::class, $nonOpts);
    }

    public function testSetOptsReturnsReference(): void {
        $settings = new HuGetoptSettings();
        $opts = [
            ['a', 'all', ''],
            ['b', 'brief', ':'],
        ];
        $getopt = new HuGetopt($opts, $settings);

        $result = $getopt->setOpts($opts);

        $this->assertSame($getopt, $result);
    }

    public function testGetMethodExists(): void {
        $this->assertTrue(method_exists(HuGetopt::class, 'get'));
    }

    public function testGetOptByStrMethodExists(): void {
        $this->assertTrue(method_exists(HuGetopt::class, 'getOptByStr'));
    }

    public function testParseArgsMethodExists(): void {
        $this->assertTrue(method_exists(HuGetopt::class, 'parseArgs'));
    }

    public function testReadPHPArgv(): void {
        global $argv;
        $originalArgv = $argv ?? null;

        $argv = ['script.php', '-v'];

        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $result = $getopt->readPHPArgv();

        $this->assertSame($getopt, $result);

        // Restore original argv
        if ($originalArgv !== null) {
            $argv = $originalArgv;
        } else {
            unset($argv);
        }
    }

    public function testGetOptByStrReturnsHuGetoptOption(): void {
        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        $opt = $getopt->getOptByStr('v');

        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $opt
        );
    }

    public function testGetAliasForGetOptByStr(): void {
        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        $opt = $getopt->get('v');

        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $opt
        );
    }

    public function testGetOptByStrWithAutoDetectShort(): void {
        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        // Auto-detect by length (1 char = short) - covers default case
        $opt = $getopt->getOptByStr('v');

        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $opt
        );
    }

    public function testGetOptByStrWithAutoDetectLong(): void {
        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        // Auto-detect by length (>1 char = long) - covers default case
        $opt = $getopt->getOptByStr('verbose');

        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $opt
        );
    }

    public function testGetOptByStrWithShortType(): void {
        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        // Explicit short type - covers case 's'
        $opt = $getopt->getOptByStr('v', 's');

        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $opt
        );
    }

    public function testGetOptByStrWithLongType(): void {
        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        // Explicit long type - covers case 'l'
        $opt = $getopt->getOptByStr('verbose', 'l');

        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $opt
        );
    }

    public function testGetShortOpt(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-v'];
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        $result = $getopt->getShortOpt('-v');
        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $result
        );
    }

    public function testGetShortOptWithArgument(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-f', 'test.txt'];
        $opts = [['f', 'file', ':']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        $result = $getopt->getShortOpt('-f');
        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $result
        );
    }

    public function testGetShortOptNotOption(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', 'notanoption'];
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        $result = $getopt->getShortOpt('notanoption');
        $this->assertNull($result);
    }

    public function testGetLongOpt(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '--verbose'];
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        $result = $getopt->getLongOpt('--verbose');
        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $result
        );
    }

    public function testGetLongOptWithEquals(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '--file=test.txt'];
        $opts = [['f', 'file', ':']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        $result = $getopt->getLongOpt('--file=test.txt');
        $this->assertInstanceOf(
            \Hubbitus\HuPHP\System\Console\HuGetoptOption::class,
            $result
        );
    }

    public function testGetLongOptNotOption(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', 'notanoption'];
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        $result = $getopt->getLongOpt('notanoption');
        $this->assertNull($result);
    }



    public function testParseArgsWithShortOption(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-v'];
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $opt = $getopt->get('v');
        $this->assertNotEmpty($opt->Opt);
    }

    public function testParseArgsWithLongOption(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '--verbose'];
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $opt = $getopt->get('verbose', 'l');
        $this->assertNotEmpty($opt->Opt);
    }

    public function testParseArgsWithOptionAndValue(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-f', 'test.txt'];
        $opts = [['f', 'file', ':']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $opt = $getopt->get('f');
        $this->assertNotEmpty($opt->Val);
    }

    public function testParseArgsWithNonOptionArguments(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', 'file1.txt', 'file2.txt'];
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $nonOpts = $getopt->getNonOpts(1);
        $this->assertCount(2, $nonOpts);
    }

    public function testParseArgsWithMultipleShortOptions(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-v', '-h'];
        $opts = [
            ['v', 'verbose', ''],
            ['h', 'help', ''],
        ];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $optV = $getopt->get('v');
        $optH = $getopt->get('h');

        $this->assertNotEmpty($optV->Opt);
        $this->assertNotEmpty($optH->Opt);
    }

    public function testParseArgsWithLongOptionEqualsForm(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '--file=test.txt'];
        $opts = [['f', 'file', ':']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $opt = $getopt->get('file', 'l');
        // Check first element of Val array (push adds to array)
        $valArray = $opt->Val->getArray();
        $this->assertEquals('test.txt', $valArray[0]);
    }

    public function testParseArgsWithMixedOptions(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-v', '--file', 'test.txt', 'arg1'];
        $opts = [
            ['v', 'verbose', ''],
            ['f', 'file', ':'],
        ];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $optV = $getopt->get('v');
        $optF = $getopt->get('file', 'l');
        $nonOpts = $getopt->getNonOpts(1);

        $this->assertNotEmpty($optV->Opt);
        $this->assertNotEmpty($optF->Val);
        $this->assertCount(1, $nonOpts);
    }

    public function testParseArgsWithClueShortOptions(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-vh'];
        $opts = [
            ['v', 'verbose', ''],
            ['h', 'help', ''],
        ];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $optV = $getopt->get('v');
        $optH = $getopt->get('h');

        $this->assertNotEmpty($optV->Opt);
        $this->assertNotEmpty($optH->Opt);
    }

    public function testParseArgsWithDoubleDash(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-v', '--', 'file1', 'file2'];
        $opts = [
            ['v', 'verbose', ''],
            ['f', 'file', ':'],
        ];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $nonOpts = $getopt->getNonOpts(1);
        $this->assertCount(3, $nonOpts);
    }

    public function testParseArgsWithOptionalArgumentPresent(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-f', 'value'];
        $opts = [['f', 'file', '::']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $opt = $getopt->get('f');
        // Check first element of Val array (push adds to array)
        $valArray = $opt->Val->getArray();
        $this->assertEquals('value', $valArray[0]);
    }

    public function testParseArgsWithOptionalArgumentMissing(): void {
        // :: (optional) - no argument is OK, just leave default value
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-f'];
        $opts = [['f', 'file', '::']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        // :: mod without argument should NOT throw exception (it's optional!)
        $getopt->parseArgs();

        $opt = $getopt->get('f');
        $valArray = $opt->Val->getArray();
        // Should have one element with empty string (default)
        $this->assertCount(1, $valArray);
        $this->assertEquals('', $valArray[0]);
    }

    public function testParseArgsWithClueShortOptionsWithArgument(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-vf', 'test.txt'];
        $opts = [
            ['v', 'verbose', ''],
            ['f', 'file', ':'],
        ];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $optV = $getopt->get('v');
        $optF = $getopt->get('f');

        $this->assertNotEmpty($optV->Opt);
        $this->assertNotEmpty($optF->Val);
    }

    public function testParseArgsWithMultipleSameOptions(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-v', '-v', '-v'];
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $opt = $getopt->get('v');
        $this->assertGreaterThan(0, $opt->Opt->count());
    }

    public function testParseArgsWithLongOptionAlternative(): void {
        $settings = new HuGetoptSettings();
        $settings->alternative = true;
        $argv = ['script.php', '-verbose'];
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $opt = $getopt->get('verbose', 'l');
        $this->assertNotEmpty($opt->Opt);
    }

    public function testParseArgsWithAllPaths(): void {
        $settings = new HuGetoptSettings();
        $argv = ['script.php', 'nonopt', '-v', '-f', 'val', '--long', 'lval', '--', 'end'];
        $opts = [
            ['v', 'verbose', ''],
            ['f', 'file', ':'],
            ['l', 'long', ':'],
        ];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        @$getopt->parseArgs();

        $optV = $getopt->get('v');
        $optF = $getopt->get('f');
        $optL = $getopt->get('long', 'l');
        $nonOpts = $getopt->getNonOpts(1);

        $this->assertTrue($optV->Val->_last_);
        // Check first element of Val array (push adds to array)
        $valArrayF = $optF->Val->getArray();
        $this->assertEquals('val', $valArrayF[0]);
        $valArrayL = $optL->Val->getArray();
        $this->assertEquals('lval', $valArrayL[0]);
        $this->assertGreaterThan(0, $nonOpts->count());
    }

    public function testParseArgsWithDoubleColonModRequiresArgumentException(): void {
        // : (required) - no argument should throw exception
        $this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException::class);
        $this->expectExceptionMessage('requires argument');

        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-f'];
        $opts = [['f', 'file', ':']];  // : not :: - this one REQUIRES argument
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        // : mod without argument should throw exception
        @$getopt->parseArgs();
    }

    public function testParseArgsWithDoubleColonModNextIsOption(): void {
        // Note: After refactoring, :: mod with next arg being an option does NOT consume it.
        // The -v is left as a separate option to be processed.
        $settings = new HuGetoptSettings();
        $argv = ['script.php', '-f', '-v'];
        $opts = [
            ['f', 'file', '::'],
            ['v', 'verbose', ''],
        ];
        $getopt = new HuGetopt($opts, $settings);
        $getopt->setArgv($argv);

        // :: mod with next arg being an option - leaves default (null)
        $getopt->parseArgs();

        $optF = $getopt->get('f');
        $optV = $getopt->get('v');

        // After fix: -v is NOT value of -f, it's processed as separate option
        $valArrayF = $optF->Val->getArray();
        $this->assertEquals([null], $valArrayF);
        // v is also processed as separate option
        $this->assertNotEmpty($optV->Opt);
    }



    public function testReadPHPArgvFromServerArgv(): void {
        global $argv;
        $originalArgv = $argv ?? null;
        $originalServerArgv = $_SERVER['argv'] ?? null;

        // Remove global argv, keep only $_SERVER['argv']
        $argv = null;
        $_SERVER['argv'] = ['script.php', '-v'];

        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $result = $getopt->readPHPArgv();

        $this->assertSame($getopt, $result);

        // Restore
        if ($originalArgv !== null) {
            $argv = $originalArgv;
        }
        if ($originalServerArgv !== null) {
            $_SERVER['argv'] = $originalServerArgv;
        } else {
            unset($_SERVER['argv']);
        }
    }

    public function testReadPHPArgvFromHTTPServerVars(): void {
        global $argv;
        $originalArgv = $argv ?? null;
        $originalServerArgv = $_SERVER['argv'] ?? null;
        $originalHttpVars = $GLOBALS['HTTP_SERVER_VARS'] ?? null;

        // Remove all except HTTP_SERVER_VARS
        $argv = null;
        unset($_SERVER['argv']);
        $GLOBALS['HTTP_SERVER_VARS']['argv'] = ['script.php', '-v'];

        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);
        $result = $getopt->readPHPArgv();

        $this->assertSame($getopt, $result);

        // Restore
        if ($originalArgv !== null) {
            $argv = $originalArgv;
        }
        if ($originalServerArgv !== null) {
            $_SERVER['argv'] = $originalServerArgv;
        }
        if ($originalHttpVars !== null) {
            $GLOBALS['HTTP_SERVER_VARS'] = $originalHttpVars;
        } else {
            unset($GLOBALS['HTTP_SERVER_VARS']);
        }
    }

    public function testReadPHPArgvThrowsExceptionWhenNoArgv(): void {
        $this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException::class);

        global $argv;
        $originalArgv = $argv ?? null;
        $originalServerArgv = $_SERVER['argv'] ?? null;
        $originalHttpVars = $GLOBALS['HTTP_SERVER_VARS'] ?? null;

        // Remove all argv sources
        $argv = null;
        unset($_SERVER['argv'], $GLOBALS['HTTP_SERVER_VARS']);

        $settings = new HuGetoptSettings();
        $opts = [['v', 'verbose', '']];
        $getopt = new HuGetopt($opts, $settings);

        try {
            $getopt->readPHPArgv();
        } finally {
            // Restore
            if ($originalArgv !== null) {
                $argv = $originalArgv;
            }
            if ($originalServerArgv !== null) {
                $_SERVER['argv'] = $originalServerArgv;
            }
            if ($originalHttpVars !== null) {
                $GLOBALS['HTTP_SERVER_VARS'] = $originalHttpVars;
            }
        }
    }
}
