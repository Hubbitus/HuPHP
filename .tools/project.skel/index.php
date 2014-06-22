<?
include_once('includes/configs/main.config.php');

try{
	echo 'Hello WORLD!';
	/**
	* ALL REAL STUFF MUST BE PLACED HERE!
	**/
}
catch (MSG_partnerAPIException $mpae){
	Single::def('HuLOG')->toLog('Error in SMS process: ' . $mpae->getMessage(), 'ERR', 'sms');
}
catch(FilesystemException $fse){
	Single::def('HuLOG')->toLog('Error in file/net operations: ' . $fse->getMessage(), 'ERR', 'file/net');
}
catch(VariableRequiredException $vre){
	Single::def('HuLOG')->toLog('Error. Variable required: ' . $vre->varName(), 'ERR', 'var', new backtrace_out($vre->bt));
}
catch(ConnectErrorDBException $dbec){//In other DB exception handler used Single::def(__db)->getError() which may prodyce cycle
	//Single::def('HuLOG')->toLog('Database error, ' . $dbec->getMessage(), 'ERR', 'db', CONF(__db)); //May password leakage
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