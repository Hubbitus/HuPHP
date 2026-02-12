HuPHP
=====
Hubbitus PHP framework

PHP framework for address various PHP missing features like:
- `Dump` class for easy and convenient dump variables. Not always debugger available, especially in production. And `var_dump`/`print_r` is very basic...
- `HuArray`. In PHP present `ArrayObject`, but it is also very primitive. As alternative for OOP syntax there are present extension like https://github.com/nikic/scalar_objects, but it also needs to be installed.
- `HuLog` - easy logging facility with auto configuration.
- `Vars` - interface for domain (entity) oriented programming.
- `macroses` - simple functions to handle checks and assertions, like RE

## Key futures should be mentioned

### Fully autoloading classes. Just one line needed:
```php
include('HuPHP.autoinclude.php');
```
Other classes will be found without explicit mention.

### Convenient debug support
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
* Robokassa.ru - payment gateway
* sim-im.org - history parsing
* dellin.ru - online delivery cost calculation
* moysklad.ru
* yandex.ru - Yandex Market XML (YML).

### Exception hierarchy, Vars, abstract storage, filesystem manipulation in most system-agnostic way.
* For some examples see code, phpdocs (with history of changes).
* Some unstructured examples you may find in [try-examples.php](https://github.com/Hubbitus/HuPHP/blob/master/try-examples.php)


Last time I not so much interesting in PHP developing itself, but will be happy if any part of my work will be useful for anyone. I also not promise, but will try fix found errors.
