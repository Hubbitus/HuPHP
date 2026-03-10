<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Exceptions\Variables;

use Hubbitus\HuPHP\Debug\Tokenizer;
use Hubbitus\HuPHP\Debug\Backtrace;

/**
* Exception thrown when a required variable is not provided.
**/
class VariableRequiredException extends VariableException {
    public ?Backtrace $bt = null;
    private ?string $var = null;
    private ?Tokenizer $tok_ = null;

    /**
    * Constructor.
    *
    * @param ?Backtrace $bt Backtrace object
    * @param ?string $varname Variable name that is required
    * @param ?string $message Exception message
    * @param int $code Exception code
    **/
    public function __construct(?Backtrace $bt = null, ?string $varname = null, ?string $message = null, int $code = 0) {
        $this->bt = $bt;
        $this->var = $varname;

        parent::__construct($message ?? '', $code);
    }

    /**
    * Return variable name.
    *
    * @param bool $noTokenize Not try to get parameter if it not provided directly.
    * @return ?string Variable name or null
    **/
    public function varName(bool $noTokenize = false): ?string {
        if ($noTokenize) {
            return $this->var;
        }

        if ($this->var !== null) {
            return $this->var;
        }

        return $this->getTokenizer()->getArg(0);
    }

    /**
    * Get Tokenizer object, suited to backtrace with instantiated exception.
    * Also create object if it does not exist yet.
    *
    * @return Tokenizer Tokenizer instance
    **/
    public function &getTokenizer(): Tokenizer {
        if ($this->tok_ === null) {
            $this->tok_ = Tokenizer::create(
                $this->bt->getNode(0)
            )->parseCallArgs();
        }

        return $this->tok_;
    }
}
