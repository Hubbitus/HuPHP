<?
/**
* Function to simulate multiple inheritance in PHP. Based on possibilities of extension runkit.
* Based upon http://rudd-o.com/archives/2006/03/18/revisiting-multiple-inheritance-in-php/
*
* @param	string	$destClassName Class which are inherits from other.
* @param	array	$srcClassNameList Array of class names (strings) to inherit from.
* @return	void
@ @example MultipleInheritance.example.php
*/
function inherits_from($destClassName, array $srcClassNameList) {
	foreach ($srcClassNameList as $s) {
	@runkit_class_adopt($destClassName,$s);
	}
}#f inherits_from
?>