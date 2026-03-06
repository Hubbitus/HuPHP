<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

/**
* Simple test formatter for unit tests.
*
* @package Debug
**/
class HuLOGSimpleTestFormatter implements IHuLOGFormatter {
    public function formatForFile(HuLOGText $logText): string {
        // Use simple format: just the values without complex formatting
        return $logText->getString(['date', ' ', 'level', 'type', ': ', 'logText', "\n"]);
    }

    public function formatForPrint(HuLOGText $logText): string {
        // Use simple format: just the values without complex formatting
        return $logText->getString(['date', ' ', 'level', 'type', ': ', 'logText', "\n"]);
    }
}
