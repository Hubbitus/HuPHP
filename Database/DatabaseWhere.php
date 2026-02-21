<?php
declare(strict_types=1);

/**
* Database abstraction layer.
* Documented AFTER creation, in progress.
*
* @package Database
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ???
*
* @uses EMPTY_STR()
* @uses NON_EMPTY_STR()
* @uses database_operators
**/

namespace Hubbitus\HuPHP\Database;

use function Hubbitus\HuPHP\Macroses\EMPTY_STR;
use function Hubbitus\HuPHP\Macroses\NON_EMPTY_STR;

class DatabaseWhere {
    /** @var array<mixed> */
    private array $_whereArr = [];
    private ?string $_whereStr = '';

    private string $_l = '';
    private string $_r = '';
    private string $_quote = "'";
    private string $_logic = 'and';

    public const string default_operator = '=';

    /**
    * $l, $r may be used as:
    *  $l = '[', $r = ']' for MSSQL Server
    *  $l = '`', $r = '`' for MySQL Server
    *  By default not needed.
    *
    * In case $l provided, but $r - not - $r assuming equals $l
    *
    * @param array<mixed> $where
    * @param string $l
    * @param string $r
    * @param string $quote
    **/
    public function __construct(array $where = [], string $l = '', string $r = '', string $quote = "'") {
        $this->setArray($where, $l, $r, $quote);
    }

    /**
    * @param array<mixed> $where
    * @param string $l
    * @param string $r
    * @param string $quote
    **/
    public function setArray(array $where, string $l = '', string $r = '', string $quote = "'"): void {
        $this->_whereArr = $where;
        $this->_l = $l;
        $this->_r = EMPTY_STR($r, $l);
        $this->_quote = $quote;
        $this->_whereStr = ''; // Will be filled on request.
    }

    /**
    * Add where conditions in end
    *
    * @param array<mixed>|string $what What append
    * @return $this
    **/
    public function &add(array|string $what): static {
        $this->_whereArr[] = $what;
        $this->_whereStr = null; // recalculate it later
        return $this;
    }

    /**
    * Return array of Where-tokens (from constructed, and may be modified).
    *
    * @return array<mixed>
    **/
    public function getArray(): array {
        return $this->_whereArr;
    }

    /**
    * Append another object to end of conditions.
    *
    * @param DatabaseWhere $whatAppend
    * @return $this
    **/
    public function append(DatabaseWhere $whatAppend): static {
        $this->_whereArr = \array_merge($this->_whereArr, $whatAppend->getArray());
        return $this;
    }

    /**
    * Append another object to end of conditions.
    * Without brackets "()", we may get broken conditions after append (broken permissions f.e.)
    * F.e. we want guarantee, what WHERE must be for Sender = '79052084523'. If provide this, and them, allow
    * append additional conditionals, somebody may break this (intentionally or not!) like:
    * WHERE Sender = '79052084523' OR 1<0
    * But if have brackets:
    * WHERE Sender = '79052084523' AND (1<0)
    *
    * Additionally "AND ()" will produce error of parsing SQL-query.
    * So, use this method, for safe add conditions.
    *
    * @param DatabaseWhere $whatAppend
    * @return $this
    **/
    public function safeAppend(DatabaseWhere $whatAppend): static {
        if ($whatAppend->count()) {
            $this->add('AND'); // So, If explicit given LogicOperator, brackets will be added around!
            $this->append($whatAppend);
        }
        return $this;
    }

    /**
    * Return amount of elements
    *
    * @return int
    **/
    public function count(): int {
        return \count($this->_whereArr);
    }

    /**
    * Return SQL-string, to using in SQL-queries statement
    *
    * @return string
    **/
    public function getSQL(): string {
        if (null === $this->_whereStr || '' === $this->_whereStr) {
            $this->convertToSQL();
        }
        return $this->_whereStr ?? '';
    }

