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

class dellinNotMatchedException extends BaseException{};
class dellinNotApplicableException extends dellinNotMatchedException{};

/**
* Class to parse dellin.ru Cities of delivery and map its to our current S-Cart database values (inspired by SPSR structure).
* Parsing limited only to Russia.
*
* @uses	HuClass, REQUIRED_VAR
* @example	to_conf(mapToSpsr).php
**/
class dellinParse{
const URL_CITIES = 'http://public.services.dellin.ru/calculatorService2/index.html?request=xmlForm';

/**
 * Object of city-regions XML document
 * @var	SimpleXMLElement
 */
private $xml_;
/**
 * Current parsed region. To do not return it from methods tied several techniques.
 * @var	dellinRegion
 */
private $curRegion_;

/**
 * Surrent parsed by RegExp city.
 * @var	HuArray
 */
private $curCity_;

/**
 * Current original city from XML file to what we try match it in DB
 * @var	SimpleXMLElement
 */
private $curCityToMatch_;

/**
 * Base part of SQL query to try several type matches of cities.
 * @const	string
 */
const MATCH_SQL_CITY = 'SELECT
		c.countyid, c.stateid, county, state, code as state_code, country_code
	FROM
		xcart_counties c LEFT JOIN xcart_states s ON (s.stateid = c.stateid)
	WHERE country_code = "RU" ';

/**
 * Base part of SQL query to try several type matches of regions.
 * @const	string
 */
const MATCH_SQL_REGION = 'SELECT *
	FROM
		xcart_states
	WHERE country_code = "RU" ';

/**
* Accumulate axceptions during match process of cities.
* $var	HuArray
**/
private $exceptionsCity;

/**
* Accumulate axceptions during match process of cities.
* $var	HuArray
**/
private $exceptionsRegion;

/**
 * HuArray of HuArrays matched cities with keys:
 *	city - parsed city (Object(dellinCity))
 *	exceptionsCity - HuArray of exception parsing
 * @var	HuArray
 */
private $matchedCities;
/**
 * HuArray of HuArrays not matched cities with keys:
 *	city - city (Object(HuArray))
 *	exceptionsCity - HuArray of exceptions parsing city
 *	region - region if present
 *	exceptionsRegion - HuArray of exceptions parsing region
 * @var	HuArray
 */
private $notMatchedCities;

	/**
	*
	* @param	string $xmlSource Filename or URL of XML file.
	**/
	public function __construct($xmlSource){
	$this->xml_ = simplexml_load_file($xmlSource);
	$this->matchedCities = new HuArray();
	$this->notMatchedCities = new HuArray();
	}#__c

	/**
	* Main horse - cycle by all presented values and try mach it to current set
	* in various way - start from most precisious to less.
	*
	* For cities wher no matches found start {@see matchRegion()} to do not get
	* not required overhead.
	**/
	public function match(){
		foreach ($this->xml_->cities->city as $this->curCityToMatch_){
		$this->curCity_ = classCreate('RegExp_pcre', '/([^()]+)(?>\s|$)(?:\(((\pL+).*?)\))?/iu', (string)$this->curCityToMatch_->name)->doMatch()->getHuMatches();
		$this->exceptionsCity = new HuArray();
			try{
			$matched = $this->_matchByConfig();
			}
			catch(dellinNotMatchedException $e){
			$this->exceptionsCity->push($e);
				try{
				$matched = $this->_matchWithRegionByConfig();
				}
				catch(dellinNotMatchedException $e){
				$this->exceptionsCity->push($e);
					try{
					$matched = $this->_matchWithFullRegion();
					}
					catch(dellinNotMatchedException $e){
					$this->exceptionsCity->push($e);
						try{
						$matched = $this->_matchWithShortRegion();
						}
						catch(dellinNotMatchedException $e){
						$this->exceptionsCity->push($e);
							try{
							$matched = $this->_matchWithoutRegion();
							}
							catch(dellinNotMatchedException $e){
							$this->exceptionsCity->push($e);
//							dump::a("STOP. No match reached for '{$this->curCity_->{0}}'. exceptions trace: " . $this->exceptionsCity->reduce(create_function('$v,$w', 'if (0===$v) $v="";/* <- Hack for PHP < 5.3.0 */ $v .= "\n" . $w->getMessage(); return $v;')), 'Match not found');
							$this->notMatchedCities->push(
								new HuArray(
									array(
										'city' => $this->curCity_
										,'region' => $this->matchRegion()
										,'exceptionsCity' => $this->exceptionsCity
										,'exceptionsRegion' => $this->exceptionsRegion
									)
								)
							);
							continue; //No more match types available
							}
						}
					}
				}
			}
		$this->matchedCities->push(
			new HuArray(
				array(
					'city' => $matched
					,'exceptionsCity' => $this->exceptionsCity
				)
			)
		);
		}
	}#m match

