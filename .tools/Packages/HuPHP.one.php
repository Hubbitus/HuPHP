<?
/** This is automaticaly generated file. Please, do not edit it! Instead use scripts from .tools directory to regenerate. **/
?><?
/**
* Класс шаблонизатора. Версия 2.1.2
* @deprecated Good idea but trash implementation. To be reimplemented.
*
* @Changelog
*	* Fri Aug 24 2007 Pavel Alexeev <Pahan [ at ] Hubbitus [ DOT ] info>
*	- Added assignByRef method
*	- changed defaults for TEMPLATE_TEMPLATES_DIR and TEMPLATE_DEFAULT_SCHEME_DIR
*
*	* Птн 27 Апр 2007 15:25:38
*	- В find_path добавил поиск пути в той же директори что и шаблон (а не только класс шаблона)
*
*
*	* Вск 22 Апр 2007 11:24:12
*	- Версия a1 - восстановлен из бакапа, скорее всего есть какие-то ошибки, которые уже исправлял раньше :(
*
*	* 2008-11-04 19:28 ver 2.1 to 2.1.1
*	- Add method assignFromArray()
*
*	* 2009-03-18 08:18 ver 2.1.1 to 2.1.2
*	- In method ::scheme() fix provide root assigments.
**/

/**
* @uses gentime
**/

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', true);

class template{
var $content_file;

var $functions = array(	#Имена определенных в шаблоне функций и их приоритеты выполнения
	'include_file' => 1,
	'cycle' => 2,
	'esli' => 3,
	'WYSIWYG' => 4,
	'alt' => 5,		#ALTernatives, switch, case
	'set' => 6,		#Устанавливаем значения переменных
	'_' => 10
	);

var $assigned = array();#Значения заменяемых переменных
var $filename;		#Имя файла шаблона, который парсим
var $error = array();	#Сообщения обошибках

var $wysiwyg = false;	#Признак что следующая форма инициализации редактора будет "короткой" когда установлено в true
var $wysiwyg_full = false;#Всегда выводить "полную" форму инициализации редактора.

var $_gentime;		#Объект подсчета времени генерации
var $gentime;		#Результирующее время

protected $_padRes = 0;	#Это для замены строк, сдвиг позиции
public $_parent = null;	#Будет ссылка на родителя
public $_top = null;	#Будет ссылка на ВЕРХНЕГО родителя

	function template($filename = false, &$parent = null, &$top = null){#Конструктор
	$this->_parent = $parent;
		if ($top) $this->_top = $top;
		else $this->_top =& $this;	#Если не вложен
	#########CONSTANTS#########
		if (!defined('TEMPLATE_DEBUG')) define ('TEMPLATE_DEBUG', false);	#Поумолчанию отключен вывод ошибок
		if (!defined('TEMPLATE_GENTIMECALC')) define ('TEMPLATE_GENTIMECALC', false);

//		if (!defined('TEMPLATE_TEMPLATES_DIR')) define ('TEMPLATE_TEMPLATES_DIR', false);
		if (!defined('TEMPLATE_TEMPLATES_DIR')) define ('TEMPLATE_TEMPLATES_DIR', 'templates/');
		if (!defined('TEMPLATE_MODULES_SUBDIR')) define ('TEMPLATE_MODULES_SUBDIR', 'modules/');

		if (!defined('TEMPLATE_DEFAULT_SCHEME')) define ('TEMPLATE_DEFAULT_SCHEME', 'default.scheme');
//		if (!defined('TEMPLATE_DEFAULT_SCHEME_DIR')) define ('TEMPLATE_DEFAULT_SCHEME_DIR', false);
		if (!defined('TEMPLATE_DEFAULT_SCHEME_DIR')) define ('TEMPLATE_DEFAULT_SCHEME_DIR', TEMPLATE_TEMPLATES_DIR);

		if (!defined('TEMPLATE_MESSAGE')) define ('TEMPLATE_MESSAGE', 'message.tmpl');
	###########################

		if ($filename){	#С именем файла - быстрый вызов, конструктора достаточно
		$this->filename = $this->load_file($filename);
		}

		#Для времени парсинга
		if ('TEMPLATE_GENTIMECALC'){
		/*-inc
		
		*/
		$this->_gentime = new gentime;
		$this->_gentime->start();
		}
	}#m constructor

	#$subdir это для модулей, будем искать тамже, где и все, но в поддиректории TEMPLATE_MODULES_SUBDIR (modules/)
	function find_path ($filename, $subdir = ''){
	$finded = 0;	#Не найдено, не изменялось пути
				# 1 - найден где задано
				# 2 - Найден, с изменением пути
				# 4 - НЕ найден вообще, даже с изменением пути
	$realfile = '';

	#+? Возможно этот поиск везде-везде избыточен, может убрать его для скорости?
		if (!(is_file($realfile = $subdir.$filename) and is_readable($realfile)))
			if (is_file($realfile = TEMPLATE_TEMPLATES_DIR.'/'.$subdir.$filename) and is_readable($realfile))
			$finded = $finded | 2;
			else{
				#В той же директории где класс шаблонизатора. Прежде всего для модулей.
				if (is_file($realfile = dirname(__FILE__).'/'.$subdir.$filename) and is_readable($realfile)){
				$finded = $finded | 2;
				}
				else
					#В той же директории где шаблон. Прежде всего для включаемых шаблонов.
					if (is_file($realfile = dirname($this->filename).'/'.$subdir.$filename) and is_readable($realfile)){
					$finded = $finded | 2;
					}
					else $finded = $finded | 4;
			}
		else {
		$finded = $finded | 1;
		}

		if ($finded & 4) return false; #Не найден
		else {
			if ($finded & 2) $this->error[] = 'NOTICE: Path of file "'.$filename.'" changed to: "'.$realfile.'"'; #"
		return $realfile;
		}
	}

	function load_file($filename){
	($this->content_file = @file_get_contents($this->find_path($filename), true)) or (exit($this->error[] = "FATAL ERROR: Не могу прочесть файл $filename"));
	return $filename;	#Возвращаем полное имя файла, что включили!
	}#m load_file

	function string_template($string){
	$this->content_file = $string;
	}

	function assign($var_name, $var_value, $filename = null){
		if ($filename == null) $filename = $this->filename;	#Имя файла шаблона необязательно.
	$this->assigned[$filename][$var_name] = $var_value;
	}#m assign

	function assignByRef($var_name, &$var_value, $filename = null){
		if ($filename == null) $filename = $this->filename;	#Имя файла шаблона необязательно.
	$this->assigned[$filename][$var_name] = $var_value;
	}#m assignByRef

	/**
	* Assign from array.
	*
	* @param array		$from - Array assign from
	* @param string	$filename - Same as in {@see: assign()} method
	* @returns &$this
	**/
	function &assignFromArray(array $from, $filename = null){
		foreach ($from as $key => $value){
		$this->assign($key, $value, $filename);
		}
	return $this;
	}#m assignByRef

	function include_file(&$match){//Включение файлов
		#Сначала проверяем на доступность и обрабатываем ошибки
		if ( !($include_filename = $this->find_path($match[2][0]))){

		$this->error[] = 'WARNING: "'.$match[2][0].'" not readable.';
		$this->result($match[0], $err = 'WARNING: "'.$match[2][0].'" not readable.');
		return false;
		}

		#Собственно включаем
		if ($match[3][0] == 'tmpl'){//Значит включается шаблон, рекурсивно его обрабатываем
		$incl = new template($include_filename, $this, $this->_top);
		#Передаем потомку назначенные переменные, расширяя их, "общими" значениями
		@$incl->assigned[$include_filename] = (array)$incl->_top->assigned[$include_filename] + (array)$this->assigned[$this->filename];
		$incl->parse(false);
		$this->result($match[0], $incl->content_file);
		unset($incl);
		}
		elseif ($match[3][0] == 'php'){//Парсим на PHP
		#+ 4 строки сохранение в строку результата обработки файла на PHP!!!
		ob_start();
		include($include_filename);
		$include = ob_get_contents();
		ob_end_clean();
		#+
		$this->result($match[0], $include);
		}
		else{//Все остальные внешние файлы, включаем AS IS
		$include = file_get_contents($include_filename);
		$this->result($match[0], $include);
		}
	}#m include_file

	function varrrs($filename, &$match, $ret = false){//Заменяем простые переменные
		if ($ret){#В этом случае, вызов для ПОЛУЧЕНИЯ значиния
		# и в $match будет СТРОКА!, тогда приведем к общему виду:
		$m = $match;
		}
		else $m =& $match[1][0];

		if (@isset($this->assigned[$filename][$m])){//Обычная переменная
		$rep_value = $this->assigned[$filename][$m];
		}
		else{//Массив (разбираем из строки)
		preg_match("#(\w*)\[\"?(.*)\"?\]#si", $m, $vr);//$vr = VaR
			if (isset($this->assigned[$filename][@$vr[1]][@$vr[2]])){//Тогда пробуем константу подставить
			$rep_value = $this->assigned[$filename][$vr[1]][$vr[2]];
			}
			else{
				if (defined($m)){
				$rep_value = constant($m);
				$this->error[] = "NOTICE: Для имени {$m} использовано значение константы";
				}
				else{//Значит ничего не найдено
				//В самом конце попробуем вычислить значение переменной
				$rep_value = $this->evalute($m);
				}
			}
		}

		if ($ret) return $rep_value;
		else $this->result($match[0], $rep_value);
	}#m varrrs

	function set(&$match){
		if (!$match[2][0]) $this->error[] = "ERROR: не задано имя переменной в SET!";
		if (!$match[3][0]) $this->error[] = "WARNING: Значение переменной в SET пустое или не задано";
	$val = $this->varrrs($this->filename, $match[3][0], true);
		if ($match[2][0] != $this->varrrs($this->filename, $match[2][0], true)) $this->error[] = "WARNING: Значение существующей переменной {$match[2][0]} перезаписано на $val в шаблоне!";
	$this->assigned[$this->filename][$match[2][0]] = $val;
	$this->result($match[0], $tt='');	#А заменям в шаблоне на пустое!
	}#m set

	function cycle(&$match){
	$cont_cycle = '';//Контейнер для содержимого цикла
		switch ($match[2][0]){
		case 'foreach':
			if (is_array($this->varrrs($this->filename, $match[3][0], true))){//Проверка из-за невозможности в foreach подавить вывод ошибок символом @
				foreach ($this->varrrs($this->filename, $match[3][0], true) as $cvar){
				$ccl = new template (null, $this, $this->_top);
				$ccl->assigned = $this->assigned;//Передаем потомку назначенные переменные
					if ($match[4][0]){//Если задано имя AS
					$ccl->assigned[$this->filename][$match[4][0]] = $cvar;//Переменная для данного прохода цикла
					}
					else{//Иначе тоже самое
						if ($ccl->assigned[$this->filename][$match[3][0]]) $ccl->assigned[$this->filename][$match[3][0]] = $cvar;//Если не массив, то побыстрому делаем
						else{
						$cur_var = $this->get_var($match[3][0], '$'.'ccl->assigned["'.$this->filename.'"]');
						eval('$cur_var = &'.$cur_var.';');//Теперь из текстового описания получим указатель.
						$cur_var = $cvar;
						}
					}

				$ccl->filename = $this->filename;//Тот же файл
				$ccl->string_template($match[8][0]);
				$ccl->parse(false);
				$cont_cycle .= $ccl->content_file;
				unset($ccl);
				}
			}
			else $this->error[] = "WARNING: {$match[3][0]} не является массивом, по элементам которого можно пройти циклом!";
		break;//foreach

		case 'sql':
			while ($cvar = $this->assigned[$this->filename][$match[3][0]]->sql_fetch_assoc()){
			$ccl = new template(false, $this, $this->_top);
			$ccl->assigned = $this->assigned;//Передаем потомку назначенные переменные
			$ccl->assigned[$this->filename][$match[3][0]] = &$cvar;//Переменная для данного прохода цикла
			$ccl->filename = $this->filename;//Тот же файл
			$ccl->string_template($match[8][0]);
			$ccl->parse(false);
			$cont_cycle .= $ccl->content_file;
			unset($ccl);
			}
		break;//sql

		case 'for':
		$match[4][0] = $this->evalute($match[4][0]);
		$match[5][0] = $this->evalute($match[5][0]);
			if ($match[6][0] == '' or $match[6][0] = 0) $match[6][0] = 1;//Значение Поумолчанию для шага
			else $match[6][0] = $this->evalute($match[6][0]);

			if ($match[6][0] > 0){
				for ($$match[3][0] = $match[4][0]; $$match[3][0] <= $match[5][0]; $$match[3][0] += $match[6][0]){
				//$$match[3] - Переменная переменной, хотя это не обязательно здесь
				$ccl = new template(false, $this, $this->_top);
				$ccl->assigned = $this->assigned;//Передаем потомку назначенные переменные
				$ccl->assigned[$this->filename][$match[3][0]] = $$match[3][0];//Переменная для данного прохода цикла
				$ccl->filename = $this->filename;//Тот же файл
				$ccl->string_template($match[8][0]);
				$ccl->parse(false);
				$cont_cycle .= $ccl->content_file;
				unset($ccl);
				}
			}
			else{//В обратную сторону цикл
				for ($$match[3][0] = $match[4][0]; $$match[3][0] >= $match[5][0]; $$match[3][0] += $match[6][0]){
				$ccl = new template(false, $this, $this->_top);
				$ccl->assigned = $this->assigned;//Передаем потомку назначенные переменные
				$ccl->assigned[$this->filename][$match[3][0]] = $$match[3][0];//Переменная для данного прохода цикла
				$ccl->filename = $this->filename;//Тот же файл
				$ccl->string_template($match[8][0]);
				$ccl->parse(false);
				$cont_cycle .= $ccl->content_file;
				unset($ccl);
				}
			}
		break;//for

		case 'distrib':
			if ($match[4][0] == 'sql'){//Преобразуем в обычный массив
			$arr = array();//Временный массив
				while ($temp = $this->assigned[$this->filename][$match[3][0]]->sql_fetch_assoc()){
				$arr[] = $temp;
				}
			$this->assigned[$this->filename][$match[3][0]] = $arr;
			unset($arr);
			unset($temp);
			}

		//Узнаем на сколько нужно разбить
		preg_match("#{distrib(?:[\s\(]*([\w\.]*)[\s\)]*)(?:[\s\(]*([\w\.\d]*)[\s\)]*)}#si", $match[8][0], $distrib);
			if (!is_numeric($distrib[2])) $distrib[2] = $this->evalute($distrib[2]);//Если не число - пытаемся вычислить
		//Разбиваем
		$this->assigned[$this->filename][$match[3][0]] = array_chunk($this->assigned[$this->filename][$match[3][0]], $distrib[2]);
			//Готовим массив заполнителей
			if (@is_array($this->assigned[$this->filename][$match[3][0]][0][0])){
			$filler = $this->assigned[$this->filename][$match[3][0]][0][0];
				foreach ($filler as $key => $value){
				$filler[$key] = $match[5][0];
				}
			}
			else $filler = $match[5][0];

			//Дополняем "заполнителями"
			if (count($this->assigned[$this->filename][$match[3][0]])>0){//Если есть хоть один елемент, чтобы было что дополнять :)
				for ($i = @count($this->assigned[$this->filename][$match[3][0]][count($this->assigned[$this->filename][$match[3][0]])-1]); $i < $distrib[2]; $i++){
				$this->assigned[$this->filename][$match[3][0]][count($this->assigned[$this->filename][$match[3][0]])-1][] = $filler;
				}
			}

			#Ну а теперь собственно простые циклы по разбитым массивам
			foreach ($this->assigned[$this->filename][$match[3][0]] as $cvar){
			//Строковая замена впринципе быстрее. ТЕПЕРЬ и регистр учитываем
			$str = str_ireplace('{distrib', '{cycle foreach', $match[8][0]);
			$str = str_ireplace('{/distrib', '{/cycle', $str);
			$ccl = new template(false, $this, $this->_top);
			$ccl->assigned = $this->assigned;//Передаем потомку назначенные переменные
			$ccl->assigned[$this->filename][$match[3][0]] = $cvar;//Переменная для данного прохода цикла
			$ccl->filename = $this->filename;//Тот же файл
			$ccl->string_template($str);
			$ccl->parse(false);
			$cont_cycle .= $ccl->content_file;
			unset($ccl);
			}
		break;//distrib
		}//switch

	$this->result($match[0], $cont_cycle);//Замена собственно на содержимое
	}#m cycle

	//Для выполнения локальных замен без долгой обработки шаблона.
	function replace($what, $to){//Например для быстрого внесения изменений в кучу одинаковых файлов.
	$this->content_file = str_replace($what, $to, $this->content_file);
	}#m replace

	function esli(&$match){
$reg = '/
\s*{else\s*(?:'.preg_quote($match[2][0]).')?\s*(?:'.preg_quote($match[3][0]).')?}
/ixm';
	$if_else_str = preg_split($reg, $match[count($match)-1][0], -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
	$ccl = new template(false, $this, $this->_top);
	$ccl->assigned = $this->assigned;//Передаем потомку назначенные переменные
	$ccl->filename = $this->filename;//Тот же файл
		if ($this->evalute($match[2][0], 'bool'))//Собственно главная проверка условия и ветвление
		$ccl->string_template($if_else_str[0]);
		else $ccl->string_template((string)@$if_else_str[1]);#Подавление и приведение на случай отсутствия
	$ccl->parse(false);
	$this->result($match[0], $ccl->content_file);//Замена собственно на содержимое
	unset($ccl);
	}#m esli

	function alt(&$match){
		#Вычисляем, только если в третьем аргументе не указано что-то,
		#явно отменяющее это для скорости!

		if (!$match[3][0]){
		$match[2][0] = $this->varrrs($this->filename, $match[2][0], true);
		}

	#Разделяем поэлементно по {when...}
	$elements = preg_split('/(?:{\/?alt.*?}\s*)|(?:\s*{(when\+?) (.+?)})|(?:\s*({dflt}))/i', $match[0][0], -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		#Теперь проходим по всем и ищем нужный элемент
	$i = 0; $count = count($elements); $found = false;
		while (!$found and $i<$count and $elements[$i] != '{dflt}'){
			if ( ($elements[$i] == 'when'	and $elements[$i+1] == $match[2][0]) or ($elements[$i] == 'when+' and $this->evalute($elements[$i+1]) == $match[2][0]) ){#Он родимый
			$found =& $elements[$i+2];
			}
		$i+=3;
		}

		#Теперь заменим либо на найденное, либо на дефолтное, либо на пустое
		if (!$found){#Не найден
			if (@$elements[$i] == '{dflt}'){#Но есть дефолтное значение
			$found =& $elements[$i+1];
			}
			else{#Нихрена нету, пустым заменим
			$found = '';
			}
		}
		#Теперь полюбому можно заменять!
		$ccl = new template(false, $this, $this->_top);
		$ccl->assigned = $this->assigned;//Передаем потомку назначенные переменные
		$ccl->filename = $this->filename;//Тот же файл
		$ccl->string_template($found);#Содержимое
		$ccl->parse(false);
		$this->result($match[0], $ccl->content_file);//Замена собственно на содержимое
		unset($ccl);
	}#m esli

	function evalute($what, $mode = false){//Функция вычисления параметра из строки
	#Глобализуем (для функции) все требующиеся переменные, чтобы проверить автоматически условие
	preg_match_all("#\\$([a-z][\w\d]*)#i", $what, $globalize, PREG_PATTERN_ORDER);
		foreach ($globalize[1] as $posible_var){
			if (isset($this->assigned[$this->filename][$posible_var])) $$posible_var = $this->assigned[$this->filename][$posible_var];
		}
	unset($globalize);//Хорошо бы конечно и созданные глобалы очистить, ну да фиг с ними...

	$error_level = error_reporting (0);//Отключим Нотайсы в любом случае
		switch ($mode){
		case 'bool'://Выполнение с приведением к типу BOOL
		@eval("\$evalute_result = (bool)(".$what.");");
		break;

		case 'string':
		eval('$evalute_result = "'.$what.'";');
		break;

		default:
		$track_errors = ini_set('track_errors', 1);  #Сохраняем старое, включаем отслеживание
		$php_errormsg = '';
		eval("\$evalute_result = ( $what );");
		ini_set('track_errors', (int)$track_errors); #Возвращаем предыдущее значение
		     #Если ноль (0), и были неопределенные константы, то лучше вернем исходную строку!
			#чтобы исключить преобразование undefined constant
			if ($evalute_result == 0 and $php_errormsg){
			$this->error[] = "WARNING: Переменная или Константа с именем $what не определена/не вычислилась";
			$evalute_result = $what;
		     }
		}
	error_reporting ($error_level);//Возвращаем уровень вывода ошибок в предыдущее значение

	return @$evalute_result;
	}#m evalute

	function get_var($txt_var, $txt_arr){
	//$txt_var - текстовое представление переменной, ex: $var['name']
	//$txt_arr - текст имени массива из которого ее нужно получить, ex: $this->assigned['var']['name']
	//Возвращаетс полное ТЕКСТОВОЕ описание переменной на искомую переменную.
	preg_match("#^[\$]{0,1}(\w*)(.*)#si", $txt_var, $vr);//Разбираем на имя и все остальное
	$vr[2] = preg_replace('#\$[^\'\"\[\s]*?\[[^\]]*\](?:\[[^\]]*?\])?(?:\[[^\]]*?\])?#i', '{\\0}', $vr[2]);//" Теперь в оставшейся части все массивы заключаем в {}. Максимум третьей размерности.
	$var = $txt_arr.'["'.$vr[1].'"]'.$this->evalute($vr[2], 'string');//Собственно компануем
	return $var;
	}#m get_var

	function WYSIWYG(&$match){
	#$name, $style = 'styles.css'
	#$num - Номер размещаемого редактора: 0 - первый и единственный, 1 - первый, 2 - второй...
		#Если не задано ничего в данную переменую, то дефолтовый текст
		if	(	$match[2][0] != ($content = $this->varrrs($this->filename, $match[2][0], true)))
		$content = $this->RTESafe($content);
		else $content = '<p>Текст вводить сюда. Чтобы вставить текст из буфера обмена нажмите Ctrl+v';

		if (!($name = $match[4][0]) or $name == '""' or $name == '"') $name = $match[2][0]; #"
		#Следующие две величины вычисляем и как выражение и как строку, и если результат не приводится к числу - ставим поумолчанию
		if	(	!$match[5][0]	#Вообще есть
				or
				$match[5][0] == '""'	#Не формально только
				or
				!(
					(int)$width = $this->varrrs($this->filename, $match[5][0], true)
					or
					(int)$width = $this->evalute($match[5][0], 'string')
				)
			){
		$this->error[] = "NOTICE: Указанная ширина редактора {$match[5][0]} некорректна, использовано значение поумолчанию - 520px";
		$width = 520;
		}
		if	(	!$match[6][0]
				or
				$match[6][0] != '""'
				or
				!(
					(int)$height = $this->varrrs($this->filename, $match[6][0], true)
					or
					(int)$height = $this->evalute($match[6][0], 'string')
				)
			){
		$this->error[] = "NOTICE: Указанная высота редактора {$match[6][0]} некорректна, использовано значение поумолчанию - 200px";
		$height = 200;
		}

		if(!is_readable($match[3][0])){//Определяем файл стилей
			if (!is_file(dirname($this->filename).'/'.$match[3][0]) or !is_readable(dirname($this->filename).'/'.$match[3][0])){//Посмотрим в той же директории
			$style = 'styles.css';
			$this->error[] = "NOTICE: Файл стилей {$match[3][0]} не найден, использован поумолчанию styles.css";
			}
			else $style = dirname($this->filename).'/'.$match[3][0];
		}
		else $style = $match[3][0];

		//$rte = 'rte'.substr(microtime(), 2, 8);;//Всегда уникальное имя для JavaScript!
		if (!$this->wysiwyg or $this->wysiwyg_full){//Выводим первый со всей инициализацией
			//Если уже включали и принудительно полная инициализация, скрипт подключать в любом случе не нужно!
			if (!$this->wysiwyg) $rep_value = "<script language='JavaScript' type='text/javascript' src='wysiwyg/richtext_compressed.js' charset='iso-8859-1'></script>";
		@$rep_value .= "
		<script language='JavaScript' type='text/javascript'>
		<!--
		//Usage: initRTE(imagesPath, includesPath, cssFile). Инициализация.";
			//Если уже проинициализирован редактор - второй раз не нужно
			if (!$this->wysiwyg) $rep_value .= "\ninitRTE('wysiwyg/images/', 'wysiwyg/', '$style');";
		@$rep_value .= "
		//Usage: writeRichText(fieldname, html, width, height, buttons, readonly). Собственно вывод окна редактора.
		writeRichText('$name', '$content', '$width', '$height', true, false);

		//Настройка обновления для отправки данных в форме
		var ua = navigator.userAgent.toLowerCase();
		isIE = ((ua.indexOf('msie') != -1) && (ua.indexOf('opera') == -1) && (ua.indexOf('webtv') == -1));
		isGecko = (ua.indexOf('gecko') != -1);

		owner_form = document.getElementById('$name').previousSibling;//Ищем содержащуюю форму
			do{//Придется так, а не просто parentNode, потому что, если form вложен в table то форма не будет предком.
			while ((owner_form.previousSibling) && (owner_form.tagName != 'FORM')){
			owner_form = owner_form.previousSibling;
			}
			}while ((owner_form.tagName != 'FORM') && (owner_form = owner_form.parentNode));

		owner_form.onsubmit = updateRTEs;
		-->
		</script>
		<noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>
		";
		$this->wysiwyg = true;
		}
		else{//Последующие
		$rep_value = "
		<script language='JavaScript' type='text/javascript'>
		<!--
		writeRichText('$name', '$content', $width, $height, true, false);
		//-->
		</script>
		<noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>
		";
		}
	$this->result($match[0], $rep_value);
	}#m WYSIWYG

	function _(&$match){//Комментарии  - их просто нужно убрать из результирующего кода
	$this->result($match[0], $ttt = '');#Там ссылка треба
	}#m _
	#Функция собственно разбирающая шаблон.
	#Основная рабочая лошадка
	function parse($printout = true){
$rega = "#{
([^\s\{\}]+)
(?>\s*)
((?:\'(?>[^\'])\')|[^\s\{\}]*)
(?>\s*)
((?:\'(?>[^\'])\')|[^\s\{\}]*)
(?>\s*)
((?:\'(?>[^\'])\')|[^\s\{\}]*)
(?>\s*)
((?:\'(?>[^\'])\')|[^\s\{\}]*)
(?>\s*)
((?:\'(?>[^\'])\')|[^\s\{\}]*)
(?>\s*)
((?:\'(?>[^\'])\')|[^\s\{\}]*)
}(?:(.+?)(?:{/\\1(?:(?>\s*)\\2)?(?:(?>\s*)\\3)?}))?#si";

	preg_match_all(str_replace("\n", '', $rega), $this->content_file, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
#	PREG_OFFSET_CAPTURE теперь будем его использовать для замены!!
		#собственно вызываем известные функции, подключаем модули налету
		foreach ($matches as $match){
			if (array_key_exists($match[1][0], $this->functions)){//Если есть функция, то вызываем ее
			$this->$match[1][0]($match);
			}
			else if ($match[1][0][0] == '+'){
			$pMod = substr($match[1][0], 1, strlen($match[1][0]));	#Possible_Module
				if (!function_exists($pMod)){
					if( !($module = $this->find_path($pMod.'.tmod', TEMPLATE_MODULES_SUBDIR)) )
					exit($this->error[] = 'FATAL ERROR: Заявленный модуль <b>'.$pMod.'</b> не найден!');
					else include_once($module);
				}
			#Теперь полюбому вызов. Функция модуля теперь обязательно глобальна
			$this->result($match[0], $pMod($this, $match));
			}
			else $this->varrrs($this->filename, $match, false);//Значит просто заменяем переменные
		}

		if (TEMPLATE_GENTIMECALC) $this->gentime = $this->_gentime->stop();
		if ($printout) $this->printout();//Выводим результат сразу, если это задано
	}#m parse

	function printout(){
	echo $this->content_file;
		if (TEMPLATE_DEBUG){
			if (!empty($this->error)){
			echo '<hr><hr>Ошибки при парсинге шаблона: <i>'.$this->filename.'</i>';
			dump($this->error);
			}
			if (TEMPLATE_GENTIMECALC) echo '<p style="color:green; text-align: center">Время обработки шаблона: '.$this->gentime.' сек.</p>';
		}
	}#m printout

	function scheme($printout = true, $scheme = TEMPLATE_DEFAULT_SCHEME){
		if (TEMPLATE_DEFAULT_SCHEME_DIR) $schemeDir = TEMPLATE_DEFAULT_SCHEME_DIR;
		else $schemeDir = dirname($this->filename);
	#Сначала сам распарсим, а потом уже заменим (быстрее по частям поидее)
	$this->parse(false);

	$schm = new template($schemeDir.'/'.$scheme, $this, $this->_top);	#Проверки пути есть в конструкторе
	//Provide assigments
	@$schm->assigned[$schm->filename] = (array)$this->assigned[$this->filename];
	$schm->replace('<!--<<'.basename($scheme, '.scheme').'>>-->', $this->content_file);
	$schm->parse(false);

	$this->content_file =& $schm->content_file;
	unset($ccl);

		if (TEMPLATE_GENTIMECALC) $this->gentime = $this->_gentime->stop();
		if ($printout)	$this->printout();
	}#m scheme

	function message($message, $printout = true, $scheme = TEMPLATE_DEFAULT_SCHEME){
	$this->filename = $this->load_file(TEMPLATE_MESSAGE);
	$this->assign('MESSAGE', $message);
	$this->scheme($printout, $scheme);
	}#m message

	#Заменям результат выполнения всех функций
	function result(&$match, &$towhat){
	$this->content_file = substr_replace($this->content_file, $towhat, $match[1] + $this->_padRes, $firstLen = strlen($match[0]));
	#Корректируем сдвиг
	$this->_padRes += strlen($towhat) - $firstLen;
	}#m result

	function RTESafe($strText){//Для WYSIWYG
	//returns safe code for preloading in the RTE
	$tmpString = trim($strText);
	//convert all types of single quotes
	$tmpString = str_replace(chr(145), chr(39), $tmpString);
	$tmpString = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString); #'

	//Это нужно для скриптов иначе в редактор не записать
	$tmpString = preg_replace('/<script/im', "<' + 'script", $tmpString);
	$tmpString = preg_replace('/<\/script/im', "<' + '/' + 'script", $tmpString);

	//convert all types of double quotes
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);

	//replace carriage returns & line feeds
	$tmpString = preg_replace('/\r?\n/im', '<nn>', $tmpString);

	//пробелы кода в пробелы "СВОИ"
	$tmpString = preg_replace('/ {2,}/ie', "'<sps n='.strlen('\\0').'></sps> '", $tmpString);
	return $tmpString;
	}#m RTESafe

	function RTEShortHTML($src){//Чистка кода и приведение его в "нормальный", приемлемый вид
	$src = stripslashes($src);
	$tmp = preg_replace("[<br>\r*?\n(</.*?>)*?</p>]si","$1",$src);
	$tmp = preg_replace("[<p>\r*?\n</p>]si","",$tmp);
	$tmp = preg_replace("[</p><p>]si","<p>",$tmp);
	$tmp = preg_replace("[\r*?\n</p>]si","",$tmp);

	$tmp = preg_replace("[<span style=\"font-weight: bold;\">(.*?)</span>]si","<b>$1</b>",$tmp);
	$tmp = preg_replace("[<span style=\"font-style: italic;\">(.*?)</span>]si","<i>$1</i>",$tmp);
	$tmp = preg_replace("[<span style=\"text-decoration: underline;\">(.*?)</span>]si","<u>$1</u>",$tmp);
	$tmp = preg_replace("[<span style=\"text-decoration: underline;\">(.*?)</span>]si","<u>$1</u>",$tmp);

	$tmp = preg_replace("[<p style=\"text-align: left;\">]si","<p class=left>",$tmp);
	$tmp = preg_replace("[<p style=\"text-align: right;\">]si","<p class=right>",$tmp);
	$tmp = preg_replace("[<p style=\"text-align: center;\">]si","<p class=center>",$tmp);
	$tmp = preg_replace("[<p style=\"text-align: justify;\">]si","<p>",$tmp);

	$tmp = preg_replace("[<hr style=\"width: 100%; height: 2px;\">]si","<hr>",$tmp);

	$tmp = preg_replace("[<li>\r*?\n\s*?<p>(.*?)</p>\r*?\n\s*?</li>]six","<li>$1",$tmp);
	$tmp = preg_replace("[<br>&nbsp;&nbsp;&nbsp; ]six","\n<p>",$tmp);
	$tmp = preg_replace("[<br>\r*?\n\s*?</li>]six","",$tmp);

	$tmp = preg_replace("[</nn>]six","",$tmp);//Это некоторые пролазят из-за моих манипуляций со структурой исходников
	return $tmp;
	}#m RTEShortHTML
}//c template

##Для совместимости с более ранними версиями, эмуляция:
	if (!function_exists('file_get_contents')){
		function file_get_contents ($filename, $includes = true){
		$str = fread($fp = fopen($filename, 'rb', $includes), filesize($filename));
		fclose($fp);
		return $str;
		}
	}
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-10-22 19:03
**/

/**
* wordwrap standard function wrap by amount *bytes*, not *chars*!
* Idea got from http://ru.php.net/manual/en/function.wordwrap.php#92577 but its implementation
* don't work. Reimplemented.
*
* @param	string	$str	String to wrap
* @param	integer=75	$len	Length to wit in.
* @param	string=\n	$break	String to place on end
* @param	boolean=false	$cut	Cut words or not (Default false, to wrap by word boundary).
* @return	string
**/
function unicode_wordwrap($str, $len = 75, $break = "\n", $cut = false){
	/*
	* {{ - one treated by PHP
	* "|.{1,$len}$" part to add $break also to end of string, because another regexp always do that and we just will cut it
	**/
	if($cut) $reg = $reg = "#(.{{$len}}|.{1,$len}$)#us";
	// "|$" part needed because if it is absent tail processed incorrectly (last word is not counted)
	else $reg = "#(.{1,$len})(?:[^\pL]|$)#us";
	return substr(preg_replace($reg, "\\1$break", $str), 0, -strlen($break));// Cut off last $break. In both cases it is always must be present.
}#f unicode_wordwrap
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-10-23 16:43
**/

/**
* Unicode variant of ucfirst!
* Idea got from http://php.net/manual/en/function.ucfirst.php#87133 but its implementation
* is very long and hard without reason.
*
* @uses	mb_strtoupper
* @param	string	$str	String to process
* @param	string=UTF-8	$enc
* @return	string
**/
function unicode_ucfirst($str, $enc = 'UTF-8'){
	return preg_replace('/^./ue', "mb_strtoupper('\\0', '$enc')", $str);
}#f unicode_ucfirst
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @subpackage _count
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Terminate program with message $message if count exceeded $count.
* @param integer	$count	Count compare to. {@see hit_count()}
* @param string	$message=''	Optional message to die with.
*
* @return	void
**/
function exit_count($count, $message=''){
	if (true === hit_count($count)) exit($message);
}

/**
* Calc hit of invokes and return === true if it equals to $count, else return number of current hit.
* @param integer	$count Count to compare.
*
* @return	bool|integer
**/
function hit_count($count){
static $_count = 0;
	if (++$_count == $count) return true;
	else return $_count;
}
?><?
/**
* Toolkit of small functions aka "macroses".
* eecho macros.
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-12-14 12:10
**/

/**
* eecho like echo, but write to stderr instead of stdout.
*
* @uses	mb_strtoupper
* @param	string	$str	String to out
* @return	boolean
**/
function eecho($str){
	return file_put_contents('php://stderr', $str);
}#f eecho
?><?
/**
* Toolkit of small functions aks "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Swap values of two vars
* @param	&$one	First var
* @param	&$two	Second var
*
* @return	void
**/
function SWAP(&$one, &$two){
$_tmp = $two;

$two = $one;
$one = $_tmp;
}
?><?
/**
* Toolkit of small functions aka "macroses".
* DEBUG version
*
* @package Macroses
* @version 1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-29 19:55 version 1.2 from 1.0
*
* @uses VariableRequiredException
* @uses backtrace
**/

/**
* Thows {@see VariableRequiredException) if !$var ({@link http://ru2.php.net/manual/ru/types.comparisons.php}).
* In constructor of VariableRequiredException passed object(backtrace).
* Otherwise return ref to var (&ref).
* This is usefull in direct operations like assigment, or other. F.e:
*	$this->settings = REQUIRED_VAR($settings);
*
* @param	&mixed	$var	Variable to test.
* @param	string	$varname	If present, initialise them arg of Tokenizer, else real parse.
* @return &mixed
* @Throws(VariableRequiredException)
**/
function &REQUIRED_VAR(&$var, $varname = null){
	if (!$var){
		throw new VariableRequiredException(
			new backtrace(),
			$varname,
			'Variable required'
		);
	}
	else return $var;
}
?><?
/**
* Toolkit of small functions aka "macroses".
* DEBUG version
*
* @package Macroses
* @version 1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-29 19:55 version 1.2 from 1.0
*
* @uses VariableIsNullException
**/

/**
* Thows {@see VariableIsNullException) if is_null($var)
* In constructor of VariableIsNullException passed object(backtrace).
* Otherwise return ref to var (&ref).
* This is usefull in direct operations like assigment, or other. F.e:
*	$this->settings = REQUIRED_VAR($settings);
*
* @param	&mixed	$var	Variable to test.
* @param	string	$varname	If present, initialise them arg of Tokenizer, else real parse.
* @return &mixed
* @Throws(VariableIsNullException)
**/
function &REQUIRED_NOT_NULL(&$var, $varname = null){
	if (is_null($var)){
		throw new VariableIsNullException(
			new backtrace(),
			$varname,
			'Variable required'
		);
	}
	else return $var;
}
?><?
/**
* Function to simulate multiple inheritance in PHP. Based on possibilities of extension runkit.
* Based upon http://rudd-o.com/archives/2006/03/18/revisiting-multiple-inheritance-in-php/
*
* @param	string	$destClassName Class which are inherits from other.
* @param	array	$srcClassNameList Array of class names (strings) to inherit from.
* @return	void
* @example MultipleInheritance.example.php
**/
function inherits_from($destClassName, array $srcClassNameList) {
	foreach ($srcClassNameList as $s) {
	@runkit_class_adopt($destClassName,$s);
	}
}#f inherits_from
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-01 13:57
**/

/**
* Intended to USE ANYWERE INSTEAD OF ISSET!!!
* Return TRUE isset, correct handling strings opposite official version.
*
* See PHP bugs:
* http://bugs.php.net/bug.php?id=43889
* http://bugs.php.net/bug.php?id=38165
* http://bugs.php.net/bug.php?id=29883
* http://bugs.php.net/bug.php?id=26413
*
* Thay want think this is "future" (I think it is a BUG!) of PHP.
* When we want check presented key in array we MUST do there 3 checks if check in array due to the
* ($str = 'text'; isset($str['any index']) <- Always return true!!!)
* instead of just check: if ( isset($GLOBALS['__CONFIG'][$classname]['class_file']) ) :
*	isset($GLOBALS['__CONFIG'][$classname])
*	and is_array($GLOBALS['__CONFIG'][$classname])
*	and isset($GLOBALS['__CONFIG'][$classname]['class_file'])
* OR use this dirty hack, which shortly, and does the same:
*	in_array('class_file', (array)@$GLOBALS['__CONFIG'][$classname])
* OR can check on string...
*
* Last variant is safele for all cases as I think. So, implement it.
*
* @param string	$what Key
* @param &(string|array)	$where Where check.
* @return boolean
**/
function is_set($what, $where){// Opposite to standard isset.
	if (is_string($where) and !is_numeric($what)) return false;
	else return isset($where[$what]);
}
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-01-30 15:10
**/

/**
* Return value of SCALAR variable if it defined without notices and error-handling.
* For safely check indexes (in string and arrays use {@see IS_SET_VAR})
*
* In most cases check like "if ($variable) $str = $variable . 'some'" is laconic form of more strict like "if (isset($variable) and $variable) $str = $variable . 'some'".
* So, if $variable was not defined yet we got notice. Well, when we do not need it, we can suppress it like "if (@$variable)"
* all seems good on first glance but we only supress error message, NOT error handling if it occures!
* So, if error handler was be set before (like set_error_handler("func_error_handler");) this error handler got control and stack will be broken!
*
* With that function we may safely use simple: $str = ISSET_VAR($variable) . 'some'...
*
* For Chec
*
* @param &mixed	$var variable amount of arguments.
* @return &mixed
**/
function &ISSET_VAR(&$var){
	if (isset($var)) return $var;
	else{
		$t = null; //To do not fire error "Only variables can be passed by reference in ..."
		return $t;
	}
}

function &IS_SET_VAR($what, &$where){
	//MUST be explicit. It used in autoload.php, so, autoloading is not present yet!

	if (is_set($what, $where)) return $where[$what];
	else{
		$t = null; //To do not fire error "Only variables can be passed by reference in ..."
		return $t;
	}
}
?><?
/**
* Throw exceptions 
* @param
* @return
*/
function INT_CHECK_RANGE($int, array $range){

}#f INT_CHECK_RANGE
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @since 2010-04-03 00:31
**/

/**
* Return first argument after @call forr what true === call($arg). Nothing return otherwise (yes, warning will).
* It is logick continue of set macroses like EMPTY_INT, EMPTY_STR, EMPTY_VAR...
*
* @param	callback	$call
* @params	variable amount of arguments to check.
* @return mixed
**/
function EMPTY_callback($call){
$numargs = func_num_args();
$i = 1; //0 is callback
	while (
		$i < $numargs
		 and
		!($res = call_user_func($call, $arg = func_get_arg($i++)))
	){/*Nothing do, just skip it */}

	if ($res) return $arg;
}#f EMPTY_callback
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Return first NON-empty var if present. Nothing return otherwise (yes, warning will).
* @params	variable amount of arguments.
* @return mixed
**/
function EMPTY_VAR(){
$numargs = func_num_args();
$i=0;
	while (
		$i < $numargs
		 and
		!($res = func_get_arg($i++))
	){/*Nothing do, just skip it */}

	if ($res) return $res;
}
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-13 12:18 ver 1.0 to 2.0
**/

/**
* Return first NON-empty string if present. Silent return empty string "" otherwise.
*
* WARNING! This macros operate by *strings*. In particular case it means are:
*	1) What null/false and even *TRUE* values threated as EMPTY *STRINGS* and default
*		value will be returned!
*	2) Opposite it, integer 0 fails this check end go to default value, what it also
*		is not what was prefered. We handle "0" correctly too as "NON EMPTY STRING"
*	3) Macros do not intended to use with arrays, but PHP has internal support conversion its
*		to 'Array' string. It is usefull. BUT, nevertheless unfortunately empty
*		array() converted into empty string! To cast into single form, all arrays
*		converted into string like "Array(N)" where N is count of elements.
*
* @example EMPTY_STR.example.php
*
* @params	variable amount of arguments.
* @return	string
**/
function EMPTY_STR(){
	$numargs = func_num_args();
	$i = 0;
	$str = null;

	do{
		$str = func_get_arg($i++);
	}
	while (
		!(//Most comples check. See explanation in PhpDoc
			(//It must be first check, because non-empty array simple check evaluated into true.
				is_array($str) //Explicit check, even it is EMPTY array
				and
				($str = 'Array(' . count($str) . ')')	// Assign in condition
			)
			or
			(
				true === $str	// False and null values self converted to empty string and do not require futher checks
				and
					(
					// Assign in condition and explicitly return true, because '' is false as empty string
					$str = ''
					or
					true
					)
			)
			or
			0 === $str		// Integer 0 is string "0" but evaluated in empty by previous check
			or
			$str				// Last generick check after all special cases!
		)
		and
		$i < $numargs //In do-wile it must be last
	);
	return (string)$str;
}#f EMPTY_STR

/**
* If provided argument $str is not empty *string* then return "$prefix.$str.$suffix" otherwise $defValue
*
* WARNING! this macros operate by *STRINGS*, so, it is handle several values such as 0, true, Array() by special way.
* To determine of string "empting" it is fully relyed on {@see EMPTY_STR()}. Please se it for more details.
*
* @example EMPTY_STR.example.php
*
* @param	string $str
* @param	string $prefix
* @param	string $suffix
* @param	string $defValue
* @return	string
**/
function NON_EMPTY_STR(&$str, $prefix='', $suffix='', $defValue=''){
	return ( strlen(($str = EMPTY_STR($str))) > 0 ? $prefix.$str.$suffix : $defValue);
}#f NON_EMPTY_STR
?><?
function EMPTY_INT (&$str, $defValue=0, $defValue2=0){
return ( @$str ? (int)$str : ($defValue ? (int)$defValue : (int)$defValue2 ));
}
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Return name of first defined class from names comes as arguments. Nothing return otherwise (yes, warning will).
*
* Common usage: $classname = DEFINED_CLASS('some_child', 'base'); $obj = new $classname();
*
* @params	variable amount of arguments - strings of class name to try.
* @return	string|null
**/
function DEFINED_CLASS(){
$numargs = func_num_args();
$i=0;
	while (
		$i < $numargs
		 and
		!( $res = class_exists($classname = func_get_arg($i++)) )
	){/*Nothing do, just skip it */}

	if ($res) return $classname;
}
?><?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Assign value of variable if value not (bool)false.
* @param	&mixed	$var
* @param	&mixed	$value
* @return void
**/
function ASSIGN_IF(&$var, &$value){
	if ($value) $var = $value;
}#f ASSIGN_IF
?><?
/**
* Old classes for images.
*
* @package Image
* @subpackage GD
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ???
* @deprecated
*
* @uses GDimage
**/



class logo extends GDimage{
	public $DEBUG = false;
	public $gp = null; //Getted Parameters
	//Массив формата входного $p из savePic, с добавленными:
	//	["wratio"]=>float(0.11647254575707) = Ширина картинки на которой выбирали, деленный на ее реальный размер
	//	["hratio"]=>float(0.11755952380952) = То же для высоты
	public $pp = null;	//Parsed Parameters. //Это будет массив новых парметров для конвертации:
/* Формат полностью адаптирован под imagecopyresized и imagecopyresampled библиотеки GD:
array(10) { (В скобках примеры, а не длины форматов!!)
["src_w"]=>float(601)
["src_h"]=>float(672)
["dst_w"]=>float(54)
["dst_h"]=>float(61)
["dst_x"]=>float(16)
["src_x"]=>int(0)
["dst_y"]=>float(7)
["src_y"]=>int(0)
["d_w"]=>float(59)
["d_h"]=>float(23)
} */

	protected function logoForm(){
		$tmpl = new template('logoForm.tmpl');
		$tmpl->scheme();
	}#m logoForm

	/*
	$p = Это собственно параметры, переданные от дрыгания картинки,
	должен быть массив следующего формата (внимание, даны примерные значения, а не длины форматов!):
	array(12){
	["left"]=>string(3) "-20"
	["top"]=>string(2) "-8"
	["width"]=>string(3) "129"
	["height"]=>string(3) "102"
	["PWidth"]=>string(3) "101"
	["PHeight"]=>string(2) "80"
	["pswidth"]=>string(2) "70"
	["psheight"]=>string(2) "79"
	["src"]=>string(48) "http://webserver/admin/.cache/logo/phps4PHTg.png"
	["fillColor"]=>string(7) "#007f00"
	["wratio"]=>float(0.11647254575707)
	["hratio"]=>float(0.11755952380952)
	}*/
	public function savePic($p){
		$this->gp = &$p;
		if ($this->DEBUG) dump($this->gp, '', 'Переданные параметры:');

		$this->createTrueColor($this->gp['PWidth'], $this->gp['PHeight']);

		$this->srcimg = new GDimage;
		$this->srcimg->getFile($this->gp['src']);

		$this->calcDimensions(); //Посчитаем размеры ресайза

		//Зальем, если был выбран цвет!
		if (@$this->gp['fillColor']) $this->fill($this->gp['fillColor']);

		//Собственно главный ресайз, когда уже все рассчитано!
		$this->resize($this->srcimg, $this->pp['dst_x'], $this->pp['dst_y'], $this->pp['src_x'], $this->pp['src_y'], $this->pp['dst_w'], $this->pp['dst_h'], $this->pp['src_w'], $this->pp['src_h']);
	}#m savePic

	protected function calcDimensions(){
		//СЧИТАЕМ все размеры, включая заходы рамок
		$this->gp['wratio'] = $this->gp['pswidth'] / $this->srcimg->width();	//Коррекция, если изображение пользователю представлено
		$this->gp['hratio'] = $this->gp['psheight'] / $this->srcimg->height();	//в неоригинальном размере

		//Инициализация, дальше, если надо скорректируем отрицательное
		$this->pp['src_w'] = ceil(($this->gp['width']/$this->gp['wratio']));
		$this->pp['src_h'] = ceil(($this->gp['height']/$this->gp['hratio']));

		$this->pp['dst_w'] = $this->gp['PWidth'];
		$this->pp['dst_h'] = $this->gp['PHeight'];

		if ($this->DEBUG) dump($this->pp,'','Параметры ресайза начальные:');

		//Корректируем все входные величины, в пересчете на эту попроавку:
		//Если рамка была _левее_ реальной картинки
		if ( $this->gp['left'] < 0 ){
			//Сдвигаем, учитывая масштаб координат
			$this->pp['dst_x'] = ceil(-$this->gp['left'] * $this->gp['PWidth'] / $this->gp['width']);
			$this->pp['src_x'] = 0;
		}
		else{//As is but Proportial
			$this->pp['src_x'] = ceil($this->gp['left'] / $this->gp['wratio']);
			$this->pp['dst_x'] = 0;
		}

		//Если рамка была _выше_ реальной картинки
		if ( $this->gp['top'] < 0 ){
			//Сдвигаем, учитывая масштаб координат
			$this->pp['dst_y'] = ceil(-$this->gp['top'] * $this->gp['PHeight'] / $this->gp['height']);
			$this->pp['src_y'] = 0;
		}
		else{
			$this->pp['src_y'] = ceil($this->gp['top'] / $this->gp['hratio']);
			$this->pp['dst_y'] = 0;
		}

		//Если _правее_ картинки выбор, обе координаты (src, dst) сдвигаем
		if ( ($this->pp['d_w'] = ceil( ($this->gp['left']<0? 0 : $this->gp['left']) + $this->gp['width'] - $this->gp['pswidth'])) > 0 ){
			//Сдвигаем, учитывая масштаб координат
			//$this->pp['d_w'] = ceil( ($this->gp['left']<0? 0 : $this->gp['left']) + $this->gp['width'] - $this->gp['pswidth'] );
			$this->pp['src_w'] -= ceil($this->pp['d_w'] / $this->gp['wratio']);
			$this->pp['dst_w'] -= ceil($this->pp['d_w'] * ($this->gp['PWidth'] / $this->gp['width']));
		}

		//Если _ниже_ картинки выбор, обе координаты (src, dst) сдвигаем
		if ( ($this->pp['d_h'] = ceil( ($this->gp['top']<0? 0 : $this->gp['top']) + $this->gp['height'] - $this->gp['psheight'])) > 0 ){
			//Сдвигаем, учитывая масштаб координат
			$this->pp['src_h'] -= ceil($this->pp['d_h'] / $this->gp['hratio']);
			$this->pp['dst_h'] -= ceil($this->pp['d_h'] * ($this->gp['PHeight'] / $this->gp['height']));
		}
		//\Теперь СЧИТАЕМ все размеры, включая заходы рамок

		if ($this->DEBUG) dump($this->pp,'','Параметры ресайза конечные:');
	}#m calcDimensions

	protected function upLoadUserImg($color){
	}#m upLoadUserImg
}#c logo
?><?
/**
* Old classes for images.
*
* @package Image
* @subpackage GD
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ???
* @deprecated
*
* @uses dump
* @uses BaseException
* @uses FilesystemException
**/

class ImageException extends BaseException{}
class ImageUploadException extends ImageException{}

class GDimage{
	protected $capabs = array();
	protected $strimg;	// Сохраненная строка изображения
	protected $img;	// Текущее изображение, ресурс

	protected $PARAM = array();
	protected $TYPE = '';

	// $this->UPLOADinfo, array установится в случае аплоада, если все проверки выше прошли
	public $UPLcheck = array(
		'maxIMGsize' => 204800,	//Byte
		'maxIMGwidth' => 1024,	//Pixels
		'maxIMGheight' => 768,	//Pixels
		'typeAllow' => array('image/gif', 'image/jpeg', 'image/png', 'image/bmp', 'image/vnd.wap.wbmp')
	);

	function __construct (){
		$this->getCapabs();
	}#__c

	protected function getCapabs(){
		$this->capabs = gd_info();
		//Вычленение версии из строки типа 'bundled (2.0.28 compatible)'
		preg_match('/[^\d]*(\d+)(\.\d+)?(\.\d+)?/i', $this->capabs['GD Version'], $m);
		unset($m[0]);
		$this->capabs['GD Version'] = implode('', $m);
	}#m getCapabs

	public function createTrueColor($width, $height){
		if (!($this->img = imageCreateTrueColor($width, $height))){
			throw new ImageException('ERROR: Не удалось создать изображение!');
		}
		$this->PARAM['w'] = $width;
		$this->PARAM['h'] = $height;
	}#m createTrueColor

	public function getFile($file){
		if (!($this->strimg = @file_get_contents($file))){
			throw new FilesystemException('ERROR: Получить картинку из файла не удалось!!');
		}
	return $this->imgINIT();
	}#m getFile

	public function imgFromString($string){
		$this->strimg = $string;
		$this->imgINIT();
	}#m imgFromString

	/**
	* $srcobj - объект класса GDimage (или производных)
	* Остальные параметры как у imagecopyresized:
	* int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	**/
	public function resize($srcobj, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $dst_w, $dst_h, $src_w = null, $src_h = null){
		// DEFAULT
		if (!$src_w) $src_w = $this->PARAM['w'];
		if (!$src_h) $src_h = $this->PARAM['h'];

		if ($this->capabs['GD Version'] > 1) //GD 2.0.xx
			$func = 'imagecopyresampled';
		else// GD 1.xx
			$func = 'imagecopyresized';

		if (call_user_func($func, $this->img, $srcobj->getResource(), $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)){
			$this->PARAM['w'] = $dst_w;
			$this->PARAM['h'] = $dst_h;
		}
		else throw new ImageException('ERROR: Изменить размеры картинки не удалось!!');
	}#m resize

	/**
	* Ака resize, только всей картинки, и с соблюдением пропорций
	**/
	public function preview($width, $height){
		if ($this->PARAM['w'] > $this->PARAM['h']){//Соблюдаем пропорции
			$dst_width = $width;
			$dst_height = ceil($this->PARAM['h'] * $dst_width / $this->PARAM['w']);
		}
		else{
			$dst_height = $height;
			$dst_width = ceil($this->PARAM['w'] * $dst_height / $this->PARAM['h']);
		}

		$this->resize($this,0,0,0,0,$dst_width,$dst_height);
	}#m preview

	/**
	* Будем разбирать заданный цвет для данного изображения, его
	* НЕОБХОДИМО получать для конкретного изображения:
	* imagecolorallocate() must be called to create each color that is to be used in the image represented by image
	* Входить может так: AABBCC, #aabbcc, #AABBCC, aabbcc если в HEX
	* Или так, если в DEC: 000111255 (по 3 символа, до 255, С ведущими нулями)
	**/
	protected function parseUserColor($color){
		if (strlen($color) == 7) $color = substr($color, 1, 6);
		return hexdec($color);
	}#m parseUserColor

	/**
	* $color строка вида FF10BB
	**/
	public function fill ($color, $x=0, $y=0){
		if (!imagefill($this->img, $x, $y, $this->parseUserColor($color))){
			throw new ImageException('ERROR: Не удалось залить изображение!');
		}
	}#m fill

	/**
	* $type одно из 'GIF', 'JPG', 'PNG', 'WBMP', 'XBM' больше GD вроде ничего не поддерживает :)
	**/
	public function convertTo($type){
		if ($type == 'GIF')//Для него отдельная проверка на запись и на чтение
			$ind = 'GIF Create Support';
		else $ind = $type.' Support';

		if (!@$this->capabs[$ind]){
			throw new ImageException('ERROR: Версия библиотеки GD не поддерживает создание '.$type.' файлов');
		}
		else{
			if ($type = 'JPG') $type = 'JPEG';// функция imagejpeg зовется

			ob_start();
			if (!call_user_func('image'.strtolower($type), $this->img)){
				ob_end_clean();// Буфферизацию полюбому отключаем
				throw new ImageException('ERROR: преобразование картинки завершилось неудачей!');
			}
			else{//Нормально
				$this->strimg = ob_get_clean();
				$this->imgINIT();
			}
		}
		$this->TYPE = $type;
	}#m convertTo

	/**
	* Делает все проверки, и инициализирует картинку из ПОСТ-запроса пользователя.
	* Параметры проверки в public $this->UPLcheck. Если нужно что-то вместо дефолтных, то нужно сначала инициализировать этот массив
	* $IMG_FILE - массив, описывающий файл картинки, в формате $HTTP_POST_FILES
	* Возбуждаются исключения ImageUploadException
	**/
	function getUpload(&$IMG_FILE){
		if (!is_uploaded_file($IMG_FILE['tmp_name']) or $IMG_FILE['error']){
			throw new ImageUploadException('ERROR: Указанная картинка не является правильно загруженной или в процессе произошли ошибки! ('.$IMG_FILE['error'].')');
		}
		else{//Нормально загружено
			if ($IMG_FILE['size'] > $this->UPLcheck['maxIMGsize']){//Проверка размера загрузки
				throw new ImageUploadException('ERROR: Размер файла картинки превышает максимально допустимый '.$this->UPLcheck['maxIMGsize'].' байт!');
			}
			else{//С размером нормально все
				$uplimg = getimagesize($IMG_FILE['tmp_name']);
				if (!in_array($uplimg['mime'], $this->UPLcheck['typeAllow'])){
					throw new ImageUploadException('ERROR: Данный тип картинок не разрешен!');
				}
				else{
					if ($this->UPLcheck['maxIMGwidth'] and $uplimg[0] > $this->UPLcheck['maxIMGwidth']){
						throw new ImageUploadException('ERROR: Картинка превышает максимально допустимую ширину '.$this->UPLcheck['maxIMGwidth'].' пикселов!');
					}
					elseif ($this->UPLcheck['maxIMGheight'] and $uplimg[1] > $this->UPLcheck['maxIMGheight']){
						throw new ImageUploadException('ERROR: Картинка превышает максимально допустимую высоту '.$this->UPLcheck['maxIMGheight'].' пикселов!');
					}
					else{//ВСЕ клево!
						$this->getFile($IMG_FILE['tmp_name']);
						$this->UPLOADinfo = $IMG_FILE;
					}
				}
			}
		}
	return true;//Все ОК
	}#f getUpload

	public function putFile($path, $type, $mode=0660){
		// Синхронизация, превращение в строку
		$this->convertTo($type);
			if (!@file_put_contents($path, $this->strimg)){
				throw new FilesystemException ($this->error[] = 'ERROR: Не удается записать картинку в '.$path);
			}
		chmod($path, $mode);
		return true;
	}#m putFile

	/**
	* возвращаем строку, например для БД или самостоятельной записи вовне.
	**/
	public function getString($type){
		// Синхронизация, превращение в строку
		$this->convertTo($type);
		return $this->strimg;
	}#m getString

	/**
	* Это необходимый пережиток процедурного GD - некоторые функции
	* требуют 2 ресурса при одном вызове, например imagecopy, приходится
	* выдавать ему напрямую....
	**/
	public function getResource(){
		return $this->img;
	}#m getResource

	/**
	* Увеличивает картинку, до указанных размеров.
	* Само изображение не масштабируется.
	* Незаполненная область заливается цветом $color (строка типа AAEEFF) (поумолчанию FFFFFF - белый)
	* $align, $valign - выравнивание изображения по горизонтали и вертикали соответственно,
	* значения-строки следующие: 'center', 'left', 'right'; 'center', 'top', 'bottom'
	**/
	public function enlarge($width, $height, $color = 'FFFFFF', $align = 'center', $valign = 'center'){
		if ($width < ($newWidth['full'] = $this->PARAM['w'])){
			$newWidth['do'] = false;
		}
		else{
			$newWidth['full'] = $width;
			$newWidth['do'] = true;
		}

		if ($height < ($newHeight['full'] = $this->PARAM['h'])){
			$newHeight['do'] = false;
		}
		else{
			$newHeight['full'] = $height;
			$newHeight['do'] = true;
		}

		if (!$newWidth['do'] and !$newHeight['do']){
//			throw ImageException ('WARNING: Картинка больше заданного, не увеличиваем.');
			return true;
		}

		if ($newWidth['do']){//Ширину рассчитвыаем, высота остается как есть
			switch ($align){
				case 'center':
					$newWidth['leftImg'] = ceil(($newWidth['full'] - $this->PARAM['w'])/2);
					break;

				case 'left':
					$newWidth['leftImg'] = 0;
					break;

				case 'right':
					$newWidth['leftImg'] = $newWidth['full'] - $this->PARAM['w'];
					break;
			}
		}
		else $newWidth['leftImg'] = 0;

		if ($newHeight['do']){//Ширину рассчитываем, высота остается как есть
			switch ($valign){
				case 'center':
					$newHeight['topImg'] = ceil(($newHeight['full'] - $this->PARAM['h'])/2);
					break;

				case 'top':
					$newHeight['topImg'] = 0;
					break;

				case 'bottom':
					$newHeight['topImg'] = $newHeight['full'] - $this->PARAM['h'];
					break;
			}
		}
		else $newHeight['topImg'] = 0;
		// Раньше вышли бы, если бы не надо было ресайзить, поэтом располагаем, как требовалось:
		// подменяем старое изображение новым:
		$tmpimg = clone $this;
		$this->createTrueColor($newWidth['full'], $newHeight['full']);
		$this->fill($color);
		imagecopy($this->img,$tmpimg->getResource(), $newWidth['leftImg'], $newHeight['topImg'], 0, 0, $tmpimg->PARAM['w'], $tmpimg->PARAM['h']);
		$tmpimg = null;
		unset($tmpimg);
	}#m enlarge

	//////////////////SHARED methods////////////////////
	protected function imgINIT(){
		if (!($this->img = @imagecreatefromstring($this->strimg))){
			throw new ImageException ('ERROR: Картинка испорчена или неподдерживаемого формата!');
		}
		$this->PARAM['w'] = imagesx($this->img);
		$this->PARAM['h'] = imagesy($this->img);
		return true;
	}#m imgINIT

	public function imgFREE(){
		$this->img = null;
		$this->PARAM = null;
	}#m imgFREE

	public function width(){
		return $this->PARAM['w'];
	}#m width

	public function height(){
		return $this->PARAM['h'];
	}#m height
}#c GDimage
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/

class YML_offer_attributes_vendormodel extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => 'vendor.model'
		,'available'	=> true
	);
}#c YML_offer_attributes_vendormodel

class YML_offer_vendormodel extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		// typePrefix?, vendor, vendorCode?, model, (provider, tarifplan?)?
		'typePrefix'	//? Группа товаров \ категория.
		,'vendor'		// Производитель
		,'vendorCode'	//? Код товара (указывается код производителя)
		,'model'		// Модель

		,'provider'	//?
		,'tarifplan'	//?

		/*??? Не понятно, послал письмо. Нету этого в DTD
		,'name'		// Наименование товарного предложения
		,'delivery'	// Элемент, обозначающий возможность доставить соответствующий товар. "false" данный товар не может быть доставлен("самовывоз"). "true" товар доставляется на условиях, которые указываются в партнерском интерфейсе http://partner.market.yandex.ru на странице "редактирование".
		,'description'	// Описание товарного предложения
		,'available'	// Статус доступности товара - в наличии/на заказ.
			// available="false" - товарное предложение на заказ. Магазин готов осуществить поставку товара на указанных условиях в течение месяца (срок может быть больше для товаров, которые всеми участниками рынка поставляются только на заказ).. Те товарные предложения, на которые заказы не принимаются, не должны выгружаться в Яндекс.Маркет.
			// available="true" - товарное предложение в наличии. Магазин готов сразу договариваться с покупателем о доставке товара
			// Более точное описание можно посмотреть в требованиях к рекламным Материалам.
		 */
	);

	public function __construct(array $array, YML_offer_attributes_vendormodel $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_vendormodel
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/

class YML_offer_attributes_tour extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => 'artist.title'
		,'available'	=> true
	);
}#c YML_offer_attributes_tour

/**
* YML tour offer.
**/
class YML_offer_tour extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		// worldRegion?, country?, region?, days, dataTour*, name, hotel_stars?, room?, meal?, included, transport, price_min?, price_max?, options?
		'worldRegion'	//? Часть света
		,'country'	//? Страна
		,'region'		//? Курорт или город
		,'days'		// Количество дней тура
		,'dataTour'	//* Даты заездов
		,'name'		// Название отеля (в некоторых случаях наименование тура)
		,'hotel_stars'	//? Звезды S отеля 5*****
		,'room'		//? Тип комнаты (SNG, DBL......)
		,'meal'		//? Тип питания (All, HB......)
		,'included'	// Что включено в стоимость тура
		,'transport'	// Транспорт
		// Absent in documentation, but in DTD:
		,'price_min'	//?
		,'price_max'	//?
		,'options'	//?
	//In parent:	,'description'	// Описание тура
	);

	public function __construct(array $array, YML_offer_attributes_tour $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_tour
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/

/**
* This is generic type, mandatory only "name" elememnt. Additionally Yandex support name it as "name".
* In this type attribute "type" in element <offer> must be ommited.
* What is more intresting, it is present in DTD, but description do not describes it separately and
* present only devil mesh of it with YML_offer_vendormodel.
* Some explanation got from Yandex-support stuff - Andrey Tikhonov.
**/
class YML_offer_attributes_generic extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => null
		,'available'	=> true
	);
}#c YML_offer_attributes_generic

class YML_offer_generic extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		// name, vendor?,vendorCode?
		'name'
		,'vendor'		//?
		,'vendorCode'	//?
	);

	public function __construct(array $array, YML_offer_attributes_generic $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_generic
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/

class YML_offer_attributes_eventticket extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => 'artist.title'
		,'available'	=> true
	);
}#c YML_offer_attributes_eventticket

/**
* YML event ticket offer.
**/
class YML_offer_eventticket extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		// name, place, hall?, hall_part?, date, is_premiere?, is_kids?
		'name'		// Название мероприятия
		,'place'		// Зал

		//?? Написал вопрос. Полагаю что все же верно hall_part, как в DTD, а не hall plan (да еще и с пробелом), как в описании
		//	,'hall plan'	// Ссылка на картинку версии зала
		,'hall_part'	//?
		,'date'		// Дата и время сеанса. Указываются в формате ISO 8601: YYYY-MM-DDThh:mm
		,'is_premiere'	//? Признак премьерности мероприятия
		,'is_kids'	//? Признак детского мероприятия
	);

	public function __construct(array $array, YML_offer_attributes_eventticket $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_eventticket
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/

class YML_offer_attributes_book extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => 'book'
		,'available'	=> true
	);
}#c YML_offer_attributes_book

class YML_offer_book extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		// author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, binding?, page_extent?, table_of_contents?
		'author'		//? Автор произведения
		,'name'		// Наименование произведения
		,'publisher'	//? Издательство
		,'series'		//? Серия
		,'year'		//? Год издания
		,'ISBN'		//? Код книги, если их несколько, то указываются через запятую.
		// Present in description, but not allowed by DTD
		//	,'description'	// Аннотация к книге.
		,'volume'		//? Количество томов.
		,'part'		//? Номер тома.
		,'language'	//? Язык произведения.
		,'binding'	//? Переплет.
		,'page_extent'	//? Количествово страниц в книге, должно быть целым положиельным числом.
		,'table_of_contents'	//? Оглавление. Выводится информация о наименованиях произведений, если это сборник рассказов или стихов.
	);

	public function __construct(array $array, YML_offer_attributes_book $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		$this->addFilterSet(new settings_filter_base('page_extent', array($this, 'filter_set__check_page_extent')));

		parent::__construct($array, $props, $currencies, $categories);
	}#__c

	/**
	* Filter: Check on set what number of pages integer positive number.
	*
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_page_extent($name, &$val){
		if ($val < 0 or (int)$val != $val) throw new YML_offer_exception_constraint('number of pages must be integer positive number.');
		return $val;
	}#m filter_check_page_extent
}#c YML_offer_book
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/

class YML_offer_attributes_audiobook extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => 'audiobook'
		,'available'	=> true
	);
}#c YML_offer_attributes_audiobook

class YML_offer_audiobook extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		//	author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, table_of_contents?, performed_by?, perfomace_type?, storage?, format?, recording_lenght?
		'author'		//? Автор произведения
		,'name'		// Наименование произведения
		,'publisher'	//? Издательство
		,'series'		//? Серия
		,'year'		//? Год издания
		,'ISBN'		//? Код книги, если их несколько, то указываются через запятую.
		// Present in description, but not allowed by DTD
		//	,'description'	// Аннотация к книге.
		,'volume'		//? Количество томов.
		,'part'		//? Номер тома.
		,'language'	//? Язык произведения.
		,'table_of_contents'	//? Оглавление. Выводится информация о наименованиях произведений, если это сборник рассказов или стихов.

		,'performed_by'	//? Исполнитель. Если их несколько, перечисляются через запятую
		,'performance_type'	//? Тип адиокниги (радиоспектакль, произведение начитано, ...)
		,'storage'		//? Носитель, на котором поставляется аудиокнига.
		,'format'			//? Формат аудиокниги.
		/**
		* WARNING! recording_lenght format is completely absent in DTD (mentioned
		*	as PCDATA) and have another format in example then in description!
		*	Current implementation by description
		* WARNING2! It is recording_lenght, NOT recording_length in DTD!
		**/
		,'recording_lenght'	//? Время звучания задается в формате mm.ss (минуты.секунды).
);

	public function __construct(array $array, YML_offer_attributes_audiobook $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		$this->addFilterSet(new settings_filter_base('recording_length', array($this, 'filter_set__check_recording_length')));

		parent::__construct($array, $props, $currencies, $categories);
	}#__c

	/**
	* Filter: Check on set what number of pages integer positive number.
	*
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_recording_length($name, &$val){
		if (!preg_match('/^\d+\.\d{2}$/')) throw new YML_offer_exception_constraint('Recording time must be a string in format: "mm.ss (minutes.seconds)"');
		return $val;
	}#m filter_check_recording_length
}#c YML_offer_audiobook
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/

class YML_offer_attributes_artisttitle extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => 'artist.title'
		,'available'	=> true
	);
}#c YML_offer_attributes_artisttitle

/**
* YML audio-video offer.
**/
class YML_offer_artisttitle extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		// artist?, title, year?, media?, starring?, director?, originalName?, country?
		'artist'		//? Исполнитель
		,'title'		// Наименование
		,'year'		//? Год
		,'media'		//? Носитель
		,'starring'	//? Актеры
		,'director'	//? Режиссер
		,'originalName'//? Оригинальное наименование
		,'country'	//? Страна
	);

	public function __construct(array $array, YML_offer_attributes_artisttitle $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_artisttitle
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/


class YML_offer_exception extends BaseException{};
class YML_offer_exception_constraint extends YML_offer_exception{};

/**
* YML offer attributes
**/
class YML_offer_attributes extends settings_filter{
	public $properties = array(
		'id'
		,'type'
		,'bid'
		,'cbid'
		,'available'
	);

	public function __construct($array){
		$this->addFilterGet(new settings_filter_base('available', array($this, 'filter_get__text_boolean')));
		parent::__construct($this->properties, $array);
	}

	/**
	* Fields-properties from DTD.
	*	Common-prefix-part: url, buyurl?, price, wprice?, currencyId, xCategory?, categoryId+, picture?, delivery?, deliveryIncluded?, orderingTime?,
	*	Common-suffix-part: aliases?, additional*, description?, sales_notes?, promo?, manufacturer_warranty?, country_of_origin?, downloadable?
	*
	*vendor.model
	*	typePrefix?, vendor, vendorCode?, model, (provider, tarifplan?)?
	*
	*Book:
	*	author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, binding?, page_extent?, table_of_contents?
	*
	*AudioBook:
	*	author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, table_of_contents?, performed_by?, perfomace_type?, storage?, format?, recording_lenght?
	*
	*artist.title:
	*	artist?, title, year?, media?, starring?, director?, originalName?, country?
	*
	*tour:
	*	worldRegion?, country?, region?, days, dataTour*, name, hotel_stars?, room?, meal?, included, transport, price_min?, price_max?, options?
	*
	*event-ticket:
	*	name, place, hall?, hall_part?, date, is_premiere?, is_kids?
	*
	*generic(not in description, but in DTD):
	*	name, vendor?,vendorCode?
	*/

	/**
	* Represent boolean values as text true/false
	*
	* @param	string	$name
	* @param	boolean	$value
	* @return string
	**/
	public function filter_get__text_boolean(&$name, &$value){
		$value ? $value = 'true' : $value = 'false';
	}#m filter_get__text_boolean

	//Defaults
	/* MUST BE DEFINED in childs
	protected $__SETS = array(
		'type' => 'vendor.model' // vendor.model | book | artist.title | tour | ticket | event-ticket
		,'available'	=> true
	);
	**/
}#c YML_offer_attributes

/**
* YML yandex offer
*
* This is base (type vendor.model) offer.
**/
abstract class YML_offer extends settings_filter{
	const NESTING_PLACEHOLDER = '<<==NESTING==>>';

	public $properties = array(
		// Common fields
		'url'		// URL-адрес страницы товара
		,'price'		// Цена, по которой данный товар можно приобрести.Цена товарного предложения округляеся и выводится в зависимости от настроек пользователя.
		,'currencyId'	// Идентификатор валюты товара (RUR, USD, UAH). Для корректного отображения цены в национальной валюте, необходимо использовать идентификатор (например, UAH) с соответствующим значением цены.
		,'categoryId'	//+ Идентификатор категории товара (целое число не более 18 знаков). Товарное предложение может принадлежать только одной категории
		,'picture'	//? Ссылка на картинку соответствующего товарного предложения. Недопустимо давать ссылку на "заглушку", т.е. на картинку где написано "картинка отсутствует" или на логотип магазина
		,'delivery'	//?
		,'deliveryIncluded'	//?
		,'orderingTime'	//?

		,self::NESTING_PLACEHOLDER

		,'aliases'	//?
		,'additional'	//*
		,'description'	//?
		,'sales_notes'	//? Элемент, предназначенный для того, чтобы показать пользователям, чем отличается данный товар от других, или для описания акций магазина (кроме скидок). Допустимая длина текста в элементе - 50 символов.
		,'promo'		//?
		,'manufacturer_warranty'	//? Элемент предназначен для отметки товаров, имеющих официальную гарантию производителя.
		,'country_of_origin'	//? Элемент предназначен для указания страны производства товара.
		,'downloadable'	//? Элемент предназначен обозначения товара, который можно скачать.

		// INNER, static
		,'__props'		//
		,'__currencies'	//
		,'__categories'	//
		,'__xpath'		//
	);

	protected $static_settings = array('__props', '__currencies', '__categories', '__xpath');

	/**
	* Constructor YML_offer
	*
	* @param	array	$array	Data to construct from.
	* @param	Object(YML_offer_attributes)	Attributes of constructed object.
	* @param	Object(DOMNode)	$currencies	Mandatory. To check constraints.
	* @param	Object(DOMNode)|null	$categories	Optional. If present, constraints will be checked.
	**/
	public function __construct(array $array, YML_offer_attributes $props, DOMNode $currencies, DOMNode $categories = null){
		$this->__props = $props;

		// SetUp filters which represents common checks:
		// CurrencyID in allowed
		$this->addFilterSet(new settings_filter_base('currencyId', array($this, 'filter_set__check_currencyId')));
		// categoryId in allowed
		$this->addFilterSet(new settings_filter_base('categoryId', array($this, 'filter_set__check_categoryId')));

		// Picture can't be empty and can't be logo or something. Check non-empty only
		//	(How I can automatically check what image is not image with "No foto" text only?).
		$this->addFilterSet(new settings_filter_base('picture', array($this, 'filter_set__check_picture')));

		$this->__currencies = $currencies; // To check constraints
		$this->__categories = $categories;
		$this->__xpath = new DOMXPath($this->__currencies->ownerDocument);
		parent::__construct($this->properties, $array);
	}#__c

	/**
	* Create result XML
	*
	* @return	Object(DOMElement)
	**/
	public function getXML(){
		/**
		* We NEED document instead of doing something like: new DOMElement
		* Please see: http://forums.codewalkers.com/php-coding-7/problem-with-dom-710474.html
		**/
		$res = new DOMDocument;
		$res->substituteEntities = true;
		$offer = $res->appendChild($res->createElement('offer'));

		foreach(array('id') as $item)		//Requred attributes
			$offer->setAttribute($item, REQUIRED_VAR($this->__props->$item));
			// Please note, type also optional. {@see YML_offer_generic}
			foreach(array('bid', 'cbid', 'available', 'type') as $item)		//Optional attributes
				if ($this->__props->$item) $offer->setAttribute($item, $this->__props->$item);
	
			foreach($this->getRegularKeys() as $itemKey){	//All defined subelements
				if ($this->{$itemKey}){
					/**
					* @internal
					* Due to the Bugs: http://bugs.php.net/bug.php?id=31191, http://bugs.php.net/bug.php?id=48109, http://bugs.php.net/bug.php?id=40105
					* we can't use short form $res->createElement($tag, $tagValue);
					**/
					$offer->appendChild($res->createElement($itemKey))->appendChild($res->createTextNode($this->{$itemKey}));
				}
			}
		return $offer;
	}#m saveXML

	/**
	* Filter: Check on set what currencyId in allowed currencies
	*
	* @param	string	$name
	* @param	mixed	$val
	* @return	mixed	Modified value.
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_currencyId($name, $val){
		if ($this->__xpath->query('currency[@id="' . $val . '"]', $this->__currencies)->length < 1) throw new YML_offer_exception_constraint("$val currency is not allowed in current configuration");
		return $val;
	}#m filter_check_currencyId

	/**
	* Filter: Check on set what categoryId in allowed currencies
	*
	* @param	string	$name
	* @param	mixed	$val
	* @return	mixed	Modified value.
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_categoryId($name, $val){
		if ( $this->__categories and $this->__xpath->query('category[@id="' . $val . '"]', $this->__categories)->length < 1 ) throw new YML_offer_exception_constraint("$val category is not allowed in current configuration");
		return $val;
	}#m filter_check_categoryId

	/**
	* Filter: Picture can't be empty and can't be logo or something. Check non-empty only
	* (How I can automatically check what image is not image with "No foto" text only?).
	*
	* @param	string	$name
	* @param	mixed	$val
	* @return	mixed	Modified value.
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_picture($name, $val){
		if (empty($val)) throw new YML_offer_exception_constraint("Picture can't be empty. Logo and 'No foto' images also unacceptable");
		return $val;
	}#m filter_check_picture

	/**
	* Reimplement to provide correct order of elements, which must be in XML
	*
	* @return	array Array of keys-properties in proper order.
	**/
	public function getRegularKeys(){
		return array_diff($this->properties, $this->static_settings);
	}#m getRegularKeys

	/**
	* Emulate nesting.
	*
	* In our case order of fields have fatal important. So, we can't just add fields.
	*	We must inject it in proper place.
	* DTD had logical structure of fields like: common prefixed fields for all offers,
	*	and common suffixed fields for all offers. And some part of main fields related
	*	to the type of offer. So, this main fields, which must be defined in child
	*	$this->properties_addon property we insert instead of placeholder self::NESTING_PLACEHOLDER
	**/
	public function nesting(){
		array_splice($this->properties, array_search(self::NESTING_PLACEHOLDER, $this->properties), 1, $this->properties_addon);
	}#m nesting

	/**
	* Common check of "Welness". Child may reimplement its base checks (f.e. to allow Categories filtering)
	*
	* @return	boolean
	**/
	public function isValid(){
		if ($this->price <= 0) return false; //Only real products
		if ('/default_image.gif' == $this->picture) return false; //Only with images
		if (!$this->vendor) return false;
		return true;
	}#m isValid
}#c YML_offer
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-09-28 17:28
**/

/**
* Flat category structure - just parentId ignored.
**/
class YML_category_flat extends YML_category{
	public function __construct(array $arr){
	$this->addFilterSet(new settings_filter_ignore('parentId'));// Just ignore

	parent::__construct($arr);
	}#__c
}#c YML_category_flat
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1.1
* @created 2009-08-24 16:06
**/



/**
* Create category DOMNode for YML.
**/
class YML_category extends settings_filter{
public $properties = array(
	'id'
	,'parentId'
	,'_value_'	// This is value of element itself.
);

	public function __construct(array $arr){
	$this->setSettingsArray(REQUIRED_NOT_NULL($arr));
	}#__c

	/**
	* Return DOMNode of category.
	* @param
	**/
	public function getXML(DOMDocument &$dom){
	REQUIRED_NOT_NULL($this->_value_, '_value_');
	REQUIRED_NOT_NULL($this->id, 'id');
	$category = $dom->createElement('category');
	/**
	* @internal
	* Due to the Bugs: http://bugs.php.net/bug.php?id=31191, http://bugs.php.net/bug.php?id=48109, http://bugs.php.net/bug.php?id=40105
	* we can't use short form $res->createElement($tag, $tagValue);
	**/
	$category->appendChild($dom->createTextNode($this->_value_));
		foreach($this->getRegularKeys() as $itemKey){//All defined subelements
			if (isset($this->$itemKey) and '_value_' != $itemKey) $category->setAttribute($itemKey, $this->$itemKey);
		}
	return $category;
	}#m getXML
}#c YML_category
?><?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @example YML.example.php
* @version 1.1
* @created 2009-06-30 17:21
**/


class YML_exception extends BaseException{};
class YML_exception_absentElement extends YML_exception{};

/**
* Yandex-market class implementation. http://partner.market.yandex.ru/legal/tt/
**/
class YML{
	private $dom_;		// Main DOMDocument
	private $xpath_;	// DOMXpath object to perfom any queries

	// Cache presents of elements in document to do not do amny times slo Xpath queries.
	protected $cache_ = array(
		'yml_catalog'	=> null,
		'shop'		=> null,
		'currencies'	=> null,
		'categories'	=> null,
		'offers'	=> null,
	);

	public function __construct(){
		// DTD. http://www.php.net/manual/en/book.dom.php#78929
		$this->dom_ = DOMImplementation::createDocument('', '', DOMImplementation::createDocumentType('yml_catalog', '', 'shops.dtd'));
		$this->dom_->encoding = 'UTF-8';
		$this->dom_->validateOnParse = true;
		$this->xpath_ = new DOMXPath($this->dom_);

		$this->cache_['yml_catalog'] = $this->dom_->appendChild($this->dom_->createElement('yml_catalog'));
		$this->cache_['yml_catalog']->setAttribute('date', date('Y-m-d H:i'));
	}#__c

	/**
	* Add <shop> to document
	*
	* @param	array	$shop Must contain fields: 'name', 'company', 'url'
	* @return	&$this;
	* @Throws(VariableRequired)
	**/
	public function &addShop(array $shop){
		$this->cache_['shop'] = $this->getYml_catalog()->appendChild($this->dom_->createElement('shop'));
		foreach (array('name', 'company', 'url') as $item){
			$this->cache_['shop']->appendChild($this->dom_->createElement($item, REQUIRED_VAR($shop[$item])));
		}
		return $this;
	}#m addShop

	/**
	* Add used currencies into document.
	*
	* @param	array	$curs Array of currencies to add. Format like (id and rate required!):
	* array(
	*	'RUR'	=> array(
	*		'rate' => 1
	*	)
	*	,'USD'	=> array(
	*		'rate' => '29.30'
	*	)
	*	,'EUR'	=> array(
	*		'rate' => 'CBRF'
	*		,'plus'=> 3
	*	)
	* )
	* @return	&$this
	**/
	public function &addCurrencies(array $curs){
		$this->cache_['currencies'] = $this->checkElementPresents('shop')->appendChild($this->dom_->createElement('currencies'));
		foreach (REQUIRED_VAR($curs) as $id => $cur){
			$currency = $this->cache_['currencies']->appendChild($this->dom_->createElement('currency'));
			$currency->setAttribute('id', $id);
			if(!empty($cur['rate'])) $currency->setAttribute('rate', $cur['rate']);
			if(!empty($cur['plus'])) $currency->setAttribute('plus', $cur['plus']);
		}
		return $this;
	}#m addCurrencies

	/**
	* Add categories into document
	*
	* @param	array	$cats Array of categories to add.
	* array(
	*	// id="1". If no 'parentId' - root category
	*	1 => array(
	*		'value'	=> 'Книги'
	*	)
	*	, //id="2"
	*	2 => array(
	*		'value'	=> 'Видео'
	*	)
	*	,
	*	3 => array(
	*		'value'	=> 'Детективы'
	*		,'parentId'	=> '1'
	*	)
	*	,
	*	4 => array(
	*		'value'	=> 'Боевики'
	*		,'parentId'	=> '1'
	* )
	* @param	string=YML_category $class What class create.
	*
	* @return	&$this
	**/
	public function &addCategories(array $cats, $class = 'YML_category'){
		foreach (REQUIRED_VAR($cats) as $id => $cat){
			$this->addCategory(  new $class( $cat + array('id' => $id) )  );
		}
		return $this;
	}#m addCategories

	/**
	* Add category into document
	*
	* @param	Object(YML_category)	$cat Category to add.
	*
	* @return	&$this
	**/
	public function &addCategory(YML_category $cat){
		if(! $this->checkElementPresents('categories')){ //Create on demand
			$this->cache_['categories'] = $this->checkElementPresents('shop', true)->appendChild($this->dom_->createElement('categories'));
		}

		$this->cache_['categories']->appendChild($cat->getXML($this->dom_));
		return $this;
	}#m addCategory

	/**
	* Add offer to shop offers. <offers> element will be created automatically -
	*	you do not need care of it.
	*
	* @param	Object(YML_offer)	$offer
	* @return	&$this
	**/
	public function &addOffer(YML_offer $offer){
		$offers = $this->getOffers();
		$offers->appendChild($this->dom_->importNode($offer->getXML(), true));
		return $this;
	}#m addOffer

	/**
	* Return reference to <offers> element. Create it, if still don't present.
	*
	* @return	&Object(DOMElement)
	**/
	private function getOffers(){
		if(! $this->checkElementPresents('offers')){ //Create on demand
			$this->cache_['offers'] = $this->checkElementPresents('shop', true)->appendChild($this->dom_->createElement('offers'));
		}
		return $this->cache_['offers'];
	}#m getOffers

	/**
	* Return <yml_catalog> DOM element.
	*
	* @return	&Object(DOMElement)
	**/
	public function &getYml_catalog(){
		return $this->checkElementPresents('yml_catalog', true);
	}#m getYml_catalog

	/**
	* Return <currencies> DOM element.
	*
	* @return	Object(DOMElement)
	**/
	public function getCurrencies(){
		return $this->checkElementPresents('currencies', true);
	}#m getCurrencies

	/**
	* Return <categories> DOM element.
	*
	* @return	Object(DOMElement)
	**/
	public function getCategories(){
		return $this->checkElementPresents('categories', true);
	}#m getCategories

	/**
	* Get string representation of XML document.
	*
	* @param	array	$opts Array of options, which must be applyed to DOMDocument
	*					object first. As array of Key=>value. Nothing checked.
	* @return	string
	**/
	public function saveXML(array $opts = array( 'formatOutput' => true )){
		foreach ($opts as $opt => $val){
			$this->dom_->{$opt} = $val;
		}
		return $this->dom_->saveXML();
	}#m saveXML

	/**
	* Return true if element was add in tree, false otherwise.
	* Made for caching purpose, because Xpath is very slow for such simple queries.
	* Return &$this, or throw exception, so it may be used in chain.
	*
	* @param	string	$name Name of requested item.
	* @param	boolean	$throw If true element required and exception throwed, otherwise returned reference to element or false.
	* @return	&DOMElement Requested node.
	* @Throws(YML_exception_absentElement)
	**/
	protected function &checkElementPresents($name, $throw = false){
		if($throw and ! $this->cache_[$name]){
			throw new YML_exception_absentElement("You must add element '$name' first!");
		}
		else return $this->cache_[$name];
	}#m checkElementPresents
}#c YML
?><?
/**
* Get, parse and manipulate SIM-IM message history files.
* @package SIMhistory
* @version 0.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @uses debug
* @uses VariableStream
* @uses NON_EMPTY_STR()
* @uses Process
**/

class sim_message {
	private $type;
	private $items = array();

	/**
	* Parses message in format:
	* ServerText="Текст сообщения"
	* Flags=17
	* Background=16777215
	* Foreground=0
	* Time=1185694212
	**/
	public function parse($type, $string){
		$this->type = $type;
		// In "" interprets as standart: variables, \n etc... No needed! Replece by ''
		// As replace " to ' need quote exists like: "text' quote" => "text'"'"' quote" overwise get error parse ini-file!
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

	/**
	* Recode text to $encTo from:
	* 1) autodetected encoding if $justRecode = false. If autodetect failed to $encFrom
	* 2) $encFrom, if $justRecode = true
	*
	* @param	string	Opt['utf-8']. Encoding To convert.
	* @param	string	Opt['cp1251']. Encoding From convert.
	* @param	boolean	Opt['false']. Do autodetect of encForm or not.
	* @param	string	Opt['russian']. Language for autodetection encoding (see man enca).
	**/
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
			// Doing Fallback. 1: "enconv: Cannot convert `STDIN' from unknown encoding"
				if (1 == $pe->state->exit_code){// Just recode manualy
					fwrite(STDERR, 'unknown In encoding. Fallback to '.$encFrom.' => '.$encTo."\n");
					$text = iconv($encFrom, $encTo, $text);
				}
				else //Ignore, if recoding not succeed
					echo 'NOT recoding properly, ignoring. '."\n".$pe->getMessage()."\nOriginal message: ".log_dump($this->items);
			}		
		}
	}#m recodeText

	public function getString(){
		$txt = '['.$this->type."]\n";
		foreach ($this->items as $key => $item){
			// String-values must be quoted!
			// Whithout (string) casting by defaults all casting to int, and this is equivalent all times!
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
	}#__c

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
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element workflow.
**/
class moysklad_workflow extends moysklad_element_base{
	function getName(){
		return 'workflow';
	}#m getName
}#c moysklad_workflow
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element warehouses.
**/
class moysklad_warehouses extends moysklad_element_base{
	function getName(){
		return 'warehouses';
	}#m getName
}#c moysklad_warehouses

/**
* Moysklad element script.
**/
class moysklad_warehouse extends moysklad_element_base {
	function getName() {
		return 'warehouse';
	}#m getName
}#c moysklad_warehouse
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element uoms.
**/
class moysklad_uoms extends moysklad_element_base{
	function getName(){
		return 'uoms';
	}#m getName
}#c moysklad_uoms

/**
* Moysklad element script.
**/
class moysklad_uom extends moysklad_element_base{
	/*
	<uom name="шт" updatedBy="admin@hubbitus" updated="2010-02-26T23:06:04.651+03:00" changeMode="NONE" readMode="PARENT">
		<id>tVD4k03EhnKBPbeXSY9RW2</id>
		<version>0</version>
		<company>hubbitus</company>
		<code>796</code>
		<description>Демонстрационные данные - начало работы с МоимСкладом</description>
	</uom>
	*/
	// Each attributes shoud be listed here
	protected $attributes_defaults = array(
		'name'		=> 'шт'
		,'updatedBy'	=> '' // "admin@hubbitus"
		,'updated'		=> null // "2010-02-26T23:06:04.958+03:00" <- date('c') by default
		,'changeMode'	=> 'NONE'
		,'readMode'	=> 'PARENT'
	);
	protected $elements_defaults = array(
		'id'			=> null
		,'version'	=> 0
		,'company'	=> null
		,'code'		=> null
		,'description'	=> null
	);

	/**
	*
	* @param array $value Mixed array ofdescedance elements and attributes:
	* @TODO add data type checks and requirements
	**/
	function __construct(array $value){
		parent::__construct('');

		// PHP does not allow dinamic (non constants) initializations
		if(is_null($this->attributes_defaults['updated'])) $this->attributes_defaults['updated'] = date('c');

		foreach($this->attributes_defaults as $attr => $val){
			// isset id not function but language construction and can't be invoked directly
			$this->dom->setAttribute($attr, EMPTY_callback(create_function('$v', 'return isset($v);'), @$value[$attr], $val));
		}
		foreach ($this->elements_defaults as $elem => $val){
			$this->dom->appendChild(new DOMElement($elem, EMPTY_callback(create_function('$v', 'return isset($v);'), @$value[$elem], $val)));
		}
	}#__c

	function getName() {
		return 'uom';
	}#m getName
}#c moysklad_uom
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element things.
**/
class moysklad_things extends moysklad_element_base{
	function getName(){
	return 'things';
	}#m getName
}#c moysklad_things

/**
 * Moysklad element script.
 **/
class moysklad_thing extends moysklad_element_base {
	function getName() {
		return 'thing';
	}#m getName
}#c moysklad_thing
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element shareModes.
**/
class moysklad_shareModes extends moysklad_element_base{
	function getName(){
		return 'shareModes';
	}#m getName
}#c moysklad_shareModes

/**
* Moysklad element shareMode.
**/
class moysklad_shareMode extends moysklad_element_base{
	function getName(){
		return 'shareMode';
	}#m getName
}#c moysklad_shareMode
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element service.
**/
class moysklad_service extends moysklad_element_base{
	function getName(){
		return 'service';
	}#m getName
}#c moysklad_service
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element scripts.
**/
class moysklad_scripts extends moysklad_element_base{
	function getName(){
		return 'scripts';
	}#m getName
}#c moysklad_scripts

/**
* Moysklad element script.
**/
class moysklad_script extends moysklad_element_base{
	function getName(){
		return 'script';
	}#m getName
}#c moysklad_script
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element reportTemplatesMetadata.
**/
class moysklad_reportTemplatesMetadata extends moysklad_element_base{
	function getName(){
		return 'reportTemplatesMetadata';
	}#m getName
}#c moysklad_reportTemplatesMetadata
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element reason.
**/
class moysklad_reason extends moysklad_element_base{
	function getName(){
	return 'reason';
	}#m getName
}#c moysklad_reason

/**
* Moysklad element script.
**/
class moysklad_lossReason extends moysklad_element_base {
	function getName() {
		return 'lossReason';
	}#m getName
}#c moysklad_lossReason

/**
* Moysklad element script.
**/
class moysklad_enterReason extends moysklad_element_base {
	function getName() {
		return 'enterReason';
	}#m getName
}#c moysklad_enterReason
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element purchaseOrders.
**/
class moysklad_purchaseOrders extends moysklad_element_base{
	function getName(){
		return 'purchaseOrders';
	}#m getName
}#c moysklad_purchaseOrders

/**
* Moysklad element script.
**/
class moysklad_purchaseOrder extends moysklad_element_base {
	function getName() {
		return 'purchaseOrder';
	}#m getName
}#c moysklad_purchaseOrder
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element project.
**/
class moysklad_project extends moysklad_element_base{
	function getName(){
		return 'project';
	}#m getName
}#c moysklad_project
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element processings.
**/
class moysklad_processings extends moysklad_element_base{
	function getName(){
		return 'processings';
	}#m getName
}#c moysklad_processings

/**
* Moysklad element script.
**/
class moysklad_processing extends moysklad_element_base {
	function getName() {
		return 'processing';
	}#m getName
}#c moysklad_processing
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element processingPlans.
**/
class moysklad_processingPlans extends moysklad_element_base{
	function getName(){
	return 'processingPlans';
	}#m getName
}#c moysklad_processingPlans

/**
* Moysklad element script.
**/
class moysklad_processingPlan extends moysklad_element_base {
	function getName() {
		return 'processingPlan';
	}#m getName
}#c moysklad_processingPlan
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element priceLists.
**/
class moysklad_priceLists extends moysklad_element_base{
	function getName(){
	return 'priceLists';
	}#m getName
}#c moysklad_priceLists

/**
* Moysklad element script.
**/
class moysklad_priceList extends moysklad_element_base {
	function getName() {
		return 'priceList';
	}#m getName
}#c moysklad_priceList
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element places.
**/
class moysklad_places extends moysklad_element_base{
	function getName(){
		return 'places';
	}#m getName
}#c moysklad_places

/**
 * Moysklad element script.
 **/
class moysklad_place extends moysklad_element_base {
	function getName() {
		return 'place';
	}#m getName
}#c moysklad_place
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element persons.
**/
class moysklad_persons extends moysklad_element_base{
	function getName(){
	return 'persons';
	}#m getName
}#c moysklad_persons

/**
* Moysklad element script.
**/
class moysklad_person extends moysklad_element_base {
	function getName() {
		return 'person';
	}#m getName
}#c moysklad_person
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element myCompany.
**/
class moysklad_myCompany extends moysklad_element_base{
	function getName(){
		return 'myCompany';
	}#m getName
}#c moysklad_myCompany
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element moves.
**/
class moysklad_moves extends moysklad_element_base{
	function getName(){
	return 'moves';
	}#m getName
}#c moysklad_moves

/**
 * Moysklad element script.
 **/
class moysklad_move extends moysklad_element_base {
	function getName() {
		return 'move';
	}#m getName
}#c moysklad_move
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

class moysklad_messages extends moysklad_element_base{
	function getName(){
		return 'messages';
	}#m getName
}#c moysklad_messages

/**
* Moysklad element script.
**/
class moysklad_message extends moysklad_element_base {
	function getName() {
		return 'message';
	}#m getName
}#c moysklad_message
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element losses.
**/
class moysklad_losses extends moysklad_element_base{
	function getName(){
		return 'losses';
	}#m getName
}#c moysklad_losses

/**
* Moysklad element script.
**/
class moysklad_loss extends moysklad_element_base {
	function getName() {
		return 'loss';
	}#m getName
}#c moysklad_loss
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element invoicesOut.
**/
class moysklad_invoicesOut extends moysklad_element_base{
	function getName(){
	return 'invoicesOut';
	}#m getName
}#c moysklad_invoicesOut

/**
* Moysklad element script.
**/
class moysklad_invoiceOut extends moysklad_element_base {
	function getName() {
	return 'invoiceOut';
	}#m getName
}#c moysklad_invoiceOut
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element invoicesIn.
**/
class moysklad_invoicesIn extends moysklad_element_base{
	function getName(){
		return 'invoicesIn';
	}#m getName
}#c moysklad_invoicesIn

/**
* Moysklad element script.
**/
class moysklad_invoiceIn extends moysklad_element_base {
	function getName() {
		return 'invoiceIn';
	}#m getName
}#c moysklad_invoiceIn
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element inventories.
**/
class moysklad_inventories extends moysklad_element_base{
	function getName(){
		return 'inventories';
	}#m getName
}#c moysklad_inventories

/**
* Moysklad element script.
**/
class moysklad_inventory extends moysklad_element_base {
	function getName() {
		return 'inventory';
	}#m getName
}#c moysklad_inventory
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element gtd.
**/
class moysklad_gtd extends moysklad_element_base{
	function getName(){
		return 'gtd';
	}#m getName
}#c moysklad_gtd
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element goods.
**/
class moysklad_goods extends moysklad_element_base{
	function getName(){
		return 'goods';
	}#m getName
}#c moysklad_goods

/**
 * Moysklad element good.
 **/
class moysklad_good extends moysklad_element_base{
	// Each attributes shoud be listed here
	protected $attributes_defaults = array(
		'minimumBalance'	=> 0		// "20.0"
		,'buyPrice'		=> 0		// "0.0"
		,'isSerialTrackable'=> 'false'// "false"
		,'salePrice'		=> null	// "1250.0"
		,'uomId'			=> null	// "tVD4k03EhnKBPbeXSY9RW2"
		,'vat'			=> 18	// "18"
		,'parentId'		=> null	// "c4rm8jR6izyZyVouh5yMw2" <- CategoryId!
		,'name'			=> null	// "Мыло душистое"
		,'updatedBy'		=> null	// "admin@hubbitus"
		,'updated'		=> null	// "2010-02-26T23:06:04.958+03:00" <- date('c') by default
		,'changeMode'		=> 'NONE'
		,'readMode'		=> 'ALL'
	);
	protected $elements_defaults = array(
		'id'			=> null
		,'version'	=> 0
		,'company'	=> null
		,'code'		=> null
		,'description'	=> null
	);

	/**
	*
	* @param array $value Mixed array ofdescedance elements and attributes:
	* @TODO add data type checks and requirements
	**/
	function __construct(array $value){
		parent::__construct('');

		// PHP does not allow dinamic (non constants) initializations
		if(is_null($this->attributes_defaults['updated']))
			 $this->attributes_defaults['updated'] = date('c');

		foreach($this->attributes_defaults as $attr => $val){
			// isset id not function but language construction and can't be invoked directly
			$this->dom->setAttribute($attr, EMPTY_callback(create_function('$v', 'return isset($v);'), @$value[$attr], $val));
		}
		foreach ($this->elements_defaults as $elem => $val){
			$this->dom->appendChild(new DOMElement($elem, EMPTY_callback(create_function('$v', 'return isset($v);'), @$value[$elem], $val)));
		}
	}#m  __construct

	function getName() {
		return 'good';
	}#m getName
}#c moysklad_good
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/



/**
* Moysklad element goodFolders.
**/
class moysklad_goodFolders extends moysklad_element_base{
	function getName(){
		return 'goodFolders';
	}#m getName
}#c moysklad_goodFolders

/**
* Moysklad element script.
**/
class moysklad_goodFolder extends moysklad_element_base {
	// Each attributes shoud be listed here
	protected $attributes_defaults = array(
		'productCode'	=> ''
		,'vat'		=> '18'
		,'name'		=> ''
		,'updatedBy'	=> 'admin@hubbitus'
		,'updated'	=> null //If null propogated by date('c') in constructor
		,'changeMode'	=> 'NONE'
		,'readMode'	=> 'ALL'
	);
	protected $elements_defaults = array(
		'id'			=> null
		,'version'	=> 0
		,'company'	=> null
		,'description'	=> null
	);

	/**
	*
	* @param array $value Mixed array ofdescedance elements and attributes:
	* @TODO add data type checks and requirements
	**/
	function __construct(array $value){
		parent::__construct('');

		// PHP does not allow dinamic (non constants) initializations
		if(is_null($this->attributes_defaults['updated'])) $this->attributes_defaults['updated'] = date('c');

		foreach($this->attributes_defaults as $attr => $val){
			// isset id not function but language construction and can't be invoked directly
			$this->dom->setAttribute($attr, EMPTY_callback(create_function('$v', 'return isset($v);'), @$value[$attr], $val));
		}
		foreach ($this->elements_defaults as $elem => $val){
			$this->dom->appendChild(new DOMElement($elem, EMPTY_callback(create_function('$v', 'return isset($v);'), @$value[$elem], $val)));
		}
	}#m  __construct

	function getName(){
		return 'goodFolder';
	}#m getName
}#c moysklad_goodFolder
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element entityTemplatesMetadata.
**/
class moysklad_entityTemplatesMetadata extends moysklad_element_base{
	function getName(){
		return 'entityTemplatesMetadata';
	}#m getName
}#c moysklad_entityTemplatesMetadata
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element enters.
**/
class moysklad_enters extends moysklad_element_base{
	function getName(){
		return 'enters';
	}#m getName
}#c moysklad_enters

/**
* Moysklad element script.
**/
class moysklad_enter extends moysklad_element_base {
	function getName() {
		return 'enter';
	}#m getName
}#c moysklad_enter
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element embeddedEntityMetadata.
**/
class moysklad_embeddedEntityMetadata extends moysklad_element_base{
	function getName(){
		return 'embeddedEntityMetadata';
	}#m getName
}#c moysklad_embeddedEntityMetadata
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Base class for most (all?) elements in moysklad.
**/
abstract class moysklad_element_base extends DOMElement{
	/**
	* We need it, because in current DOM implementation nor DOMelement can append childs
	* ( http://www.sitepoint.com/forums/showthread.php?t=199386 ),
	* nor even DOMDocumentFragment until both had not produced from DOMDocument (->createElement or ->createDocumentFragment)
	*
	* @var Object(DOMDocument)
	**/
	protected $domf_;

	/**
	* Main method for overload. Must return element name
	**/
	abstract function getName();

	public function __construct($value = '', $namespaceURI = ''){
		parent::__construct($this->getName(), $value, $namespaceURI);

		$this->domf_ = new DOMDocument;
		$this->domf_->appendChild($this);
		$this->domf_->registerNodeClass('DOMElement', __CLASS__);
		if (is_null($value)) $this->set_xsi_nil();
		// http://bugs.php.net/bug.php?id=51462
//		$GLOBALS['PHP_HACK_DOM_ELEMENT'][spl_object_hash($rootElement)] = $rootElement; //Note, reference is not enough :(
//		$GLOBALS['PHP_HACK_DOM_ELEMENT'][spl_object_hash($this)] = $this; //Note, reference is not enough :(
	}#__c

	/**
	* http://bugs.php.net/bug.php?id=51462
	**/
	public function __destruct() {
//	unset($GLOBALS['PHP_HACK_DOM_ELEMENT'][spl_object_hash($this)]);
	}#__destructor

	/**
	* Turn element into someting like: <workflow xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
	*
	* @return	&$this
	**/
	public function &set_xsi_nil(){
		$this->domf_->documentElement->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$this->domf_->documentElement->setAttribute('xsi:nil', 'true');
		return $this;
	}#m set_xsi_nil

	/**
	* @var	DOMElement $dom
	**/
	function __get($name){
		if('dom' == $name) return $this->domf_->documentElement;
		else throw new moysklad_exception_absentElement("$name is not present");
	}#m getDOMElement

	/**
	* Get string representation of XML document.
	*
	* @param	array	$opts Array of options, which must be applyed to DOMDocument
	*					object first. As array of Key=>value. Nothing checked.
	* @return	string
	**/
	public function saveXML(array $opts = array( 'formatOutput' => true, 'encoding' => 'utf-8', 'preserveWhiteSpace' => true )){
		$dom = new DOMDocument('1.0'); // DOMDocument NEEDED ot import into it nodes, it also NEEDED to export result asXML...
		$dom->appendChild($dom->importNode($this->dome_, true));
		foreach ($opts as $opt => $val){
			$dom->{$opt} = $val;
		}
		return $dom->saveXML();
	}#m saveXML
}#c moysklad_element_base
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element deliveries-supply.
**/
class moysklad_deliveries_supply extends moysklad_element_base{
	function getName(){
	return 'deliveries-supply';
	}#m getName
}#c moysklad_deliveries_supply

/**
* Moysklad element script.
**/
class moysklad_supply extends moysklad_element_base {
	function getName() {
		return 'supply';
	}#m getName
}#c moysklad_supply
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element deliveries-demand.
**/
class moysklad_deliveries_demand extends moysklad_element_base{
	function getName(){
		return 'deliveries-demand';
	}#m getName
}#c moysklad_deliveries_demand

/**
* Moysklad element script.
**/
class moysklad_demand extends moysklad_element_base{
	function getName(){
		return 'demand';
	}#m getName
}#c moysklad_demand
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element customerOrders.
**/
class moysklad_customerOrders extends moysklad_element_base{
	function getName(){
		return 'customerOrders';
	}#m getName
}#c moysklad_customerOrders

/**
* Moysklad element script.
**/
class moysklad_customerOrder extends moysklad_element_base {
	function getName() {
		return 'customerOrder';
	}#m getName
}#c moysklad_customerOrder
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element customEntityMetadata.
**/
class moysklad_customEntityMetadata extends moysklad_element_base{
	function getName(){
	return 'customEntityMetadata';
	}#m getName
}#c moysklad_customEntityMetadata
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element customEntity.
**/
class moysklad_customEntity extends moysklad_element_base{
	function getName(){
		return 'customEntity';
	}#m getName
}#c moysklad_customEntity
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element currencies.
**/
class moysklad_currencies extends moysklad_element_base{
	function getName(){
		return 'currencies';
	}#m getName
}#c moysklad_currencies

/**
* Moysklad element script.
**/
class moysklad_currency extends moysklad_element_base {
	function getName() {
		return 'currency';
	}#m getName
}#c moysklad_currency
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element country.
**/
class moysklad_country extends moysklad_element_base{
	function getName(){
		return 'country';
	}#m getName
}#c moysklad_country
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element contract.
**/
class moysklad_contract extends moysklad_element_base{
	function getName(){
		return 'contract';
	}#m getName
}#c moysklad_contract
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element consignments.
**/
class moysklad_consignments extends moysklad_element_base{
	function getName(){
		return 'consignments';
	}#m getName
}#c moysklad_consignments

/**
* Moysklad element script.
**/
class moysklad_consignment extends moysklad_element_base {
	function getName() {
		return 'consignment';
	}#m getName
}#c moysklad_consignment
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element connectors.
**/
class moysklad_connectors extends moysklad_element_base{
	function getName(){
		return 'connectors';
	}#m getName
}#c moysklad_connectors

/**
* Moysklad element script.
**/
class moysklad_connector extends moysklad_element_base {
	function getName() {
		return 'connector';
	}#m getName
}#c moysklad_connector
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element companies.
**/
class moysklad_companies extends moysklad_element_base{
	function getName(){
	return 'companies';
	}#m getName
}#c moysklad_companies

/**
* Moysklad element script.
**/
class moysklad_company extends moysklad_element_base {
	function getName() {
		return 'company';
	}#m getName
}#c moysklad_company
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element agents.
**/
class moysklad_agents extends moysklad_element_base{
	function getName(){
	return 'agents';
	}#m getName
}#c moysklad_agents

/**
* Moysklad element script.
**/
class moysklad_agent extends moysklad_element_base {
	function getName() {
		return 'agent';
	}#m getName
}#c moysklad_agent
?><?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-03-30 13:34
**/




class moysklad_exception extends BaseException{};
class moysklad_exception_absentElement extends moysklad_exception{};
class moysklad_exception_unemplimented extends moysklad_exception{};
class moysklad_exception_novalid extends moysklad_exception{};

/**
* Main moysklad XML class implementation.
* WARNING: It is very small part implemented. Only vast majority to goods exchange.
* @TODO Implement full by XML Scheme http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @author Pavel Alexeev aka Pahan-Hubbitus
* @created 2010-03-30 13:37
* @copyright 2010 Pavael Alexeev Aka Pahan-Hubbitus
**/
class moysklad{
	const XML_SCHEMA = 'http://www.moysklad.ru/schema/exchange-1.2.0.xsd';

	private $dom_;		// Main DOMDocument
	private $xpath_;	// DOMXpath object to perfom any queries

	/**
	* If explicit null element converted at start in something like:
	*	<workflow xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
	* @var array
	**/
	protected $root_elements = array(
		'workflow'				=> null
		,'shareModes'				=> null
		,'scripts'				=> null
		,'messages'				=> null
		,'customEntityMetadata'		=> null
		,'embeddedEntityMetadata'	=> null
		,'entityTemplatesMetadata'	=> null
		,'reportTemplatesMetadata'	=> null
		,'customEntity'			=> ''	// Empty
		,'reason'					=> ''
		,'currencies'				=> ''	// Empty
		,'country'				=> ''	// Empty
		,'gtd'					=> ''	// Empty
		,'uoms'					=> ''
		,'goodFolders'				=> ''
		,'goods'					=> ''
		,'service'				=> ''	// Empty
		,'things'					=> ''	// Empty
		,'myCompany'				=> ''	// Empty
		,'agents'					=> ''
		,'companies'				=> ''
		,'persons'				=> ''
		,'places'					=> null
		,'warehouses'				=> ''
		,'project'				=> ''	// Empty
		,'contract'				=> ''	// Empty
		,'processingPlans'			=> ''
		,'consignments'			=> ''
		,'priceLists'				=> null
		,'deliveries-demand'		=> null
		,'deliveries-supply'		=> null
		,'inventories'				=> null
		,'moves'					=> null
		,'losses'					=> null
		,'enters'					=> null
		,'invoicesIn'				=> null
		,'invoicesOut'				=> null
		,'processings'				=> null
		,'customerOrders'			=> null
		,'purchaseOrders'			=> null
		,'connectors'				=> null
	);

	public function __construct(){
		// DTD. http://www.php.net/manual/en/book.dom.php#78929
		$this->dom_ = new DOMDocument;
		$this->dom_->encoding = 'UTF-8';
		$this->dom_->validateOnParse = true;
		$this->dom_->standalone = true;
		$this->xpath_ = new DOMXPath($this->dom_);

		// This is 'constant' part
		$exchange = $this->dom_->appendChild($this->dom_->createElement('exchange'));

		foreach ($this->root_elements as $item => $value){
			
			$__class_name = 'moysklad_' . $item;
			$itemObj = new $__class_name($value);
			$this->commonAddRootElement($item, $itemObj);
		}
	}#__c

	private function commonAddRootElement($rootElementName, moysklad_element_base &$elem){
		$this->dom_->documentElement->appendChild($this->dom_->importNode($elem, true));
	}#m commonAddElement

	private function commonAddElement($rootElementName, moysklad_element_base &$elem){
		$this->dom_->getElementsByTagName($rootElementName)->item(0)->appendChild($this->dom_->importNode($elem, true));
	}#m commonAddElement

	/**
	* Add workflow element into document.
	*
	* @param	Object()	$item
	* @return	&$this
	**/
	public function &add_workflow(moysklad_workflow $item){
		$this->commonAddElement('workflow', $item);
		return $this;
	}#m add_workflow

	/**
	* Add shareMode element into document.
	*
	* @param	Object()	$item
	* @return	&$this
	**/
	public function &add_shareMode(moysklad_workflow $item){
		$this->commonAddElement('shareModes', $item);
		return $this;
	}#m add_shareMode

	/**
	* Add script element into document.
	*
	* @param	Object()	$item
	* @return	&$this
	**/
	public function &add_script(moysklad_workflow $item){
		$this->commonAddElement('scripts', $item);
		return $this;
	}#m add_script

	/**
	* Add message element into document.
	*
	* @param	Object(moysklad_message)	$item
	* @return	&$this
	**/
	public function &add_message(moysklad_message $item) {
		$this->commonAddElement('messages', $item);
		return $this;
	}#m add_message

	/**
	* Add customEntityMetadata element into document.
	*
	* @param	Object(moysklad_customEntityMetadata)	$item
	* @return	&$this
	**/
	public function &add_customEntityMetadata(moysklad_customEntityMetadata $item) {
		$this->commonAddElement('customEntityMetadata', $item);
		return $this;
	}#m add_customEntityMetadata

	/**
	* Add embeddedEntityMetadata element into document.
	*
	* @param	Object(moysklad_embeddedEntityMetadata)	$item
	* @return	&$this
	**/
	public function &add_embeddedEntityMetadata(moysklad_embeddedEntityMetadata $item){
		$this->commonAddElement('embeddedEntityMetadata', $item);
		return $this;
	}#m add_embeddedEntityMetadata


	/**
	* Add entityTemplatesMetadata element into document.
	*
	* @param	Object(moysklad_entityTemplatesMetadata)	$item
	* @return	&$this
	**/
	public function &add_entityTemplatesMetadata(moysklad_entityTemplatesMetadata $item) {
		$this->commonAddElement('entityTemplatesMetadata', $item);
		return $this;
	}#m add_entityTemplatesMetadata


	/**
	* Add reportTemplatesMetadata element into document.
	*
	* @param	Object(moysklad_reportTemplatesMetadata)	$item
	* @return	&$this
	**/
	public function &add_reportTemplatesMetadata(moysklad_reportTemplatesMetadata $item){
		$this->commonAddElement('reportTemplatesMetadata', $item);
		return $this;
	}#m add_reportTemplatesMetadata


	/**
	* Add customEntity element into document.
	*
	* @param	Object(moysklad_customEntity)	$item
	* @return	&$this
	**/
	public function &add_customEntity(moysklad_customEntity $item) {
		$this->commonAddElement('customEntity', $item);
		return $this;
	}#m add_customEntity

	/**
	* Add lossReason element into document.
	*
	* @param	Object(moysklad_lossReason)	$item
	* @return	&$this
	**/
	public function &add_lossReason(moysklad_lossReason $item) {
		$this->commonAddElement('reason', $item);
		return $this;
	}#m add_lossReason

	/**
	* Add enterReason element into document.
	*
	* @param	Object(moysklad_enterReason)	$item
	* @return	&$this
	**/
	public function &add_enterReason(moysklad_enterReason $item) {
		$this->commonAddElement('reason', $item);
		return $this;
	}#m add_enterReason

	/**
	* Add currency element into document.
	*
	* @param	Object(moysklad_currency)	$item
	* @return	&$this
	**/
	public function &add_currency(moysklad_currency $item) {
		$this->commonAddElement('currencies', $item);
		return $this;
	}#m add_currency

	/**
	* Add country element into document.
	*
	* @param	Object(moysklad_country)	$item
	* @return	&$this
	**/
	public function &add_country(moysklad_country $item) {
		$this->commonAddElement('country', $item);
		return $this;
	}#m add_country

	/**
	* Add gtd element into document.
	*
	* @param	Object(moysklad_gtd)	$item
	* @return	&$this
	**/
	public function &add_gtd(moysklad_gtd $item) {
		$this->commonAddElement('gtd', $item);
		return $this;
	}#m add_gtd

	/**
	* Add uom (Unit Of Metric?) element into document.
	*
	* @param	Object(moysklad_uom)	$item
	* @return	&$this
	**/
	public function &add_uom(moysklad_uom $item) {
		$this->commonAddElement('uoms', $item);
		return $this;
	}#m add_uom

	/**
	* Add goodFolder element into document.
	*
	* @param	Object(moysklad_goodFolder)	$item
	* @return	&$this
	**/
	public function &add_goodFolder(moysklad_goodFolder $item) {
		$this->commonAddElement('goodFolders', $item);
		return $this;
	}#m add_goodFolder

	/**
	* Add good element into document.
	*
	* @param	Object(moysklad_good)	$item
	* @return	&$this
	**/
	public function &add_good(moysklad_good $item) {
		$this->commonAddElement('goods', $item);
		return $this;
	}#m add_good

	/**
	* Add service element into document.
	*
	* @param	Object(moysklad_service)	$item
	* @return	&$this
	**/
	public function &add_service(moysklad_service $item) {
		$this->commonAddElement('service', $item);
		return $this;
	}#m add_service

	/**
	* Add thing element into document.
	*
	* @param	Object(moysklad_thing)	$item
	* @return	&$this
	**/
	public function &add_thing(moysklad_thing $item) {
		$this->commonAddElement('things', $item);
		return $this;
	}#m add_thing

	/**
	* Add myCompany element into document.
	*
	* @param	Object(moysklad_myCompany)	$item
	* @return	&$this
	**/
	public function &add_myCompany(moysklad_myCompany $item) {
		$this->commonAddElement('myCompany', $item);
		return $this;
	}#m add_myCompany

	/**
	* Add agent (поставщик) element into document.
	*
	* @param	Object(moysklad_agent)	$item
	* @return	&$this
	**/
	public function &add_agent(moysklad_agent $item) {
		$this->commonAddElement('agents', $item);
		return $this;
	}#m add_agent

	/**
	* Add company (контрагент) element into document.
	*
	* @param	Object(moysklad_company)	$item
	* @return	&$this
	**/
	public function &add_company(moysklad_company $item) {
		$this->commonAddElement('companies', $item);
		return $this;
	}#m add_company

	/**
	* Add person element into document.
	*
	* @param	Object(moysklad_person)	$item
	* @return	&$this
	**/
	public function &add_person(moysklad_person $item) {
		$this->commonAddElement('persons', $item);
		return $this;
	}#m add_person

	/**
	* Add place element into document.
	*
	* @param	Object(moysklad_place)	$item
	* @return	&$this
	**/
	public function &add_place(moysklad_place $item) {
		$this->commonAddElement('places', $item);
		return $this;
	}#m add_place

	/**
	* Add warehouse element into document.
	*
	* @param	Object(moysklad_warehouse)	$item
	* @return	&$this
	**/
	public function &add_warehouse(moysklad_warehouse $item) {
		$this->commonAddElement('warehouses', $item);
		return $this;
	}#m add_warehouse

	/**
	* Add project element into document.
	*
	* @param	Object(moysklad_project)	$item
	* @return	&$this
	**/
	public function &add_project(moysklad_project $item) {
		$this->commonAddElement('project', $item);
		return $this;
	}#m add_project

	/**
	* Add contract element into document.
	*
	* @param	Object(moysklad_contract)	$item
	* @return	&$this
	**/
	public function &add_contract(moysklad_contract $item) {
		$this->commonAddElement('contract', $item);
		return $this;
	}#m add_contract

	/**
	* Add processingPlan (сборка-производство) element into document.
	*
	* @param	Object(moysklad_processingPlan)	$item
	* @return	&$this
	**/
	public function &add_processingPlan(moysklad_processingPlan $item) {
		$this->commonAddElement('processingPlans', $item);
		return $this;
	}#m add_processingPlan

	/**
	* Add consignment (сборка, технологическая карта?) element into document.
	*
	* @param	Object(moysklad_consignment)	$item
	* @return	&$this
	**/
	public function &add_consignment(moysklad_consignment $item) {
		$this->commonAddElement('consignments', $item);
		return $this;
	}#m add_consignment

	/**
	* Add priceList element into document.
	*
	* @param	Object(moysklad_priceList)	$item
	* @return	&$this
	**/
	public function &add_priceList(moysklad_priceList $item) {
		$this->commonAddElement('priceLists', $item);
		return $this;
	}#m add_priceList

	/**
	* Adddemand element into document.
	*
	* @param	Object(moysklad_demand)	$item
	* @return	&$this
	**/
	public function &add_demand(moysklad_demand $item) {
		$this->commonAddElement('deliveries-demand', $item);
		return $this;
	}#m add_demand

	/**
	* Add supply element into document.
	*
	* @param	Object(moysklad_supply)	$item
	* @return	&$this
	**/
	public function &add_supply(moysklad_supply $item) {
		$this->commonAddElement('deliveries-supply', $item);
		return $this;
	}#m add_supply

	/**
	* Add inventory element into document.
	*
	* @param	Object(moysklad_inventory)	$item
	* @return	&$this
	**/
	public function &add_inventory(moysklad_inventory $item) {
		$this->commonAddElement('inventories', $item);
		return $this;
	}#m add_inventory

	/**
	* Add move element into document.
	*
	* @param	Object(moysklad_move)	$item
	* @return	&$this
	**/
	public function &add_move(moysklad_move $item) {
		$this->commonAddElement('moves', $item);
		return $this;
	}#m add_move

	/**
	* Add loss element into document.
	*
	* @param	Object(moysklad_loss)	$item
	* @return	&$this
	**/
	public function &add_loss(moysklad_loss $item) {
		$this->commonAddElement('losses', $item);
		return $this;
	}#m add_loss

	/**
	* Add enter element into document.
	*
	* @param	Object(moysklad_enter)	$item
	* @return	&$this
	**/
	public function &add_enter(moysklad_enter $item) {
		$this->commonAddElement('enters', $item);
		return $this;
	}#m add_enter

	/**
	* Add invoiceIn element into document.
	*
	* @param	Object(moysklad_invoiceIn)	$item
	* @return	&$this
	**/
	public function &add_invoiceIn(moysklad_invoiceIn $item) {
		$this->commonAddElement('invoiceIn', $item);
		return $this;
	}#m add_invoiceIn

	/**
	* Add invoicesOut element into document.
	*
	* @param	Object(moysklad_invoicesOut)	$item
	* @return	&$this
	**/
	public function &add_invoicesOut(moysklad_invoicesOut $item) {
		$this->commonAddElement('invoicesOut', $item);
		return $this;
	}#m add_invoicesOut

	/**
	* Add processing element into document.
	*
	* @param	Object(moysklad_processing)	$item
	* @return	&$this
	**/
	public function &add_processing(moysklad_processing $item) {
		$this->commonAddElement('processings', $item);
		return $this;
	}#m add_processing

	/**
	* Add customerOrder element into document.
	*
	* @param	Object(moysklad_customerOrder)	$item
	* @return	&$this
	**/
	public function &add_customerOrder(moysklad_customerOrder $item) {
		$this->commonAddElement('customerOrders', $item);
		return $this;
	}#m add_customerOrder

	/**
	* Add purchaseOrder element into document.
	*
	* @param	Object(moysklad_purchaseOrder)	$item
	* @return	&$this
	**/
	public function &add_purchaseOrder(moysklad_purchaseOrder $item) {
		$this->commonAddElement('purchaseOrders', $item);
		return $this;
	}#m add_purchaseOrder

	/**
	* Add connector element into document.
	*
	* @param	Object(moysklad_connector)	$item
	* @return	&$this
	**/
	public function &add_connector(moysklad_connector $item) {
		$this->commonAddElement('connectors', $item);
		return $this;
	}#m add_connector

	/**
	* Validate XML by its scherma http://www.moysklad.ru/schema/exchange-1.1.0.xsd
	*
	* Throws(moysklad_exception_novalid)
	**/
	public function &schemaValidate(){
		if (!$this->dom_->schemaValidate('http://www.moysklad.ru/schema/exchange-1.1.0.xsd')){
			throw new moysklad_exception_novalid('Documen does no valid!');
		}
		return $this;
	}#m schemaValidate

	/**
	* Get string representation of XML document.
	*
	* @param	array	$opts Array of options, which must be applyed to DOMDocument
	*					object first. As array of Key=>value. Nothing checked.
	* @return	string
	* Throws(moysklad_exception_novalid)
	**/
	public function saveXML(array $opts = array( 'formatOutput' => true )){
		foreach ($opts as $opt => $val){
			$this->dom_->{$opt} = $val;
		}
		return $this->dom_->saveXML();
	}#m saveXML
}#c moysklad
?><?php
/**
* Dellin.ru API.
*
* @package dellin.ru
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @link http://dellin.ru/developers/
* @created 2010-10-31 11:21
**/



/**
* Class to represent region and set map to its in X-Cart database
*
* @uses REQUIRED_VAR
**/
class dellinRegion{
/**
 * stateid xcart_states table field to map X-Cart DB data
 * @var	int
 */
public $stateid;
/**
 * code xcart_states table field to map X-Cart DB data
 * @var	int
 */
public $code;
/**
 * code xcart_states table field to map X-Cart DB data. String representation. Excessive.
 * @var	string
 */
public $state;

/**
 * No match untill performed.
 * @const	int
 */
const MATCH_NOT_TRIED = -1;
/**
 * No match to X-Cart item. All MATCH_* constant represent match only exactly 1
 * match present. Otherwise it treated as no match.
 * @const	int
 */
const MATCH_NO = 0;
/**
 * Match from config
 * @const	int
 */
const MATCH_BY_CONFIG = 1;
/**
 * Match by starts (LIKE) with full region name
 * @const	int
 */
const MATCH_BY_FULL_NAME = 2;
/**
 * Match by starts (LIKE) with short region name
 * @const	int
 */
const MATCH_BY_SHORT_NAME = 4;

/**
 * Last match type.
 * @var	int
 */
private $matchType = self::MATCH_NOT_TRIED;

/**
 * Original city from XML file to what we try match it in DB
 * @var SimpleXMLElement.
 */
public $toMatch;

	/**
	 * Construct object from array with same keys.
	 *
	 * @param unknown_type $reg
	 */
	function __construct(array $reg) {
		foreach(array('stateid', 'code', 'state', 'matchType') as $var){
		$this->{$var} = REQUIRED_VAR($reg[$var]);
		}
	// It is optional and in config will not be present.
	$this->toMatch = @$reg['toMatch'];
	}#__c
}#c dellinRegion
?>
<?php
/**
* Dellin.ru API.
*
* @package dellin.ru
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @link http://dellin.ru/developers/
* @created 2010-10-31 11:21
**/

class dellinNotMatchedException extends BaseException{};
class dellinNotApplicableException extends dellinNotMatchedException{};

/**
* Class to parse dellin.ru Cities of delivery and map its to our current S-Cart database values (inspired by SPSR structure).
* Parsing limited only to Russia.
*
* @uses	HuClass, REQUIRED_VAR
* @example	to_conf(mapToSpsr).php
**/
class dellinParse{
const URL_CITIES = 'http://public.services.dellin.ru/calculatorService2/index.html?request=xmlForm';

/**
 * Object of city-regions XML document
 * @var	SimpleXMLElement
 */
private $xml_;
/**
 * Current parsed region. To do not return it from methods tied several techniques.
 * @var	dellinRegion
 */
private $curRegion_;

/**
 * Surrent parsed by RegExp city.
 * @var	HuArray
 */
private $curCity_;

/**
 * Current original city from XML file to what we try match it in DB
 * @var	SimpleXMLElement
 */
private $curCityToMatch_;

/**
 * Base part of SQL query to try several type matches of cities.
 * @const	string
 */
const MATCH_SQL_CITY = 'SELECT
		c.countyid, c.stateid, county, state, code as state_code, country_code
	FROM
		xcart_counties c LEFT JOIN xcart_states s ON (s.stateid = c.stateid)
	WHERE country_code = "RU" ';

/**
 * Base part of SQL query to try several type matches of regions.
 * @const	string
 */
const MATCH_SQL_REGION = 'SELECT *
	FROM
		xcart_states
	WHERE country_code = "RU" ';

/**
* Accumulate axceptions during match process of cities.
* $var	HuArray
**/
private $exceptionsCity;

/**
* Accumulate axceptions during match process of cities.
* $var	HuArray
**/
private $exceptionsRegion;

/**
 * HuArray of HuArrays matched cities with keys:
 *	city - parsed city (Object(dellinCity))
 *	exceptionsCity - HuArray of exception parsing
 * @var	HuArray
 */
private $matchedCities;
/**
 * HuArray of HuArrays not matched cities with keys:
 *	city - city (Object(HuArray))
 *	exceptionsCity - HuArray of exceptions parsing city
 *	region - region if present
 *	exceptionsRegion - HuArray of exceptions parsing region
 * @var	HuArray
 */
private $notMatchedCities;

	/**
	*
	* @param	string $xmlSource Filename or URL of XML file.
	**/
	public function __construct($xmlSource){
	$this->xml_ = simplexml_load_file($xmlSource);
	$this->matchedCities = new HuArray();
	$this->notMatchedCities = new HuArray();
	}#__c

	/**
	* Main horse - cycle by all presented values and try mach it to current set
	* in various way - start from most precisious to less.
	*
	* For cities wher no matches found start {@see matchRegion()} to do not get
	* not required overhead.
	**/
	public function match(){
		foreach ($this->xml_->cities->city as $this->curCityToMatch_){
		$this->curCity_ = classCreate('RegExp_pcre', '/([^()]+)(?>\s|$)(?:\(((\pL+).*?)\))?/iu', (string)$this->curCityToMatch_->name)->doMatch()->getHuMatches();
		$this->exceptionsCity = new HuArray();
			try{
			$matched = $this->_matchByConfig();
			}
			catch(dellinNotMatchedException $e){
			$this->exceptionsCity->push($e);
				try{
				$matched = $this->_matchWithRegionByConfig();
				}
				catch(dellinNotMatchedException $e){
				$this->exceptionsCity->push($e);
					try{
					$matched = $this->_matchWithFullRegion();
					}
					catch(dellinNotMatchedException $e){
					$this->exceptionsCity->push($e);
						try{
						$matched = $this->_matchWithShortRegion();
						}
						catch(dellinNotMatchedException $e){
						$this->exceptionsCity->push($e);
							try{
							$matched = $this->_matchWithoutRegion();
							}
							catch(dellinNotMatchedException $e){
							$this->exceptionsCity->push($e);
//							dump::a("STOP. No match reached for '{$this->curCity_->{0}}'. exceptions trace: " . $this->exceptionsCity->reduce(create_function('$v,$w', 'if (0===$v) $v="";/* <- Hack for PHP < 5.3.0 */ $v .= "\n" . $w->getMessage(); return $v;')), 'Match not found');
							$this->notMatchedCities->push(
								new HuArray(
									array(
										'city' => $this->curCity_
										,'region' => $this->matchRegion()
										,'exceptionsCity' => $this->exceptionsCity
										,'exceptionsRegion' => $this->exceptionsRegion
									)
								)
							);
							continue; //No more match types available
							}
						}
					}
				}
			}
		$this->matchedCities->push(
			new HuArray(
				array(
					'city' => $matched
					,'exceptionsCity' => $this->exceptionsCity
				)
			)
		);
		}
	}#m match

	/**
	* Very similar to {@see match()} method, but starts similar match process
	*	for region, not city.
	* Do NOT do any cicles and try match only current region! As write before
	*	intended for use from {@see match()}, but may be invoked outside also.
	*
	* For optimisation purposes we use it from match only for not matched cities,
	* where used tables joins for selects, which should be more efficient then
	* subsequents queries
	*
	* @return	Object(dellinRegion)
	**/
	public function matchRegion(){
	$this->exceptionsRegion = new HuArray();
		try{
		$matched = $this->_matchRegionByConfig();
		}
		catch(dellinNotMatchedException $e){
		$this->exceptionsRegion->push($e);
			try{
			$matched = $this->_matchRegionByFullName();
			}
			catch(dellinNotMatchedException $e){
			$this->exceptionsRegion->push($e);
				try{
				$matched = $this->_matchRegionByShortName();
				}
				catch(dellinNotMatchedException $e){
				$this->exceptionsRegion->push($e);
				return null;
				}
			}
		}
	return $matched;
	}#m matchRegion

	/**
	* Helper function to dump all results of query result.
	*
	* @param	database	$db
	* @return	void|string
	**/
	private function _dumpDBresults(database &$db, $return = true){
	$ret = "\n";
		while ($db->sql_fetch_object()){
			if ($return){
			$ret .= rtrim(dump::log($db->RES, 'match', true));
			}
			else{
			dump::a($db->RES, 'match');
			}
		}
		if($return) return $ret;
	}#m _dumpDBresults

	/**
	* Direct return city from config. Throw exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchByConfig(){
		if (!($city = CONF('cities')->{$this->curCity_->{1}}))
		throw new dellinNotMatchedException("City '{$this->curCity_->{1}}' not found in config cities. Method: " . __METHOD__);
	$city->toMatch = $this->curCityToMatch_;
	return $city;
	}#m _matchByConfig

	/**
	* Direct return region from config. Throw exception otherwise.
	*
	* @return Object(dellinRegion)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchRegionByConfig(){
		if (!($this->curRegion_ = CONF('regions')->{$this->curCity_->{2}}))
		throw new dellinNotMatchedException("Region '{$this->curCity_->{2}}' not found in config regions. Method: " . __METHOD__);
	$th->toMatch = $this->curCityToMatch_;
	return $this->curRegion_;
	}#m _matchRegionByConfig

	/**
	* Tries match exact city name in DB and region from config.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchWithRegionByConfig(){
		try{
		$this->_matchRegionByConfig();
		}
		catch(dellinNotApplicableException $e){
		throw new dellinNotApplicableException("Region '{$this->curCity_->{2}}' is not found in config, this matching is not possible. Method: " . __METHOD__);
		}
	$sql = self::MATCH_SQL_CITY . "AND county = '{$this->curCity_->{1}}' AND c.stateid = {$this->curRegion_->stateid}  AND code = {$this->curRegion_->code}";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("'{$this->curCity_->{1}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . '. Matches: ' . $this->_dumpDBresults(db(), true));
	db()->sql_fetch_object();
	return new dellinCity(
		array(
			'countyid'	=> db()->RES->countyid
			,'stateid'	=> db()->RES->stateid
			,'county'		=> db()->RES->county
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinCity::MATCH_WITH_REGION_BY_CONFIG
		)
	);
	}#m _matchWithRegionByConfig

	/**
	* Tries match exact city name in DB and region by starts (LIKE) from full name.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchWithFullRegion(){
		if(!$this->curCity_->{2}){
		throw new dellinNotApplicableException('Full region name is not present, this matching is not possible. Method: ' . __METHOD__);
		}
	$sql = self::MATCH_SQL_CITY . "AND county = '{$this->curCity_->{1}}' AND state LIKE '{$this->curCity_->{2}}%'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("'{$this->curCity_->{1}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . ' (' . $this->curCity_->{2} . ').' . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinCity(
		array(
			'countyid'	=> db()->RES->countyid
			,'stateid'	=> db()->RES->stateid
			,'county'		=> db()->RES->county
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinCity::MATCH_WITH_FULL_REGION
		)
	);
	}#m _matchWithFullRegion

	/**
	* Tries match by start region full name in DB.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinRegion)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchRegionByFullName(){
		if(!$this->curCity_->{2}){
		throw new dellinNotApplicableException('Full region name is not present, this matching is not possible. Method: ' . __METHOD__);
		}
	$sql = self::MATCH_SQL_REGION . "AND state LIKE '{$this->curCity_->{2}}%'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("Region '{$this->curCity_->{2}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinRegion(
		array(
			'stateid'		=> db()->RES->stateid
			,'code'		=> db()->RES->code
			,'state'		=> db()->RES->state
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinRegion::MATCH_BY_FULL_NAME
		)
	);
	}#m _matchRegionByFullName

	/**
	* Tries match exact city name in DB and region by starts (LIKE) from short name.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchWithShortRegion(){
		if(!$this->curCity_->{3}){
		throw new dellinNotApplicableException('Short region name is not present, this matching is not possible. Method: ' . __METHOD__);
		}
	$sql = self::MATCH_SQL_CITY . "AND county = '{$this->curCity_->{1}}' AND state LIKE '{$this->curCity_->{3}}%'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("'{$this->curCity_->{1}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . ' (' . $this->curCity_->{3} . ').' . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinCity(
		array(
			'countyid'	=> db()->RES->countyid
			,'stateid'	=> db()->RES->stateid
			,'county'		=> db()->RES->county
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinCity::MATCH_WITH_SHORT_REGION
		)
	);
	}#m _matchWithShortRegion

	/**
	* Tries match by start region full name in DB.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinRegion)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchRegionByShortName(){
		if(!$this->curCity_->{3}){
		throw new dellinNotApplicableException('Short region name is not present, this matching is not possible. Method: ' . __METHOD__);
		}
	$sql = self::MATCH_SQL_REGION . "AND state LIKE '{$this->curCity_->{3}}%'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("Region '{$this->curCity_->{3}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinRegion(
		array(
			'stateid'		=> db()->RES->stateid
			,'code'		=> db()->RES->code
			,'state'		=> db()->RES->state
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinRegion::MATCH_BY_FULL_NAME
		)
	);
	}#m _matchRegionByShortName

	/**
	* Tries match exact city name in DB and region by starts (LIKE) from full name.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchWithoutRegion(){
	$sql = self::MATCH_SQL_CITY . "AND county = '{$this->curCity_->{1}}'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("'{$this->curCity_->{1}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . '.' . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinCity(
		array(
			'countyid'	=> db()->RES->countyid
			,'stateid'	=> db()->RES->stateid
			,'county'		=> db()->RES->county
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinCity::MATCH_WITHOUT_REGION
		)
	);
	}#m _matchWithoutRegion

	/**
	 * Return HuArray of matched cities
	 * @return	HuArray
	 */
	public function getMatchedCities(){
	return $this->matchedCities;
	}#m getMatchedCities

	/**
	 * Return HuArray of NOT matched cities
	 *
	 * @param	string	$whatRegion. String, represent what set needed, available values are:
	 *	'all'				- All not matched cities. Default.
	 *	'withMatchedRegion'		- Only with matched region
	 *	'withNotMatchedRegion'	- Only with region which is not matched
	 * @return	HuArray CLONED, not reference!
	 * @Throws	VariableRangeException
	 */
	public function getNotMatchedCities($whatRegion = 'all'){
		switch($whatRegion){
			case 'all':
				return HuClass::cloning($this->notMatchedCities);
			break;
			case 'withMatchedRegion':
				return HuClass::cloning($this->notMatchedCities)->filter(create_function('$v', 'return !is_null($v->region);'));
			break;
			case 'withNotMatchedRegion':
				return HuClass::cloning($this->notMatchedCities)->filter(create_function('$v', 'return is_null($v->region);'));
			break;
			default:
				throw new VariableRangeException("'$whatRegion' is not proper value of \$whatRegion parameter");
		}

	}#m getNotMatchedCities

	/**
	 * Build text representation of array for config-file in var_export format
	 *	to map matched values
	 *
	 * @return	string
	 */
	public function buildMapConfig(){
	$ret = "\$GLOBALS['__CONFIG']['dellin'] = array(
	#countyid => XML id\n";
		foreach ($this->getMatchedCities() as $city){
 		$ret .= "\t" . $city->city->getPHPConfigLine() . ",\n";
		}
	return $ret .= ');';
	}#m buildMapConfig

	/**
	 * Build SQL insertions text of new citties where region parsed.
	 *
	 * @return	string
	 */
	public function buildInsertsOfNewCitites(){
	$ret = '';
		// All new, but with region parsed
		foreach ($this->getNotMatchedCities('withMatchedRegion') as $city){
		$ret .= 'INSERT INTO xcart_counties (stateid, county) VALUES (' . REQUIRED_VAR($city->region->stateid) . ', "' . REQUIRED_VAR($city->city->{1}) . '");' . "\n";
		}
	return $ret;
	}#m buildInsertsOfNewCitites
};//c dellinParse
?><?php
/**
* Dellin.ru API.
*
* @package dellin.ru
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @link http://dellin.ru/developers/
* @created 2010-10-31 11:21
**/



/**
 * Class represent dellin.ru city entity mapped to one in X-Cart FB (SPSR)
 *
 * @uses REQUIRED_VAR
 */
class dellinCity{
/**
 * countyid xcart_counties table field to map X-Cart DB data
 * @var	int
 */
private $countyid;
/**
 * stateid xcart_counties table field to map X-Cart DB data
 * @var	int
 */
private $stateid;
/**
 * county xcart_counties table field to map X-Cart DB data
 * @var	string
 */
private $county;

/**
 * Original city from XML file to what we try match it in DB
 * @var SimpleXMLElement.
 */
public $toMatch;

/**
 * No match untill performed.
 * @const	int
 */
const MATCH_NOT_TRIED = -1;
/**
 * No match to X-Cart item. All MATCH_* constant represent match only exactly 1
 * match present. Otherwise it treated as no match.
 * @const	int
 */
const MATCH_NO = 0;
/**
 * Exact match
 * @const	int
 */
const MATCH_EXACT = 1;
/**
 * Match with region from config
 * @const	int
 */
const MATCH_WITH_REGION_BY_CONFIG = 2;
/**
 * Match with full region name
 * @const	int
 */
const MATCH_WITH_FULL_REGION = 4;
/**
 * Match with short region name
 * @const	int
 */
const MATCH_WITH_SHORT_REGION = 8;
/**
 * Match only without region.
 * @const	int
 */
const MATCH_WITHOUT_REGION = 16;
/**
 * Last match type.
 * @var	int
 */
private $matchType = self::MATCH_NOT_TRIED;

	/**
	 *
	 * @param array $arr Instantiate from: array('countyid', 'stateid', 'county', 'matchType')
	 */
	public function  __construct(array $arr){
		foreach (array('countyid', 'stateid', 'county', 'matchType') as $var){
		$this->{$var} = REQUIRED_VAR($arr[$var]);
		}
	// It is optional and in config will not be present.
	$this->toMatch = @$arr['toMatch'];
	}#__c

	/**
	 * Return line to use in config-file in var_export format to match itself
	 * Primarly for matched cities.
	 *
	 * @return	string
	 */
	public function getPHPConfigLine(){
	return $this->countyid . "\t=> '" . REQUIRED_VAR($this->toMatch->id) . '\'';
	}#m getPHPConfigLine
}#c dellinCity
?>
<?
/**
* SPSR infrastructure support to online shipping rates calculation ( http://www.cpcr.ru/calculator.html )
*
* @package SPSR
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @example SPSR_in_parse.example.php
* @created 2009-05-25 11:56
**/

/**
* Class to parse and manipulate very different cpcr.ru data for online tariff calculation.
*
* SPSR have many files wit different structure and organazed through arse!
* Futhermore it have not normal specification how parse and links it.
* Even more evil - answers of support frequently crudely and incompetent (sometimes different from different people).
* This class is essential which I can understand from very-very-very long correspondence with this company.
*
* We want on out unified structure, but in fact have ABSOLUTELY different
* scheme on in:
* 1) In the Russia(Россия) there defined regions: Region XML elements in the same file, where Countries defined:
*		http://www.cpcr.ru/cgi-bin/postxml.pl?Regions
* 2) But regions is NOT defined for other Countries. So, for unified approach we will use self::$no_region_name (by def: '---') single region.
* 3) The next exception is Belarus (Белоруссия). In SPSR notation it is Region of Russia! (<Regions Id="55" Owner_Id="2" RegionName="Белоруссия"/>)
*		and its cities located in Russian xml file by region!
* 4) Belarus have region with same name as country! It renamed to self::no_region_name.
* 5) The next step is Cities.
*	5.1) For the Russia it is localted in file http://www.cpcr.ru/components/xml/cities.xml
*	5.2) For all other countries in - http://www.cpcr.ru/components/xml/citiesc.xml
*	And what is more bad - files have different structures! So, we must handle its separately.
* 6) Some incorrect and strange items filtered by Regexp in SPSR_in_parse::filterOut
* 7) Some regions (Russia) have cities with the same name it own.
*	7.1) Such cities filtered out.
*	7.2) If after all filters Region has not any Cities, add one "meta" with name self::$all_cities_name and
*		other attributes from city filtered in 7.1 or just create new one with name self::$all_cities_name and
*		copied attributes: "Owner_Id" => "Region_Owner_ID", "Id" => "Region_ID" from region.
**/
class SPSR_in_parse{
static $no_region_name = '---';
static $all_cities_name = 'Все';

private $_regionsfile;
private $_citiesfile;
private $_citiescfile;

private /* DOMDocument */ $_regionsXML;
private /* DOMDocument */ $_citiesXML;
private /* DOMDocument */ $_citiescXML;

protected $_mainXML;
protected $_xpath;
protected $_compiled = false;

/**
* Filter some strange and incorrect items like "Дальнее зарубежье" region of Russia!. Regexp.
* @var	array $filterOut
**/
public $filterOut = array(
//	'countries'
	'regions'	=> '/Дальнее зарубежье/'
//	'cities'
);

	public function __construct(
		//$regionsfile = 'Regions.xml', $citiesfile = 'cities.xml', $citiescfile = 'citiesc.xml'
		$regionsfile	= 'http://www.cpcr.ru/cgi-bin/postxml.pl?Regions'
		,$citiesfile	= 'http://www.cpcr.ru/components/xml/cities.xml'
		,$citiescfile	= 'http://www.cpcr.ru/components/xml/citiesc.xml'
		,$outencoding	= 'utf-8'
	){
	$this->_regionsfile = $regionsfile;
	$this->_citiesfile	= $citiesfile;
	$this->_citiescfile	= $citiescfile;

	$this->_regionsXML = new DOMDocument('1.0');
	$this->_regionsXML->load($this->_regionsfile);

	$this->_mainXML = new DOMDocument('1.0', $outencoding);
	$root = $this->_mainXML->appendChild(new DOMElement('Root'));
	$this->_xpath = new DOMXPath($this->_regionsXML);
	}#__c

	/**
	* Compile all mesh of foles CPCR to one well-signed structure.
	*
	* @param	string	XPAth string to select countries, which interested now.
	* @return	&$this
	**/
	public function &compile($needCountries = '//Countries[normalize-space(@Country_Name)="Россия" or normalize-space(@Country_Name)="Белоруссия" or normalize-space(@Country_Name)="Украина"]'){
		foreach($this->_xpath->query($needCountries) as $country){
		$this->compileCountry($country);
		}
	$this->_compiled = true;
	return $this;
	}#m compile

	/**
	* Compile separate country.
	*
	* @param	Object(DOMElement) $country
	* @return	&$this
	**/
	protected function compileCountry(DOMElement $country){
	$doc = new DOMDocument('1.0', 'utf-8'); // DOMDocument NEEDED ot import into it nodes, it also NEEDED to export result asXML...
	$doc->appendChild($doc->importNode($country, true));
	$doc->preserveWhiteSpace = false;
	$doc->formatOutput = true;

		if (isset($this->filterOut['countries']) and preg_match($this->filterOut['countries'], $country->getAttribute('Country_Name'))) continue; //6
		/*
		* For understanding this magick, see description of class itself.
		*/
		if ( 'Россия' == $country->getAttribute('Country_Name') or 'Белоруссия' == $country->getAttribute('Country_Name')){
		$this->loadCities();
			foreach ($this->_xpath->query('//Regions[@Owner_Id="' . $country->getAttribute('Owner_Id') . '"]') as $region){
				if (isset($this->filterOut['regions']) and preg_match($this->filterOut['regions'], $region->getAttribute('RegionName'))) continue; //6
			//http://ru2.php.net/manual/ru/domdocument.importnode.php
			$reg = $doc->firstChild->appendChild($doc->importNode($region, true));
				foreach ($this->parseCitiesRussian($reg) as $city){
				$meta_city = null;
					if (isset($this->filterOut['cities']) and preg_match($this->filterOut['cities'], $city->getAttribute('CityName'))) continue; //6
					if ($city->getAttribute('CityName') == $reg->getAttribute('RegionName')){
					$meta_city = $city; //In buffer
					continue; //7.1
					}
				$reg->appendChild($doc->importNode($city, true));
				}
				if (0 == $reg->childNodes->length){// 7.2 No cities
					if ($meta_city){
					$meta_city = $reg->appendChild($doc->importNode($meta_city, true));
					$meta_city->setAttribute('CityName', self::$all_cities_name);
					}
					else{
					$meta_city = $reg->appendChild(new DOMElement('c'));
					$meta_city->setAttribute('CityName', self::$all_cities_name);
					$meta_city->setAttribute('Region_Owner_ID', $reg->getAttribute('Owner_Id'));
					$meta_city->setAttribute('Region_ID', $reg->getAttribute('Id'));
					}
				}
			}

			if('Белоруссия' == $country->getAttribute('Country_Name')){
			$xpath = new DOMXPath($doc);
			$xpath->query('//Regions[@RegionName="Белоруссия"]')->item(0)->setAttribute('RegionName', self::$no_region_name); // 4
			}
		}
		else{
		$this->loadCitiesc();
	//	$reg = $doc->firstChild->appendChild($doc->createElement('Region'));
		$reg = $doc->firstChild->appendChild(new DOMElement('Region'));
		$reg->setAttribute('Id', -1);
		$reg->setAttribute('Owner_Id', 0);
		$reg->setAttribute('RegionName', self::$no_region_name);
			foreach ($this->parseCitiesForeign($country) as $city){
				if (isset($this->filterOut['cities']) and preg_match($this->filterOut['cities'], $city->getAttribute('CityName'))) continue; //6
			$city = $reg->appendChild($doc->importNode($city, true));
			//Rename Attributes to follow single naming scheme
			$city->setAttribute('Id', $city->getAttribute('id'));
			$city->removeAttribute('id');

			$city->setAttribute('Owner_Id', $city->getAttribute('owner_id'));
			$city->removeAttribute('owner_id');
			}
		}

	$this->_mainXML->firstChild->appendChild($this->_mainXML->importNode($doc->firstChild, true));
	return $this;
	}#m compileCountry

	/**
	* Return Country from compiled list (if was not compiled yet - compiled by default) by its ID.
	*
	* @param	int	$id
	* @return	Object(DOMElement)
	**/
	public function getCountryById($id){
		if (!$this->_compiled) $this->compile();

	/*
	* We can't use $this->_mainXML->getElementById($id); because documeent have not DTD and nothing defined as ID attribute!
	**/
	$list = $this->getCountryByXPath('//Countries[@Id="' . $id . '"]');
	assert($list->length <= 1);
	return @$list->item(0);
	}#m getCountryById

	/**
	* Return Country from compiled list (if was not compiled yet - compiled by default) by its name.
	*
	* @param	string	$name
	* @return	Object(DOMElement)|null
	**/
	public function getCountryByName($name){
		if (!$this->_compiled) $this->compile();

	$list = $this->getCountryByXPath('//Countries[normalize-space(@Country_Name)="' . $name . '"]');
	assert($list->length <= 1);
	return @$list->item(0);
	}#m getCountryByName

	/**
	* Return Country from compiled list (if was not compiled yet - compiled by default) by Xpath.
	*
	* @param	string	$xpathquery
	* @return	Object(DOMNodeList)
	**/
	public function getCountryByXPath($xpathquery){
		if (!$this->_compiled) $this->compile();

	$xpath = new DOMXPath($this->_mainXML);
	return $xpath->query($xpathquery);
	}#m getCountryByXPath

	/**
	* Return all countries.
	*
	* @return	Object(DOMDocument)
	**/
	public function getCountries(){
	return $this->_mainXML;
	}#m getCountries

	/**
	* Foreign countries do not devided to regions, only Russia.
	* Return DOMNodeList of cities belong to region.
	*
	* @param	integer	$regionId
	* @return	Object(DOMNodeList)
	**/
	public function parseCitiesRussian(DOMElement &$region){
	$xpath = new DOMXPath($this->_citiesXML);
	return $xpath->query('//c[@Region_ID="' . $region->getAttribute('Id') . '" and @Region_Owner_ID="' . $region->getAttribute('Owner_Id') . '"]');
	}#m parseCitiesRussian

	/**
	* Foreign countries do not devided to regions, only Russia.
	* Return DOMNodeList of cities belong to country.
	*
	* @param	integer	$regionId
	* @return	Object(DOMNodeList)
	**/
	public function parseCitiesForeign(DOMElement &$country){
	$xpath = new DOMXPath($this->_citiescXML);
	return $xpath->query('//c[@countries_id="' . $country->getAttribute('Id') . '" and @countries_owner_id="' . $country->getAttribute('Owner_Id') . '"]');
	}#m parseCitiesForeign

	/**
	* Load XML file of Russian cities if it is not loaded yet (or second argument set to true)
	*
	* @param	boolean	$force If true - in any case load.
	* @return	&$this
	**/
	public function &loadCities($force = false){
		if (!$this->_citiesXML and !$force){
		$this->_citiesXML = new DOMDocument('1.0');
		$this->_citiesXML->load($this->_citiesfile);
		$this->_compiled = false;
		}
	return $this;
	}#m loadCities

	/**
	* Load XML file of Foreign cities if it is not loaded yet (or second argument set to true)
	*
	* @param	boolean	$force If true - in any case load.
	* @return	&$this
	**/
	public function &loadCitiesc($force = false){
		if (!$this->_citiescXML and !$force){
		$this->_citiescXML = new DOMDocument('1.0');
		$this->_citiescXML->load($this->_citiescfile);
		$this->_compiled = false;
		}
	return $this;
	}#m loadCitiesc

	/**
	* Helper (temp?) function to output Object(DOMElement) as XML
	*
	* @param	Object(DOMNode)	$elem
	* @param	string	$header
	* @return	nothing
	**/
	public static function dumpDOMnode(DOMNode $elem, $header = 'DOMElement'){
	$o = new DOMnodeOutExtraData($elem);
	dump::a(trim($o->strToPrint()), $header);
	}#m dumpDOMnode
}#c SPSR_in_parse
?><?
/**
* a1agregator.ru SMS-API ( https://partner.a1agregator.ru/files/fileView/17 )
*
* @package a1agregator.ru SMS-API
* @version 1.0.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-05-17 14:16
*
* @uses BaseException
8 @uses settins_check
**/
class a1agregatorException extends BaseException{}
class a1agregatorMSGParseException extends a1agregatorException{}
class a1agregatorMSGSkeyMismatchException extends a1agregatorMSGParseException{}
class a1agregatorMSGSignatureException extends a1agregatorMSGParseException{}

/**
*
**/
class a1agregator_MSG_answer extends settings_check{
	public $properties = array(
		'smsid', 'status', 'answer'
		//auxiliary data
		,'ANSWER_FORMAT'
	);

	//defaults
	protected $__SETS = array(
		'status'	=> 'reply',

		'ANSWER_FORMAT'	=> array(
			array('smsid', 'smsid:', "\n")
			,array('status', 'status:', "\n\n")
			,array('answer', '', "\n")
		),
	);

	public function __construct(){
	}#__c

	/**
	* Reimplement without in parameter.
	*
	* @return	string
	**/
	public function getString(){
		return parent::getString($this->ANSWER_FORMAT);
	}#m getString

	/**
	* Conversion to string uses {@see getString()} method
	*
	* @return	string
	**/
	function __toString(){
		return $this->getString();
	}#m __toString
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
	*
	* // Additional My:
	* @var $PlainSkey - Right Skey to check
	* @var $operator	– Absent in documentation, but present in tests
	* @var $country_id	– Absent in documentation, but present in tests
	**/
	public $properties = array(
		'date', 'msg', 'msg_trans', 'operator_id', 'operator', 'user_id', 'smsid', 'cost_rur', 'cost', 'test', 'num', 'retry', 'try', 'ran', 'skey', 'sign', 'country_id'
		//auxiliary data
		,'SIGNATURE_FORMAT'
		,'PlainSkey'
	);

	protected $__SETS = array(
		'SIGNATURE_FORMAT'	=> array(
			'date', 'msg_trans', 'operator_id', 'user_id', 'smsid', 'cost_rur', 'ran', 'test', 'num', 'country_id', 'PlainSkey'
		),
	);

	/**
	* @var	Object(a1agregator_MSG_answer).
	**/
	public $Answer = null;

	// This settings do not clear on call clear() and do not rewrited by setSettingsArray()
	protected $static_settings = array('SIGNATURE_FORMAT');

	/**
	* @param	array	$array Settings.
	* @param	string	$skey Current (plain!) Skey to check
	**/
	public function __construct(array $array, $skey){
		$this->set($array, $skey);
	}#__c

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
		// http://amurspb.ru/sms.a1agregator/smsgate.php?user_id=71111111111&num=1121&cost=4.61134054658&cost_rur=170.18199&msg=&skey=1094c779dce7ed5f70410cc81f9b10fc&operator_id=299&date=2009-05-17+19%3A32%3A51&smsid=1288931845&msg_trans=&operator=operator&test=1&ran=5&try=1&country_id=45909&sign=f077a353485a66ce3089661bedc99f62
		parse_str($rawString, $msg);
		$this->setFromArray($msg);
		return $this;
	}#m setFromQueryString

	/**
	* Parse and initialize message from array. Common use, initialise from $_GET, $_POST or $_REQUEST global arrays.
	*
	* All checks performed.
	* @param	array	$arr
	* @param	string	$skey Current (plain!) Skey to check
	* @return &$this
	* @Throw(a1agregatorMSGParseException, a1agregatorMSGSignatureException, a1agregatorMSGSkeyMismatchException)
	**/
	function &set(array $arr, $skey){
		$this->setSettingsArray($arr);
		$this->setSetting('PlainSkey', $skey);
		// do NOT use empty() because it works ONLY with variables!!!
		if (!isset($this->msg) or !isset($this->operator_id) or !isset($this->user_id) or !isset($this->smsid) or !isset($this->cost_rur) or !isset($this->test) or !isset($this->try) or !isset($this->sign)){
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
			throw new a1agregatorMSGParseException('Error parsing Incoming message. '.$what);
		}

		if ($this->skey and md5($this->PlainSkey) != $this->skey){
			throw new a1agregatorMSGSkeyMismatchException('Skey mismatch');
		}
		if ($this->sign and $this->sign != $this->calcSignature()){
			dump::a($this->sign);
			dump::a($this->calcSignature());
			dump::a($this->getString($this->SIGNATURE_FORMAT));
			throw new a1agregatorMSGSignatureException('Signature incorrect!');
		}
		$this->Answer = new a1agregator_MSG_answer();
		return $this;
	}#m set

	/**
	* Reimplement. NON-string detect fields needed. Use isset instead!
	*
	* @inheritdoc
	**/
	public function formatField($field){
		if (is_array($field)){
			if (!isset($field[0])) $field = array_values($field);
			return (isset($this->{$field[0]}) ? @$field[1] . $this->{$field[0]} . @$field[2] : @$field[3]);
		}
		else{
			return (isset($this->{$field}) ? $this->{$field} : $field); //Или по имени настройку, если это просто текст;
		}
	}#m formatField
};#c a1agregator_MSG
?><?
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
?><?
/**
* Orange SMS-API ( http://api.o-range.ru/DOCS/ )
*
* @package Orange SMS-API
* @version 1.0.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-05-17 14:16 ver 1.0.1 to 1.0.2
**/

class SMS_partner_MSG extends settings{
	protected $__SETS = array(
		'CMD_SEND_FORMAT'	=> array(
		'&cmd=send',
			array('answerto', '&answerto='),
			array('to', '&to='),
			array('base64_text', '&text=')
			// @TODO: add UCS2 support option
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

	// This settings will not be cleared on call clear() and do not rewrited by setSettingsArray()
	protected $static_settings = array('CMD_SEND_FORMAT', 'LOG_FORMAT');

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
	* Reimplementation to protect uncleared items
	*
	* @param array	$setArr
	**/
	public function setSettingsArray(array $setArr){
		parent::setSettingsArray(array_merge($setArr, array_intersect_key($this->__SETS, array_flip($this->static_settings))));
	}#m setSettingsArray
};#c SMS_partner_MSG
?><?
/**
* Orange SMS-API ( http://api.o-range.ru/DOCS/ )
*
* @package Orange SMS-API
* @version 1.1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-06 15:29 ver 1.0 to 1.0.1
**/

/**
* Setting related to partner - login, password and others...
**/
class SMS_partner_settings extends settings {
	/*
	//Defaults
	protected $__SETS = array(
		'URL'		=> 'http://api.o-range.ru/?',
		'authTime'	=> '20', // minutes

		'in_pass'		=> 'SuperPuperPass',

		'login'		=> 'conf',
		'password'	=> '1234',

		'net_transport'=> 'file_inmem', // classname
		'user_answer'	=> 'Spasibo, Vasha anketa podniata. Ne zabud\'te obnovit\' stranicu chtobi ubeditsia v etom'
	);
	*/
}

class SMS_partnerAPI extends get_settings{
	protected $_log;

	protected $_transport = null; // Make on the fly

	private $authID;

	private /* SMS_partner_MSG */ $MSG;

	public function __construct(SMS_partner_settings &$sets, HuLOG &$log){
		@session_start();
		$this->_sets =& $sets;
		$this->MSG = new SMS_partner_MSG();
		$this->_log =& $log;
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
			$this->transport->setPath($this->settings->URL . 'cmd=auth&login=' . $this->settings->login . '&pass=' . $this->settings->password);
			$this->transport->loadContent();
			$_SESSION['authID'] = $this->authID = $this->parseServerAnswer('authID');
			if (!$this->authID) throw new MSG_AuthErrorException("Auth error!\nServer response: ".$this->transport->getBLOB()."\nOn query:".$this->settings->URL.'cmd=auth&login='.$this->settings->login.'&pass='.$this->settings->password);
		}
		catch(FilesystemException $fse){// ERROR. @TODO process this error.
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

	public function parseInMSG($rawString){
		// http://ourSMSgate.tld/gate.cgi?pass=d41d8cd98f00b204e9800998ecf8427e&ShortNbr=2300&msgInID=12452&UserPhone=79111234567&text=8uXx8u7i7uUg8e7u4fnl7ejl
		parse_str($rawString, $msg);
		$this->MSG->setSettingsArray($msg);
		// do NOT use empty() because it works ONLY with variables!!!
		if (!@$this->MSG->msgInID or !@$this->MSG->ShortNbr or !@$this->MSG->UserPhone or !@$this->MSG->pass or !@$this->MSG->text){
			$what = 'Empty field(s):';
			if (!@$this->MSG->msgInID)	$what .= ' [msgInID]';
			if (!@$this->MSG->ShortNbr)	$what .= ' [ShortNbr]';
			if (!@$this->MSG->UserPhone)	$what .= ' [UserPhone]';
			if (!@$this->MSG->pass)		$what .= ' [pass]';
			throw new MSG_InParseErrorException('Error parsing Incoming message. '.$what."\nQUERY_STRING:".EMPTY_STR($rawString, @$_SERVER['QUERY_STRING']));
		}

		$this->in_auth();

		$this->MSG->setSetting('base64_text', $this->MSG->text);
		$this->MSG->setSetting('text', base64_decode(str_replace(' ', '+', @$this->MSG->base64_text)));
		// prepare for answer. Inverse (copy) few fields by default.
		$this->MSG->setSetting('answerto', $this->MSG->msgInID);
		$this->MSG->setSetting('to', $this->MSG->UserPhone);

		$this->_log->toLog('MSG in', 'ACS', 'msg', $this->MSG->getString($this->MSG->LOG_FORMAT));
		echo 'OK'; // Answer to Server
	}#m parseInMSG

	public function setMSG(array $msg){
		$this->MSG->mergeSettingsArray($msg);
	}#m setMSG

	public function getMessage(){
		return $this->MSG;
	}#m getMessage

	public function sendMSG(){
		// http://api.o-range.ru/?authID=fc6XJgmaNpF8ZRMQ0lVXU1&cmd=send&answerto=26260&to=79052084523&text=8uXx8iDw8/Hx6u7j7iD/5/vq4CDiIHNtc19hcGk=
		try{
			$this->MSG->setSetting('base64_text', base64_encode($this->MSG->text));
			$this->transport->setPath($this->settings->URL.'authID='.$this->getAuthID().$this->MSG->getString($this->MSG->CMD_SEND_FORMAT));
			$this->transport->loadContent();
			$this->MSG->setSetting('msgOutID', $this->_result = $this->parseServerAnswer('msgOutID'));
			if (!intval($this->MSG->msgOutID)) throw new MSG_SendFailException("Message sent error!\nServer response: ".$this->transport->getBLOB()."\nOn query:".$this->settings->URL.'authID='.$this->getAuthID().$this->MSG->getString($this->MSG->CMD_SEND_FORMAT));
			$this->_log->toLog('MSG out', 'ACS', 'msg', $this->MSG->getString($this->MSG->LOG_FORMAT));
		}
		catch (FilesystemException $fse){
			// @TODO Error-handling
			$this->_log->toLog($fse->__toString(), 'ERR', 'net');
			throw $fse;
		}
		catch (MSG_SendFailException $msf){
			$this->_log->toLog($msf->__toString(), 'ERR', 'net');
			throw $msf;
		}
	}#m sendMSG

	protected function parseServerAnswer($what){
		// <OK/><authID>cpahqq4enpjbag0266hqvddr42</authID>
		$reg = new RegExp_pcre ('@<OK/><'.RegExp_pcre::quote($what).'>(.*)</' . RegExp_pcre::quote($what) . '>@', $this->transport->getBLOB());
		return ($this->_result = $reg->match(1));
	}#m parseServerAnswer

	/**
	* Authentificate incoming message.
	*
	* @return	boolean
	**/
	function in_auth() {
		return ($this->MSG->pass == $this->settings->in_pass);
	}#m in_auth
}#c SMS_partnerAPI
?><?
/* Взято откуда-то из комментариев php.net */
function uniord($c)
{
$ud = 0;
	if (ord($c{0})>=0 && ord($c{0})<=127) $ud = ord($c{0});
	if (ord($c{0})>=192 && ord($c{0})<=223) $ud = (ord($c{0})-192)*64 + (ord($c{1})-128);
	if (ord($c{0})>=224 && ord($c{0})<=239) $ud = (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
	if (ord($c{0})>=240 && ord($c{0})<=247) $ud = (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
	if (ord($c{0})>=248 && ord($c{0})<=251) $ud = (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
	if (ord($c{0})>=252 && ord($c{0})<=253) $ud = (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
	//error
	if (ord($c{0})>=254 && ord($c{0})<=255)	$ud = false;
return $ud;
}

/* Взято из комментов http://ru.php.net/chr */
function unichr($dec) {
	if ($dec < 128) {
	$utf = chr($dec);
	}
	elseif ($dec < 2048){
	$utf = chr(192 + (($dec - ($dec % 64)) / 64));
	$utf .= chr(128 + ($dec % 64));
	}
	else{
	$utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
	$utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
	$utf .= chr(128 + ($dec % 64));
	}
return $utf;
}
?><?
/**
* In PHP we unfortunately do not have multiple inheritance :(
* So, turn it class into interface and provide common, possible implementation
* through static methods of __outExtraData__common_implementation homonymous methods
* and providing link to $this and in method implementation refer to it as &obj instead of direct $this.
*
* Common implementation will be present in comments near after declaration.
**/

interface outExtraData{
	//public $_curTypeOut = OS::OUT_TYPE_BROWSER; //Track to helpers, who provide format (parts) and need known for what

	/**
	* String to print into file. Primary for logs string representation
	*
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	**/
	public function strToFile($format = null);

	/**
	* Return string to print into user browser.
	*
	* @param * @param mixed(null)	$format Any useful helper information to format
	* @return string
	**/
	public function strToWeb($format = null);

	/**
	* String to print on console.
	*
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	**/
	public function strToConsole($format = null);

	/**
	* String to print. Automaticaly detect (by {@link OS::getOutType()}) Web or Console and
	*	invoke appropriate ::strToWeb() or ::strToConsole()
	*
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strToPrint($format = null);/*{Now common solution is (see description on begin abput Multiple Inheritance):
	return __outExtraData__common_implementation::strToPrint($this, $format);
	}#m strToPrint
	*/

	/**
	* Convert to string by provided type.
	*
	* @param integer $type	One of OS::OUT_TYPE_* constant. {@link OS::OUT_TYPE_BROWSER}
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	* @Throw(VariableRangeException)
	**/
	public function strByOutType($type, $format = null);/*{Now common solution is (see description on begin abput Multiple Inheritance):
	return __outExtraData__common_implementation::strByOutType($this, $type, $format);
	*/
}#c

/* see description on begin about Multiple Inheritance **/
class __outExtraData__common_implementation{
	//Only hack - common realization!
	public static function strByOutType(/*$this*/&$obj, $type, $format = null){
		$obj->_curTypeOut = $type;

		switch ($type){
			case OS::OUT_TYPE_BROWSER:
			return $obj->strToWeb($format);
				break;

			case OS::OUT_TYPE_CONSOLE:
				return $obj->strToConsole($format);
				break;

			case OS::OUT_TYPE_FILE:
				return $obj->strToFile($format);
				break;

			// Addition, pseudo
			case OS::OUT_TYPE_PRINT:
				return $obj->strToPrint($format);
				break;

			default:
				throw new VariableRangeException('$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!');
		}
	}#m strByOutType

	public function strToPrint(/*$this*/&$obj, $format = null){
		$obj->_curTypeOut = OS::OUT_TYPE_PRINT;//Pseudo. Will be clarified.
		if (OS::OUT_TYPE_BROWSER == OS::getOutType()) return $obj->strToWeb($format);
		else return $obj->strToConsole($format);
	}#m strToPrint
}#c
?><?
/**
* Debug and backtrace toolkit.
*
* Class to provide easy wrapper aroun HuFormat for anywhere usage.
*
* @package Debug
* @subpackage HuLOG
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-16 19:06
*
* @uses HuFormat
* @uses commonOutExtraData
**/

/**
* Class to provide easy wrapper aroun HuFormat for anywhere usage.
**/
class huFormatOutExtraData extends commonOutExtraData{
	protected $format;	//Array of format
	protected /* HuFormat */ $_format;

	/**
	* Constructor.
	*
	* @param	mixed	$var Var to output with provided format.
	* @param	array	$format	Format how output $vavr. Must contain 3 elements:
	*	'FORMAT_CONSOLE', 'FORMAT_WEB', 'FORMAT_FILE' each represent according
	*	format (See class {@see HuFormat} for more details).
	**/
	function  __construct($var, array $format){
		$this->format = $format;
		$this->_format = new HuFormat(null, $var);
	}#__c

	/**
	*@inheritdoc
	**/
	public function strToConsole($format = null){
		return $this->_format->setFormat(EMPTY_VAR($format, $this->format['FORMAT_CONSOLE']))->getString();
	}#m strToConsole

	/**
	*@inheritdoc
	**/
	public function strToFile($format = null){
		return $this->_format->setFormat(EMPTY_VAR($format, $this->format['FORMAT_FILE']))->getString();
	}#m strToFile

	/**
	*@inheritdoc
	**/
	public function strToWeb($format = null){
		return $this->_format->setFormat(EMPTY_VAR($format, $this->format['FORMAT_WEB']))->getString();
	}#m strToWeb
}#c huFormatOutExtraData
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuLOG
* @version 2.0.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ???
*
* @uses dump
* @uses outExtraData.interface
**/

/**
* Common realisation suitable for the most types. Primarly intended to logs, like:
* Single::def('HuLog')->toLog('Exception occured: ' . $e->getMessage(), 'ERR', 'Some', new commonOutExtraData($SomeCurrentSctructuredData));
* Output based on dump::* functions
**/
class commonOutExtraData implements outExtraData{
protected $_var = null;
	public function __construct($var){
	$this->_var =& $var;
	}

	public function strToConsole($format = null){
	return dump::c($this->_var, null, true);
	}#m strToConsole

	public function strToFile($format = null){
	return dump::log($this->_var, false, true);
	}#m strToFile

	public function strToWeb($format = null){
	return dump::w($this->_var, false, true);
	}#m strToWeb

	public function strToPrint($format = null){
	return __outExtraData__common_implementation::strToPrint($this, $format);
	}#m strToPrint

	public function strByOutType($type, $format = null){
	return __outExtraData__common_implementation::strByOutType($this, $type, $format);
	}#m strByOutType
}#c commonOutExtraData
?><?
/**
* VariableStream stream wrapper. Manipulate stream 'var://varName' as file,
* where content is $varName.
*
* @package Vars
* @version 1.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-06-16 14:33
**/

/**
* Very usefull example from http://www.php.net/manual/ru/function.stream-wrapper-register.php as base for implementation
**/
class VariableStream {
	var $position;
	var $varname;
  
	function stream_open($path, $mode, $options, &$opened_path){
		$url = parse_url($path);
		$this->varname = $url["host"];
		$this->position = 0;
	   
		return true;
	}

	function stream_read($count){
		$ret = substr($GLOBALS[$this->varname], $this->position, $count);
		$this->position += strlen($ret);
		return $ret;
	}

	function stream_write($data){
		$left = substr($GLOBALS[$this->varname], 0, $this->position);
		$right = substr($GLOBALS[$this->varname], $this->position + strlen($data));
		$GLOBALS[$this->varname] = $left . $data . $right;
		$this->position += strlen($data);
		return strlen($data);
	}

	function stream_tell(){
		return $this->position;
	}

	function stream_eof(){
		return $this->position >= strlen($GLOBALS[$this->varname]);
	}

	/**
	* This is STUB, only for warnings supress
	**/
	function stream_stat(){
		return array();
	}

	function stream_seek($offset, $whence){
		switch ($whence) {
			case SEEK_SET:
				if ($offset < strlen($GLOBALS[$this->varname]) && $offset >= 0) {
					 $this->position = $offset;
					 return true;
				} else {
					 return false;
				}
				break;
			   
			case SEEK_CUR:
				if ($offset >= 0) {
					 $this->position += $offset;
					 return true;
				} else {
					 return false;
				}
				break;
			   
			case SEEK_END:
				if (strlen($GLOBALS[$this->varname]) + $offset >= 0) {
					 $this->position = strlen($GLOBALS[$this->varname]) + $offset;
					 return true;
				} else {
					 return false;
				}
				break;
			   
			default:
				return false;
		}
	}
}

stream_wrapper_register("var", "VariableStream")
	or die("Failed to register protocol");

/*
@example

$myvar = "";
   
$fp = fopen("var://myvar", "r+");

fwrite($fp, "line1\n");
fwrite($fp, "line2\n");
fwrite($fp, "line3\n");

rewind($fp);
while (!feof($fp)) {
	echo fgets($fp);
}
fclose($fp);
var_dump($myvar);
*/
?><?
/**
* Class for transliteration Russian data.
*
* @package Vars
* @subpackage translit
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-06 16:02 ver 1.0 to 1.1
**/

class trans{
	function translit($rus_str){
	$rusTable = array(
	'/А/','/Б/','/В/','/Г/','/Д/','/Е/','/Ё/','/Ж/','/З/','/И/','/Й/','/К/','/Л/','/М/','/Н/','/О/','/П/','/Р/','/С/','/Т/','/У/','/Ф/','/Х/','/Ц/','/Ч/','/Ш/','/Щ/','/Ь/','/Ы/','/Ъ/','/Э/','/Ю/','/Я/',
	'/а/','/б/','/в/','/г/','/д/','/е/','/ё/','/ж/','/з/','/и/','/й/','/к/','/л/','/м/','/н/','/о/','/п/','/р/','/с/','/т/','/у/','/ф/','/х/','/ц/','/ч/','/ш/','/щ/','/ь/','/ы/','/ъ/','/э/','/ю/','/я/'
	);
	$engTable = array(
	'A','B','V','G','D','E','Jo','Zh','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','H','C','Ch','Sh','Sch',"",'Y','','Je','Ju','Ja',
	'a','b','v','g','d','e','jo','zh','z','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch',"",'y','','je','ju','ja'
	);
	return preg_replace($rusTable, $engTable, $rus_str);
	}#m translit
}#c trans
?><?
/**
* Charset encoding suite
* Iconv implementation
*
* @package Vars
* @subpackage charset_convert
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-06 16:08 ver 1.0 to 1.1
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses charset_convert
* @uses charset_convert_exception
**/


class iconv_charset_convert extends charset_convert{
	/**
	* Constructor. All as parent
	**/
	public function __construct($text, $inEnc = null, $outEnc = 'UTF-8'){
		parent::__construct($text, $inEnc, $outEnc);
	}#__c

	/**
	* @inheritdoc
	* @Throws(charset_convert_exception, VariableRequiredException)
	**/
	public function convert(){
		REQUIRED_VAR($this->_in, 'InEncoding');
		REQUIRED_VAR($this->_out, 'OutEncoding');
		/*
		* iconnv do not provide any chance to handle errors. Even if provided charset is not correct, it only produce PHP Notice and return ampty string.
		* So, as last chance - catch this warning and convert it into exceprion!
		*/
		// BackUP settings
		$oldErrorHandler = set_error_handler( array($this, 'error_handler') );
		$oldErrorReporting = error_reporting(E_ALL);
		$this->_resText = iconv($this->_in, $this->_out, $this->_text);

		// Restore settings
		error_reporting($oldErrorReporting);
		if ($oldErrorHandler) set_error_handler( $oldErrorHandler );
		elseif (is_null($oldErrorHandler)) restore_error_handler();

		//Processing
		if ($this->_charset_convert_Errors){
			$ttt = $this->_charset_convert_Errors; //Local buffer
			$this->_charset_convert_Errors = null; //Clear BEFORE throw, because if it will be catched correctly - it is not be cleared as well!
			throw new charset_convert_exception( implode(';', $ttt) );
		}
	}#m convert

	/**
	* Static equivalent of {@see ::convert()} for satac, fast invoke
	*
	* @return string of result
	**/
	static public function conv($text, $inEnc = null, $outEnc = 'UTF-8'){
	// This is correct only if Late Static Binding present. So, it starts from PHP 5.3.0
	// If we want make this code work on earler releases - just copy this function compleatly in derivates.
		$conv = new self($text, $inEnc, $outEnc);
		return $conv->getResult();
	}#m conv

	protected $_charset_convert_Errors = '';

	function error_handler($errno, $errstr, $errfile, $errline /*, $errcontext */ ){
		if (stristr($errstr, 'iconv')){// This hack only fo MSSQL errors
			$this->_charset_convert_Errors[] = $errstr;
			//Don't execute PHP internal error handler
			return true;
		}
		else return false;// Default error-handler
	}#m error_handler
}
?><?
/**
* Charset encoding suite
*
* @package Vars
* @subpackage charset_convert
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-06 16:08 ver 1.1 to 1.0
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
**/



abstract class charset_convert{
	protected $_in = null;
	protected $_out = null;
	protected $_text = null;
	protected $_resText = null;

	/**
	* Constructor.
	*
	* @param string $text
	* @param string $inEnc
	* @param string $outEnc='UTF-8'
	* @Throws(VariableRequiredException)
	**/
	public function __construct($text, $inEnc = null, $outEnc = 'UTF-8'){
		$this->setInEnc($inEnc);
		$this->setOutEnc($outEnc);
		$this->setText(REQUIRED_VAR($text, 'TextToConvert'));

		if ($inEnc and $outEnc) $this->convert();
	}#__c

	/**
	* Main working horse. Must be reimplemented each time we should provide new layer of xonversion (mb, iconv, recode etc)
	*
	* @return &$this
	**/
	abstract public function convert(); //{}#m convert

	/**
	* Static equivalent of {@see ::convert()} for satac, fast invoke
	*
	* @return string of result
	**/
	static public function conv($text, $inEnc = null, $outEnc = 'UTF-8'){
		// This is correct only if Late Static Binding present. So, it starts from PHP 5.3.0
		// If we want make this code work on earler releases - just copy this function compleatly in derivates.
		$conv = new self($text, $inEnc, $outEnc);
		return $conv->getResult();
	}#m conv

	/**
	* Set new In encoding
	*
	* @param string $enc New encoding
	* @return &$this
	**/
	public function &setInEnc($enc){
		$this->_in = $enc;
		$this->_resText = null;
		return $this;
	}#m setInEnc

	/**
	* Get current In encoding
	*
	* @return string
	**/
	public function getInEnc(){
		return $this->_in;
	}#m getInEnc

	/**
	* Set new Out encoding
	*
	* @param string $enc New encoding
	* @return &$this
	**/
	public function &setOutEnc($enc){
		$this->_out = $enc;
		$this->_resText = null;
		return $this;
	}#m setOutEnc

	/**
	* Get current Out encoding
	*
	* @return string
	**/
	public function getOutEnc(){
		return $this->_out;
	}#m getOutEnc

	/**
	* Set text to convert encoding.
	*
	* @package string $newText
	* @return &$this
	**/
	public function &setText($newText){
		$this->_text = $newText;
		return $this;
	}#m setText

	/**
	* Get current text
	*
	* @return string
	**/
	public function getText(){
		return $this->_text;
	}#m getText

	/**
	* Return result of convertation. If it is empty, run {@see ::convert()}
	*
	* @return string
	**/
	public function getResult(){
		if (empty($this->_resText)) $this->convert();
		return $this->_resText;
	}#m getResult

	/**
	* Auto convertion into string; {@see ->getResult()}
	*
	* @return
	**/
	public function __toString(){
		return $this->getResult();
	}#m __toString
} #c charset_convert
?><?
/**
* Singleton pattern.
*
* @package Vars
* @subpackage Classes
* @version 1.2.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-30 13:22
*
* @uses OS
* @uses HuConfig
* @uses ClassNotExistsException
**/
 //Must be implisit, to break dependency circle. Free &CONF() function used.

/**
* Example from http://ru2.php.net/manual/ru/language.oop5.patterns.php, modified
**/
class Single{
	/**
	* Hold an instance of the class
	**/
	private static $instance = array();

	/**
	* A private constructor; prevents direct creation of object
	**/
	protected final function __construct(){
		echo 'I am constructed. But can\'t be :) ';
	}//__c

	/**
	* The main singleton static method
	* All call must be: Single::singleton('ClassName'). Or by its short alias: Single::def('ClassName')
	*
	* @param	string	$className Class name to provide Singleton instance for it.
	* @params	variable number of parameters. Any other parameters directly passed to instantiated class-constructor.
	**/
	public static function &singleton($className){
		$args = func_get_args();
		unset($args[0]);//Class name

		$hash = $className . '_' . self::hash($args);
		if (!isset(self::$instance[$hash])){
			if (!function_exists('__autoload') and (!function_exists('spl_autoload_functions') or !spl_autoload_functions())) self::tryIncludeByClassName($className);

			/*
			Using Reflection to instanciate class with any args.
			See http://ru2.php.net/manual/ru/function.call-user-func-array.php, comment of richard_harrison at rjharrison dot org
			*/
			$reflectionObj = new ReflectionClass($className);
			// use Reflection to create a new instance, using the $args
			self::$instance[$hash] = $reflectionObj->newInstanceArgs($args);
		}

		return self::$instance[$hash];
	}#m singleton

	/**
	* The default configured. Short alias for {@see ::singleton()}
	*
	* @return &Object($classname)
	**/
	public static function &def($className){
		return self::singleton($className, CONF()->getRaw($className, true));
	}#m def

	/**
	* Try include
	* @deprecated Use autoload instead.
	*
	*
	* @param string	$className Name of needed class
	* @return
	**/
	public static function tryIncludeByClassName($className){
		file_put_contents('php://stderr', 'Usage of Single::tryIncludeByClassName is deprecated. Use autoload instead.');
		// is_readable is not use include_path, so can not use this check. More explanation see {$link OS::is_includeable()}
		if (!class_exists($className) and isset($GLOBALS['__CONFIG'][$className]['class_file']) and OS::is_includeable($GLOBALS['__CONFIG'][$className]['class_file']))
		include($GLOBALS['__CONFIG'][$className]['class_file']);

		// Check again
		if (!class_exists($className)) throw new ClassNotExistsException($className . ' NOT exist!'. (!@$GLOBALS['__CONFIG'][$className]['class_file'] ? '' : ' And, additionaly include provided path ['.$GLOBALS['__CONFIG'][$className]['class_file'].'] not helped in this!'));
	}#m tryIncludeByClassName

	/**
	* Prevent users to clone the instance
	**/
	public function __clone(){
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}#m __clone

	/**
	 * Provide simple way of hashing objects and array
	 *
	 * @param	mixed $param
	 * @return	string
	 */
	public static function hash($param){
		return md5(http_build_query($param));
	}#m hash
}#c Single

/**
* @example
* This will always retrieve a single instance of the class
*
* $test = Single::singleton();
* $test->bark();
* $test = Single::singleton()->bark();
* //Default invoke, using $GLOBALS['__CONFIG']['classname'] as arguments.
* Single::def('classname')->...
**/
?><?
/**
* Extends settings_check to allow apply get/set filters.
* It may be constraints check (f.e. check and throw exception on error),
*	and/or any modifications like clear user input, convert formats and etc.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.2
* @created 2009-06-29
* @created ?2009-09-29 13:55 ver 1.0 to 1.1
**/

/**
* Entity of "filter". In most cases only calling $callback on provided pair references $name/$value in method apply().
* But it is quite powerful. Childs of this basic class may provide any service such as: non-deterministic
*	state-based results, based on time or amount of call results (f.e. Apply filter only 3 times!?), depend
*	on any global environment etc...
**/
class settings_filter_base{
public $propName;
private $callback_;

	public function __construct($propName, $callback){
	$this->propName = $propName;
	$this->callback_ = $callback;
	}#__c

	/**
	* In simplest variant - just direct apply provided callback.
	* $name and $value provided as refrence, so, user can change both as want.
	* It is usefull to jungle and add additional filters by name. F.e. set GET filter like "UC:name"
	* to return uppercase value of "name", o rename option on set time etc.
	*
	* @param	&mixed	$name	Reference to name of option.
	* @param	&mixed	$value	Reference to new value of option
	* @return	mixed	Returns what user callback return.
	**/
	public function apply(&$name, &$value){
	/*
	* call_user_func_array to pass reference, what is not allowed in call_user_func.
	* Solution found in man, see Example1 http://ru2.php.net/call_user_func
	**/
	return call_user_func_array( $this->callback_, array(&$name, &$value) );
	}#m apply
}#c settings_filter_base

?><?
/**
* Extends settings_check to allow apply get/set filters.
* It may be constraints check (f.e. check and throw exception on error),
*	and/or any modifications like clear user input, convert formats and etc.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-29
**/


/**
* Extended variant of settings_check, with check possible options.
* You may easy add any amount of "filters" on get/set property operations
* 	by easy register new filter like:
*	$obj->addGetFilter('testProp', callback $func);
*
* Set filters intended prmarly for static transformations like check, conversions etc.
* 	In this case value transformed on set stage once, and stored result as
*	value only (original value droped).
*
* Get filters primarly intended for dinamic values, non-deterministic behaviour.
*	F.e. to add current datetime to field, check outside params etc...
*
* @uses settings_filter_base
*
* @example settings_filter.example.php
**/
class settings_filter extends settings_check_static{
	protected $__filt_set = array();
	protected $__filt_get = array();

	/**
	* Apply all desired filters and set value.
	**/
	public function setSetting($name, $value){
		foreach ($this->getFilterSet($name) as $filt){
			$filt->apply($name, $value);
		}
		if (!is_null($name)) parent::setSetting($name, $value);
	}#m setSetting

	/**
	* Apply all desired filters and return value.
	* Result not cached!
	* @inheritdoc
	**/
	public function &getProperty($name){
		$val =& parent::getProperty($name);
		foreach ($this->getFilterGet($name) as $filt){
			$filt->apply($name, $val);
		}
		return $val;
	}#m getProperty

	/**
	* Reimplemnt in more generic form for automatic handle all get/set transformations.
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr){
		$this->__SETS = array();
		// For our realisation just foreach all, now we can simple invoke mergeSettingsArray()
		$this->mergeSettingsArray($setArr);
	}#m setSettingsArray

	/**
	* Reimplemnt in more generic form for automatic handle all get/set transformations.
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
		/*
		* This may be done also through array_walk, but in it required intermediate function to swap arguments.
		* I think direct cycle will be faster.
		**/
		foreach (REQUIRED_VAR($setArr) as $key => $value)
			$this->setSetting($key, $value);
	}#m mergeSettingsArray

	/**
	* Add filter into property Get filters queue.
	*
	* @param	Object(settings_filter_base)	$filt. Filter to add.
	* @return	integer.	FilterId to allow delete it later.
	**/
	public function addFilterGet(settings_filter_base $filt){
		$q = $this->getFilterGet($filt->propName);
		$q->push($filt);
		return ($q->count() - 1);
	}#m addFilterGet

	/**
	* Add filter into property Set filters queue.
	*
	* @param	Object(settings_filter_base)	$filt. Filter to add.
	* @return	integer.	FilterId to allow delete it later.
	**/
	public function addFilterSet(settings_filter_base $filt){
		$q = $this->getFilterSet($filt->propName);
		$q->push($filt);
		return ($q->count() - 1);
	}#m addFilterSet

	/**
	* Base variant of search feilter. Compare just by full name of property.
	* Extend class and reimplement getFilterGet()/getFilterSet() methods may be good idea to provide select,
	*	say by part of name, by start, pattern or even by regular expression!
	*
	* @param	string	$name Name ofproperty for what filter search.
	* @return	&Object(SplDoublyLinkedList) Queue of required filters (may be empty).
	**/
	protected function &getFilterGet($name){
		if (!isset($this->__filt_get[$name])) $this->__filt_get[$name] = new SplDoublyLinkedList();
		return $this->__filt_get[$name];
	}#m getFilterGet

	/**
	* Base variant of search feilter. Compare just by full name of property.
	* Extend class and reimplement getFilterGet()/getFilterSet() methods may be good idea to provide select,
	*	say by part of name, by start, pattern or even by regular expression!
	*
	* @param	string	$name Name of property for what filter search.
	* @return	&Object(SplDoublyLinkedList) Queue of required filters (may be empty).
	**/
	protected function &getFilterSet($name){
		if (!isset($this->__filt_set[$name])) $this->__filt_set[$name] = new SplDoublyLinkedList();
		return $this->__filt_set[$name];
	}#m getFilterSet

	/** @TODO. Implement RAW-functionality in child class
	* If for property registered at least one filter vith private flag, all property turn to private, and
	*	requesting its raw value caused exception
	public function getRaw($name){
		if(!)
	}#m getRaw
	**/

	/**
	* Delete Get filter property from filters queue.
	*
	* @param	string	$name Name of property for what filter search.
	* @param	integer	$filterId Filter Id from methods {@see addFilter[GS]et()}
	* @return	&$this
	**/
	public function &delFilterGet($propName, $filterId){
		$this->getFilterGet($propName)->offsetUnset($filterId);;
		return $this;
	}#m delFilterGet

	/**
	* Delete Set filter property from filters queue.
	*
	* Warning: nothing original values saved, so, delete filter from queue
	*	will affect only nes set operations. All properties now leave as is.
	*
	* @param	string	$name Name of property for what filter search.
	* @param	integer	$filterId Filter Id from methods {@see addFilter[GS]et()}
	* @return	&$this
	**/
	public function &delFilterSet($propName, $filterId){
		$this->getFilterSet($propName)->offsetUnset($filterId);;
		return $this;
	}#m delFilterSet
}#c settings_filter
?><?
/**
* ReadOnly set - filter. Throws VariableReadOnlyException on try change value.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2010-11-18
* @created 2010-11-18 13:43
**/

/**
* ReadOnly set - filter. Throws VariableReadOnlyException on try change value.
**/
class settings_filter_readOnly extends settings_filter_base{
	public function __construct($propName){
	parent::__construct($propName, null);
	}#__c

	/**
	* @inheritdoc
	* Throws(VariableReadOnlyException)
	**/
	public function apply(&$name, &$value){
	throw new VariableReadOnlyException();
	}#m apply
}#c settings_filter_readOnly
?><?
/**
* Null - filter. Return value "AS IS".
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2010-11-18 13:43
**/

/**
* Null - filter. Return value "AS IS".
**/
class settings_filter_null extends settings_filter_base{
	/**
	* Only one argument required.
	**/
	public function __construct($propName){
	parent::__construct($propName, null);
	}#__c

	public function apply(&$name, &$value){
	return null;
	}#m apply
}#c settings_filter_null
?><?
/**
* Ignore - filter. Ignore all value and always return null.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2010-11-18 13:43
**/

/**
* Ignore - filter. Ignore all value and always return null.
**/
class settings_filter_ignore extends settings_filter_base{
	public function __construct($propName){
	parent::__construct($propName, null);
	}#__c

	public function apply(&$name, &$value){
	$name = null;
	return null;
	}#m apply
}#c settings_filter_ignore
?><?
/**
* Default get - filter. If not value (empty of calback) returns default..
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2011, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2011-03-22 16:24
**/

/**
* Default get - filter. If not value (empty of calback) returns default.
**/
class settings_filter_default extends settings_filter_base{
private $default;
private $callback_;
	/**
	 * If property empty (check by call $emptyCallback) return default value.
	 *
	 * @param	string	$propName
	 * @param	mixed	$defaultValue
	 * @param	callback(null)	$emptyCallback. Should behave as empty() standard
	 *	function - accept 1 argument and returns true if argument considered 'empty'.
	 *	By default - null, then empty construction used itself.
	 */
	public function __construct($propName, $defaultValue, $emptyCallback = null){
	parent::__construct($propName, null);
	$this->default = $defaultValue;
	// PHP does not allow call 'empty' via call_user_func, threat it as language
	//	construction contrary of function.
		if ($emptyCallback){
		$this->callback_ = $emptyCallback;
		}
		else{
		$this->callback_ = create_function('$var', 'return empty($var);');
		}
	}#__c

	public function apply(&$name, &$value){
		if (call_user_func($this->callback_, $value)){
		$value = $this->default;
		}
	}#m apply
}#c settings_filter_null
?><?
/**
* Extended variant of settings_check to handle "uncleared" fields.
*
* @package settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created ???
**/

/**
* Extended variant of settings_check to handle "uncleared" fields.
**/
class settings_check_static extends settings_check{
protected $static_settings = array();
	/**
	* Clear all except uncleared items.
	**/
	public function clear(){
		foreach ($this->getRegularKeys() as $key => $sets){
		$this->__SETS[$key] = null;
		}
	}#m clear

	/**
	* Return array of regular keys, without 'uncleared' (private, static)
	*
	* @return	array
	**/
	public function getRegularKeys(){
	return array_diff(array_keys($this->__SETS), $this->static_settings);
	}#m getRegularKeys
}#c settings_check_static
?><?
/**
* Provide easy to use settigns-cllass for many purpose. Similar array
* of settings, but provide several addition methods, and magick methods
* to be easy done routine tasks, such as get, set, merge and convert to
* string by provided simple format (For more complex formatting {@see
* class HuFormat}).
*
* @package settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0.7
* @created ?2008-05-30 16:08
**/




/**
* Extended variant of settings, with check possible options.
* Slowly, but safely.
**/
class settings_check extends settings{
	public $properties = array();

	/**
	* Constructor.
	*
	* @param	array	$possibles. Array of string - possibe names of propertys.
	* @param	array=null	$array Initial values.
	**/
	function __construct(array $possibles, array $array = null){
		$this->properties = $possibles;
		if ($array) $this->mergeSettingsArray($array);
	}#constructor

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function setSetting($name, $value){
		parent::setSetting($this->checkNamePossible($name, __METHOD__), $value);
	}#m setSetting

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function &getProperty($name){
		return parent::getProperty($this->checkNamePossible($name, __METHOD__));
	}#m getProperty

	/**
	* Add setting vith value in possible settings.
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	nothing
	**/
	public function addSetting($name, $value){
		$this->properties[] = $name;
		parent::setSetting($name, $value);
	}#m addSetting

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr){
		array_walk(array_keys(REQUIRED_VAR($setArr)), array($this, 'checkNamePossible'), __METHOD__);
		parent::setSettingsArray($setArr);
	}#m setSettingsArray

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param	string	$name	Name of required property
	* @return	boolean
	**/
	public function __isset($name) {
		return parent::__isset($this->checkNamePossible($name, __METHOD__));
	}#m __isset

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
		array_walk(array_keys(REQUIRED_VAR($setArr)), array($this, 'checkNamePossible'), __METHOD__);
		parent::mergeSettingsArray($setArr);
	}#m mergeSettingsArray

	/**
	* Check if name is possible, and Throw(ClassPropertyNotExistsException) if not.
	*
	* @param	string	$name. Name to check.
	* @param	string	$method. To Exception - caller method name.
	* @param	string	$walkmethod. Only for array_walk compatibility - it is must be 3d parameter.
	* @return	string	$name
	* @Throws	(ClassPropertyNotExistsException)
	**/
	protected function checkNamePossible($name, $method, $walkmethod = null){
		if (!in_array($name, $this->properties)) throw new ClassPropertyNotExistsException(EMPTY_STR($walkmethod, $method).': Property "'.$name.'" does NOT exist in ' . get_class($this) . '!');
		return	$name;
	}#m checkNamePossible

	/**
	* Emulate nesting.
	*
	* As we reimplement object to do not have properties itself, instead
	*	define it in $this->properties we should  provide mechanism to emulate
	*	nestiong, to do not mention each time again presented properties.
	* So, with this method we can define in childs new propery
	*	$this->properties_addon and than call this method (in constructor f.e.)
	*	to add new props.
	*
	* So, method MUST be called explicitly. No any magic here!!!
	**/
	public function nesting(){
		//We can't use here nor operatorr + (union), nor array_merge function. We need ADD elements.
		array_splice($this->properties, count($this->properties), 1, $this->properties_addon);
	}#m nesting
}#c settings_check
?><?
/**
* Provide easy to use settigns-cllass for many purpose. Similar array
* of settings, but provide several addition methods, and magick methods
* to be easy done routine tasks, such as get, set, merge and convert to
* string by provided simple format (For more complex formatting {@see
* class HuFormat}).
*
* @package Vars
* @subpackage settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0.5
* @created 2008-05-30 23:19
**/


 // Static method ::create()

class settings extends HuClass{
	protected $__SETS = array(); // Array of settings itself

	/**
	* Constructor.
	*
	* @param array=null $array
	**/
	function __construct(array $array = null){
		if ($array) $this->mergeSettingsArray($array);
	}#__c

	/**
	* Set setting by its name.
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	&$this
	**/
	public function &setSetting($name, $value){
		$this->__SETS[$name] = $value;
		return $this;
	}#m setSetting

	/**
	* Rewrite ALL settings. To change only needed - use {@see ::setSetting()} method
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param	array	$setArr
	* @return	nothing
	**/
	public function setSettingsArray(array $setArr){
		$this->__SETS = REQUIRED_VAR($setArr);
	}#m setSettingsArray

	/**
	* Rewrite provided settings by its values. To change single setting you may use {@see ::setSetting()}
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param	array	$setArr
	**/
	public function mergeSettingsArray(array $setArr){
		/**
		* @internal
		* We don't use array_merge there because want preserve keys, even numerical:
		* http://ru2.php.net/manual/en/function.array-merge.php#92602
		* We also can't use simple array concatenation because want overwrite old values by new one...
		* So, doing all manually!
		**/
		foreach (REQUIRED_VAR($setArr) as $key => $val){
			$this->__SETS[$key] = $val;
		}
	}#m mergeSettingsArray

	/**
	* Return requested property by name. For more usefull access see {@see ::__get()} method.
	*
	* @param	string	$name
	* @return	mixed
	**/
	public function &getProperty($name){
		return $this->__SETS[REQUIRED_NOT_NULL($name)];
	}#m getProperty

	/**
	* Usefull alias of {@see ::setSetting()} to provide easy access in style of $obj->PropertyName = 'Some new value';
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	&$this
	**/
	public function &__set($name, $value){
		$this->setSetting($name, $value);
		return $this;
	}#m __set

	/**
	* Usefull alias of {@see ::getProperty()} to provide easy access in style of $obj->PropertyName
	*
	* @param	string	$name
	* @return	mixed
	**/
	public function &__get($name){
		return $this->getProperty($name);
	}#m __get

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param	string	$name	Name of requested property
	* @return	boolean
	**/
	public function __isset($name) {
		return isset($this->__SETS[REQUIRED_NOT_NULL($name)]);
	}#m __isset

	/**
	* Rreturn string in what merged settings by provided format.
	*
	* Descriptiopn of elements $fields {@see ::formatField()} method
	*
	* @param	array	$fields
	* @return	string
	**/
	public function getString(array $fields){
		$str = '';
		foreach (REQUIRED_VAR($fields) as $field){
			$str .= $this->formatField($field);
		}
		return $str;
	}#m getString

	/**
	* Format Field primarly for {@see ::getString}, but may be used and separatly
	* $field one of:
	*	1) Именем настройки. Если найдена такая настройка и она не пуста, подставляется она
	*	2) Просто константной строкой, тогда выводится как есть
	*	2) Массивом, формата:
	*		array(
	*		'str' => Имя настройки. (обязательно)
	*		'prefix' => ''
	*		'suffix' => ''
	*		'defValue' => ''
	*		)
	*		Вместо ассоциативного массива, допустимы и числовые стандартные индексы, чтобы короче писать не:
	*		array('str' =>'tag', 'prefix' => '<', 'suffix' => '>', 'defValue' => '<unknown>'),
	*		а просто, коротко и красиво
	*		array('tag', '<', '>', '<unknown>'),
	*		Передаются в макрос NON_EMPTY_STR, см. его для подробностей
	*
	* @param	array|string	$field
	* @return string
	**/
	public function formatField($field){
		if (is_array($field)){
			if (!isset($field[0])) $field = array_values($field);
			return NON_EMPTY_STR(@$this->getProperty($field[0]), @$field[1], @$field[2], @$field[3]);
		}
		else{
			return EMPTY_STR(@$this->getProperty($field), $field); // Or by name if it just text
		}
	}#m formatField

	/**
	* Clear all settings
	*
	* @return &$this
	**/
	public function &clear(){
		$this->__SETS = array();
		return $this;
	}#m clear

	/**
	* Return amount of settings.
	*
	* @return integer
	**/
	public function length(){
		return sizeof($this->__SETS);
	}#m length
}#c settings

/**
* It's Before declaration of VariableRequiredException may produce cycle of includes...
**/



/**
* Parent class for more usefull using in parents who want be "customizable". Convenient nesting.
**/
class get_settings{
/** WARNING! Must be inicialised in parents! **/
protected /* settings */ $_sets = null;

	/**
	* Overload to provide ref on settings object. So, settings will be changable,
	* but can't be replaced settings object!
	*
	* @param <type> $name
	* @return	mixed
	**/
	public function &__get ($name){
		if ('settings' == $name) return $this->_sets;
	}#m __get

	/**
	* Return settings object
	*
	* @return	&Object(settings)
	**/
	public function &sets(){
	return $this->_sets;
	}#m sets
}#c get_settings
?><?
class SerializedData{
private $__data = array();

//В классе могут стихийно появляться открытые свойства, используем как контейнер для них
//public $text = 'Какой-то текст';

	function __construct (&$serializedStr = null){
		if ($serializedStr){//Если не задано, то создается контейнер, ничего не надо, просто заполнять его
			if (! ($this->__data = @unserialize($serializedStr)) ){
			throw new SerializeException('Ошибка во время ДЕсериализации объекта');
			}
		}
	}

	function __get($name){
	return $this->__data[$name];
	}#m __get

	function __set($name, $val){
	$this->__data[$name] = $val;
	}#m __set

	//It is worth noting that before PHP 5.2.0 the __toString  method was only called when it was directly combined with echo() or print().
	function __toString(){
	return serialize($this->__data);
	}#m __toString

	function toString(){
	return $this->__toString();
	}#m __toString
}#c SerializedData
?><?
/**
* Routine tasks to made easy OOP.
*
* @package Vars
* @subpackage Settings
* @version 0.4
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-01 22:12
**/

//It used for __autoload, so, we must directly provide dependencies here


 // OS::is_includable


/**
* Class to provide easy access to $GLOBALS['__CONFIG'] variables.
* Intended use with Singleton class as:
* @example Single::def('HuConfig')->config_value
**/
class HuConfig extends settings_check{
	private $_include_tryed = array();

	function __construct() {
		parent::__construct(array_keys($GLOBALS['__CONFIG']), $GLOBALS['__CONFIG']);
	}#__c

	/**
	* As __get before.
	* Now {@see __get()} reimplemented to return HuArray instead of raw arrays
	* Bee careful - after standard call (not raw) original Array value was replaced by HuArray!
	*
	* @param string	$varname
	* @param boolean(false)	$nothrow If true - silently not thrown any exception.
	* @return &mixed
	**/
	public function &getRaw($varname, $nothrow = false){
		return $this->getProperty($varname, $nothrow);
	}#m getRaw

	/**
	* For more comfort access in config fields without temporary variables like:
	* Single::def('HuConfig')->test->first
	*
	* @param string	$varname
	* @return &Object(HuArray)
	**/
	public function &__get($varname){
		$ret =& $this->getProperty($varname);

		if (is_array($ret)){
			$ret = new HuArray($ret); //Replace original on the fly
			return $ret;
		}
		else return $ret;
	}#m __get

	/**
	* Reimplement as initial, only return value by reference
	* Also try include file 'includes/configs/' . $name . '.config.php' if it exist to find needed settings.
	* @inheritdoc
	* @param	boolean(false)	$nothrow If true - silently not thrown any exception.
	**/
	public function &getProperty($name, $nothrow = false){
		try{
			return $this->__SETS[$this->checkNamePossible(REQUIRED_NOT_NULL($name), __METHOD__)];
		}
		catch(ClassPropertyNotExistsException $cpne){
			//Try include appropriate file:
			if (!in_array($name, $this->_include_tryed)){
				$this->_include_tryed[] = $name; //In any case to do not check again next time
				$path = 'includes/configs/' . $name . '.config.php';
				if(OS::is_includeable($path)){
					include($path);
				}
				if(m()->is_set($name, $GLOBALS['__CONFIG'])){//New key
					$this->addSetting($name, $GLOBALS['__CONFIG'][$name]);
				}
				return $this->__SETS[$name];
			}

			//Silent if required.
			if (!$nothrow) throw $cpne; //If include and fine failed throw outside;
			else{
				// Avoid: Notice: Only variable references should be returned by reference in /var/www/_SHARED_/Vars/HuConfig.class.php on line 101
				$t = null;
				return $t;
			}
		}
	}#m getProperty
}#c

/**
* Short alias to Single::def('config'). In case of we can-t define constant like:
* define('CONF', Single::def('config'));
* In this case got error: PHP Warning:  Constants may only evaluate to scalar values
* We can do that as variable like $CONF, but meantime it is not convenient in functions/methods:
* we must use global $CONF; first, or also very long $GLOBALS['CONF']
*
* So, choose function aliasing. Now we can invoke it instead of Single::def('HuConfig')->config_value
* or even $GLOBALS['CONF']->someSetting but just:
* CONF()->config_value
*
* Furthermore most often use of that will: Single::def('HuConfig')->className->setting.
* So, class name put to optioal parameter to allow like:
* CONF('className')->desiredClassOption
*
* @param	string(null)	$className Optional class name
* @param	boolean(false)	$nothrow If true - silently not thrown any exception.
* @return Single_Object(HuConfig)|Object(HuArray). If className present - Object(HuArray) returned, Single_Object(HuConfig) otherwise to next query.
**/
function &CONF($className = null, $nothrow = false){
	/*
	* Strange, but if we direct return:
	* if ($className) return Single::def('HuConfig')->$className;
	* All work as expected and variable returned by reference, but notice occured:
	* PHP Notice:  Only variable references should be returned by reference in /var/www/_SHARED_/Vars/HuConfig.class.php on line 111
	* implicit call to __get solve problem. Is it bug?
	* @todo Fill bug
	**/
	/*
	* We want use HuConfig in singleton::def. It is produce cycle dependency.
	* So, rely on HuConfig do not take any settings in constructor, we may sefely call Single::singleton directly
 	if ($className) return Single::def('HuConfig')->__get($className);
	else return Single::def('HuConfig');
	**/
	if ($className) return Single::singleton('HuConfig')->__get($className);
	else return Single::singleton('HuConfig');
}#f CONF

/**
* @example
* dump::a(Single::def('HuConfig')->test);
* dump::a(Single::def('HuConfig')->test->First);
* dump::a(Single::def('HuConfig')->test->Second);
* Single::def('HuConfig')->test->Second = 'Another text';
* dump::a(Single::def('HuConfig')->test->Second);
* CONF()->test->Second = 'Yet ANOTHER Another text';
* dump::a(CONF()->test->Second);
* dump::a(Single::def('HuConfig')->test);
**/
?><?
/**
* Routine tasks to made easy OOP.
*
* @package Vars
* @subpackage Classes
* @version 1.5
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-31 5:31 v 1.0b to 1.0c
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses ClassUnknownException
**/

/**
* To explicit indicate what value not provided, also Null NOT provided too!
**/
class NullClass {}

abstract class HuClass{
	/**
	* To extends most (all) classes.
	* Or to fast copy (with runkit_method_copy) into other classes.
	* Method to allow constructions like: className::create()->methodName() because (new classname())->methodName are NOT allow them!!!
	*
	* @param variable parameters according to class.
	* @return instance of the reguired new class.
	* @Throw(ClassUnknownException)
	**/
	public static function create(){
		// http://blog.felho.hu/what-is-new-in-php-53-part-2-late-static-binding.html
		if (function_exists('get_called_class')) $className = get_called_class(); // Most reliable if available
		else throw new ClassUnknownException('Can\'t determinate class name for who was called ::create() (LSB is not accesible [present start from PHP 5.3.0-dev]). You can use ::createWithoutLSB method or classCREATE() free function with explicit name of needed class!');

		$reflectionObj = new ReflectionClass($className);

		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(func_get_args());
		else return $reflectionObj->newInstance();
	}#m create

	/**
	* This is just wrupper for system construction 'clone'. Objects in PHP implicitly returned by
	*	reference (see http://www.php.net/manual/en/language.references.spot.php#59820), so, to use it
	*	without modification of main object I should clone it, but it is break one line counstruction chain.
	* F.e. I want only count such items:
	*	$dellin->getNotMatchedCities()->filter(create_function('$v', 'return is_null($v->region);'))->count()
	* it implicitly MODIFY $dellin object! Off course if it is not return clone of object itself, what is not
	*	a common case. So, we want do something similar:
	* (clone $dellin->getNotMatchedCities())->filter(create_function('$v', 'return is_null($v->region);'))->count()
	* But PHP does not allow such construction and fire there parsing error.
	* For this case the method intended. In our example it whould:
	* HuClass::clone($dellin->getNotMatchedCities())->filter(create_function('$v', 'return is_null($v->region);'))->count()
	*
	* Some developer notes:
	*	- Unfortunately we can't name it as just clone even in class because it is reserved word.
	*	- I use clone in method because argument itself again implicitly passed as reference, so it is required.
	**/
	public static function cloning($obj){
		return clone $obj;
	}#m cloning

	/**
	* This is similar create, but created for backward capability only.
	* It is UGLY. Do not use it, if you have choice.
	* It is DEPRECATED immediately after creation! But now, realy, it is stil neded :(
	*
	* @deprecated
	* @param	$directClassName = null - The directy provided class name to instantiate.
	* @params	variable parameters according to class.
	* @return	instance of the reguired new class.
	* @Throws(VariableRequiredException)
	**/
	static function createWithoutLSB($directClassName /*, Other Params */){
		
		$reflectionObj = new ReflectionClass(REQUIRED_VAR($directClassName));
		$args = func_get_args();//0 argument - $directClassName
		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(array_slice($args, 1));
		else return $reflectionObj->newInstance();
	}#m createWhithoutLSB

	/**
	* PHP hasn't any normal possibilities to cast objects into derived class (reinterpret_cast analog). We need hack to do it.
	* See http://ru2.php.net/mysql_fetch_object comments by "Chris at r3i dot it"
	* So, in this page, below, i found next fine workaraound (see comment and example of "trithaithus at tibiahumor dot net")
	*
	* Also this hack was be founded here http://blog.adaniels.nl/articles/a-dark-corner-of-php-class-casting/
	*
	* @param $toClassName string Class name to what casting do
	* @param $what mixed
	* @return Object($toClassName)
	**/
	static function cast($toClassName, $what){
		return unserialize(
			preg_replace(
				'/^O:[0-9]+:"[^"]+":/',
				'O:'.strlen($toClassName).':"' . $toClassName . '":',
				serialize($what)
			)
		);
	}#m cast
}#c HuClass

/**
* Free function. For instantiate all objects.
* {@inheritdoc HuClass::createWithoutLSB}
**/
function classCREATE($ClassName /*, Other Params */){
	/*
	* We must use temporary variable due to error:
	* PHP Fatal error:  func_get_args(): Can't be used as a function parameter in /home/_SHARED_/Vars/HuClass.php on line 107
	**/
	$args = func_get_args(); //0 argument - $ClassName
	return call_user_func_array(
		array(
			'HuClass',
			'createWithoutLSB'
		)
		,$args
	);
}#f classCREATE
?><?
/**
* Class to provide OOP interface to array operations.
*
* @package Vars
* @version 1.2.4
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-09-22 17:55 ver 1.1 to 1.1.1
*
* @uses REQUIRED_NOT_NULL()
* @uses VariableIsNullException
* @uses settings
**/



class HuArray extends settings implements Iterator{
const huScheme = 'hu://';

	/**
	* Constructor.
	*
	* @param	(array)mixed=null	$array	 Mixed, explicit cast as array!
	**/
	function __construct(/*(array)*/ $array = null){
		parent::__construct((array)$array);
	}#__c

	/**
	* Push values.
	*
	* @param	mixed	$var.
	* @params	mixed	any amount of vars (First explicity to make mandatory one at once)
	* @return	&$this
	**/
	public function &push($var){
		//On old PHP got error: PHP Fatal error:  func_get_args(): Can't be used as a function parameter in /home/_SHARED_/Vars/HuArray.php on line 58
		//call_user_func_array('array_push', array_merge(array(0 => &$this->__SETS), func_get_args()));
		//Do the same with temp var:
		$args = func_get_args();
		call_user_func_array('array_push', array_merge(array(0 => &$this->__SETS), $args));
		return $this;
	}#m push

	/**
	* Push array of values.
	*
	* @param 	array	$arr
	* @return	&$this
	**/
	public function &pushArray(array $arr){
		if ($arr)
			call_user_func_array('array_push', array_merge(array(0 => &$this->__SETS), $arr));
		return $this;
	}#m pushArray

	/**
	* Push values from Object(HuArray).
	*
	* @param 	mixed	$var.
	* @return	$this->pushArray()
	**/
	public function &pushHuArray(HuArray $arr){
		return $this->pushArray($arr->getArray());
	}#m pushHuArray

	/**
	* Return last element in array. Reference, direct-editable!!
	*
	* @return &mixed
	**/
	public function &last(){
		end($this->__SETS);
		return $this->__SETS[key($this->__SETS)];
	}#m last

	/**
	* Return Array representation (cast to (array)).
	*
	* @return	array
	**/
	public function getArray(){
		return $this->__SETS;
	}#m getArray

	/**
	* {@see http://php.net/array_slice}
	*
	* @param	integer	$offset
	*	Если параметр offset положителен, последовательность начнётся на расстоянии offset от начала array. Если offset отрицателен, последовательность начнётся на расстоянии offset от конца.
	* @param	integer	$length
	*	Если в эту функцию передан положительный параметр length, последовательность будет включать length элементов. Если в эту функцию передан отрицательный параметр length, в последовательность войдут все элементы исходного массива, начиная с позиции offset и заканчивая позицией, отстоящей на length элементов от конца. Если этот параметр будет опущен, в последовательность войдут все элементы исходного массива, начиная с позиции offset.
	* @param	boolean	$preserve_keys
	*	Обратите внимание, поумолчанию сбрасываются ключи массива. Можно переопределить это поведение, установив параметр preserve_keys в TRUE.
	* @return Object(HuArray)
	**/
	public function getSlice($offset, $length = null, $preserve_keys = false){
		return new HuArray(array_slice($this->__SETS, $offset, EMPTY_VAR($length, sizeof($this->__SETS)), $preserve_keys));
	}#m getSlice

	/**
	* Overload to return reference.
	*
	* @param	mixed	$name
	* @return	&mixed
	* @Throws(VariableIsNullException)
	**/
	public function &getProperty($name){
		return $this->__SETS[REQUIRED_NOT_NULL($name)];
	}#m getProperty

	/**
	* @var	&mixed	->_last_
	**/
	/**
	* Overload to return reference.
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	function &__get($name){
		/**
		* Needed name, because $var->last() = 'NewVal' produce error, even if value returned by reference:
		* PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		**/
		if ('_last_' == $name) return $this->last();
		/*
		* Short form of ::hu. To allow constructions like:
		* $obj->{'hu://varName'}->{'hu://0'};
		* instead of directly:
		* $obj->hu('varName')->hu(0);
		* As you like
		**/
		elseif( self::huScheme == substr($name, 0, strlen(self::huScheme)) ) return $this->hu( substr($name, strlen(self::huScheme)) );
		else
			return $this->getProperty($name);
	}#m __get

	/**
	* Like standard {@see __get()}, but if returned value is regular array, convert it into HuArray and return reference to it.
	* @example:
	* $ha = new HuArray(
	*	array(
	*		'one' => 1
	*		,'two' => 2
	*		,'arr' => array(0, 11, 22, 777)
	*	)
	* );
	* dump::a($ha->one);
	* dump::a($ha->arr);					// Result Array (raw, as is)!
	* dump::a($ha->hu('arr'));				// Result HuArray (only if result had to be array, as is otherwise)!!! Original modified in place!
	* dump::a($ha->hu('arr')->hu(2));			// Property access. Alse as any HuArray methods like walk(), filter() and any other.
	* dump::a($ha->{'hu://arr'}->{'hu://2'});	// Alternative method ({@see ::__get()}). Another, form.
	* Also this form is allow writing:
	* $ha->{'hu://arr'} = 'Qwerty';
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	function &hu($name){
		if (is_array($this->$name)) $this->$name = new HuArray($this->$name);
		return $this->getProperty($name);
	}#m hu

	/**
	* Allow change value by short direct form->setttingName = 'qwerty';
	*
	* @param	string	$name
	* @param	mixed	$value
	**/
	function &__set($name, $value){
		/**
		* Needed name, because $var->last() = 'NewVal' produce error, even if value returned by reference:
		* PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		**/
		if ('_last_' == $name){
			$ref =& $this->last();
		}
		elseif( self::huScheme == substr($name, 0, strlen(self::huScheme)) ) $ref =& $this->hu( substr($name, strlen(self::huScheme)) );
		else{
			$ref =& $this->getProperty($name);
		}
		$ref = $value;
	}#m __set

	/**
	* Apply callback function to each element.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function walk($callback){
		array_walk($this->__SETS, $callback);
		return $this;
	}#m walk

	/**
	* Filter array, using callback. If the callback function returns true, the current value from input is returned into the result
	* array. Array keys are preserved.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function &filter($callback){
		$this->__SETS = array_filter($this->__SETS, $callback);
		return $this;
	}#m filter

	/**
	* Filter array by keys and leave only mentioned in $keys array
	*
	* @param	array	$keys
	* @return	&$this
	**/
	public function &filterByKeys(array $keys){
		$this->__SETS = array_diff_key( $this->__SETS, array_flip(  array_intersect(   array_keys($this->__SETS), $keys   )  ) );
		return $this;
	}#m filterByKeys

	/**
	* Filter array by keys and leave only NOT mentioned in $keys array (opposite to method {@see ::filterByKeys()})
	*
	* Implementation idea taken from: http://ru.php.net/array_filter comment of niehztog
	*
	* @param	array	$keys
	* @return	&$this
	**/
	public function &filterOutByKeys(array $keys){
		$this->__SETS = array_diff_key( $this->__SETS, array_flip($keys) );
		return $this;
	}#m filterOutByKeys

	/**
	* Similar to {@see ::filer()} except of operate by keys instead of values.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function &filterKeysCallback($callback){
		$keys = new self(array_flip( $this->__SETS ));
		$keys->filter($callback);
		$this->filterByKeys($keys->getArray());
		return $this;
	}#m filterKeysCallback

	/**
	* Implode to the string using provided delimiter.
	*
	* @param	string=''	$delim
	* @return	string
	**/
	public function implode($delim = ''){
		return implode($delim, $this->__SETS);
	}#m implode

	/**
	* Return number of elements
	*
	* @return	int
	**/
	public function count(){
		return count($this->__SETS);
	}#m count

	/**
	* Iteratively reduce the array to a single value using a callback function.
	* @link http://ru.php.net/array_reduce
	*
	* @param	callback	$callback
	* @param	integer	$initial
	* @return	mixed
	**/
	public function reduce($callback, $initial = 0){
		return array_reduce($this->__SETS, $callback, $initial);
	}#m reduce

	/// From interface Iterator ///

	public function rewind(){
		reset($this->__SETS);
	}#m rewind

	public function current(){
		return /* $var = */ current($this->__SETS);
	}#m current

	public function key(){
		return /* $var = */ key($this->__SETS);
	}#m key

	public function next(){
		return /* $var =*/ next($this->__SETS);
	}#m next

	public function valid(){
		return ($this->current() !== false);
	}#m valid
}#c HuArray
?><?
/**
* Debug and backtrace toolkit.
*
* Class to provide easy wrapper aroun HuFormat for anywhere usage.
*
* @package Debug
* @subpackage HuLOG
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-05-20 19:10
**/

/**
* Class to provide easy wrapper to dump DOMElement (and DOMnode possibly), which default dump seems like:
* object(DOMElement)#97 (0) {
* }
* This wrapper put it into DOMDocument, and autput it as formated XML. For output standard family of dump::* methods used.
*
* @uses commonOutExtraData
* @uses dump
**/
class DOMnodeOutExtraData extends commonOutExtraData{
	protected /* DOMDocument */ $_var;

	/**
	* Constructor.
	*
	* @param	Object(DOMNode)	$var Var to output with provided format.
	* @param	string='utf-8'	$format	Format how output $vavr. Must contain 3 elements:
	*	'FORMAT_CONSOLE', 'FORMAT_WEB', 'FORMAT_FILE' each represent according
	*	format (See class {@see HuFormat} for more details).
	**/
	function  __construct(DOMNode $var, $encoding = 'utf-8'){
		$this->_var = new DOMDocument('1.0', $encoding); // DOMDocument NEEDED ot import into it nodes, it also NEEDED to export result asXML...
		$this->_var->appendChild($this->_var->importNode($var, true));
		$this->_var->preserveWhiteSpace = false;
		$this->_var->formatOutput = true;
	}#__c

	public function strToConsole($format = null){
		return dump::c(trim($this->_var->saveXML()), null, true);
	}#m strToConsole

	public function strToFile($format = null){
		return dump::log(trim($this->_var->saveXML()), false, true);
	}#m strToFile

	public function strToWeb($format = null){
		return dump::w(trim($this->_var->saveXML()), false, true);
	}#m strToWeb
}#c huFormatOutExtraData
?><?
/**
* Constants manipulation
*
* @package Vars
* @subpackage Consts
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-29 17:45 ver 1.0 to 1.0.1
**/

/**
* @example consts.example.php
**/
class consts{ // constants
	/**
	* Возвращает массив констант
	*
	* @param	string	Category of constants needed.
	* @param	string	Regexp to filter out. Default "@.*@i", what meen - no filter, return all.
	* @param	boolean	True, if want do NOT categorize items
	* @return	array	Associative array of matched constants with its values.
	*/
	public static function get_regexp($category='', $regexp='@.*@i', $not_categorized=false){
		// It seems just presents of argument checked, without of dependency of it value (true, false and null probed)
		if ($not_categorized) $constants = get_defined_constants($not_categorized);
		else $constants = get_defined_constants();

		$consts = ( ($not_categorized or empty($category))? $constants : $constants[$category] );
		$new_consts = array();
		if (is_array(reset($consts))){
			foreach ($consts as $key => $c_arr){
				$new_c_arr = @array_flip (preg_grep ( $regexp, array_flip($c_arr) ));
				if ( ! empty($new_c_arr) ) $new_consts[$key] = $new_c_arr;
			}
		}
		else{
			$new_consts = @array_flip (preg_grep ( $regexp, array_flip($consts) ));
		}

		return $new_consts;
	}#m get_regexp

	/**
	* Return pair Constant-name and it values
	*
	* @param	string Constant name.
	* @return array Associative array with key of constant-name, and value it value
	*/
	public static function get($const){
		return array($const => constant($const));
	}#m get

	/**
	* Locate constant-name by its value.
	*
	* @param mixed	$value - needed value
	* @param	string	Category of constants needed. {@see ::get_regexp}
	* @param	string	Regexp to filter out. Default "@.*@i", what meen - no filter, return all. {@see ::get_regexp}
	* @param	boolean	True, if want do NOT categorize items {@see ::get_regexp}
	* @return	array	Associative array of matched constants with its values.
	**/
	public static function getNameByValue($value, $category='', $regexp='@.*@i', $not_categorized=false){
		$constants = self::get_regexp($category, $regexp, $not_categorized);
		$cmp = new const_value_filter($value);

		if (!is_array(current($constants)))
			return array_filter($constants, array($cmp, 'cmp'));
		else{
			foreach ($constants as $key => &$arr){
				$constants[$key] = array_filter($constants[$key], array($cmp, 'cmp'));
			}
			return array_filter($constants);
		}
	}#m getNameByValue
}#c consts

/*
* Due to:
* PHP Fatal error:  Class declarations may not be nested in ...
* it helper-class must be defined in global scope.
**/
class const_value_filter{
	private $_val;

	function __construct(&$val){
		$this->_val =& $val;
	}#__c

	function cmp(&$item){
		return ($this->_val == $item);
	}#m cmp
}#c
?><?
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
?><?
/**
* Manipulate processes on *NIX-like systems.
*
* @package Process
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* Base idea got from http://www.php.net/manual/ru/function.proc-open.php
*
* @uses ProcessException
**/

 //Function used. Must be included explicit.

/* Aka struct of data */
class Process_state{
	/*All this members was private on Process class, so, now will be public,
	if we do not provides complex of get/set method to each */
	public $writeData;

	public $exit_code;

	private $cwd = null;
	private $env = array();

	public $nonBlockingMode = false;
	public $nonBlockTimeout = 500000;// microseconds

	public $retval;
	public $error;

	public $CMD;

	public function getCwd(){
		return $this->cwd;
	}#m getCwd

	public function setCwd($newCwd){
		$this->cwd = $newCwd;
	}#m setCwd

	public function getEnv(){
		return $this->env;
	}#m getEnv

	public function setEnv(array $env){
		$this->env = $env;
	}#m setEnv

	public function getResult(){
		return $this->retval;
	}#m getResult

	public function getError(){
		return trim($this->error);
	}#m getError

	public function describe(){
		return log_dump(
			array(
				'writeData'	=> $this->writeData,
				'retval'		=> $this->getResult(),
				'error'		=> $this->getError(),
				'exit_code'	=> $this->exit_code,
				'cwd'		=> $this->getCwd(),
				'env'		=> trim(log_dump($this->getEnv())),
				'nonBlockingMode'	=> $this->nonBlockingMode,
				'nonBlockTimeout'	=> $this->nonBlockTimeout
			)
		);
	}#m describe
}#c Process_state

class Process{
	const STDIN = 0;
	const STDOUT = 1;
	const STDERR = 2;

	private $descriptorspec = array(
		0 => array('pipe', 'r'),
		1 => array('pipe', 'w'),
		2 => array('pipe', 'w')
	);

	private $resource = null;
	private $pipes;

	private $state;

	function __construct(Process_state $state, $doNOTopen = false){
		$this->setState($state);
		if ($this->state->CMD and !$doNOTopen ) $this->open();
	}#__c

	public function &getState(){
		return $this->state;
	}#m getState

	public function setState($state){
		$this->state = $state;
	}#m setState

	public function open(){
		$this->resource = proc_open($this->state->CMD, $this->descriptorspec, $this->pipes, $this->state->getCwd(), $this->state->getEnv());

		if (!is_resource($this->resource)){
			throw new ProcessException ('Can\'t open process!'.$this->state->describe(), 0, $this->getState());
		}
}

	public function setNonBlockingMode($nonBlock = true, $nonBlockTimeout = 500000){
		$this->state->nonBlockingMode = $nonBlock;
		$this->state->nonBlockTimeout = $nonBlockTimeout;
		if ($this->state->nonBlockingMode){
			stream_set_blocking($this->pipes[self::STDIN], false);
			stream_set_blocking($this->pipes[self::STDOUT], false);
			stream_set_blocking($this->pipes[self::STDERR], false);
		}
	}#m setNonBlockingMode

	public function writeIn($inStr = false, $noWait = false){
		// By dafault saved data write
		if ($inStr) $this->state->writeData = $inStr;
		fwrite($this->pipes[self::STDIN], $this->state->writeData);
		fflush($this->pipes[self::STDIN]);
		if (! $this->state->nonBlockingMode) fclose($this->pipes[self::STDIN]);
		elseif ($this->state->nonBlockingMode and ! $noWait) usleep ($this->state->nonBlockTimeout);
	}#m writeIn

	public function readOut(){
		$this->state->retval = stream_get_contents($this->pipes[self::STDOUT]);
		fflush($this->pipes[self::STDOUT]);
		if (! $this->state->nonBlockingMode) fclose($this->pipes[self::STDOUT]);
	}#m readOut

	public function readErr(){
		$this->state->error = stream_get_contents($this->pipes[self::STDERR]);
		if (! $this->state->nonBlockingMode) fclose($this->pipes[self::STDERR]);
	}#m readErr

	public function closeAll(){
		if ($this->state->nonBlockingMode){
			@fclose($this->pipes[self::STDIN]);
			@fclose($this->pipes[self::STDOUT]);
			@fclose($this->pipes[self::STDERR]);
		}
		$this->state->exit_code = proc_close($this->resource);
		if ($this->state->exit_code) throw new ProcessException('Ended with non 0 status! - '.$this->state->exit_code."\n".$this->state->describe(), 0, $this->getState());
	}#m run

	public function execute(){
		$this->readErr();
		$this->readOut();
		$this->closeAll();
		if ($this->state->getError()) throw new ProcessException($this->state->getError().$this->state->describe(), 0, $this->getState());
		return $this->state->getResult();
	}#m execute

	public static function exec($command, $cwd = null, array $env = null, $writeData = null){
		if (! $command instanceof Process_state){
			$state = new Process_state();
			$state->CMD = $command;
			if ($cwd) $state->setCwd($cwd);
			if ($env) $state->setEnv($env);
			if ($writeData) $state->writeData = $writeData;
		}
		else{
			$state = $command;
		}

		$prcs = new Process($state);
		$prcs->writeIn();
		return $prcs->execute();
	}#m exec

/*
function __destruct(){
	if ($this->pipes[self::STDIN]) fclose($this->pipes[self::STDIN]);
	if ($this->pipes[self::STDOUT]) fclose($this->pipes[self::STDOUT]);
	if ($this->pipes[self::STDERR]) fclose($this->pipes[self::STDERR]);
}#__d
*/
}#c Process

/*
EXAMPLES
try{
//Standalone Usage
$prcs = new Process('enca');
$prcs->writeIn(file_get_contents('t1'));
$prcs->readOut();
$prcs->closeAll();
c_dump($prcs->getResult());
//\standalone

//Non Blocking mode of descriptors. Allow execute more than one command!
$prcs = new Process('bash');
$prcs->setNonBlockingMode(true, 50000);
$prcs->writeIn("ls -1\n");
$prcs->readErr(); c_dump($prcs->getError());
$prcs->readOut(); c_dump($prcs->getResult());

$prcs->writeIn("date\n");
$prcs->readErr(); c_dump($prcs->getError());
$prcs->readOut(); c_dump($prcs->getResult());
$prcs->closeAll();
//\non blocking

//Simple usage
$prcs = new Process('df -h');
echo $prcs->execute();
//\simple

//Static call
echo Process::exec('w');
//\static
}
catch (Exception $e){
    echo 'Exception: '.$e->getMessage() . "\n";
    // there was a problem executing the command
}
*/
?><?
/**
* System environment and information
* @package System ??
* @version 2.0.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-11-05 00:47 ver 2.0b to 2.0.1
**/

/**
* Class OS has mainly (all) static methods, to determine system-enveroments, like OS or type of out.
* Was System, but it is registered in PEAR, change to OS
**/
class OS {
	const OUT_TYPE_BROWSER = 1;
	const OUT_TYPE_CONSOLE = 2;
	const OUT_TYPE_PRINT = 4; /** Pseudo!!! Need automaticaly detect OUT_TYPE_BROWSER or OUT_TYPE_CONSOLE */
	const OUT_TYPE_FILE = 8;
	const OUT_TYPE_WAP = 16;
	#const OUT_TYPE_ = 16;

	/**
	* Possible return-values of
	* http://ru2.php.net/php_sapi_name comment from "cheezy at lumumba dot luc dot ac dot be"
	**/
	static $SAPIs = array(
		'aolserver',
		'activescript',
		'apache',
		'cgi-fcgi',
		'cgi',
		'isapi',
		'nsapi',
		'phttpd',
		'roxen',
		'java_servlet',
		'thttpd',
		'pi3web',
		'apache2filter',
		'caudium',
		'apache2handler',
		'tux',
		'webjames',
		'cli',
		'embed,',
		'milter'
	);


	/**
	* Determines out type of current-running process.
	*
	* @return Now one of const: ::OUT_TYPE_BROWSER or ::OUT_TYPE_CONSOLE
	**/
	static public function getOutType(){
		if (isset($_SERVER['HTTP_USER_AGENT'])) return self::OUT_TYPE_BROWSER;
		else return self::OUT_TYPE_CONSOLE;
	}#m getOutType

	/**
	* php_sapi_name()
	*
	* @return
	**/
	static public function phpSapiName(){
		return php_sapi_name();
	}#m phpSapiName

	/**
	* Check if file is includable. I can't just use if (@inlude($file)). Or, more exactly i can, but
	*	it is have small different meaning:
	*	@include('include.php') not return and NOT shown errors in including file! Nothing:
	*		Not Notice, Warning or Fatal!!!!
	*		See http://ru2.php.net/manual/ru/function.include-once.php comments of
	*		"flobee at gmail dot com" and "php at metagg dot com" and http://php.net/include/
	*		comment of "medhefgo at googlemail dot com"
	*		In other words, absent way (get me known if I am wrong) to suppress errors like
	*		'file not found' or 'not readable', construction @include suppres ALL (even Critical!)
	*		in including files, and nested (included from including).
	*	Result of check may be also applyable to require()
	*
	* @param	string $filenam As it can be passed to include or require.
	* @return	boolean
	**/
	static public function is_includeable($filename){
		/** is_file, is_readable not suitable, because include_path do not take effect.
		* And opposite comment of "php at metagg dot com" and "medhefgo at googlemail dot com",
		* woudn't manualy check all paths in include_path. Just open this file to read
		* with include_path check parameter support! */
		if ($res = @fopen($filename, 'r', true)){
			fclose($res);	// Not realy need opened file, only result of opening.
		}
		return (bool)$res;
	}#m is_inludeable

	/**
	* Check if given path is absolute or not.
	*
	* @param $pathToCheck	string Path to check
	* @return boolean
	**/
	static public function isPathAbsolute($pathToCheck){
		if ( preg_match('@^(?:' . implode('|', stream_get_wrappers()) . ')://@', $pathToCheck) ) return true; // Registered wrappers always absolute!

		//@TODO: case 'DAR': ;break; //Darwin http://qaix.com/php-web-programming/139-944-constant-php-os-and-mac-server-read.shtml
		// This check from http://ru2.php.net/php_uname
		if ('WIN' != strtoupper(substr(PHP_OS, 0, 3))){
			return ( '/' == $pathToCheck{0} );
		}
		else{//WIN
			return ( ':' == $pathToCheck{1} );
		}
	}
}#c OS
?><?
/**
* @deprecated Now use HuGetopt to any purpose! Stay for backward compatibylity.
* RegExp manupulation.
*
* @package HuGetopt
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

require_once 'Console/Getopt.php'; // From PEAR

class Hu_Console_Getopt extends Console_Getopt{
	public static function SwapKeysAndNumbers($options){
		$ret = array (
			0 => array(), //To be filling
			1 => $options[1] //Just copy
		);

		foreach ($options[0] as $key => $val){
			$ret[0][$options[0][$key][0]] = array(0 => $key, 1 => $options[0][$key][1]);
		}
		return $ret;
	}#m SwapKeysAndNumbers
}#c Hu_Console_Getopt
?><?
/**
* Console package to parse parameters in CLI-mode
*
* @package Console
* @subpackage Getopt
* @version 0.1.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-30 15:52 v 0.1.1 to 0.1.2
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
**/




class HuGetoptArgumentRequiredException extends VariableRequiredException{}

class HuGetopt_option extends settings_check{
/**
* Like this (examples, structure):
*	||| First "Availability"
*	|-> OptL	=> 'long'
*	|-> OptS	=> 's'
*	|-> Mod		=> ':'
*	||| Then, 'real'. This fields filled only after parse user input. Each array, because more then once may be presents in CL.
*	|-> Opt		=> HyArray('s')		// Opt present, parsed from user-input.
*	|-> Sep		=> HuArray('-')		// '--'
*	|-> Val		=> HuArray('Text')	//Or just true if interesting only present it or not.
*	||| Misc
*	|-> =		=> HuArray(false)
*	|-> 'OptT'	=> HuArray('s', 'l')//OptType
**/

	/**
	* @inheritdoc
	**/
	public function __construct(array $possibles, array $array = null){
		parent::__construct($possibles, $array);
		foreach (array('Opt', 'Sep', 'Val', '=', 'OptT') as $k){
			if ( !isset($this->{$k}) ) $this->setSetting($k, new HuArray);
		}
	}#__c

	/**
	* Add parsed option in values HuArrays (Opt, Sep, Val, =, OptT)
	*
	* @param Object(HuGetopt_option) $toAdd
	* @return	&$this
	**/
	public function add(HuGetopt_option $toAdd){
		foreach (array('Opt', 'Sep', 'Val', '=', 'OptT') as $k){
			$this->{$k}->pushHuArray($toAdd->{$k});
		}
		return $this;
	}#m add

}#c HuGetopt_option

/**
* @example of use HuGetopt.example.php
**/
class HuGetopt_settings extends settings{
	protected $__SETS = array(
		'start_short'	=> array('-'),
		'start_long'	=> array('--'),
		'alternative'	=> false,	/** Allow long options to start with a short_start (‘-’ by default) **/

		/** {@see class HuGetopt_option} to description name and {@see class settings_check} to check option. **/
		'HuGetopt_option_options'	=> array(
			'OptL', 'OptS', 'Mod',
			'Opt', 'Sep', 'Val',
			'=', 'OptT'
		)
	);
}#c HuGetopt_settings

/**
* First there was a try to use http://pear.php.net/Console_getopt. Created Hu_Console_Getopt
* extending pear class. But it is very limited:
*	1. No way to bind short options to long
*	2. No way get value presented by long OR short options. They logic only outside.
*	3. Options provided after non optioned arguments - compleatly ignored.
*	4. In case when one option provided more than once - only last value present, other lost.
*
* Adjust PEAR Console_getopt is very difficult, so, write self version.
*
* In most casese behaviour this class is same as described in GNU "man 3 getopt", with several exceptions-additionals:
*	1) Format of incoming options (optstring by GNU man) is different, more flexible allow associate short option with long!
*	2) Don't support GNU extension -W
*	3) Enviroment variable POSIXLY_CORRECT not handled, and behaviour always same as GNU default (first +/- in optstring modes too not handled!!!)
*	4) Additionly in settings moved 'long_start' ('--') and 'short_start' ('-') and may be changed if you want.
* 		Even more, it is array, and may contain any amount of element. It is usefull, if you, for example, wish use '-' and '+' in short options.
*	5) PHP-CLI self do NOT correctly handle long options with sign "=" form or without space:
*		--longOpt 'optarg' - correct
* 		--longOpt='optarg' - In $argv placed full, not correct exploded to opt and optarg.
*		--longOpt'optarg' - In $argv placed full, not correct exploded to opt and optarg.
*		GuGetopt correct handle all this cases.
*	6) Also PHP-CLI does NOT handle short options in clue form (F.e. -o -t -f -s - does, -otfs - NOT). So, HuGetopt - handle it properly!
**/
class HuGetopt extends get_settings{
	/**
	* Array of raw arguments to parse
	* @var array
	*/
	private $argv;

	/**
	* Parsed options (what provided to parse arguments of command line).
	* @var array
	**/
	private $_opts;

	/**
	* Non-option arguments (all other)
	* @var HuArray
	**/
	private $_nonopts;

	/**
	* Parsed arguments from command line.
	* @var array
	**/
	private $_args;

	/**
	* Short and long and long arrays cache. Only fore speedup, otherwise need iterate each time ti find needed.
	* @var array
	* @var array
	**/
	private $_optsL;
	private $_optsS;

	private $_curArgv = 0;	//Current index.
	private $_curArg;		//Current arg, if needed correction on real.

	/**
	* Construct
	*
	* @param	array	$opts. Options to set. {@see ::setOpts()}
	* @param 	Object(HuGetopt_settings)	$sets=null. Settings. If null - instanced default.
	**/
	public function __construct(array $opts, HuGetopt_settings $sets = null){
		$this->_sets = EMPTY_VAR($sets, new HuGetopt_settings);
		$this->setOpts($opts);
		$this->_nonopts = new HuArray();
	}#__c

	/**
	* Set allowed options to parse.
	* $opts array of options, which have format:
	*	array(
	*		's',	//Short option
	* 		'long',	//Long option
	* 		'mods'	//Modifiers
	*	)
	* Where mods mean:
	*	':' - Must have value.
	*	'::'- May have value.
	*
	* @param	array	$opts. Options to set.
	* @return	&$this
	* @Throws(VariableRequiredException)
	**/
	public function &setOpts(array $opts){
		$this->_optsS = $this->_optsL = $this->_opts = array();
		foreach (REQUIRED_VAR($opts) as $k => $opt){
			$this->_opts[$k]	= new HuGetopt_option(
				$this->sets()->HuGetopt_option_options
				,array(
					'OptS' 	=> $opt[0],
					'OptL'	=> @$opt[1],
					'Mod'	=> (string)@$opt[2]
				)
			);
			$this->_optsS[$opt[0]]	= $k;
			if (@$opt[1])
				$this->_optsL[$opt[1]]	= $k;
		}
		return $this;
	}#m setOpts

	/**
	* Return Object(HuGetopt_option) by its string 'w', or 'what'
	*
	* @param	string	$str
	* @param	char=a	$type. Possibles: s|l|a
	*	s - Short
	*	l - Long
	*	a and any other!!- ('a' by default) Make assumption by length $str - if strlen($str) == 1 - short, other - long
	* @return	&Object(HuGetopt_option)
	**/
	public function &getOptByStr($str, $type = 'a'){
		switch ($type){
			case 's':
				$type =& $this->_optsS;
				break;

			case 'l':
				$type =& $this->_optsL;
				break;

			default:
				if (1 == strlen($str)) $type =& $this->_optsS;
				else $type =& $this->_optsL;
				break;
		}
		return $this->_opts[ $type [$str] ];
	}#m getOptByStr

	/**
	* Main Horse!!! Doing most work.
	*
	* @return nothing
	* @Throws(HuGetoptArgumentRequiredException)
	**/
	public function parseArgs(){
		$this->_nonopts->push($this->currentArg());
		while($cArg = $this->nextArg()){
			if ( '--' == ($cArg) ){
				$this->_nonopts->pushArray(array_splice($this->nextArg(), $this->_curArgv));
				break;
			}

			if ( !($o = $this->isOpt($cArg)) ){
				$this->_nonopts->push($cArg);
				continue;
			}

		//reference. All modification - inplace.
		$o = $this->getOptByStr($o->Opt->{0}, $o->OptT->{0})->add($o);

			if('' == $o->Mod){
				$o->Val->_last_ = true;
			}
			else{//: or ::
				$optarg = $o->Val->_last_; //def
				if (
					!$o->Val->count()	//If NOT long option '=' form
					 and
					( ( false !== ($optarg = $this->nextArg())) and false === $this->isOpt($optarg) ) //And next NOT arg of current option
					){

						if('::' == $o->Mod){//Mandatory argument for option
							throw new HuGetoptArgumentRequiredException(new backtrace(), 'Option [' . $o->Opt->_last_ . '] requires argument!');
						}
					}
				$o->Val->_last_ = $optarg;
			}
		}
	}#m parseArgs

	/**
	* Move internal pointer to next arg, and return it.
	*
	* @return	string
	**/
	protected function nextArg(){
		if ($this->_curArg){
			$tmp = $this->_curArg;
			$this->_curArg = null;
			return $tmp;
		}
		elseif(++$this->_curArgv < sizeof($this->argv)){
			return $this->argv[$this->_curArgv];
		}
		else return false;
	}#m nextArg

	/**
	* Return current argument
	*
	* @return	string
	**/
	protected function currentArg(){
		if ($this->_curArg){
			$tmp = $this->_curArg;
			$this->_curArg = null;
			return $tmp;
		}
		else return $this->argv[$this->_curArgv];
	}#m currentArg

	/**
	* Return option or not $arg.
	*
	* @param	string	$arg. Usaly element of $argv
	* @return
	**/
	protected function isOpt($arg){
		return ( ($r =& $this->isShortOpt($arg)) ? $r : $this->isLongOpt($arg) );
	}#m isOpt

	/**
	* Check if arg is short option.
	*
	* @param	string	$arg. Arg-string to check
	* @return	false|Object(HuGetopt_option). In object ->Val NOT filled. For exception see description {@see ::isLongOpt()}
	**/
	public function isShortOpt($arg){
		$re = new RegExp_pcre(
			( $reg = '/^('.implode('|', RegExp_pcre::quote($this->sets()->start_short)).')('.implode('|', array_keys($this->_optsS)).')(.*)/s' ),
			$arg
		);
		$re->doMatch();

		if ($re->matchCount()){
			//Handle sequence of short options without optarguments -otfs.
			if ($o = $this->getOptByStr($re->match(2), 's') and (':' == $o->Mod or '::' == $o->Mod) ){//Have optarg
				return new HuGetopt_option(
					$this->sets()->HuGetopt_option_options
					,array(
						'Sep'	=> new HuArray($re->match(1)),
						'Opt'	=> new HuArray($re->match(2)),
						'Val'	=> new HuArray(('' !== (string)$re->match(3) ? $re->match(3) : $this->nextArg())),
						'OptT'	=> new HuArray('s')
					)
				);
			}
			else{//Not have optarg => $re->match(2) is continue of nonoptarg options.
				if ($re->match(3)) $this->_curArg = '-' . $re->match(3);
				return new HuGetopt_option(
					$this->sets()->HuGetopt_option_options
					,array(
						'Sep'	=> new HuArray($re->match(1)),
						'Opt'	=> new HuArray($re->match(2)),
						'Val'	=> new HuArray( array(null) ),
						'OptT'	=> new HuArray('s')
					)
				);
			}
		}
		return false;
	}#m isShortOpt

	/**
	* Check if arg is long option
	*	But, BE CAREFULL ->Val will be filled in only one case: See additional
	*	5 in main description of class HuGetopt about bug in php-cli to parse
	*	--longOpt='optarg' and --longOpt'optarg' forms of long options. In
	*	this form, when value of arg in same element of $argv - this it parsed
	*	and filled ->Val with this value, and ->= set to true. In other cases,
	*	next argument not got!
	*
	* @param	string	$arg. Arg-string to check
	* @return	false|Object(HuGetopt_option).
	**/
	public function isLongOpt($arg){
		$re = new RegExp_pcre(
			( $reg = '/^('.implode('|', RegExp_pcre::quote($this->sets()->alternative ? array_merge($this->sets()->start_long, $this->sets()->start_short) : $this->sets()->start_long)).')('.implode('|', array_keys($this->_optsL)).')(=|(?>\s*))(.*)/s' ),
			$arg
		);
		$re->doMatch();

		if ($re->matchCount()){
			return new HuGetopt_option(
				$this->sets()->HuGetopt_option_options
				,array(
					'Sep'	=> new HuArray($re->match(1)),
					'Opt'	=> new HuArray($re->match(2)),
					'='		=> new HuArray($re->match(3)),
					'Val'	=> new HuArray(($re->match(4) ? $re->match(4) : $this->nextArg())),
					'OptT'	=> new HuArray('l')
				)
			);
		}
		return false;
	}#m isLongOpt

	/**
	* Set new array of arguments
	*
	* @param array	$argv
	* @return	&$this
	**/
	public function &setArgv(array $argv){
		$this->argv = $argv;
		return $this;
	}#m setArgv

	/**
	* Short alias for {@see ::getOptByStr()}
	*
	* @param mixed	$opt
	* @param mixed('a')	$type
	* @return Object(HuGetopt_option)	$this->getOptByStr()
	**/
	public function get($opt, $type = 'a'){
		return $this->getOptByStr($opt, $type);
	}#m get

	/**
	* Object(HuArray) of NonOption arguments. all, 0 - by default is name of self script!
	*
	* @param integer(0)	$from. Start from element. Very usfull value 1, to ignore skript-name.
	* @return Object(HuArray).
	**/
	public function getNonOpts($from = 0){
		return $this->_nonopts->getSlice($from);
	}#m getNonOpts

	/**
	 * Return array known (defined for parsing, not parsed!) short options.
	 * 
	 * @return array
	 */
	public function getListShortOpts(){
		return $this->_optsS;
	}#m getListShortOpts

	/**
	 * Return array known (defined for parsing, not parsed!) long options.
	 *
	 * @return array
	 */
	public function getListLongOpts(){
		return $this->_optsL;
	}#m getListLongOpts

	/**
	* Idea (and method name) got from PEAR Console_getopt and adopted, modified.
	* Safely read the $argv PHP array across different PHP configurations.
	* Will take care on register_globals and register_argc_argv ini directives
	*
	* @return &this;
	* @Throw(VariableEmptyException)
	**/
	public function &readPHPArgv(){
		global $argv;

		if (is_array($argv)) $this->setArgv($argv);
		elseif (@is_array($_SERVER['argv'])) $this->setArgv($_SERVER['argv']);
		elseif (@is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) $this->setArgv($GLOBALS['HTTP_SERVER_VARS']['argv']);
		else throw new VariableEmptyException("readPHPArgv(): Could not read cmd args (register_argc_argv=Off?)");

		return $this;
	}#m readPHPargv
}#c HuGetopt
?><?
/**
* RegExp manupulation. PCRE-version.
*
* @package RegExp
* @version 2.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-02-11 13:41 ver 2.1 to 2.1.1
**/

 //Must be explicit, because there used eval-hack to define some subclas and it excluded from autoload

/**
* @uses RegExp_base
**/
class RegExp_pcre extends RegExp_base {
	const className = 'RegExp_pcre';

	/**
	* {@inheritdoc}
	**/
	public function test(){
		return ($this->matchCount = preg_match($this->RegExp, $this->sourceText));
	}#m test

	/**
	* {@inheritdoc}
	**/
	public static function quote($toQuote, $delimeter = '/'){
		if (is_array($toQuote)){
			array_walk_recursive($toQuote, create_function('&$v,&$k,&$d', '$v = preg_quote($v, $d);'), $delimeter);
			return $toQuote;
		}
		else return preg_quote($toQuote, $delimeter);
	}#m quote

	/**
	* {@inheritdoc}
	**/
	public function &doMatch($flags = null, $offset = null){
		$this->matchCount = preg_match($this->RegExp, $this->sourceText, $this->matches, $flags, $offset);
		$this->matchesValid = true;
		//Now must be called manually, if needed! $this->convertOffsetToChars($flags);
		return $this;
	}#m doMatch

	/**
	* {@inheritdoc}
	**/
	public function &doMatchAll($flags = null, $offset = null){
		$this->matchCount = preg_match_all($this->RegExp, $this->sourceText, $this->matches, $flags, $offset);
		$this->matchesValid = true;
		//Now must be called manually, if needed! $this->convertOffsetToChars($flags);
		return $this;
	}#m doMatchAll

	/**
	* Conversion bytes offsets to characters.
	*
	* Whith PREG_OFFSET_CAPTURE preg_match* returns bytes offset!!!! nor chars!!!!
	* So, recalculate it in chars is several methods:
	* 1) Using utf8_decode. See http://ru2.php.net/manual/ru/function.strlen.php
	*	comment "chernyshevsky at hotmail dot com"
	* 2) And using mb_strlen http://ru2.php.net/manual/ru/function.preg-match.php comment "chuckie"
	*
	* I using combination of its. And it independent of the presence mbstring extension!
	*
	* @param	int(PREG_OFFSET_CAPTURE)	$flags Flags which was used in previous operation.
	* @return	nothing
	*/
	public final function convertOffsetToChars($flags = PREG_OFFSET_CAPTURE){
	/*
	* A recalculate offset may be done by many ways. See test/strlen_speed_tests.php for more detailes.
	* Short conclusion from this tests are:
	* 1) It is very-very slowly operations, so
	*	1.1) We refusal to do it in any time. This must be called manually if you want (and it also may need binary offset meantime too!!!).
	*	1.2) For that, change access type to public
	* 2) To case when it is needed second conclusion - the most fast way is mb_strlen, but it is not included in core PHP...
	*	2.1) If available, use mb_strlen
	*	2.2) For capability, provide fallback to strlen(utf8_decode(...)) (2nd place of speed)
	**/
		if ($this->matchCount and ($flags & PREG_OFFSET_CAPTURE)){
			if (function_exists('mb_strlen')){
				$func_strlen = create_function('$str', 'return mb_strlen($str, \'UTF-8\');');
			}
			else{//Fallback
				$func_strlen = create_function('$str', 'return strlen(utf8_decode($str));');
			}

			foreach($this->matches as &$match){
				foreach ($match as &$m){
					$m[1] = $func_strlen(substr($this->sourceText, 0, $m[1]));
				}
			}
		}
	}#m convertOffsetToChars

	/**
	* {@inheritdoc}
	* Description see {@link http://php.net/preg_replace}
	* Results cached, so fill free invoke it several times without overhead of replace.
	*
	* @param int	$limit If present - replace only $limit occurrences. In default case of -1 - replace ALL.
	* @return array Results of replace. Cached.
	**/
	public function replace($limit = -1){
		if (!$this->replaceValid){
			$this->replaceRes = preg_replace($this->RegExp, $this->replaceTo, $this->sourceText, $limit);
			$this->replaceValid = true;
		}
		return $this->replaceRes;
	}#m replace

	/**
	* Split by regexp.
	*
	* @since Version 2.1.1
	*
	* @param int(-1)	$limit If present - replace only $limit occurrences. In default case of -1 - replace ALL.
	* @param int(null)	$flags {@link http://php.net/preg-split} for detailed descriptions of $flags.
	* @return &$this
	**/
	public function &split($limit = -1, $flags = null){
		$this->matches = preg_split($this->RegExp, $this->sourceText, $limit, $flags);
		$this->matchesValid = true;
		return $this;
	}#m split
}#c RegExp_pcre
?><?php
/**
* RegExp manupulation.
*
* @package RegExp
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2014, Pahan-Hubbitus (Pavel Alexeev)
* @created 21.06.2014 14:56:24
**/

/**
* After PHP 5.2.0 get error defining abstract static methods: Strict standards: Static function RegExp_base_base::quote() should not be abstract :
* http://www.php.net//manual/ru/migration52.incompatible.php , So introduce interface as workaround ( as per http://stackoverflow.com/questions/13494807/strict-standards-static-function-modeltablestruct-should-not-be-abstract-in )
**/
interface IRegExp{
	/**
	* Quote given string or each (recursive) string in array.
	*
	* @param	string|array	$toQuote
	* @param	string='/'	$delimiter. Chars to addition escape. Usaly (and default) char start and end of regexp.
	* @return	string|array	Same type as given.
	**/
	public static function quote($toQuote, $delimeter = '/');
}
?><?php
/*******************************************************************************
* Software: UFPDF, Unicode Free PDF generator                                  *
* Version:  0.1                                                                *
*           based on FPDF 1.52 by Olivier PLATHEY                              *
* Date:     2004-09-01                                                         *
* Author:   Steven Wittens <steven@acko.net>                                   *
* License:  GPL                                                                *
*                                                                              *
* UFPDF is a modification of FPDF to support Unicode through UTF-8.            *
*                                                                              *
*******************************************************************************/

if(!class_exists('UFPDF'))
{
define('UFPDF_VERSION','0.1');

include_once 'fpdf.php';

class UFPDF extends FPDF
{

/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
function UFPDF($orientation='P',$unit='mm',$format='A4')
{
  FPDF::FPDF($orientation, $unit, $format);
}

function GetStringWidth($s)
{
  //Get width of a string in the current font
  $s = (string)$s;
  $codepoints=$this->utf8_to_codepoints($s);
  $cw=&$this->CurrentFont['cw'];
  $w=0;
  foreach($codepoints as $cp)
    $w+=$cw[$cp];
  return $w*$this->FontSize/1000;
}

function AddFont($family,$style='',$file='')
{
  //Add a TrueType or Type1 font
  $family=strtolower($family);
  if($family=='arial')
    $family='helvetica';
  $style=strtoupper($style);
  if($style=='IB')
    $style='BI';
  if(isset($this->fonts[$family.$style]))
    $this->Error('Font already added: '.$family.' '.$style);
  if($file=='')
    $file=str_replace(' ','',$family).strtolower($style).'.php';
  if(defined('FPDF_FONTPATH'))
    $file=FPDF_FONTPATH.$file;
  include($file);
  if(!isset($name))
    $this->Error('Could not include font definition file');
  $i=count($this->fonts)+1;
  $this->fonts[$family.$style]=array('i'=>$i,'type'=>$type,'name'=>$name,'desc'=>$desc,'up'=>$up,'ut'=>$ut,'cw'=>$cw,'file'=>$file,'ctg'=>$ctg);
  if($file)
  {
    if($type=='TrueTypeUnicode')
      $this->FontFiles[$file]=array('length1'=>$originalsize);
    else
      $this->FontFiles[$file]=array('length1'=>$size1,'length2'=>$size2);
  }
}

function Text($x,$y,$txt)
{
  //Output a string
  $s=sprintf('BT %.2f %.2f Td %s Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escapetext($txt));
  if($this->underline and $txt!='')
    $s.=' '.$this->_dounderline($x,$y,$this->GetStringWidth($txt),$txt);
  if($this->ColorFlag)
    $s='q '.$this->TextColor.' '.$s.' Q';
  $this->_out($s);
}

function AcceptPageBreak()
{
  //Accept automatic page break or not
  return $this->AutoPageBreak;
}

function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
{
  //Output a cell
  $k=$this->k;
  if($this->y+$h>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak())
  {
    //Automatic page break
    $x=$this->x;
    $ws=$this->ws;
    if($ws>0)
    {
      $this->ws=0;
      $this->_out('0 Tw');
    }
    $this->AddPage($this->CurOrientation);
    $this->x=$x;
    if($ws>0)
    {
      $this->ws=$ws;
      $this->_out(sprintf('%.3f Tw',$ws*$k));
    }
  }
  if($w==0)
    $w=$this->w-$this->rMargin-$this->x;
  $s='';
  if($fill==1 or $border==1)
  {
    if($fill==1)
      $op=($border==1) ? 'B' : 'f';
    else
      $op='S';
    $s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
  }
  if(is_string($border))
  {
    $x=$this->x;
    $y=$this->y;
    if(is_int(strpos($border,'L')))
      $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
    if(is_int(strpos($border,'T')))
      $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
    if(is_int(strpos($border,'R')))
      $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    if(is_int(strpos($border,'B')))
      $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
  }
  if($txt!='')
  {
    $width = $this->GetStringWidth($txt);
    if($align=='R')
      $dx=$w-$this->cMargin-$width;
    elseif($align=='C')
      $dx=($w-$width)/2;
    else
      $dx=$this->cMargin;
    if($this->ColorFlag)
      $s.='q '.$this->TextColor.' ';
    $txtstring=$this->_escapetext($txt);
    $s.=sprintf('BT %.2f %.2f Td %s Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txtstring);
    if($this->underline)
      $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$width,$txt);
    if($this->ColorFlag)
      $s.=' Q';
    if($link)
      $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$width,$this->FontSize,$link);
  }
  if($s)
    $this->_out($s);
  $this->lasth=$h;
  if($ln>0)
  {
    //Go to next line
    $this->y+=$h;
    if($ln==1)
      $this->x=$this->lMargin;
  }
  else
    $this->x+=$w;
}

/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/

function _puttruetypeunicode($font) {
  //Type0 Font
  $this->_newobj();
  $this->_out('<</Type /Font');
  $this->_out('/Subtype /Type0');
  $this->_out('/BaseFont /'. $font['name'] .'-UCS');
  $this->_out('/Encoding /Identity-H');
  $this->_out('/DescendantFonts ['. ($this->n + 1) .' 0 R]');
  $this->_out('>>');
  $this->_out('endobj');

  //CIDFont
  $this->_newobj();
  $this->_out('<</Type /Font');
  $this->_out('/Subtype /CIDFontType2');
  $this->_out('/BaseFont /'. $font['name']);
  $this->_out('/CIDSystemInfo <</Registry (Adobe) /Ordering (UCS) /Supplement 0>>');
  $this->_out('/FontDescriptor '. ($this->n + 1) .' 0 R');
  $c = 0;
  foreach ($font['cw'] as $i => $w) {
    @$widths .= $i .' ['. $w.'] ';
  }
  $this->_out('/W ['. $widths .']');
  $this->_out('/CIDToGIDMap '. ($this->n + 2) .' 0 R');
  $this->_out('>>');
  $this->_out('endobj');

  //Font descriptor
  $this->_newobj();
  $this->_out('<</Type /FontDescriptor');
  $this->_out('/FontName /'.$font['name']);
  foreach ($font['desc'] as $k => $v) {
    @$s .= ' /'. $k .' '. $v;
  }
  if ($font['file']) {
		$s .= ' /FontFile2 '. $this->FontFiles[$font['file']]['n'] .' 0 R';
  }
  $this->_out($s);
  $this->_out('>>');
  $this->_out('endobj');

  //Embed CIDToGIDMap
  $this->_newobj();
  if(defined('FPDF_FONTPATH'))
    $file=FPDF_FONTPATH.$font['ctg'];
  else
    $file=$font['ctg'];
  $fcont = file_get_contents($file, FILE_USE_INCLUDE_PATH);
  $size = strlen($fcont);
  if(!$size)
    $this->Error('Font file not found');
  $this->_out('<</Length '.$size);
	if(substr($file,-2) == '.z')
    $this->_out('/Filter /FlateDecode');
  $this->_out('>>');
  $this->_putstream($fcont);
  $this->_out('endobj');
}

function _dounderline($x,$y,$width,$txt)
{
  //Underline text
  $up=$this->CurrentFont['up'];
  $ut=$this->CurrentFont['ut'];
  $w=$width+$this->ws*substr_count($txt,' ');
  return sprintf('%.2f %.2f %.2f %.2f re f',$x*$this->k,($this->h-($y-$up/1000*$this->FontSize))*$this->k,$w*$this->k,-$ut/1000*$this->FontSizePt);
}

function _textstring($s)
{
  //Convert to UTF-16BE
  $s = $this->utf8_to_utf16be($s);
  //Escape necessary characters
  return '('. strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\')) .')';
}

function _escapetext($s)
{
  //Convert to UTF-16BE
  $s = $this->utf8_to_utf16be($s, false);
  //Escape necessary characters
  return '('. strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\')) .')';
}

function _putinfo()
{
	$this->_out('/Producer '.$this->_textstring('UFPDF '. UFPDF_VERSION));
	if(!empty($this->title))
		$this->_out('/Title '.$this->_textstring($this->title));
	if(!empty($this->subject))
		$this->_out('/Subject '.$this->_textstring($this->subject));
	if(!empty($this->author))
		$this->_out('/Author '.$this->_textstring($this->author));
	if(!empty($this->keywords))
		$this->_out('/Keywords '.$this->_textstring($this->keywords));
	if(!empty($this->creator))
		$this->_out('/Creator '.$this->_textstring($this->creator));
	$this->_out('/CreationDate '.$this->_textstring('D:'.date('YmdHis')));
}

// UTF-8 to UTF-16BE conversion.
// Correctly handles all illegal UTF-8 sequences.
function utf8_to_utf16be(&$txt, $bom = true) {
  $l = strlen($txt);
  $out = $bom ? "\xFE\xFF" : '';
  for ($i = 0; $i < $l; ++$i) {
    $c = ord($txt{$i});
    // ASCII
    if ($c < 0x80) {
      $out .= "\x00". $txt{$i};
    }
    // Lost continuation byte
    else if ($c < 0xC0) {
      $out .= "\xFF\xFD";
      continue;
    }
    // Multibyte sequence leading byte
    else {
      if ($c < 0xE0) {
        $s = 2;
      }
      else if ($c < 0xF0) {
        $s = 3;
      }
      else if ($c < 0xF8) {
        $s = 4;
      }
      // 5/6 byte sequences not possible for Unicode.
      else {
        $out .= "\xFF\xFD";
        while (ord($txt{$i + 1}) >= 0x80 && ord($txt{$i + 1}) < 0xC0) { ++$i; }
        continue;
      }
      
      $q = array($c);
      // Fetch rest of sequence
      while (isset($txt{$i + 1}) && ord($txt{$i + 1}) >= 0x80 && ord($txt{$i + 1}) < 0xC0) { ++$i; $q[] = ord($txt{$i}); }
      
      // Check length
      if (count($q) != $s) {
        $out .= "\xFF\xFD";        
        continue;
      }
      
      switch ($s) {
        case 2:
          $cp = (($q[0] ^ 0xC0) << 6) | ($q[1] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x80) {
            $out .= "\xFF\xFD";        
          }
          else {
            $out .= chr($cp >> 8);
            $out .= chr($cp & 0xFF);
          }
          continue;

        case 3:
          $cp = (($q[0] ^ 0xE0) << 12) | (($q[1] ^ 0x80) << 6) | ($q[2] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x800) {
            $out .= "\xFF\xFD";        
          }
          // Check for UTF-8 encoded surrogates (caused by a bad UTF-8 encoder)
          else if ($c > 0xD800 && $c < 0xDFFF) {
            $out .= "\xFF\xFD";
          }
          else {
            $out .= chr($cp >> 8);
            $out .= chr($cp & 0xFF);
          }
          continue;

        case 4:
          $cp = (($q[0] ^ 0xF0) << 18) | (($q[1] ^ 0x80) << 12) | (($q[2] ^ 0x80) << 6) | ($q[3] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x10000) {
            $out .= "\xFF\xFD";
          }
          // Outside of the Unicode range
          else if ($cp >= 0x10FFFF) {
            $out .= "\xFF\xFD";            
          }
          else {
            // Use surrogates
            $cp -= 0x10000;
            $s1 = 0xD800 | ($cp >> 10);
            $s2 = 0xDC00 | ($cp & 0x3FF);
            
            $out .= chr($s1 >> 8);
            $out .= chr($s1 & 0xFF);
            $out .= chr($s2 >> 8);
            $out .= chr($s2 & 0xFF);
          }
          continue;
      }
    }
  }
  return $out;
}

// UTF-8 to codepoint array conversion.
// Correctly handles all illegal UTF-8 sequences.
function utf8_to_codepoints(&$txt) {
  $l = strlen($txt);
  $out = array();
  for ($i = 0; $i < $l; ++$i) {
    $c = ord($txt{$i});
    // ASCII
    if ($c < 0x80) {
      $out[] = ord($txt{$i});
    }
    // Lost continuation byte
    else if ($c < 0xC0) {
      $out[] = 0xFFFD;
      continue;
    }
    // Multibyte sequence leading byte
    else {
      if ($c < 0xE0) {
        $s = 2;
      }
      else if ($c < 0xF0) {
        $s = 3;
      }
      else if ($c < 0xF8) {
        $s = 4;
      }
      // 5/6 byte sequences not possible for Unicode.
      else {
        $out[] = 0xFFFD;
        while (ord($txt{$i + 1}) >= 0x80 && ord($txt{$i + 1}) < 0xC0) { ++$i; }
        continue;
      }
      
      $q = array($c);
      // Fetch rest of sequence
      while (ord($txt{$i + 1}) >= 0x80 && ord($txt{$i + 1}) < 0xC0) { ++$i; $q[] = ord($txt{$i}); }
      
      // Check length
      if (count($q) != $s) {
        $out[] = 0xFFFD;
        continue;
      }
      
      switch ($s) {
        case 2:
          $cp = (($q[0] ^ 0xC0) << 6) | ($q[1] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x80) {
            $out[] = 0xFFFD;
          }
          else {
            $out[] = $cp;
          }
          continue;

        case 3:
          $cp = (($q[0] ^ 0xE0) << 12) | (($q[1] ^ 0x80) << 6) | ($q[2] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x800) {
            $out[] = 0xFFFD;
          }
          // Check for UTF-8 encoded surrogates (caused by a bad UTF-8 encoder)
          else if ($c > 0xD800 && $c < 0xDFFF) {
            $out[] = 0xFFFD;
          }
          else {
            $out[] = $cp;
          }
          continue;

        case 4:
          $cp = (($q[0] ^ 0xF0) << 18) | (($q[1] ^ 0x80) << 12) | (($q[2] ^ 0x80) << 6) | ($q[3] ^ 0x80);
          // Overlong sequence
          if ($cp < 0x10000) {
            $out[] = 0xFFFD;
          }
          // Outside of the Unicode range
          else if ($cp >= 0x10FFFF) {
            $out[] = 0xFFFD;
          }
          else {
            $out[] = $cp;
          }
          continue;
      }
    }
  }
  return $out;
}

//End of class
}

}
?>
<?php
//
//  FPDI - Version 1.3.1
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

if (!defined ('PDF_TYPE_NULL'))
    define ('PDF_TYPE_NULL', 0);
if (!defined ('PDF_TYPE_NUMERIC'))
    define ('PDF_TYPE_NUMERIC', 1);
if (!defined ('PDF_TYPE_TOKEN'))
    define ('PDF_TYPE_TOKEN', 2);
if (!defined ('PDF_TYPE_HEX'))
    define ('PDF_TYPE_HEX', 3);
if (!defined ('PDF_TYPE_STRING'))
    define ('PDF_TYPE_STRING', 4);
if (!defined ('PDF_TYPE_DICTIONARY'))
    define ('PDF_TYPE_DICTIONARY', 5);
if (!defined ('PDF_TYPE_ARRAY'))
    define ('PDF_TYPE_ARRAY', 6);
if (!defined ('PDF_TYPE_OBJDEC'))
    define ('PDF_TYPE_OBJDEC', 7);
if (!defined ('PDF_TYPE_OBJREF'))
    define ('PDF_TYPE_OBJREF', 8);
if (!defined ('PDF_TYPE_OBJECT'))
    define ('PDF_TYPE_OBJECT', 9);
if (!defined ('PDF_TYPE_STREAM'))
    define ('PDF_TYPE_STREAM', 10);
if (!defined ('PDF_TYPE_BOOLEAN'))
    define ('PDF_TYPE_BOOLEAN', 11);
if (!defined ('PDF_TYPE_REAL'))
    define ('PDF_TYPE_REAL', 12);
    


if (!class_exists('pdf_parser')) {
    
class pdf_parser {
	
	/**
     * Filename
     * @var string
     */
    var $filename;
    
    /**
     * File resource
     * @var resource
     */
    var $f;
    
    /**
     * PDF Context
     * @var object pdf_context-Instance
     */
    var $c;
    
    /**
     * xref-Data
     * @var array
     */
    var $xref;

    /**
     * root-Object
     * @var array
     */
    var $root;
	
    /**
     * PDF version of the loaded document
     * @var string
     */
    var $pdfVersion;
    
    /**
     * Constructor
     *
     * @param string $filename  Source-Filename
     */
	function pdf_parser($filename) {
        $this->filename = $filename;
        
        $this->f = @fopen($this->filename, 'rb');

        if (!$this->f)
            $this->error(sprintf('Cannot open %s !', $filename));

        $this->getPDFVersion();

        $this->c = new pdf_context($this->f);
        
        // Read xref-Data
        $this->xref = array();
        $this->pdf_read_xref($this->xref, $this->pdf_find_xref());
        
        // Check for Encryption
        $this->getEncryption();

        // Read root
        $this->pdf_read_root();
    }
    
    /**
     * Close the opened file
     */
    function closeFile() {
    	if (isset($this->f) && is_resource($this->f)) {
    	    fclose($this->f);	
    		unset($this->f);
    	}	
    }
    
    /**
     * Print Error and die
     *
     * @param string $msg  Error-Message
     */
    function error($msg) {
    	die('<b>PDF-Parser Error:</b> '.$msg);	
    }
    
    /**
     * Check Trailer for Encryption
     */
    function getEncryption() {
        if (isset($this->xref['trailer'][1]['/Encrypt'])) {
            $this->error('File is encrypted!');
        }
    }
    
	/**
     * Find/Return /Root
     *
     * @return array
     */
    function pdf_find_root() {
        if ($this->xref['trailer'][1]['/Root'][0] != PDF_TYPE_OBJREF) {
            $this->error('Wrong Type of Root-Element! Must be an indirect reference');
        }
        
        return $this->xref['trailer'][1]['/Root'];
    }

    /**
     * Read the /Root
     */
    function pdf_read_root() {
        // read root
        $this->root = $this->pdf_resolve_object($this->c, $this->pdf_find_root());
    }
    
    /**
     * Get PDF-Version
     *
     * And reset the PDF Version used in FPDI if needed
     */
    function getPDFVersion() {
        fseek($this->f, 0);
        preg_match('/\d\.\d/',fread($this->f,16),$m);
        if (isset($m[0]))
            $this->pdfVersion = $m[0];
        return $this->pdfVersion;
    }
    
    /**
     * Find the xref-Table
     */
    function pdf_find_xref() {
       	$toRead = 1500;
                
        $stat = fseek ($this->f, -$toRead, SEEK_END);
        if ($stat === -1) {
            fseek ($this->f, 0);
        }
       	$data = fread($this->f, $toRead);
        
        $pos = strlen($data) - strpos(strrev($data), strrev('startxref')); 
        $data = substr($data, $pos);
        
        if (!preg_match('/\s*(\d+).*$/s', $data, $matches)) {
            $this->error('Unable to find pointer to xref table');
    	}

    	return (int) $matches[1];
    }

    /**
     * Read xref-table
     *
     * @param array $result Array of xref-table
     * @param integer $offset of xref-table
     */
    function pdf_read_xref(&$result, $offset) {
        
        fseek($this->f, $o_pos = $offset-20); // set some bytes backwards to fetch errorious docs
            
        $data = fread($this->f, 100);
        
        $xrefPos = strrpos($data, 'xref');

        if ($xrefPos === false) {
            fseek($this->f, $offset);
            $c = new pdf_context($this->f);
            $xrefStreamObjDec = $this->pdf_read_value($c);
            
            if (is_array($xrefStreamObjDec) && isset($xrefStreamObjDec[0]) && $xrefStreamObjDec[0] == PDF_TYPE_OBJDEC) {
                $this->error(sprintf('This document (%s) probably uses a compression technique which is not supported by the free parser shipped with FPDI.', $this->filename));
            } else {            
                $this->error('Unable to find xref table.');
            }
        }
        
        if (!isset($result['xref_location'])) {
            $result['xref_location'] = $o_pos+$xrefPos;
            $result['max_object'] = 0;
    	}

    	$cylces = -1;
        $bytesPerCycle = 100;
        
    	fseek($this->f, $o_pos = $o_pos+$xrefPos+4); // set the handle directly after the "xref"-keyword
        $data = fread($this->f, $bytesPerCycle);
        
        while (($trailerPos = strpos($data, 'trailer', max($bytesPerCycle*$cylces++, 0))) === false && !feof($this->f)) {
            $data .= fread($this->f, $bytesPerCycle);
        }
        
        if ($trailerPos === false) {
            $this->error('Trailer keyword not found after xref table');
        }
        
        $data = substr($data, 0, $trailerPos);
        
        // get Line-Ending
        preg_match_all("/(\r\n|\n|\r)/", substr($data, 0, 100), $m); // check the first 100 bytes for linebreaks

        $differentLineEndings = count(array_unique($m[0]));
        if ($differentLineEndings > 1) {
            $lines = preg_split("/(\r\n|\n|\r)/", $data, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $lines = explode($m[0][1], $data);
        }
        
        $data = $differentLineEndings = $m = null;
        unset($data, $differentLineEndings, $m);
        
        $linesCount = count($lines);
        
        $start = 1;
        
        for ($i = 0; $i < $linesCount; $i++) {
            $line = trim($lines[$i]);
            if ($line) {
                $pieces = explode(' ', $line);
                $c = count($pieces);
                switch($c) {
                    case 2:
                        $start = (int)$pieces[0];
                        $end   = $start+(int)$pieces[1];
                        if ($end > $result['max_object'])
                            $result['max_object'] = $end;
                        break;
                    case 3:
                        if (!isset($result['xref'][$start]))
                            $result['xref'][$start] = array();
                        
                        if (!array_key_exists($gen = (int) $pieces[1], $result['xref'][$start])) {
                	        $result['xref'][$start][$gen] = $pieces[2] == 'n' ? (int) $pieces[0] : null;
                	    }
                        $start++;
                        break;
                    default:
                        $this->error('Unexpected data in xref table');
                }
            }
        }
        
        $lines = $pieces = $line = $start = $end = $gen = null;
        unset($lines, $pieces, $line, $start, $end, $gen);
        
        fseek($this->f, $o_pos+$trailerPos+7);
        
        $c = new pdf_context($this->f);
	    $trailer = $this->pdf_read_value($c);
	    
	    $c = null;
	    unset($c);
	    
	    if (!isset($result['trailer'])) {
            $result['trailer'] = $trailer;          
	    }
	    
	    if (isset($trailer[1]['/Prev'])) {
	        $this->pdf_read_xref($result, $trailer[1]['/Prev'][1]);
	    } 
	    
	    $trailer = null;
	    unset($trailer);
        
        return true;
    }
    
    /**
     * Reads an Value
     *
     * @param object $c pdf_context
     * @param string $token a Token
     * @return mixed
     */
    function pdf_read_value(&$c, $token = null) {
    	if (is_null($token)) {
    	    $token = $this->pdf_read_token($c);
    	}
    	
        if ($token === false) {
    	    return false;
    	}

    	switch ($token) {
            case	'<':
    			// This is a hex string.
    			// Read the value, then the terminator

                $pos = $c->offset;

    			while(1) {

                    $match = strpos ($c->buffer, '>', $pos);
				
    				// If you can't find it, try
    				// reading more data from the stream

    				if ($match === false) {
    					if (!$c->increase_length()) {
    						return false;
    					} else {
                        	continue;
                    	}
    				}

    				$result = substr ($c->buffer, $c->offset, $match - $c->offset);
    				$c->offset = $match + 1;
    				
    				return array (PDF_TYPE_HEX, $result);
                }
                
                break;
    		case	'<<':
    			// This is a dictionary.

    			$result = array();

    			// Recurse into this function until we reach
    			// the end of the dictionary.
    			while (($key = $this->pdf_read_token($c)) !== '>>') {
    				if ($key === false) {
    					return false;
    				}
    				
    				if (($value =   $this->pdf_read_value($c)) === false) {
    					return false;
    				}
    				
    				// Catch missing value
    				if ($value[0] == PDF_TYPE_TOKEN && $value[1] == '>>') {
    				    $result[$key] = array(PDF_TYPE_NULL);
    				    break;
    				}
    				
    				$result[$key] = $value;
    			}
				
    			return array (PDF_TYPE_DICTIONARY, $result);

    		case	'[':
    			// This is an array.

    			$result = array();

    			// Recurse into this function until we reach
    			// the end of the array.
    			while (($token = $this->pdf_read_token($c)) !== ']') {
                    if ($token === false) {
    					return false;
    				}
					
    				if (($value = $this->pdf_read_value($c, $token)) === false) {
                        return false;
    				}
					
    				$result[] = $value;
    			}
    			
                return array (PDF_TYPE_ARRAY, $result);

    		case	'('		:
                // This is a string
                $pos = $c->offset;
                
                $openBrackets = 1;
    			do {
                    for (; $openBrackets != 0 && $pos < $c->length; $pos++) {
                        switch (ord($c->buffer[$pos])) {
                            case 0x28: // '('
                                $openBrackets++;
                                break;
                            case 0x29: // ')'
                                $openBrackets--;
                                break;
                            case 0x5C: // backslash
                                $pos++;
                        }
                    }
    			} while($openBrackets != 0 && $c->increase_length());
    			
    			$result = substr($c->buffer, $c->offset, $pos - $c->offset - 1);
    			$c->offset = $pos;
    			
    			return array (PDF_TYPE_STRING, $result);

            case 'stream':
            	$o_pos = ftell($c->file)-strlen($c->buffer);
		        $o_offset = $c->offset;
		        
		        $c->reset($startpos = $o_pos + $o_offset);
		        
		        $e = 0; // ensure line breaks in front of the stream
		        if ($c->buffer[0] == chr(10) || $c->buffer[0] == chr(13))
		        	$e++;
		        if ($c->buffer[1] == chr(10) && $c->buffer[0] != chr(10))
		        	$e++;
		        
		        if ($this->actual_obj[1][1]['/Length'][0] == PDF_TYPE_OBJREF) {
		        	$tmp_c = new pdf_context($this->f);
		        	$tmp_length = $this->pdf_resolve_object($tmp_c,$this->actual_obj[1][1]['/Length']);
		        	$length = $tmp_length[1][1];
		        } else {
		        	$length = $this->actual_obj[1][1]['/Length'][1];	
		        }
		        
		        if ($length > 0) {
    		        $c->reset($startpos+$e,$length);
    		        $v = $c->buffer;
		        } else {
		            $v = '';   
		        }
		        $c->reset($startpos+$e+$length+9); // 9 = strlen("endstream")
		        
		        return array(PDF_TYPE_STREAM, $v);
		        
	        default	:
            	if (is_numeric ($token)) {
                    // A numeric token. Make sure that
    				// it is not part of something else.
    				if (($tok2 = $this->pdf_read_token ($c)) !== false) {
                        if (is_numeric ($tok2)) {

    						// Two numeric tokens in a row.
    						// In this case, we're probably in
    						// front of either an object reference
    						// or an object specification.
    						// Determine the case and return the data
    						if (($tok3 = $this->pdf_read_token ($c)) !== false) {
                                switch ($tok3) {
    								case	'obj'	:
                                        return array (PDF_TYPE_OBJDEC, (int) $token, (int) $tok2);
    								case	'R'		:
    									return array (PDF_TYPE_OBJREF, (int) $token, (int) $tok2);
    							}
    							// If we get to this point, that numeric value up
    							// there was just a numeric value. Push the extra
    							// tokens back into the stack and return the value.
    							array_push ($c->stack, $tok3);
    						}
    					}

    					array_push ($c->stack, $tok2);
    				}

    				if ($token === (string)((int)$token))
        				return array (PDF_TYPE_NUMERIC, (int)$token);
    				else 
    					return array (PDF_TYPE_REAL, (float)$token);
    			} else if ($token == 'true' || $token == 'false') {
                    return array (PDF_TYPE_BOOLEAN, $token == 'true');
    			} else if ($token == 'null') {
    			   return array (PDF_TYPE_NULL);
    			} else {
                    // Just a token. Return it.
    				return array (PDF_TYPE_TOKEN, $token);
    			}
         }
    }
    
    /**
     * Resolve an object
     *
     * @param object $c pdf_context
     * @param array $obj_spec The object-data
     * @param boolean $encapsulate Must set to true, cause the parsing and fpdi use this method only without this para
     */
    function pdf_resolve_object(&$c, $obj_spec, $encapsulate = true) {
        // Exit if we get invalid data
    	if (!is_array($obj_spec)) {
            $ret = false;
    	    return $ret;
    	}

    	if ($obj_spec[0] == PDF_TYPE_OBJREF) {

    		// This is a reference, resolve it
    		if (isset($this->xref['xref'][$obj_spec[1]][$obj_spec[2]])) {

    			// Save current file position
    			// This is needed if you want to resolve
    			// references while you're reading another object
    			// (e.g.: if you need to determine the length
    			// of a stream)

    			$old_pos = ftell($c->file);

    			// Reposition the file pointer and
    			// load the object header.
				
    			$c->reset($this->xref['xref'][$obj_spec[1]][$obj_spec[2]]);

    			$header = $this->pdf_read_value($c);

    			if ($header[0] != PDF_TYPE_OBJDEC || $header[1] != $obj_spec[1] || $header[2] != $obj_spec[2]) {
    				$this->error("Unable to find object ({$obj_spec[1]}, {$obj_spec[2]}) at expected location");
    			}

    			// If we're being asked to store all the information
    			// about the object, we add the object ID and generation
    			// number for later use
				$result = array();
				$this->actual_obj =& $result;
    			if ($encapsulate) {
    				$result = array (
    					PDF_TYPE_OBJECT,
    					'obj' => $obj_spec[1],
    					'gen' => $obj_spec[2]
    				);
    			} 

    			// Now simply read the object data until
    			// we encounter an end-of-object marker
    			while(1) {
                    $value = $this->pdf_read_value($c);
					if ($value === false || count($result) > 4) {
						// in this case the parser coudn't find an endobj so we break here
						break;
    				}

    				if ($value[0] == PDF_TYPE_TOKEN && $value[1] === 'endobj') {
    					break;
    				}

                    $result[] = $value;
    			}

    			$c->reset($old_pos);

                if (isset($result[2][0]) && $result[2][0] == PDF_TYPE_STREAM) {
                    $result[0] = PDF_TYPE_STREAM;
                }

    			return $result;
    		}
    	} else {
    		return $obj_spec;
    	}
    }

    
    
    /**
     * Reads a token from the file
     *
     * @param object $c pdf_context
     * @return mixed
     */
    function pdf_read_token(&$c)
    {
    	// If there is a token available
    	// on the stack, pop it out and
    	// return it.

    	if (count($c->stack)) {
    		return array_pop($c->stack);
    	}

    	// Strip away any whitespace

    	do {
    		if (!$c->ensure_content()) {
    			return false;
    		}
    		$c->offset += strspn($c->buffer, " \n\r\t", $c->offset);
    	} while ($c->offset >= $c->length - 1);

    	// Get the first character in the stream

    	$char = $c->buffer[$c->offset++];

    	switch ($char) {

    		case '[':
    		case ']':
    		case '(':
    		case ')':
    		
    			// This is either an array or literal string
    			// delimiter, Return it

    			return $char;

    		case '<':
    		case '>':

    			// This could either be a hex string or
    			// dictionary delimiter. Determine the
    			// appropriate case and return the token

    			if ($c->buffer[$c->offset] == $char) {
    				if (!$c->ensure_content()) {
    				    return false;
    				}
    				$c->offset++;
    				return $char . $char;
    			} else {
    				return $char;
    			}

			case '%':
			    
			    // This is a comment - jump over it!
			    
                $pos = $c->offset;
    			while(1) {
    			    $match = preg_match("/(\r\n|\r|\n)/", $c->buffer, $m, PREG_OFFSET_CAPTURE, $pos);
                    if ($match === 0) {
    					if (!$c->increase_length()) {
    						return false;
    					} else {
                        	continue;
                    	}
    				}

    				$c->offset = $m[0][1]+strlen($m[0][0]);
    				
    				return $this->pdf_read_token($c);
                }
                
			default:

    			// This is "another" type of token (probably
    			// a dictionary entry or a numeric value)
    			// Find the end and return it.

    			if (!$c->ensure_content()) {
    				return false;
    			}

    			while(1) {

    				// Determine the length of the token

    				$pos = strcspn($c->buffer, " %[]<>()\r\n\t/", $c->offset);
    				
    				if ($c->offset + $pos <= $c->length - 1) {
    					break;
    				} else {
    					// If the script reaches this point,
    					// the token may span beyond the end
    					// of the current buffer. Therefore,
    					// we increase the size of the buffer
    					// and try again--just to be safe.

    					$c->increase_length();
    				}
    			}

    			$result = substr($c->buffer, $c->offset - 1, $pos + 1);

    			$c->offset += $pos;
    			return $result;
    	}
    }
}

}
?><?php
//
//  FPDI - Version 1.3.1
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

class pdf_context {

    /**
     * Modi
     *
     * @var integer 0 = file | 1 = string
     */
    var $_mode = 0;
    
	var $file;
	var $buffer;
	var $offset;
	var $length;

	var $stack;

	// Constructor

	function pdf_context(&$f) {
		$this->file =& $f;
		if (is_string($this->file))
		    $this->_mode = 1;
		$this->reset();
	}

	// Optionally move the file
	// pointer to a new location
	// and reset the buffered data

	function reset($pos = null, $l = 100) {
	    if ($this->_mode == 0) {
        	if (!is_null ($pos)) {
    			fseek ($this->file, $pos);
    		}
    
    		$this->buffer = $l > 0 ? fread($this->file, $l) : '';
    		$this->length = strlen($this->buffer);
    		if ($this->length < $l)
                $this->increase_length($l - $this->length);
	    } else {
	        $this->buffer = $this->file;
	        $this->length = strlen($this->buffer);
	    }
		$this->offset = 0;
		$this->stack = array();
	}

	// Make sure that there is at least one
	// character beyond the current offset in
	// the buffer to prevent the tokenizer
	// from attempting to access data that does
	// not exist

	function ensure_content() {
		if ($this->offset >= $this->length - 1) {
			return $this->increase_length();
		} else {
			return true;
		}
	}

	// Forcefully read more data into the buffer

	function increase_length($l=100) {
		if ($this->_mode == 0 && feof($this->file)) {
			return false;
		} else if ($this->_mode == 0) {
		    $totalLength = $this->length + $l;
		    do {
                $this->buffer .= fread($this->file, $totalLength-$this->length);
            } while ((($this->length = strlen($this->buffer)) != $totalLength) && !feof($this->file));
			
			return true;
		} else {
	        return false;
		}
	}
}
?><?php
//
//  FPDI - Version 1.3.1
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//



class fpdi_pdf_parser extends pdf_parser {

    /**
     * Pages
     * Index beginns at 0
     *
     * @var array
     */
    var $pages;
    
    /**
     * Page count
     * @var integer
     */
    var $page_count;
    
    /**
     * actual page number
     * @var integer
     */
    var $pageno;
    
    /**
     * PDF Version of imported Document
     * @var string
     */
    var $pdfVersion;
    
    /**
     * FPDI Reference
     * @var object
     */
    var $fpdi;
    
    /**
     * Available BoxTypes
     *
     * @var array
     */
    var $availableBoxes = array('/MediaBox', '/CropBox', '/BleedBox', '/TrimBox', '/ArtBox');
        
    /**
     * Constructor
     *
     * @param string $filename  Source-Filename
     * @param object $fpdi      Object of type fpdi
     */
    function fpdi_pdf_parser($filename, &$fpdi) {
        $this->fpdi =& $fpdi;
		
        parent::pdf_parser($filename);

        // resolve Pages-Dictonary
        $pages = $this->pdf_resolve_object($this->c, $this->root[1][1]['/Pages']);

        // Read pages
        $this->read_pages($this->c, $pages, $this->pages);
        
        // count pages;
        $this->page_count = count($this->pages);
    }
    
    /**
     * Overwrite parent::error()
     *
     * @param string $msg  Error-Message
     */
    function error($msg) {
    	$this->fpdi->error($msg);	
    }
    
    /**
     * Get pagecount from sourcefile
     *
     * @return int
     */
    function getPageCount() {
        return $this->page_count;
    }


    /**
     * Set pageno
     *
     * @param int $pageno Pagenumber to use
     */
    function setPageno($pageno) {
        $pageno = ((int) $pageno) - 1;

        if ($pageno < 0 || $pageno >= $this->getPageCount()) {
            $this->fpdi->error('Pagenumber is wrong!');
        }

        $this->pageno = $pageno;
    }
    
    /**
     * Get page-resources from current page
     *
     * @return array
     */
    function getPageResources() {
        return $this->_getPageResources($this->pages[$this->pageno]);
    }
    
    /**
     * Get page-resources from /Page
     *
     * @param array $obj Array of pdf-data
     */
    function _getPageResources ($obj) { // $obj = /Page
    	$obj = $this->pdf_resolve_object($this->c, $obj);

        // If the current object has a resources
    	// dictionary associated with it, we use
    	// it. Otherwise, we move back to its
    	// parent object.
        if (isset ($obj[1][1]['/Resources'])) {
    		$res = $this->pdf_resolve_object($this->c, $obj[1][1]['/Resources']);
    		if ($res[0] == PDF_TYPE_OBJECT)
                return $res[1];
            return $res;
    	} else {
    		if (!isset ($obj[1][1]['/Parent'])) {
    			return false;
    		} else {
                $res = $this->_getPageResources($obj[1][1]['/Parent']);
                if ($res[0] == PDF_TYPE_OBJECT)
                    return $res[1];
                return $res;
    		}
    	}
    }


    /**
     * Get content of current page
     *
     * If more /Contents is an array, the streams are concated
     *
     * @return string
     */
    function getContent() {
        $buffer = '';
        
        if (isset($this->pages[$this->pageno][1][1]['/Contents'])) {
            $contents = $this->_getPageContent($this->pages[$this->pageno][1][1]['/Contents']);
            foreach($contents AS $tmp_content) {
                $buffer .= $this->_rebuildContentStream($tmp_content).' ';
            }
        }
        
        return $buffer;
    }
    
    
    /**
     * Resolve all content-objects
     *
     * @param array $content_ref
     * @return array
     */
    function _getPageContent($content_ref) {
        $contents = array();
        
        if ($content_ref[0] == PDF_TYPE_OBJREF) {
            $content = $this->pdf_resolve_object($this->c, $content_ref);
            if ($content[1][0] == PDF_TYPE_ARRAY) {
                $contents = $this->_getPageContent($content[1]);
            } else {
                $contents[] = $content;
            }
        } else if ($content_ref[0] == PDF_TYPE_ARRAY) {
            foreach ($content_ref[1] AS $tmp_content_ref) {
                $contents = array_merge($contents,$this->_getPageContent($tmp_content_ref));
            }
        }

        return $contents;
    }


    /**
     * Rebuild content-streams
     *
     * @param array $obj
     * @return string
     */
    function _rebuildContentStream($obj) {
        $filters = array();
        
        if (isset($obj[1][1]['/Filter'])) {
            $_filter = $obj[1][1]['/Filter'];

            if ($_filter[0] == PDF_TYPE_TOKEN) {
                $filters[] = $_filter;
            } else if ($_filter[0] == PDF_TYPE_ARRAY) {
                $filters = $_filter[1];
            }
        }

        $stream = $obj[2][1];

        foreach ($filters AS $_filter) {
            switch ($_filter[1]) {
                case '/FlateDecode':
                    if (function_exists('gzuncompress')) {
                        $stream = (strlen($stream) > 0) ? @gzuncompress($stream) : '';
                    } else {
                        $this->error(sprintf('To handle %s filter, please compile php with zlib support.',$_filter[1]));
                    }
                    if ($stream === false) {
                        $this->error('Error while decompressing stream.');
                    }
                break;
                case '/LZWDecode':
                    
                    $decoder = new FilterLZW_FPDI($this->fpdi);
                    $stream = $decoder->decode($stream);
                    break;
                case '/ASCII85Decode':
                    
                    $decoder = new FilterASCII85_FPDI($this->fpdi);
                    $stream = $decoder->decode($stream);
                    break;
                case null:
                    $stream = $stream;
                break;
                default:
                    $this->error(sprintf('Unsupported Filter: %s',$_filter[1]));
            }
        }
        
        return $stream;
    }
    
    
    /**
     * Get a Box from a page
     * Arrayformat is same as used by fpdf_tpl
     *
     * @param array $page a /Page
     * @param string $box_index Type of Box @see $availableBoxes
     * @return array
     */
    function getPageBox($page, $box_index) {
        $page = $this->pdf_resolve_object($this->c,$page);
        $box = null;
        if (isset($page[1][1][$box_index]))
            $box =& $page[1][1][$box_index];
        
        if (!is_null($box) && $box[0] == PDF_TYPE_OBJREF) {
            $tmp_box = $this->pdf_resolve_object($this->c,$box);
            $box = $tmp_box[1];
        }
            
        if (!is_null($box) && $box[0] == PDF_TYPE_ARRAY) {
            $b =& $box[1];
            return array('x' => $b[0][1]/$this->fpdi->k,
                         'y' => $b[1][1]/$this->fpdi->k,
                         'w' => abs($b[0][1]-$b[2][1])/$this->fpdi->k,
                         'h' => abs($b[1][1]-$b[3][1])/$this->fpdi->k,
                         'llx' => min($b[0][1], $b[2][1])/$this->fpdi->k,
                         'lly' => min($b[1][1], $b[3][1])/$this->fpdi->k,
                         'urx' => max($b[0][1], $b[2][1])/$this->fpdi->k,
                         'ury' => max($b[1][1], $b[3][1])/$this->fpdi->k,
                         );
        } else if (!isset ($page[1][1]['/Parent'])) {
            return false;
        } else {
            return $this->getPageBox($this->pdf_resolve_object($this->c, $page[1][1]['/Parent']), $box_index);
        }
    }

    function getPageBoxes($pageno) {
        return $this->_getPageBoxes($this->pages[$pageno-1]);
    }
    
    /**
     * Get all Boxes from /Page
     *
     * @param array a /Page
     * @return array
     */
    function _getPageBoxes($page) {
        $boxes = array();

        foreach($this->availableBoxes AS $box) {
            if ($_box = $this->getPageBox($page,$box)) {
                $boxes[$box] = $_box;
            }
        }

        return $boxes;
    }

    /**
     * Get the page rotation by pageno
     *
     * @param integer $pageno
     * @return array
     */
    function getPageRotation($pageno) {
        return $this->_getPageRotation($this->pages[$pageno-1]);
    }
    
    function _getPageRotation ($obj) { // $obj = /Page
    	$obj = $this->pdf_resolve_object($this->c, $obj);
    	if (isset ($obj[1][1]['/Rotate'])) {
    		$res = $this->pdf_resolve_object($this->c, $obj[1][1]['/Rotate']);
    		if ($res[0] == PDF_TYPE_OBJECT)
                return $res[1];
            return $res;
    	} else {
    		if (!isset ($obj[1][1]['/Parent'])) {
    			return false;
    		} else {
                $res = $this->_getPageRotation($obj[1][1]['/Parent']);
                if ($res[0] == PDF_TYPE_OBJECT)
                    return $res[1];
                return $res;
    		}
    	}
    }
    
    /**
     * Read all /Page(es)
     *
     * @param object pdf_context
     * @param array /Pages
     * @param array the result-array
     */
    function read_pages (&$c, &$pages, &$result) {
        // Get the kids dictionary
    	$kids = $this->pdf_resolve_object ($c, $pages[1][1]['/Kids']);

        if (!is_array($kids))
            $this->error('Cannot find /Kids in current /Page-Dictionary');
        foreach ($kids[1] as $v) {
    		$pg = $this->pdf_resolve_object ($c, $v);
            if ($pg[1][1]['/Type'][1] === '/Pages') {
                // If one of the kids is an embedded
    			// /Pages array, resolve it as well.
                $this->read_pages ($c, $pg, $result);
    		} else {
    			$result[] = $pg;
    		}
    	}
    }

    
    
    /**
     * Get PDF-Version
     *
     * And reset the PDF Version used in FPDI if needed
     */
    function getPDFVersion() {
        parent::getPDFVersion();
        $this->fpdi->PDFVersion = max(@$this->fpdi->PDFVersion, $this->pdfVersion);
    }
    
}
?><?php
//
//  FPDI - Version 1.3.1
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

define('FPDI_VERSION','1.3.1');

// Check for TCPDF and remap TCPDF to FPDF
if (class_exists('TCPDF')) {
    
}





class FPDI extends FPDF_TPL {
    /**
     * Actual filename
     * @var string
     */
    var $current_filename;

    /**
     * Parser-Objects
     * @var array
     */
    var $parsers;
    
    /**
     * Current parser
     * @var object
     */
    var $current_parser;
    
    /**
     * object stack
     * @var array
     */
    var $_obj_stack;
    
    /**
     * done object stack
     * @var array
     */
    var $_don_obj_stack;

    /**
     * Current Object Id.
     * @var integer
     */
    var $_current_obj_id;
    
    /**
     * The name of the last imported page box
     * @var string
     */
    var $lastUsedPageBox;
    
    var $_importedPages = array();
    
    
    /**
     * Set a source-file
     *
     * @param string $filename a valid filename
     * @return int number of available pages
     */
    function setSourceFile($filename) {
        $this->current_filename = $filename;
        $fn =& $this->current_filename;

        if (!isset($this->parsers[$fn]))
            $this->parsers[$fn] = new fpdi_pdf_parser($fn, $this);
        $this->current_parser =& $this->parsers[$fn];
        
        return $this->parsers[$fn]->getPageCount();
    }
    
    /**
     * Import a page
     *
     * @param int $pageno pagenumber
     * @return int Index of imported page - to use with fpdf_tpl::useTemplate()
     */
    function importPage($pageno, $boxName='/CropBox') {
        if ($this->_intpl) {
            return $this->error('Please import the desired pages before creating a new template.');
        }
        
        $fn =& $this->current_filename;
        
        // check if page already imported
        $pageKey = $fn.((int)$pageno).$boxName;
        if (isset($this->_importedPages[$pageKey]))
            return $this->_importedPages[$pageKey];
        
        $parser =& $this->parsers[$fn];
        $parser->setPageno($pageno);

        $this->tpl++;
        $this->tpls[$this->tpl] = array();
        $tpl =& $this->tpls[$this->tpl];
        $tpl['parser'] =& $parser;
        $tpl['resources'] = $parser->getPageResources();
        $tpl['buffer'] = $parser->getContent();
        
        if (!in_array($boxName, $parser->availableBoxes))
            return $this->Error(sprintf('Unknown box: %s', $boxName));
        $pageboxes = $parser->getPageBoxes($pageno);
        
        /**
         * MediaBox
         * CropBox: Default -> MediaBox
         * BleedBox: Default -> CropBox
         * TrimBox: Default -> CropBox
         * ArtBox: Default -> CropBox
         */
        if (!isset($pageboxes[$boxName]) && ($boxName == '/BleedBox' || $boxName == '/TrimBox' || $boxName == '/ArtBox'))
            $boxName = '/CropBox';
        if (!isset($pageboxes[$boxName]) && $boxName == '/CropBox')
            $boxName = '/MediaBox';
        
        if (!isset($pageboxes[$boxName]))
            return false;
        $this->lastUsedPageBox = $boxName;
        
        $box = $pageboxes[$boxName];
        $tpl['box'] = $box;
        
        // To build an array that can be used by PDF_TPL::useTemplate()
        $this->tpls[$this->tpl] = array_merge($this->tpls[$this->tpl],$box);
        
        // An imported page will start at 0,0 everytime. Translation will be set in _putformxobjects()
        $tpl['x'] = 0;
        $tpl['y'] = 0;
        
        $page =& $parser->pages[$parser->pageno];
        
        // handle rotated pages
        $rotation = $parser->getPageRotation($pageno);
        $tpl['_rotationAngle'] = 0;
        if (isset($rotation[1]) && ($angle = $rotation[1] % 360) != 0) {
            $steps = $angle / 90;
                
            $_w = $tpl['w'];
            $_h = $tpl['h'];
            $tpl['w'] = $steps % 2 == 0 ? $_w : $_h;
            $tpl['h'] = $steps % 2 == 0 ? $_h : $_w;
            
            $tpl['_rotationAngle'] = $angle*-1;
        }
        
        $this->_importedPages[$pageKey] = $this->tpl;
        
        return $this->tpl;
    }
    
    function getLastUsedPageBox() {
        return $this->lastUsedPageBox;
    }
    
    function useTemplate($tplidx, $_x=null, $_y=null, $_w=0, $_h=0, $adjustPageSize=false) {
        if ($adjustPageSize == true && is_null($_x) && is_null($_y)) {
            $size = $this->getTemplateSize($tplidx, $_w, $_h);
            $format = array($size['w'], $size['h']);
            if ($format[0]!=$this->CurPageFormat[0] || $format[1]!=$this->CurPageFormat[1]) {
                $this->w=$format[0];
                $this->h=$format[1];
                $this->wPt=$this->w*$this->k;
        		$this->hPt=$this->h*$this->k;
        		$this->PageBreakTrigger=$this->h-$this->bMargin;
        		$this->CurPageFormat=$format;
        		$this->PageSizes[$this->page]=array($this->wPt, $this->hPt);
            }
        }
        
        $this->_out('q 0 J 1 w 0 j 0 G 0 g'); // reset standard values
        $s = parent::useTemplate($tplidx, $_x, $_y, $_w, $_h);
        $this->_out('Q');
        return $s;
    }
    
    /**
     * Private method, that rebuilds all needed objects of source files
     */
    function _putimportedobjects() {
        if (is_array($this->parsers) && count($this->parsers) > 0) {
            foreach($this->parsers AS $filename => $p) {
                $this->current_parser =& $this->parsers[$filename];
                if (isset($this->_obj_stack[$filename]) && is_array($this->_obj_stack[$filename])) {
                    while(($n = key($this->_obj_stack[$filename])) !== null) {
                        $nObj = $this->current_parser->pdf_resolve_object($this->current_parser->c,$this->_obj_stack[$filename][$n][1]);
						
                        $this->_newobj($this->_obj_stack[$filename][$n][0]);
                        
                        if ($nObj[0] == PDF_TYPE_STREAM) {
							$this->pdf_write_value ($nObj);
                        } else {
                            $this->pdf_write_value ($nObj[1]);
                        }
                        
                        $this->_out('endobj');
                        $this->_obj_stack[$filename][$n] = null; // free memory
                        unset($this->_obj_stack[$filename][$n]);
                        reset($this->_obj_stack[$filename]);
                    }
                }
            }
        }
    }
    
    
    /**
     * Private Method that writes the form xobjects
     */
    function _putformxobjects() {
        $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	    reset($this->tpls);
        foreach($this->tpls AS $tplidx => $tpl) {
            $p=($this->compress) ? gzcompress($tpl['buffer']) : $tpl['buffer'];
    		$this->_newobj();
    		$cN = $this->n; // TCPDF/Protection: rem current "n"
    		
    		$this->tpls[$tplidx]['n'] = $this->n;
    		$this->_out('<<'.$filter.'/Type /XObject');
            $this->_out('/Subtype /Form');
            $this->_out('/FormType 1');
            
            $this->_out(sprintf('/BBox [%.2F %.2F %.2F %.2F]', 
                (isset($tpl['box']['llx']) ? $tpl['box']['llx'] : $tpl['x'])*$this->k,
                (isset($tpl['box']['lly']) ? $tpl['box']['lly'] : -$tpl['y'])*$this->k,
                (isset($tpl['box']['urx']) ? $tpl['box']['urx'] : $tpl['w'] + $tpl['x'])*$this->k,
                (isset($tpl['box']['ury']) ? $tpl['box']['ury'] : $tpl['h']-$tpl['y'])*$this->k
            ));
            
            $c = 1;
            $s = 0;
            $tx = 0;
            $ty = 0;
            
            if (isset($tpl['box'])) {
                $tx = -$tpl['box']['llx'];
                $ty = -$tpl['box']['lly']; 
                
                if ($tpl['_rotationAngle'] <> 0) {
                    $angle = $tpl['_rotationAngle'] * M_PI/180;
                    $c=cos($angle);
                    $s=sin($angle);
                    
                    switch($tpl['_rotationAngle']) {
                        case -90:
                           $tx = -$tpl['box']['lly'];
                           $ty = $tpl['box']['urx'];
                           break;
                        case -180:
                            $tx = $tpl['box']['urx'];
                            $ty = $tpl['box']['ury'];
                            break;
                        case -270:
                            $tx = $tpl['box']['ury'];
                            $ty = 0;
                            break;
                    }
                }
            } else if ($tpl['x'] != 0 || $tpl['y'] != 0) {
                $tx = -$tpl['x']*2;
                $ty = $tpl['y']*2;
            }
            
            $tx *= $this->k;
            $ty *= $this->k;
            
            if ($c != 1 || $s != 0 || $tx != 0 || $ty != 0) {
                $this->_out(sprintf('/Matrix [%.5F %.5F %.5F %.5F %.5F %.5F]',
                    $c, $s, -$s, $c, $tx, $ty
                ));
            }
            
            $this->_out('/Resources ');

            if (isset($tpl['resources'])) {
                $this->current_parser =& $tpl['parser'];
                $this->pdf_write_value($tpl['resources']); // "n" will be changed
            } else {
                $this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
            	if (isset($this->_res['tpl'][$tplidx]['fonts']) && count($this->_res['tpl'][$tplidx]['fonts'])) {
                	$this->_out('/Font <<');
                    foreach($this->_res['tpl'][$tplidx]['fonts'] as $font)
                		$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
                	$this->_out('>>');
                }
            	if(isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images']) || 
            	   isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls']))
            	{
                    $this->_out('/XObject <<');
                    if (isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images'])) {
                        foreach($this->_res['tpl'][$tplidx]['images'] as $image)
                  			$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
                    }
                    if (isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls'])) {
                        foreach($this->_res['tpl'][$tplidx]['tpls'] as $i => $tpl)
                            $this->_out($this->tplprefix.$i.' '.$tpl['n'].' 0 R');
                    }
                    $this->_out('>>');
            	}
            	$this->_out('>>');
            }

            $nN = $this->n; // TCPDF: rem new "n"
            $this->n = $cN; // TCPDF: reset to current "n"
            $this->_out('/Length '.strlen($p).' >>');
    		$this->_putstream($p);
    		$this->_out('endobj');
    		$this->n = $nN; // TCPDF: reset to new "n"
        }
        
        $this->_putimportedobjects();
    }

    /**
     * Rewritten to handle existing own defined objects
     */
    function _newobj($obj_id=false,$onlynewobj=false) {
        if (!$obj_id) {
            $obj_id = ++$this->n;
        }

        //Begin a new object
        if (!$onlynewobj) {
            $this->offsets[$obj_id] = is_subclass_of($this, 'TCPDF') ? $this->bufferlen : strlen($this->buffer);
            $this->_out($obj_id.' 0 obj');
            $this->_current_obj_id = $obj_id; // for later use with encryption
        }
        return $obj_id;
    }

    /**
     * Writes a value
     * Needed to rebuild the source document
     *
     * @param mixed $value A PDF-Value. Structure of values see cases in this method
     */
    function pdf_write_value(&$value)
    {
        if (is_subclass_of($this, 'TCPDF')) {
            parent::pdf_write_value($value);
        }
        
        switch ($value[0]) {

    		case PDF_TYPE_TOKEN :
                $this->_straightOut($value[1] . ' ');
    			break;
		    case PDF_TYPE_NUMERIC :
    		case PDF_TYPE_REAL :
                if (is_float($value[1]) && $value[1] != 0) {
    			    $this->_straightOut(rtrim(rtrim(sprintf('%F', $value[1]), '0'), '.') .' ');
    			} else {
        			$this->_straightOut($value[1] . ' ');
    			}
    			break;
    			
    		case PDF_TYPE_ARRAY :

    			// An array. Output the proper
    			// structure and move on.

    			$this->_straightOut('[');
                for ($i = 0; $i < count($value[1]); $i++) {
    				$this->pdf_write_value($value[1][$i]);
    			}

    			$this->_out(']');
    			break;

    		case PDF_TYPE_DICTIONARY :

    			// A dictionary.
    			$this->_straightOut('<<');

    			reset ($value[1]);

    			while (list($k, $v) = each($value[1])) {
    				$this->_straightOut($k . ' ');
    				$this->pdf_write_value($v);
    			}

    			$this->_straightOut('>>');
    			break;

    		case PDF_TYPE_OBJREF :

    			// An indirect object reference
    			// Fill the object stack if needed
    			$cpfn =& $this->current_parser->filename;
    			
    			if (!isset($this->_don_obj_stack[$cpfn][$value[1]])) {
    			    $this->_newobj(false,true);
    			    $this->_obj_stack[$cpfn][$value[1]] = array($this->n, $value);
                    $this->_don_obj_stack[$cpfn][$value[1]] = array($this->n, $value); // Value is maybee obsolete!!!
                }
                $objid = $this->_don_obj_stack[$cpfn][$value[1]][0];

    			$this->_out($objid.' 0 R');
    			break;

    		case PDF_TYPE_STRING :

    			// A string.
                $this->_straightOut('('.$value[1].')');

    			break;

    		case PDF_TYPE_STREAM :

    			// A stream. First, output the
    			// stream dictionary, then the
    			// stream data itself.
                $this->pdf_write_value($value[1]);
    			$this->_out('stream');
    			$this->_out($value[2][1]);
    			$this->_out('endstream');
    			break;
            case PDF_TYPE_HEX :
                $this->_straightOut('<'.$value[1].'>');
                break;

            case PDF_TYPE_BOOLEAN :
    		    $this->_straightOut($value[1] ? 'true ' : 'false ');
    		    break;
            
    		case PDF_TYPE_NULL :
                // The null object.

    			$this->_straightOut('null ');
    			break;
    	}
    }
    
    
    /**
     * Modified so not each call will add a newline to the output.
     */
    function _straightOut($s) {
        if (!is_subclass_of($this, 'TCPDF')) {
            if($this->state==2)
        		$this->pages[$this->page] .= $s;
        	else
        		$this->buffer .= $s;
        } else {
            if ($this->state == 2) {
				if (isset($this->footerlen[$this->page]) AND ($this->footerlen[$this->page] > 0)) {
					// puts data before page footer
					$page = substr($this->getPageBuffer($this->page), 0, -$this->footerlen[$this->page]);
					$footer = substr($this->getPageBuffer($this->page), -$this->footerlen[$this->page]);
					$this->setPageBuffer($this->page, $page.' '.$s."\n".$footer);
				} else {
					$this->setPageBuffer($this->page, $s, true);
				}
			} else {
				$this->setBuffer($s);
			}
        }
    }

    /**
     * rewritten to close opened parsers
     *
     */
    function _enddoc() {
        parent::_enddoc();
        $this->_closeParsers();
    }
    
    /**
     * close all files opened by parsers
     */
    function _closeParsers() {
        if ($this->state > 2 && count($this->parsers) > 0) {
          	foreach ($this->parsers as $k => $_){
            	$this->parsers[$k]->closeFile();
            	$this->parsers[$k] = null;
            	unset($this->parsers[$k]);
            }
            return true;
        }
        return false;
    }

}
?><?php
//
//  FPDF_TPL - Version 1.1.3
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

/**
* We extend UFPDF instead of base FPDF. Senceraly PHP have not multiple inheritance and I
* have not any chance do that whithout modification of base libraries.
*
* Symlynk used to donot mangle pathes. Just  do not worked.
**/

class FPDF_TPL extends UFPDF {
    /**
     * Array of Tpl-Data
     * @var array
     */
    var $tpls = array();

    /**
     * Current Template-ID
     * @var int
     */
    var $tpl = 0;
    
    /**
     * "In Template"-Flag
     * @var boolean
     */
    var $_intpl = false;
    
    /**
     * Nameprefix of Templates used in Resources-Dictonary
     * @var string A String defining the Prefix used as Template-Object-Names. Have to beginn with an /
     */
    var $tplprefix = "/TPL";

    /**
     * Resources used By Templates and Pages
     * @var array
     */
    var $_res = array();
    
    /**
     * Last used Template data
     *
     * @var array
     */
    var $lastUsedTemplateData = array();
    
    /**
     * Start a Template
     *
     * This method starts a template. You can give own coordinates to build an own sized
     * Template. Pay attention, that the margins are adapted to the new templatesize.
     * If you want to write outside the template, for example to build a clipped Template,
     * you have to set the Margins and "Cursor"-Position manual after beginTemplate-Call.
     *
     * If no parameter is given, the template uses the current page-size.
     * The Method returns an ID of the current Template. This ID is used later for using this template.
     * Warning: A created Template is used in PDF at all events. Still if you don't use it after creation!
     *
     * @param int $x The x-coordinate given in user-unit
     * @param int $y The y-coordinate given in user-unit
     * @param int $w The width given in user-unit
     * @param int $h The height given in user-unit
     * @return int The ID of new created Template
     */
    function beginTemplate($x=null, $y=null, $w=null, $h=null) {
        if ($this->page <= 0)
            $this->error("You have to add a page to fpdf first!");

        if ($x == null)
            $x = 0;
        if ($y == null)
            $y = 0;
        if ($w == null)
            $w = $this->w;
        if ($h == null)
            $h = $this->h;

        // Save settings
        $this->tpl++;
        $tpl =& $this->tpls[$this->tpl];
        $tpl = array(
            'o_x' => $this->x,
            'o_y' => $this->y,
            'o_AutoPageBreak' => $this->AutoPageBreak,
            'o_bMargin' => $this->bMargin,
            'o_tMargin' => $this->tMargin,
            'o_lMargin' => $this->lMargin,
            'o_rMargin' => $this->rMargin,
            'o_h' => $this->h,
            'o_w' => $this->w,
            'buffer' => '',
            'x' => $x,
            'y' => $y,
            'w' => $w,
            'h' => $h
        );

        $this->SetAutoPageBreak(false);
        
        // Define own high and width to calculate possitions correct
        $this->h = $h;
        $this->w = $w;

        $this->_intpl = true;
        $this->SetXY($x+$this->lMargin, $y+$this->tMargin);
        $this->SetRightMargin($this->w-$w+$this->rMargin);

        return $this->tpl;
    }
    
    /**
     * End Template
     *
     * This method ends a template and reset initiated variables on beginTemplate.
     *
     * @return mixed If a template is opened, the ID is returned. If not a false is returned.
     */
    function endTemplate() {
        if ($this->_intpl) {
            $this->_intpl = false; 
            $tpl =& $this->tpls[$this->tpl];
            $this->SetXY($tpl['o_x'], $tpl['o_y']);
            $this->tMargin = $tpl['o_tMargin'];
            $this->lMargin = $tpl['o_lMargin'];
            $this->rMargin = $tpl['o_rMargin'];
            $this->h = $tpl['o_h'];
            $this->w = $tpl['o_w'];
            $this->SetAutoPageBreak($tpl['o_AutoPageBreak'], $tpl['o_bMargin']);
            
            return $this->tpl;
        } else {
            return false;
        }
    }
    
    /**
     * Use a Template in current Page or other Template
     *
     * You can use a template in a page or in another template.
     * You can give the used template a new size like you use the Image()-method.
     * All parameters are optional. The width or height is calculated automaticaly
     * if one is given. If no parameter is given the origin size as defined in
     * beginTemplate() is used.
     * The calculated or used width and height are returned as an array.
     *
     * @param int $tplidx A valid template-Id
     * @param int $_x The x-position
     * @param int $_y The y-position
     * @param int $_w The new width of the template
     * @param int $_h The new height of the template
     * @retrun array The height and width of the template
     */
    function useTemplate($tplidx, $_x=null, $_y=null, $_w=0, $_h=0) {
        if ($this->page <= 0)
            $this->error("You have to add a page to fpdf first!");

        if (!isset($this->tpls[$tplidx]))
            $this->error("Template does not exist!");
            
        if ($this->_intpl) {
            $this->_res['tpl'][$this->tpl]['tpls'][$tplidx] =& $this->tpls[$tplidx];
        }
        
        $tpl =& $this->tpls[$tplidx];
        $w = $tpl['w'];
        $h = $tpl['h'];
        
        if ($_x == null)
            $_x = 0;
        if ($_y == null)
            $_y = 0;
            
        $_x += $tpl['x'];
        $_y += $tpl['y'];
        
        $wh = $this->getTemplateSize($tplidx, $_w, $_h);
        $_w = $wh['w'];
        $_h = $wh['h'];
    
        $tData = array(
            'x' => $this->x,
            'y' => $this->y,
            'w' => $_w,
            'h' => $_h,
            'scaleX' => ($_w/$w),
            'scaleY' => ($_h/$h),
            'tx' => $_x,
            'ty' =>  ($this->h-$_y-$_h),
            'lty' => ($this->h-$_y-$_h) - ($this->h-$h) * ($_h/$h)
        );
        
        $this->_out(sprintf("q %.4F 0 0 %.4F %.4F %.4F cm", $tData['scaleX'], $tData['scaleY'], $tData['tx']*$this->k, $tData['ty']*$this->k)); // Translate 
        $this->_out(sprintf('%s%d Do Q', $this->tplprefix, $tplidx));

        $this->lastUsedTemplateData = $tData;
        
        return array("w" => $_w, "h" => $_h);
    }
    
    /**
     * Get The calculated Size of a Template
     *
     * If one size is given, this method calculates the other one.
     *
     * @param int $tplidx A valid template-Id
     * @param int $_w The width of the template
     * @param int $_h The height of the template
     * @return array The height and width of the template
     */
    function getTemplateSize($tplidx, $_w=0, $_h=0) {
        if (!$this->tpls[$tplidx])
            return false;

        $tpl =& $this->tpls[$tplidx];
        $w = $tpl['w'];
        $h = $tpl['h'];
        
        if ($_w == 0 and $_h == 0) {
            $_w = $w;
            $_h = $h;
        }

    	if($_w==0)
    		$_w = $_h*$w/$h;
    	if($_h==0)
    		$_h = $_w*$h/$w;
    		
        return array("w" => $_w, "h" => $_h);
    }
    
    /**
     * See FPDF/TCPDF-Documentation ;-)
     */
    function SetFont($family, $style='', $size=0, $fontfile='') {
        if (!is_subclass_of($this, 'TCPDF') && func_num_args() > 3) {
            $this->Error('More than 3 arguments for the SetFont method are only available in TCPDF.');
        }
        /**
         * force the resetting of font changes in a template
         */
        if ($this->_intpl)
            $this->FontFamily = '';
            
        parent::SetFont($family, $style, $size, $fontfile);
       
        $fontkey = $this->FontFamily.$this->FontStyle;
        
        if ($this->_intpl) {
            $this->_res['tpl'][$this->tpl]['fonts'][$fontkey] =& $this->fonts[$fontkey];
        } else {
            $this->_res['page'][$this->page]['fonts'][$fontkey] =& $this->fonts[$fontkey];
        }
    }
    
    /**
     * See FPDF/TCPDF-Documentation ;-)
     */
    function Image($file, $x, $y, $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0) {
        if (!is_subclass_of($this, 'TCPDF') && func_num_args() > 7) {
            $this->Error('More than 7 arguments for the Image method are only available in TCPDF.');
        }
        
        parent::Image($file, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $ismask, $imgmask, $border);
        if ($this->_intpl) {
            $this->_res['tpl'][$this->tpl]['images'][$file] =& $this->images[$file];
        } else {
            $this->_res['page'][$this->page]['images'][$file] =& $this->images[$file];
        }
    }
    
    /**
     * See FPDF-Documentation ;-)
     *
     * AddPage is not available when you're "in" a template.
     */
    function AddPage($orientation='', $format='') {
        if ($this->_intpl)
            $this->Error('Adding pages in templates isn\'t possible!');
        parent::AddPage($orientation, $format);
    }

    /**
     * Preserve adding Links in Templates ...won't work
     */
    function Link($x, $y, $w, $h, $link, $spaces=0) {
        if (!is_subclass_of($this, 'TCPDF') && func_num_args() > 5) {
            $this->Error('More than 7 arguments for the Image method are only available in TCPDF.');
        }
        
        if ($this->_intpl)
            $this->Error('Using links in templates aren\'t possible!');
        parent::Link($x, $y, $w, $h, $link, $spaces);
    }
    
    function AddLink() {
        if ($this->_intpl)
            $this->Error('Adding links in templates aren\'t possible!');
        return parent::AddLink();
    }
    
    function SetLink($link, $y=0, $page=-1) {
        if ($this->_intpl)
            $this->Error('Setting links in templates aren\'t possible!');
        parent::SetLink($link, $y, $page);
    }
    
    /**
     * Private Method that writes the form xobjects
     */
    function _putformxobjects() {
        $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	    reset($this->tpls);
        foreach($this->tpls AS $tplidx => $tpl) {

            $p=($this->compress) ? gzcompress($tpl['buffer']) : $tpl['buffer'];
    		$this->_newobj();
    		$this->tpls[$tplidx]['n'] = $this->n;
    		$this->_out('<<'.$filter.'/Type /XObject');
            $this->_out('/Subtype /Form');
            $this->_out('/FormType 1');
            $this->_out(sprintf('/BBox [%.2F %.2F %.2F %.2F]',
                // llx
                $tpl['x'],
                // lly
                -$tpl['y'],
                // urx
                ($tpl['w']+$tpl['x'])*$this->k,
                // ury
                ($tpl['h']-$tpl['y'])*$this->k
            ));
            
            if ($tpl['x'] != 0 || $tpl['y'] != 0) {
                $this->_out(sprintf('/Matrix [1 0 0 1 %.5F %.5F]',
                     -$tpl['x']*$this->k*2, $tpl['y']*$this->k*2
                ));
            }
            
            $this->_out('/Resources ');

            $this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        	if (isset($this->_res['tpl'][$tplidx]['fonts']) && count($this->_res['tpl'][$tplidx]['fonts'])) {
            	$this->_out('/Font <<');
                foreach($this->_res['tpl'][$tplidx]['fonts'] as $font)
            		$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
            	$this->_out('>>');
            }
        	if(isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images']) || 
        	   isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls']))
        	{
                $this->_out('/XObject <<');
                if (isset($this->_res['tpl'][$tplidx]['images']) && count($this->_res['tpl'][$tplidx]['images'])) {
                    foreach($this->_res['tpl'][$tplidx]['images'] as $image)
              			$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
                }
                if (isset($this->_res['tpl'][$tplidx]['tpls']) && count($this->_res['tpl'][$tplidx]['tpls'])) {
                    foreach($this->_res['tpl'][$tplidx]['tpls'] as $i => $tpl)
                        $this->_out($this->tplprefix.$i.' '.$tpl['n'].' 0 R');
                }
                $this->_out('>>');
        	}
        	$this->_out('>>');
        	
        	$this->_out('/Length '.strlen($p).' >>');
    		$this->_putstream($p);
    		$this->_out('endobj');
        }
    }
    
    /**
     * Overwritten to add _putformxobjects() after _putimages()
     *
     */
    function _putimages() {
        parent::_putimages();
        $this->_putformxobjects();
    }
    
    function _putxobjectdict() {
        parent::_putxobjectdict();
        
        if (count($this->tpls)) {
            foreach($this->tpls as $tplidx => $tpl) {
                $this->_out(sprintf('%s%d %d 0 R', $this->tplprefix, $tplidx, $tpl['n']));
            }
        }
    }

    /**
     * Private Method
     */
    function _out($s) {
        if ($this->state==2 && $this->_intpl) {
            $this->tpls[$this->tpl]['buffer'] .= $s."\n";
        } else {
            parent::_out($s);
        }
    }
}
?><?php
//
//  FPDI - Version 1.3.1
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//



class FilterLZW_FPDI extends FilterLZW {

    var $fpdi;

    function FilterLZW_FPDI(&$fpdi) {
        $this->fpdi =& $fpdi;
    }
    
    function error($msg) {
        $this->fpdi->error($msg);
    }
}
?><?php
//
//  FPDI - Version 1.3.1
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

class FilterLZW {
    
    var $sTable = array();
    var $data = null;
    var $dataLength = 0;
    var $tIdx;
    var $bitsToGet = 9;
    var $bytePointer;
    var $bitPointer;
    var $nextData = 0;
    var $nextBits = 0;
    var $andTable = array(511, 1023, 2047, 4095);

    function error($msg) {
        die($msg);
    }
    
    /**
     * Method to decode LZW compressed data.
     *
     * @param string data    The compressed data.
     */
    function decode($data) {

        if($data[0] == 0x00 && $data[1] == 0x01) {
            $this->error('LZW flavour not supported.');
        }

        $this->initsTable();

        $this->data = $data;
        $this->dataLength = strlen($data);

        // Initialize pointers
        $this->bytePointer = 0;
        $this->bitPointer = 0;

        $this->nextData = 0;
        $this->nextBits = 0;

        $oldCode = 0;

        $string = '';
        $uncompData = '';

        while (($code = $this->getNextCode()) != 257) {
            if ($code == 256) {
                $this->initsTable();
                $code = $this->getNextCode();

                if ($code == 257) {
                    break;
                }

                $uncompData .= $this->sTable[$code];
                $oldCode = $code;

            } else {

                if ($code < $this->tIdx) {
                    $string = $this->sTable[$code];
                    $uncompData .= $string;

                    $this->addStringToTable($this->sTable[$oldCode], $string[0]);
                    $oldCode = $code;
                } else {
                    $string = $this->sTable[$oldCode];
                    $string = $string.$string[0];
                    $uncompData .= $string;

                    $this->addStringToTable($string);
                    $oldCode = $code;
                }
            }
        }
        
        return $uncompData;
    }


    /**
     * Initialize the string table.
     */
    function initsTable() {
        $this->sTable = array();

        for ($i = 0; $i < 256; $i++)
            $this->sTable[$i] = chr($i);

        $this->tIdx = 258;
        $this->bitsToGet = 9;
    }

    /**
     * Add a new string to the string table.
     */
    function addStringToTable ($oldString, $newString='') {
        $string = $oldString.$newString;

        // Add this new String to the table
        $this->sTable[$this->tIdx++] = $string;

        if ($this->tIdx == 511) {
            $this->bitsToGet = 10;
        } else if ($this->tIdx == 1023) {
            $this->bitsToGet = 11;
        } else if ($this->tIdx == 2047) {
            $this->bitsToGet = 12;
        }
    }

    // Returns the next 9, 10, 11 or 12 bits
    function getNextCode() {
        if ($this->bytePointer == $this->dataLength) {
            return 257;
        }

        $this->nextData = ($this->nextData << 8) | (ord($this->data[$this->bytePointer++]) & 0xff);
        $this->nextBits += 8;

        if ($this->nextBits < $this->bitsToGet) {
            $this->nextData = ($this->nextData << 8) | (ord($this->data[$this->bytePointer++]) & 0xff);
            $this->nextBits += 8;
        }

        $code = ($this->nextData >> ($this->nextBits - $this->bitsToGet)) & $this->andTable[$this->bitsToGet-9];
        $this->nextBits -= $this->bitsToGet;

        return $code;
    }
    
    function encode($in) {
        $this->error("LZW encoding not implemented.");
    }
}
?><?php
//
//  FPDI - Version 1.3.1
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//



class FilterASCII85_FPDI extends FilterASCII85 {

    var $fpdi;
    
    function FPDI_FilterASCII85(&$fpdi) {
        $this->fpdi =& $fpdi;
    }

    function error($msg) {
        $this->fpdi->error($msg);
    }
}
?><?php
//
//  FPDI - Version 1.3.1
//
//    Copyright 2004-2009 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

if (!defined('ORD_z'))
	define('ORD_z',ord('z'));
if (!defined('ORD_exclmark'))
	define('ORD_exclmark', ord('!'));
if (!defined('ORD_u'))	
	define('ORD_u', ord('u'));
if (!defined('ORD_tilde'))
	define('ORD_tilde', ord('~'));

class FilterASCII85 {
    
    function error($msg) {
        die($msg);
    }
    
    function decode($in) {
        $out = '';
        $state = 0;
        $chn = null;
        
        $l = strlen($in);
        
        for ($k = 0; $k < $l; ++$k) {
            $ch = ord($in[$k]) & 0xff;
            
            if ($ch == ORD_tilde) {
                break;
            }
            if (preg_match('/^\s$/',chr($ch))) {
                continue;
            }
            if ($ch == ORD_z && $state == 0) {
                $out .= chr(0).chr(0).chr(0).chr(0);
                continue;
            }
            if ($ch < ORD_exclmark || $ch > ORD_u) {
                $this->error('Illegal character in ASCII85Decode.');
            }
            
            $chn[$state++] = $ch - ORD_exclmark;
            
            if ($state == 5) {
                $state = 0;
                $r = 0;
                for ($j = 0; $j < 5; ++$j)
                    $r = $r * 85 + $chn[$j];
                $out .= chr($r >> 24);
                $out .= chr($r >> 16);
                $out .= chr($r >> 8);
                $out .= chr($r);
            }
        }
        $r = 0;
        
        if ($state == 1)
            $this->error('Illegal length in ASCII85Decode.');
        if ($state == 2) {
            $r = $chn[0] * 85 * 85 * 85 * 85 + ($chn[1]+1) * 85 * 85 * 85;
            $out .= chr($r >> 24);
        }
        else if ($state == 3) {
            $r = $chn[0] * 85 * 85 * 85 * 85 + $chn[1] * 85 * 85 * 85  + ($chn[2]+1) * 85 * 85;
            $out .= chr($r >> 24);
            $out .= chr($r >> 16);
        }
        else if ($state == 4) {
            $r = $chn[0] * 85 * 85 * 85 * 85 + $chn[1] * 85 * 85 * 85  + $chn[2] * 85 * 85  + ($chn[3]+1) * 85 ;
            $out .= chr($r >> 24);
            $out .= chr($r >> 16);
            $out .= chr($r >> 8);
        }

        return $out;
    }
    
    function encode($in) {
        $this->error("ASCII85 encoding not implemented.");
    }
}
?><?php
/*******************************************************************************
* FPDF                                                                         *
*                                                                              *
* Version: 1.6                                                                 *
* Date:    2008-08-03                                                          *
* Author:  Olivier PLATHEY                                                     *
*******************************************************************************/

define('FPDF_VERSION','1.6');

class FPDF
{
var $page;               //current page number
var $n;                  //current object number
var $offsets;            //array of object offsets
var $buffer;             //buffer holding in-memory PDF
var $pages;              //array containing pages
var $state;              //current document state
var $compress;           //compression flag
var $k;                  //scale factor (number of points in user unit)
var $DefOrientation;     //default orientation
var $CurOrientation;     //current orientation
var $PageFormats;        //available page formats
var $DefPageFormat;      //default page format
var $CurPageFormat;      //current page format
var $PageSizes;          //array storing non-default page sizes
var $wPt,$hPt;           //dimensions of current page in points
var $w,$h;               //dimensions of current page in user unit
var $lMargin;            //left margin
var $tMargin;            //top margin
var $rMargin;            //right margin
var $bMargin;            //page break margin
var $cMargin;            //cell margin
var $x,$y;               //current position in user unit
var $lasth;              //height of last printed cell
var $LineWidth;          //line width in user unit
var $CoreFonts;          //array of standard font names
var $fonts;              //array of used fonts
var $FontFiles;          //array of font files
var $diffs;              //array of encoding differences
var $FontFamily;         //current font family
var $FontStyle;          //current font style
var $underline;          //underlining flag
var $CurrentFont;        //current font info
var $FontSizePt;         //current font size in points
var $FontSize;           //current font size in user unit
var $DrawColor;          //commands for drawing color
var $FillColor;          //commands for filling color
var $TextColor;          //commands for text color
var $ColorFlag;          //indicates whether fill and text colors are different
var $ws;                 //word spacing
var $images;             //array of used images
var $PageLinks;          //array of links in pages
var $links;              //array of internal links
var $AutoPageBreak;      //automatic page breaking
var $PageBreakTrigger;   //threshold used to trigger page breaks
var $InHeader;           //flag set when processing header
var $InFooter;           //flag set when processing footer
var $ZoomMode;           //zoom display mode
var $LayoutMode;         //layout display mode
var $title;              //title
var $subject;            //subject
var $author;             //author
var $keywords;           //keywords
var $creator;            //creator
var $AliasNbPages;       //alias for total number of pages
var $PDFVersion;         //PDF version number

/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
function FPDF($orientation='P', $unit='mm', $format='A4')
{
	//Some checks
	$this->_dochecks();
	//Initialization of properties
	$this->page=0;
	$this->n=2;
	$this->buffer='';
	$this->pages=array();
	$this->PageSizes=array();
	$this->state=0;
	$this->fonts=array();
	$this->FontFiles=array();
	$this->diffs=array();
	$this->images=array();
	$this->links=array();
	$this->InHeader=false;
	$this->InFooter=false;
	$this->lasth=0;
	$this->FontFamily='';
	$this->FontStyle='';
	$this->FontSizePt=12;
	$this->underline=false;
	$this->DrawColor='0 G';
	$this->FillColor='0 g';
	$this->TextColor='0 g';
	$this->ColorFlag=false;
	$this->ws=0;
	//Standard fonts
	$this->CoreFonts=array('courier'=>'Courier', 'courierB'=>'Courier-Bold', 'courierI'=>'Courier-Oblique', 'courierBI'=>'Courier-BoldOblique',
		'helvetica'=>'Helvetica', 'helveticaB'=>'Helvetica-Bold', 'helveticaI'=>'Helvetica-Oblique', 'helveticaBI'=>'Helvetica-BoldOblique',
		'times'=>'Times-Roman', 'timesB'=>'Times-Bold', 'timesI'=>'Times-Italic', 'timesBI'=>'Times-BoldItalic',
		'symbol'=>'Symbol', 'zapfdingbats'=>'ZapfDingbats');
	//Scale factor
	if($unit=='pt')
		$this->k=1;
	elseif($unit=='mm')
		$this->k=72/25.4;
	elseif($unit=='cm')
		$this->k=72/2.54;
	elseif($unit=='in')
		$this->k=72;
	else
		$this->Error('Incorrect unit: '.$unit);
	//Page format
	$this->PageFormats=array('a3'=>array(841.89,1190.55), 'a4'=>array(595.28,841.89), 'a5'=>array(420.94,595.28),
		'letter'=>array(612,792), 'legal'=>array(612,1008));
	if(is_string($format))
		$format=$this->_getpageformat($format);
	$this->DefPageFormat=$format;
	$this->CurPageFormat=$format;
	//Page orientation
	$orientation=strtolower($orientation);
	if($orientation=='p' || $orientation=='portrait')
	{
		$this->DefOrientation='P';
		$this->w=$this->DefPageFormat[0];
		$this->h=$this->DefPageFormat[1];
	}
	elseif($orientation=='l' || $orientation=='landscape')
	{
		$this->DefOrientation='L';
		$this->w=$this->DefPageFormat[1];
		$this->h=$this->DefPageFormat[0];
	}
	else
		$this->Error('Incorrect orientation: '.$orientation);
	$this->CurOrientation=$this->DefOrientation;
	$this->wPt=$this->w*$this->k;
	$this->hPt=$this->h*$this->k;
	//Page margins (1 cm)
	$margin=28.35/$this->k;
	$this->SetMargins($margin,$margin);
	//Interior cell margin (1 mm)
	$this->cMargin=$margin/10;
	//Line width (0.2 mm)
	$this->LineWidth=.567/$this->k;
	//Automatic page break
	$this->SetAutoPageBreak(true,2*$margin);
	//Full width display mode
	$this->SetDisplayMode('fullwidth');
	//Enable compression
	$this->SetCompression(true);
	//Set default PDF version number
	$this->PDFVersion='1.3';
}

function SetMargins($left, $top, $right=null)
{
	//Set left, top and right margins
	$this->lMargin=$left;
	$this->tMargin=$top;
	if($right===null)
		$right=$left;
	$this->rMargin=$right;
}

function SetLeftMargin($margin)
{
	//Set left margin
	$this->lMargin=$margin;
	if($this->page>0 && $this->x<$margin)
		$this->x=$margin;
}

function SetTopMargin($margin)
{
	//Set top margin
	$this->tMargin=$margin;
}

function SetRightMargin($margin)
{
	//Set right margin
	$this->rMargin=$margin;
}

function SetAutoPageBreak($auto, $margin=0)
{
	//Set auto page break mode and triggering margin
	$this->AutoPageBreak=$auto;
	$this->bMargin=$margin;
	$this->PageBreakTrigger=$this->h-$margin;
}

function SetDisplayMode($zoom, $layout='continuous')
{
	//Set display mode in viewer
	if($zoom=='fullpage' || $zoom=='fullwidth' || $zoom=='real' || $zoom=='default' || !is_string($zoom))
		$this->ZoomMode=$zoom;
	else
		$this->Error('Incorrect zoom display mode: '.$zoom);
	if($layout=='single' || $layout=='continuous' || $layout=='two' || $layout=='default')
		$this->LayoutMode=$layout;
	else
		$this->Error('Incorrect layout display mode: '.$layout);
}

function SetCompression($compress)
{
	//Set page compression
	if(function_exists('gzcompress'))
		$this->compress=$compress;
	else
		$this->compress=false;
}

function SetTitle($title, $isUTF8=false)
{
	//Title of document
	if($isUTF8)
		$title=$this->_UTF8toUTF16($title);
	$this->title=$title;
}

function SetSubject($subject, $isUTF8=false)
{
	//Subject of document
	if($isUTF8)
		$subject=$this->_UTF8toUTF16($subject);
	$this->subject=$subject;
}

function SetAuthor($author, $isUTF8=false)
{
	//Author of document
	if($isUTF8)
		$author=$this->_UTF8toUTF16($author);
	$this->author=$author;
}

function SetKeywords($keywords, $isUTF8=false)
{
	//Keywords of document
	if($isUTF8)
		$keywords=$this->_UTF8toUTF16($keywords);
	$this->keywords=$keywords;
}

function SetCreator($creator, $isUTF8=false)
{
	//Creator of document
	if($isUTF8)
		$creator=$this->_UTF8toUTF16($creator);
	$this->creator=$creator;
}

function AliasNbPages($alias='{nb}')
{
	//Define an alias for total number of pages
	$this->AliasNbPages=$alias;
}

function Error($msg)
{
	//Fatal error
	die('<b>FPDF error:</b> '.$msg);
}

function Open()
{
	//Begin document
	$this->state=1;
}

function Close()
{
	//Terminate document
	if($this->state==3)
		return;
	if($this->page==0)
		$this->AddPage();
	//Page footer
	$this->InFooter=true;
	$this->Footer();
	$this->InFooter=false;
	//Close page
	$this->_endpage();
	//Close document
	$this->_enddoc();
}

function AddPage($orientation='', $format='')
{
	//Start a new page
	if($this->state==0)
		$this->Open();
	$family=$this->FontFamily;
	$style=$this->FontStyle.($this->underline ? 'U' : '');
	$size=$this->FontSizePt;
	$lw=$this->LineWidth;
	$dc=$this->DrawColor;
	$fc=$this->FillColor;
	$tc=$this->TextColor;
	$cf=$this->ColorFlag;
	if($this->page>0)
	{
		//Page footer
		$this->InFooter=true;
		$this->Footer();
		$this->InFooter=false;
		//Close page
		$this->_endpage();
	}
	//Start new page
	$this->_beginpage($orientation,$format);
	//Set line cap style to square
	$this->_out('2 J');
	//Set line width
	$this->LineWidth=$lw;
	$this->_out(sprintf('%.2F w',$lw*$this->k));
	//Set font
	if($family)
		$this->SetFont($family,$style,$size);
	//Set colors
	$this->DrawColor=$dc;
	if($dc!='0 G')
		$this->_out($dc);
	$this->FillColor=$fc;
	if($fc!='0 g')
		$this->_out($fc);
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;
	//Page header
	$this->InHeader=true;
	$this->Header();
	$this->InHeader=false;
	//Restore line width
	if($this->LineWidth!=$lw)
	{
		$this->LineWidth=$lw;
		$this->_out(sprintf('%.2F w',$lw*$this->k));
	}
	//Restore font
	if($family)
		$this->SetFont($family,$style,$size);
	//Restore colors
	if($this->DrawColor!=$dc)
	{
		$this->DrawColor=$dc;
		$this->_out($dc);
	}
	if($this->FillColor!=$fc)
	{
		$this->FillColor=$fc;
		$this->_out($fc);
	}
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;
}

function Header()
{
	//To be implemented in your own inherited class
}

function Footer()
{
	//To be implemented in your own inherited class
}

function PageNo()
{
	//Get current page number
	return $this->page;
}

function SetDrawColor($r, $g=null, $b=null)
{
	//Set color for all stroking operations
	if(($r==0 && $g==0 && $b==0) || $g===null)
		$this->DrawColor=sprintf('%.3F G',$r/255);
	else
		$this->DrawColor=sprintf('%.3F %.3F %.3F RG',$r/255,$g/255,$b/255);
	if($this->page>0)
		$this->_out($this->DrawColor);
}

function SetFillColor($r, $g=null, $b=null)
{
	//Set color for all filling operations
	if(($r==0 && $g==0 && $b==0) || $g===null)
		$this->FillColor=sprintf('%.3F g',$r/255);
	else
		$this->FillColor=sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
	$this->ColorFlag=($this->FillColor!=$this->TextColor);
	if($this->page>0)
		$this->_out($this->FillColor);
}

function SetTextColor($r, $g=null, $b=null)
{
	//Set color for text
	if(($r==0 && $g==0 && $b==0) || $g===null)
		$this->TextColor=sprintf('%.3F g',$r/255);
	else
		$this->TextColor=sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
	$this->ColorFlag=($this->FillColor!=$this->TextColor);
}

function GetStringWidth($s)
{
	//Get width of a string in the current font
	$s=(string)$s;
	$cw=&$this->CurrentFont['cw'];
	$w=0;
	$l=strlen($s);
	for($i=0;$i<$l;$i++)
		$w+=$cw[$s[$i]];
	return $w*$this->FontSize/1000;
}

function SetLineWidth($width)
{
	//Set line width
	$this->LineWidth=$width;
	if($this->page>0)
		$this->_out(sprintf('%.2F w',$width*$this->k));
}

function Line($x1, $y1, $x2, $y2)
{
	//Draw a line
	$this->_out(sprintf('%.2F %.2F m %.2F %.2F l S',$x1*$this->k,($this->h-$y1)*$this->k,$x2*$this->k,($this->h-$y2)*$this->k));
}

function Rect($x, $y, $w, $h, $style='')
{
	//Draw a rectangle
	if($style=='F')
		$op='f';
	elseif($style=='FD' || $style=='DF')
		$op='B';
	else
		$op='S';
	$this->_out(sprintf('%.2F %.2F %.2F %.2F re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
}

function AddFont($family, $style='', $file='')
{
	//Add a TrueType or Type1 font
	$family=strtolower($family);
	if($file=='')
		$file=str_replace(' ','',$family).strtolower($style).'.php';
	if($family=='arial')
		$family='helvetica';
	$style=strtoupper($style);
	if($style=='IB')
		$style='BI';
	$fontkey=$family.$style;
	if(isset($this->fonts[$fontkey]))
		return;
	include($this->_getfontpath().$file);
	if(!isset($name))
		$this->Error('Could not include font definition file');
	$i=count($this->fonts)+1;
	$this->fonts[$fontkey]=array('i'=>$i, 'type'=>$type, 'name'=>$name, 'desc'=>$desc, 'up'=>$up, 'ut'=>$ut, 'cw'=>$cw, 'enc'=>$enc, 'file'=>$file);
	if($diff)
	{
		//Search existing encodings
		$d=0;
		$nb=count($this->diffs);
		for($i=1;$i<=$nb;$i++)
		{
			if($this->diffs[$i]==$diff)
			{
				$d=$i;
				break;
			}
		}
		if($d==0)
		{
			$d=$nb+1;
			$this->diffs[$d]=$diff;
		}
		$this->fonts[$fontkey]['diff']=$d;
	}
	if($file)
	{
		if($type=='TrueType')
			$this->FontFiles[$file]=array('length1'=>$originalsize);
		else
			$this->FontFiles[$file]=array('length1'=>$size1, 'length2'=>$size2);
	}
}

function SetFont($family, $style='', $size=0)
{
	//Select a font; size given in points
	global $fpdf_charwidths;

	$family=strtolower($family);
	if($family=='')
		$family=$this->FontFamily;
	if($family=='arial')
		$family='helvetica';
	elseif($family=='symbol' || $family=='zapfdingbats')
		$style='';
	$style=strtoupper($style);
	if(strpos($style,'U')!==false)
	{
		$this->underline=true;
		$style=str_replace('U','',$style);
	}
	else
		$this->underline=false;
	if($style=='IB')
		$style='BI';
	if($size==0)
		$size=$this->FontSizePt;
	//Test if font is already selected
	if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
		return;
	//Test if used for the first time
	$fontkey=$family.$style;
	if(!isset($this->fonts[$fontkey]))
	{
		//Check if one of the standard fonts
		if(isset($this->CoreFonts[$fontkey]))
		{
			if(!isset($fpdf_charwidths[$fontkey]))
			{
				//Load metric file
				$file=$family;
				if($family=='times' || $family=='helvetica')
					$file.=strtolower($style);
				include($this->_getfontpath().$file.'.php');
				if(!isset($fpdf_charwidths[$fontkey]))
					$this->Error('Could not include font metric file');
			}
			$i=count($this->fonts)+1;
			$name=$this->CoreFonts[$fontkey];
			$cw=$fpdf_charwidths[$fontkey];
			$this->fonts[$fontkey]=array('i'=>$i, 'type'=>'core', 'name'=>$name, 'up'=>-100, 'ut'=>50, 'cw'=>$cw);
		}
		else
			$this->Error('Undefined font: '.$family.' '.$style);
	}
	//Select it
	$this->FontFamily=$family;
	$this->FontStyle=$style;
	$this->FontSizePt=$size;
	$this->FontSize=$size/$this->k;
	$this->CurrentFont=&$this->fonts[$fontkey];
	if($this->page>0)
		$this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}

function SetFontSize($size)
{
	//Set font size in points
	if($this->FontSizePt==$size)
		return;
	$this->FontSizePt=$size;
	$this->FontSize=$size/$this->k;
	if($this->page>0)
		$this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}

function AddLink()
{
	//Create a new internal link
	$n=count($this->links)+1;
	$this->links[$n]=array(0, 0);
	return $n;
}

function SetLink($link, $y=0, $page=-1)
{
	//Set destination of internal link
	if($y==-1)
		$y=$this->y;
	if($page==-1)
		$page=$this->page;
	$this->links[$link]=array($page, $y);
}

function Link($x, $y, $w, $h, $link)
{
	//Put a link on the page
	$this->PageLinks[$this->page][]=array($x*$this->k, $this->hPt-$y*$this->k, $w*$this->k, $h*$this->k, $link);
}

function Text($x, $y, $txt)
{
	//Output a string
	$s=sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
	if($this->underline && $txt!='')
		$s.=' '.$this->_dounderline($x,$y,$txt);
	if($this->ColorFlag)
		$s='q '.$this->TextColor.' '.$s.' Q';
	$this->_out($s);
}

function AcceptPageBreak()
{
	//Accept automatic page break or not
	return $this->AutoPageBreak;
}

function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
	//Output a cell
	$k=$this->k;
	if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
	{
		//Automatic page break
		$x=$this->x;
		$ws=$this->ws;
		if($ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		$this->AddPage($this->CurOrientation,$this->CurPageFormat);
		$this->x=$x;
		if($ws>0)
		{
			$this->ws=$ws;
			$this->_out(sprintf('%.3F Tw',$ws*$k));
		}
	}
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$s='';
	if($fill || $border==1)
	{
		if($fill)
			$op=($border==1) ? 'B' : 'f';
		else
			$op='S';
		$s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
	}
	if(is_string($border))
	{
		$x=$this->x;
		$y=$this->y;
		if(strpos($border,'L')!==false)
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'T')!==false)
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		if(strpos($border,'R')!==false)
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'B')!==false)
			$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	}
	if($txt!=='')
	{
		if($align=='R')
			$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
		elseif($align=='C')
			$dx=($w-$this->GetStringWidth($txt))/2;
		else
			$dx=$this->cMargin;
		if($this->ColorFlag)
			$s.='q '.$this->TextColor.' ';
		$txt2=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
		$s.=sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
		if($this->underline)
			$s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
		if($this->ColorFlag)
			$s.=' Q';
		if($link)
			$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
	}
	if($s)
		$this->_out($s);
	$this->lasth=$h;
	if($ln>0)
	{
		//Go to next line
		$this->y+=$h;
		if($ln==1)
			$this->x=$this->lMargin;
	}
	else
		$this->x+=$w;
}

function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
{
	//Output text with automatic or explicit line breaks
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 && $s[$nb-1]=="\n")
		$nb--;
	$b=0;
	if($border)
	{
		if($border==1)
		{
			$border='LTRB';
			$b='LRT';
			$b2='LR';
		}
		else
		{
			$b2='';
			if(strpos($border,'L')!==false)
				$b2.='L';
			if(strpos($border,'R')!==false)
				$b2.='R';
			$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
		}
	}
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$ns=0;
	$nl=1;
	while($i<$nb)
	{
		//Get next character
		$c=$s[$i];
		if($c=="\n")
		{
			//Explicit line break
			if($this->ws>0)
			{
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
			continue;
		}
		if($c==' ')
		{
			$sep=$i;
			$ls=$l;
			$ns++;
		}
		$l+=$cw[$c];
		if($l>$wmax)
		{
			//Automatic line break
			if($sep==-1)
			{
				if($i==$j)
					$i++;
				if($this->ws>0)
				{
					$this->ws=0;
					$this->_out('0 Tw');
				}
				$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			}
			else
			{
				if($align=='J')
				{
					$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
					$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
				}
				$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
		}
		else
			$i++;
	}
	//Last chunk
	if($this->ws>0)
	{
		$this->ws=0;
		$this->_out('0 Tw');
	}
	if($border && strpos($border,'B')!==false)
		$b.='B';
	$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
	$this->x=$this->lMargin;
}

function Write($h, $txt, $link='')
{
	//Output text in flowing mode
	$cw=&$this->CurrentFont['cw'];
	$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		//Get next character
		$c=$s[$i];
		if($c=="\n")
		{
			//Explicit line break
			$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			if($nl==1)
			{
				$this->x=$this->lMargin;
				$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			}
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			//Automatic line break
			if($sep==-1)
			{
				if($this->x>$this->lMargin)
				{
					//Move to next line
					$this->x=$this->lMargin;
					$this->y+=$h;
					$w=$this->w-$this->rMargin-$this->x;
					$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
					$i++;
					$nl++;
					continue;
				}
				if($i==$j)
					$i++;
				$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
			}
			else
			{
				$this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			if($nl==1)
			{
				$this->x=$this->lMargin;
				$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			}
			$nl++;
		}
		else
			$i++;
	}
	//Last chunk
	if($i!=$j)
		$this->Cell($l/1000*$this->FontSize,$h,substr($s,$j),0,0,'',0,$link);
}

function Ln($h=null)
{
	//Line feed; default value is last cell height
	$this->x=$this->lMargin;
	if($h===null)
		$this->y+=$this->lasth;
	else
		$this->y+=$h;
}

function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='')
{
	//Put an image on the page
	if(!isset($this->images[$file]))
	{
		//First use of this image, get info
		if($type=='')
		{
			$pos=strrpos($file,'.');
			if(!$pos)
				$this->Error('Image file has no extension and no type was specified: '.$file);
			$type=substr($file,$pos+1);
		}
		$type=strtolower($type);
		if($type=='jpeg')
			$type='jpg';
		$mtd='_parse'.$type;
		if(!method_exists($this,$mtd))
			$this->Error('Unsupported image type: '.$type);
		$info=$this->$mtd($file);
		$info['i']=count($this->images)+1;
		$this->images[$file]=$info;
	}
	else
		$info=$this->images[$file];
	//Automatic width and height calculation if needed
	if($w==0 && $h==0)
	{
		//Put image at 72 dpi
		$w=$info['w']/$this->k;
		$h=$info['h']/$this->k;
	}
	elseif($w==0)
		$w=$h*$info['w']/$info['h'];
	elseif($h==0)
		$h=$w*$info['h']/$info['w'];
	//Flowing mode
	if($y===null)
	{
		if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
		{
			//Automatic page break
			$x2=$this->x;
			$this->AddPage($this->CurOrientation,$this->CurPageFormat);
			$this->x=$x2;
		}
		$y=$this->y;
		$this->y+=$h;
	}
	if($x===null)
		$x=$this->x;
	$this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
	if($link)
		$this->Link($x,$y,$w,$h,$link);
}

function GetX()
{
	//Get x position
	return $this->x;
}

function SetX($x)
{
	//Set x position
	if($x>=0)
		$this->x=$x;
	else
		$this->x=$this->w+$x;
}

function GetY()
{
	//Get y position
	return $this->y;
}

function SetY($y)
{
	//Set y position and reset x
	$this->x=$this->lMargin;
	if($y>=0)
		$this->y=$y;
	else
		$this->y=$this->h+$y;
}

function SetXY($x, $y)
{
	//Set x and y positions
	$this->SetY($y);
	$this->SetX($x);
}

function Output($name='', $dest='')
{
	//Output PDF to some destination
	if($this->state<3)
		$this->Close();
	$dest=strtoupper($dest);
	if($dest=='')
	{
		if($name=='')
		{
			$name='doc.pdf';
			$dest='I';
		}
		else
			$dest='F';
	}
	switch($dest)
	{
		case 'I':
			//Send to standard output
			if(ob_get_length())
				$this->Error('Some data has already been output, can\'t send PDF file');
			if(php_sapi_name()!='cli')
			{
				//We send to a browser
				header('Content-Type: application/pdf');
				if(headers_sent())
					$this->Error('Some data has already been output, can\'t send PDF file');
				header('Content-Length: '.strlen($this->buffer));
				header('Content-Disposition: inline; filename="'.$name.'"');
				header('Cache-Control: private, max-age=0, must-revalidate');
				header('Pragma: public');
				ini_set('zlib.output_compression','0');
			}
			echo $this->buffer;
			break;
		case 'D':
			//Download file
			if(ob_get_length())
				$this->Error('Some data has already been output, can\'t send PDF file');
			header('Content-Type: application/x-download');
			if(headers_sent())
				$this->Error('Some data has already been output, can\'t send PDF file');
			header('Content-Length: '.strlen($this->buffer));
			header('Content-Disposition: attachment; filename="'.$name.'"');
			header('Cache-Control: private, max-age=0, must-revalidate');
			header('Pragma: public');
			ini_set('zlib.output_compression','0');
			echo $this->buffer;
			break;
		case 'F':
			//Save to local file
			$f=fopen($name,'wb');
			if(!$f)
				$this->Error('Unable to create output file: '.$name);
			fwrite($f,$this->buffer,strlen($this->buffer));
			fclose($f);
			break;
		case 'S':
			//Return as a string
			return $this->buffer;
		default:
			$this->Error('Incorrect output destination: '.$dest);
	}
	return '';
}

/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/
function _dochecks()
{
	//Check availability of %F
	if(sprintf('%.1F',1.0)!='1.0')
		$this->Error('This version of PHP is not supported');
	//Check mbstring overloading
	if(ini_get('mbstring.func_overload') & 2)
		$this->Error('mbstring overloading must be disabled');
	//Disable runtime magic quotes
	if(get_magic_quotes_runtime())
		@set_magic_quotes_runtime(0);
}

function _getpageformat($format)
{
	$format=strtolower($format);
	if(!isset($this->PageFormats[$format]))
		$this->Error('Unknown page format: '.$format);
	$a=$this->PageFormats[$format];
	return array($a[0]/$this->k, $a[1]/$this->k);
}

function _getfontpath()
{
	if(!defined('FPDF_FONTPATH') && is_dir(dirname(__FILE__).'/font'))
		define('FPDF_FONTPATH',dirname(__FILE__).'/font/');
	return defined('FPDF_FONTPATH') ? FPDF_FONTPATH : '';
}

function _beginpage($orientation, $format)
{
	$this->page++;
	$this->pages[$this->page]='';
	$this->state=2;
	$this->x=$this->lMargin;
	$this->y=$this->tMargin;
	$this->FontFamily='';
	//Check page size
	if($orientation=='')
		$orientation=$this->DefOrientation;
	else
		$orientation=strtoupper($orientation[0]);
	if($format=='')
		$format=$this->DefPageFormat;
	else
	{
		if(is_string($format))
			$format=$this->_getpageformat($format);
	}
	if($orientation!=$this->CurOrientation || $format[0]!=$this->CurPageFormat[0] || $format[1]!=$this->CurPageFormat[1])
	{
		//New size
		if($orientation=='P')
		{
			$this->w=$format[0];
			$this->h=$format[1];
		}
		else
		{
			$this->w=$format[1];
			$this->h=$format[0];
		}
		$this->wPt=$this->w*$this->k;
		$this->hPt=$this->h*$this->k;
		$this->PageBreakTrigger=$this->h-$this->bMargin;
		$this->CurOrientation=$orientation;
		$this->CurPageFormat=$format;
	}
	if($orientation!=$this->DefOrientation || $format[0]!=$this->DefPageFormat[0] || $format[1]!=$this->DefPageFormat[1])
		$this->PageSizes[$this->page]=array($this->wPt, $this->hPt);
}

function _endpage()
{
	$this->state=1;
}

function _escape($s)
{
	//Escape special characters in strings
	$s=str_replace('\\','\\\\',$s);
	$s=str_replace('(','\\(',$s);
	$s=str_replace(')','\\)',$s);
	$s=str_replace("\r",'\\r',$s);
	return $s;
}

function _textstring($s)
{
	//Format a text string
	return '('.$this->_escape($s).')';
}

function _UTF8toUTF16($s)
{
	//Convert UTF-8 to UTF-16BE with BOM
	$res="\xFE\xFF";
	$nb=strlen($s);
	$i=0;
	while($i<$nb)
	{
		$c1=ord($s[$i++]);
		if($c1>=224)
		{
			//3-byte character
			$c2=ord($s[$i++]);
			$c3=ord($s[$i++]);
			$res.=chr((($c1 & 0x0F)<<4) + (($c2 & 0x3C)>>2));
			$res.=chr((($c2 & 0x03)<<6) + ($c3 & 0x3F));
		}
		elseif($c1>=192)
		{
			//2-byte character
			$c2=ord($s[$i++]);
			$res.=chr(($c1 & 0x1C)>>2);
			$res.=chr((($c1 & 0x03)<<6) + ($c2 & 0x3F));
		}
		else
		{
			//Single-byte character
			$res.="\0".chr($c1);
		}
	}
	return $res;
}

function _dounderline($x, $y, $txt)
{
	//Underline text
	$up=$this->CurrentFont['up'];
	$ut=$this->CurrentFont['ut'];
	$w=$this->GetStringWidth($txt)+$this->ws*substr_count($txt,' ');
	return sprintf('%.2F %.2F %.2F %.2F re f',$x*$this->k,($this->h-($y-$up/1000*$this->FontSize))*$this->k,$w*$this->k,-$ut/1000*$this->FontSizePt);
}

function _parsejpg($file)
{
	//Extract info from a JPEG file
	$a=GetImageSize($file);
	if(!$a)
		$this->Error('Missing or incorrect image file: '.$file);
	if($a[2]!=2)
		$this->Error('Not a JPEG file: '.$file);
	if(!isset($a['channels']) || $a['channels']==3)
		$colspace='DeviceRGB';
	elseif($a['channels']==4)
		$colspace='DeviceCMYK';
	else
		$colspace='DeviceGray';
	$bpc=isset($a['bits']) ? $a['bits'] : 8;
	//Read whole file
	$f=fopen($file,'rb');
	$data='';
	while(!feof($f))
		$data.=fread($f,8192);
	fclose($f);
	return array('w'=>$a[0], 'h'=>$a[1], 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'DCTDecode', 'data'=>$data);
}

function _parsepng($file)
{
	//Extract info from a PNG file
	$f=fopen($file,'rb');
	if(!$f)
		$this->Error('Can\'t open image file: '.$file);
	//Check signature
	if($this->_readstream($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
		$this->Error('Not a PNG file: '.$file);
	//Read header chunk
	$this->_readstream($f,4);
	if($this->_readstream($f,4)!='IHDR')
		$this->Error('Incorrect PNG file: '.$file);
	$w=$this->_readint($f);
	$h=$this->_readint($f);
	$bpc=ord($this->_readstream($f,1));
	if($bpc>8)
		$this->Error('16-bit depth not supported: '.$file);
	$ct=ord($this->_readstream($f,1));
	if($ct==0)
		$colspace='DeviceGray';
	elseif($ct==2)
		$colspace='DeviceRGB';
	elseif($ct==3)
		$colspace='Indexed';
	else
		$this->Error('Alpha channel not supported: '.$file);
	if(ord($this->_readstream($f,1))!=0)
		$this->Error('Unknown compression method: '.$file);
	if(ord($this->_readstream($f,1))!=0)
		$this->Error('Unknown filter method: '.$file);
	if(ord($this->_readstream($f,1))!=0)
		$this->Error('Interlacing not supported: '.$file);
	$this->_readstream($f,4);
	$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
	//Scan chunks looking for palette, transparency and image data
	$pal='';
	$trns='';
	$data='';
	do
	{
		$n=$this->_readint($f);
		$type=$this->_readstream($f,4);
		if($type=='PLTE')
		{
			//Read palette
			$pal=$this->_readstream($f,$n);
			$this->_readstream($f,4);
		}
		elseif($type=='tRNS')
		{
			//Read transparency info
			$t=$this->_readstream($f,$n);
			if($ct==0)
				$trns=array(ord(substr($t,1,1)));
			elseif($ct==2)
				$trns=array(ord(substr($t,1,1)), ord(substr($t,3,1)), ord(substr($t,5,1)));
			else
			{
				$pos=strpos($t,chr(0));
				if($pos!==false)
					$trns=array($pos);
			}
			$this->_readstream($f,4);
		}
		elseif($type=='IDAT')
		{
			//Read image data block
			$data.=$this->_readstream($f,$n);
			$this->_readstream($f,4);
		}
		elseif($type=='IEND')
			break;
		else
			$this->_readstream($f,$n+4);
	}
	while($n);
	if($colspace=='Indexed' && empty($pal))
		$this->Error('Missing palette in '.$file);
	fclose($f);
	return array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'FlateDecode', 'parms'=>$parms, 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
}

function _readstream($f, $n)
{
	//Read n bytes from stream
	$res='';
	while($n>0 && !feof($f))
	{
		$s=fread($f,$n);
		if($s===false)
			$this->Error('Error while reading stream');
		$n-=strlen($s);
		$res.=$s;
	}
	if($n>0)
		$this->Error('Unexpected end of stream');
	return $res;
}

function _readint($f)
{
	//Read a 4-byte integer from stream
	$a=unpack('Ni',$this->_readstream($f,4));
	return $a['i'];
}

function _parsegif($file)
{
	//Extract info from a GIF file (via PNG conversion)
	if(!function_exists('imagepng'))
		$this->Error('GD extension is required for GIF support');
	if(!function_exists('imagecreatefromgif'))
		$this->Error('GD has no GIF read support');
	$im=imagecreatefromgif($file);
	if(!$im)
		$this->Error('Missing or incorrect image file: '.$file);
	imageinterlace($im,0);
	$tmp=tempnam('.','gif');
	if(!$tmp)
		$this->Error('Unable to create a temporary file');
	if(!imagepng($im,$tmp))
		$this->Error('Error while saving to temporary file');
	imagedestroy($im);
	$info=$this->_parsepng($tmp);
	unlink($tmp);
	return $info;
}

function _newobj()
{
	//Begin a new object
	$this->n++;
	$this->offsets[$this->n]=strlen($this->buffer);
	$this->_out($this->n.' 0 obj');
}

function _putstream($s)
{
	$this->_out('stream');
	$this->_out($s);
	$this->_out('endstream');
}

function _out($s)
{
	//Add a line to the document
	if($this->state==2)
		$this->pages[$this->page].=$s."\n";
	else
		$this->buffer.=$s."\n";
}

function _putpages()
{
	$nb=$this->page;
	if(!empty($this->AliasNbPages))
	{
		//Replace number of pages
		for($n=1;$n<=$nb;$n++)
			$this->pages[$n]=str_replace($this->AliasNbPages,$nb,$this->pages[$n]);
	}
	if($this->DefOrientation=='P')
	{
		$wPt=$this->DefPageFormat[0]*$this->k;
		$hPt=$this->DefPageFormat[1]*$this->k;
	}
	else
	{
		$wPt=$this->DefPageFormat[1]*$this->k;
		$hPt=$this->DefPageFormat[0]*$this->k;
	}
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	for($n=1;$n<=$nb;$n++)
	{
		//Page
		$this->_newobj();
		$this->_out('<</Type /Page');
		$this->_out('/Parent 1 0 R');
		if(isset($this->PageSizes[$n]))
			$this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]',$this->PageSizes[$n][0],$this->PageSizes[$n][1]));
		$this->_out('/Resources 2 0 R');
		if(isset($this->PageLinks[$n]))
		{
			//Links
			$annots='/Annots [';
			foreach($this->PageLinks[$n] as $pl)
			{
				$rect=sprintf('%.2F %.2F %.2F %.2F',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
				$annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
				if(is_string($pl[4]))
					$annots.='/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
				else
				{
					$l=$this->links[$pl[4]];
					$h=isset($this->PageSizes[$l[0]]) ? $this->PageSizes[$l[0]][1] : $hPt;
					$annots.=sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]>>',1+2*$l[0],$h-$l[1]*$this->k);
				}
			}
			$this->_out($annots.']');
		}
		$this->_out('/Contents '.($this->n+1).' 0 R>>');
		$this->_out('endobj');
		//Page content
		$p=($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
		$this->_newobj();
		$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
		$this->_putstream($p);
		$this->_out('endobj');
	}
	//Pages root
	$this->offsets[1]=strlen($this->buffer);
	$this->_out('1 0 obj');
	$this->_out('<</Type /Pages');
	$kids='/Kids [';
	for($i=0;$i<$nb;$i++)
		$kids.=(3+2*$i).' 0 R ';
	$this->_out($kids.']');
	$this->_out('/Count '.$nb);
	$this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]',$wPt,$hPt));
	$this->_out('>>');
	$this->_out('endobj');
}

function _putfonts()
{
	$nf=$this->n;
	foreach($this->diffs as $diff)
	{
		//Encodings
		$this->_newobj();
		$this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
		$this->_out('endobj');
	}
	foreach($this->FontFiles as $file=>$info)
	{
		//Font file embedding
		$this->_newobj();
		$this->FontFiles[$file]['n']=$this->n;
		$font='';
		$f=fopen($this->_getfontpath().$file,'rb',1);
		if(!$f)
			$this->Error('Font file not found');
		while(!feof($f))
			$font.=fread($f,8192);
		fclose($f);
		$compressed=(substr($file,-2)=='.z');
		if(!$compressed && isset($info['length2']))
		{
			$header=(ord($font[0])==128);
			if($header)
			{
				//Strip first binary header
				$font=substr($font,6);
			}
			if($header && ord($font[$info['length1']])==128)
			{
				//Strip second binary header
				$font=substr($font,0,$info['length1']).substr($font,$info['length1']+6);
			}
		}
		$this->_out('<</Length '.strlen($font));
		if($compressed)
			$this->_out('/Filter /FlateDecode');
		$this->_out('/Length1 '.$info['length1']);
		if(isset($info['length2']))
			$this->_out('/Length2 '.$info['length2'].' /Length3 0');
		$this->_out('>>');
		$this->_putstream($font);
		$this->_out('endobj');
	}
	foreach($this->fonts as $k=>$font)
	{
		//Font objects
		$this->fonts[$k]['n']=$this->n+1;
		$type=$font['type'];
		$name=$font['name'];
		if($type=='core')
		{
			//Standard font
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/BaseFont /'.$name);
			$this->_out('/Subtype /Type1');
			if($name!='Symbol' && $name!='ZapfDingbats')
				$this->_out('/Encoding /WinAnsiEncoding');
			$this->_out('>>');
			$this->_out('endobj');
		}
		elseif($type=='Type1' || $type=='TrueType')
		{
			//Additional Type1 or TrueType font
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/BaseFont /'.$name);
			$this->_out('/Subtype /'.$type);
			$this->_out('/FirstChar 32 /LastChar 255');
			$this->_out('/Widths '.($this->n+1).' 0 R');
			$this->_out('/FontDescriptor '.($this->n+2).' 0 R');
			if($font['enc'])
			{
				if(isset($font['diff']))
					$this->_out('/Encoding '.($nf+$font['diff']).' 0 R');
				else
					$this->_out('/Encoding /WinAnsiEncoding');
			}
			$this->_out('>>');
			$this->_out('endobj');
			//Widths
			$this->_newobj();
			$cw=&$font['cw'];
			$s='[';
			for($i=32;$i<=255;$i++)
				$s.=$cw[chr($i)].' ';
			$this->_out($s.']');
			$this->_out('endobj');
			//Descriptor
			$this->_newobj();
			$s='<</Type /FontDescriptor /FontName /'.$name;
			foreach($font['desc'] as $k=>$v)
				$s.=' /'.$k.' '.$v;
			$file=$font['file'];
			if($file)
				$s.=' /FontFile'.($type=='Type1' ? '' : '2').' '.$this->FontFiles[$file]['n'].' 0 R';
			$this->_out($s.'>>');
			$this->_out('endobj');
		}
		else
		{
			//Allow for additional types
			$mtd='_put'.strtolower($type);
			if(!method_exists($this,$mtd))
				$this->Error('Unsupported font type: '.$type);
			$this->$mtd($font);
		}
	}
}

function _putimages()
{
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	reset($this->images);
	while(list($file,$info)=each($this->images))
	{
		$this->_newobj();
		$this->images[$file]['n']=$this->n;
		$this->_out('<</Type /XObject');
		$this->_out('/Subtype /Image');
		$this->_out('/Width '.$info['w']);
		$this->_out('/Height '.$info['h']);
		if($info['cs']=='Indexed')
			$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
		else
		{
			$this->_out('/ColorSpace /'.$info['cs']);
			if($info['cs']=='DeviceCMYK')
				$this->_out('/Decode [1 0 1 0 1 0 1 0]');
		}
		$this->_out('/BitsPerComponent '.$info['bpc']);
		if(isset($info['f']))
			$this->_out('/Filter /'.$info['f']);
		if(isset($info['parms']))
			$this->_out($info['parms']);
		if(isset($info['trns']) && is_array($info['trns']))
		{
			$trns='';
			for($i=0;$i<count($info['trns']);$i++)
				$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
			$this->_out('/Mask ['.$trns.']');
		}
		$this->_out('/Length '.strlen($info['data']).'>>');
		$this->_putstream($info['data']);
		unset($this->images[$file]['data']);
		$this->_out('endobj');
		//Palette
		if($info['cs']=='Indexed')
		{
			$this->_newobj();
			$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
			$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
			$this->_putstream($pal);
			$this->_out('endobj');
		}
	}
}

function _putxobjectdict()
{
	foreach($this->images as $image)
		$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
}

function _putresourcedict()
{
	$this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
	$this->_out('/Font <<');
	foreach($this->fonts as $font)
		$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
	$this->_out('>>');
	$this->_out('/XObject <<');
	$this->_putxobjectdict();
	$this->_out('>>');
}

function _putresources()
{
	$this->_putfonts();
	$this->_putimages();
	//Resource dictionary
	$this->offsets[2]=strlen($this->buffer);
	$this->_out('2 0 obj');
	$this->_out('<<');
	$this->_putresourcedict();
	$this->_out('>>');
	$this->_out('endobj');
}

function _putinfo()
{
	$this->_out('/Producer '.$this->_textstring('FPDF '.FPDF_VERSION));
	if(!empty($this->title))
		$this->_out('/Title '.$this->_textstring($this->title));
	if(!empty($this->subject))
		$this->_out('/Subject '.$this->_textstring($this->subject));
	if(!empty($this->author))
		$this->_out('/Author '.$this->_textstring($this->author));
	if(!empty($this->keywords))
		$this->_out('/Keywords '.$this->_textstring($this->keywords));
	if(!empty($this->creator))
		$this->_out('/Creator '.$this->_textstring($this->creator));
	$this->_out('/CreationDate '.$this->_textstring('D:'.@date('YmdHis')));
}

function _putcatalog()
{
	$this->_out('/Type /Catalog');
	$this->_out('/Pages 1 0 R');
	if($this->ZoomMode=='fullpage')
		$this->_out('/OpenAction [3 0 R /Fit]');
	elseif($this->ZoomMode=='fullwidth')
		$this->_out('/OpenAction [3 0 R /FitH null]');
	elseif($this->ZoomMode=='real')
		$this->_out('/OpenAction [3 0 R /XYZ null null 1]');
	elseif(!is_string($this->ZoomMode))
		$this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode/100).']');
	if($this->LayoutMode=='single')
		$this->_out('/PageLayout /SinglePage');
	elseif($this->LayoutMode=='continuous')
		$this->_out('/PageLayout /OneColumn');
	elseif($this->LayoutMode=='two')
		$this->_out('/PageLayout /TwoColumnLeft');
}

function _putheader()
{
	$this->_out('%PDF-'.$this->PDFVersion);
}

function _puttrailer()
{
	$this->_out('/Size '.($this->n+1));
	$this->_out('/Root '.$this->n.' 0 R');
	$this->_out('/Info '.($this->n-1).' 0 R');
}

function _enddoc()
{
	$this->_putheader();
	$this->_putpages();
	$this->_putresources();
	//Info
	$this->_newobj();
	$this->_out('<<');
	$this->_putinfo();
	$this->_out('>>');
	$this->_out('endobj');
	//Catalog
	$this->_newobj();
	$this->_out('<<');
	$this->_putcatalog();
	$this->_out('>>');
	$this->_out('endobj');
	//Cross-ref
	$o=strlen($this->buffer);
	$this->_out('xref');
	$this->_out('0 '.($this->n+1));
	$this->_out('0000000000 65535 f ');
	for($i=1;$i<=$this->n;$i++)
		$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
	//Trailer
	$this->_out('trailer');
	$this->_out('<<');
	$this->_puttrailer();
	$this->_out('>>');
	$this->_out('startxref');
	$this->_out($o);
	$this->_out('%%EOF');
	$this->state=3;
}
//End of class
}

//Handle special IE contype request
if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']=='contype')
{
	header('Content-Type: application/pdf');
	exit;
}

?>
<?php

/**
 *  File: calendar.php | (c) dynarch.com 2004
 *  Distributed as part of "The Coolest DHTML Calendar"
 *  under the same terms.
 *  -----------------------------------------------------------------
 *  This file implements a simple PHP wrapper for the calendar.  It
 *  allows you to easily include all the calendar files and setup the
 *  calendar by instantiating and calling a PHP object.
 */

define('NEWLINE', "\n");

class DHTML_Calendar {
    var $calendar_lib_path;

    var $calendar_file;
    var $calendar_lang_file;
    var $calendar_setup_file;
    var $calendar_theme_file;
    var $calendar_options;

    function DHTML_Calendar($calendar_lib_path = '/calendar/',
                            $lang              = 'en',
                            $theme             = 'calendar-win2k-1',
                            $stripped          = true) {
        if ($stripped) {
            $this->calendar_file = 'calendar_stripped.js';
            $this->calendar_setup_file = 'calendar-setup_stripped.js';
        } else {
            $this->calendar_file = 'calendar.js';
            $this->calendar_setup_file = 'calendar-setup.js';
        }
        $this->calendar_lang_file = 'lang/calendar-' . $lang . '.js';
        $this->calendar_theme_file = $theme.'.css';
        $this->calendar_lib_path = preg_replace('/\/+$/', '/', $calendar_lib_path);
        $this->calendar_options = array('ifFormat' => '%Y/%m/%d',
                                        'daFormat' => '%Y/%m/%d');
    }

    function set_option($name, $value) {
        $this->calendar_options[$name] = $value;
    }

    function load_files() {
        echo $this->get_load_files_code();
    }

    function get_load_files_code() {
        $code  = ( '<link rel="stylesheet" type="text/css" media="all" href="' .
                   $this->calendar_lib_path . $this->calendar_theme_file .
                   '" />' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_file .
                   '"></script>' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_lang_file .
                   '"></script>' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_setup_file .
                   '"></script>' );
        return $code;
    }

    function _make_calendar($other_options = array()) {
        $js_options = $this->_make_js_hash(array_merge($this->calendar_options, $other_options));
        $code  = ( '<script type="text/javascript">Calendar.setup({' .
                   $js_options .
                   '});</script>' );
        return $code;
    }

    function make_input_field($cal_options = array(), $field_attributes = array()) {
        $id = $this->_gen_id();
        $attrstr = $this->_make_html_attr(array_merge($field_attributes,
                                                      array('id'   => $this->_field_id($id),
                                                            'type' => 'text')));
        echo '<input ' . $attrstr .'/>';
        echo '<a href="#" id="'. $this->_trigger_id($id) . '">' .
            '<img align="middle" border="0" src="' . $this->calendar_lib_path . 'img.gif" alt="" /></a>';

        $options = array_merge($cal_options,
                               array('inputField' => $this->_field_id($id),
                                     'button'     => $this->_trigger_id($id)));
        echo $this->_make_calendar($options);
    }

    /// PRIVATE SECTION

    function _field_id($id) { return 'f-calendar-field-' . $id; }
    function _trigger_id($id) { return 'f-calendar-trigger-' . $id; }
    function _gen_id() { static $id = 0; return ++$id; }

    function _make_js_hash($array) {
        $jstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            if (is_bool($val))
                $val = $val ? 'true' : 'false';
            else if (!is_numeric($val))
                $val = '"'.$val.'"';
            if ($jstr) $jstr .= ',';
            $jstr .= '"' . $key . '":' . $val;
        }
        return $jstr;
    }

    function _make_html_attr($array) {
        $attrstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            $attrstr .= $key . '="' . $val . '" ';
        }
        return $attrstr;
    }
};

?><?php
/**
 * Subsys_JsHttpRequest_Php: PHP backend for JavaScript DHTML loader.
 * (C) 2005 Dmitry Koterov, http://forum.dklab.ru/users/DmitryKoterov/
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * See http://www.gnu.org/copyleft/lesser.html
 *
 * Do not remove this comment if you want to use the script!
 * Не удаляйте данный комментарий, если вы хотите использовать скрипт!
 *
 * This backend library also supports POST requests additionally to GET.
 *
 * @author Dmitry Koterov 
 * @version 3.29
 */

class Subsys_JsHttpRequest_Php
{
    var $SCRIPT_ENCODING = "windows-1251";
    var $SCRIPT_DECODE_MODE = '';
    var $UNIQ_HASH;
    var $SCRIPT_ID;
    var $LOADER = null;
    var $QUOTING = null;
    
    /**
     * Constructor.
     * 
     * Create new Subsys_JsHttpRequest_Php backend object and attach it
     * to script output buffer. As a result - script will always return
     * correct JavaScript code, even in case of fatal errors.
     */
    function Subsys_JsHttpRequest_Php($enc)
    {
        // QUERY_STRING is in form: PHPSESSID=<sid>&a=aaa&b=bbb&<id>
        // where <id> is request ID, <sid> - session ID (if present), 
        // PHPSESSID - session parameter name (by default = "PHPSESSID").

        // Parse QUERY_STRING wrapper format.
        $this->LOADER = "SCRIPT";
        if (preg_match('/(\d+)((?:-\w+)?)$/s', $_SERVER['QUERY_STRING'], $m)) {
            $this->SCRIPT_ID = $m[1];
            // XMLHttpRequest is used if URI ends with "&".
            if ($m[2] == '-xml') $this->LOADER = "XMLHttpRequest";
        } else {
            $this->SCRIPT_ID = 0;
        }

        // Start OB handling early.
        $this->UNIQ_HASH = md5(microtime().getmypid());
        ini_set('error_prepend_string', ini_get('error_prepend_string').$this->UNIQ_HASH);
        ini_set('error_append_string',  ini_get('error_append_string') .$this->UNIQ_HASH);
        ob_start(array(&$this, "_obHandler"));

        // Set up encoding.
        $this->setEncoding($enc);
    }


    /**
     * string getJsCode()
     * 
     * Return JavaScript part of library.
     */
    function getJsCode()
    {
        return file_get_contents(dirname(__FILE__).'/Js.js');
    }


    /**
     * void setEncoding(string $encoding)
     * 
     * Set active script encoding & correct QUERY_STRING according to it.
     * Examples:
     *   "windows-1251"          - set plain encoding (non-windows characters, 
     *                             e.g. hieroglyphs, are totally ignored)
     *   "windows-1251 entities" - set windows encoding, BUT additionally replace:
     *                             "&"         ->  "&amp;" 
     *                             hieroglyph  ->  &#XXXX; entity
     */
    function setEncoding($enc)
    {
        // Parse encoding.
        preg_match('/^(\S*)(?:\s+(\S*))$/', $enc, $p);
        $this->SCRIPT_ENCODING    = strtolower(@$p[1]? $p[1] : $enc);
        $this->SCRIPT_DECODE_MODE = @$p[2]? $p[2] : '';
        // Manually parse QUERY_STRING because of damned Unicode's %uXXXX.
        $this->_correctQueryString();
    }

    
    /**
     * string quoteInput(string $input)
     * 
     * Quote string according to input decoding mode.
     * If entities is used (see setEncoding()), no '&' character is quoted,
     * only '"', '>' and '<' (we presume than '&' is already quoted by
     * input reader function).
     *
     * Use this function INSTEAD of htmlspecialchars() for $_GET data 
     * in your scripts.
     */
    function quoteInput($s)
    {
        if ($this->SCRIPT_DECODE_MODE == 'entities')
            return str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $s);
        else
            return htmlspecialchars($s);
    }


    /**
     * Internal methods.
     */
    
    /**
     * Convert PHP scalar, array or hash to JS scalar/array/hash.
     */
    function _php2js($a)
    {
        if (is_null($a)) return 'null';
        if ($a === false) return 'false';
        if ($a === true) return 'true';
        if (is_scalar($a)) {
            $a = addslashes($a);
            $a = str_replace("\n", '\n', $a);
            $a = str_replace("\r", '\r', $a);
            return "'$a'";
        }
        $isList = true;
        for ($i=0, reset($a); $i<count($a); $i++, next($a))
            if (key($a) !== $i) { $isList = false; break; }
        $result = array();
        if ($isList) {
            foreach ($a as $v) $result[] = Subsys_JsHttpRequest_Php::_php2js($v);
            return '[ ' . join(',', $result) . ' ]';
        } else {
            foreach ($a as $k=>$v) $result[] = Subsys_JsHttpRequest_Php::_php2js($k) . ': ' . Subsys_JsHttpRequest_Php::_php2js($v);
            return '{ ' . join(',', $result) . ' }';
        }
    }


    /**
     * Parse & decode QUERY_STRING.
     */
    function _correctQueryString()
    {
        // ATTENTION!!!
        // HTTP_RAW_POST_DATA is only accessible when Content-Type of POST request
        // is NOT default "application/x-www-form-urlencoded"!!!
        // Library frontend sets "application/octet-stream" for that purpose,
        // see JavaScript code.
        foreach (array('_GET'=>@$_SERVER['QUERY_STRING'], '_POST'=>@$GLOBALS['HTTP_RAW_POST_DATA']) as $dst=>$src) {
            if (isset($GLOBALS[$dst])) {
                // First correct all 2-byte entities.
                $s = preg_replace('/%(?!5B)(?!5D)([0-9a-f]{2})/si', '%u00\\1', $src);
                // Now we can use standard parse_str() with no worry!
                $data = null;
                parse_str($s, $data);
                $GLOBALS[$dst] = $this->_ucs2EntitiesDecode($data);
            }
        }
        $_REQUEST = 
            (isset($_COOKIE)? $_COOKIE : array()) + 
            (isset($_POST)? $_POST : array()) + 
            (isset($_GET)? $_GET : array());
        if (ini_get('register_globals')) {
            // TODO?
        }
    }


    /**
     * Called in case of error too!
     */
    function _obHandler($text)
    {
        // Check for error.
        if (preg_match('{'.$this->UNIQ_HASH.'(.*?)'.$this->UNIQ_HASH.'}sx', $text)) {
            $text = str_replace($this->UNIQ_HASH, '', $text);
            $this->WAS_ERROR = 1;
        }
        // Content-type header.
        // In XMLHttpRRequest mode we must return text/plain - damned stupid Opera 8.0. :(
        Header("Content-type: " . ($this->LOADER=="SCRIPT"? "text/javascript" : "text/plain") . "; charset=" . $this->SCRIPT_ENCODING);
        // Make resulting hash.
        if (!isset($this->RESULT)) $this->RESULT = @$GLOBALS['_RESULT'];
        $result = $this->_php2js($this->RESULT);
        $text = 
            "// BEGIN Subsys_JsHttpRequest_Js\n" .
            "Subsys_JsHttpRequest_Js.dataReady(\n" . 
                "  " . $this->_php2js($this->SCRIPT_ID) . ", // this ID is passed from JavaScript frontend\n" . 
                "  " . $this->_php2js(trim($text)) . ",\n" .
                "  " . $result . "\n" .
            ")\n" .
            "// END Subsys_JsHttpRequest_Js\n" .
        "";
//      $f = fopen("debug", "w"); fwrite($f, $text); fclose($f);
        return $text;
    }


    /**
     * Decode all %uXXXX entities in string or array (recurrent).
     * String must not contain %XX entities - they are ignored!
     */
    function _ucs2EntitiesDecode($data)
    {
        if (is_array($data)) {
            $d = array();
            foreach ($data as $k=>$v) {
                $d[$this->_ucs2EntitiesDecode($k)] = $this->_ucs2EntitiesDecode($v);
            }
            return $d;
        } else {
            if (strpos($data, '%u') !== false) { // improve speed
                $data = preg_replace_callback('/%u([0-9A-F]{1,4})/si', array(&$this, '_ucs2EntitiesDecodeCallback'), $data);
            }
            return $data;
        }
    }


    /**
     * Decode one %uXXXX entity (RE callback).
     */
    function _ucs2EntitiesDecodeCallback($p)
    {
        $hex = $p[1];
        $dec = hexdec($hex);
        if ($dec === "38" && $this->SCRIPT_DECODE_MODE == 'entities') {
            // Process "&" separately in "entities" decode mode.
            $c = "&amp;";
        } else {
            if (is_callable('iconv')) {
                $c = @iconv('UCS-2BE', $this->SCRIPT_ENCODING, pack('n', $dec));
            } else {
                $c = $this->_decUcs2Decode($dec, $this->SCRIPT_ENCODING);
            }
            if (!strlen($c)) {
                if ($this->SCRIPT_DECODE_MODE == 'entities') {
                    $c = '&#'.$dec.';';
                } else {
                    $c = '?';
                }
            }
        }
        return $c;
    }


    /**
     * If there is no ICONV, try to decode 1-byte characters manually
     * (for most popular charsets only).
     */

    /**
     * Convert from UCS-2BE decimal to $toEnc.
     */
    function _decUcs2Decode($code, $toEnc)
    {
        if ($code < 128) return chr($code);
        if (isset($this->_encTables[$toEnc])) {
            $p = array_search($code, $this->_encTables[$toEnc]);
            if ($p !== false) return chr(128 + $p);
        }
        return "";
    }
    

    /**
     * UCS-2BE -> 1-byte encodings (from #128).
     */
    var $_encTables = array(
        'windows-1251' => array(
            0x0402, 0x0403, 0x201A, 0x0453, 0x201E, 0x2026, 0x2020, 0x2021,
            0x20AC, 0x2030, 0x0409, 0x2039, 0x040A, 0x040C, 0x040B, 0x040F,
            0x0452, 0x2018, 0x2019, 0x201C, 0x201D, 0x2022, 0x2013, 0x2014,
            0x0098, 0x2122, 0x0459, 0x203A, 0x045A, 0x045C, 0x045B, 0x045F,
            0x00A0, 0x040E, 0x045E, 0x0408, 0x00A4, 0x0490, 0x00A6, 0x00A7,
            0x0401, 0x00A9, 0x0404, 0x00AB, 0x00AC, 0x00AD, 0x00AE, 0x0407,
            0x00B0, 0x00B1, 0x0406, 0x0456, 0x0491, 0x00B5, 0x00B6, 0x00B7,
            0x0451, 0x2116, 0x0454, 0x00BB, 0x0458, 0x0405, 0x0455, 0x0457,
            0x0410, 0x0411, 0x0412, 0x0413, 0x0414, 0x0415, 0x0416, 0x0417,
            0x0418, 0x0419, 0x041A, 0x041B, 0x041C, 0x041D, 0x041E, 0x041F,
            0x0420, 0x0421, 0x0422, 0x0423, 0x0424, 0x0425, 0x0426, 0x0427,
            0x0428, 0x0429, 0x042A, 0x042B, 0x042C, 0x042D, 0x042E, 0x042F,
            0x0430, 0x0431, 0x0432, 0x0433, 0x0434, 0x0435, 0x0436, 0x0437,
            0x0438, 0x0439, 0x043A, 0x043B, 0x043C, 0x043D, 0x043E, 0x043F,
            0x0440, 0x0441, 0x0442, 0x0443, 0x0444, 0x0445, 0x0446, 0x0447,
            0x0448, 0x0449, 0x044A, 0x044B, 0x044C, 0x044D, 0x044E, 0x044F,
        ),
        'koi8-r' => array(
            0x2500, 0x2502, 0x250C, 0x2510, 0x2514, 0x2518, 0x251C, 0x2524,
            0x252C, 0x2534, 0x253C, 0x2580, 0x2584, 0x2588, 0x258C, 0x2590,
            0x2591, 0x2592, 0x2593, 0x2320, 0x25A0, 0x2219, 0x221A, 0x2248,
            0x2264, 0x2265, 0x00A0, 0x2321, 0x00B0, 0x00B2, 0x00B7, 0x00F7,
            0x2550, 0x2551, 0x2552, 0x0451, 0x2553, 0x2554, 0x2555, 0x2556,
            0x2557, 0x2558, 0x2559, 0x255A, 0x255B, 0x255C, 0x255d, 0x255E,
            0x255F, 0x2560, 0x2561, 0x0401, 0x2562, 0x2563, 0x2564, 0x2565,
            0x2566, 0x2567, 0x2568, 0x2569, 0x256A, 0x256B, 0x256C, 0x00A9,
            0x044E, 0x0430, 0x0431, 0x0446, 0x0434, 0x0435, 0x0444, 0x0433,
            0x0445, 0x0438, 0x0439, 0x043A, 0x043B, 0x043C, 0x043d, 0x043E,
            0x043F, 0x044F, 0x0440, 0x0441, 0x0442, 0x0443, 0x0436, 0x0432,
            0x044C, 0x044B, 0x0437, 0x0448, 0x044d, 0x0449, 0x0447, 0x044A,
            0x042E, 0x0410, 0x0411, 0x0426, 0x0414, 0x0415, 0x0424, 0x0413,
            0x0425, 0x0418, 0x0419, 0x041A, 0x041B, 0x041C, 0x041d, 0x041E,
            0x041F, 0x042F, 0x0420, 0x0421, 0x0422, 0x0423, 0x0416, 0x0412,
            0x042C, 0x042B, 0x0417, 0x0428, 0x042d, 0x0429, 0x0427, 0x042A
        ),
    );
}
?><?php
/**
 * JsHttpRequest: PHP backend for JavaScript DHTML loader.
 * (C) Dmitry Koterov, http://en.dklab.ru
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * See http://www.gnu.org/copyleft/lesser.html
 *
 * Do not remove this comment if you want to use the script!
 * Не удаляйте данный комментарий, если вы хотите использовать скрипт!
 *
 * This backend library also supports POST requests additionally to GET.
 *
 * @author Dmitry Koterov
 * @version 5.x $Id$
 */

class JsHttpRequest{
	var $SCRIPT_ENCODING = "windows-1251";
	var $SCRIPT_DECODE_MODE = '';
	var $LOADER = null;
	var $ID = null;	
	
	// Internal; uniq value.
	var $_uniqHash;
	// Magic number for display_error checking.
	var $_magic = 14623;
	// Previous display_errors value.
	var $_prevDisplayErrors = null;	
	// Internal: response content-type depending on loader type.
	var $_contentTypes = array(
		"script" => "text/javascript",
		"xml"	=> "text/plain", // In XMLHttpRequest mode we must return text/plain - stupid Opera 8.0. :(
		"form"   => "text/html",
		""	   => "text/plain", // for unknown loader
	);
	// Internal: conversion to UTF-8 JSON cancelled because of non-ascii key.
	var $_toUtfFailed = false;
	// Internal: list of characters 128...255 (for strpbrk() ASCII check).
	var $_nonAsciiChars = '';
	// Which Unicode conversion function is available?
	var $_unicodeConvMethod = null;
	// Emergency memory buffer to be freed on memory_limit error.
	var $_emergBuffer = null;

	
	/**
	 * Constructor.
	 * 
	 * Create new JsHttpRequest backend object and attach it
	 * to script output buffer. As a result - script will always return
	 * correct JavaScript code, even in case of fatal errors.
	 *
	 * QUERY_STRING is in form of: PHPSESSID=<sid>&a=aaa&b=bbb&JsHttpRequest=<id>-<loader>
	 * where <id> is a request ID, <loader> is a loader name, <sid> - a session ID (if present), 
	 * PHPSESSID - session parameter name (by default = "PHPSESSID").
	 * 
	 * If an object is created WITHOUT an active AJAX query, it is simply marked as
	 * non-active. Use statuc method isActive() to check.
	 */
	function JsHttpRequest($enc)
	{
		global $JsHttpRequest_Active;

		// Parse QUERY_STRING.
		if (preg_match('/^(.*)(?:&|^)JsHttpRequest=(?:(\d+)-)?([^&]+)((?:&|$).*)$/s', @$_SERVER['QUERY_STRING'], $m)) {
			$this->ID = $m[2];
			$this->LOADER = strtolower($m[3]);
			$_SERVER['QUERY_STRING'] = preg_replace('/^&+|&+$/s', '', preg_replace('/(^|&)'.session_name().'=[^&]*&?/s', '&', $m[1] . $m[4]));
			unset(
				$_GET['JsHttpRequest'],
				$_REQUEST['JsHttpRequest'],
				$_GET[session_name()],
				$_POST[session_name()],
				$_REQUEST[session_name()]
			);
			// Detect Unicode conversion method.
			$this->_unicodeConvMethod = function_exists('mb_convert_encoding')? 'mb' : (function_exists('iconv')? 'iconv' : null);
	
			// Fill an emergency buffer. We erase it at the first line of OB processor
			// to free some memory. This memory may be used on memory_limit error.
			$this->_emergBuffer = str_repeat('a', 1024 * 200);

			// Intercept fatal errors via display_errors (seems it is the only way).	 
			$this->_uniqHash = md5('JsHttpRequest' . microtime() . getmypid());
			$this->_prevDisplayErrors = ini_get('display_errors');
			ini_set('display_errors', $this->_magic); //
			ini_set('error_prepend_string', $this->_uniqHash . ini_get('error_prepend_string'));
			ini_set('error_append_string',  ini_get('error_append_string') . $this->_uniqHash);

			// Start OB handling early.
			ob_start(array(&$this, "_obHandler"));
			$JsHttpRequest_Active = false;
	
			// Set up the encoding.
			$this->setEncoding($enc);
	
			// Check if headers are already sent (see Content-Type library usage).
			// If true - generate a debug message and exit.
			$file = $line = null;
			$headersSent = version_compare(PHP_VERSION, "4.3.0") < 0? headers_sent() : headers_sent($file, $line);
			if ($headersSent) {
				trigger_error(
					"HTTP headers are already sent" . ($line !== null? " in $file on line $line" : " somewhere in the script") . ". "
					. "Possibly you have an extra space (or a newline) before the first line of the script or any library. "
					. "Please note that JsHttpRequest uses its own Content-Type header and fails if "
					. "this header cannot be set. See header() function documentation for more details",
					E_USER_ERROR
				);
				exit();
			}
		} else {
			$this->ID = 0;
			$this->LOADER = 'unknown';
			$JsHttpRequest_Active = false;
		}
	}
	

	/**
	 * Static function.
	 * Returns true if JsHttpRequest output processor is currently active.
	 * 
	 * @return boolean	True if the library is active, false otherwise.
	 */
	function isActive()
	{
		return !empty($GLOBALS['JsHttpRequest_Active']);
	}
	

	/**
	 * string getJsCode()
	 * 
	 * Return JavaScript part of the library.
	 */
	function getJsCode()
	{
		return file_get_contents(dirname(__FILE__) . '/JsHttpRequest.js');
	}


	/**
	 * void setEncoding(string $encoding)
	 * 
	 * Set an active script encoding & correct QUERY_STRING according to it.
	 * Examples:
	 *   "windows-1251"		  - set plain encoding (non-windows characters,
	 *							 e.g. hieroglyphs, are totally ignored)
	 *   "windows-1251 entities" - set windows encoding, BUT additionally replace:
	 *							 "&"		 ->  "&amp;"
	 *							 hieroglyph  ->  &#XXXX; entity
	 */
	function setEncoding($enc)
	{
		// Parse an encoding.
		preg_match('/^(\S*)(?:\s+(\S*))$/', $enc, $p);
		$this->SCRIPT_ENCODING	= strtolower(!empty($p[1])? $p[1] : $enc);
		$this->SCRIPT_DECODE_MODE = !empty($p[2])? $p[2] : '';
		// Manually parse QUERY_STRING because of damned Unicode's %uXXXX.
		$this->_correctSuperglobals();
	}

	
	/**
	 * string quoteInput(string $input)
	 * 
	 * Quote a string according to the input decoding mode.
	 * If entities are used (see setEncoding()), no '&' character is quoted,
	 * only '"', '>' and '<' (we presume that '&' is already quoted by
	 * an input reader function).
	 *
	 * Use this function INSTEAD of htmlspecialchars() for $_GET data 
	 * in your scripts.
	 */
	function quoteInput($s)
	{
		if ($this->SCRIPT_DECODE_MODE == 'entities')
			return str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $s);
		else
			return htmlspecialchars($s);
	}
	

	/**
	 * Convert a PHP scalar, array or hash to JS scalar/array/hash. This function is 
	 * an analog of json_encode(), but it can work with a non-UTF8 input and does not 
	 * analyze the passed data. Output format must be fully JSON compatible.
	 * 
	 * @param mixed $a   Any structure to convert to JS.
	 * @return string	JavaScript equivalent structure.
	 */
	function php2js($a=false)
	{
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a)) {
			if (is_float($a)) {
				// Always use "." for floats.
				$a = str_replace(",", ".", strval($a));
			}
			// All scalars are converted to strings to avoid indeterminism.
			// PHP's "1" and 1 are equal for all PHP operators, but 
			// JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
			// we should get the same result in the JS frontend (string).
			// Character replacements for JSON.
			static $jsonReplaces = array(
				array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
				array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')
			);
			return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
			if (key($a) !== $i) { 
				$isList = false; 
				break; 
			}
		}
		$result = array();
		if ($isList) {
			foreach ($a as $v) {
				$result[] = JsHttpRequest::php2js($v);
			}
			return '[ ' . join(', ', $result) . ' ]';
		} else {
			foreach ($a as $k => $v) {
				$result[] = JsHttpRequest::php2js($k) . ': ' . JsHttpRequest::php2js($v);
			}
			return '{ ' . join(', ', $result) . ' }';
		}
	}
	
		
	/**
	 * Internal methods.
	 */

	/**
	 * Parse & decode QUERY_STRING.
	 */
	function _correctSuperglobals()
	{
		// In case of FORM loader we may go to nirvana, everything is already parsed by PHP.
		if ($this->LOADER == 'form') return;
		
		// ATTENTION!!!
		// HTTP_RAW_POST_DATA is only accessible when Content-Type of POST request
		// is NOT default "application/x-www-form-urlencoded"!!!
		// Library frontend sets "application/octet-stream" for that purpose,
		// see JavaScript code. In PHP 5.2.2.HTTP_RAW_POST_DATA is not set sometimes; 
		// in such cases - read the POST data manually from the STDIN stream.
		$rawPost = strcasecmp(@$_SERVER['REQUEST_METHOD'], 'POST') == 0? (isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : @file_get_contents("php://input")) : null;
		$source = array(
			'_GET' => !empty($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING'] : null, 
			'_POST'=> $rawPost,
		);
		foreach ($source as $dst=>$src) {
			// First correct all 2-byte entities.
			$s = preg_replace('/%(?!5B)(?!5D)([0-9a-f]{2})/si', '%u00\\1', $src);
			// Now we can use standard parse_str() with no worry!
			$data = null;
			parse_str($s, $data);
			$GLOBALS[$dst] = $this->_ucs2EntitiesDecode($data);
		}
		$GLOBALS['HTTP_GET_VARS'] = $_GET; // deprecated vars
		$GLOBALS['HTTP_POST_VARS'] = $_POST;
		$_REQUEST = 
			(isset($_COOKIE)? $_COOKIE : array()) + 
			(isset($_POST)? $_POST : array()) + 
			(isset($_GET)? $_GET : array());
		if (ini_get('register_globals')) {
			// TODO?
		}
	}


	/**
	 * Called in case of error too!
	 */
	function _obHandler($text)
	{
		unset($this->_emergBuffer); // free a piece of memory for memory_limit error
		unset($GLOBALS['JsHttpRequest_Active']);

		// Check for error & fetch a resulting data.
		if (preg_match("/{$this->_uniqHash}(.*?){$this->_uniqHash}/sx", $text, $m)) {
			if (!ini_get('display_errors') || (!$this->_prevDisplayErrors && ini_get('display_errors') == $this->_magic)) {
				// Display_errors:
				// 1. disabled manually after the library initialization, or
				// 2. was initially disabled and is not changed
				$text = str_replace($m[0], '', $text); // strip whole error message
			} else {
				$text = str_replace($this->_uniqHash, '', $text);
			}
		}
		if ($m && preg_match('/\bFatal error(<.*?>)?:/i', $m[1])) {
			// On fatal errors - force null result (generate 500 error). 
			$this->RESULT = null;
		} else {
			// Make a resulting hash.
			if (!isset($this->RESULT)) {
				$this->RESULT = isset($GLOBALS['_RESULT'])? $GLOBALS['_RESULT'] : null;
			}
		}

		
		$encoding = $this->SCRIPT_ENCODING;
		$result = array(
			'id'   => $this->ID,
			'js'   => $this->RESULT,
			'text' => $text,
		);
		if (function_exists('array_walk_recursive') && function_exists('json_encode') && $this->_unicodeConvMethod) {
			$encoding = "UTF-8";
			$this->_nonAsciiChars = join("", array_map('chr', range(128, 255)));
			$this->_toUtfFailed = false;
			array_walk_recursive($result, array(&$this, '_toUtf8_callback'), $this->SCRIPT_ENCODING);
			if (!$this->_toUtfFailed) {
				// If some key contains non-ASCII character, convert everything manually.
				$text = json_encode($result);
			} else {
				$text = $this->php2js($result);
			}
		} else {
			$text = $this->php2js($result);
		}

		// Content-type header.
		// In XMLHttpRequest mode we must return text/plain - damned stupid Opera 8.0. :(
		$ctype = !empty($this->_contentTypes[$this->LOADER])? $this->_contentTypes[$this->LOADER] : $this->_contentTypes[''];
		header("Content-type: $ctype; charset=$encoding");
		
		if ($this->LOADER != "xml") {
			// In non-XML mode we cannot use plain JSON. So - wrap with JS function call.
			// If top.JsHttpRequestGlobal is not defined, loading is aborted and 
			// iframe is removed, so - do not call dataReady().
			$text = "" 
				. ($this->LOADER == "form"? 'top && top.JsHttpRequestGlobal && top.JsHttpRequestGlobal' : 'JsHttpRequest') 
				. ".dataReady(" . $text . ")\n"
				. "";
			if ($this->LOADER == "form") {
				$text = '<script type="text/javascript" language="JavaScript"><!--' . "\n$text" . '//--></script>';
			}
		}

		return $text;
	}


	/**
	 * Internal function, used in array_walk_recursive() before json_encode() call.
	 * If a key contains non-ASCII characters, this function sets $this->_toUtfFailed = true,
	 * becaues array_walk_recursive() cannot modify array keys.
	 */
	function _toUtf8_callback(&$v, $k, $fromEnc)
	{
		if ($v === null || is_bool($v)) return;
		if ($this->_toUtfFailed || !is_scalar($v) || strpbrk($k, $this->_nonAsciiChars) !== false) {
			$this->_toUtfFailed = true;
		} else {
			$v = $this->_unicodeConv($fromEnc, 'UTF-8', $v);
		}
	}
	

	/**
	 * Decode all %uXXXX entities in string or array (recurrent).
	 * String must not contain %XX entities - they are ignored!
	 */
	function _ucs2EntitiesDecode($data)
	{
		if (is_array($data)) {
			$d = array();
			foreach ($data as $k=>$v) {
				$d[$this->_ucs2EntitiesDecode($k)] = $this->_ucs2EntitiesDecode($v);
			}
			return $d;
		} else {
			if (strpos($data, '%u') !== false) { // improve speed
				$data = preg_replace_callback('/%u([0-9A-F]{1,4})/si', array(&$this, '_ucs2EntitiesDecodeCallback'), $data);
			}
			return $data;
		}
	}


	/**
	 * Decode one %uXXXX entity (RE callback).
	 */
	function _ucs2EntitiesDecodeCallback($p)
	{
		$hex = $p[1];
		$dec = hexdec($hex);
		if ($dec === "38" && $this->SCRIPT_DECODE_MODE == 'entities') {
			// Process "&" separately in "entities" decode mode.
			$c = "&amp;";
		} else {
			if ($this->_unicodeConvMethod) {
				$c = @$this->_unicodeConv('UCS-2BE', $this->SCRIPT_ENCODING, pack('n', $dec));
			} else {
				$c = $this->_decUcs2Decode($dec, $this->SCRIPT_ENCODING);
			}
			if (!strlen($c)) {
				if ($this->SCRIPT_DECODE_MODE == 'entities') {
					$c = '&#' . $dec . ';';
				} else {
					$c = '?';
				}
			}
		}
		return $c;
	}


	/**
	 * Wrapper for iconv() or mb_convert_encoding() functions.
	 * This function will generate fatal error if none of these functons available!
	 * 
	 * @see iconv()
	 */
	function _unicodeConv($fromEnc, $toEnc, $v)
	{
		if ($this->_unicodeConvMethod == 'iconv') {
			return iconv($fromEnc, $toEnc, $v);
		} 
		return mb_convert_encoding($v, $toEnc, $fromEnc);
	}


	/**
	 * If there is no ICONV, try to decode 1-byte characters manually
	 * (for most popular charsets only).
	 */
	 
	/**
	 * Convert from UCS-2BE decimal to $toEnc.
	 */
	function _decUcs2Decode($code, $toEnc)
	{
		if ($code < 128) return chr($code);
		if (isset($this->_encTables[$toEnc])) {
			// TODO: possible speedup by using array_flip($this->_encTables) and later hash access in the constructor.
			$p = array_search($code, $this->_encTables[$toEnc]);
			if ($p !== false) return chr(128 + $p);
		}
		return "";
	}
	

	/**
	 * UCS-2BE -> 1-byte encodings (from #128).
	 */
	var $_encTables = array(
		'windows-1251' => array(
			0x0402, 0x0403, 0x201A, 0x0453, 0x201E, 0x2026, 0x2020, 0x2021,
			0x20AC, 0x2030, 0x0409, 0x2039, 0x040A, 0x040C, 0x040B, 0x040F,
			0x0452, 0x2018, 0x2019, 0x201C, 0x201D, 0x2022, 0x2013, 0x2014,
			0x0098, 0x2122, 0x0459, 0x203A, 0x045A, 0x045C, 0x045B, 0x045F,
			0x00A0, 0x040E, 0x045E, 0x0408, 0x00A4, 0x0490, 0x00A6, 0x00A7,
			0x0401, 0x00A9, 0x0404, 0x00AB, 0x00AC, 0x00AD, 0x00AE, 0x0407,
			0x00B0, 0x00B1, 0x0406, 0x0456, 0x0491, 0x00B5, 0x00B6, 0x00B7,
			0x0451, 0x2116, 0x0454, 0x00BB, 0x0458, 0x0405, 0x0455, 0x0457,
			0x0410, 0x0411, 0x0412, 0x0413, 0x0414, 0x0415, 0x0416, 0x0417,
			0x0418, 0x0419, 0x041A, 0x041B, 0x041C, 0x041D, 0x041E, 0x041F,
			0x0420, 0x0421, 0x0422, 0x0423, 0x0424, 0x0425, 0x0426, 0x0427,
			0x0428, 0x0429, 0x042A, 0x042B, 0x042C, 0x042D, 0x042E, 0x042F,
			0x0430, 0x0431, 0x0432, 0x0433, 0x0434, 0x0435, 0x0436, 0x0437,
			0x0438, 0x0439, 0x043A, 0x043B, 0x043C, 0x043D, 0x043E, 0x043F,
			0x0440, 0x0441, 0x0442, 0x0443, 0x0444, 0x0445, 0x0446, 0x0447,
			0x0448, 0x0449, 0x044A, 0x044B, 0x044C, 0x044D, 0x044E, 0x044F,
		),
		'koi8-r' => array(
			0x2500, 0x2502, 0x250C, 0x2510, 0x2514, 0x2518, 0x251C, 0x2524,
			0x252C, 0x2534, 0x253C, 0x2580, 0x2584, 0x2588, 0x258C, 0x2590,
			0x2591, 0x2592, 0x2593, 0x2320, 0x25A0, 0x2219, 0x221A, 0x2248,
			0x2264, 0x2265, 0x00A0, 0x2321, 0x00B0, 0x00B2, 0x00B7, 0x00F7,
			0x2550, 0x2551, 0x2552, 0x0451, 0x2553, 0x2554, 0x2555, 0x2556,
			0x2557, 0x2558, 0x2559, 0x255A, 0x255B, 0x255C, 0x255d, 0x255E,
			0x255F, 0x2560, 0x2561, 0x0401, 0x2562, 0x2563, 0x2564, 0x2565,
			0x2566, 0x2567, 0x2568, 0x2569, 0x256A, 0x256B, 0x256C, 0x00A9,
			0x044E, 0x0430, 0x0431, 0x0446, 0x0434, 0x0435, 0x0444, 0x0433,
			0x0445, 0x0438, 0x0439, 0x043A, 0x043B, 0x043C, 0x043d, 0x043E,
			0x043F, 0x044F, 0x0440, 0x0441, 0x0442, 0x0443, 0x0436, 0x0432,
			0x044C, 0x044B, 0x0437, 0x0448, 0x044d, 0x0449, 0x0447, 0x044A,
			0x042E, 0x0410, 0x0411, 0x0426, 0x0414, 0x0415, 0x0424, 0x0413,
			0x0425, 0x0418, 0x0419, 0x041A, 0x041B, 0x041C, 0x041d, 0x041E,
			0x041F, 0x042F, 0x0420, 0x0421, 0x0422, 0x0423, 0x0416, 0x0412,
			0x042C, 0x042B, 0x0417, 0x0428, 0x042d, 0x0429, 0x0427, 0x042A
		),
	);
}
?><?
/**
* Operations with file by serial read/write
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.0b
* @created ?2009-03-25 13:51 ver 2.0b
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses file_base
* @uses VariableStream
**/



class file_read extends file_base{
private $fd = null;

protected $_line_no = 0; //Current line number. Read only. For getline() access.

	/**
	* If file opened befor, content will be written in current position of file.
	* If it wasn't opened - open occured.
	* @inheritdoc
	*
	* @param	integer	Append by default if descriptor opened.	FILE_USE_INCLUDE_PATH supported if fd not opened en we open new.
	* @param	resource	$resource_context See {@link http://php.net/stream-context-create}.
	*	Used only if file opened here (was NOT opened before)
	* @return	integer	Count of written bytes
	**/
	public function writeContent($flags = null, $resource_context = null){
		if (!$this->fd) $this->open('w', ( $flags & FILE_USE_INCLUDE_PATH ), $resource_context);

		/*
		* To provide consistence API and do not fake incoming method parameters we must use streams.
		* There present function stream_get_contents, but I not found opposite, which can write string to stream.
		* My decision to use stream_copy_to_stream() function, but for that I must have another stream to copy from.
		* I not found standard way to map variable on stream, so, use VariableStream in conditional of global temp variable
		*
		* Another possible way, may be using 'php://memory' or 'php://temp' (http://mikenaberezny.com/2008/10/17/php-temporary-streams/),
		* but in this case full variable data must be explicid copyed in this stream.
		* VariableStream with variable reference give mor migick on my opinion.
		*/
		$GLOBALS['__tmp_content_var_stream'] =& $this->content;
		$this->checkOpenError(
			// $this->rawFilename because may be file generally not exists!
			(bool)( $count = @stream_copy_to_stream($this->fd, ($tfd = fopen('var://__tmp_content_var_stream'))) )
		);

		$this->_writePending = false;
		fclose($tfd);
		return $count;
	}#m writeContent

	/// Self introduced methods ///

	/**
	* Open file for reading/writing (according to $mode)
	*
	* @param	string	$mode. See {@link http://php.net/fopen}
	* @param	boolean	$use_include_path
	* @param	resource	$zcontext  See {@link http://php.net/fopen}
	**/
	public function open($mode, $use_include_path = false , $zcontext = null){
		$this->checkOpenError(
			(bool)(
				$zcontext
				?
				($this->fd = fopen($this->path(), $mode, $use_include_path, $zcontext))
				:
				($this->fd = fopen($this->path(), $mode, $use_include_path))
			)
		);
		$this->lineContent = array();
		$this->content = '';
	}#m open

	/**
	* Get next line from stream.
	*
	* @param	integer $length. Optional - maximum length of string. If null - all string returned (by default).
	* @return	string
	* @Throws(VariableRequiredException)
	**/
	public function getline($length = null){
		++$this->_line_no;
		return $length ? fgets(REQUIRED_VAR($this->fd), $length) : fgets(REQUIRED_VAR($this->fd));
	}#m getline

	/**
	* Return current line number in getline() mode access.
	*
	* WARNING! Please keep in mind, it is not provide reliable interface to calculate real lines.
	* In current implementation by the fact it reflect count of invokes method ::getline() only!!!
	*
	* @return	integer
	**/
	function lineNo(){
		return $this->_line_no;;
	}#m lineNo

	/**
	* Return tail of stream as string.
	*
	* {@link http://php.net/stream-get-contents}
	*
	* @param	integer	$maxlength
	* @param	offset	$offset
	* @return	string
	**/
	public function getTail ($maxlength = -1, $offset = 0){
		return stream_get_contents($this->fd, $maxlength, $offset);
	}#m getTail
}#c file_read
?><?
/**
* Operations with file in memory.
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.0.1b
* @created ?2009-03-25 13:51 ver 2.0b
*
* @uses REQUIRED_VAR
* @uses VariableRequiredException
* @uses file_base
**/



class file_inmem extends file_base{
private $lineContent = null;

private $_lineSep = "\n";		// Unix by default

private $_linesOffsets = array();	// Cache For ->getLineByOffset and ->getOffsetByLine methods

	/**
	* Load full content of file into memmory.
	*
	* If file very big consider read it for example by lines, if task allow it.
	* @todo Split 2 such approach into child classes
	*
	* @param	boolean	$use_include_path
	* @param	resource	$resource_context
	* @param	integer	$offset
	* @param	integer	$maxlen
	* @return	&$this
	**/
	public function &loadContent($use_include_path = false, $resource_context = null, $offset = null, $maxlen = null){
		$this->checkOpenError(
			false !== (
				$maxlen
				?
				($this->content = file_get_contents($this->path(), $use_include_path, $resource_context, $offset, $maxlen))
				:
				($this->content = file_get_contents($this->path(), $use_include_path, $resource_context, $offset))
			)
		);
		$this->lineContent = array();
		$this->_linesOffsets = array();
		return $this;
	}#m loadContent

	/**
	* @inheritdoc
	**/
	public function &setContentFromString($string){
		$this->lineContent = array();
		$this->_linesOffsets = array();
		return parent::setContentFromString($string);
	}#m setContentFromString

	/**
	* Partial write not supported, reset full string to resplit by lines it in future.
	* @inheritdoc
	* @Throws(VariableRequiredException)
	**/
	public function &appendString($string){
		return $this->setContentFromString($this->content . REQUIRED_VAR($string));
	}#m appendString

	/**
	* @inheritdoc
	*
	* Additional parameters are:
	* @param	string	$implodeWith See descr ->implodeLines()
	* @param	boolean	$updateLineSep See descr ->implodeLines()
	**/
	public function writeContent($flags = null, $resource_context = null, $implodeWith = null, $updateLineSep = true){
		$this->checkOpenError(
			// $this->rawFilename because may be file generally not exists!
			false !==  ($count = @file_put_contents($this->path(), $this->getBLOB($implodeWith, $updateLineSep), $flags, $resource_context))
		);
		$this->_writePending = false;
		return $count;
	}#m writeContent

	/// Self introduced methods ///

	/**
	* Return array of specified lines or all by default
	*
	* @param	array $lines. If empty array - whole array of lines. Else
	*	Array(int $offset  [, int $length  [, bool $preserve_keys  ]] ). See http://php.net/array_slice
	* @param	boolean(true) $updateLineSep. See explanation in ->explodeLines() method.
	* @return	array Array of lines
	* @Throw(VariableEmptyException)
	**/
	public function getLines(array $lines = array(), $updateLineSep = true){
		$this->checkLoad();
		if (!$this->lineContent) $this->explodeLines($updateLineSep);

		if(!empty($lines)) return call_user_func_array('array_slice', array_merge(array( 0 => $this->lineContent), $lines) );
		else return $this->lineContent;
	}#m getLines

	/**
	* Explode loaded content to lines.
	*
	* @param	boolean $updateLineSep if true - update lineSep by presented in whole content.
	**/
	protected function explodeLines($updateLineSep = true){
		preg_match_all('/(.*?)([\n\r])/', $this->content, $this->lineContent, PREG_PATTERN_ORDER);
		if ($updateLineSep) $this->_lineSep = $this->lineContent[2][0/*Any realy. Assuming all equal.*/];
		$this->lineContent = $this->lineContent[1];
		$this->_linesOffsets = array();
	}#m explodeLines

	/**
	* Implode lineContent to whole contents.
	*
	* @param	string	$implodeWith String implode with. If null, by default - ->_lineSep.
	* @param	boolean	$updateLineSep if true - update lineSep by presented $implodeWith.
	**/
	protected function implodeLines($implodeWith = null, $updateLineSep = true){
		if ($implodeWith and $updateLineSep) $this->setLineSep($implodeWith);
		$this->_linesOffsets = array();
		return ($this->content = implode($implodeWith, $this->lineContent)); //Set or not, implode as requested.
	}#m implodeLines

	/**
	* Return string of content
	*
	* @param	string	$implodeWith See descr ->implodeLines()
	* @param	boolean	$updateLineSep See descr ->implodeLines()
	* @return	string
	**/
	public function getBLOB($implodeWith = null, $updateLineSep = true){
		if (
			! $this->content
			or
			( $implodeWith and $implodeWith != $this->_lineSep)
		)
		$this->implodeLines($implodeWith, $updateLineSep);
		return $this->content;
	}#m getBLOB

	/**
	* Get current used line separator.
	* @return	string
	**/
	public function getLineSep() {
		return $this->_lineSep;
	}#m getLineSep

	/**
	* Set new line separator.
	*
	* It also may be used to convert line separators like:
	* $f = new file_inmem('filename');
	* $f->setLineSep("\r\n")->loadContent()->setLineSep("\n")->writeContent();
	*	or even more easy:
	* $f->setLineSep("\r\n")->loadContent()->->writeContent(nul, null, "\n");
	*
	* @param	string	$newSep
	* @return	&$this
	**/
	public function &setLineSep($newSep) {
		$this->_leneSep = $newSep;
		$this->_linesOffsets = array();
		return $this;
	}#m getLineSep

	/**
	* Return line with requested number.
	*
	* Boundaries NOT checked!
	*
	* @param	int	$line
	* @return	string
	**/
	public function getLineAt($line){
		if (!$this->lineContent) $this->explodeLines($updateLineSep);
		return $this->lineContent[$line];
	}#m getLineAt

	/**
	* Calculate line number by file offset.
	*
	* @param	integer	$offset
	* @return	integer
	* @Throw(VariableRangeException)
	**/
	public function getLineByOffset($offset){
		if (!$this->_linesOffsets) $this->makeCacheLineOffsets();
		if ($offset > $this->_linesOffsets[sizeof($this->_linesOffsets)-1][1])
		throw new VariableRangeException('Overflow! This offset does not exists.');

		// Data ordered - provide binary search as fast alternative to array_search
		$size = sizeof($this->_linesOffsets) - 1; // For speed up only
		$left = 0; $right = $size;	// Points of interval
		$found = false;
		$line = ceil($size / 2);

		/*
		* Boundary conditions. Additional check of lowest value is mandatory, if used ceil() (0 is not accessible).
		* Additional check of highest value addad only to efficient
		* adjusting, because on it point the maximum time for the
		* convergence of the algorithm
		**/
		if ($offset >= $this->_linesOffsets[0][0] and $offset <= $this->_linesOffsets[0][1])
			return 0;

		if ($offset >= $this->_linesOffsets[$size][0] and $offset <= $this->_linesOffsets[$size][1])
			return $size;

		do{
			if ( $offset >= $this->_linesOffsets[$line][0] ){
				if ( $offset <= $this->_linesOffsets[$line][1] ){
					$found = true; // done
				}
				else{
					$left = $line;
					$line += ceil( ($right - $line) / 2 );
				}
			}
			else{
				$right = $line;
				$line -= ceil( ($line - $left) / 2);
			}
		} while(!$found);

		if ($found === true) return $line;
		else return false;
	}#m getLineByOffset

	/**
	* Opposit to {@see ::getLineByOffset()} returm offset of line begin.
	*
	* @param	integer	$line
	* @return	array(OffsetBeginLine, OffsetEndLine). In OffsetEndLine included length of ->_lineSep!
	**/
	public function getOffsetByLine($line){
		if (!$this->_linesOffsets) $this->makeCacheLineOffsets();
		if ($line >= sizeof($this->_linesOffsets)) throw new VariableRangeException('Overflow! This line does not exists.');

		return $this->_linesOffsets[$line];
	}#m getOffsetByLine

	/**
	* Check loaded content is not empty. Throw exception otherwise.
	*
	* @return	&this
	* @Throw(VariableEmptyException)
	**/
	private function &checkLoad(){
		if (empty($this->lineContent) and empty($this->content))
		throw VariableEmptyException('Line-Content and Content is empty! May be you forgot call one of ->load*() method first?');
		return $this;
	}#m checkLoad

	/**
	* Make cache of lines and its offsets.
	**/
	private function makeCacheLineOffsets(){
		$this->_linesOffsets = array();
		$offset = 0;
		$lines = $this->getLines();

		$linesCount = sizeof($lines);
		// First line is additional case
		$this->_linesOffsets[0] = array($offset, ($offset += -1 + strlen(utf8_decode($lines[0])) + strlen(utf8_decode($this->getLineSep()))) );
		// From 1 line, NOT 0
		for($i = 1; $i < $linesCount; $i++){
			$this->_linesOffsets[$i] = array(
				$offset + 1,
				( $offset += strlen(utf8_decode($lines[$i])) + strlen(utf8_decode($this->getLineSep())) )
			);
		}
	}#m makeCacheLineOffsets

	/**
	* Iconv content from one charset to enother. If in charset is not known consider use method {@see ::enconv()}
	*
	* @uses iconv
	* @param	string	$fromEnc
	* @param	string=UTF-8	$toEnc
	* @return	&$this
	**/
	public function &iconv($fromEnc, $toEnc = 'UTF-8'){
		$this->setContentFromString(iconv($fromEnc, $toEnc, $this->getBLOB()));
		return $this;
	}#m iconv

	/**
	* Uses shell execute enconv to guess encoding and convert it to desired
	*
	* @uses Process
	* @uses shell enconv
	* @param	string=russian	$lang
	* @param	string=UTF-8	$toEnc
	* @return	&$this;
	**/
	public function &enconv($lang = 'russian', $toEnc = 'UTF-8'){
		$this->setContentFromString(Process::exec("enconv -L $lang -x $toEnc", null, null, $this->getBLOB()));
		return $this;
	}#m enconv
}#c file_inmem
?><?
/**
* Base file operations.
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.1
* @created ?2008-08-27 ver 1.0 to 1.1
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses FileNotExistsException
* @uses FileNotReadableException
* @uses OS
**/




/**
* Base class for most file-related operations.
**/
class file_base{
	private $filename = '';
	private $rawFilename = ''; //Filename to try open. For error-reports.
	private $dir = '';

	protected $_writePending = false;

	/** Pending content for write **/
	protected	$content;

	/**
	* Construct new object with provided (optional) path (URL).
	*
	* @param	string	$filename
	**/
	public function __construct($filename = ''){
		if ($filename) $this->setPath($filename);
	}#__c

	/**
	* Write all pendings write if it wasn't be done manually before. This is to avoid data loss.
	**/
	public function __destruct(){
		if ($this->_writePending) $this->writeContent();
	}#__d

	/**
	* Set new path. For example to writing new file.
	*
	* @param	string	$filename	New filename
	* @return	&$this
	**/
	public function &setPath($filename){
		$this->filename = $this->rawFilename = $filename;
		/**
		* And we MUST set full path in ->filename because after f.e. chdir(...) relative path may change sense.
		* Additionally, in __destruct call to getcwd return '/'!!! {@See http://bugs.php.net/bug.php?id=30210}
		**/
		// We can't direct use $this->filename instead of $realpath because if it ! we not always want null it!
		if (!($realpath = realpath($this->rawFilename))){
			/**
			* Realpath may fail because file not found. But we can't agree with that,
			* because setPath may be invoked to set path for write new (create) file!
			* So, we try manually construct current full path (see abowe why we should do it)
			**/
			if (! OS::isPathAbsolute($this->rawFilename)){
				$this->filename = getcwd() . DIRECTORY_SEPARATOR . $this->rawFilename;
			}
		}
		else $this->filename = $realpath;
		return $this;
	}#m setPath

	/**
	* Return curent path
	*
	* @return	string
	**/
	public function path(){
		return $this->filename;
	}#m path

	/**
	* Return curent RAW (what wich be passed into the {@see setPath()}, without any transformation) path.
	*
	* @return	string
	**/
	public function rawPath(){
		return $this->rawFilename;
	}#m rawPath

	/**
	* Return true if current set path is exists.
	*
	* @return	boolean
	**/
	public function isExists(){
		// Very strange: file_exists('') === true!!!
		return ('' != $this->path() and file_exists($this->path()));
	}#m isExists

	/**
	* Return true, if file on current path is readable.
	*
	* @return	boolean
	**/
	public function isReadable(){
		return is_readable($this->path());
	}#m isReadable

	/**
	* Unlink (delete) file
	*
	* @return>boolean
	**/
	public function unlink(){
		return unlink($this->path());
	}#m unlink

	/**
	* Return directory part of current path (file must not be exist!).
	*
	* @return	string
	**/
	public function getDir(){
		return dirname($this->path());
	}#m getDir

	/**
	* Clear pending writes.
	*
	* @return	&$this
	**/
	public function &clearPendingWrite(){
		$this->_writePending = false;
		return $this;
	}#m clearPendingWrite

	/**
	* Set content for write.
	*
	* @param string	$string. String to set from.
	* @return &$this
	* @Throws(VariableRequiredException)
	**/
	public function &setContentFromString($string){
		$this->content = REQUIRED_NOT_NULL($string);
		$this->_writePending = true;
		return $this;
	}#m setContentFromString

	/**
	* Append string to pending write buffer.
	*
	* @param	string	$string. String to append from.
	* @return	&$this
	* @Throw(VariableRequiredException)
	**/
	public function &appendString($string){
		$this->content += REQUIRED_VAR($string);
		$this->_writePending = true;
		return $this;
	}#m appendString

	/**
	* Write whole content to file (filename may be set via ->setPath('NewFileName'))
	*
	* @param	integer	flags See {@link http://php.net/file_put_contents}
	* @param	resource	$resource_context See {@link http://php.net/stream-context-create}
	* @return	integer	Count of written bytes
	**/
	public function writeContent($flags = null, $resource_context = null){
		$this->checkOpenError(
			// $this->rawFilename because may be file generally not exists!
			false !== ($count = @file_put_contents($this->path(), $this->content, $flags, $resource_context))
		);

		$this->_writePending = false;
		return $count;
	}#m writeContent

	/// private functions ///

	protected function checkOpenError($succ){
		if ( ! $succ ){
			if (!$this->isExists()) throw new FileNotExistsException('File not found', $this->path());
			if (!$this->isReadable()) throw new FileNotReadableException('File not readable. Check permissions.', $this->path());
			throw new FileNotReadableException('Unknown error operate on file.', $this->path());
		}
	}#m checkOpenError
}#c file_base
?><?php
/*
FROM http://web.archive.org/web/20060519194518/http://www.liacs.nl/~dvbeelen/MIME.php.txt

     MIME.php, provides functions for determining MIME types and getting info about MIME types
     Copyright (C) 2003 Arend van Beelen, Auton Rijnsburg

     This library is free software; you can redistribute it and/or
     modify it under the terms of the GNU Lesser General Public
     License as published by the Free Software Foundation; either
     version 2.1 of the License, or (at your option) any later version.

     This library is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
     Lesser General Public License for more details.

     You should have received a copy of the GNU Lesser General Public
     License along with this library; if not, write to the Free Software
     Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

     For any questions, comments or whatever, you may mail me at: arend@auton.nl
*/

class MIME
{
	function __construct()
	{
		$this->XDG_DATA_DIRS = explode(':', (isset($_ENV['XDG_DATA_DIRS'])?$_ENV['XDG_DATA_DIRS']:'/usr/local/share/:/usr/share/'));
	}

	// tries to determine the mimetype of the given file
	// if the second variable is false, the file won't be opened and magic checking will be skipped
	static function type($filename, $openfile = true)
	{
		global $MIME;

		$mimetype = '';
		$matchlen = 0;

		$basename = basename($filename);

		// load the glob files if they haven't been loaded already
		if(!isset($MIME->globFileLines))
		{
			$MIME->globFileLines = array();

			// go through the data dirs to search for the globbing files
			foreach($MIME->XDG_DATA_DIRS as $dir)
			{
				// read the file
				if(file_exists("$dir/mime/globs") &&
				   ($lines = file("$dir/mime/globs")) !== false)
				{
					$MIME->globFileLines = array_merge($MIME->globFileLines, $lines);
				}
			}
		}

		// check the globs twice (both case sensitive and insensitive)
		for($i = 0; $i < 2; $i++)
		{
			// walk through the file line by line
			foreach($MIME->globFileLines as $line)
			{
				// check whether the line is a comment
				if($line{0} == '#')
					continue;

				// strip the newline character, but leave any spaces
				$line = substr($line, 0, strlen($line) - 1);

				list($mime, $glob) = explode(':', $line, 2);

				// check for a possible direct match
				if($basename == $glob)
					return $mime;

				// match the globs
				$flag = ($i > 0 ? FNM_CASEFOLD : 0);
				if(fnmatch($glob, $basename, $flag) == true && strlen($glob) > $matchlen)
				{
					$mimetype = $mime;
					$matchlen = strlen($glob);
				}
			}
		}

		// check for hits
		if($mimetype != '')
			return $mimetype;

		// if globbing didn't return any results we're going to do some magic
		// quit now if we may not or cannot open the file
		if($openfile == false || ($fp = fopen($filename, 'r')) == false)
			return '';

		// load the magic files if they weren't loaded yet
		if(!isset($MIME->magicRules))
		{
			$MIME->magicRules = array();

			// go through the data dirs to search for the magic files
			foreach(array_reverse($MIME->XDG_DATA_DIRS) as $dir)
			{
				// read the file
				if(!file_exists("$dir/mime/magic") ||
				   ($buffer = file_get_contents("$dir/mime/magic")) === false)
					continue;

				// check the file type
				if(substr($buffer, 0, 12) != "MIME-Magic\0\n")
					continue;

				$buffer = substr($buffer, 12);

				// go through the entire file
				while($buffer != '')
				{
					if($buffer{0} != '[' && $buffer{0} != '>' &&
					   ($buffer{0} < '0' || $buffer{0} > '9'))
						break;

					switch($buffer{0})
					{
						// create an entry for a new mimetype
						case '[':
							$mime = substr($buffer, 1, strpos($buffer, ']') - 1);
							$MIME->magicRules[$mime] = array();
							$parents[0] =& $MIME->magicRules[$mime];
							$buffer = substr($buffer, strlen($mime) + 3);
							break;

						// add a new rule to the current mimetype
						case '>':
						default:
							$indent = ($buffer{0} == '>' ? 0 : intval($buffer));
							$buffer = substr($buffer, strpos($buffer, '>') + 1);
							$parents[$indent][] = new MIME_MagicRule;
							$rulenum = sizeof($parents[$indent]) - 1;
							$parents[$indent][$rulenum]->start_offset = intval($buffer); $buffer = substr($buffer, strpos($buffer, '=') + 1);
							$value_length = 256 * ord($buffer{0}) + ord($buffer{1}); $buffer = substr($buffer, 2);
							$parents[$indent][$rulenum]->value = substr($buffer, 0, $value_length); $buffer = substr($buffer, $value_length);
							$parents[$indent][$rulenum]->mask = ($buffer{0} != '&' ? str_repeat("\xff", $value_length) : substr($buffer, 1, $value_length)); if($buffer{0} == '&') $buffer = substr($buffer, $value_length + 1);
							$parents[$indent][$rulenum]->word_size = ($buffer{0} != '~' ? 1 : intval(substr($buffer, 1))); while($buffer{0} != '+' && $buffer{0} != "\n" && $buffer != '') $buffer = substr($buffer, 1);
							$parents[$indent][$rulenum]->range_length = ($buffer{0} != '+' ? 1 : intval($buffer)); $buffer = substr($buffer, strpos($buffer, "\n") + 1);
							$parents[$indent][$rulenum]->children = array();
							$parents[$indent + 1] =& $parents[$indent][$rulenum]->children;
							break;
					}
				}
			}

			// sort the array so items with high priority will get on top
			ksort($MIME->magicRules);
			$magicRules = array_reverse($MIME->magicRules);
			reset($MIME->magicRules);
		}

		// call the recursive function for all mime types
		foreach($MIME->magicRules as $mime => $rules)
		{
			foreach($rules as $rule)
			{
				if($MIME->applyRecursiveMagic($rule, $fp) == true)
				{
					list($priority, $mimetype) = explode(':', $mime, 2);
					fclose($fp);
					return $mimetype;
				}
			}
		}

		// nothing worked, I will now only determine whether the file is binary or text
		fseek($fp, 0);
		$length = (filesize($filename) > 50 ? 50 : filesize($filename));
		$data = fread($fp, $length);
		fclose($fp);
		for($i = 0; $i < $length; $i++)
		{
			if($data{$i} < "\x20" && $data{$i} != "\x09" && $data{$i} != "\x0a" && $data{$i} != "\x0d")
			{
				return 'application/octet-stream';
			}
		}
		return 'text/plain';
	}

	// apply the magic rules recursivily -- helper function for type()
	private function applyRecursiveMagic(MIME_MagicRule $rule, $fp)
	{
		global $MIME;

		fseek($fp, $rule->start_offset);
		$data = fread($fp, strlen($rule->value) + $rule->range_length);
		if(strstr($data, $rule->value) !== false)
		{
			if(sizeof($rule->children) == 0)
			{
				return true;
			}
			else
			{
				foreach($rule->children as $child)
				{
					if($MIME->applyRecursiveMagic($child, $fp) == true)
					{
						return true;
					}
				}
			}
		}
		return false;
	}

	// gets the textual description of the mimetype, optionally in the specified language
	static function description($mimetype, $language = 'en')
	{
		global $MIME;

		$MIME->description = '';
		$MIME->lang = $language;
		$MIME->read = false;

		// go through the data dirs to search for the XML file for the specified mime type
		foreach($MIME->XDG_DATA_DIRS as $dir)
		{
			$filename = "$dir/mime/$mimetype.xml";

			// open the XML file
			if(!file_exists($filename) ||
			   ($fp = fopen($filename, 'r')) == false)
				continue;

			// initialize XML parser
			$xml_parser = xml_parser_create();
			xml_set_element_handler($xml_parser, array($MIME, 'description_StartElement'), array($MIME, 'description_EndElement'));
			xml_set_character_data_handler($xml_parser, array($MIME, 'description_Data'));

			// read the file and parse
			while($data = str_replace("\n", "", fread($fp, 4096)))
			{
				if(!xml_parse($xml_parser, $data, feof($fp)))
				{
					error_log("ERROR: Couldn't parse $filename: ".
					          xml_error_string(xml_get_error_code($xml_parser)));
					break;
				}
			}
			fclose($fp);
		}

		return $MIME->description;
	}

	// helper function for description()
	private function description_StartElement($parser, $name, $attrs)
	{
		$this->read = false;
		if($name == 'COMMENT')
		{
			if(!isset($attrs['XML:LANG']) || $attrs['XML:LANG'] == $this->lang)
			{
				$this->read = true;
			}
		}
	}

	// helper function for description()
	private function description_EndElement($parser, $name)
	{
		$this->read = false;
	}

	// helper function for description()
	private function description_Data($parser, $data)
	{
		if($this->read == true)
		{
			$this->description = $data;
		}
	}

	private $XDG_DATA_DIRS;
	private $globFileLines;
	private $magicRules;
	private $description;
	private $lang;
	private $read;
}

// helper class for MIME::type()
class MIME_MagicRule
{
	var $start_offset;
	var $value;
	var $mask;
	var $word_size;
	var $range_length;
	var $children;
}

// create one global instance of the class
$MIME = new MIME;

?>
<?
/**
* VariablesExceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.1
* @created ?2008-05-29 17:51 ver 2.0b to 2.1
*
* @uses BaseException
* @uses backtrace
**/

class VariableException extends BaseException{};

/**
* @TODO Rewrite to use internal Exception backtrace
**/
class VariableRequiredException extends VariableException{
	public $bt = null;
	private $var = null;

	private $tok_ = null;

	public function __construct(backtrace &$bt, $varname = null, $message = null, $code = 0) {
		$this->bt = $bt;
		$this->var= $varname;

		parent::__construct($message, $code);
	}#c

	/**
	* Return varname
	*
	* @param bool	$noTokenize=false Not try get parameter if it not provided directly.
	* @return string
	**/
	public function varName($noTokenize = false){
		if ($noTokenize){
			return $this->var;
		}

		if ($this->var) return $this->var;

		return $this->getTokenizer()->getArg(0);
	}

	/**
	* Get Tokenizer object, suited to backtrace with instantiated exception.
	* Also create object if it is not exists as yet.
	*
	* @return Object(Tokenizer)
	**/
	public function &getTokenizer(){
		if (!$this->tok_){
			/*-inc
			if (!class_exists('Tokenizer')){
				if(@$__CONFIG['debug']['parseCallParam'] or !@NO_DEBUG){
				
				}
			}
			*/

			$this->tok_ = Tokenizer::create(
				$this->bt->getNode(0)
			)->parseCallArgs();
		}

	return $this->tok_;
	}
};

class VariableEmptyException		extends VariableRequiredException{}
class VariableIsNullException		extends VariableRequiredException{}

class VariableRangeException		extends VariableException{}
/** Greater than */
class VariableRangeGTException	extends VariableRangeException{}
/** Less than */
class VariableRangeLTException	extends VariableRangeException{}

class VariableArrayInconsistentException extends VariableException{}

class VariableReadOnlyException	extends VariableException{}
?><?
/*-inc

*/
/**
* @uses BaseException
**/

class SerializeException extends BaseException{}
?><?
/**
* ProcessException
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/*-inc

*/
/**
* @uses BaseException
**/

class ProcessException extends BaseException{
public $state = null;

	public function __construct($message = null, $code = 0, $pr) {
	$this->state = $pr;

	// make sure everything is assigned properly
	parent::__construct($message, $code);
	}#c
};
?><?
/*-inc

*/
/**
* @uses BaseException
**/

class NetworkException extends Exception{}

class SocketOpenException extends NetworkException{}
class SocketReadException extends NetworkException{}
class SocketReadTimeoutException extends SocketReadException{}
class SocketWriteException extends NetworkException{}
class SocketWriteTimeoutException extends SocketWriteException{}
?><?
/**
* FileSystem Exceptions
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/*-inc

*/
/**
* @uses BaseException
**/

class FilesystemException extends BaseException{
protected $fullPath = '';

	function __construct($message, $fullPath){
	$this->fullPath = $fullPath;
	parent::__construct($message);
	}

	// custom string representation of object
	public function __toString(){
	return __CLASS__ . ": [{$this->fullPath}]: {$this->message}\n";
	}
}

class RemoteGetException extends FilesystemException{}

class FileLoadErrorException extends FilesystemException{}
class FileNotReadableException extends FileLoadErrorException{}
class FileNotExistsException extends FileLoadErrorException{}

?><?
/*-inc

*/
/**
* @uses BaseException
**/

class WrongStringDateException extends BaseException{}
class WrongStringModifyDateException extends WrongStringDateException{}
?><?
/**
* Database exceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1
* @created ?2009-03-10 07:55 ver 1.0 to 1.1
*
* @uses BaseException
**/

class DBException extends BaseException{
	public $db;

	public function __construct($message, database &$db){
		parent::__construct($message);
		$this->db = $db;
	}#__c
}

class ConnectErrorDBException extends DBException{
	public $DBError;//Ref to database_error object
}
class DBSelectErrorDBException extends DBException{}
class QueryFailedDBException extends DBException{}

class DBnullGetException extends DBException{}//Got empty results from DB
?><?
/**
* ClassExceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1
* @created ?2008-05-31 5:26 ver 1.0 to 1.1
*
* @uses BaseException
**/

class ClassException extends BaseException{}

class ClassUnknownException extends ClassException{}
class ClassNotExistsException extends ClassException{}
class ClassMethodExistsException extends ClassException{}
class ClassPropertyNotExistsException extends ClassException{}
?><?
/**
* Charset encoding suite
*
* @package charset_convert
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ???
*
* @uses BaseException
**/

class charset_convert_exception extends BaseException{}
?><?
/*-inc

*/
/**
* @uses BaseException
**/

class SessionException extends BaseException{}
?><?
/**
* O-Range-SMS-partnerAPI ralated exceptions.
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created ?2009-03-09 20:58
*
* @uses BaseException
**/

class MSG_partnerAPIException extends BaseException{}

class MSG_AuthErrorException extends MSG_partnerAPIException{}
class MSG_SendFailException extends MSG_partnerAPIException{}
class MSG_InParseErrorException extends MSG_partnerAPIException{}
?><?
/**
* BaseException
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

class BaseException extends Exception{
	// $pos = false - at end, else - in begining
	public function ADDMessage($addmess, $pos = false){
		if (!$pos) $this->message .= $addmess;
		else $this->message = $addmess.$this->message;
	}
}

class NotImplementedException extends BaseException{};
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage log_dump
* @version 2.2b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-05-29 17:35
**/

 //Function used. Must be included explicit.
/**
* @uses log_dump()
**/

/**
* Log dump. Useful to return string for file-write.
*
* @param	mixed $var Variable (or scalar) to dump.
* @param string|false	$header. Header to prepend dump of $var.
* @param boolean		$return If true - return result as string instead of echoing.
* @return string|void	Depend on parameter $return
**/
function log_dump($var, $header = false, $return = true){
	$ret = '';
	if ($header) $ret .= $header .':'; //As is and only explicitly given, without any magic
	$ret .= dump_utils::transformCorrect_print_r(print_r($var, true))."\n";
	if ($return) return $ret;
	else echo $ret;
}#f log_dump

	if (
		!class_exists('dump')
		or
			(
			!defined('DUMP_DO_NOT_DEFINE_STUMP_DUMP')
			and DUMP_DO_NOT_DEFINE_STUMP_DUMP
			)
	){
		class dump extends dump_utils{
			function log($var, $header = false, $return = true){
			return log_dump($var, $header = false, $return = true);
			}
		};
	}
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ???
**/

class gentime{
	var $time_start;

	function start(){
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->time_start = $mtime;
	}

	function stop(){
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		return sprintf ("%f", ($mtime - $this->time_start));// Seconds
	}
	
	function bench($code, $iteration = 1000){
		ob_start();
		$sum_time = 0;
		$min_time = 100;
		$max_time = 0;

		for ($i=0; $i<$iteration; $i++){
			$this->start();
			eval($code);
			$cur_time = $this->stop('noprint');
			$sum_time += $cur_time;
			if ($cur_time > $max_time) $max_time = $cur_time;
			if ($cur_time < $min_time) $min_time = $cur_time;
		}

		ob_end_clean();
		eval($code); // to out
		printf ("<br>Максимальное время %f секунд<br><b>Среднее время %f</b><br>Минимальное время %f<br>", $max_time, $sum_time/$iteration, $min_time);
	}
}//c gentime
?><?
/**
* Debug and backtrace toolkit.
* @package Debug
* @subpackage Dump-utils
* @version 2.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-06-26 03:58
**/

class dump_utils{
	/**
	* Transform string, which is text-representation of requested var into more well formated form.
	* print_r variant
	* @param string $dump String returned by print_r
	* @return string. Transformed, well-formated.
	**/
	static public function transformCorrect_print_r($dump){
	return
		trim(
			preg_replace(
				array(
					'/Array\n\s*\(/',
					'/Object\n\s*\(/',
					'/\["(.+)"\]=>\n /',
					'/Array(0){\s+}/',
				),
				array(
					'Array(',
					'Object(',
					'[\1]=>',
					'Array(0){}',
				),
				$dump
			)
		);
	}#m transformCorrect_print_r

	/**
	* Transform string, which is text-representation of requested var into more well formated form.
	* var_dump variant
	* @param string $dump String returned by var_dump
	* @return string. Transformed, well-formated.
	**/
	static public function transformCorrect_var_dump($dump){
	return
		trim(/* For var_dump variant */
			preg_replace(
				array(
					'/array(\(\d+\))\s+({)/i',
					'/Object\n\s*\(/',
					'/\["?(.+?)"?\]=>\n\s*/',
				),
				array(
					'Array\1\2',
					'Object(',
					'[\1] => ',
				),
				$dump
			)
		);
	}#m transformCorrect_var_dump
}; #c dump_utils
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage Debug
* @version 2.4.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-29 15:58 Version 2.3 from 2.2.b
**/

define ('DUMP_DO_NOT_DEFINE_STUMP_DUMP', true);


	//Avoid warning
	if (!array_key_exists('__CONFIG', $GLOBALS) or !array_key_exists('debug', $GLOBALS['__CONFIG'])) $GLOBALS['__CONFIG']['debug'] = array();

	/**
	* @internal
	* Even here, used directly $GLOBALS, because it may be included in other scope (e.g. from function)
	*
	* We MUST use direct array_key_exists instead if isset (or even is_set) check because, according
	*	to MAN: "isset() will return FALSE if testing a variable that has been set to NULL", but we really want known fact of presence.
	**/
	if (!array_key_exists('parseCallParam', $GLOBALS['__CONFIG']['debug'])){
		/**
		* Parsing what parameters present at call time.
		* For example:
		* dump::c($ttt)
		* is equivalent to
		* dump::c($ttt, '$ttt')
		* This future is very usefull, but require Tokenizer class and got time overhead.
		* @global	integer	$GLOBALS['__CONFIG']['debug']['parseCallParam']
		**/
		$GLOBALS['__CONFIG']['debug']['parseCallParam'] = true;
	}

	if (!array_key_exists('errorReporting', $GLOBALS['__CONFIG']['debug'])){
		/**
		* Set error_reporting to this value.
		* Null has special means - no change!
		* @global	integer	$GLOBALS['__CONFIG']['debug']['errorReporting']
		**/
		$GLOBALS['__CONFIG']['debug']['errorReporting'] = E_ALL;
	}

	if (null !== $GLOBALS['__CONFIG']['debug']['errorReporting']){
		error_reporting($GLOBALS['__CONFIG']['debug']['errorReporting']);
	}

	if (!array_key_exists('display_errors', $GLOBALS['__CONFIG']['debug'])){
		/**
		* Enable or disable global errors reporting.
		* @global	integer	$GLOBALS['__CONFIG']['debug']['display_errors']
		**/
		$GLOBALS['__CONFIG']['debug']['display_errors'] = 1;
	}

	if (null !== $GLOBALS['__CONFIG']['debug']['display_errors']){
		ini_set('display_errors', $GLOBALS['__CONFIG']['debug']['display_errors']);
	}

	if ($GLOBALS['__CONFIG']['debug']['parseCallParam']){
		
		
	}

/**
* @package Debug
* Mainly for emulate namespace for old PHP versions
* Most (all?) methods are static
**/
class dump extends dump_utils{
	/**
	* Return $header. If in $header present - return as is, else make guess as real be invoked.
	*
	* @param &mixed $header. Be careful! By default, in parent methods like dump::*() $header=false!
	*	If passed $header === null it allows distinguish what it is not passed by default or
	*	it is not needed!!
	* @return &mixed $var
	**/
	static public function getHeader(&$header, &$var){
		if ($header){
			return $header;
		}
		elseif(
			//Be careful! Null, NOT false by default in dump::*()! It allows distinguish what it is
			//not passed by default or it is not needed!!
			$header !== null
			and $GLOBALS['__CONFIG']['debug']['parseCallParam']
			and
			(
				$cp = Tokenizer::trimQuotes(
					Tokenizer::create(
						backtrace::create()->find(
							backtraceNode::create(
								array(
									'class'	=> 'dump',
									'function'=> '[awce]',
									'type'	=> '::'
								)
							)
						)->end()
					)->parseCallArgs()->getArg(0)
				)
			)
			!= ( is_object($var) ? spl_object_hash($var) : @(string)$var ) /* PHP Catchable fatal error NOT handled traditionaly
			with try-catch block!
			See http://ru2.php.net/manual/en/migration52.error-messages.php
			and http://www.zend.com/forums/index.php?t=rview&th=2607&goto=6920
			*/
			) return $cp;
	}#m getHeader

	/**
	* Console dump. Useful in cli-php. See also {@link ::a()} and {@link ::auto()}
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function c($var, $header = false, $return = false){
		$ret = '';

		if ($header = self::getHeader($header, $var)) $ret .= "\033[1m".$header."\033[0m: ";

		ob_start();
	
		//This may happens. F.e. it present in template class
		if ($return_html_errors = ini_get('html_errors')){
			ini_set('html_errors', false);
		}
		var_dump($var);//This isn't possible return string in other way, such as it possible in print_r(, true)
		$dStr = ob_get_clean();
		$ret .= self::transformCorrect_var_dump($dStr)."\n";

		if ($return_html_errors) //Revertb back
			ini_set('html_errors', true);

		if ($return) return $ret;
		else echo $ret;
	}#m c

	/**
	* Log dump. Useful to return string for file-write. See also {@link ::a()} and {@link ::auto()}
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function log($var, $header = false, $return = true){
		
		return log_dump($var, $header, $return);
	}#m log

	/**
	* Buffered dump. Useful to return string for file-write. See also {@link ::a()} and {@link ::auto()}
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param string|array	Callback-function or array(object, 'method')
	* @return string|void	Depend of parameter $return
	**/
	static public function buff($var, $header = false, $debug_func = 'print_r'){
		/*
		* For use with family ob_*!
		* In this case do not restricted use standart print_r, var_dump and var_export
		*
		* Out to stderr, instead of stdout
		* This is "no good" method, but it is worked for me.
		* $extra may contain only SHORT aliases!
		*/
		$header = self::getHeader($header, $var);

		$print_func = ' '.$debug_func;
		$cmd = 'echo "<? '.$print_func.'(unserialize('.addcslashes(escapeshellarg(serialize($var)),'"').')'.($extra ? ",'".addcslashes($header, '$')."'" : '').');?>" | php';
		file_put_contents('php://stderr', shell_exec($cmd));
	}#m buff

	/**
	* Short alias to Buffered Console Dump. Parameters are same. See appropriate methods
	**/
	static public function b_c($var, $header = false){
		$header = self::getHeader($header, $var);

		return dump::buff($var, $header, 'dump::c');
	}#m b_c

	/**
	* WEB dump. Useful to dump in Web-browser. See also {@link ::a()} and {@link ::auto()}
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function w($var, $header = false, $return = false){
		$ret = '';
		if ($header = self::getHeader($header, $var)) $ret .= '<h4 style="color:green">'.$header.":</h4>\n";

		ob_start();
		var_dump($var);//This isn't possible return string in other way, such as it possible in print_r(, true)
		$dStr = ob_get_clean();

		// if (ini_get('xdebug.overload_var_dump')){
		// Config-directives not always is set...
		if ('<pre' == substr($dStr, 0, 4)){
			$ret .= $dStr;
		}
		else{//By hand
			$ret .= '<pre><xmp>';
			// $ret .= self::transformCorrect_print_r(print_r($var, true))."\n";
			$ret .= self::transformCorrect_var_dump($dStr)."\n";
			$ret .= '</xmp></pre>';
		}

		if ($return) return $ret;
		else echo $ret;
	}#m w

	/**
	* WAP dump. Useful to dump in WAP-browser (XML).
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function wap($var, $header = false, $return = false){
		$ret = '';
		if ($header) $ret .= '<h4>'.$header."</h4>\n"; //Only explicitly given
		$ret .= nl2br(print_r($var, true)).'<br />';
		if ($return) return $ret;
		else echo $ret;
	}#m wap

	/**
	* Make guess how invoked from cli or from WEB-server (any other) and turn next to c_dump or w_dump respectively.
	*
	* @return mixed	::c or ::w invoke whith same parameters.
	**/
	static public function auto($var, $header = false, $return = false){
		/**
		* May use php_sapi_name() or (in notice of this) constant PHP_SAPI. Use second.
		*/
		if (PHP_SAPI == 'cli') return self::c($var, $header, $return);
		else return self::w($var, $header, $return);
	}#m auto

	/**
	* Only short alias for {@link ::auto()}, nothing more!
	*
	* @return mixed	::c() or ::w() invoke whith same parameters.
	**/
	static public function a($var, $header = false, $return = false){
		return self::auto($var, $header, $return);
	}#m a

	/**
	* As {@link ::a()} but print to STDERR!
	*
	* @return	nothing
	**/
	static public function e($var, $header = false){
		file_put_contents('php://stderr', self::auto($var, $header, true));
	}#m e

	/**
	* One name to invoke dependently by out type.
	*
	* @return mixed One of result call: ::c, ::a, ::log, ::wap.
	* @Throw(VariableRangeException)
	**/
	public static function byOutType($type, $var, $header = false, $return = false){
		

		switch ($type){
			case OS::OUT_TYPE_BROWSER:
				return self::w($var, $header, $return);
				break;

			case OS::OUT_TYPE_CONSOLE:
				return self::c($var, $header, $return);
				break;

			case OS::OUT_TYPE_FILE:
				return self::log($var, $header, $return);
				break;

			case OS::OUT_TYPE_WAP:
				return self::wap($var, $header, $return);
				break;

			// Addition
			case OS::OUT_TYPE_PRINT:
				return self::a($var, $header, $return);
				break;

			default:
				
				throw new VariableRangeException('$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!');
		}
	}#m byOutType
}#c debug

/**
* dump::getHeader assumed on spl_object_hash() for objects, so, we must emulate it on old versions of PHP.
* Simple implementation got from http://xpoint.ru/forums/programming/PHP/thread/38733.xhtml
*
* @param Object $obj
* @return string - object hash.
**/
if (!function_exists("spl_object_hash")) {
	function spl_object_hash($obj){
	static $cur_id = 0;
		if (!is_object($obj))
		return null;

		!isset($obj->_obj_id_) and $obj->_obj_id_ = md5($cur_id++);

	return $obj->_obj_id_;
	}#f spl_object_hash
}
?><?
/**
* Debug and backtrace toolkit.
* Class to provide convenient backtrace logging.
*
* @package Debug
* @subpackage Bactrace
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-07 18:35
*
* @uses commonOutExtraData
**/

class backtrace_out extends commonOutExtraData{
	public function strToConsole($format = nul){
	return $this->_var->printout(true, null, OS::OUT_TYPE_CONSOLE);
	}#m strToConsole

	public function strToFile($format = null){
	return $this->_var->printout(true, null, OS::OUT_TYPE_FILE);
	}#m strToFile

	public function strToWeb($format = null){
	return $this->_var->printout(true, null, OS::OUT_TYPE_BROWSER);
	}#m strToWeb
}#c
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage Bactrace
* @version 2.1.6
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-30 01:20 v 2.1b to 2.1.1
*
* @uses ASSIGN_IF()
* @uses EMPTY_VAR()
* @uses REQUIRED_VAR()
* @uses VariableEmptyException
* @uses VariableArrayInconsistentException
* @uses VariableRangeException
* @uses VariableRequiredException
* @uses BacktraceEmptyException
* @uses ClassPropertyNotExistsException
* @uses HuFormat
**/





class BacktraceEmptyException extends VariableEmptyException{}

/**
* BackTraceNode. In array converted to like this. Otherwise each member accessible separately.
* Structure example:
* Array(){
*	[file] => string(37) "/var/www/_SHARED_/Debug/backtrace.php"	//Mandatory
*	[line] => int(47)	//Mandatory
*	[function] => string(11) "__construct"	//Mandatory
*	[class] => string(9) "backtrace"
*	[object] => object(backtrace)#1 (2) { <Full Object> }
*	[type] => string(2) "->"
*	[args] => Array(2){	//Mandatory
*		[0] => NULL
*		[1] => int(0)
*	}
* 	//Additional according to standad element of array from debug_backtrace();
*	//Point to number in element of array debug_backtrace();
* 	[N] => 1	//Mandatory
* }
*
* implements Iterator by example from main descrioption http://php.net/manual/ru/language.oop5.iterations.php
**/
class backtraceNode implements Iterator{
	static public $properties = array(
		'file',
		'line',
		'function',
		'class',
		'object',
		'type',
		'args',
		'N'
	);

	private $_btn = null;

	protected $_format;	/** Format to format args to string {@see setArgsFormat} **/

	/**
	* Construct object from array
	*
	* @param	array	$arr	Array to construct from
	* @param	$N		Number of node, got separatly (may be already in $arr).
	* @return	Object(backtraceNode)
	**/
	public function __construct(array $arr = null, $N = false){
		ASSIGN_IF($this->_btn, $arr);
		if (false !== $N) $this->_btn['N'] = $N;
	}#__c

	/**
	* To allow constructions like: backtraceNode::create()->methodName()
	* {@inheritdoc ::__construct()}
	**/
	static public function create(array $arr = null, $N = false){
		/*
		* Require late-static-bindings future, so, it is available only in PHP version >= 5.3.0-dev
		**/
		if (version_compare(PHP_VERSION, '5.3.0-dev', '>=')){
			return eval('return new static($arr, $N);');
		}
		else{//This is legitimate onli if it has not derived. So, now it is true...
			return new self($arr, $N);
		}
	}#m create

	/**
	* Return property, if it exists, Throw ClassPropertyNotExistsException otherwise
	*
	* @param	string	$name	Name of required property
	* @return	mixed	Reference on property value
	* @Throw(ClassPropertyNotExistsException)
	**/
	public function &__get($name){
		if (!in_array($name, backtraceNode::$properties)) throw new ClassPropertyNotExistsException('Property "'.$name.'" does NOT exist!');

		return $this->_btn[$name];
	}#m __get

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param	string	$name	Name of required property
	* @return	boolean
	**/
	public function __isset($name) {
		if (!in_array($name, backtraceNode::$properties)) throw new ClassPropertyNotExistsException('Property <'.$name.'> does NOT exist!');

		return isset($this->_btn[$name]);
	}#m __isset

	/**
	* Dump in appropriate(auto) form bactraceNode.
	*
	* @param	boolean	$return
	* @param	string	$header('backtraceNode')
	* @return	mixed	return dump::a(...)
	**/
	public function dump($return = false, $header = 'backtraceNode'){
		return dump::a($this->_btn, $header, $return);
	}#m dump

	/// From interface Iterator ///

	public function rewind(){
		reset($this->_btn);
	}#m rewind

	public function current(){
		return /* $var = */ current($this->_btn);
	}#m current

	public function key(){
		return /* $var = */ key($this->_btn);
	}#m key

	public function next(){
		return /* $var =*/ next($this->_btn);
	}#m next

	public function valid(){
		return /* $var = */ ($this->current() !== false);
	}#m valid

	/// Private and protected methods ///

	/**
	* Compares two nodes by fnmatch() all properties in $node1
	*
	* @param	Object(backtraceNode)	$toCmp Node compare to
	* @return	integer. 0 if equals. Other otherwise (> or < not defined, but *may be* done later).
	**/
	public function FnmatchCmp(backtraceNode $toCmp){
		foreach($toCmp as $key => $prop){
			if (!isset($this->$key) or !fnmatch($prop, $this->$key)) return 1;
		}
		return 0;	 // FnmatchEquals!
	}#m FnmatchCmp

	/**
	* Set format to formatArgs. Array by type of out as key {@see OS::OUT_* constants}, and values as array in format,
	*	as described in {@see class HuFormat}. {@example Debug/_HuFormat.defaults/backtrace::printout.php}
	*	On time of set format NOT CHECKED!
	*
	* @param	array	$format
	* @return	nothing
	* @Throws(VariableRequiredException)
	**/
	public function setArgsFormat($format){
		$this->_format = REQUIRED_VAR($format);
	}#m setArgsFormat

	/**
	* Return string of formated args
	*
	* @param	array(null)	$format
	*	If null, trying from ->_format set in {@see ::setSrgsFormat()}, and finaly
	*		get global defined by default in HuFormat $GLOBALS['__CONFIG']['backtrace::printout']
	* @param	integer		$OutType	If present - determine type of format from $format (passed or default). Must be index in $format.
	* @return	string
	* @Throws(VariableArrayInconsistentException, VariableRequiredException)
	**/
	public function formatArgs($format = null, $OutType = null){
		$OutType = ((null === $OutType) ? OS::getOutType() : $OutType); //Caching
		$format = REQUIRED_VAR(
			EMPTY_VAR(
				$format
				,$this->_format[$OutType]['argtypes']
				,@$GLOBALS['__CONFIG']['backtrace::printout'][$OutType]['argtypes']
				,
					// Trying include. Conditional ternar operator only for doing include inplace. Parentness () around include is mandatory!!!
					( /*-One- (include_once('Debug/_HuFormat.defaults/backtrace::printout.php')) ++>true*/ (false) || true )
					?
					// Again provide its value. If it now present - cool, if not - REQUIRED_VAR thor exception
					@$GLOBALS['__CONFIG']['backtrace::printout'][$OutType]['argtypes']
					: // Only for compatibility with old version which don't support short (cond ?: then) version
					null
			)
		);

		$args = '';
		$hf = new HuFormat;

		foreach ($this->args as $var){
			if (!empty($args)) $args .= ', ';

			if (isset($format[gettype($var)])){
				$form =& $format[gettype($var)];
			}
			elseif(isset($format['default'])){
				$form =& $format['default'];
			}
			else throw new VariableArrayInconsistentException('Format of type '.gettype($var).' not found. "default" also not provided in $format');

			$hf->set($form, $var);
			$args .= $hf->getString();
		}
		return $args;
	}#m formatArgs
}#c backtraceNode

/**
* @uses dump
**/
class backtrace implements Iterator{
	private $_bt = array();

	private $_curNode = 0;
	protected $_format;

	/**
	* Constructor
	*
	* @param	array	$bt	Array as result debug_backtrace() or it part. If null filled by
	*	direct debug_backtrace() call.
	* @param	int(1)	$removeSelf	If filled automaticaly, containts also this call
	*	(or call ::create() if appropriate). This will remove it. Number is amount of arrays
	*	remove from stack.
	* @return	Object(backtrace)
	**/
	public function __construct(array $bt = null, $removeSelf = 1){
		if ($bt) $this->_bt = $bt;
		else $this->_bt = debug_backtrace();

		while ($removeSelf--) array_shift($this->_bt);
	}#__c

	/**
	* To allow constructions like: backtrace::create()->methodName()
	*
	* @param	array	$bt	{@link ::__construct}
	* @param	int(2)	$removeSelf	{@link ::__construct}
	* @return	backtrace
	**/
	static public function create(array $bt = null, $removeSelf = 2){
		return new self($bt, $removeSelf);
	}#m create

	/**
	* Dump in appropriate(auto) form bactrace.
	*	Fast dump of current backtrase may be invoked as backtrace::create()->dump();
	*
	* @deprecated since 2.1.5.1
	* @param	boolean	$return
	* @param	string	$header('_debug_bactrace()')
	* @return	mixed	return auto::a(...)
	**/
	public function dump($return = false, $header = '_debug_bactrace()'){
		return dump::a($this->_bt, $header, $return);
	}#m dump

	/**
	* Get BackTraceNode by its number
	*
	* @param	integer	$N - Number of interested Node
	* @return	Object(backtraceNode)
	* @Throw(VariableRangeException)
	**/
	public function getNode($N){
		if (isset($this->_bt[ $N = $this->getNumberOfNode($N) ])){
			if (is_array($this->_bt[ $N ])){
			//Cache on fly!!!
			$this->_bt[ $N ] = new backtraceNode($this->_bt[$N], $N);
			}
		//instanceof backtraceNode
		return $this->_bt[$N];
		}
		else throw new VariableRangeException('Needed BackTraceNode not found in this BackTrace!');
	}#m getNode

	/**
	* Replace (or silently add) node in place $N
	*
	* @param	integer	$N	Place to node. If not exists - silently create.
	*	{@see ::getNumberOfNode() fo more description}
	* @return	nothing
	**/
	public function setNode($N, backtraceNode $node){
		$this->_bt[ $this->getNumberOfNode($N) ] = $node;
	}#m setNode

	/**
	* Return real number of requested Node in _bt array, implements next logic:
	*	If $N === null set on current node ({@see ::current()}).
	*	If $N < 0	Negative values to to refer in backward: -2 mean: sizeof(debug_backtrace() - 2)!
	*		Be carefull value -1 meaning LAST element, not second from end!
	*
	* @param	integer	$N
	* @return	integer	Number of requested node.
	**/
	private function getNumberOfNode($N){
		return ( (null !== $N) ? ($N >= 0 ? $N : $this->length() + $N) : $this->key() );
	}#m getNumberOfNode

	/**
	* Delete node in place $N
	* After delete, all indexes is recomputed. BUT, current position not changed!
	* So, be carefully in loops - it may have undefined behavior.
	*
	* @param	integer	$N	Place of node.
	*	{@see ::getNumberOfNode() for more details}
	* @return	nothing
	* @Throw(VariableRangeException)
	**/
	public function delNode($N = null){
		if (!isset($this->_bt[ $calcN = $this->getNumberOfNode($N)])){
			throw new VariableRangeException($N.' node not found! Can\'t delete!');
		}
		else{
			//Do NOT use unset, because it left old keys
			array_splice($this->_bt, $calcN, 1);
		}
	}#m delNode

	/**
	* Return count of BackTraceNodes.
	*
	* @return	integer
	**/
	public function length(){
		return sizeof($this->_bt);
	}#m length

	/**
	* Find node of bactrace. To match each possible used fnmatch (http://php.net/fnmatch),
	* so all it patterns and syntrax allowed.
	*
	* @param	Object(backtraceNode)	$need	Parameters to search:
	* 	array(
	*		'file'	=> "*backtrace.php"
	*		'class'	=> "dump"
	*		'function'=> "[aw]"
	*		'type'	=> "->"
	*	)
	* Array may contain next elements, each compared as *strings*: file, line, function, class,
	* object (yes it is, also compared as string, so it may have a sence if implemented __toString
	* magic method on it), type.
	*	Args and N may be present, but first is stupidly compare as string ('Array' === 'Array' :))
	* and to search by N use ::getNode() this faster.
	* @return	Object(backtrace)
	**/
	public function find(backtraceNode $need){
		$ret = clone $this;

		//Foreach is dangerous, because we delete elements.
		$ret->rewind();
		while ($node = $ret->current()){
			//Returned 0 if equals
			if ($node->FnmatchCmp($need) != 0){
				$ret->delNode();
			}
			else{
				$node = $ret->next();
			}
		}
		return $ret;
	}#m find

	/**
	* @todo Implement RegExp find. Not now.
	**/
	public function findRegexp(backtraceNode $need){
		throw new BaseException('Method findRegexp not implemented now!');
	}#m findRegexp

	/**
	* Getted (and modifiyed) from http://php.rinet.ru/manual/ru/function.debug-backtrace.php
	* comments of users
	*
	* @param	boolean(false)	$return	Return or print directly.
	* @param	array(null)	$format
	*	If null, trying from format set in {@see ::setPrintoutFormat()}, and finaly
	*		get global defined by default in HuFormat $GLOBALS['__CONFIG']['backtrace::printout']
	* @param	integer(null)	$OutType	If present - determine type of format from $format (passed or default). Must be index in $format.
	* @Throws(VariableRequiredException, BacktraceEmptyException)
	**/
	public function printout($return = false, array $format = null, $OutType = null){
		$OutType = ((null === $OutType) ? OS::getOutType() : $OutType); //Caching
		$format = REQUIRED_VAR(
			EMPTY_VAR(
				$format
				,$this->_format[$OutType]
				,@$GLOBALS['__CONFIG']['backtrace::printout'][$OutType]
				,(
					// Trying include. Conditional ternar operator only for doing include inplace. Parentness () around include is mandatory!!!
					( /*-One- (include_once('Debug/_HuFormat.defaults/backtrace::printout.php')) ++>true*/ (false) || true )
					?
					// Again provide its value. If it now present - cool, if not - REQUIRED_VAR thor exception
					@$GLOBALS['__CONFIG']['backtrace::printout'][$OutType]
					: // Only for compatibility with old version which don't support short (cond ?: then) version
					null
				)
			)
		);

		if ($this->_bt){
			$hf = new HuFormat($format, $this);
			$ret = $hf->getString();
		}
		else{
			throw new BacktraceEmptyException(new backtrace, '$this->_bt', 'Backtrace is empty! Nothing to printout!');
		}

		if ($return) return $ret;
		else echo $ret;
	}#m printout

	/**
	* Set format to printout. Array by type of out as key {@see OS::OUT_* constants}, and values as array in format,
	*	as described in {@see class HuFormat}. {@example Debug/_HuFormat.defaults/backtrace::printout.php}
	*	On time of set format NOT CHECKED!
	*
	* @param	array	$format
	* @return	&$this
	* @Throws(VariableRequiredException)
	**/
	public function &setPrintoutFormat($format){
		$this->_format = REQUIRED_VAR($format);
		return $this;
	}#m setPrintoutFormat

	/**
	* By default convert into string will ::printout();
	*
	* @return string
	**/
	public function __toString(){
		return $this->printout(true);
	}#m __toString

	/*
	* From interface Iterator
	* Use self indexing to allow delete nodes and continue loop foreach.
	**/

	/**
	* Rewind internal pointer to begin
	*
	* @return	nothing
	**/
	public function rewind(){
		$this->_curNode = 0;
	}#m rewind

	/**
	* Return current backtraceNode
	*
	* @return	Object(backtraceNode)|null
	**/
	public function current(){
		try{
			return $this->getNode($this->_curNode);
		}
		catch (VariableRangeException $vre){
			return null;
		}
	}#m current

	/**
	* Return current key
	*
	* @return	integer
	**/
	public function key(){
		return $this->_curNode;
	}#m key

	/**
	* Return next backtraceNode
	*
	* @return	Object(backtraceNode)|null
	**/
	public function next(){
		try{
			return $this->getNode( ++$this->_curNode );
		}
		catch (VariableRangeException $vre){
			return null;
		}
	}#m next

	/**
	* Return if Iterator valid and not end reached.
	*
	* @return	boolean
	**/
	public function valid(){
		return ($this->current() !== null);
	}#m valid

	/**
	* Return end backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	*	and added to more flexibility.
	*
	* @return	Object(backtraceNode)
	**/
	public function end(){
		return $this->getNode( ($this->_curNode = $this->length() - 1) );
	}#m end

	/**
	* Return prev backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	*	and added to more flexibility.
	*
	* @return	Object(backtraceNode)|null
	**/
	public function prev(){
		if ($this->_curNode < 1) return null;

		return $this->getNode( --$this->_curNode );
	}#m prev
}#c backtrace
?><?
/**
* Debug and backtrace toolkit.
*
* In call function funcName($currentValue); in any place, in function by other methods available only
* value of variable $currentValue but name call-time (in this example '$currentValue') - NOT.
*
* This return array of names CALL parameters!
* Implementation is UGLY - view in source PHP files and parse it, but I NOT known other way!!!
*
* In number of array in debug_backtrace().
*
*, like this:
*Array(
*	[file] => /var/www/vkontakte.nov.su/backends/postMessageReply.php
*	[line] => 22
*	[function] => REQUIRED_VAR
*	[args] => Array(
*		[0] =>
*		)
*)
*
* I cannot do that easy in Regular Expression, due to possible call like this:
* t($tt,
* 	$ttt[0]
* 	,$ttt['qaz']
* 				,tttt(),
*
*				"exampleFunc() call")
* ;
*
* $db[$N]['line'] refer to string with closing call ')' :(.
* Now search open string number. And then from it string, by function name tokenize all what me need.
*
* @package Debug
* @version 2.1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-18 17:44 ver 2.1 to 2.1.1
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses backtrace
* @uses RegExp_pcre
* @uses file_inmem
**/



	if (!defined('T_ML_COMMENT')) {
		define('T_ML_COMMENT', T_COMMENT);
	} else {
		define('T_DOC_COMMENT', T_ML_COMMENT);
	}

class Tokenizer{
	private /* backtraceNode */ $_debugBacktrace = null;

	protected $_filePhpSrc = null;
	private $_callStartLine = 0;
	private $_callText = '';
	private $_tokens = null;
	private $_curTokPos = 0;
	private $_args = array();
	private $_regexp = null;

	/**
	* Constructor.
	*
	* @param array|Object(backtraceNode) $db	Array, one of is subarrays from return result by debug_backtrace();
	* @return $this
	**/
	public function __construct(/* array | backtraceNode */ $db = array()){
		if (is_array($db)) $this->setFromBTN(new backtraceNode($db));
		$this->setFromBTN($db);
	}#__c

	/**
	* Set from Object(backtraceNode).
	*
	* {@inheritdoc ::__construct()}
	* @return &$this
	**/
	public function &setFromBTN(backtraceNode $db){
		$this->clear();
		$this->_debugBacktrace = $db;
		return $this;
	}#m setFromBTN

	/**
	* To allow constructions like: Tokenizer::create()->methodName()
	* {@inheritdoc ::__construct()}
	**/
	static public function create(/* array | backtraceNode */ $db){
		return new self($db);
	}#m create

	/**
	* Clear object
	*
	* @return nothing
	**/
	public function clear(){
		$this->_debugBacktrace = null;
		$this->_filePhpSrc = null;
		$this->_callStartLine = 0;
		$this->_callText = '';
		$this->_tokens = null;
		$this->_curTokPos = 0;
		$this->_args = array();
		$this->_regexp = null;
	}#m clear

	/**
	* Return string of parsed argument by it number (index from 0). Bounds not checked!
	*
	* @param integer $n - Number of interesting argument.
	* @return string
	**/
	public function getArg($n, $trim = true){
		if ($trim) return trim($this->_args[$n]);
		else return $this->_args[$n];
	}#m getArg

	/**
	* Set to arg new value.
	*
	* @param	integer	$n - Number of interesting argument. Bounds not checked!
	* @param	mixed	$value Value to set.
	* @return	&$this
	**/
	public function &setArg($n, $value){
		$this->_args[$n] = $value;
		return $this;
	}#m setArg

	/**
	* Return array of all parsed arguments.
	*
	* @return array
	**/
	public function getArgs(){
		return $this->_args;
	}#m getArgs

	/**
	* Return count of parsed arguments.
	*
	* @return integer
	**/
	public function countArgs(){
		return sizeof($this->_args);
	}#m countArgs

	/**
	* Search full text of call in src php-file
	*
	* @return $this
	* @Throws(VariableRequiredException)
	**/
	protected function findTextCall(){
		$this->_filePhpSrc = new file_inmem(REQUIRED_VAR($this->_debugBacktrace->file));
		$this->_filePhpSrc->loadContent();

		$rega = '/'
			.RegExp_pcre::quote(@$this->_debugBacktrace->type) // For classes '->' or '::'. For regular functions not exist.
			.'\b'.$this->_debugBacktrace->function // In case of method and regular function same name present.
			.'\s*\((.*?)\s*\)' // call
			.'/xms';

			$this->_regexp = new RegExp_pcre($rega, $this->_filePhpSrc->getBLOB());
		$this->_regexp->doMatchAll(PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		$this->_regexp->convertOffsetToChars(PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		return $this;
	}#m findTextCall

	/**
	* See description on begin of file ->_debugBacktrace->line not correct start call-line if call
	* continued on more then one string!
	* Seek closest-back line from found matches. In other words, search start of call.
	* So, in any case, I do not have chance separate calls :( , if it presents more then one in string!
	* Found and peek first call in string, other not handled on this moment.
	*
	* @return &$this;
	**/
	protected function &findCallStrings(){
		if (!$this->_regexp) $this->findTextCall();

		$delta = PHP_INT_MAX;
		$this->_callStartLine = 0;

		//Search closest line
		foreach ($this->_regexp->getMatches() as $k => $match){
			$lineN = $this->_filePhpSrc->getLineByOffset($match[0][1]) + 1; //Indexing from 0
				if ( ($d = $this->_debugBacktrace->line - $lineN) >= 0 and $d < $delta){
					$delta = $d;
					$this->_callStartLine = $lineN;
				}
				else break;//Not needed more
			}

			$this->_callText = implode(
				$this->_filePhpSrc->getLineSep()
				,$this->_filePhpSrc->getLines(
					array(
						$this->_callStartLine - 1
						,$delta + 1
					)
				)
			);
	return $this;
	}#m findCallStrings

	/**
	* Parse tokens
	*
	* @return &$this
	**/
	public function &parseTokens(){
		if (!$this->_callText) $this->findCallStrings();

		// Without start and end tags not parsed properly.
		$this->_tokens = token_get_all('<?' . $this->_callText . '?>');
		return $this;
	}#m parseTokens

	/**
	* Working horse!
	* Base idea from: http://ru2.php.net/manual/ru/ref.tokenizer.php
	*
	* @param boolean(true) $stripWhitespace = False! Because stripped any space, not only on
	*	start and end of arg! This is may be not wanted behavior on constructions like:
	*	$a instance of A. Instead see option $trim in {@link ::getArg()) method.
	* @param boolean(false) $stripComments = false
	* @return $this
	**/
	public function &parseCallArgs($stripWhitespace = false, $stripComments = false){
		if ($this->_tokens === null) $this->parseTokens();

		$this->skipToStartCallArguments();
		$this->addArg();
		$sParenthesis = 0; //stack
		$sz = sizeof($this->_tokens);
			while ($this->_curTokPos < $sz){
				$token =& $this->_tokens[$this->_curTokPos++];

				if (is_string($token)){
					switch($token){
						case '(':
							++$sParenthesis;
							// Self ( - do not want
							if ($sParenthesis > 1) $this->addToArg($token);
						break;

						case ')':
							--$sParenthesis;
							if (0 == $sParenthesis) break 2;
							$this->addToArg($token);
							break;

						case ',':
							if (1 == $sParenthesis) $this->addArg();
							else $this->addToArg($token);
							break;

						default:
							$this->addToArg($token);
					}
				}
				else{
					switch($token[0]){
						case T_COMMENT:
						case T_ML_COMMENT:	// we've defined this
						case T_DOC_COMMENT:	// and this
							if (!$stripComments) $this->addToArg($token[1]);
							break;

						case T_WHITESPACE:
							if (!$stripWhitespace) $this->addToArg($token[1]);
							break;

						default:
							$this->addToArg($token[1]);
					}
				}
			}
		return $this;
	}#m parseCallArgs

	/**
	* Move ->_curTokPos to first tokens after functionName(
	*
	* @return $this
	**/
	private function skipToStartCallArguments(){
		$sz = sizeof($this->_tokens);
			while ($this->_curTokPos < $sz){
				$token =& $this->_tokens[$this->_curTokPos++];
				if (is_array($token) and T_STRING == $token[0] and $token[1] == $this->_debugBacktrace->function)
					return;
			}
		return $this;
	}#m skipToStartCallArguments

	/**
	* Add text to CURRENT arg.
	*
	* @return noting
	**/
	private function addToArg($str){
		$this->_args[$this->countArgs() - 1] .= $str;
	}#m addToArg

	/**
	* Add next arg to array
	*
	* @return nothing
	**/
	private function addArg(){
		$this->_args[$this->countArgs()] = '';
	}#m addArg

	/**
	* Strip quotes on start and end of argument.
	* Paired
	*
	* @param	string	$arg	Argument to process.
	* @param	boolean	$all If true - all trim, else (by default) - only paired (if only ended with quote, or only started - leaf it as is).
	* @return	string
	**/
	static public function trimQuotes($arg, $all = false){
		if (!$arg) return '';

		$len = strlen($arg);
		if ('"' == $arg{0} or '\'' == $arg{0}) $from = 1;
		else $from = 0;
		if ('"' == $arg{$len-1} or '\'' == $arg{$len-1}) $len -= (1 + $from);

		if ($all) return (substr($arg, $from, $len));
		elseif(strlen($arg) - $len > 1) return (substr($arg, $from, $len));
		else return $arg;
	}#m trimQuotes
}#c Tokenizer
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuLOG
* @version 2.0.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-05-30 23:19
*
* @uses settings
* @uses NullClass
* @uses commonOutExtraData
* @uses HuError
* @uses OS
**/




class HuLOG_settings extends settings{
	const LOG_TO_FILE	= OS::OUT_TYPE_FILE; // To file
	const LOG_TO_PRINT	= OS::OUT_TYPE_PRINT; // To stdout (print, echo)
	// Unfortunetly PHP does NOT support computed value of constants
	//const LOG_TO_BOTH	= OS::OUT_TYPE_FILE + OS::OUT_TYPE_PRINT;	//to both
	const LOG_TO_BOTH	= 12; // to both

protected $__SETS = array(
	'FILE_PREFIX'		=> 'log_',
	'LOG_FILE_DIR'		=> './log/',

	'LOG_TO_ACS'		=> self::LOG_TO_BOTH,
	'LOG_TO_ERR'		=> self::LOG_TO_BOTH,

	/** In SUBarray in order not to generate extra Entity
	'HuLOG_Text_settings' => array(
		// Here may be overwritten defaults settings. {@see HuLOG_text_settings}
	)
	*/
);
}#c HuLOG_settings

class HuLOG_text extends HuError{
	/**
	* Constructor.
	*
	* @param Object(HuLOG_text_settings)|array	$sets	Initial settings.
	*	If HuLOG_text_settings assigned AS IS, if array MERGED with defaults and overwrite
	*	presented settings!
	**/
	public function __construct( /* HuLOG_text_settings | array */ $sets){
		if (is_array($sets) and !empty($sets)){ //MERGE, NOT overwrite!
			$this->_sets = new HuLOG_text_settings();
			$this->_sets->mergeSettingsArray($sets);
		}
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new HuLOG_text_settings();//default
	}#__c
}#c HuLOG_text

class HuLOG_text_settings extends HuError_settings{
	protected $__SETS = array(
		/**
		* @see HuError::updateDate()
		*/
		'AUTO_DATE'		=> true,
		'DATE_FORMAT'		=> 'Y-m-d H:i:s:',

		/** Header for 'extra'-data, which may be present */
		'EXTRA_HEADER'		=> 'Extra info',

		/** In format {@link settings::getString()} */
		'FORMAT_CONSOLE'	=> array(	//Формат вывода для отладки
			array('date', "\033[36m", "\033[0m"),
			'level',
			array('type', "\033[1m", "\033[0m: ", ''),//Bold
			'logText',
			array('extra', "\n"),
			"\n"
		),
		'FORMAT_WEB'		=> array(
			array('date', "<b>", "</b>"),
			'level',
			array('type', "<b>", "</b>: ", ''),
			'logText',
			array('extra', "<br\\>\n"),
			"<br\\>\n"
		),
		'FORMAT_FILE'		=> array(
			'date',
			'level',
			array('type', '', ': ', ''),
			'logText',
			array('extra', "\n"),
			"\n"
		)
	);
}#c HuLOG_text_settings

class HuLOG extends get_settings{//HubbitusLOG :) log занял давно, для совместимости старого кода не заменяю имя!
	public $_level = 0;//Для установки уровней вложенности логовых сообщений в файле

	protected $lastLogText /*HuLOG_text*/= null;
	protected $lastLogTime = null;

	protected $_sets = null;

	function __construct (/* HuLOG_settings OR array*/ $sets = null){
		if (is_array($sets)) $this->_sets = new HuLOG_settings((array)$sets);
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new HuLOG_settings();//Default
		$this->lastLogText = new HuLOG_text ($this->settings->HuLOG_Text_settings);
	}

	private function log_to_file($file='ERR'){
//	exec('echo -ne '.escapeshellarg($this->lastLogText->strToFile($this->lastLogText->settings->FORMAT_FILE)).' >> '.$this->settings->LOG_FILE_DIR.$this->settings->FILE_PREFIX.$file.' 2>&1');
		file_put_contents(
			$this->settings->LOG_FILE_DIR.$this->settings->FILE_PREFIX.$file,
			$this->lastLogText->strToFile($this->lastLogText->settings->FORMAT_FILE),
			FILE_APPEND
		);
	}#m log_to_file

	private function log_print(){
		echo $this->lastLogText->strToPrint();
	}#m log_print

	protected function makeLogString($log_string, $file, $type, $extra){
		$this->lastLogTime = time();
		$this->lastLogText->setSettingsArray(
			($extra instanceof NullClass) /* EXPLICIT check what $extra was provided! Null also possible value, what must be dumped, if it peovided, I can't ignore it, also as any other predefined value! **/
			?
			array(
				'level'	=> sprintf('% ' . (((int)$this->_level)*2) . 's', ' '),	//Отступ
				'type'	=> $type,			//Type-prefix
				'logText'	=> $log_string,	//Main text!
			)
			:
			array(
				// Now auto or disabled
//-				'date'	=> date($this->_sets->DATE_TIME_FORMAT, $this->lastLogTime),//Дата-время
				'level'	=> sprintf('% ' . (((int)$this->_level)*2) . 's', ' '),	//Отступ
				'type'	=> $type,			//Type-prefix
				'logText'	=> $log_string,	//Main text!
				'extra'	=> ( ($extra instanceof outExtraData) ? $extra : new commonOutExtraData($extra))	//Additional extra data
			)
		);
	}

	/**
	* Main method to log messages
	*
	* @param $log_string - собственно строка в лог, сразу после даты
	* @param $file:
	*	* ERR - Ошибки
	*	* ACS - Доступ (ACesS)
	* @param $extra - Любая дополнительная переменная, информация, комментарии...
	**/
	public function toLog($log_string, $file='ERR', $type='', $extra=null){
		if ( ! ($to = $this->settings->getProperty('LOG_TO_'.$file)) ){
			//От себя (HuLOG) пишем в лог
			$to = HuLOG_Settings::LOG_TO_BOTH;
			$file = 'ERR';
			$this->makeLogString('НЕ задан файл, куда логгить и как!', $file, 'HuLOG');
			$this->writeLogs($to);
		}

		/**
		* In PHP 5.1.6 without temporary variable $func_num_args we got error:
		* Fatal error: func_num_args(): Can't be used as a function parameter in /home/www/_shareDInclude_/_2.0_/Debug/HuLOG.php on line 162
		**/
		$func_num_args = func_num_args();
		$this->makeLogString($log_string, $file, $type, ($func_num_args > 3 ? $extra : new NullClass) );
		$this->writeLogs($to, $file);
	}#m toLog

	protected function writeLogs($to, $file){
		if ( $to & HuLOG_Settings::LOG_TO_FILE ) $this->log_to_file($file);
		if ( $to & HuLOG_Settings::LOG_TO_PRINT ) $this->log_print();
	}#m writeLogs
}//c HuLOG
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuFormat
* @version 2.1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created  2009-03-13 19:01
*
* @uses EMPTY_STR()
* @uses ASSIGN_IF()
* @uses REQUIRED_VAR()
*
* @uses VariableException
* @uses VariableRangeException
* @uses VariableRequiredException
*
* @uses HuError
* @uses Single
* @uses OS
**/





class HuFormatException extends VariableException{}

/**
* Class to format different structures.
* @example HuFormat.example.php
**/
class HuFormat extends HuError{
	/** Replace this in ->_format on real value of _value (after process mod_s) **/
	const sprintf_var = '__vAr__';
	/** Var to process in eval-string in mod_e. In eval string off course witch sign $. **/
	const evalute_var = 'var';

	/** Separator to separate mods from name in one string. For more info see {@see ::parseModsName()} **/
	const mods_separator = ':::';

	/**
	* For each present Mod we must have method with name "mod_[mod]" where [mod] is letter of mode.
	*	Additionally, because PHP function (methods too) name are case insensitive, for upper-case letter
	*	modifiers must used double letters.
	*	For example:
	*		Mod 'e' => mod_e
	*		Mod 'E' => mod_EE (same as mod_ee)
	*
	* @var array
	**/
	static public $MODS = array(
		'A'	=> 1,	//ALL. Exclusive, all other modifiers not processed. Each process as HuFormat.
		's'	=> 2,	//Setting
		'a'	=> 4,	//Array
		'n'	=> 8,	//Non_empty_str
		'p'	=> 16,	//sPrintf. {@link http://php.net/sprintf}
		'e'	=> 32,	//Evaluate. Evaluated only ->_name !!!
		'E'	=> 64,	//Evaluate full! Evaluate all as full result.
		'v'	=> 128,	//Value,
		'I'	=> 256,	//Iterate ->_value (or ->_realValue) and each format as ->_format
		'k'	=> 512,	//Key. Get key of current iteration of I:::.
	);

	private $_format;				//Array of format.
	private $_modStr;				//Modifiers.
	private $_mod;					//Integer of present mods
	private $_modArr = array();		//Array of present mods
	private $_value;				//Value, what processed in this formating.
	private $_realValue;			//If modified (part) in mod_s, mod_a
	private $_realValued = false;		//Flag, to allow pipe through several mods (like as s. a, e)
	private $_name;
	private $_key;					//Key from mod_I itaration for the mod_k

	private $_resStr;				//For caching

	/**
	* @method Object(settings) sets() return current settings
	**/

	/**
	* @method Object(HuFormat) create() Return new instance of object.
	**/

	/**
	* Constructor
	*
	* {@see ::set()}
	*	Be careful - you should explicit provide value like false (invoke as __construct(null, $t = false) for example, because 2d parameter is reference). Otherwise default value null means - using $this as value!
	**/
	public function __construct(array $format = null, &$value = null, $key = null){
		$this->set($format, $value, $key);
	}#__c

	/**
	* Set main: format and value.
	*
	* @param	array|string	$format. If === null, skipped to allow set other
	*	parts. To clear you may use false/true or any else such as empty string.
	* @param	&mixed	$value.	{@see ::setValue()} Skiped if === null. You
	*	may call {@see ::setValue()} to do that
	* @param	mixed	$key	Key of iteration in mod_I and/or mod_A.
	* @return	&$this
	**/
	public function &set($format = null, &$value = null, $key = null){
		if (null !== $value) $this->setValue($value);
		if (null !== $format) $this->setFormat($format);
		$this->_key = $key;
		return $this;
	}#m set

	/**
	* Return current value.
	*
	* @return &mixed
	**/
	public function &getValue(){
		if ($this->_realValued) return $this->_realValue;
		else return $this->_value;
	}#m getValue

	/**
	* Set value
	*
	* @param	&mixed	$value.	Value to format.
	*	If === null $this->_value =& $this; $this->_realValue =& $this->_value;
	* @return &$this
	**/
	public function &setValue(&$value){
		if(null === $value){
		$this->_value =& $this;
		}
		else $this->_value = $value;
		$this->_realValued = false;
		$this->_resStr = null;
		return $this;
	}#m setValue

	/**
	* Parse incoming format-array and set appropriate properties.
	* Accepts 3 forms of format (in examples ':::' is {@see ::parseModsName()}):
	*	1. All in one Array: [0] - Mods:::Name; [1],[2],[3]...[n] - data.
	*	array(
	*		'sn:::bold_text',	//Mods:::Name
	*		'<b>', '</b>', 'default text'	//Data
	*	)
	*
	*	2. Associative array (hash): Key - Mods:::Name, value - Array of [0] - Mods, [1], [2]...[n] - data.
	*	array(
	*		//Name
	*		'bold_text'	=> array(	//Mods empty, Name
	*			'<b>', '</b>', 'default text'	//Data
	*		)
	*	)
	*
	*	3. Just simply string like 'text to add'. Leaved as is.
	*
	* @param array|string	$format to parse
	* @return &$this
	**/
	public function &setFormat($format){
		$this->_mod = 0;
		$this->_modStr = $this->_name = $this->_resStr = $this->_realValue = null;
		$this->_modArr = array();
		$this->_realValued = false;

		if (is_array($format)){
			if (is_array($format[key($format)])){//<2>
				$this->parseModsName(key($format));
				$this->_format = $format[key($format)];
			}
			else{//<1>
				$this->parseModsName(array_shift($format));
				$this->_format = $format;//Tail
			}
		}
		else{//<3>
			$this->_name = $this->_realValue = $format;
			$this->_realValued = true;
		}

		return $this;
	}#m setFormat

	/**
	* Parses and set from given str. As separator used {@see self::mods_separator}.
	* F.e.: 'AI:::line'. If separator not present - whole string in NAME!
	*
	* @param string $str
	* @return &$this
	**/
	protected function &parseModsName($str){
		if (!strstr($str, self::mods_separator)){//Whole name
			$this->_name = $str;
			$this->_modStr = '';
		}
		else{//Separator present
			list ($this->_modStr, $this->_name) = explode(self::mods_separator, $str);
		}
		return $this->parseMods(true);
	}#m parseModsName

	/**
	* Construct and return string to represent provided value according given format.
	*
	* @return string
	**/
	public function getString(){
		if (!$this->_resStr){
		$this->_resStr = '';

			foreach ($this->_modArr as $mod){
				if (ctype_upper($mod)){
				$this->_resStr .= call_user_func(array($this, 'mod_'.$mod.$mod));
				}
				else $this->_resStr .= call_user_func(array($this, 'mod_'.$mod));
			}

			//If all mod_* are only evaluate value and not produce out.
			if (!$this->_resStr) return $this->getValue();
		}

		return $this->_resStr;
	}#m getString

	/**
	* Set or not?
	*
	* @param integer	$mod.
	* @return boolean
	**/
	public function isMod($mod){
		if (!$this->_mod and $this->_modstr) $this->parseMods();
		return ($this->_mod & $mod);
	}#m isMod

	/**
	* Set, or unset mods.
	*
	* @param string	$mods. String to set o unset Mods like: '-I+s+n'.
	*	If '-' - unset.
	*	If '+' - set.
	*	If '*' - invert.
	*	If absent - equal to '+'
	* @return &$this
	* @Throw(VariableRangeException)
	**/
	public function &changeModsStr($mods){
		for($i=0; $i < strlen($mods); $i++){
			if (in_array($mods{$i}, array('+', '-', '*'))){
				$op = $mods{$i};
				$mod = $mods{++$i};
			}
			else{
				$mod = $mods{$i};
				$op = '+';	//Default
			}

			switch ($op){
				case '+':
					$this->_mod |= self::$MODS[$mod];
					break;

				case '-':
					$this->_mod ^= self::$MODS[$mod];
					break;

				case '*':
					$this->_mod &= ~self::$MODS[$mod];
					break;

				default:
					throw new VariableRangeException('Unknown operator - "'.$op.'"');
			}
		}

		$this->parseMods(false);
		return $this;
	}#m changeModsStr

	/**
	* Set Modifiers from string.
	*
	* @param string	$modstr	String of modifiers.
	* @return &$this
	* @Throws(VariableRequiredException)
	**/
	protected function &setModsStr($modstr){
		$this->_modStr = REQUIRED_VAR($modstr);
		$this->parseMods();
		return $this;
	}#m setModsStr

	/**
	* Get string of Modifiers.
	*
	* @return string
	**/
	public function &getModsStr(){
		return implode('', $this->_modArr);
	}#m getModsStr

	/**
	* Get Modifiers.
	*
	* @return integer
	**/
	public function &getMods(){
		return $this->_mod;
	}#m setMods

	/**
	* Set Modifiers.
	*
	* @param integer	$mods. Modifiers to set.
	* @return &$this
	**/
	public function &setMods($mods){
		$this->_mod &= $mods;
		$this->parseMods(false);
		return $this;
	}#m setMods

	/// Private and Protected methods ///

	/**
	* Parse modifiers from string. 1 char on mod.
	*
	* @param bolean(true)	$direction
	*	True	- from string $this->_modStr
	*	False	- from integer $this-_mod
	* @return &this
	* @Throw(VariableRangeException)
	**/
	protected function &parseMods($direction = true){
		if ($direction){
			$this->_mod = 0;
				for($i=0; $i < strlen($this->_modStr); $i++){
					if (in_array($this->_modStr{$i}, array_keys(self::$MODS))){
						$this->_mod |= self::$MODS[$this->_modStr{$i}];
						array_push($this->_modArr, $this->_modStr{$i});
					}
					else throw new VariableRangeException('Unknown modifier - '.$this->_modStr{$i});
				}
		}
		else{//Now correct array-values
			foreach (self::$MODS as $key => $M){
				if ($this->isMod($M) and !in_array($key, $this->_modArr)){
					array_push($this->_modArr, $M);
					$this->_modStr .= $M;
				}
				elseif (!$this->isMod($M) and in_array($key, $this->_modArr)){
					$k = array_keys($this->_modArr, $key);
					unset($this->_modArr[$k[0]]);
					$this->_modStr = str_replace($key, '', $this->_modStr);
				}
			}
		}

		//In modifyed mods - must recalculate values
		$this->_realValued = false;
		$this->_resStr = null;

		return $this;
	}#m parseMods

	/**
	* Treat ->_name as property-name
	*
	* @return void
	**/
	protected function mod_s(){
		if (!$this->_realValued){
			$this->_realValue = @$this->_value->{$this->_name};
			$this->_realValued = true;
		}
		else $this->_realValue = $this->_value->{$this->_realValue};
	}#m mod_s

	/**
	* Tread ->_name as index in ->_value
	*
	* @return void
	**/
	protected function mod_a(){
		if (!$this->_realValued){
			$this->_realValue = $this->_value[$this->_name];
			$this->_realValued = true;
		}
		else $this->_realValue = $this->_value[$this->_realValue];
	}#m mod_a

	/**
	* Process ->_value through NON_EMPTY_STR. ->_format must have appropriate values.
	*
	* @return string
	**/
	protected function mod_n(){
		return NON_EMPTY_STR($this->getValue(), @$this->_format[0], @$this->_format[1], @$this->_format[2]);
	}#m mod_n

	/**
	* Procces ->_value through standard sprintf function. All elements self::sprintf_var (def: __vAr__) in ->_format replaced by its
	* real value, and this array go in sprintf
	*
	* @return string
	**/
	protected function mod_p(){
		//Replace by real value.
		foreach (array_keys($this->_format, self::sprintf_var) as $key){
			$this->_format[$key] = $this->_realValue;
		}
		return call_user_func_array('sprintf', $this->_format);
	}#m mod_p

	/**
	* Evalute. Evaluted only ->_value
	*
	* @return void
	**/
	protected function mod_e(){
		if (!$this->_realValued){
			eval('$this->_realValue = '.$this->_name.';');
			$this->_realValued = true;
		}
		else eval('$this->_realValue = '.$this->_realValue.';');
	}#m mod_e

	/**
	* Evaluate full! Evaluate all as full result.
	*
	* @return string
	**/
	protected function mod_EE(){
		${self::evalute_var} = $this->getValue();
		eval('$ret = '.$this->_format[0].';');
		return $ret;
	}#m mod_E

	/**
	* Value instead name
	*
	* @return void
	**/
	protected function mod_v(){
		if (!$this->_realValued){
			$this->_realValue = $this->_value;
			$this->_realValued = true;
		}
		else{
			throw new HuFormatException('Got conflicted format modifiers!');
		}
	}#m mod_e

	/**
	* ALL. Recursive parse format
	*
	* @return string
	**/
	protected function mod_AA(){
		$hf = new self(null, $this->_value, $this->_key);
		$ret = '';
		foreach ($this->_format as $f){
			$hf->setFormat($f);
			$ret .= $hf->getString();
		}
		return $ret;
	}#m mod_AA

	/**
	* Iterate by ->_value or ->_realValue.
	*
	* @return string
	**/
	protected function mod_II(){
		$hf = new self($this->_format, $t = false, $this->_key);
		$ret = '';

		foreach ($this->getValue() as $key => $v){
			$hf->setValue($v);
			$hf->_key = $key; //Only for I usefull
			$ret .= $hf->getString();
		}
		return $ret;
	}#m mod_II

	/**
	* Get Key of cunrrent iteration of I:::.
	*
	* @return string
	**/
	protected function mod_k(){
		$this->_realValue = $this->_key;
		$this->_realValued = true;
	}#m mod_k

	/**
	* As we averload getString() without arguments, implementation from HuError
	* is not suitable. So, overload it as and thown exception (class by autoload) to avoid accidentally usages.
	* @TODO It is very usefull methods. Consider implementation in the future.
	**/
	public function strToFile($format = null){ throw new ClassMethodExistsException('Method strToFile is not exists yet'); }
	public function strToWeb($format = null){ throw new ClassMethodExistsException('Method strToWeb is not exists yet'); }
	public function strToConsole($format = null){ throw new ClassMethodExistsException('Method strToConsole is not exists yet'); }
	public function strToPrint($format = null){ throw new ClassMethodExistsException('Method strToPrint is not exists yet'); }
	public function strByOutType($type, $format = null){ throw new ClassMethodExistsException('Method strByOutType is not exists yet'); }
};#c HuFormat
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuLOG
* @version 2.1.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-05-31 03:19
*
* @uses EMPTY_VAR()
* @uses NON_EMPTY_STR()
* @uses settings
* @uses debug
* @uses OS
* @uses VariableRangeException
* @uses outExtraData.interface
**/



class HuError_settings extends settings{
	// Defaults
	protected $__SETS = array(
		/**
		* @example HuLOG.php
		**/
		'FORMAT_WEB'		=> array(),	/** For strToWeb().		If empty (by default): dump::w */
		'FORMAT_CONSOLE'	=> array(),	/** For strToConsole().	If empty (by default): dump::c */
		'FORMAT_FILE'		=> array(),	/** For strToFile().	If empty (by default): dump::log */

		/**
		* @see ::updateDate()
		**/
		'AUTO_DATE'		=> true,
		'DATE_FORMAT'		=> 'Y-m-d H:i:s',
	);

	/**
	* @example
	* protected $__SETS = array(
	*	//В формате settings::getString(array)
	*	'FORMAT_CONSOLE'	=> array(
	*		array('date', "\033[36m", "\033[0m"),
	*		'level',
	*		array('type', "\033[1m", "\033[0m: ", ''),//Bold
	*		'logText',
	*		array('extra', "\n"),
	*		"\n"
	*	),
	*	'FORMAT_WEB'	=> array(
	*		array('date', "<b>", "</b>"),
	*		'level',
	*		array('type', "<b>", "</b>: ", ''),
	*		'logText',
	*		array('extra', "<br\\>\n"),
	*		"<br\\>\n"
	*	),
	*	'FORMAT_FILE'	=> array(
	*		'date',
	*		'level',
	*		array('type', '', ': ', ''),
	*		'logText',
	*		array('extra', "\n"),
	*		"\n"
	*		),
	*	),
	* );
	**/
}#c HuError_settings

class HuError extends settings implements outExtraData{
	/** Self settings. **/
	protected /* settings */ $_sets = null;
	public $_curTypeOut = OS::OUT_TYPE_BROWSER; //Track to helpers, who provide format (parts) and need known for what

	public function __construct(HuError_settings $sets = null){
		$this->_sets = EMPTY_VAR($sets, new HuError_settings);
	}#__c

	/**
	* Due to absent mutiple inheritance in PHP, just copy/paste from class get_settings.
	* Overloading to provide ref on settings without change possibility.
	* In this case change settings is allowed, but change full settings object - not!
	*
	* @param string Needed name
	* @return mixed Object of settings.
	**/
	function &__get ($name){
		switch ($name){
			case 'settings': return $this->_sets;
				break;

			case 'date':
			case 'DATE':
				if (!@$this->getProperty($name)) $this->updateDate();
			//break;	/** NOT need break. Create by read, and continue return value!

			default:
			/**
			* Set properties is implicit and NOT returned reference by default.
			* But for 'settings' we want opposite reference. Whithout capability of functions
			* overload by type arguments - is only way silently ignore Notice: Only variable references should be returned by reference
			**/
			$t = $this->getProperty($name);
			return $t;
		}
	}#m __get

	/**
	* String to print into file.
	*
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_FILE setting used.
	*	And if it settings empty (or not exists) too, just using dump::log() for all filled fields.
	* @return string
	**/
	public function strToFile($format = null){
		$this->_curTypeOut = OS::OUT_TYPE_FILE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_FILE)) return $this->getString($format);
		else return dump::log($this->__SETS, null, true);
	}#m strToFile

	/**
	* String to print into user browser.
	*
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_WEB setting used.
	*	And if it settings empty (or not exists) too, just using dump::w() for all filled fields.
	* @return string
	**/
	public function strToWeb($format = null){
		$this->_curTypeOut = OS::OUT_TYPE_BROWSER;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_WEB)) return $this->getString($format);
		else return dump::w($this->__SETS, null, true);
	}#m strToWeb

	/**
	* String to print on console.
	*
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_CONSOLE setting used.
	*	And if it settings empty (or not exists) too, just using dump::c() for all filled fields.
	* @return string
	**/
	public function strToConsole($format = null){
		$this->_curTypeOut = OS::OUT_TYPE_CONSOLE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_CONSOLE)) return $this->getString($format);
		else return dump::c($this->__SETS, null, true);
	}#m strToConsole

	/**
	* String to print. Automaticaly detect Web or Console. Detect by {@link OS::getOutType()}
	*	and invoke appropriate ::strToWeb() or ::strToConsole()
	*
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strToPrint($format = null){
		return __outExtraData__common_implementation::strToPrint($this, $format);
	}#m strToPrint

	/**
	* Convert to string by type.
	*
	* @param integer $type	One of OS::OUT_TYPE_* constant. {@link OS::OUT_TYPE_BROWSER}
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	* @Throw(VariableRangeException)
	**/
	public function strByOutType($type, $format = null){
		return __outExtraData__common_implementation::strByOutType($this, $type, $format);
	}#m strByOutType

	/**
	* Detect appropriate print (to Web or Console) and return correct form
	*
	* @return string ::strToPrint()
	**/
	public function __toString(){
		return $this->strToPrint();
	}#m __toString

	/**
	* Overload settings::setSetting() to handle autodate
	*
	* @inheritdoc
	**/
	public function setSetting($name, $value){
		parent::setSetting($name, $value);

		$this->updateDate();
	}#m setSetting

	/**
	* Overload settings::setSettingsArray() to handle autodate
	*
	* @inheritdoc
	* @return $this
	**/
	public function setSettingsArray(array $setArr){
		parent::setSettingsArray($setArr);

		//Insert after update data
		$this->updateDate();
		return $this;
	}#m setSettingsArray

	/**
	* Just alias for ::setSettingsArray()
	*
	* @param	$setArr
	* @return mixed	::setSettingsArray()
	**/
	public function setFromArray(array $setArr){
		return $this->setSettingsArray($setArr);
	}#m setFromArray

	/**
	* Overload settings::mergeSettingsArray() to handle autodate
	*
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
		//Insert BEFORE update data in merge. User data 'date' must overwrite auto, if present!
		$this->updateDate();

		parent::mergeSettingsArray($setArr);
	}#m mergeSettingsArray

	/**
	* Just alias for ::mergeSettingsArray()
	*
	* @param	$setArr
	* @return mixed	::mergeSettingsArray()
	**/
	public function mergeFromArray(array $setArr){
		$this->mergeSettingsArray($setArr);
	}#m mergeFromArray

	/**
	* If settings->AUTO_DATE == true and settings->DATE_FORMAT correctly provided - update current
	* date in ->date
	*
	* @return
	**/
	public function updateDate(){
		if (
			$this->settings->AUTO_DATE
			and
			/** Parent::setSetting instead $this-> to aviod infinity recursion */
			$this->settings->DATE_FORMAT
		)
			parent::setSetting('date', date($this->settings->DATE_FORMAT));
	}#m updateDate

	/**
	* Overloading getString to separetly handle 'extra'
	*
	* @inheritdocs
	**/
	public function formatField($field){
		if (is_array($field)){
			 if(!isset($field[0])) $field = array_values($field);
			$fieldValue = @$this->{$field[0]};
		}
		else{
			$field = (array)$field;
			$fieldValue = EMPTY_VAR(@$this->{$field[0]}, $field[0]); //Setting by name, or it is just text
		}

		if ($fieldValue instanceof outExtraData){
			return NON_EMPTY_STR($fieldValue->strByOutType($this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		elseif($fieldValue instanceof backtrace){
			return NON_EMPTY_STR($fieldValue->printout(true, null, $this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		else return NON_EMPTY_STR($fieldValue, @$field[1], @$field[2], @$field[3]);
	}#m formatField
}#c HuError
?><?
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
?><?
/**
* Database abstraction layer.
* Driver for MSSQL database server
*
* @package Database
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-10 07:50 ver 2.0 to 2.0.1
*
* @uses database
**/

class mysql_database_settings extends DB_settings {

}#c mysql_database_settings

class mysql_database extends database{
	public $db_type = 'mysql';

/* Only parent, nothing more
	function __construct(
		$sets = null	// mysql_database_settings or array
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
						mysql_errno(),
						mysql_error(),
						'',
						debug_backtrace()
					);

				// It often called from constructor. So, object is not istantiated to future cal to it getError()
				$cedbe = new ConnectErrorDBException($this->Error->settings->TXT_cantConnect, $this);
				$cedbe->DBError =& $this->Error;
				throw $cedbe;
			}
		}
	}#m db_connect

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
			throw new QueryFailedDBException($this->Error->settings->TXT_queryFailed, $this);
		}

		if ($last_id){// или просто ресурс запроса
			$res = mysql_insert_id($this->db_link);
		}

		$this->result = $res;
		// For bakward capability only. Return deprecated
		return $res;
	}//m query

	public function query_limit($query, $from, $amount, $print_query = false){
		if (!empty($from) or ! empty($amount)) $query .= ' LIMIT '.(int)$from.','.(int)$amount;
		return $this->query($query, $print_query);
	}#m query_limit

	/**
	* In MySQL not needed - implement as stub
	**/
	public function ToBlob($str){
		return $str;
	}#m ToBlob

	/**
	* MySQL does not support multiple recordset. This is possible in mysqli only.
	**/
	public function sql_next_result(){return false;}

	public function sql_escape_string(&$string_to_escape){
		return mysql_escape_string($string_to_escape);
	}#m sql_escape_string

	/**
	* TOTAL rows for query, whithout LIMIT effect
	* In case MySQL to work it properly and DO NOT slow all, insert
	* SQL_CALC_FOUND_ROWS keyword after SELECT is fully on user!!!
	* Instead, whithout it, result will be wrong, and equal ->sql_num_rows();
	*/
	public function rowsTotal(){
		$this->query('SELECT FOUND_ROWS()');
		return current($this->sql_fetch_row());
	}#m rowsTotal

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
				'bt' => new backtrace($d_backtrace, 0)
			)
		);
	}
}#c mysql_database
?><?
/**
* Database abstraction layer.
* Driver for MSSQL database server
*
* @package Database
* @version 2.1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-31 1:14 v 2.0b to 2.1
*
* @uses database
**/

$__MSSQL_Error = ''; // Global variable. I don't known other way :(
function myErrorHandler($errno, $errstr, $errfile, $errline /*, $errcontext */ ){
	global $__MSSQL_Error;
	if (stristr($errstr, 'mssql')){ //This hack only fo MSSQL errors
		$__MSSQL_Error .= $errstr;
		/* Don't execute PHP internal error handler */
		return true;
	}
		else return false; // Default error-handler
}

class mssql_database_settings extends DB_settings {
	const INT_STR_LENGTH=10; // STRING-length of int, to coding in MSSQL-"array"
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
				if ($this->settings->DEBUG){
				global $__MSSQL_Error;
				$this->collectDebugInfo(
					-1,
					mssql_get_last_message(),
					$__MSSQL_Error,
					debug_backtrace()
				);
				}

			// It often called from constructor. So, object is not istantiated to future cal to it getError()
			$cedbe = new ConnectErrorDBException($this->Error->settings->TXT_cantConnect, $this);
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

		//Recode if needed
		$this->iconv_query();

		if ($print_query) echo $query.'<br>';

		//I don't known other way handle this errors because mssql_get_last_message()
		//return only last string of error. To other I parse SDERR
		if ($this->settings->DEBUG){
			if (! @$this->old_error_handler)
				$this->old_error_handler = set_error_handler("myErrorHandler");
			/*ob_start �� �������� � ������, ���� ������ stdout � stderr! */
			global $__MSSQL_Error;
			$__MSSQL_Error = '';
		}

		if (!($res=mssql_query($query.($last_id ? ' ; SELECT @@IDENTITY as last_id' : ''), $this->db_link))){
			if ($this->settings->DEBUG){
				global $__MSSQL_Error;
				$this->collectDebugInfo(
					-1,
					mssql_get_last_message(),
					$__MSSQL_Error,
					debug_backtrace()
				);
			}
			throw new QueryFailedDBException($this->Error->settings->TXT_queryFailed, $this);
		}

		// In case INSERT statement, and if required - return Last_insert_id
		if ($last_id){
			list($res) = mssql_fetch_row($res);
		}

		$this->result = $res;
		// For backward capability only. Return deprecated
		return $res;
	}//m query

	public function query_limit($query, $from, $amount, $print_query = false){
		// Replaceqoutes: ' and " by ''
		$query = preg_replace('/[\'"]/', "''", $query);
		$this->query("EXEC proclimit '$query', $from, $amount", $print_query);
		// Errors handled before, if it occures.
		// Empty recorset. See stored Procedure proclimit and its description
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
?><?
/**
* Database abstraction layer.
* Documented AFTER creation, in progress.
* @package Database
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ???
*
* @uses EMPTY_STR()
* @uses NON_EMPTY_STR()
* @uses database_operators
**/

class database_where{
	private $_whereArr = array();
	private $_whereStr = '';

	private $_l;
	private $_r;

	private $_quote = "'";

	private $_logic = 'and';

	const default_operator = '=';

	/**
	* @TODO Hmm, may be need opposite str2arr?? Later.
	* $l, $r may be used as:
	*	$l = '[', $r = ']' for MSSQL Server
	*	$l = '`', $r = '`' for MySQL Server
	*	By default not needed.
	*
	* In case $l provided, but $r - not - $r assuming equals $l
	**/
	public function __construct(array $where = array(), $l = '', $r = '', $quote = "'"){
		$this->setArray($where, $l, $r, $quote);
	}#__c

	public function setArray(array $where, $l = '', $r = '', $quote = "'"){
		$this->_whereArr = $where;
		$this->_l = $l;
		$this->_r = EMPTY_STR($r, $l);
		$this->_quote = $quote;
		$this->_whereStr = ''; // Will be filled on request.
	}#m setArray

	/**
	* Add where conditions in end
	*
	* @param array|string	$what What append
	* @return &$this
	**/
	public function &add(/* array|string */$what){
		$this->_whereArr[] = $what;
		$this->_whereStr = null; //recalc it later
		return $this;
	}#m add

	/**
	* Return array of Where-tokens (from constructed, and may be modified).
	*
	* @return array
	**/
	public function getArray(){
		return $this->_whereArr;
	}#m getArray

	/**
	* Append another object to end of conditions.
	*
	* @return &$this
	**/
	public function append(database_where $whatAppend){
		$this->_whereArr = array_merge($this->_whereArr, $whatAppend->getArray());
		return $this;
	}#m append

	/**
	* Append another object to end of conditions.
	* Without brackets "()", we may get broken conditions after append (broken permissions f.e.)
	* F.e. we want garantee, what WHERE must be for Sender = '79052084523'. If provide this, and them, allow
	* append additional conditionals, somebody may break this (intentionally or not!) like:
	* WHERE Sender = '79052084523' OR 1<0
	* But if have brackets:
	* WHERE Sender = '79052084523' AND (1<0)
	*
	* Additionaly "AND ()" will produce error of parsing SQL-query.
	* So, use this method, for safe add conditions.
	*
	* @return &$this
	**/
	public function safeAppend(database_where $whatAppend){
		if ($whatAppend->count()){
			$this->add('AND'); //So, If expliscit given LogicOperator, brackets will be added aroud!
			$this->append($whatAppend);
		}
		return $this;
	}#m safeAppend

	/**
	* Return amount of elements
	*
	* @return integer
	**/
	public function count(){
		return count($this->_whereArr);
	}#m count

	/**
	* Return SQL-string, to using in SQL-querys statement
	*
	* @return string
	*/
	public function getSQL(){
		if (!$this->_whereStr) $this->convertToSQL();
		return $this->_whereStr;
	}#m getSQL

	/**
	* @ This is main working horse!
	* Handle user-friendly form of parameters. $this->_whereArr is array of elements:
	* $this->_whereArr
	* 1	array('ID' => 1)					-> ID=1				// Operator 'self::default_operator' is default. Field is key, value in value.
	* 2	array('ID' => array (2, '<='))		-> ID <= 2			// As 1, but value - array. Warning - Operator is SECOND argument of secod array. [Operator:=]
	* 3	array('ID' => array (2, 'BETWEEN', 15))	-> ID BETWEEN 1 AND 15
	* 4	array('ID', array (2, '<='))			-> ID <= 2			// As <2>, but 2 argument - array. Warning - Operator is SECOND argument of secod array. [Operator:=]
	* 5	array('ID', '1', 'q:>=')				-> ID>='1'			// Operator given explicit, owervise '='. One dimension array. Arrange: FieldName, FieldValue, [Operator:=]
	* 6	array('ID', '1', 'BETWEEN', 10)		-> ID BETWEEN 1 AND 10	// Special case, ternary operator.
	* 7	(string)""
	*	7.1 If string is operator from database_operators::$operators3 (such as AND, OR, XOR, && etc) - change logic (default is 'and'), and group other in (). F.e.:
	*		$this->_whereArr = array(
	*		array('ID' => 1),
	*		array('ID' => array (2, '<='))),
	*		'or'
	*		array('ID', array (2, '<='))
	*		array('ID', '1', '>=')
	*		)
	*			MUST produce: "(ID=1 and ID <=2) or (ID <= 2 or ID >= 1)"
	*
	*	7.2 Else - append string as normal SQL
	*
	* ADDITIONALY has second sintax LIKE:
	* $this->_whereArr = array(
	* 8	'ID'		=> array(1, '<'),
	* 9	'USER'	=> 5,			// or 'USER'	=> array(5)
	* )
	*
	* In both sintax if Operator contains ':' each symbol before mean:
	*	'q (Quote)' - additianaly quote FieldVaue(s) with self::quote (default:'). F.e.:
	*		array('Name', '[ABC]%', 'q:LIKE')
	*		transformed to:
	*		"Name LIKE '[ABC]%'"
	*	'e' (Escape) - additionaly Escape FieldName with $this->_l and $this->_r. F.e _l='[' and _r = ']':
	*		array('Name of field', '[ABC]%', 'q:LIKE')
	**/
	private function convertToSQL(){
		// If empty add WHERE keyword
		if (! empty($this->_whereArr)){// Has at least 1 element
			$this->_whereStr = 'WHERE (';
		}
		else return '';

		$add_logic_op = false;
		foreach ($this->_whereArr as $key => $item){
			if (is_string($item) or is_numeric($item)){//<7.x>
				if (in_array($logic = strtoupper(trim($item)), database_operators::$operatorsLogical)){
					$this->_logic = $logic;
					$this->_whereStr .= ') '.$this->_logic.' (';//<7.1>
					$add_logic_op = false;	//add operator
				}
				else $this->_whereStr .= NON_EMPTY_STR($item, ' ', ' ');//<7.2> - AS IS
			}
			else{
				// add operator
				if ($add_logic_op) $this->_whereStr .= NON_EMPTY_STR($this->_logic, ' ', ' ');

				if (is_numeric($key)){//First sintax
					/*
					$item = array('newKey' => array(newValue, operator));
					OR
					$item = array('newKey' => newValue);
					*/
					if ( 1 == sizeof($item)){
						$item = (array)$item;
						list($new_key, $new_item) = each($item);
						$this->_whereStr .= $this->constructPhrase($new_key, (array)$new_item);//<1>,<2>,<3>
					}
					else{//Key, value, Operator
						if ( is_array($item[1]) )
							$this->_whereStr .= $this->constructPhrase($item[0], $item[1]);//<4>
						else
							$this->_whereStr .= $this->constructPhrase($item[0], array_slice($item, 1));//<5>,<6>
					}
				}
				else{// Second syntax
					if ( is_array($item[0]) )
						$this->_whereStr .= $this->constructPhrase($key, $item[0]);//<9>
					else
						$this->_whereStr .= $this->constructPhrase($key, (array)$item);//<8>
				}

				// One added.
				$add_logic_op = true;
			}
		}
		$this->_whereStr .= ')';
	}#m convertToSQL

	/**
	* Parse user input in convertToString(). There have canonical form:
	* $OperVal is array of Operator and Value(s), like this:
	*	array(-8, 'qe:>=', 90)
	* @returns string
	**/
	private function constructPhrase($FieldName, array $OperVal){
		$ret = '';
		$opt = '';
		if (1 == sizeof($OperVal)){
			$op = self::default_operator;
		}
		else{
			if (strpos(@$OperVal[1], ':')){
				// May produce Notice, if single option(s), without operator
				@list ($opt, $op) = explode(':', @$OperVal[1]);
				$op = strtoupper(EMPTY_STR(trim($op), self::default_operator));
			}
			else{//Or Operator, or Empty!
				$op = EMPTY_STR(@$OperVal[1], self::default_operator);
			}
		}

		$ret .= $this->escapeFieldName($FieldName, $opt);
		switch ($op){
			case 'BETWEEN': //Special case - ternary operator
				$ret .= ' '.$op.' '.$this->quoteFieldValue($OperVal[0], $opt).' AND '.$this->quoteFieldValue($OperVal[2], $opt);
				break;

			default:
				$ret .= ' '.$op.' '.$this->quoteFieldValue($OperVal[0], $opt);
		}
		return $ret;
	}#m constructPhrase

	/**
	*
	**/
	private function escapeFieldName(&$fieldName, $opt){
		if (stristr($opt, 'e'))
		return $this->_l.$fieldName.$this->_r;
		else return $fieldName;
	}#m escapeFieldName

	/**
	*
	**/
	private function quoteFieldValue(&$fieldVal, $opt){
		if (stristr($opt, 'q'))
		return $this->_quote.$fieldVal.$this->_quote;
		else return $fieldVal;
	}#m quoteFieldValue
}#c database_where
?><?

class database_operators{
	/** Unary operators **/
	static $operators1 = array(
		'BINARY',
		'COLLATE',
		'~',
		'-' // Change the sign of the argument
	);

	/** Binary operators **/
	static $operators2 = array(
		'>>',
		'*',
		'-' /*Minus operator */,
		'RLIKE',
		'SOUNDS LIKE',
		'&',
		'|',
		'^',
		'DIV',
		'/',
		'<=>',
		'=',
		'>=',
		'>',
		'IS NOT NULL',
		'IS NOT',
		'IS NULL',
		'IS',
		'<<',
		'<=',
		'<',
		'LIKE',
		'!=, <>',
		'NOT LIKE',
		'NOT REGEXP',
		'NOT, !',
		'%',
		'+',
		'REGEXP',
	);

	/* Ternary operators */
	static $operators3 = array(
		'BETWEEN',
		'NOT BETWEEN'	// '! BETWEEN' is incorrect!
	);

	/** Logical operators **/
	static $operatorsLogical = array(
		'AND', '&&',
		'XOR',
		'||', 'OR'
	);

	/** control-flow operators **/
	static $operatorsFlow = array(
		'CASE',
	);
}#c
?><?
/**
* Database abstraction layer.
* Documented AFTER creation, in progress.
*
* @package Database
* @version 2.1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-31 16:31 v 2.0b to 2.1
*
* @uses settings
* @uses get_settings
**/

class DB_settings extends settings {

	// Deafults
	protected $__SETS = array(
	/*
		'hostname'	=> 'localhost',
		'username'	=> 'root',
		'password'	=> '',
		'dbName'		=> 'grub',
		'persistent'	=> true,
		'charset'		=> 'CP1251' // TO fire SET NAMES... {@see ::set_names()}
	*/
		'CHARSET_RECODE' => array (
			'FROM'	=> 'CP1251',	// Script charset
			'TO'		=> 'UTF-8'	// DB charset
		),
		'DEBUG'		=> false,

		/** In SUBarray in order not to generate extra Entity */
		'DBError_settings' => array(
		// Here may be overwritten defaults settings. {@see HuLOG_text_settings}
		)
	);
}#c

abstract class database extends get_settings{
	protected $_sets /* DB_settings */ = null;

	protected $db_link = null;

	protected	$Query = '';	//SQL-query
	protected	$result;		//result link
	protected	$RES;		//result Set
	public	$Field=null;	//Last field from call sql_fetch_field
	public	$Fields=null;	//Fields from call sql_fetch_fields
	protected	/* DBError */ $Error = null;

	protected	$rowsTotal;

	/**
	* Constructor.
	* @param Object(DB_settings)|array	$sets	Initial settings.
	*/
	public function __construct( /* DB_settings | array */ $sets, $dontConnect = false){
		if (is_array($sets)) $this->_sets = new DB_settings((array)$sets);
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new DB_settings();//Default

		$this->Error = new DBError(new DBError_settings($this->settings->DBError_settings));

		if (!$dontConnect){
			$this->db_connect();
			$this->db_select();
			$this->set_names();
		}
	}#__c

	public function db_select(){
		if (!call_user_func($this->db_type.'_select_db', $this->settings->dbName))
		throw new DBSelectErrorDBException($this->Error->settings->TXT_noDBselected, $this);
	}#m db_select

	/**
	 * Fire query "SET NAMES $charset".
	 * By default if $charset is null and set $this->settings->CHARSET_RECODE->TO it used,
	 *	or if defined $this->settings->charset otherwise do nothing.
	 *
	 * @param	string(null)	$charset
	 * @return	nothing
	 */
	public function set_names($charset = null){
		if ( ($ch = $charset) or ($ch = @$this->settings->CHARSET_RECODE['TO']) or ($ch = $this->settings->charset) ){
			$this->query('SET NAMES ' . $ch);
		}
	}#m set_names

	/**
	* Переопределяем, чтобы сделать ссылку на настройки не изменяемой!
	*	таким образом настройки менять можно будет, а сменить объект настроек - нет
	**/
	function &__get ($name){
		switch ($name){
			case 'settings': return $this->_sets;

			case 'RES': return $this->RES;

			case 'sql_fields':
				if (!$this->Fields) return $this->sql_fetch_fields();
			return $this->Fields;
			break;
		}
	}#m __get

	public function &sql_num_fields(){
		return call_user_func($this->db_type.'_num_fields', $this->result);
	}#m sql_num_fields

	public function &sql_fetch_field($offset = null){
		if ($offset) $this->Field = call_user_func($this->db_type.'_fetch_field', $this->result, $offset);
		else $this->Field = call_user_func($this->db_type.'_fetch_field', $this->result);
		return $this->Field;
	}#m sql_fetch_field

	public function &sql_fetch_fields(){
		while ($this->Fields[] = $this->sql_fetch_field()){}
		return $this->Fields;
	}#m sql_fetch_fields

	/**
	* Добиваемся некоторой "универсальности"
	**/
	public function &sql_fetch_assoc(){
	$this->RES = @call_user_func($this->db_type.'_fetch_assoc', $this->result);

	/**
	* Перекодировка, если надо
	**/
	$this->iconv_result();
		/*
		* Это только чтобы можно было проверять успешность операции, в условии!
		* НЕ для использования самой переменной
		**/
		return $this->RES;
	}#m sql_fetch_assoc

	public function &sql_fetch_row(){
		$this->RES = @call_user_func($this->db_type.'_fetch_row', $this->result);

		$this->iconv_result();
		return $this->RES;
	}#m sql_fetch_row

	public function &sql_fetch_array(){
		$this->RES = @call_user_func($this->db_type.'_fetch_array', $this->result);

		$this->iconv_result();
		return $this->RES;
	}#m sql_fetch_array

	public function &sql_fetch_object($className = 'stdClass', array $params = array()){
		// See http://ru2.php.net/mysql_fetch_object comments by "Chris at r3i dot it"
		if ($params) $this->RES = @call_user_func($this->db_type.'_fetch_object', $this->result, $className, $params);
		else $this->RES = @call_user_func($this->db_type.'_fetch_object', $this->result, $className);

		$this->iconv_result();
		return $this->RES;
	}#m sql_fetch_object

	public function sql_free_result(){
		call_user_func($this->db_type.'_free_result', $this->result);
		return true;
	}#m sql_free_result

	public function sql_num_rows(){
		return call_user_func($this->db_type.'_num_rows', $this->result);
	}#m sql_num_rows

	/**
	* Returns TOTAL rows for query, whithout LIMIT effect
	**/
	public function rowsTotal(){
		return $this->rowsTotal;
	}#m rowsTotal

	abstract public function db_connect();
	abstract public function query($query, $print_query = false, $last_id = false);
	abstract public function query_limit($query, $from, $amount, $print_query = false);
	abstract public function ToBlob($str);
	abstract public function sql_next_result();
	abstract public function sql_escape_string(&$string_to_escape);

	/////////////////////////Private-Protected HELPERS///////////////////////////

	/**
	* Перекодировка РЕЗУЛЬТАТА, тоесть ИЗ БД
	**/
	protected function iconv_result(){
		if (@$this->settings->CHARSET_RECODE and $this->RES){
			if (is_array($this->RES)){
				foreach ($this->RES as $key => $value){
					$this->RES[$key] = @iconv($this->settings->CHARSET_RECODE['TO'], $this->settings->CHARSET_RECODE['FROM'], $value);
				}
			}
			else{//Object
				foreach ($this->RES as $key => $value){
					$this->RES->$key = @iconv($this->settings->CHARSET_RECODE['TO'], $this->settings->CHARSET_RECODE['FROM'], $value);
				}
			}
		}
	}#m iconv_result

	/**
	* Перекодировка ЗАПРОСА, тоесть В БД
	**/
	protected function iconv_query(){
		if (@$this->settings->CHARSET_RECODE){
			$this->Query = iconv($this->settings->CHARSET_RECODE['FROM'], $this->settings->CHARSET_RECODE['TO'], $this->Query);
		}
	}#m iconv_query

	abstract protected function collectDebugInfo($errNo, $server_message, $server_messageS = '', $d_backtrace);

	/**
	* @return Object(DBError)
	*/
	public function getError(){
		return $this->Error;
	}#m getError

	/**
	* Return last (current) SQL string
	* @return
	*/
	public function getSQL(){
		return $this->Query;
	}#m getSQL

	public function __wakeup(){
		$this->db_connect();
	}#m __wakeup
}#c database
?><?
/**
* Database abstraction layer.
* Documented AFTER creation, in progress.
* @package Database
* @subpackage DBError
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @uses HuError
**/
class DBError extends HuError{
	/**
	* Constructor.
	* @param Object(DBError_settings)|array	$sets	Initial settings.
	*	If DBError_settings assigned AS IS, if array MERGED with defaults and overwrite
	*	presented settings!
	*/
	public function __construct( /* DBError_settings | array */ $sets){
		if (is_array($sets) and !empty($sets)){ // MERGE, NOT overwrite!
			$this->_sets = new DBError_settings();
			$this->_sets->mergeSettingsArray($sets);
		}
		elseif($sets) $this->_sets = $sets;
		else $this->_sets = new DBError_settings();// default
	}#__c
}

class DBError_settings extends HuError_settings{
	//Defaults
	protected $__SETS = array(
		//Aka-Constants
		'TXT_queryFailed' 	=> 'SQL Query failed',
		'TXT_cantConnect'	=> 'Could not connect to DB',
		'TXT_noDBselected'	=> 'Can not change database',

		/**
		* @see HuError::updateDate()
		*/
		'AUTO_DATE'		=> true,
		'DATE_FORMAT'		=> 'Y-m-d H:i:s: ',

		/** Header for 'extra'-data, which may be present */
		'EXTRA_HEADER'		=> 'Extra info',
		// In format settings::getString(array)
		// To out in Browser
		'FORMAT_WEB'	=> array (
			array('TXT_queryFailed', "\n<br \><u><b>", "</b></u>:\n<br \>", ''),
			'server_message',
			array('errNo', ' (', ')'),
			array ('server_messageS', "<br \>\n<b>", '</b>'),
			"\n<br><u>On query:</u> ",
			array('Query', '<pre style="color: red">', '</pre>'),
			array('bt', "<br \>\n")
		),
		// In CLI-mode
		'FORMAT_CONSOLE'	=> array (
			array('TXT_queryFailed', "\033[1m", "\033[0m:\n", ''),
			'server_message',
			array('errNo', '(', ')'),
			array('server_messageS', "\n\033[31;1m", "\033[0m"),
			"\n\033[4;1mOn query:\033[0m ",
			array('Query', "\033[31m", "\033[0m"),
			array('bt', "\n")
		),
		// Primarly for logs (files)
		'FORMAT_FILE'	=> array (
			array('TXT_queryFailed', '', ":\n"),
			'server_message',
			array('errNo', '(', ')'),
			array('server_messageS', "\n", ""),
			"\nOn query:",
			array('Query', "=>", "<="),
			array('bt', "\n")
		),
	);
}#c DBError_settings
?><?
if (!class_exists('SplDoublyLinkedList')){
/**
* This is Uglu hack for backward capability. We use SplDoublyLinkedList, but it is not present on PHP
* before 5.3.0. So, for our Iterator purpose we implement it MINIMAL.
*
* Implementation got from example: http://php.net/Iterator
*
* Deprecated for any other use than backward capability!
*
* @deprecated Since creation
**/
class SplDoublyLinkedList implements Iterator{
	private $var = array();

	public function __construct($array = array ()){
		$this->var = $array;
	}

	public function rewind(){
		reset($this->var);
	}

	public function current(){
		$var = current($this->var);
		return $var;
	}

	public function key(){
		$var = key($this->var);
		return $var;
	}

	public function next(){
		$var = next($this->var);
		return $var;
	}

	public function valid(){
		$var = $this->current() !== false;
		return $var;
	}

	public function push($item){
		$this->var[] = $item;
	}

	public function count(){
		return count($this->var);
	}
}#c SplDoublyLinkedList
}
?>