    /**
    * This is main working horse!
    * Handle user-friendly form of parameters. $this->_whereArr is array of elements:
    * $this->_whereArr
    * 1  array('ID' => 1)                              -> ID=1                              // Operator 'self::default_operator' is default. Field is key, value in value.
    * 2  array('ID' => array (2, '<='))                -> ID <= 2                           // As 1, but value - array. Warning - Operator is SECOND argument of second array. [Operator:=]
    * 3  array('ID' => array (2, 'BETWEEN', 15))       -> ID BETWEEN 1 AND 15
    * 4  array('ID', array (2, '<='))                  -> ID <= 2                           // As <2>, but 2 argument - array. Warning - Operator is SECOND argument of second array. [Operator:=]
    * 5  array('ID', '1', 'q:>=')                      -> ID>='1'                           // Operator given explicit, otherwise '='. One dimension array. Arrange: FieldName, FieldValue, [Operator:=]
    * 6  array('ID', '1', 'BETWEEN', 10)               -> ID BETWEEN 1 AND 10              // Special case, ternary operator.
    * 7  (string)""
    *      7.1 If string is operator from database_operators::$operators3 (such as AND, OR, XOR, && etc) - change logic (default is 'and'), and group other in (). F.e.:
    *          $this->_whereArr = array(
    *          array('ID' => 1),
    *          array('ID' => array (2, '<='))),
    *          'or'
    *          array('ID', array (2, '<='))
    *          array('ID', '1', '>=')
    *          )
    *              MUST produce: "(ID=1 and ID <=2) or (ID <= 2 or ID >= 1)"
    *
    *      7.2 Else - append string as normal SQL
    *
    * ADDITIONALLY has second syntax LIKE:
    * $this->_whereArr = array(
    * 8  'ID'        => array(1, '<'),
    * 9  'USER'      => 5,                    // or 'USER'      => array(5)
    * )
    *
    * In both syntax if Operator contains ':' each symbol before mean:
    *  'q (Quote)' - additionally quote FieldValue(s) with self::quote (default:'). F.e.:
    *      array('Name', '[ABC]%', 'q:LIKE')
    *      transformed to:
    *      "Name LIKE '[ABC]%'"
    *  'e' (Escape) - additionally Escape FieldName with $this->_l and $this->_r. F.e _l='[' and _r = ']':
    *      array('Name of field', '[ABC]%', 'q:LIKE')
    **/
    private function convertToSQL(): void {
        // If empty add WHERE keyword
        if (!empty($this->_whereArr)) { // Has at least 1 element
            $this->_whereStr = 'WHERE (';
        } else {
            $this->_whereStr = '';
            return;
        }

        $add_logic_op = false;
        foreach ($this->_whereArr as $key => $item) {
            // Check for second syntax first: 'field' => value or 'field' => [value, operator]
            if (!\is_numeric($key)) { // Second syntax
                // add operator
                if ($add_logic_op) {
                    $this->_whereStr .= NON_EMPTY_STR($this->_logic, ' ', ' ');
                }

                if (\is_array($item)) {
                    // Case <8> or <9>: array value
                    $this->_whereStr .= $this->constructPhrase($key, $item);
                } else {
                    // Simple value: 'field' => value
                    $this->_whereStr .= $this->constructPhrase($key, [$item]); // <9>
                }

                // One added.
                $add_logic_op = true;
            } elseif (\is_string($item) || \is_numeric($item)) { // <7.x>
                if (\is_string($item) && \in_array($logic = \strtoupper(\trim($item)), DatabaseOperators::$operatorsLogical, true)) {
                    $this->_logic = $logic;
                    $this->_whereStr .= ') ' . $this->_logic . ' ('; // <7.1>
                    $add_logic_op = false; // add operator
                } else {
                    $this->_whereStr .= NON_EMPTY_STR($item, ' ', ' '); // <7.2> - AS IS
                }
            } else {
                // add operator
                if ($add_logic_op) {
                    $this->_whereStr .= NON_EMPTY_STR($this->_logic, ' ', ' ');
                }

                if (\is_numeric($key)) { // First syntax
                    /*
                    $item = array('newKey' => array(newValue, operator));
                    OR
                    $item = array('newKey' => newValue);
                    */
                    if (1 === \sizeof($item)) {
                        $item = (array) $item;
                        // PHP 8+ compatible replacement for each()
                        $new_key = \key($item);
                        $new_item = \current($item);
                        $this->_whereStr .= $this->constructPhrase($new_key, (array) $new_item); // <1>,<2>,<3>
                    } else { // Key, value, Operator
                        if (\is_array($item[1])) {
                            $this->_whereStr .= $this->constructPhrase($item[0], $item[1]); // <4>
                        } else {
                            $this->_whereStr .= $this->constructPhrase($item[0], \array_slice($item, 1)); // <5>,<6>
                        }
                    }
                }

                // One added.
                $add_logic_op = true;
            }
        }
        $this->_whereStr .= ')';
    }

    /**
    * Parse user input in convertToString(). There have canonical form:
    * $OperVal is array of Operator and Value(s), like this:
    *  array(-8, 'qe:>=', 90)
    *
    * @param string $fieldName
    * @param array<mixed> $OperVal
    * @return string
    **/
    private function constructPhrase(string $fieldName, array $OperVal): string {
        $ret = '';
        $opt = '';
        if (1 === \sizeof($OperVal)) {
            $op = self::default_operator;
        } else {
            if (\strpos(@$OperVal[1], ':')) {
                // May produce Notice, if single option(s), without operator
                @list($opt, $op) = \explode(':', @$OperVal[1]);
                $op = \strtoupper(EMPTY_STR(\trim($op), self::default_operator));
            } else { // Or Operator, or Empty!
                $op = EMPTY_STR(@$OperVal[1], self::default_operator);
            }
        }

        $ret .= $this->escapeFieldName($fieldName, $opt);
        switch ($op) {
            case 'BETWEEN': // Special case - ternary operator
                $ret .= ' ' . $op . ' ' . $this->quoteFieldValue($OperVal[0], $opt) . ' AND ' . $this->quoteFieldValue($OperVal[2], $opt);
                break;

            default:
                $ret .= ' ' . $op . ' ' . $this->quoteFieldValue($OperVal[0], $opt);
        }
        return $ret;
    }

    /**
    * Escape field name with configured delimiters
    *
    * @param string $fieldName
    * @param string $opt
    * @return string
    **/
    private function escapeFieldName(string &$fieldName, string $opt): string {
        if (\stristr($opt, 'e')) {
            return $this->_l . $fieldName . $this->_r;
        } else {
            return $fieldName;
        }
    }

    /**
    * Quote field value with configured quote character
    *
    * @param mixed $fieldVal
    * @param string $opt
    * @return mixed
    **/
    private function quoteFieldValue(mixed &$fieldVal, string $opt): mixed {
        if (\stristr($opt, 'q')) {
            return $this->_quote . $fieldVal . $this->_quote;
        } else {
            return $fieldVal;
        }
    }
}
