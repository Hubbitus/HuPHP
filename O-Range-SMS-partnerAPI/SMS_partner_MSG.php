<?
class SMS_partner_MSG extends settings{
protected $__SETS = array(
	'CMD_SEND_FORMAT'	=> array(
	'&cmd=send',
		array('answerto', '&answerto='),
		array('to', '&to='),
		array('base64_text', '&text=')
		#TODO: add UCS2 support option
	),
	'LOG_FORMAT'	=> array(
		array('pass',			"\t[pass]=>",			"\n"),
		array('ShortNbr',		"\t[ShortNbr]=>",		"\n"),
		array('msgInID',		"\t[msgInID]=>",		"\n"),
		array('UserPhone',		"\t[UserPhone]=>",		"\n"),
		array('text',			"\t[text]=>",			"\n"),
		array('base64_text',	"\t[base64_text]=>",	"\n"),
		array('answerto',		"\t[answerto]=>",		"\n"),
		array('_result',		"\t[_result]=>",		"\n")
	)
);

#This settings do not clear on call clear() and do not rewrited by setSettingsArray()
protected $static_settings = array('CMD_SEND_FORMAT', 'LOG_FORMAT');

	/**
	* Clear all but uncleared items.
	**/
	public function clear(){
		foreach ($this->__SETS as $key => $sets){
			if (!in_array($key, $this->static_settings))
			$this->__SETS[$key] = null;
		}
	}#m clear

	/**
	* Reimplementation to protect uncleared items
	*
	* @param array	$setArr
	**/
	public function setSettingsArray(array $setArr){
	parent::setSettingsArray(array_merge($setArr, array_intersect_key($this->__SETS, array_flip($this->static_settings))));
	}#m setSettingsArray

};#c SMS_partner_MSG
?>