	/**
	* Very similar to {@see match()} method, but starts similar match process
	*	for region, not city.
	* Do NOT do any cicles and try match only current region! As write before
	*	intended for use from {@see match()}, but may be invoked outside also.
	*
	* For optimisation purposes we use it from match only for not matched cities,
	* where used tables joins for selects, which should be more efficient then
	* subsequents queries
	*
	* @return	Object(dellinRegion)
	**/
	public function matchRegion(){
	$this->exceptionsRegion = new HuArray();
		try{
		$matched = $this->_matchRegionByConfig();
		}
		catch(dellinNotMatchedException $e){
		$this->exceptionsRegion->push($e);
			try{
			$matched = $this->_matchRegionByFullName();
			}
			catch(dellinNotMatchedException $e){
			$this->exceptionsRegion->push($e);
				try{
				$matched = $this->_matchRegionByShortName();
				}
				catch(dellinNotMatchedException $e){
				$this->exceptionsRegion->push($e);
				return null;
				}
			}
		}
	return $matched;
	}#m matchRegion

	/**
	* Helper function to dump all results of query result.
	*
	* @param	database	$db
	* @return	void|string
	**/
	private function _dumpDBresults(database &$db, $return = true){
	$ret = "\n";
		while ($db->sql_fetch_object()){
			if ($return){
			$ret .= rtrim(dump::log($db->RES, 'match', true));
			}
			else{
			dump::a($db->RES, 'match');
			}
		}
		if($return) return $ret;
	}#m _dumpDBresults

	/**
	* Direct return city from config. Throw exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchByConfig(){
		if (!($city = CONF('cities')->{$this->curCity_->{1}}))
		throw new dellinNotMatchedException("City '{$this->curCity_->{1}}' not found in config cities. Method: " . __METHOD__);
	$city->toMatch = $this->curCityToMatch_;
	return $city;
	}#m _matchByConfig

	/**
	* Direct return region from config. Throw exception otherwise.
	*
	* @return Object(dellinRegion)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchRegionByConfig(){
		if (!($this->curRegion_ = CONF('regions')->{$this->curCity_->{2}}))
		throw new dellinNotMatchedException("Region '{$this->curCity_->{2}}' not found in config regions. Method: " . __METHOD__);
	$th->toMatch = $this->curCityToMatch_;
	return $this->curRegion_;
	}#m _matchRegionByConfig

	/**
	* Tries match exact city name in DB and region from config.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchWithRegionByConfig(){
		try{
		$this->_matchRegionByConfig();
		}
		catch(dellinNotApplicableException $e){
		throw new dellinNotApplicableException("Region '{$this->curCity_->{2}}' is not found in config, this matching is not possible. Method: " . __METHOD__);
		}
	$sql = self::MATCH_SQL_CITY . "AND county = '{$this->curCity_->{1}}' AND c.stateid = {$this->curRegion_->stateid}  AND code = {$this->curRegion_->code}";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("'{$this->curCity_->{1}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . '. Matches: ' . $this->_dumpDBresults(db(), true));
	db()->sql_fetch_object();
	return new dellinCity(
		array(
			'countyid'	=> db()->RES->countyid
			,'stateid'	=> db()->RES->stateid
			,'county'		=> db()->RES->county
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinCity::MATCH_WITH_REGION_BY_CONFIG
		)
	);
	}#m _matchWithRegionByConfig

	/**
	* Tries match exact city name in DB and region by starts (LIKE) from full name.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchWithFullRegion(){
		if(!$this->curCity_->{2}){
		throw new dellinNotApplicableException('Full region name is not present, this matching is not possible. Method: ' . __METHOD__);
		}
	$sql = self::MATCH_SQL_CITY . "AND county = '{$this->curCity_->{1}}' AND state LIKE '{$this->curCity_->{2}}%'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("'{$this->curCity_->{1}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . ' (' . $this->curCity_->{2} . ').' . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinCity(
		array(
			'countyid'	=> db()->RES->countyid
			,'stateid'	=> db()->RES->stateid
			,'county'		=> db()->RES->county
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinCity::MATCH_WITH_FULL_REGION
		)
	);
	}#m _matchWithFullRegion

	/**
	* Tries match by start region full name in DB.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinRegion)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchRegionByFullName(){
		if(!$this->curCity_->{2}){
		throw new dellinNotApplicableException('Full region name is not present, this matching is not possible. Method: ' . __METHOD__);
		}
	$sql = self::MATCH_SQL_REGION . "AND state LIKE '{$this->curCity_->{2}}%'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("Region '{$this->curCity_->{2}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinRegion(
		array(
			'stateid'		=> db()->RES->stateid
			,'code'		=> db()->RES->code
			,'state'		=> db()->RES->state
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinRegion::MATCH_BY_FULL_NAME
		)
	);
	}#m _matchRegionByFullName

	/**
	* Tries match exact city name in DB and region by starts (LIKE) from short name.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchWithShortRegion(){
		if(!$this->curCity_->{3}){
		throw new dellinNotApplicableException('Short region name is not present, this matching is not possible. Method: ' . __METHOD__);
		}
	$sql = self::MATCH_SQL_CITY . "AND county = '{$this->curCity_->{1}}' AND state LIKE '{$this->curCity_->{3}}%'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("'{$this->curCity_->{1}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . ' (' . $this->curCity_->{3} . ').' . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinCity(
		array(
			'countyid'	=> db()->RES->countyid
			,'stateid'	=> db()->RES->stateid
			,'county'		=> db()->RES->county
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinCity::MATCH_WITH_SHORT_REGION
		)
	);
	}#m _matchWithShortRegion

	/**
	* Tries match by start region full name in DB.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinRegion)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchRegionByShortName(){
		if(!$this->curCity_->{3}){
		throw new dellinNotApplicableException('Short region name is not present, this matching is not possible. Method: ' . __METHOD__);
		}
	$sql = self::MATCH_SQL_REGION . "AND state LIKE '{$this->curCity_->{3}}%'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("Region '{$this->curCity_->{3}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinRegion(
		array(
			'stateid'		=> db()->RES->stateid
			,'code'		=> db()->RES->code
			,'state'		=> db()->RES->state
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinRegion::MATCH_BY_FULL_NAME
		)
	);
	}#m _matchRegionByShortName

	/**
	* Tries match exact city name in DB and region by starts (LIKE) from full name.
	* Accept only 1 equals trows exception otherwise.
	*
	* @return Object(dellinCity)
	* @Throws(dellinNotMatchedException)
	**/
	private function _matchWithoutRegion(){
	$sql = self::MATCH_SQL_CITY . "AND county = '{$this->curCity_->{1}}'";
	db()->query($sql);

		if(1 != db()->sql_num_rows())
		throw new dellinNotMatchedException("'{$this->curCity_->{1}}' matched to " . db()->sql_num_rows() . ' results in ' . __METHOD__ . '.' . (db()->sql_num_rows() ? ' Matches: ' . $this->_dumpDBresults(db(), true) : ''));
	db()->sql_fetch_object();
	return new dellinCity(
		array(
			'countyid'	=> db()->RES->countyid
			,'stateid'	=> db()->RES->stateid
			,'county'		=> db()->RES->county
			,'toMatch'	=> $this->curCityToMatch_
			,'matchType'	=> dellinCity::MATCH_WITHOUT_REGION
		)
	);
	}#m _matchWithoutRegion

