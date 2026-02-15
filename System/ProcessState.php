<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System;

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

	public $retval;
	public $error;

	public $CMD;

	public function getCwd(){
		return $this->cwd;
	}
	public function setCwd($newCwd){
		$this->cwd = $newCwd;
	}
	public function getEnv(){
		return $this->env;
	}
	public function setEnv(array $env){
		$this->env = $env;
	}
	public function getResult(){
		return $this->retval;
	}
	public function getError(){
		return trim($this->error);
	}
	public function describe(){
		return log_dump(
			array(
				'writeData'	=> $this->writeData,
				'retval'		=> $this->getResult(),
				'error'		=> $this->getError(),
				'exit_code'	=> $this->exit_code,
				'cwd'		=> $this->getCwd(),
				'env'		=> trim(log_dump($this->getEnv())),
				'nonBlockingMode'	=> $this->nonBlockingMode,
				'nonBlockTimeout'	=> $this->nonBlockTimeout
			)
		);
	}
}
