<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Macro;

/**
* Unicode utility macros as static methods.
**/
class Unicode {
	/**
	* Unicode-aware ucfirst function.
	*
	* @param string $str String to process
	* @param string $enc Character encoding (default UTF-8)
	* @return string String with first character in uppercase
	**/
	public static function ucfirst(string $str, string $enc = 'UTF-8'): string {
		if ('' === $str) {
			return '';
		}

		$result = \preg_replace_callback(
			'/^./u',
			static fn(array $matches): string => \mb_strtoupper($matches[0], $enc),
			$str
		);

		return $result ?: '';
	}

	/**
	* Unicode-aware wordwrap function.
	*
	* @param string $str String to wrap
	* @param int $len Line length (default 75)
	* @param string $break Line break character (default "\n")
	* @param bool $cut Cut words if they exceed length (default false)
	* @return string Wrapped string
	**/
	public static function wordwrap(string $str, int $len = 75, string $break = "\n", bool $cut = false): string {
		$reg = $cut ? "#(.{{$len}}|.{1,$len}$)#us" : "#(.{1,$len})(?:[^\pL]|$)#us";

		$result = \preg_replace($reg, "\\1$break", $str);

		return \substr($result ?: $str, 0, -\strlen($break));
	}

	/**
	* Returns Unicode code point of first character.
	*
	* @param string $c Character
	* @return int|null Unicode code point or false on error
	**/
	public static function ord(string $c): ?int {
		$ud = 0;
		$byte0 = \ord($c[0]);

		if ($byte0 >= 0 && $byte0 <= 127) {
			$ud = $byte0;
		} elseif ($byte0 >= 192 && $byte0 <= 223) {
			$ud = ($byte0 - 192) * 64 + (\ord($c[1]) - 128);
		} elseif ($byte0 >= 224 && $byte0 <= 239) {
			$ud = ($byte0 - 224) * 4096 + (\ord($c[1]) - 128) * 64 + (\ord($c[2]) - 128);
		} elseif ($byte0 >= 240 && $byte0 <= 247) {
			$ud = ($byte0 - 240) * 262144 + (\ord($c[1]) - 128) * 4096 + (\ord($c[2]) - 128) * 64 + (\ord($c[3]) - 128);
		} elseif ($byte0 >= 248 && $byte0 <= 251) {
			$ud = ($byte0 - 248) * 16777216 + (\ord($c[1]) - 128) * 262144 + (\ord($c[2]) - 128) * 4096 + (\ord($c[3]) - 128) * 64 + (\ord($c[4]) - 128);
		} elseif ($byte0 >= 252 && $byte0 <= 253) {
			$ud = ($byte0 - 252) * 1073741824 + (\ord($c[1]) - 128) * 16777216 + (\ord($c[2]) - 128) * 262144 + (\ord($c[3]) - 128) * 4096 + (\ord($c[4]) - 128) * 64 + (\ord($c[5]) - 128);
		} elseif ($byte0 >= 254 && $byte0 <= 255) {
			return null; // Error
		}

		return $ud;
	}

	/**
	* Returns Unicode character for code point.
	*
	* @param int $dec Unicode code point
	* @return string UTF-8 encoded character
	**/
	public static function chr(int $dec): string {
		if ($dec < 128) {
			return \chr($dec);
		} elseif ($dec < 2048) {
			return \chr(192 + (($dec - ($dec % 64)) / 64)) . \chr(128 + ($dec % 64));
		} else {
			return \chr(224 + (($dec - ($dec % 4096)) / 4096))
				. \chr(128 + ((($dec % 4096) - ($dec % 64)) / 64))
				. \chr(128 + ($dec % 64));
		}
	}
}
