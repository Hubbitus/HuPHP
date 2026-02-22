<?php
declare(strict_types=1);

/**
* Default log formatter using HuLOGText.
*
* @package Debug
* @subpackage HuLOG
**/

namespace Hubbitus\HuPHP\Debug;

/**
* Default log formatter implementation using HuLOGText.
* Uses the built-in formatting methods of HuLOGText class with settings-based format configuration.
**/
class HuLOGTextFormatter implements IHuLOGFormatter {
    /**
    * Format log text for file output.
    *
    * @param HuLOGText $logText Log text object to format
    * @return string Formatted log message for file output
    **/
    public function formatForFile(HuLOGText $logText): string {
        /** @var array<mixed> $format */
        $format = $logText->getProperty('FORMAT_FILE');
        return $logText->strForFile($format);
    }

    /**
    * Format log text for print output.
    *
    * @param HuLOGText $logText Log text object to format
    * @return string Formatted log message for print output
    **/
    public function formatForPrint(HuLOGText $logText): string {
        /** @var array<mixed> $format */
        $format = $logText->getProperty('FORMAT_CONSOLE');
        return $logText->strForConsole($format);
    }
}
