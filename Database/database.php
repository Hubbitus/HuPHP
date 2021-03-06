<?
/**
* Database abstraction layer.
* Documented AFTER creation, in progress.
*
* @package Database
* @version 2.1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-31 16:31 v 2.0b to 2.1
*
* @uses settings
* @uses get_settings
**/

class DB_settings extends settings {

	// Deafults
	protected $__SETS = array(
	/*
		'hostname'	=> 'localhost',
		'username'	=> 'root',
		'password'	=> '',
		'dbName'		=> 'grub',
		'persistent'	=> true,
		'charset'		=> 'CP1251' // TO fire SET NAMES... {@see ::set_names()}
	*/
		'CHARSET_RECODE' => array (
			'FROM'	=> 'CP1251',	// Script charset
			'TO'		=> 'UTF-8'	// DB charset
		),
		'DEBUG'		=> false,

		/** In SUBarray in order not to generate extra Entity */
		'DBError_settings' => array(
		// Here may be overwritten defaults settings. {@see HuLOG_text_settings}
		)
	);
}#c

abstract class database extends get_settings{
	protected $_sets /* DB_settings */ = null;

	protected $db_link = null;

	protected	$Query = '';	//SQL-query
	protected	$result;		//result link
	protected	$RES;		//result Set
	public	$Field=null;	//Last field from call sql_fetch_field
	public	$Fields=null;	//Fields from call sql_fetch_fields
	protected	/* DBError */ $Error = null;

	protected	$rowsTotal;

	/**
	* Constructor.
	* @param Object(DB_settings)|array	$sets	Initial settings.
	*/
	public function __construct( /* DB_settings | array */ $sets, $dontConnect = false){
		if (is_array($sets)) $this->_sets = new DB_settings((array)$sets);
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new DB_settings();//Default

		$this->Error = new DBError(new DBError_settings($this->settings->DBError_settings));

		if (!$dontConnect){
			$this->db_connect();
			$this->db_select();
			$this->set_names();
		}
	}#__c

	public function db_select(){
		if (!call_user_func($this->db_type.'_select_db', $this->settings->dbName))
		throw new DBSelectErrorDBException($this->Error->settings->TXT_noDBselected, $this);
	}#m db_select

	/**
	 * Fire query "SET NAMES $charset".
	 * By default if $charset is null and set $this->settings->CHARSET_RECODE->TO it used,
	 *	or if defined $this->settings->charset otherwise do nothing.
	 *
	 * @param	string(null)	$charset
	 * @return	nothing
	 */
	public function set_names($charset = null){
		if ( ($ch = $charset) or ($ch = @$this->settings->CHARSET_RECODE['TO']) or ($ch = $this->settings->charset) ){
			$this->query('SET NAMES ' . $ch);
		}
	}#m set_names

	/**
	* Переопределяем, чтобы сделать ссылку на настройки не изменяемой!
	*	таким образом настройки менять можно будет, а сменить объект настроек - нет
	**/
	function &__get ($name){
		switch ($name){
			case 'settings': return $this->_sets;

			case 'RES': return $this->RES;

			case 'sql_fields':
				if (!$this->Fields) return $this->sql_fetch_fields();
			return $this->Fields;
			break;
		}
	}#m __get

	public function &sql_num_fields(){
		return call_user_func($this->db_type.'_num_fields', $this->result);
	}#m sql_num_fields

	public function &sql_fetch_field($offset = null){
		if ($offset) $this->Field = call_user_func($this->db_type.'_fetch_field', $this->result, $offset);
		else $this->Field = call_user_func($this->db_type.'_fetch_field', $this->result);
		return $this->Field;
	}#m sql_fetch_field

	public function &sql_fetch_fields(){
		while ($this->Fields[] = $this->sql_fetch_field()){}
		return $this->Fields;
	}#m sql_fetch_fields

	/**
	* Добиваемся некоторой "универсальности"
	**/
	public function &sql_fetch_assoc(){
	$this->RES = @call_user_func($this->db_type.'_fetch_assoc', $this->result);

	/**
	* Перекодировка, если надо
	**/
	$this->iconv_result();
		/*
		* Это только чтобы можно было проверять успешность операции, в условии!
		* НЕ для использования самой переменной
		**/
		return $this->RES;
	}#m sql_fetch_assoc

	public function &sql_fetch_row(){
		$this->RES = @call_user_func($this->db_type.'_fetch_row', $this->result);

		$this->iconv_result();
		return $this->RES;
	}#m sql_fetch_row

	public function &sql_fetch_array(){
		$this->RES = @call_user_func($this->db_type.'_fetch_array', $this->result);

		$this->iconv_result();
		return $this->RES;
	}#m sql_fetch_array

	public function &sql_fetch_object($className = 'stdClass', array $params = array()){
		// See http://ru2.php.net/mysql_fetch_object comments by "Chris at r3i dot it"
		if ($params) $this->RES = @call_user_func($this->db_type.'_fetch_object', $this->result, $className, $params);
		else $this->RES = @call_user_func($this->db_type.'_fetch_object', $this->result, $className);

		$this->iconv_result();
		return $this->RES;
	}#m sql_fetch_object

	public function sql_free_result(){
		call_user_func($this->db_type.'_free_result', $this->result);
		return true;
	}#m sql_free_result

	public function sql_num_rows(){
		return call_user_func($this->db_type.'_num_rows', $this->result);
	}#m sql_num_rows

	/**
	* Returns TOTAL rows for query, whithout LIMIT effect
	**/
	public function rowsTotal(){
		return $this->rowsTotal;
	}#m rowsTotal

	abstract public function db_connect();
	abstract public function query($query, $print_query = false, $last_id = false);
	abstract public function query_limit($query, $from, $amount, $print_query = false);
	abstract public function ToBlob($str);
	abstract public function sql_next_result();
	abstract public function sql_escape_string(&$string_to_escape);

	/////////////////////////Private-Protected HELPERS///////////////////////////

	/**
	* Перекодировка РЕЗУЛЬТАТА, тоесть ИЗ БД
	**/
	protected function iconv_result(){
		if (@$this->settings->CHARSET_RECODE and $this->RES){
			if (is_array($this->RES)){
				foreach ($this->RES as $key => $value){
					$this->RES[$key] = @iconv($this->settings->CHARSET_RECODE['TO'], $this->settings->CHARSET_RECODE['FROM'], $value);
				}
			}
			else{//Object
				foreach ($this->RES as $key => $value){
					$this->RES->$key = @iconv($this->settings->CHARSET_RECODE['TO'], $this->settings->CHARSET_RECODE['FROM'], $value);
				}
			}
		}
	}#m iconv_result

	/**
	* Перекодировка ЗАПРОСА, тоесть В БД
	**/
	protected function iconv_query(){
		if (@$this->settings->CHARSET_RECODE){
			$this->Query = iconv($this->settings->CHARSET_RECODE['FROM'], $this->settings->CHARSET_RECODE['TO'], $this->Query);
		}
	}#m iconv_query

	abstract protected function collectDebugInfo($errNo, $server_message, $server_messageS = '', $d_backtrace);

	/**
	* @return Object(DBError)
	*/
	public function getError(){
		return $this->Error;
	}#m getError

	/**
	* Return last (current) SQL string
	* @return
	*/
	public function getSQL(){
		return $this->Query;
	}#m getSQL

	public function __wakeup(){
		$this->db_connect();
	}#m __wakeup
}#c database
?>