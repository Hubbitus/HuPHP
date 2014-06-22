<?
/**
* User-base.
* @package Users
* @version 1.1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-09-22 17:33
*
* @uses EMPTY_VAR()
* @uses template
* @uses database_where
* @uses settings
**/

include_once('macroses/EMPTY_VAR.php');
include_once('template/template_class_2.1.php');

class user_settings extends settings{}

class UserAuthentificateException extends BaseException{}
	abstract class user_base extends get_settings{
	protected /* messages */ $_messages;
	protected /* message */ $_message; //last, cache

	protected $_id;
	protected $_name;
	protected $_logo;
	protected $_login;

	protected $_authentificated = false;
	protected $_authorizated = false;

	/**
	* Construnctor PRIVATE, so, you must use static authentification!
	*
	* @param user_settings $sets
	**/
	final private function __construct(user_settings $sets){
		$this->_sets = $sets;
	}#__c

	/**
	* Authentification
	* @return
	* Until we have not LSB, the ::authentification method must be defined in derived class!
	**/
	public static function authentification(user_settings $sets = null, $data = null){
		//Make included the class definition of used (in settings) DB driver.
		Single::tryIncludeByClassName(__db);
		@session_start();
		if (isset($_SESSION['user'])) return $_SESSION['user'];

		if (!$data){//Form
			$tmpl = new template($sets->auth_template);
			$tmpl->assign('backpath', $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']);
			exit($tmpl->scheme());
		}
		else{//Process authorization

			if (!($autentificate = self::autentificate($data))) throw new UserAuthentificateException('User authentification failed! Wrong Login or Password.');
			$retUser = new self($sets);
			$retUser->authorizitaion($autentificate);
			$_SESSION['user'] =& $retUser;
			return $retUser;
		}
	}#m authentification
	//abstract public static function authentification(user_settings $sets = null, $data = null);

	/**
	* Work horse. Main implementation of autentification. If need other way to autentificate user - just need reimplement this metod in derived class.
	*
	* @param array $data. Data to autentificate user. Depends from implementation.
	* @return Object
	**/
	function autentificate($data){
		$where = new database_where(
			array(
				array('Login', $data['login'], 'q:'),
				array('Pass', md5($data['pass']), 'q:')
			)
		);
		Single::def(__db)->query('SELECT ID, Login, Name FROM Companies '.$where->getSQL());
		return Single::def(__db)->sql_fetch_object();
	}#m autentificate

	/**
	* Stub! Values IS EXAMPLE! Fill it in real case.
	* @return &$this
	**/
	private function &authorizitaion(&$data){
		$this->_id = $data->ID;
		$this->_name = $data->Name;
		$this->_login = $data->Login;
		return $this;
	}#m authorization

	/**
	* User logout
	*
	* @return
	**/
	public static function logout(){
		@session_start();
		unset($_SESSION['user']);
	}#m logout

	public function getID(){
		return $this->_id;
	}#m getID

	public function getName(){
		return $this->_name;
	}#m getName

	public function getLogin(){
		return $this->_login;
	}#m getLogin

	public function getLogoBlob(){
		if (!$this->_foto) $this->_foto = current(Single::def(__db)->query('SELECT Logo FROM Companies WHERE ID = '.$this->getID()));
		return $this->_foto;
	}#m getFotoBlob

	public function __wakeup(){
		$this->_messages = null; //Must be realy DB queryd. DB-Cache not implemented now.
		/** @todo Implement DB_cache **/
    }
}#c user
?>