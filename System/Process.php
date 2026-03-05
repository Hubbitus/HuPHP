<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System;

use Hubbitus\HuPHP\Exceptions\ProcessException;

/**
* Manipulate processes on *NIX-like systems.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* Base idea got from http://www.php.net/manual/ru/function.proc-open.php
**/
class Process {
    public const int STDIN = 0;
    public const int STDOUT = 1;
    public const int STDERR = 2;

    /** @var array<int, array<int, string>> Process descriptor specification */
    private array $descriptorSpec = [
        self::STDIN  => ['pipe', 'r'],
        self::STDOUT => ['pipe', 'w'],
        self::STDERR => ['pipe', 'w']
    ];

    /** @var resource|null Process resource */
    private $resource = null;

    /** @var array<int, resource>|null Process pipes */
    private ?array $pipes = null;

    /** @var ProcessState Process state object */
    private ProcessState $state;

    /**
    * Constructor
    *
    * @param ProcessState $state Process state object
    * @param bool $doNOTopen If true, don't open process automatically
    **/
    public function __construct(ProcessState $state, bool $doNOTopen = false) {
        $this->setState($state);
        if ($this->state->CMD and !$doNOTopen) {
            $this->open();
        }
    }

    /**
    * Get process state
    *
    * @return ProcessState Process state object
    **/
    public function &getState(): ProcessState {
        return $this->state;
    }

    /**
    * Set process state.
    *
    * @param ProcessState $state Process state object
    * @return void
    **/
    public function setState(ProcessState $state): void {
        $this->state = $state;
    }

    /**
    * Open process.
    *
    * @return void
    * @throws ProcessException If process cannot be opened
    **/
    public function open(): void {
        $this->resource = \proc_open(
            $this->state->CMD,
            $this->descriptorSpec,
            $this->pipes,
            $this->state->getCwd(),
            $this->state->getEnv()
        );

        if (!\is_resource($this->resource)) {
            throw new ProcessException('Can\'t open process!' . $this->state->describe(), 0, $this->getState());
        }
    }

    /**
    * Set non-blocking mode for process pipes.
    *
    * @param bool $nonBlock Enable non-blocking mode
    * @param int $nonBlockTimeout Timeout in microseconds
    * @return void
    **/
    public function setNonBlockingMode(bool $nonBlock = true, int $nonBlockTimeout = 500000): void {
        $this->state->nonBlockingMode = $nonBlock;
        $this->state->nonBlockTimeout = $nonBlockTimeout;
        if ($this->state->nonBlockingMode) {
            \stream_set_blocking($this->pipes[self::STDIN], false);
            \stream_set_blocking($this->pipes[self::STDOUT], false);
            \stream_set_blocking($this->pipes[self::STDERR], false);
        }
    }

    /**
    * Write data to process stdin.
    *
    * @param mixed $inStr Input string or false to use saved data
    * @param bool $noWait If true, don't wait after writing
    * @return void
    **/
    public function writeIn(mixed $inStr = false, bool $noWait = false): void {
        // By default saved data write
        if ($inStr !== false) {
            $this->state->writeData = $inStr;
        }
        if ($this->state->writeData !== null) {
            \fwrite($this->pipes[self::STDIN], (string) $this->state->writeData);
        }
        \fflush($this->pipes[self::STDIN]);
        if (!$this->state->nonBlockingMode) {
            \fclose($this->pipes[self::STDIN]);
        } elseif ($this->state->nonBlockingMode && !$noWait) {
            \usleep($this->state->nonBlockTimeout);
        }
    }

    /**
    * Read data from process stdout.
    *
    * @return void
    **/
    public function readOut(): void {
        $this->state->retVal = \stream_get_contents($this->pipes[self::STDOUT]);
        \fflush($this->pipes[self::STDOUT]);
        if (!$this->state->nonBlockingMode) {
            \fclose($this->pipes[self::STDOUT]);
        }
    }

    /**
    * Read data from process stderr.
    *
    * @return void
    **/
    public function readErr(): void {
        $this->state->error = \stream_get_contents($this->pipes[self::STDERR]);
        if (!$this->state->nonBlockingMode) {
            \fclose($this->pipes[self::STDERR]);
        }
    }

    /**
    * Close all process pipes and wait for completion.
    *
    * @return void
    * @throws ProcessException If process ended with non-zero exit code
    **/
    public function closeAll(): void {
        if ($this->state->nonBlockingMode) {
            @\fclose($this->pipes[self::STDIN]);
            @\fclose($this->pipes[self::STDOUT]);
            @\fclose($this->pipes[self::STDERR]);
        }
        $this->state->exit_code = \proc_close($this->resource);
        if ($this->state->exit_code !== 0) {
            throw new ProcessException(
                'Ended with non 0 status! - ' . $this->state->exit_code . "\n" . $this->state->describe(),
                0,
                $this->getState()
            );
        }
    }

    /**
    * Execute process and return result.
    *
    * @return mixed Process result
    * @throws ProcessException If process failed
    **/
    public function execute(): mixed {
        $this->readErr();
        $this->readOut();
        $this->closeAll();
        $error = $this->state->getError();
        if ($error !== '') {
            throw new ProcessException($error . $this->state->describe(), 0, $this->getState());
        }
        return $this->state->getResult();
    }

    /**
    * Static method to execute a command.
    *
    * @param ProcessState|string $command Command string or ProcessState object
    * @param string|null $cwd Working directory
    * @param array<string, string>|null $env Environment variables
    * @param mixed $writeData Data to write to stdin
    * @return mixed Command output
    **/
    public static function exec(
        ProcessState|string $command,
        ?string $cwd = null,
        ?array $env = null,
        mixed $writeData = null
    ): mixed {
        if (!$command instanceof ProcessState) {
            $state = new ProcessState();
            $state->CMD = $command;
            if ($cwd !== null) {
                $state->setCwd($cwd);
            }
            if ($env !== null) {
                $state->setEnv($env);
            }
            if ($writeData !== null) {
                $state->writeData = $writeData;
            }
        } else {
            $state = $command;
        }

        $process = new Process($state);
        $process->writeIn();
        return $process->execute();
    }
}
