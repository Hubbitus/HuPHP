<?
include_once('HuLOG.php');
include_once('settings.php');
include_once('file_base.php');
include_once('RegExp/RegExp_pcre.php');

include_once('macroses/EMPTY_STR.php');

include_once('SMS_partnerAPI/SMS_partner_MSG.php');
include_once('Exceptions/SMS_partnerAPI.php');

class SMS_partner_settings extends settings {
/*
#Defaults
protected $__SETS = array(
	'login' =>
	'password' =>
);
*/
}

class SMS_partnerAPI extends get_settings{
protected $_db;
protected $_log;

protected $_transport = null;	#Making on fly

private $authID;

private /* SMS_partner_MSG */ $MSG;

public function __construct(SMS_partner_settings &$sets, &$db = null, &$log = null){
@session_start();
$this->_sets = $sets;
$this->MSG = new SMS_partner_MSG();

	if (is_array($log)) $this->_log = new HuLOG ($log);
	elseif ($log) $this->_log = $log;
	else $this->_log = new HuLOG;

	if (is_array($db)){
		if (!class_exists($db['class_name'])) include_once($db_class_file);
	$this->_db = new $db['class_name']($db);
	}
	elseif ($db) $this->_db = $db;
	else{
	include_once('Vars/Singleton.php');
	include_once('include/configs/db_config.php');
	$this->_db =& Single::def(__db);
	}
}#__c

function __get($name){
	if ('transport' == $name) return $this->getTransport();
	else return parent::__get($name);
}#m __get


public function &getTransport(){
	if (! $this->_transport ) $this->_transport = new $this->settings->net_transport;
return $this->_transport;
}#m getTransport

public function auth(){
ini_set('session.cache_expire', $this->settings->authTime);
	try{
	$this->transport->setName($this->settings->URL . 'cmd=auth&login=' . $this->settings->login . '&pass=' . $this->settings->password);
	$this->transport->getContents();
	$_SESSION['authID'] = $this->authID = $this->parseServerAnswer('authID');
		if (!$this->authID) throw new MSG_AuthErrorException("Auth error!\nServer response: ".$this->transport->getBLOB()."\nOn query:".$this->settings->URL.'cmd=auth&login='.$this->settings->login.'&pass='.$this->settings->password);
	}
	catch(FilesystemException $fse){#ERROR. TODO process this error.
	$this->_log->toLog($fse->__toString(), 'ERR', 'net');
	throw $fse;
	}
	catch (MSG_partnerAPIException $mpae){
	$this->_log->toLog($mpae->__toString(), 'ERR', 'auth');
	throw $mpae;
	}
}#m auth

private function getAuthID(){
	if (!$this->authID)
		if (@$_SESSION['authID']) $this->authID = $_SESSION['authID'];
		else $this->auth();
return $this->authID;
}#m getAuthID


public function parseInMSG($rawString = null){
#http://ourSMSgate.tld/gate.cgi?pass=d41d8cd98f00b204e9800998ecf8427e&ShortNbr=2300&msgInID=12452&UserPhone=79111234567&text=8uXx8u7i7uUg8e7u4fnl7ejl
parse_str(EMPTY_STR($rawString, @$_SERVER['QUERY_STRING']), $msg);
$this->MSG->setSettingsArray($msg);
$this->MSG->setProperty('base64_text', $this->MSG->text);
$this->MSG->setProperty('text', base64_decode(str_replace(' ', '+', $this->MSG->base64_text)));
#prepare for answer. Inverse (copy) few fields by default.
$this->MSG->setProperty('answerto', $this->MSG->msgInID);
$this->MSG->setProperty('to', $this->MSG->UserPhone);

	#do NOT use empty() because it works ONLY with variables!!!
	if (!@$this->MSG->msgInID or !@$this->MSG->ShortNbr or !@$this->MSG->UserPhone or !@$this->MSG->pass){
	$what = 'Empty field(s):';
		if (!@$this->MSG->msgInID)	$what .= ' [msgInID]';
		if (!@$this->MSG->ShortNbr)	$what .= ' [ShortNbr]';
		if (!@$this->MSG->UserPhone)	$what .= ' [UserPhone]';
		if (!@$this->MSG->pass)		$what .= ' [pass]';
	throw new MSG_InParseErrorException('Error parsing Incoming message. '.$what."\nQUERY_STRING:".EMPTY_STR($rawString, @$_SERVER['QUERY_STRING']));
	}
$this->_log->toLog('MSG in', 'ACS', 'msg', $this->MSG->getString($this->MSG->LOG_FORMAT));
echo 'OK';	#Answer to Server
}#m parseInMSG

public function setMSG(array $msg){
$this->MSG->mergeSettingsArray($msg);
}#m setMSG

public function getMessage(){
return $this->MSG;
}#m getMessage

public function sendMSG(){
#http://api.o-range.ru/?authID=fc6XJgmaNpF8ZRMQ0lVXU1&cmd=send&answerto=26260&to=79052084523&text=8uXx8iDw8/Hx6u7j7iD/5/vq4CDiIHNtc19hcGk=
	try{
	$this->MSG->setProperty('base64_text', base64_encode($this->MSG->text));
	$this->transport->setName($this->settings->URL.'authID='.$this->getAuthID().$this->MSG->getString($this->MSG->CMD_SEND_FORMAT));
	$this->transport->getContents();
	$this->MSG->setSetting('msgOutID', $this->_result = $this->parseServerAnswer('msgOutID'));
		if (!intval($this->MSG->msgOutID)) throw new MSG_SendFailException("Message sent error!\nServer response: ".$this->transport->getBLOB()."\nOn query:".$this->settings->URL.'authID='.$this->getAuthID().$this->MSG->getString($this->MSG->CMD_SEND_FORMAT));
	$this->_log->toLog('MSG out', 'ACS', 'msg', $this->MSG->getString($this->MSG->LOG_FORMAT));
	}
	catch (FilesystemException $fse){
	#TODO Error-handling
	$this->_log->toLog($fse->__toString(), 'ERR', 'net');
	throw $fse;
	}
	catch (MSG_SendFailException $msf){
	$this->_log->toLog($msf->__toString(), 'ERR', 'net');
	throw $msf;
	}
}#m sendMSG

protected function parseServerAnswer($what){
#<OK/><authID>cpahqq4enpjbag0266hqvddr42</authID>
$reg = new RegExp_pcre ('#<OK/><'.RegExp_pcre::quote($what).'>(.*)</' . RegExp_pcre::quote($what) . '>#', $this->transport->getBLOB());
return ($this->_result = $reg->match(1));
}#m parseServerAnswer

}#c SMS_partnerAPI
?>