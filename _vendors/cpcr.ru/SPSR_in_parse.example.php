<?
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