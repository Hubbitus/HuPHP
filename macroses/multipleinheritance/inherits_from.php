<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Macroses\multipleinheritance;

/**
 * EXPERIMENTAL: Multiple inheritance simulation using runkit extension.
 * 
 * This code requires the PECL runkit extension which is not available in
 * standard PHP installations. It's kept for historical/reference purposes only.
 * 
 * @see https://pecl.php.net/package/runkit7
 * @deprecated Runkit is not maintained for modern PHP versions
 * 
 * Function to simulate multiple inheritance in PHP. Based on possibilities of extension runkit.
 * Based upon http://rudd-o.com/archives/2006/03/18/revisiting-multiple-inheritance-in-php/
 *
 * @param        string  $destClassName Class which are inherits from other.
 * @param        array   $srcClassNameList Array of class names (strings) to inherit from.
 * @return       void
 * @example MultipleInheritance.example.php
 **/
function inherits_from($destClassName, array $srcClassNameList) {
        foreach ($srcClassNameList as $s) {
                @runkit_class_adopt($destClassName,$s);
        }
}
