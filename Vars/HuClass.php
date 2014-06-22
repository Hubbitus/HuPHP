<?
/**
* Routine tasks to made easy OOP.
*
* @package Vars
* @subpackage Classes
* @version 1.5
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-31 5:31 v 1.0b to 1.0c
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses ClassUnknownException
**/

/**
* To explicit indicate what value not provided, also Null NOT provided too!
**/
class NullClass {}

abstract class HuClass{
	/**
	* To extends most (all) classes.
	* Or to fast copy (with runkit_method_copy) into other classes.
	* Method to allow constructions like: className::create()->methodName() because (new classname())->methodName are NOT allow them!!!
	*
	* @param variable parameters according to class.
	* @return instance of the reguired new class.
	* @Throw(ClassUnknownException)
	**/
	public static function create(){
		// http://blog.felho.hu/what-is-new-in-php-53-part-2-late-static-binding.html
		if (function_exists('get_called_class')) $className = get_called_class(); // Most reliable if available
		else throw new ClassUnknownException('Can\'t determinate class name for who was called ::create() (LSB is not accesible [present start from PHP 5.3.0-dev]). You can use ::createWithoutLSB method or classCREATE() free function with explicit name of needed class!');

		$reflectionObj = new ReflectionClass($className);

		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(func_get_args());
		else return $reflectionObj->newInstance();
	}#m create

	/**
	* This is just wrupper for system construction 'clone'. Objects in PHP implicitly returned by
	*	reference (see http://www.php.net/manual/en/language.references.spot.php#59820), so, to use it
	*	without modification of main object I should clone it, but it is break one line counstruction chain.
	* F.e. I want only count such items:
	*	$dellin->getNotMatchedCities()->filter(create_function('$v', 'return is_null($v->region);'))->count()
	* it implicitly MODIFY $dellin object! Off course if it is not return clone of object itself, what is not
	*	a common case. So, we want do something similar:
	* (clone $dellin->getNotMatchedCities())->filter(create_function('$v', 'return is_null($v->region);'))->count()
	* But PHP does not allow such construction and fire there parsing error.
	* For this case the method intended. In our example it whould:
	* HuClass::clone($dellin->getNotMatchedCities())->filter(create_function('$v', 'return is_null($v->region);'))->count()
	*
	* Some developer notes:
	*	- Unfortunately we can't name it as just clone even in class because it is reserved word.
	*	- I use clone in method because argument itself again implicitly passed as reference, so it is required.
	**/
	public static function cloning($obj){
		return clone $obj;
	}#m cloning

	/**
	* This is similar create, but created for backward capability only.
	* It is UGLY. Do not use it, if you have choice.
	* It is DEPRECATED immediately after creation! But now, realy, it is stil neded :(
	*
	* @deprecated
	* @param	$directClassName = null - The directy provided class name to instantiate.
	* @params	variable parameters according to class.
	* @return	instance of the reguired new class.
	* @Throws(VariableRequiredException)
	**/
	static function createWithoutLSB($directClassName /*, Other Params */){
		include_once('macroses/REQUIRED_VAR.php');
		$reflectionObj = new ReflectionClass(REQUIRED_VAR($directClassName));
		$args = func_get_args();//0 argument - $directClassName
		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(array_slice($args, 1));
		else return $reflectionObj->newInstance();
	}#m createWhithoutLSB

	/**
	* PHP hasn't any normal possibilities to cast objects into derived class (reinterpret_cast analog). We need hack to do it.
	* See http://ru2.php.net/mysql_fetch_object comments by "Chris at r3i dot it"
	* So, in this page, below, i found next fine workaraound (see comment and example of "trithaithus at tibiahumor dot net")
	*
	* Also this hack was be founded here http://blog.adaniels.nl/articles/a-dark-corner-of-php-class-casting/
	*
	* @param $toClassName string Class name to what casting do
	* @param $what mixed
	* @return Object($toClassName)
	**/
	static function cast($toClassName, $what){
		return unserialize(
			preg_replace(
				'/^O:[0-9]+:"[^"]+":/',
				'O:'.strlen($toClassName).':"' . $toClassName . '":',
				serialize($what)
			)
		);
	}#m cast
}#c HuClass

/**
* Free function. For instantiate all objects.
* {@inheritdoc HuClass::createWithoutLSB}
**/
function classCREATE($ClassName /*, Other Params */){
	/*
	* We must use temporary variable due to error:
	* PHP Fatal error:  func_get_args(): Can't be used as a function parameter in /home/_SHARED_/Vars/HuClass.php on line 107
	**/
	$args = func_get_args(); //0 argument - $ClassName
	return call_user_func_array(
		array(
			'HuClass',
			'createWithoutLSB'
		)
		,$args
	);
}#f classCREATE
?>