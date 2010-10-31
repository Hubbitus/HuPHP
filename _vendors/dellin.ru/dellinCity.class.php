<?php
/**
* Dellin.ru API.
*
* @package dellin.ru
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @link http://dellin.ru/developers/
*
* @changelog
*	* 2010-10-31 11:21 ver 1.0
*	- Initial version
**/

include_once('macroses/REQUIRED_VAR.php');

/**
 * Class represent dellin.ru city entity mapped to one in X-Cart FB (SPSR)
 *
 * @uses REQUIRED_VAR
 */
class dellinCity{
/**
 * countyid xcart_counties table field to map X-Cart DB data
 * @var	int
 */
private $countyid;
/**
 * stateid xcart_counties table field to map X-Cart DB data
 * @var	int
 */
private $stateid;
/**
 * county xcart_counties table field to map X-Cart DB data
 * @var	string
 */
private $county;

/**
 * Original city from XML file to what we try match it in DB
 * @var SimpleXMLElement.
 */
public $toMatch;

/**
 * No match untill performed.
 * @const	int
 */
const MATCH_NOT_TRIED = -1;
/**
 * No match to X-Cart item. All MATCH_* constant represent match only exactly 1
 * match present. Otherwise it treated as no match.
 * @const	int
 */
const MATCH_NO = 0;
/**
 * Exact match
 * @const	int
 */
const MATCH_EXACT = 1;
/**
 * Match with region from config
 * @const	int
 */
const MATCH_WITH_REGION_BY_CONFIG = 2;
/**
 * Match with full region name
 * @const	int
 */
const MATCH_WITH_FULL_REGION = 4;
/**
 * Match with short region name
 * @const	int
 */
const MATCH_WITH_SHORT_REGION = 8;
/**
 * Match only without region.
 * @const	int
 */
const MATCH_WITHOUT_REGION = 16;
/**
 * Last match type.
 * @var	int
 */
private $matchType = self::MATCH_NOT_TRIED;

	/**
	 *
	 * @param array $arr Instantiate from: array('countyid', 'stateid', 'county', 'matchType')
	 */
	public function  __construct(array $arr){
		foreach (array('countyid', 'stateid', 'county', 'matchType') as $var){
		$this->{$var} = REQUIRED_VAR($arr[$var]);
		}
	// It is optional and in config will not be present.
	$this->toMatch = @$arr['toMatch'];
	}#__c

	/**
	 * Return line to use in config-file in var_export format to match itself
	 * Primarly for matched cities.
	 *
	 * @return	string
	 */
	public function getPHPConfigLine(){
	return $this->countyid . "\t=> '" . REQUIRED_VAR($this->toMatch->id) . '\'';
	}#m getPHPConfigLine
}#c dellinCity
?>
