<?

class database_operators{
	/** Unary operators **/
	static $operators1 = array(
		'BINARY',
		'COLLATE',
		'~',
		'-' // Change the sign of the argument
	);

	/** Binary operators **/
	static $operators2 = array(
		'>>',
		'*',
		'-' /*Minus operator */,
		'RLIKE',
		'SOUNDS LIKE',
		'&',
		'|',
		'^',
		'DIV',
		'/',
		'<=>',
		'=',
		'>=',
		'>',
		'IS NOT NULL',
		'IS NOT',
		'IS NULL',
		'IS',
		'<<',
		'<=',
		'<',
		'LIKE',
		'!=, <>',
		'NOT LIKE',
		'NOT REGEXP',
		'NOT, !',
		'%',
		'+',
		'REGEXP',
	);

	/* Ternary operators */
	static $operators3 = array(
		'BETWEEN',
		'NOT BETWEEN'	// '! BETWEEN' is incorrect!
	);

	/** Logical operators **/
	static $operatorsLogical = array(
		'AND', '&&',
		'XOR',
		'||', 'OR'
	);

	/** control-flow operators **/
	static $operatorsFlow = array(
		'CASE',
	);
}#c
?>