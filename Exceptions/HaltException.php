<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Exceptions;

/**
* Exception thrown when execution should be halted (alternative to exit()).
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
*/
class HaltException extends \Exception {
    /** @var int Exit code */
    public int $exitCode;

    /**
     * @param string $message Error message
     * @param int $exitCode Exit code (default 0)
     */
    public function __construct(string $message = '', int $exitCode = 0) {
        $this->exitCode = $exitCode;
        parent::__construct($message, $exitCode);
    }
}
