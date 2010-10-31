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
* Class to represent region and set map to its in X-Cart database
*
* @uses REQUIRED_VAR
**/
class dellinRegion{
/**
 * stateid xcart_states table field to map X-Cart DB data
 * @var	int
 */
public $stateid;
/**
 * code xcart_states table field to map X-Cart DB data
 * @var	int
 */
public $code;
/**
 * code xcart_states table field to map X-Cart DB data. String representation. Excessive.
 * @var	string
 */
public $state;

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
 * Match from config
 * @const	int
 */
const MATCH_BY_CONFIG = 1;
/**
 * Match by starts (LIKE) with full region name
 * @const	int
 */
const MATCH_BY_FULL_NAME = 2;
/**
 * Match by starts (LIKE) with short region name
 * @const	int
 */
const MATCH_BY_SHORT_NAME = 4;

/**
 * Last match type.
 * @var	int
 */
private $matchType = self::MATCH_NOT_TRIED;

/**
 * Original city from XML file to what we try match it in DB
 * @var SimpleXMLElement.
 */
public $toMatch;

	/**
	 * Construct object from array with same keys.
	 *
	 * @param unknown_type $reg
	 */
	function __construct(array $reg) {
		foreach(array('stateid', 'code', 'state', 'matchType') as $var){
		$this->{$var} = REQUIRED_VAR($reg[$var]);
		}
	// It is optional and in config will not be present.
	$this->toMatch = @$reg['toMatch'];
	}#__c
}#c dellinRegion
?>
