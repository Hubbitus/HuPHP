<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

/**
* Interface for formatting log messages.
**/
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
