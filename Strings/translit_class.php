<?
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
	}#m trans
}#c translit
?>