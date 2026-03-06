<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Exceptions;

/**
 * Exception thrown when execution should be halted (alternative to exit()).
 *
 * @package Exceptions
 * @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
 * @copyright Copyright (c) 2026, Pahan-Hubbitus (Pavel Alexeev)
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
