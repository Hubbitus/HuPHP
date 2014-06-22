<?
/**
* Robokassa.ru interface ( http://www.robokassa.ru/Doc/Ru/Interface.aspx )
*
* @package Orange SMS-API
* @version 1.1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*/

/**
* Robokassa allow use additional user parameters, but require start
* it names from 'sph' prefix.
*
* @uses settings
* @uses VariableRangeException
**/
class robokassa_sph extends settings{
	/**
	* Reimplemnt to add prefixes to names of user pparameters. As required by spec.
	* @inheritdoc
	**/
	public function setSetting($name, $value){
		if(strtolower(substr($name, 0, 3)) != 'sph') $name = 'sph_' . $name;
		parent::setSetting($name, $value);
	}#m setSetting

	/**
	* Reimplemnt to add prefixes to names of user pparameters. As required by spec.
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr){
		$this->__SETS = array();
		/**
		* @internal
		* For our realisation just foreach all, now we can simple invoke mergeSettingsArray()
		**/
		$this->mergeSettingsArray($setArr);
	}#m setSettingsArray

	/**
	* Reimplemnt to add prefixes to names of user pparameters. As required by spec.
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
		foreach (REQUIRED_VAR($setArr) as $key => $value) $this->setSetting($key, $value);
	}#m mergeSettingsArray

	/**
	* Sort by keys, how spec require it.
	*
	* @return	&$this
	**/
	function &sort(){
		ksort($this->__SETS);
		return $this;
	}#m sort

	/**
	* Return URL-string of users parameters.
	*
	* @return	string
	**/
	public function getString(){
		$this->sort();
		$ret = '';
		foreach ($this->__SETS as $key => $value){
			$ret .= '&' . urlencode($key) . '=' . urlencode($value);
		}
		if (strlen($ret) > 2048) throw new VariableRangeException('Max encoded length of SPHs can not be greater than 2048');
		return $ret;
	}#m getString

	/**
	* Allow conversion into string
	*
	* @return	string
	**/
	public function __toString(){
		return $this->getString();
	}#m __toString()
}#c robokassa_sph

/**
* Class to handle ROBOKASSA.ru interface (http://www.robokassa.ru/Doc/Ru/Interface.aspx) of payment gateway
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
*
* @uses settings_check
* @uses VariableRangeException
**/
class robokassa extends settings_check{
	const BASE_url_production = 'https://merchant.roboxchange.com/Index.aspx?';
	const BASE_url_test = 'http://test.robokassa.ru/Index.aspx?';

	public $properties = array(
		'MrchLogin', 'MrchPass1', 'MrchPass2',  'OutSum', 'InvId', 'Desc', 'SignatureValue', 'IncCurrLabel', 'Culture', 'Encoding', 'Email'
		,'sph'
		// auxiliary data
		,'URL_FORMAT', 'SIGNATURE_FORMAT', 'SIGNATURE_IN_success_FORMAT', 'SIGNATURE_IN_result_FORMAT', 'BASE_URL'
	);

	protected $__SETS = array(
		/**
		* @var $MrchLogin;	Merchant Login in Robokassa
		* @var $MrchPass1;	Merchant Password1 in Robokassa
		* @var $MrchPass2;	Merchant Password2 in Robokassa for the XML interface
		* @var $OutSum;		Sum of order
		* @var $InvId;		Order number in store. Must be unique in store.
		* @var $Desc;		Order description. Max length - 100 chars.
		*
		* @var $SignatureValue;	Computed: Signature value. {@see ::getSignature()} method.
		*
		* @var $IncCurrLabel;	Optional: Initial currency. May be changed by user during pay process.
		* @var $Culture			Optional: Language: 'en' | 'ru'
		* @var $Encoding 		Optional: For the HTML-form of kassa. Not needed on redirect.
		* @var $Email			Optional: Email of user. May be changed by user during pay process.
		*
		* @var $sph				Object(robokassa_sph)
		* @var $sph:			String of alternative conversion of $this->sph. For more detailes {@see ::getProperty()} method.
		*/
		//Default values
		'BASE_URL' => self::BASE_url_production
		,'Culture' => 'ru'
		,'Encoding' => 'utf-8'

		,'URL_FORMAT'	=> array(
			'BASE_URL'
			,array('MrchLogin', 'MrchLogin=')
			,array('OutSum_F', '&OutSum=')
			,array('InvId', '&InvId=')
			,array('Desc', '&Desc=')
			,array('SignatureValue', '&SignatureValue=')
			,array('IncCurrLabel', '&IncCurrLabel=')
			,array('Culture', '&Culture=')
			,array('Encoding', '&Encoding=')
			,array('Email', '&Email=')

			,array('sph', '', '', '') //Last '' is important
		)
		,'SIGNATURE_FORMAT'	=> array(
			array('MrchLogin', '', ':')
			,array('OutSum', '', ':')
			,array('InvId', '', ':')
//			,array('Desc', '', ':') By Specification it is not marked as Optional, but in example it is not present!
			,'MrchPass1'
			,array('sph:', ':', '', '')
		)
		,'SIGNATURE_IN_success_FORMAT'	=> array( //IN signature, to check
			array('OutSum', '', ':')
			,array('InvId', '', ':')
			,'MrchPass1'
			,array('sph:', ':', '', '')
		)
		,'SIGNATURE_IN_result_FORMAT'	=> array( //IN signature, to check
			array('OutSum', '', ':')
			,array('InvId', '', ':')
			,'MrchPass2'
			,array('sph:', ':', '', '')
		)
	);

