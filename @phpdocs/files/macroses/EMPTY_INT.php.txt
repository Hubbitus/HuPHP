<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Macroses;

function EMPTY_INT (&$str, $defValue=0, $defValue2=0){
return ( @$str ? (int)$str : ($defValue ? (int)$defValue : (int)$defValue2 ));
}
