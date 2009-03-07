<?
/**
* Class for transliteration Russian data.
*
* @package Vars
* @subpackage translit
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-06 16:02 ver 1.0 to 1.1
*	* Add header PhpDoc. Start Version enumerate (assume was 1.0)
*	* file rename from translit_class.php to trans.class.php (it will be reflected in framework)
*	* As it now part off Vars package move it into Vars directory (as part of /Strings directory).
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
	}#m trans
}#c trans
?>