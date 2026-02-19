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
	public ?float $time_start = null;

	public function start(): void {
		$mtime = \microtime();
		$mtime = \explode(' ', $mtime);
		$this->time_start = (float)$mtime[1] + (float)$mtime[0];
	}

	public function stop(string $mode = ''): string {
		$mtime = \microtime();
		$mtime = \explode(' ', $mtime);
		$time_end = (float)$mtime[1] + (float)$mtime[0];
		return \sprintf('%f', ($time_end - $this->time_start));
	}

	public function bench(string $code, int $iteration = 1000): void {
		\ob_start();
		$sum_time = 0.0;
		$min_time = 100.0;
		$max_time = 0.0;

		for ($i = 0; $i < $iteration; $i++) {
			$this->start();
			eval($code);
			$cur_time = (float)$this->stop();
			$sum_time += $cur_time;
			if ($cur_time > $max_time) {
				$max_time = $cur_time;
			}
			if ($cur_time < $min_time) {
				$min_time = $cur_time;
			}
		}
		\ob_end_clean();

		eval($code); // Single time again for output

		$avg = $iteration > 0 ? $sum_time / $iteration : 0.0;
		\printf('<br>Максимальное время %f секунд<br><b>Среднее время %f</b><br>Минимальное время %f<br>', $max_time, $avg, $min_time);\printf ("<br>Maximum time seconds: %f<br><b>Average time: %f</b><br>Minimum: %f<br>", $max_time, $avg, $min_time);
	}
}
