<?
if (!class_exists('SplDoublyLinkedList')){
/**
* This is Uglu hack for backward capability. We use SplDoublyLinkedList, but it is not present on PHP
* before 5.3.0. So, for our Iterator purpose we implement it MINIMAL.
*
* Implementation got from example: http://php.net/Iterator
*
* Deprecated for any other use than backward capability!
*
* @deprecated Since creation
**/
class SplDoublyLinkedList implements Iterator{
private $var = array();

	public function __construct($array = array ()){
	$this->var = $array;
	}

	public function rewind(){
//	echo "rewinding\n";
	reset($this->var);
	}

	public function current(){
	$var = current($this->var);
//	echo "current: $var\n";
	return $var;
	}

	public function key(){
	$var = key($this->var);
//	echo "key: $var\n";
	return $var;
	}

	public function next(){
	$var = next($this->var);
//	echo "next: $var\n";
	return $var;
	}

	public function valid(){
	$var = $this->current() !== false;
//	echo "valid: {$var}\n";
	return $var;
	}

	public function push($item){
	$this->var[] = $item;
	}

	public function count(){
	return count($this->var);
	}
}#c SplDoublyLinkedList
}
?>