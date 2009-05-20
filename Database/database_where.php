<?
/*-inc
include_once('Database/database_operators.php');
*/
include_once('macroses/EMPTY_STR.php');
/**
* @uses EMPTY_STR()
* @uses NON_EMPTY_STR()
* @uses database_operators
**/

class database_where{
private $_whereArr = array();
private $_whereStr = '';

private $_l;
private $_r;

private $_quote = "'";

private $_logic = 'and';

const default_operator = '=';

	/**
	* #Hmm, may be need opposite str2arr?? Later.
	* $l, $r may be used as:
	*	$l = '[', $r = ']' for MSSQL Server
	*	$l = '`', $r = '`' for MySQL Server
	*	By default not needed.
	*
	* In case $l provided, but $r - not - $r assuming equals $l
	**/
	public function __construct(array $where = array(), $l = '', $r = '', $quote = "'"){
	$this->setArray($where, $l, $r, $quote);
	}#c

	public function setArray(array $where, $l = '', $r = '', $quote = "'"){
	$this->_whereArr = $where;
	$this->_l = $l;
	$this->_r = EMPTY_STR($r, $l);
	$this->_quote = $quote;
	$this->_whereStr = '';	#Will be filled on request.
	}#m setArray

	/**
	* Add where conditions in end
	*
	* @param array|string	$what What append
	* @return &$this
	**/
	public function &add(/* array|string */$what){
	$this->_whereArr[] = $what;
	$this->_whereStr = null; //recalc it later
	return $this;
	}#m add

	/**
	* Return array of Where-tokens (from constructed, and may be modified).
	*
	* @return array
	**/
	public function getArray(){
	return $this->_whereArr;
	}#m getArray

	/**
	* Append another object to end of conditions.
	*
	* @return &$this
	**/
	public function append(database_where $whatAppend){
	$this->_whereArr = array_merge($this->_whereArr, $whatAppend->getArray());
	return $this;
	}#m append

	/**
	* Append another object to end of conditions.
	* Without brackets "()", we may get broken conditions after append (broken permissions f.e.)
	* F.e. we want garantee, what WHERE must be for Sender = '79052084523'. If provide this, and them, allow
	* append additional conditionals, somebody may break this (intentionally or not!) like:
	* WHERE Sender = '79052084523' OR 1<0
	* But if have brackets:
	* WHERE Sender = '79052084523' AND (1<0)
	*
	* Additionaly "AND ()" will produce error of parsing SQL-query.
	* So, use this method, for safe add conditions.
	*
	* @return &$this
	**/
	public function safeAppend(database_where $whatAppend){
		if ($whatAppend->count()){
		$this->add('AND'); //So, If expliscit given LogicOperator, brackets will be added aroud!
//		$this->add('(');
		$this->append($whatAppend);
//		$this->add(')');
		}
	return $this;
	}#m safeAppend

	/**
	* Return amount of elements
	*
	* @return integer
	**/
	public function count(){
	return count($this->_whereArr);
	}#m count

