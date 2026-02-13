<?php
declare(strict_types=1);

class SerializedData{
private $__data = array();

//В классе могут стихийно появляться открытые свойства, используем как контейнер для них
//public $text = 'Какой-то текст';

	function __construct (&$serializedStr = null){
		if ($serializedStr){//Если не задано, то создается контейнер, ничего не надо, просто заполнять его
			if (! ($this->__data = @unserialize($serializedStr)) ){
			throw new SerializeException('Ошибка во время ДЕсериализации объекта');
			}
		}
	}

	function __get($name){
	return $this->__data[$name];
	}
	function __set($name, $val){
	$this->__data[$name] = $val;
	}
	//It is worth noting that before PHP 5.2.0 the __toString  method was only called when it was directly combined with echo() or print().
	function __toString(){
	return serialize($this->__data);
	}
	function toString(){
	return $this->__toString();
	}
}
