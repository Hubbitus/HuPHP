<?php
declare(strict_types=1);

/**
* Interface for formatting log messages.
*
* @package Debug
**/

namespace Hubbitus\HuPHP\Debug;

interface IHuLOGFormatter {
    /**
    * Format log message for file output
    *
    * @param HuLOGText $logText
    * @return string
    **/
    public function formatForFile(HuLOGText $logText): string;

    /**
    * Format log message for print output
    *
    * @param HuLOGText $logText
    * @return string
    **/
    public function formatForPrint(HuLOGText $logText): string;
}
