<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System;

use Hubbitus\HuPHP\Exceptions\ProcessException;

/**
* Manipulate processes on *NIX-like systems.
*
* @package Process
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* Base idea got from http://www.php.net/manual/ru/function.proc-open.php
*
* @uses ProcessException
**/

class Process {
	const STDIN = 0;
	const STDOUT = 1;
	const STDERR = 2;

	private $descriptorSpec = array(
		0 => array('pipe', 'r'),
		1 => array('pipe', 'w'),
		2 => array('pipe', 'w')
	);

	private $resource = null;
	private $pipes;

	private $state;

	function __construct(ProcessState $state, $doNOTopen = false){
		$this->setState($state);
		if ($this->state->CMD and !$doNOTopen ) $this->open();
	}
	public function &getState(){
		return $this->state;
	}
	public function setState($state){
		$this->state = $state;
	}
	public function open(){
		$this->resource = proc_open($this->state->CMD, $this->descriptorSpec, $this->pipes, $this->state->getCwd(), $this->state->getEnv());

		if (!is_resource($this->resource)){
			throw new ProcessException ('Can\'t open process!'.$this->state->describe(), 0, $this->getState());
		}
	}

	public function setNonBlockingMode($nonBlock = true, $nonBlockTimeout = 500000){
		$this->state->nonBlockingMode = $nonBlock;
		$this->state->nonBlockTimeout = $nonBlockTimeout;
		if ($this->state->nonBlockingMode){
			stream_set_blocking($this->pipes[self::STDIN], false);
			stream_set_blocking($this->pipes[self::STDOUT], false);
			stream_set_blocking($this->pipes[self::STDERR], false);
		}
	}
	public function writeIn($inStr = false, $noWait = false){
		// By default saved data write
		if ($inStr) $this->state->writeData = $inStr;
		if ($this->state->writeData !== null) {
			fwrite($this->pipes[self::STDIN], (string)$this->state->writeData);
		}
		fflush($this->pipes[self::STDIN]);
		if (! $this->state->nonBlockingMode) fclose($this->pipes[self::STDIN]);
		elseif ($this->state->nonBlockingMode and ! $noWait) usleep ($this->state->nonBlockTimeout);
	}
	public function readOut(){
		$this->state->retVal = stream_get_contents($this->pipes[self::STDOUT]);
		fflush($this->pipes[self::STDOUT]);
		if (! $this->state->nonBlockingMode) fclose($this->pipes[self::STDOUT]);
	}
	public function readErr(){
		$this->state->error = stream_get_contents($this->pipes[self::STDERR]);
		if (! $this->state->nonBlockingMode) fclose($this->pipes[self::STDERR]);
	}
	public function closeAll(){
		if ($this->state->nonBlockingMode){
			@fclose($this->pipes[self::STDIN]);
			@fclose($this->pipes[self::STDOUT]);
			@fclose($this->pipes[self::STDERR]);
		}
		$this->state->exit_code = proc_close($this->resource);
		if ($this->state->exit_code) throw new ProcessException('Ended with non 0 status! - '.$this->state->exit_code."\n".$this->state->describe(), 0, $this->getState());
	}
	public function execute(){
		$this->readErr();
		$this->readOut();
		$this->closeAll();
		if ($this->state->getError()) throw new ProcessException($this->state->getError().$this->state->describe(), 0, $this->getState());
		return $this->state->getResult();
	}
	public static function exec($command, $cwd = null, array $env = null, $writeData = null){
		if (! $command instanceof ProcessState){
			$state = new ProcessState();
			$state->CMD = $command;
			if ($cwd) $state->setCwd($cwd);
			if ($env) $state->setEnv($env);
			if ($writeData) $state->writeData = $writeData;
		}
		else{
			$state = $command;
		}

		$process = new Process($state);
		$process->writeIn();
		return $process->execute();
	}
/*
function __destruct(){
	if ($this->pipes[self::STDIN]) fclose($this->pipes[self::STDIN]);
	if ($this->pipes[self::STDOUT]) fclose($this->pipes[self::STDOUT]);
	if ($this->pipes[self::STDERR]) fclose($this->pipes[self::STDERR]);
}#__d
*/
}
/*
EXAMPLES
try{
//Standalone Usage
$process = new Process('enca');
$process->writeIn(file_get_contents('t1'));
$process->readOut();
$process->closeAll();
c_dump($process->getResult());
//\standalone

//Non Blocking mode of descriptors. Allow execute more than one command!
$process = new Process('bash');
$process->setNonBlockingMode(true, 50000);
$process->writeIn("ls -1\n");
$process->readErr(); c_dump($process->getError());
$process->readOut(); c_dump($process->getResult());

$process->writeIn("date\n");
$process->readErr(); c_dump($process->getError());
$process->readOut(); c_dump($process->getResult());
$process->closeAll();
//\non blocking

//Simple usage
$process = new Process('df -h');
echo $process->execute();
//\simple

//Static call
echo Process::exec('w');
//\static
}
catch (Exception $e){
    echo 'Exception: '.$e->getMessage() . "\n";
    // there was a problem executing the command
}
*/
