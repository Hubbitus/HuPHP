<?
/**
* a1agregator.ru SMS-API ( https://partner.a1agregator.ru/files/fileView/17 )
*
* @package a1agregator.ru SMS-API
* @version 1.0.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-05-17 14:16 ver 1.0
*	- Initial version
**/

//include('macroses/REQUIRED_VAR.php');

/**
* @uses BaseException
**/
class a1agregatorException extends BaseException{}
class a1agregatorMSGParseException extends a1agregatorException{}
class a1agregatorMSGSkeyMismatchException extends a1agregatorMSGParseException{}
class a1agregatorMSGSignatureException extends a1agregatorMSGParseException{}

/**
* @uses settins_check
**/
class a1agregator_MSG_answer extends settings_check{
static public $properties = array(
	'smsid', 'status', 'answer'
	//auxiliary data
	,'ANSWER_FORMAT'
);

//defaults
protected $__SETS = array(
	'atatus'	=> 'reply',

	'ANSWER_FORMAT'	=> array(
	
	),
);

	function __construct(){
	}#m __c
}#c a1agregator_MSG_answer

/**
* @uses settins_check
**/
class a1agregator_MSG extends settings_check{
/**
* @var $date		– дата и время сообщения в системе a1agregator.ru. В данном примере 2008-03-28+17%3A13%3A33 – это 28 марта 2008 года в 17:13:33
* @var $msg		– сообщение, которое отправил абонент, в примере “test”
* @var $msg_trans	– транслитерация сообщения, в примере “test”
* @var $operator_id	– числовой идентификатор оператора, в примере 120 (Билайн)
* @var $user_id	– телефон абонента, отправившего смс, в примере 7909908037
* @var $smsid		– идентификатор сообщения в системе а1агрегатор, в примере 5094
* @var $cost_rur	- сумма, которая зачисляется на счет партнера в рублях, в примере 0.54
* @var $cost		- параметр, определяющий сумму, которая зачисляется на счет партнера в usd, переведенная по курсу последней выплаты, в примере 0.015. Этот параметр носит только информативный характер. Сумма за эту смс при выплате в WMZ будет скорректирована по курсу WM на день выплаты.
* @var $test		– необязательный параметр, приходит только при тестовой смс. Если он равен единице значит смс тестовая.
* @var $num		– короткий номер, на который абонент отправлял запрос, в примере 1121
* @var $retry		– параметр повтора смс, если равен единице значит смс повторная. При повторной смс все другие параметры дублируют первую непрошедшую смс.
* @var $try		– Порядковый номер попытки отправки смс сообщения через разные прокси сервера. SMS можно также считать повторной SMS с параметрами retry =1 или try<>1.
* @var $ran		– параметр надежности абонентского номера - цифра от 1 до 10, которая показывает степень доверия и обеспеченности деньгами к абон. номеру. 1-4 - ненадежные, 5-7 - средние, 8-10 - надежные. В примере 7
* @var $skey		– это последовательность символов, которая кодируется по алгоритму MD5, передается в случаи использования параметра  “Секретный ключ”, в примере передается слово test. Применяется в целях дополнительной безопасности. Указывать секретный ключ необязательно.
* @var $sign		– это последовательность символов, которая кодируется по алгоритму MD5, передается всегда. Последовательность получается путем последовательного соединения параметров:
*	date, msg_trans, operator_id, user_id, smsid, cost_rur, ran, test, num, country_id, skey
*	Применяется в целях повышения безопасности.  
* @var $operator	– Absent in documentation, but present in tests
* @var $country_id	– Absent in documentation, but present in tests
**/

static public $properties = array(
	'date', 'msg', 'msg_trans', 'operator_id', 'operator', 'user_id', 'smsid', 'cost_rur', 'cost', 'test', 'num', 'retry', 'try', 'ran', 'skey', 'sign', 'country_id'
	//auxiliary data
	,'SIGNATURE_FORMAT'
);

protected $__SETS = array(
	'SIGNATURE_FORMAT'	=> array(
	'date', 'msg_trans', 'operator_id', 'user_id', 'smsid', 'cost_rur', 'ran', 'test', 'num', 'country_id', 'skey'
	),
);

/**
* @var	Object(a1agregator_MSG_answer).
**/
private $Answer = null;
/**
* @var	string	Right Skey to check
**/
private $Skey = null;

#This settings do not clear on call clear() and do not rewrited by setSettingsArray()
protected $static_settings = array('SIGNATURE_FORMAT');

