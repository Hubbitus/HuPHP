<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\System\Console;

use Hubbitus\HuPHP\System\Console\HuGetoptOption;
use Hubbitus\HuPHP\Vars\HuArray;
use Hubbitus\HuPHP\Vars\Settings\SettingsCheck;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\System\Console\HuGetoptOption
**/
class HuGetoptOptionTest extends TestCase {
	public function testConstructor(): void {
		$possibles = ['long' => 'l'];
		$option = new HuGetoptOption($possibles);

		$this->assertInstanceOf(HuGetoptOption::class, $option);
	}

	public function testConstructorInitializesDefaultProperties(): void {
		$possibles = ['long' => 'l'];
		$option = new HuGetoptOption($possibles);

		$this->assertInstanceOf(HuArray::class, $option->Opt);
		$this->assertInstanceOf(HuArray::class, $option->Sep);
		$this->assertInstanceOf(HuArray::class, $option->Val);
		$this->assertInstanceOf(HuArray::class, $option->{'='});
		$this->assertInstanceOf(HuArray::class, $option->OptT);
	}

	public function testConstructorWithArray(): void {
		$possibles = ['OptL' => null, 'OptS' => null];
		$array = ['OptL' => 'long', 'OptS' => 'l'];
		$option = new HuGetoptOption($possibles, $array);

		$this->assertEquals('long', $option->OptL);
		$this->assertEquals('l', $option->OptS);
	}

	public function testConstructorWithExistingProperties(): void {
		$possibles = ['long' => 'l'];
		$array = ['Opt' => new HuArray(['test'])];
		$option = new HuGetoptOption($possibles, $array);

		$this->assertInstanceOf(HuArray::class, $option->Opt);
		$this->assertCount(1, $option->Opt);
	}

	public function testAddMethod(): void {
		$possibles = ['long' => 'l'];
		$option1 = new HuGetoptOption($possibles);
		$option1->Opt->push('l');
		$option1->Sep->push('-');
		$option1->Val->push('value');

		$option2 = new HuGetoptOption($possibles);
		$option2->Opt->push('l');
		$option2->Sep->push('--');
		$option2->Val->push('value2');

		$result = $option1->add($option2);

		$this->assertSame($option1, $result);
		$this->assertCount(2, $option1->Opt);
		$this->assertCount(2, $option1->Sep);
		$this->assertCount(2, $option1->Val);
	}

	public function testAddMethodPreservesAllProperties(): void {
		$possibles = ['long' => 'l'];
		$option1 = new HuGetoptOption($possibles);
		$option1->{'='}->push(false);
		$option1->OptT->push('s');

		$option2 = new HuGetoptOption($possibles);
		$option2->{'='}->push(true);
		$option2->OptT->push('l');

		$option1->add($option2);

		$this->assertCount(2, $option1->{'='});
		$this->assertCount(2, $option1->OptT);
	}

	public function testAddMultipleOptions(): void {
		$possibles = ['long' => 'l'];
		$option1 = new HuGetoptOption($possibles);
		$option2 = new HuGetoptOption($possibles);
		$option3 = new HuGetoptOption($possibles);

		$option1->Opt->push('l');
		$option2->Opt->push('l');
		$option3->Opt->push('l');

		$option1->add($option2)->add($option3);

		$this->assertCount(3, $option1->Opt);
	}

	public function testAddWithEmptyOption(): void {
		$possibles = ['long' => 'l'];
		$option1 = new HuGetoptOption($possibles);
		$option1->Opt->push('l');

		$option2 = new HuGetoptOption($possibles);

		$option1->add($option2);

		$this->assertCount(1, $option1->Opt);
	}

	public function testInheritsFromSettingsCheck(): void {
		$possibles = ['long' => 'l'];
		$option = new HuGetoptOption($possibles);

		$this->assertInstanceOf(SettingsCheck::class, $option);
	}

	public function testPropertyAccess(): void {
		$possibles = ['long' => 'l', 'CustomProp' => null, 'Opt' => null, 'Sep' => null, 'Val' => null, '=' => null, 'OptT' => null];
		$option = new HuGetoptOption($possibles);

		$option->CustomProp = 'test';
		$this->assertEquals('test', $option->CustomProp);
	}

	public function testGetPropertyMethod(): void {
		$possibles = ['long' => 'l', 'Opt' => null, 'Sep' => null, 'Val' => null, '=' => null, 'OptT' => null];
		$option = new HuGetoptOption($possibles, ['Opt' => new HuArray(['test'])]);

		$this->assertInstanceOf(HuArray::class, $option->getProperty('Opt'));
	}

	public function testSetSettingMethod(): void {
		$possibles = ['long' => 'l', 'Opt' => null, 'Sep' => null, 'Val' => null, '=' => null, 'OptT' => null];
		$option = new HuGetoptOption($possibles);

		$option->setSetting('Sep', new HuArray(['--']));
		$this->assertInstanceOf(HuArray::class, $option->Sep);
	}

	public function testMergeSettingsArray(): void {
		$possibles = ['long' => 'l', 'Opt' => null, 'Sep' => null, 'Val' => null, '=' => null, 'OptT' => null];
		$option = new HuGetoptOption($possibles, ['Opt' => new HuArray(['l'])]);

		$option->mergeSettingsArray(['Sep' => new HuArray(['--'])]);

		$this->assertCount(1, $option->Opt);
		$this->assertCount(1, $option->Sep);
	}
}
