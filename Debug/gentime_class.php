<?
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
?>