<?
include('Debug/debug.php');
include('Debug/MultipleInheritance.php');

//Class names and inheritance
class A{
public static $instance='AaA';
const ClassName='aAa';

	function aa(){
	dump::a('This is method A::aa ('.__CLASS__.'; '.__METHOD__.')');
	}

	function ab(){
	dump::a('This is method A::ab ('.__CLASS__.'; '.__METHOD__.')');
	}
}#c A

class B{
public static $instance='BbB';
const ClassName='bBb';

	function bb(){
	dump::a('This is method B::bb ('.__CLASS__.'; '.__METHOD__.')');
	}

	function ab(){
	dump::a('This is method B::ab ('.__CLASS__.'; '.__METHOD__.')');
	}
}

class C{
	function cc(){
	dump::a('This is method C::cc ('.__CLASS__.'; '.__METHOD__.')');
	}
}

inherits_from(
	'C',
	array('A','B')
);

$var_c = new C;

$var_c->aa();
$var_c->bb();
$var_c->cc();
$var_c->ab(); //A::ab !!

///////////////////////////////////
echo "\n";

#
#class D extends A{// Is NOT worked!!!
#	function dd(){
#	dump::a('This is method D::dd ('.__CLASS__.'; '.__METHOD__.')');
#	}
#}
#inherits_from(
#	'D',
#	array('B')
#);
#

class D extends A{
	function __construct(){
	runkit_method_copy(__CLASS__, 'bb', 'B', 'bb');
	//runkit_method_copy(__CLASS__, 'ab', 'B', 'ab'); //Error: PHP Warning:  runkit_method_copy(): Destination method d::ab() already exists in /var/www/_SHARED_/MultipleInheritance/MultipleInheritance.php on line 81

	runkit_method_copy(__CLASS__, 'cc', 'C', 'cc');
	}
}

$var_d = new D;

$var_d->aa();
$var_d->bb();
$var_d->cc();
$var_d->ab(); //A::ab !!
?>