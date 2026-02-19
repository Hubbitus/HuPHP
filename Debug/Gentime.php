<?php
declare(strict_types=1);

/**
* Debug and backtrace toolkit.
*
* @package Debug
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ???
**/

namespace Hubbitus\HuPHP\Debug;

class Gentime {
	private string $time_start;

	public function start(){
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] . $mtime[0];
		$this->time_start = $mtime;
	}

	private function stop(){
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		return sprintf ("%f", ($mtime - $this->time_start));// Seconds
	}

	public function bench($code, $iteration = 1000){
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
		$avg = $iteration > 0 ? $sum_time / $iteration : 0;
		\printf ("<br>Maximum time seconds: %f<br><b>Average time: %f</b><br>Minimum: %f<br>", $max_time, $avg, $min_time);
	}
}
