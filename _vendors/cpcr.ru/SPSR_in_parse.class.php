<?
/**
* SPSR infrastructure support to online shipping rates calculation support ( http://www.cpcr.ru/calculator.html )
*
* @package SPSR
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*
* @example SPSR_in_parse.example.php
*
* @changelog
*	* 2009-05-25 11:56 ver 1.0
*	- Initial version.
*
*	* 2009-05-25 15:30 ver 1.0 to 1.1
*	- Add filtering support.
**/

/**
* Class to parse and manipulate very different cpcr.ru data for online tariff calculation.
*
* SPSR have many files wit different structure and organazed through arse!
* Futhermore it have not normal specification how parse and links it.
* Even more evil - answers of support frequently crudely and incompetent (sometimes different from different people).
* This class is essential which I can understand from very-very-very long correspondence with this company.
*
* We want on out unified structure, but in fact have ABSOLUTELY different
* scheme on in:
* 	In the Russia(Россия) there defined regions: Region XML elements in the same file, where Countries defined:
*		http://www.cpcr.ru/cgi-bin/postxml.pl?Regions
*	But regions is NOT defined for other Countries. So, for unified approach will use '---' single region.
*	The next (end??) exception is Belarus (Белоруссия). In SPSR notation it is Region of Russia! (<Regions Id="55" Owner_Id="2" RegionName="Белоруссия"/>)
*		and its cities located in Russian xml file by region!
*		Additional to it, Белоруссия country have Белоруссия region! I rename it in self::no_region_name.
*
* The next step is Cities.
*	For the Russia it is localted in file http://www.cpcr.ru/components/xml/cities.xml
*	For all other countries in - http://www.cpcr.ru/components/xml/citiesc.xml
*	And what is more bad - filees have different structures! So, we must handle its separately.
**/
class SPSR_in_parse{
static $no_region_name = '---';

private $_regionsfile;
private $_citiesfile;
private $_citiescfile;

private /* DOMDocument */ $_regionsXML;
private /* DOMDocument */ $_citiesXML;
private /* DOMDocument */ $_citiescXML;

protected $_mainXML;
protected $_xpath;
protected $_compiled = false;

/**
* Filter some strange and incorrect items like "Дальнее зарубежье" region of Russia!. Regexp.
* @var	array $filterOut
**/
public $filterOut = array(
//	'countries'
	'regions'	=> '/Дальнее зарубежье/'
//	'cities'
);

	public function __construct(
		//$regionsfile = 'Regions.xml', $citiesfile = 'cities.xml', $citiescfile = 'citiesc.xml'
		$regionsfile	= 'http://www.cpcr.ru/cgi-bin/postxml.pl?Regions'
		,$citiesfile	= 'http://www.cpcr.ru/components/xml/cities.xml'
		,$citiescfile	= 'http://www.cpcr.ru/components/xml/citiesc.xml'
		,$outencoding	= 'utf-8'
	){
	$this->_regionsfile = $regionsfile;
	$this->_citiesfile	= $citiesfile;
	$this->_citiescfile	= $citiescfile;

	$this->_regionsXML = new DOMDocument('1.0');
	$this->_regionsXML->load($this->_regionsfile);

	$this->_mainXML = new DOMDocument('1.0', $outencoding);
	$this->_xpath = new DOMXPath($this->_regionsXML);
	}#__c

	/**
	* Compile all mesh of foles CPCR to one well-signed structure.
	*
	* @param	string	XPAth string to select countries, which interested now.
	* @return	&$this
	**/
	public function &compile($needCountries = '//Countries[normalize-space(@Country_Name)="Россия" or normalize-space(@Country_Name)="Белоруссия" or normalize-space(@Country_Name)="Украина"]'){
		foreach($this->_xpath->query($needCountries) as $country){
		$this->compileCountry($country);
		}
	$this->_compiled = true;
	return $this;
	}#m compile

	/**
	* Compile separate country.
	*
	* @param	Object(DOMElement) $country
	* @return	&$this
	**/
	protected function compileCountry(DOMElement $country){
	$doc = new DOMDocument('1.0', 'utf-8'); // DOMDocument NEEDED ot import into it nodes, it also NEEDED to export result asXML...
	$doc->appendChild($doc->importNode($country, true));
	$doc->preserveWhiteSpace = false;
	$doc->formatOutput = true;

		if (isset($this->filterOut['countries']) and preg_match($this->filterOut['countries'], $country->getAttribute('Country_Name'))) continue;
		/*
		* For understanding this magick, see description of class itself.
		*/
		if ( 'Россия' == $country->getAttribute('Country_Name') or 'Белоруссия' == $country->getAttribute('Country_Name')){
		$this->loadCities();
			foreach ($this->_xpath->query('//Regions[@Owner_Id="' . $country->getAttribute('Owner_Id') . '"]') as $region){
				if (isset($this->filterOut['regions']) and preg_match($this->filterOut['regions'], $region->getAttribute('RegionName'))) continue;
			//http://ru2.php.net/manual/ru/domdocument.importnode.php
			$reg = $doc->firstChild->appendChild($doc->importNode($region, true));
				foreach ($this->parseCitiesRussian($region) as $city){
					if (isset($this->filterOut['cities']) and preg_match($this->filterOut['cities'], $city->getAttribute('CityName'))) continue;
				$reg->appendChild($doc->importNode($city, true));
				}
			}

			if('Белоруссия' == $country->getAttribute('Country_Name')){
			$xpath = new DOMXPath($doc);
			$xpath->query('//Regions[@RegionName="Белоруссия"]')->item(0)->setAttribute('RegionName', self::$no_region_name);
			}
		}
		else{
		$this->loadCitiesc();
	//	$reg = $doc->firstChild->appendChild($doc->createElement('Region'));
		$reg = $doc->firstChild->appendChild(new DOMElement('Region'));
		$reg->setAttribute('Id', -1);
		$reg->setAttribute('Owner_Id', 0);
		$reg->setAttribute('RegionName', self::$no_region_name);
			foreach ($this->parseCitiesForeign($country) as $city){
				if (isset($this->filterOut['cities']) and preg_match($this->filterOut['cities'], $city->getAttribute('CityName'))) continue;
			$city = $reg->appendChild($doc->importNode($city, true));
			//Rename Attributes to follow single naming scheme
			$city->setAttribute('Id', $city->getAttribute('id'));
			$city->removeAttribute('id');

			$city->setAttribute('Owner_Id', $city->getAttribute('owner_id'));
			$city->removeAttribute('owner_id');
			}
		}

	$this->_mainXML->appendChild($this->_mainXML->importNode($doc->firstChild, true));
	return $this;
	}#m compileCountry

