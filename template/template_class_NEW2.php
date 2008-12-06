<?
/*
Класс шаблонизатора. Версия 2.0a1

%Changelog
* Птн 27 Апр 2007 15:25:38
В find_path добавил поиск пути в той же директори что и шаблон (а не только класс шаблона)

* Вск 22 Апр 2007 11:24:12
Версия a1 - восстановлен из бакапа, скорее всего есть какие-то ошибки, которые уже исправлял раньше :(
*/
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

		if (!defined('TEMPLATE_TEMPLATES_DIR')) define ('TEMPLATE_TEMPLATES_DIR', false);
		if (!defined('TEMPLATE_MODULES_SUBDIR')) define ('TEMPLATE_MODULES_SUBDIR', 'modules/');

		if (!defined('TEMPLATE_DEFAULT_SCHEME')) define ('TEMPLATE_DEFAULT_SCHEME', 'default.scheme');
		if (!defined('TEMPLATE_DEFAULT_SCHEME_DIR')) define ('TEMPLATE_DEFAULT_SCHEME_DIR', false);

		if (!defined('TEMPLATE_MESSAGE')) define ('TEMPLATE_MESSAGE', 'message.tmpl');
	###########################

		if ($filename){	#С именем файла - быстрый вызов, конструктора достаточно
		$this->filename = $this->load_file($filename);
		}

		#Для времени парсинга
		if ('TEMPLATE_GENTIMECALC'){
		include_once('gentime_class.php');
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

	$ccl = new template($schemeDir.'/'.$scheme, $this, $this->_top);	#Проверки пути есть в конструкторе
	$ccl->replace('<!--<<'.basename($scheme, '.scheme').'>>-->', $this->content_file);
	$ccl->parse(false);

	$this->content_file =& $ccl->content_file;
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
?>