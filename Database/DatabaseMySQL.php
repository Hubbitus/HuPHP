<?php
declare(strict_types=1);

/**
* Database abstraction layer.
* Driver for MySQL database server
*
* @package Database
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-10 07:50 ver 2.0 to 2.0.1
*
* @uses database
**/

class DatabaseSettingsMySQL extends DatabaseSettings {
}
class DatabaseMySQL extends Database{
	public $db_type = 'mysql';

/* Only parent, nothing more
	function __construct(
		$sets = null	// mysql_database_settings or array
		,$dontConnect = false ){
	parent::__construct($sets, $dontConnect);
	}
*/

	public function db_connect(){
		if (!is_resource($this->db_link)){//Establish connection
			if (!($this->db_link = @call_user_func($this->db_type.'_'.($this->settings->persistent ? 'p' : '') .'connect', $this->settings->hostname, $this->settings->username, $this->settings->password))){
				$this->Query = '[' . $this->db_type.'_'.($this->settings->persistent ? 'p' : '') .'connect' . ']';

				if ($this->settings->DEBUG)
					$this->collectDebugInfo(
						mysql_errno(),
						mysql_error(),
						'',
						debug_backtrace()
					);

				// It often called from constructor. So, object is not istantiated to future cal to it getError()
				$cedbe = new DatabaseConnectErrorException($this->Error->settings->TXT_cantConnect, $this);
				$cedbe->DBError =& $this->Error;
				throw $cedbe;
			}
		}
	}
	public function query($query, $print_query = false, $last_id = false){
		$this->Fields = null;
		$this->Query = $query;

		$this->iconv_query();

		if ($print_query) echo $query.'<br>';

		if (!($res = mysql_query($query, $this->db_link))){
			if ($this->settings->DEBUG)
				$this->collectDebugInfo(
					mysql_errno(),
					mysql_error(),
					'',
					debug_backtrace()
				);
			throw new DatabaseQueryFailedException($this->Error->settings->TXT_queryFailed, $this);
		}

		if ($last_id){// или просто ресурс запроса
			$res = mysql_insert_id($this->db_link);
		}

		$this->result = $res;
		// For bakward compatibility only. Return deprecated
		return $res;
	}

	public function query_limit($query, $from, $amount, $print_query = false){
		if (!empty($from) or ! empty($amount)) $query .= ' LIMIT '.(int)$from.','.(int)$amount;
		return $this->query($query, $print_query);
	}
	/**
	* In MySQL not needed - implement as stub
	**/
	public function ToBlob($str){
		return $str;
	}
	/**
	* MySQL does not support multiple recordset. This is possible in mysqli only.
	**/
	public function sql_next_result(){return false;}

	public function sql_escape_string(&$string_to_escape){
		return mysql_escape_string($string_to_escape);
	}
	/**
	* TOTAL rows for query, Without LIMIT effect
	* In case MySQL to work it properly and DO NOT slow all, insert
	* SQL_CALC_FOUND_ROWS keyword after SELECT is fully on user!!!
	* Instead, Without it, result will be wrong, and equal ->sql_num_rows();
	*/
	public function rowsTotal(){
		$this->query('SELECT FOUND_ROWS()');
		return current($this->sql_fetch_row());
	}
	protected function collectDebugInfo($errNo, $server_message, $server_messageS = '', $d_backtrace){
		$this->Error->clear();
		$this->Error->mergeSettingsArray(
			array(
				'TXT_queryFailed' => $this->Error->settings->TXT_queryFailed,
				'errNo'	=> $errNo,
				'server_message'	=> $server_message,
				'server_messageS'	=> $server_messageS,
				'Query' 	=> $this->Query,
				'call_from_file' => @$d_backtrace[1]['file'],
				'call_from_line' => @$d_backtrace[1]['line'],
				'bt' => new Backtrace($d_backtrace, 0)
			)
		);
	}
}
