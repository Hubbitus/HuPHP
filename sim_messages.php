<?
/**
* Get, parse and manipulate SIM-IM message history files.
* @package SIMhistory
* @version 0.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

include_once('Debug/debug.php');
include_once('Vars/VariableStream.php');
include_once('macroses/EMPTY_STR.php');
include_once('System/Process.php');

class sim_message {
private $type;
private $items = array();

/*
ServerText="Текст сообщения"
Flags=17
Background=16777215
Foreground=0
Time=1185694212
*/
	public function parse($type, $string){
	$this->type = $type;
	#In "" interprets as standart: variables, \n etc... No needed! Replece by ''
	#As replace " to ' need quote exists like: "text' quote" => "text'"'"' quote" overwise get error parse ini-file!
	$string = preg_replace(
		array(
			'/\'/',
			'/^(ServerText|Text)="(.*)"/m',
		),
		array(
			'\'"\'"\'',
			'\1=\'\2\'',
		),
		$string
	);
	$GLOBALS['temp_string'] = $string;
	$this->items = parse_ini_file('var://temp_string');
	}#m parse

	/*
	Recode text to $encTo from:
		1) autodetected encoding if $justRecode = false. If autodetect failed to $encFrom
		2) $encFrom, if $justRecode = true
	@param	string	Opt['utf-8']. Encoding To convert.
	@param	string	Opt['cp1251']. Encoding From convert.
	@param	boolean	Opt['false']. Do autodetect of encForm or not.
	@param	string	Opt['russian']. Language for autodetection encoding (see man enca).
	*/
	public function recodeText($encTo = 'utf-8', $encFrom = 'cp1251', $justRecode = false, $language = 'russian'){
		if ($this->type != 'Message') return false;

		if (@$this->items['ServerText']) $text =& $this->items['ServerText'];
		else $text =& $this->items['Text'];
		if ($justRecode){//DON'T autodetect conversion
		$text = iconv($encFrom, $encTo, $text);
		}
		else{
			try{
			$text = Process::exec('enconv '.NON_EMPTY_STR($encTo, '-x ').NON_EMPTY_STR($language, ' -L '), null, null, $text);
			}
			catch (ProcessException $pe){
			#Doing Fallback. 1: "enconv: Cannot convert `STDIN' from unknown encoding"
			//c_dump($pe->state->exit_code);
				if (1 == $pe->state->exit_code){#Just recode manualy
				fwrite(STDERR, 'unknown In encoding. Fallback to '.$encFrom.' => '.$encTo."\n");
				$text = iconv($encFrom, $encTo, $text);
				}
				else #Ignore, if recoding not succeed
				echo 'NOT recoding properly, ignoring. '."\n".$pe->getMessage()."\nOriginal message: ".log_dump($this->items);
			}		
		}
	}#m recodeText

	public function getString(){
	$txt = '['.$this->type."]\n";
		foreach ($this->items as $key => $item){
			#String-values must be quoted!
			#Whithout (string) casting by defaults all casting to int, and this is equivalent all times!
			if ( (string)$item != (string)intval($item)){
			$item = NON_EMPTY_STR($item, '"', '"');
			}
		$txt .= NON_EMPTY_STR($item, $key.'=', "\n");
		}
	return $txt;
	}#m getString

	public function __get($name){
	return @$this->items[$name];
	}#m __get
	}#c sim_message

	class sim_messages{
	private $messages = array();
	private $order = null;

	public function __construct($filename = null){
		if ($filename) $this->parseFile($filename);
	}#m __construct

	public function parseFile($filename){
	$this->messages = array();

	$cont = file_get_contents($filename);
	$messages = preg_split('/\[(Message|Status|Added|Grant autorization|ICQAuthRequest)\]/', $cont, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

	$mes = new sim_message();
		for($i=0; $i<count($messages); $i+=2){
		$mes->parse($messages[$i], @$messages[$i+1]);
		$this->messages[] = clone $mes;
		}
	}#m parseFile

	public function orderBy($orderName = 'Time'){
	$this->order = $orderName;
	usort($this->messages, array($this, 'orderCmp'));
	}#m orderBy

	private function orderCmp($a, $b){
		if ($a->{$this->order} == $b->{$this->order}) {
		return 0;
		}
	return ($a->{$this->order} < $b->{$this->order}) ? -1 : +1;
	}#m orderCmp

	public function recodeAll($encTo = 'utf-8', $encFrom = 'cp1251', $justRecode = false, $language = 'russian'){
		foreach ($this->messages as $key => $mes){
		$this->messages[$key]->recodeText($encTo, $encFrom, $justRecode, $language);
		}
	}#m recodeAll

	public function add(sim_message $mes){
	$this->messages[] = $mes;
	}#m add

	public function getArray(){
	return $this->messages;
	}#m getArray

	public function merge(sim_messages $addMessages){
	$this->messages = array_merge($this->messages, $addMessages->getArray());
		if ($this->order) $this->orderBy($this->order);
	}#m merge

	public function getString(){
	$str = '';
		foreach ($this->messages as $msg){
		$str .= $msg->getString();
		}
	return $str;
	}#m getString
}#c sim_messages
?>