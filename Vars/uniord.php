<?
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
?>