<?php
if (!class_exists('SplDoublyLinkedList')){
/**
* This is Ugly hack for backward compatibility. We use SplDoublyLinkedList, but it is not present on PHP
* before 5.3.0. So, for our Iterator purpose we implement it MINIMALLY.
*
* Implementation got from example: http://php.net/Iterator
*
* Deprecated for any other use than backward compatibility!
*
* @deprecated Since creation
**/
class SplDoublyLinkedList implements Iterator{
	private $var = array();

	public function __construct($array = array ()){
		$this->var = $array;
	}

	public function rewind(){
		reset($this->var);
	}

	public function current(){
		$var = current($this->var);
		return $var;
	}

	public function key(){
		$var = key($this->var);
		return $var;
	}

	public function next(){
		$var = next($this->var);
		return $var;
	}

	public function valid(){
		$var = $this->current() !== false;
		return $var;
	}

	public function push($item){
		$this->var[] = $item;
	}

	public function count(){
		return count($this->var);
	}
}
}
