HuPHP
=====
Hubbitus PHP framework

PHP framework I long time want publish. It mostly written for PHP5 in OOP style provide some object oriented thinks which PHP 6 brings natively, implements auto debug output dependant on automatically detected output method (web, console, log), implements some external APIs like Yandex Market XML, O-range.ru SMS API, rokokassa.ru and a1agregator.ru integration and so on.

## Key futures should be mentioned

### Fully autoloading classes. Just one line needed:
```php
include('autoinclude.php');
```
Other classes will be found without explicit mention.

### Convenients debug support
* **Automatic runtime caller arguments name parsing**:
```php
include('autoload.php');

$var1 = 77;
$arr = array(1, 2);
$ha = new HuArray($arr);
$testArray = array($var1, $arr, $ha, 777);
dump::a($testArray);
dump::a(array($var1, $arr, $ha, 777));
```
On console run you got:
```php
$testArray: Array(4){
  [0] => int(77)
  [1] => Array(2){
    [0] => int(1)
    [1] => int(2)
  }
  [2] => class HuArray#1 (1) {
    protected $__SETS =>
    Array(2){
      [0] => int(1)
      [1] => int(2)
    }
  }
  [3] => int(777)
}
array($var1, $arr, $ha, 777): Array(4){
  [0] => int(77)
  …
```
**Please note what you see *array($var1, $arr, $ha, 777)* - caller time arguments name! I haven't found such functionality in any other frameworks!**
* When you try see it file in browser via web server, output automatically will be changed on colored HTML, for logs it will be more simplified.
* All formats highly customizable.

### Yet another DB abstraction
* Supported MSSQL, MySQL, SQLite.
* Charset recoding on the fly supported.
* OOP style

### Various utils
* For create on-file framework as single big PHP file or runned Phar archive
* autoloading map auto-generation
* typical project create skeleton…

### Some parts covered by tests (but much less then I want).
### RegExp support in cross-type Object oriented way
* Posix (base) and PCRE supported
```php
$re = new RegExp_pcre(
        ( $reg = '/^('.implode('|', RegExp_pcre::quote($start_long)).')('.implode('|', $optsL).
        ')(=|\b)(.*)/' ),
        $arg);
$re->doMatch();
dump::a($reg);
dump::a($re->getMatches());
dump::a($re->match(0));
```

### External services protocols and APIs implemented
* O-Range.ru SMS-partner API
* a1agregator.ru - SMS services
* Robokassa.ru - payment gateway
* sim-im.org - history parsing
* cpcr.ru - online delivery cost calculation
* dellin.ru - online delivery cost calculation
* moysklad.ru                                
* yandex.ru - Yandex Market XML (YML).

### Exception hierarchy, Vars, abstract storage, fitlesystem manipulation in most system-agnostic way.
* For some examples see code, phpdocs (with history of changes).
* Some unstructoired examples you may found in [try-examples.php](https://github.com/Hubbitus/HuPHP/blob/master/try-examples.php)


Last time I not so much interesting in PHP developing itself, but will be happy if any part of my work will be usefull for anyone. I also not promise, but will try fix found errors.
