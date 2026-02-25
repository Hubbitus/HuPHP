<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System;

/**
* Output type enumeration.
* Enum for output types used throughout the framework.
* Replaces legacy OS::OUT_TYPE_* constants.
**/
enum OutputType {
    case WEB; // Was: 'BROWSER'
    case CONSOLE;
    case PRINT;
    case FILE;
    case WAP;
}