	/**
	* Return Country from compiled list (if was not compiled yet - compiled by default) by its ID.
	*
	* @param	int	$id
	* @return	Object(DOMElement)
	**/
	public function getCountryById($id){
		if (!$this->_compiled) $this->compile();

	/*
	* We can't use $this->_mainXML->getElementById($id); because documeent have not DTD and nothing defined as ID attribute!
	**/
	$list = $this->getCountryByXPath('//Countries[@Id="' . $id . '"]');
	assert($list->length <= 1);
	return @$list->item(0);
	}#m getCountryById

	/**
	* Return Country from compiled list (if was not compiled yet - compiled by default) by its name.
	*
	* @param	string	$name
	* @return	Object(DOMElement)|null
	**/
	public function getCountryByName($name){
		if (!$this->_compiled) $this->compile();

	$list = $this->getCountryByXPath('//Countries[normalize-space(@Country_Name)="' . $name . '"]');
	assert($list->length <= 1);
	return @$list->item(0);
	}#m getCountryByName

	/**
	* Return Country from compiled list (if was not compiled yet - compiled by default) by Xpath.
	*
	* @param	string	$xpathquery
	* @return	Object(DOMNodeList)
	**/
	public function getCountryByXPath($xpathquery){
		if (!$this->_compiled) $this->compile();

	$xpath = new DOMXPath($this->_mainXML);
	return $xpath->query($xpathquery);
	}#m getCountryByXPath

	/**
	* Return all countries.
	*
	* @return	Object(DOMDocument)
	**/
	public function getCountries(){
	return $this->_mainXML;
	}#m getCountries

	/**
	* Foreign countries do not devided to regions, only Russia.
	* Return DOMNodeList of cities belong to region.
	*
	* @param	integer	$regionId
	* @return	Object(DOMNodeList)
	**/
	public function parseCitiesRussian(DOMElement &$region){
	$xpath = new DOMXPath($this->_citiesXML);
	return $xpath->query('//c[@Region_ID="' . $region->getAttribute('Id') . '" and @Region_Owner_ID="' . $region->getAttribute('Owner_Id') . '"]');
	}#m parseCitiesRussian

	/**
	* Foreign countries do not devided to regions, only Russia.
	* Return DOMNodeList of cities belong to country.
	*
	* @param	integer	$regionId
	* @return	Object(DOMNodeList)
	**/
	public function parseCitiesForeign(DOMElement &$country){
	$xpath = new DOMXPath($this->_citiescXML);
	return $xpath->query('//c[@countries_id="' . $country->getAttribute('Id') . '" and @countries_owner_id="' . $country->getAttribute('Owner_Id') . '"]');
	}#m parseCitiesForeign

	/**
	* Load XML file of Russian cities if it is not loaded yet (or second argument set to true)
	*
	* @param	boolean	$force If true - in any case load.
	* @return	&$this
	**/
	public function &loadCities($force = false){
		if (!$this->_citiesXML and !$force){
		$this->_citiesXML = new DOMDocument('1.0');
		$this->_citiesXML->load($this->_citiesfile);
		$this->_compiled = false;
		}
	return $this;
	}#m loadCities

	/**
	* Load XML file of Foreign cities if it is not loaded yet (or second argument set to true)
	*
	* @param	boolean	$force If true - in any case load.
	* @return	&$this
	**/
	public function &loadCitiesc($force = false){
		if (!$this->_citiescXML and !$force){
		$this->_citiescXML = new DOMDocument('1.0');
		$this->_citiescXML->load($this->_citiescfile);
		$this->_compiled = false;
		}
	return $this;
	}#m loadCitiesc

	/**
	* Helper (temp?) function to output Object(DOMElement) as XML
	*
	* @param	Object(DOMNode)	$elem
	* @param	string	$header
	* @return	nothing
	**/
	public static function dumpDOMnode(DOMNode $elem, $header = 'DOMElement'){
	$o = new DOMnodeOutExtraData($elem);
	dump::a(trim($o->strToPrint()), $header);
	}#m dumpDOMnode
}#c SPSR_in_parse
?>