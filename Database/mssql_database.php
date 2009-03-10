<?
/**
* Database abstraction layer.
* Driver for MSSQL database server
*
* @package Database
* @version 2.1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-31 1:14 v 2.0b to 2.1
*	- Totally rewritten
*
*	* 2009-03-10 07:50 ver 2.1 to 2.1.1
*	- db_connect often called from constructor. So, object is not istantiated to future cal to it getError()
*		Now we provide property DBError in it itself as ref to db->Error.
**/

include_once('Database/database.php');

$__MSSQL_Error = '';#Global variable. I don't known other way :(
function myErrorHandler($errno, $errstr, $errfile, $errline /*, $errcontext */ ){
global $__MSSQL_Error;
	if (stristr($errstr, 'mssql')){#This hack only fo MSSQL errors
	$__MSSQL_Error .= $errstr;
	/* Don't execute PHP internal error handler */
	return true;
	}
	else return false;# Default error-handler
}

class mssql_database_settings extends DB_settings {
const INT_STR_LENGTH=10; #STRING-length of int, to coding in MSSQL-"array"
}#c mssql_database_settings

class mssql_database extends database{
public $db_type = 'mssql';

/* Only parent, nothing more
	function __construct(
		$sets = null	// mssql_database_settings or array
		,$dontConnect = false ){
	parent::__construct($sets, $dontConnect);
	}#c
*/

	public function db_connect(){
		if (!is_resource($this->db_link)){//Establish connection
			if (!($this->db_link = @call_user_func($this->db_type.'_'.($this->settings->persistent ? 'p' : '') .'connect', $this->settings->hostname, $this->settings->username, $this->settings->password))){
			$this->Query = '[' . $this->db_type.'_'.($this->settings->persistent ? 'p' : '') .'connect' . ']';
				if ($this->settings->DEBUG)
				$this->collectDebugInfo(
					-1,
					mssql_get_last_message(),
					$__MSSQL_Error,
					debug_backtrace()
				);

			// It often called from constructor. So, object is not istantiated to future cal to it getError()
			$cedbe = new ConnectErrorDBException ($this->Error->settings->TXT_cantConnect);
			$cedbe->DBError =& $this->Error;
			throw $cedbe;
			}

		mssql_min_error_severity(1);
		mssql_min_message_severity(1);
		}
	}#m db_connect

	public function query($query, $print_query = false, $last_id = false){
	$this->Fields = null;
	$this->Query = $query;

	#Recode if needed
	$this->iconv_query();

		if ($print_query) echo $query.'<br>';

		#I don't known other way handle this errors because mssql_get_last_message()
		#return only last string of error. To other I parse SDERR
		if ($this->settings->DEBUG){
			if (! @$this->old_error_handler) 
			$this->old_error_handler = set_error_handler("myErrorHandler");
			/*ob_start �� �������� � ������, ���� ������ stdout � stderr! */
		global $__MSSQL_Error;
		$__MSSQL_Error = '';
		}
		
		if (!($res=mssql_query($query.($last_id ? ' ; SELECT @@IDENTITY as last_id' : ''), $this->db_link))){
			if ($this->settings->DEBUG)
			$this->collectDebugInfo(
				-1,
				mssql_get_last_message(),
				$__MSSQL_Error,
				debug_backtrace()
			);
		throw new QueryFailedDBException($this->Error->settings->TXT_queryFailed);
		}

		#In case INSERT statement, and if required - return Last_insert_id
		if ($last_id){
		list($res) = mssql_fetch_row($res);
		}

	$this->result = $res;
	#For backward capability only. Return deprecated
	return $res;
	}//m query

	public function query_limit($query, $from, $amount, $print_query = false){
	#Replace qoutes: ' and " by ''
	$query = preg_replace('/[\'"]/', "''", $query); #"
	$this->query("EXEC proclimit '$query', $from, $amount", $print_query);
	#Errors handled before, if it occures.
	#Empty recorset. See stored Procedure proclimit and its description
	$this->sql_next_result($this->result);
	$this->rowsTotal = current($this->sql_fetch_row());
	$this->sql_next_result($this->result);
	}#m query_limit

	public function ToBlob($str){
	$str = @unpack("H*hex", $str);
	$str = '0x'.$str['hex'];
	return $str;
	}#m ToBlob

	final public function sql_next_result(){
	return mssql_next_result($this->result);
	}#m sql_next_result

	public function sql_escape_string(&$string_to_escape){
	$replaced_string = str_replace("'", "''", $string_to_escape);
	return $replaced_string;
	}#m sql_escape_string

	/**
	* To coding into MSSQL pseudo "array" (which are not supported)
	* Result string, which will be splited into items by fixed length of item.
	* (On server for it presents table Numbers and user defined function (UDF) fixstring_single)
	*
	* @param array	$arr Source array to coding.
	* @return string
	**/
	public function MSSQLintArray($arr){
	return implode('', array_map(array($this, 'int_fixed_length'), (array)$arr));
	}

	/**
	* Helper method for ::MSSQLintArray()
	**/
	protected final function int_fixed_length($itm){
	return sprintf('%'.mssql_database_settings::INT_STR_LENGTH.'s', $itm);
	}#m int_fixed_length

	protected function collectDebugInfo($errNo, $server_message, $server_messageS = '', $d_backtrace){
	$this->Error->clear();
	$this->Error->mergeSettingsArray(
		array(
			'TXT_queryFailed' => $this->Error->settings->TXT_queryFailed,
			'errNo'	=> $errNo,
			'server_message'	=> $server_message,
			'server_messageS'	=> $server_messageS,
			'Query' 	=> $this->Query,
//			'call_from_file' => $d_backtrace[1]['file'],
//			'call_from_line' => $d_backtrace[1]['line'],
//			'bt'	=> new backtrace(null, 2)//Self collectDebugInfo no needed.
			'bt' => new backtrace($d_backtrace, 0)
		)
	);
	}

	/**
	* Fetch result into object of $className
	* @param string	$className=stdClass - Class name to cast.
	* @param array		$params NOT USED in MSSSQL version.
	**/
	public function &sql_fetch_object($className = 'stdClass', array $params = array()){
	/**
	* See http://ru2.php.net/mysql_fetch_object comments by "Chris at r3i dot it"
	* MSSQL, also have second undocumented parameter, but it is "int result_type" (from sources)
	* In any case it can't be classname. Shit. I didn't dig to find out what it means.
	*
	* So, in this page, below, i found next fine workaraound (see comment and example of "trithaithus at tibiahumor dot net")
	**/
	$this->RES = mssql_fetch_object($this->result);

		if ($className != 'stdClass'){//This is hack, and take overhead, do not made perfom without necessary.
		$this->RES = unserialize(
		preg_replace(
			'/^O:[0-9]+:"[^"]+":/i',
			'O:'.strlen($className).':"'.$className.'":',
			serialize($this->RES)
			)
		);
		}

	$this->iconv_result();
	return $this->RES;
	}#m sql_fetch_array
}#c mssql_database
?>