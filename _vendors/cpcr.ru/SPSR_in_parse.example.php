<?
/**
* SPSR infrastructure support to online shipping rates calculation support ( http://www.cpcr.ru/calculator.html )
* This is example of usage.
*
* @package SPSR
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-05-25 11:56 ver 1.0
*	- Initial version.
**/

include('autoload.php');

// Locale files
$s = new SPSR_in_parse($regionsfile = 'Regions.xml', $citiesfile = 'cities.xml', $citiescfile = 'citiesc.xml');
// Remote is default
//$s = new SPSR_in_parse();
$s->compile(); //3 default countries

//SPSR::dumpDOMnode($s->getCountryById(2), 'Belorussian');
//SPSR::dumpDOMnode($s->getCountryById(209), 'Russian');


$countries = $s->getCountries();
$countries->preserveWhiteSpace = false;
$countries->formatOutput = true;
echo (trim($countries->saveXML()));
?>