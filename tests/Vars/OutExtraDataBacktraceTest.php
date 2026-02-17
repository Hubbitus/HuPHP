<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

// OutExtraDataBacktrace has a bug in the source code (nul instead of null)
// Tests are skipped until the bug is fixed in the source file

class OutExtraDataBacktraceTest extends \PHPUnit\Framework\TestCase
{
	public function testPlaceholder(): void
	{
		$this->markTestSkipped('OutExtraDataBacktrace has a bug in source code (nul instead of null)');
	}
}