	/**
	 * Return HuArray of matched cities
	 * @return	HuArray
	 */
	public function getMatchedCities(){
	return $this->matchedCities;
	}#m getMatchedCities

	/**
	 * Return HuArray of NOT matched cities
	 *
	 * @param	string	$whatRegion. String, represent what set needed, available values are:
	 *	'all'				- All not matched cities. Default.
	 *	'withMatchedRegion'		- Only with matched region
	 *	'withNotMatchedRegion'	- Only with region which is not matched
	 * @return	HuArray CLONED, not reference!
	 * @Throws	VariableRangeException
	 */
	public function getNotMatchedCities($whatRegion = 'all'){
		switch($whatRegion){
			case 'all':
				return HuClass::cloning($this->notMatchedCities);
			break;
			case 'withMatchedRegion':
				return HuClass::cloning($this->notMatchedCities)->filter(create_function('$v', 'return !is_null($v->region);'));
			break;
			case 'withNotMatchedRegion':
				return HuClass::cloning($this->notMatchedCities)->filter(create_function('$v', 'return is_null($v->region);'));
			break;
			default:
				throw new VariableRangeException("'$whatRegion' is not proper value of \$whatRegion parameter");
		}

	}#m getNotMatchedCities

	/**
	 * Build text representation of array for config-file in var_export format
	 *	to map matched values
	 *
	 * @return	string
	 */
	public function buildMapConfig(){
	$ret = "\$GLOBALS['__CONFIG']['dellin'] = array(
	#countyid => XML id\n";
		foreach ($this->getMatchedCities() as $city){
 		$ret .= "\t" . $city->city->getPHPConfigLine() . ",\n";
		}
	return $ret .= ');';
	}#m buildMapConfig

	/**
	 * Build SQL insertions text of new citties where region parsed.
	 *
	 * @return	string
	 */
	public function buildInsertsOfNewCitites(){
	$ret = '';
		// All new, but with region parsed
		foreach ($this->getNotMatchedCities('withMatchedRegion') as $city){
		$ret .= 'INSERT INTO xcart_counties (stateid, county) VALUES (' . REQUIRED_VAR($city->region->stateid) . ', "' . REQUIRED_VAR($city->city->{1}) . '");' . "\n";
		}
	return $ret;
	}#m buildInsertsOfNewCitites
};//c dellinParse
?>