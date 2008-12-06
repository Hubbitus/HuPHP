<?
/**
* Routine tasks to made easy OOP.
*
* @package Vars
* @subpackage Classes
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-05-31 5:31 v 1.0b to 1.1
*	- Add static method ::createWithoutLSB.
* 2008-06-05 16:00
*	- In function classCREATE provide all aditions arguments to HuClass::createWithoutLSB
**/

include_once('Exceptions/classes.php');

/**
* To explicit indicate what value not provided, also Null NOT provided too!
**/
class NullClass {}

abstract class HuClass{
	/**
	* To extends most (all) classes.
	* Or to fast copy (with runkit_method_copy) into other classes.
	* Method to allow constructions like: className::create()->methodName() because (new classname())->methodName are NOT allow them!!!
	* @param variable parameters according to class.
	* @return instance of the reguired new class.
	**/
	static function create(){
//	$reflectionObj = new ReflectionClass(static::className);
	#http://blog.felho.hu/what-is-new-in-php-53-part-2-late-static-binding.html
	$reflectionObj = new ReflectionClass(get_called_class());

		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(func_get_args());
		else return $reflectionObj->newInstance();
	}#m create

	/**
	* This is similar create, but created for backward capability only.
	* It is UGLY. Do not use ti, if you have choice.
	* It is DEPRECATED immediately after creation! But now, realy, it is stil neded :(
	*
	* @deprecated
	* @param $directClassName = null - The directy provided class name to instantiate.
	*	If not provided, as last chance, try get_called_class, after throw exception 
	* @params variable parameters according to class.
	* @return instance of the reguired new class.
	* @Throw(ClassUnknownException)
	**/
	static function createWithoutLSB($directClassName = null /*, Other Params */){
		if (function_exists('get_called_class')){
		$reflectionObj = new ReflectionClass(get_called_class());
		}
		elseif($directClassName){
		$reflectionObj = new ReflectionClass($directClassName);
		}
		else{
		throw new ClassUnknownException('You not provide ClassName, and Late State Binding (LSB) is not available on your system (present PHP 5.3.0-dev). Do not known what class need be instanciated. Sory! ');
		}

	$args = func_get_args();//0 argument - $directClassName
		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(array_slice($args, 1));
		else return $reflectionObj->newInstance();
	}#m createWhithoutLSB
}#c HuClass

/**
* Free function. For instantiate all objects.
* {@inheritdoc HuClass::createWithoutLSB}
**/
function classCREATE($ClassName = null /*, Other Params */){
$args = func_get_args();//0 argument - $directClassName
return call_user_func_array(
	array(
		'HuClass',
		'createWithoutLSB'
	),
	array_slice($args, 1)
);
}
?>