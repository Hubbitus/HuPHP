<?
/**
* VariableStream stream wrapper. Manipulate stream 'var://varName' as file,
* where content is $varName.
*
* @package Vars
* @version 1.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-06-16 14:33
**/

/**
* Very usefull example from http://www.php.net/manual/ru/function.stream-wrapper-register.php as base for implementation
**/
class VariableStream {
	var $position;
	var $varname;
  
	function stream_open($path, $mode, $options, &$opened_path){
		$url = parse_url($path);
		$this->varname = $url["host"];
		$this->position = 0;
	   
		return true;
	}

	function stream_read($count){
		$ret = substr($GLOBALS[$this->varname], $this->position, $count);
		$this->position += strlen($ret);
		return $ret;
	}

	function stream_write($data){
		$left = substr($GLOBALS[$this->varname], 0, $this->position);
		$right = substr($GLOBALS[$this->varname], $this->position + strlen($data));
		$GLOBALS[$this->varname] = $left . $data . $right;
		$this->position += strlen($data);
		return strlen($data);
	}

	function stream_tell(){
		return $this->position;
	}

	function stream_eof(){
		return $this->position >= strlen($GLOBALS[$this->varname]);
	}

	/**
	* This is STUB, only for warnings supress
	**/
	function stream_stat(){
		return array();
	}

	function stream_seek($offset, $whence){
		switch ($whence) {
			case SEEK_SET:
				if ($offset < strlen($GLOBALS[$this->varname]) && $offset >= 0) {
					 $this->position = $offset;
					 return true;
				} else {
					 return false;
				}
				break;
			   
			case SEEK_CUR:
				if ($offset >= 0) {
					 $this->position += $offset;
					 return true;
				} else {
					 return false;
				}
				break;
			   
			case SEEK_END:
				if (strlen($GLOBALS[$this->varname]) + $offset >= 0) {
					 $this->position = strlen($GLOBALS[$this->varname]) + $offset;
					 return true;
				} else {
					 return false;
				}
				break;
			   
			default:
				return false;
		}
	}
}

stream_wrapper_register("var", "VariableStream")
	or die("Failed to register protocol");

/*
@example

$myvar = "";
   
$fp = fopen("var://myvar", "r+");

fwrite($fp, "line1\n");
fwrite($fp, "line2\n");
fwrite($fp, "line3\n");

rewind($fp);
while (!feof($fp)) {
	echo fgets($fp);
}
fclose($fp);
var_dump($myvar);
*/
?>