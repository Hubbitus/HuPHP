<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System\Console;

use Hubbitus\HuPHP\Vars\Settings\Settings;

/**
* @example of use HuGetopt.example.php
**/
class HuGetoptSettings extends Settings {
	protected $__SETS = [
		'start_short'	=> ['-'],
		'start_long'	=> ['--'],
		'alternative'	=> false, /** Allow long options to start with a short_start (‘-’ by default) **/

		/** {@see class HuGetopt_option} to description name and {@see class settings_check} to check option. **/
		'HuGetopt_option_options'	=> [
			'OptL', 'OptS', 'Mod',
			'Opt', 'Sep', 'Val',
			'=', 'OptT'
		]
	];
}