	/**
	* Return SQL-string, to using in SQL-querys statement
	*
	* @return string
	*/
	public function getSQL(){
		if (!$this->_whereStr) $this->convertToSQL();
	return $this->_whereStr;
	}#m getSQL

##############################################################
	/**
	* @ This is main working horse!
	* Handle user-friendly form of parameters. $this->_whereArr is array of elements:
	* $this->_whereArr
	* 1	array('ID' => 1)					-> ID=1				# Operator 'self::default_operator' is default. Field is key, value in value.
	* 2	array('ID' => array (2, '<='))		-> ID <= 2			# As 1, but value - array. Warning - Operator is SECOND argument of secod array. [Operator:=]
	* 3	array('ID' => array (2, 'BETWEEN', 15))	-> ID BETWEEN 1 AND 15
	* 4	array('ID', array (2, '<='))			-> ID <= 2			# As <2>, but 2 argument - array. Warning - Operator is SECOND argument of secod array. [Operator:=]
	* 5	array('ID', '1', 'q:>=')				-> ID>='1'			# Operator given explicit, owervise '='. One dimension array. Arrange: FieldName, FieldValue, [Operator:=]
	* 6	array('ID', '1', 'BETWEEN', 10)		-> ID BETWEEN 1 AND 10	# Special case, ternary operator.
	* 7	(string)""
	*	7.1 If string is operator from database_operators::$operators3 (such as AND, OR, XOR, && etc) - change logic (default is 'and'), and group other in (). F.e.:
	*		$this->_whereArr = array(
	*		array('ID' => 1),
	*		array('ID' => array (2, '<='))),
	*		'or'
	*		array('ID', array (2, '<='))
	*		array('ID', '1', '>=')
	*		)
	*			MUST produce: "(ID=1 and ID <=2) or (ID <= 2 or ID >= 1)"
	*
	*	7.2 Else - append string as normal SQL
	*
	* ADDITIONALY has second sintax LIKE:
	* $this->_whereArr = array(
	* 8	'ID'		=> array(1, '<'),
	* 9	'USER'	=> 5,			# or 'USER'	=> array(5)
	* )
	*
	* In both sintax if Operator contains ':' each symbol before mean:
	*	'q (Quote)' - additianaly quote FieldVaue(s) with self::quote (default:'). F.e.:
	*		array('Name', '[ABC]%', 'q:LIKE')
	*		transformed to:
	*		"Name LIKE '[ABC]%'"
	*	'e' (Escape) - additionaly Escape FieldName with $this->_l and $this->_r. F.e _l='[' and _r = ']':
	*		array('Name of field', '[ABC]%', 'q:LIKE')
	**/
	private function convertToSQL(){
		#Если не пустое - добавляем само слово WHERE
		if (! empty($this->_whereArr)){#Has at least 1 element
		$this->_whereStr = 'WHERE (';
		}
		else return '';

	$add_logic_op = false;
		foreach ($this->_whereArr as $key => $item){
			if (is_string($item) or is_numeric($item)){#<7.x>
				if (in_array($logic = strtoupper(trim($item)), database_operators::$operatorsLogical)){
				$this->_logic = $logic;
				$this->_whereStr .= ') '.$this->_logic.' (';#<7.1>
				$add_logic_op = false;	#add operator
				}
				else $this->_whereStr .= NON_EMPTY_STR($item, ' ', ' ');#<7.2> - AS IS
			}
			else{
				#add operator
				if ($add_logic_op) $this->_whereStr .= NON_EMPTY_STR($this->_logic, ' ', ' ');

				if (is_numeric($key)){#First sintax
					/*
					$item = array('newKey' => array(newValue, operator));
					OR
					$item = array('newKey' => newValue);
					*/
					if ( 1 == sizeof($item)){
					#Ensure array
					$item = (array)$item;
					list($new_key, $new_item) = each($item);
					$this->_whereStr .= $this->constructPhrase($new_key, (array)$new_item);#<1>,<2>,<3>
					}
					else{#Key, value, Operator
						if ( is_array($item[1]) )
						$this->_whereStr .= $this->constructPhrase($item[0], $item[1]);#<4>
						else
						$this->_whereStr .= $this->constructPhrase($item[0], array_slice($item, 1));#<5>,<6>
					}
				}
				else{#Second sintax
					if ( is_array($item[0]) )
					$this->_whereStr .= $this->constructPhrase($key, $item[0]);#<9>
					else
					$this->_whereStr .= $this->constructPhrase($key, (array)$item);#<8>
				}

			#One added.
			$add_logic_op = true;
			}
		}
	$this->_whereStr .= ')';
	}#m convertToSQL

	/**
	* Parse user input in convertToString(). There have canonical form:
	* $OperVal is array of Operator and Value(s), like this:
	*	array(-8, 'qe:>=', 90)
	* @returns string
	**/
	private function constructPhrase($FieldName, array $OperVal){
	$ret = '';
	$opt = '';
		if (1 == sizeof($OperVal)){
		$op = self::default_operator;
		}
		else{
			if (strpos(@$OperVal[1], ':')){
			#May produce Notice, if single option(s), without operator
			@list ($opt, $op) = explode(':', @$OperVal[1]);
			$op = strtoupper(EMPTY_STR(trim($op), self::default_operator));
			}
			else{//Or Operator, or Empty!
			$op = EMPTY_STR(@$OperVal[1], self::default_operator);
			}
		}

	$ret .= $this->escapeFieldName($FieldName, $opt);
			switch ($op){
			case 'BETWEEN':#Special case - ternary operator
			$ret .= ' '.$op.' '.$this->quoteFieldValue($OperVal[0], $opt).' AND '.$this->quoteFieldValue($OperVal[2], $opt);
			break;

			default:
			$ret .= ' '.$op.' '.$this->quoteFieldValue($OperVal[0], $opt);
			}
	return $ret;
	}#m constructPhrase

	/**
	**/
	private function escapeFieldName(&$fieldName, $opt){
		if (stristr($opt, 'e'))
		return $this->_l.$fieldName.$this->_r;
		else return $fieldName;
	}#m escapeFieldName

	/**
	**/
	private function quoteFieldValue(&$fieldVal, $opt){
		if (stristr($opt, 'q'))
		return $this->_quote.$fieldVal.$this->_quote;
		else return $fieldVal;
	}#m quoteFieldValue
}#c database_where
?>