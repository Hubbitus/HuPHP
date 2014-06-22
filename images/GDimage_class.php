<?
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
?>