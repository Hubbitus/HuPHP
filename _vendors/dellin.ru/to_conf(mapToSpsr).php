#!/usr/bin/env php
<?php
/**
* Dellin.ru API.
*
* @package dellin.ru
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @link http://dellin.ru/developers/
* @created 2010-10-31 11:21
**/

include('x-cart-init.php');
error_reporting(E_ALL);// After X-Cart init

include_once('macroses/EMPTY_STR.php');

	if (db()->settings->charset) db()->query('SET NAMES ' . db()->settings->charset);

	try{
		$FILE = 'dellin_cities.xml';

		$date = date('Y-m-d'); // Caching: Get file once per day.
		system("[ ! -f '$FILE.$date' ] && wget -qO- '" . dellinParse::URL_CITIES . "' | xmllint --format - > '$FILE.$date' && ln -sb '$FILE.$date' '$FILE'");

		$dellin = new dellinParse($FILE);
		$dellin->match();

		// Write results in config
		$file = new file_inmem('mod_DELLIN.config.php');
		$file->setContentFromString("<?php\n" . $dellin->buildMapConfig() . "\n?>")->writeContent();
		dump::a('mod_DELLIN.config.php written');

		// Write results in SQL
		$file = new file_inmem('dellin_NEW_cities.sql');
		$file->setContentFromString($dellin->buildInsertsOfNewCitites())->writeContent();
		dump::a('dellin_NEW_cities.sql written');

		dump::a(//Just as main header, totals
			array(
				'Not matched cities'		=> $dellin->getNotMatchedCities()->count(),
				'With matched regions'		=> $dellin->getNotMatchedCities('withMatchedRegion')->count(),
				'With not matched regions'	=> $dellin->getNotMatchedCities('withNotMatchedRegion')->count()
			)
			,'Not matched cities and regions'
		);

		$i = 1; // Dump unmatched entries
		foreach ($dellin->getNotMatchedCities('withNotMatchedRegion') as $city){
			dump::a('exceptions trace: ' . $city->exceptionsCity->reduce(create_function('$v,$w', 'if (0===$v) $v="";/* <- Hack for PHP < 5.3.0 */ $v .= "\n" . $w->getMessage(); return $v;')), $i++ . ') ' . $city->city->{0});
			dump::a('Region parse exceptions trace: ' . $city->exceptionsRegion->reduce(create_function('$v,$w', 'if (0===$v) $v="";/* <- Hack for PHP < 5.3.0 */ $v .= "\n" . $w->getMessage(); return $v;')), 'Error matching region: ' . ($city->city->{2} . NON_EMPTY_STR($city->city->{3}, ' (', ')')));
		}
	}
	catch(VariableRequiredException $vre){
		Single::def('HuLOG')->toLog('Error. Variable required: ' . $vre->varName(), 'ERR', 'var', new backtrace_out($vre->bt));
	}
	catch(ConnectErrorDBException $dbec){//In other DB exception handler used Single::def(__db)->getError() which may prodyce cycle
		Single::def('HuLOG')->toLog('Database error, ' . $dbec->getMessage(), 'ERR', 'db', $dbec->DBError);
	}
	catch(DBException $dbe){
		Single::def('HuLOG')->toLog('Database error, ' . $dbe->getMessage(), 'ERR', 'db', Single::def(__db)->getError());
	}
	/** IF need other exception processing must be placed here! **/
	catch(Exception $e){
		Single::def('HuLOG')->toLog('UNKNOWN Exception' . $e, 'ERR', 'unkn');
	}
?>