	/**
	* @param	array	$array Settings.
	* @param	string	$skey Current Skey to check
	**/
	function __construct(array $array, $skey){
	$this->Skey = $skey;
	$this->setFromArray($array);
	}#m __c

	/**
	* Clear all except uncleared items.
	**/
	public function clear(){
		foreach ($this->__SETS as $key => $sets){
			if (!in_array($key, $this->static_settings))
			$this->__SETS[$key] = null;
		}
	}#m clear

	/**
	* Reimplementation to protect unclearable items
	*
	* @param	array	$setArr
	* @return	nothing
	**/
	public function setSettingsArray(array $setArr){
	parent::setSettingsArray(array_merge($setArr, array_intersect_key($this->__SETS, array_flip($this->static_settings))));
	}#m setSettingsArray

	/**
	* Calculate signature.
	*
	* @return	string
	**/
	public function calcSignature(){
	return md5( $this->getString($this->SIGNATURE_FORMAT) );
	}#m calcSignature

	/**
	* Parse and initialize message from QUERY_STRING. Common use, initialise from $_SERVER['QUERY_STRING']
	*
	* @param	string	$rawString
	* @return	&$this
	**/
	function &setFromQueryString($rawString){
	#http://amurspb.ru/sms.a1agregator/smsgate.php?user_id=71111111111&num=1121&cost=4.61134054658&cost_rur=170.18199&msg=&skey=1094c779dce7ed5f70410cc81f9b10fc&operator_id=299&date=2009-05-17+19%3A32%3A51&smsid=1288931845&msg_trans=&operator=operator&test=1&ran=5&try=1&country_id=45909&sign=f077a353485a66ce3089661bedc99f62
	parse_str($rawString, $msg);
	$this->setFromArray($msg);
	return $this;
	}#m setFromQueryString

	/**
	* Parse and initialize message from array. Common use, initialise from $_GET, $_POST or $_REQUEST global arrays.
	*
	* All checks performed.
	* @param	array	$arr
	* @return &$this
	* @Throw(a1agregatorMSGParseException, a1agregatorMSGSignatureException, a1agregatorMSGSkeyMismatchException)
	**/
	function &setFromArray(array $arr){
	$this->Answer = new a1agregator_MSG_answer();
	$this->setSettingsArray($arr);
		#do NOT use empty() because it works ONLY with variables!!!
		if (!@$this->msg or !@$this->operator_id or !@$this->user_id or !@$this->smsid or !@$this->cost_rur or !@$this->test or !@$this->try or !@$this->sign){
		$what = 'Empty required field(s):';
			/**
			 * Warning! In Specification required fields is not marked! It is minimum on my think!
			 */
			if (!@$this->msg)			$what .= ' [msg]';
			if (!@$this->operator_id)	$what .= ' [operator_id]';
			if (!@$this->user_id)		$what .= ' [user_id]';
			if (!@$this->smsid)			$what .= ' [smsid]';
			if (!@$this->cost_rur)		$what .= ' [cost_rur]';
			if (!@$this->test)			$what .= ' [test]';
			if (!@$this->try)			$what .= ' [try]';
			if (!@$this->sign)			$what .= ' [sign]';
		throw new a1agregatorMSGParseException('Error parsing Incoming message. '.$what."\nQUERY_STRING:".EMPTY_STR($rawString, @$_SERVER['QUERY_STRING']));
		}
		if ($this->sign and $this->sign != $this->calcSignature()){
		throw new a1agregatorMSGSignatureException('Signature incorrect!');
		}
		if ($this->skey and md5($this->Skey) != $this->skey){
		throw new a1agregatorMSGSkeyMismatchException('Skey mismatch');
		}
	return $this;
	}#m setFromArray

	/**
	* Set text of answer to server.
	*
	* @param	string	$text
	* @return	&$this
	* @Throw(VariableRequiredException)
	**/
	function &setAnswer($text){
	$this->Answer->answer = REQUIRED_VAR($text);
	return $this;
	}#m setAnswer
};#c a1agregator_MSG
?>