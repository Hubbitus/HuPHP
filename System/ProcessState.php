<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System;
use Hubbitus\HuPHP\Debug\Dump;

/* Aka struct of data */
class ProcessState {
	/*All this members was private on Process class, so, now will be public,
	if we do not provides complex of get/set method to each */
	public $writeData;

	public $exit_code;

	private $cwd = null;
	private $env = array();

	public $nonBlockingMode = false;
	public $nonBlockTimeout = 500000;// microseconds

	public $retVal;
	public $error;

	public $CMD;

	public function getCwd(): mixed {
		return $this->cwd;
	}

	public function setCwd($newCwd): void {
		$this->cwd = $newCwd;
	}

	public function getEnv(): array {
		return $this->env;
	}

	public function setEnv(array $env): void {
		$this->env = $env;
	}

	public function getResult(): mixed {
		return $this->retVal;
	}

	public function getError(): string {
		return trim($this->error);
	}

	public function describe(): mixed {
		return Dump::log(
			[
				'writeData'	=> $this->writeData,
				'retVal'	=> $this->getResult(),
				'error'		=> $this->getError(),
				'exit_code'	=> $this->exit_code,
				'cwd'		=> $this->getCwd(),
				'env'		=> trim(Dump::log($this->getEnv())),
				'nonBlockingMode'	=> $this->nonBlockingMode,
				'nonBlockTimeout'	=> $this->nonBlockTimeout
			]
		);
	}
}
