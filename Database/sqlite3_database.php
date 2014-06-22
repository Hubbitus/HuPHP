<?
/**
* Database abstraction layer.
* Driver for SQLite2 and SQLite3 database server
*
* @package Database
* @version 0.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-12-21 18:40
*
* @uses database
**/

class sqlite3_database_settings extends DB_settings{

}#c sqlite3_database_settings

/**
* Implementation over PDO_sqlite3
**/
class sqlite3_database extends database{
	public $db_type = 'sqlite3';

/* Only parent, nothing more
	function __construct(
		$sets = null	// sqlite3_database_settings or array
		,$dontConnect = false ){
	parent::__construct($sets, $dontConnect);
	}#c
*/

	public function db_connect(){
		if (!is_resource($this->db_link)){//Establish connection
			try{
				$this->db_link = new PDO('sqlite:' . $this->settings->db_file);
				$this->db_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch (PDOException $e){
				$this->Query = '[' . $this->db_type .' connect' . ']';
					if ($this->settings->DEBUG)
						$this->collectDebugInfo(
							$e->getCode(),
							$e->getMessage(),
							'',
							debug_backtrace()
						);

					// It often called from constructor. So, object is not istantiated to future cal to it getError()
					$cedbe = new ConnectErrorDBException ($this->Error->settings->TXT_cantConnect, $this);
					$cedbe->DBError =& $this->Error;
					throw $cedbe;
			}
		}
	}#m db_connect

	/**
	* For sqlite in file only one database may be placed and select do not implemented
	**/
	public function db_select(){
	}#m db_select

	public function query($query, $print_query = false, $last_id = false){
		$this->Fields = null;
		$this->Query = $query;

		$this->iconv_query();

		if ($print_query) echo $query.'<br>';
		try{
			$this->result = $this->db_link->query($query);
		}
		catch (PDOException $e){
			if ($this->settings->DEBUG){
				$this->collectDebugInfo(
					$e->getCode(),
					$e->getMessage(),
					'',
					debug_backtrace()
				);
			}
			throw new QueryFailedDBException($this->Error->settings->TXT_queryFailed, $this);
		}

			// If requested return Last_insert_id (in case of INSERT),
		if ($last_id){ // or just resource
			$this->result = $this->db_link->lastInsertId();
		}

		// For backward capability only. Return deprecated
		return $this->result;
	}//m query

	public function query_limit($query, $from, $amount, $print_query = false){
		if (!empty($from) or ! empty($amount)) $query .= ' LIMIT '.(int)$from.','.(int)$amount;
		return $this->query($query, $print_query);
	}#m query_limit

	/**
	* In sqlite3 does not needed
	**/
	public function ToBlob($str){
		return $str;
	}#m ToBlob

	public function sql_next_result(){
		return $this->db_link->nextRowset();
	}

	public function sql_escape_string(&$string_to_escape){
		return $this->db_link->quote($string_to_escape);
	}#m sql_escape_string

	/**
	* TOTAL rows for query, whithout LIMIT effect
	* In case sqlite3 to work it properly and DO NOT slow all, insert
	* SQL_CALC_FOUND_ROWS keyword after SELECT is fully on user!!!
	* Instead, whithout it, result will be wrong, and equal ->sql_num_rows();
	**/
	public function rowsTotal(){
		$this->query('SELECT FOUND_ROWS()');
		return current($this->sql_fetch_row());
	}#m rowsTotal

	protected function collectDebugInfo($errNo, $server_message, $server_messageS = '', $d_backtrace){
		$this->Error->clear();
		$this->Error->mergeSettingsArray(
			array(
				'TXT_queryFailed'	=> $this->Error->settings->TXT_queryFailed,
				'errNo'			=> $errNo,
				'server_message'	=> $server_message,
				'server_messageS'	=> $server_messageS,
				'Query' 			=> $this->Query,
				'call_from_file'	=> @$d_backtrace[1]['file'],
				'call_from_line'	=> @$d_backtrace[1]['line'],
				'bt'				=> new backtrace($d_backtrace, 0)
			)
		);
	}

	public function &sql_fetch_field($offset = 0){
		return $this->result->getColumnMeta($offset);
	}#m sql_fetch_field

	public function &sql_fetch_assoc(){
		return $this->sql_fetch_array(PDO::FETCH_ASSOC);
	}#m sql_fetch_assoc

	public function &sql_fetch_row(){
		return $this->sql_fetch_array(PDO::FETCH_NUM);
	}#m sql_fetch_row

	/**
	* http://ru2.php.net/manual/en/pdostatement.fetch.php
	*
	* @param	integer=PDO::FETCH_BOTH	$fetch_style
	* @param	integer=PDO::FETCH_ORI_NEXT	$cursor_orientation
	* @param	integer=0	$cursor_offset
	**/
	public function &sql_fetch_array($fetch_style = PDO::FETCH_BOTH, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0){
		$this->RES = $this->result->fetch($fetch_style, $cursor_orientation, $cursor_offset);
		$this->iconv_result();
		return $this->RES;
	}#m sql_fetch_array

	public function &sql_fetch_object($className = 'stdClass', array $params = array()){
		$this->RES = $this->result->fetchObject($className, $params);
		$this->iconv_result();
		return $this->RES;
	}#m sql_fetch_object

	public function sql_free_result(){
		$this->result->closeCursor();
		$this->result = null;
		return true;
	}#m sql_free_result

	final public function sql_num_rows(){
		return $this->result->rowCount();
	}#m sql_num_rows
}#c sqlite3_database
?>