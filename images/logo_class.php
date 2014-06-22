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
* @uses GDimage
**/

include_once('include/config.php');

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
?>