	//[&shpa=yyy&shpb=xxx...-пользовательские_параметры_начинающиеся_с_SHP_в_сумме_до_2048_знаков]
	private /* robokassa_sph */ $sph;

	/**
	* @param	array	$array Settings.
	**/
	function __construct(array $array){
		$this->sph = new robokassa_sph(@$array['sph']);
		parent::__construct($this->properties, $array);
	}#__c

	/**
	* Get URL to redirect on robokassa payment geteway.
	*
	* @return	string
	**/
	function getPayURL(){
		return $this->getString($this->URL_FORMAT);
	}#m getPayURL

	/**
	* Get payForm to select mathod and Currency of pay.
	* @TODO: IMPLEMENT IT. Got as example from X-cart e-gold processor.
	 */
	function getPayForm(){
	?>
	<form action="https://www.e-gold.com/sci_asp/payments.asp" method=POST name=process>
	<input type=hidden name=PAYEE_ACCOUNT value="<?php echo htmlspecialchars($accid); ?>">
	<input type=hidden name=PAYEE_NAME value="<?php echo htmlspecialchars($accname); ?>">
	<input type=hidden name=PAYMENT_UNITS value="<?php echo htmlspecialchars($curr); ?>">
	<input type=hidden name=PAYMENT_METAL_ID value="0">
	<input type=hidden name=PAYMENT_URL value="<?php echo $http_location."/payment/cc_egold.php?ok=true"; ?>">
	<input type=hidden name=PAYMENT_URL_METHOD value="POST">
	<input type=hidden name=STATUS_URL value="<?php echo "mailto:".htmlspecialchars($config["Company"]["orders_department"]); ?>">
	<input type=hidden name=NOPAYMENT_URL value="<?php echo $http_location."/payment/cc_egold.php?ok=false"; ?>">
	<input type=hidden name=NOPAYMENT_URL_METHOD value="POST">
	<input type=hidden name=PAYMENT_AMOUNT value="<?php echo $cart["total_cost"]; ?>">
	<input type=hidden name=BAGGAGE_FIELDS value="ORDER_NUM">
	<input type=hidden name=ORDER_NUM value="<?php echo htmlspecialchars($ordr); ?>">
	</form>
	<?
	}#m getPayForm"

	/**
	* Reimplement to allow calculate signature on the fly and some more magick
	**/
	public function getProperty($name){
		switch ($name){
			case 'SignatureValue':
				return $this->getSignature();
				break;

			case 'sph':	// Force string
				return $this->sph->getString();
				break;

			case 'sph:':	// For signature calculation need other format
				return preg_replace('^&', ':', $this->sph);
				break;

			case 'OutSum_F': //Formated by specification.
				return number_format($this->__SETS['OutSum'], 2, '.', '');
				break;//OutSum

			default:
				return parent::getProperty($name);
		}
	}#m getProperty

	/**
	* Reimplement to allow add some Spec-checks.
	* @inheritdoc
	**/
	public function setSetting($name, $value){
		parent::setSetting($this->checkNamePossible($name, __METHOD__), $value);
		switch ($name){
			case 'Desc':
				if (strlen($this->$name) > 100) throw new VariableRangeException('Max possible length of "Desc" field can not be greater than 100');
				break;//Desc
		}
	}#m setSetting

	/**
	* Reimplemnt to add prefixes to names of user pparameters. As required by spec.
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr){
		$this->__SETS = array();
		/**
		* @internal
		* For our realization just foreach all, now we can simple invoke mergeSettingsArray()
		**/
		$this->mergeSettingsArray($setArr);
	}#m setSettingsArray

	/**
	* Reimplemnt to add prefixes to names of user pparameters. As required by spec.
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
		foreach (REQUIRED_VAR($setArr) as $key => $value) $this->setSetting($key, $value);
	}#m mergeSettingsArray

	/**
	* Switch between test or production mode.
	*
	* @param	boolean	$on=true
	* @return	&$this
	**/
	public function &testMode($on = true){
		if ($on) $this->BASE_URL = self::BASE_url_test;
		else $this->BASE_URL = self::BASE_url_production;
		return $this;
	}#m testMode

	/**
	* Calculate signature.
	*
	* @return	string
	**/
	public function getSignature(){
		return md5( $this->getString($this->SIGNATURE_FORMAT) );
	}#m getSignature

	/**
	* Calculate IN signature to check in Success request.
	*
	* @return	string
	**/
	public function getSignatureInSuccess(){
		return md5( $this->getString($this->SIGNATURE_IN_success_FORMAT) );
	}#m getSignatureInSuccess

	/**
	* Calculate IN signature to check in Result request.
	*
	* @return	string
	**/
	public function getSignatureInResult(){
		return md5( $this->getString($this->SIGNATURE_IN_result_FORMAT) );
	}#m getSignatureInResult
}#c robokassa

/** @example:
* $r = new robokassa(
*	array(
*		'MrchLogin' => 'korda'
*		,'MrchPass1'	=> 'my_super_pass'
*		,'OutSum'	=> '777'
*		,'InvId'	=> '123'
*		,'Desc'		=> 'Super puper order'
*
*		,'IncCurrLabel'	=> 'PCR'	// Yandex-money
*		,'Culture'	=> 'ru'	// Non needed, 'ru' is default value
*		,'Email'	=> 'pupkin@user.ru'
*
*		,'sph'		=> array(
*			'Test1' => 'Test1Value'
*			,'Test2' => 'Test2Value'
*		)
*	)
*);
*
*$r->testMode();
*dump::a($r);
*echo($r->getPayURL());
*header('Location:' . $r->getPayURL());
**/
?>