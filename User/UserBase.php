<?php
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

namespace Hubbitus\HuPHP\User;

use Hubbitus\HuPHP\Vars\Settings\Settings;
use Hubbitus\HuPHP\Vars\Settings\SettingsGet;
use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Template\template;
use Hubbitus\HuPHP\Database\DatabaseWhere;
use Hubbitus\HuPHP\Vars\Single;
use function Hubbitus\HuPHP\Macroses\EMPTY_VAR;

class UserSettings extends Settings {}

class UserAuthenticateException extends BaseException {}

abstract class UserBase extends SettingsGet {
	protected /* messages */ $_messages;
	protected /* message */ $_message; //last, cache

	protected $_id;
	protected $_name;
	protected $_logo;
	protected $_login;

	protected $_authenticated = false;
	protected $_authorized = false;

	/**
	* Constructor PRIVATE, so, you must use static authentication!
	*
	* @param UserSettings $sets
	**/
	final private function __construct(UserSettings $sets){
		$this->_sets = $sets;
	}
	/**
	* Authentication
	* Until we have not LSB, the ::authentication method must be defined in derived class!
	**/
	public static function authentication(?UserSettings $sets = null, $data = null){
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

			if (!($autentificate = self::autentificate($data))) throw new UserAuthenticateException('User authentification failed! Wrong Login or Password.');
			$retUser = new self($sets);
			$retUser->authorizitaion($autentificate);
			$_SESSION['user'] =& $retUser;
			return $retUser;
		}
	}
	//abstract public static function authentification(user_settings $sets = null, $data = null);

	/**
	* Work horse. Main implementation of autentification. If need other way to autentificate user - just need reimplement this metod in derived class.
	*
	* @param array $data. Data to autentificate user. Depends from implementation.
	* @return Object
	**/
	function autentificate($data){
		$where = new DatabaseWhere(
			array(
				array('Login', $data['login'], 'q:'),
				array('Pass', md5($data['pass']), 'q:')
			)
		);
		Single::def(__db)->query('SELECT ID, Login, Name FROM Companies '.$where->getSQL());
		return Single::def(__db)->sql_fetch_object();
	}
	/**
	* Stub! Values IS EXAMPLE! Fill it in real case.
	* @return &$this
	**/
	private function &authorizitaion(&$data){
		$this->_id = $data->ID;
		$this->_name = $data->Name;
		$this->_login = $data->Login;
		return $this;
	}
	/**
	* User logout
	*
	* @return
	**/
	public static function logout(){
		@session_start();
		unset($_SESSION['user']);
	}
	public function getID(){
		return $this->_id;
	}
	public function getName(){
		return $this->_name;
	}
	public function getLogin(){
		return $this->_login;
	}
	public function getLogoBlob(){
		if (!$this->_foto) $this->_foto = current(Single::def(__db)->query('SELECT Logo FROM Companies WHERE ID = '.$this->getID()));
		return $this->_foto;
	}
	public function __wakeup(){
		$this->_messages = null; //Must be realy DB queryd. DB-Cache not implemented now.
		/** @todo Implement DB_cache **/
    }
}
