<?php
declare(strict_types=1);

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

abstract class HuClass {
	/**
	* This is just wrapper for system construction 'clone'. Objects in PHP implicitly returned by
	*	reference (see http://www.php.net/manual/en/language.references.spot.php#59820), so, to use it
	*	without modification of main object I should clone it, but it is break one line construction chain.
	* F.e. I want only count such items:
	*	$dellin->getNotMatchedCities()->filter(create_function('$v', 'return is_null($v->region);'))->count()
	* it implicitly MODIFY $dellin object! Off course if it is not return clone of object itself, what is not
	*	a common case. So, we want do something similar:
	* (clone $dellin->getNotMatchedCities())->filter(create_function('$v', 'return is_null($v->region);'))->count()
	* But PHP does not allow such construction and fire there parsing error.
	* For this case the method intended. In our example it would:
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
	* PHP hasn't any normal possibilities to cast objects into derived class (reinterpret_cast analog). We need hack to do it.
	* See http://ru2.php.net/mysql_fetch_object comments by "Chris at r3i dot it"
	* So, in this page, below, i found next fine workaround (see comment and example of "trithaithus at tibiahumor dot net")
	*
	* Also this hack was be founded here http://blog.adaniels.nl/articles/a-dark-corner-of-php-class-casting/
	*
	* @param $toClassName string Class name to what casting do
	* @param $what mixed
	* @return object($toClassName)
	**/
	public static function reinterpret_cast($toClassName, $what): object {
		return unserialize(
			preg_replace(
				'/^O:[0-9]+:"[^"]+":/',
				'O:'.strlen($toClassName).':"' . $toClassName . '":',
				serialize($what)
			)
		);
	}#m cast
}#c
