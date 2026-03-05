<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System\Console;

use Hubbitus\HuPHP\Vars\HuArray;
use Hubbitus\HuPHP\Vars\Settings\SettingsCheck;

/**
 * HuGetopt option class.
 *
 * Structure example:
 * ||| First "Availability"
 * |-> OptL    => 'long'
 * |-> OptS    => 's'
 * |-> Mod     => ':'
 * ||| Then, 'real'. This fields filled only after parse user input. Each array, because more then once may be presents in CL.
 * |-> Opt     => HuArray('s')      // Opt present, parsed from user-input.
 * |-> Sep     => HuArray('-')      // '--'
 * |-> Val     => HuArray('Text')   // Or just true if interesting only present it or not.
 * ||| Misc
 * |-> equals  => HuArray(false)
 * |-> 'OptT'  => HuArray('s', 'l') // OptType
 *
 * @property HuArray $Opt
 * @property HuArray $Sep
 * @property HuArray $Val
 * @property HuArray $OptT
 */
class HuGetoptOption extends SettingsCheck {

    /**
     * Constructor.
     *
     * @param array<string, mixed>|array<int, string> $possibles Array of possible property names (numeric or associative)
     * @param array<string, mixed>|null $array Initial values
     */
    public function __construct(array $possibles, ?array $array = null) {
        // Convert numeric array to associative for SettingsCheck compatibility
        if (array_keys($possibles) === range(0, count($possibles) - 1)) {
            // Convert ['OptL', 'OptS', ...] to ['OptL' => null, 'OptS' => null, ...]
            $possibles = array_fill_keys($possibles, null);
        }

        // Add internal properties to possibles before calling parent
        foreach (['Opt', 'Sep', 'Val', '=', 'OptT'] as $k) {
            if (!isset($possibles[$k])) {
                $possibles[$k] = null;
            }
        }

        // Call parent constructor without array to avoid premature merge
        parent::__construct($possibles, null);

        // Initialize internal properties if not already set
        foreach (['Opt', 'Sep', 'Val', '=', 'OptT'] as $k) {
            if (!array_key_exists($k, $this->__SETS)) {
                $this->setSetting($k, new HuArray());
            }
        }

        // Now merge array data if provided
        if (null !== $array) {
            $this->mergeSettingsArray($array);
        }
    }

    /**
     * Add parsed option in values HuArrays (Opt, Sep, Val, =, OptT)
     *
     * @param HuGetoptOption $toAdd Option to add
     * @return $this
     */
    public function add(HuGetoptOption $toAdd): static {
        foreach (['Opt', 'Sep', 'Val', '=', 'OptT'] as $k) {
            $this->{$k}->pushHuArray($toAdd->{$k});
        }
        return $this;
    }
}
