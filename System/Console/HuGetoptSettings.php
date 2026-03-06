<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System\Console;

use Hubbitus\HuPHP\Vars\Settings\Settings;

/**
* HuGetopt settings class.
*
* @property array $HuGetopt_option_options
* @property array $start_short
* @property array $start_long
* @property bool  $alternative
* @example of use HuGetopt.example.php
**/
class HuGetoptSettings extends Settings {
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->initDefaults();
    }

    /**
     * Initialize default settings.
     */
    protected function initDefaults(): void {
        $this->__SETS = [
            'start_short' => ['-'],
            'start_long' => ['--'],
            // Allow long options to start with a short_start ('-' by default)
            'alternative' => false,

            // {@see class HuGetopt_option} to description name and {@see class settings_check} to check option.
            'HuGetopt_option_options' => [
                'OptL', 'OptS', 'Mod',
                'Opt', 'Sep', 'Val',
                '=', 'OptT'
            ]
        ];
    }
}
