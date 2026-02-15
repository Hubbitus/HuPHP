<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug\Format;

use Hubbitus\HuPHP\System\OS;

class PrintoutDefault {
    /**
    * Helper to more flexibility show large amount of data (long strings, dump of arrays etc.)
    *
    * @param string $shortVar
    * @param string $longVar
    * @param string('<textarea') $innerTagStart
    * @param string('</textarea>') $innerTagEnd
    * @return string
    **/
    public static function backtrace__printout_WEB_helper($shortVar, $longVar, $innerTagStart = '<textarea', $innerTagEnd = '</textarea>') {
        $str = '<span title="' . $longVar . '" onclick=\'' .
            'this.onclickBak=this.onclick; this.onclick=null; var ttt = this.innerHTML; this.innerHTML="' . $innerTagStart . ' style=\\"color: green; width: 50em; height: 7em; overflow: auto\\" ondblclick=\\"this.parentNode.onclick=this.parentNode.onclickBak; var ttt=this.parentNode.title; this.parentNode.title=( this.defaultValue ? this.defaultValue : this.innerHTML); this.parentNode.innerHTML = ttt; \\" + this.title + "' . $innerTagEnd . '"; this.title = ttt;\'' .
            '>' . $shortVar . '</span>';
        return '\'' . $str . '\'';
    }
    public static function configure(): void {
        /** For format description see {@link HuFormat class} **/
        $GLOBALS['__CONFIG']['backtrace::printout'] = [
            'FORMAT_WEB' => [
                'A:::' => [
                    "<div style='text-align: left; font-family: monospace; padding-left:2em'>\n<b style='color: brown'>Backtrace:</b><br />",
                    [
                        'I:::call' => [
                            'A:::' => [
                                '<b style=\'color: green\'>call:</b> ',
                                ['sn:::class', '<span style=\'color: purple\'>', '</span>'],
                                ['sn:::type', '<span style=\'color: brown\'>', '</span>'],
                                ['sn:::function', '<span style=\'color: magenta\'>', '</span>'],
                                ['E:::args', '\'(\'.$var->formatArgs().\')\''],
                                "<br />\n",

                                '&rArr;&rArr;<b style=\'color: red\'>file:</b> ',
                                ['sn:::file', '<u>', '</u>'],
                                ' - ',
                                ['sn:::line', '<b style=\'background-color: orange\'>', '</b>'],
                                "<br />\n"
                            ],
                        ],
                    ],
                    "</div>\n",
                ],

                'argtypes' => [
                    'integer' => ['v:::'], // As is
                    'double' => ['v:::'],
                    'string' => ['E:::', self::backtrace__printout_WEB_helper('\\\'\'.htmlspecialchars(substr($var, 0, 32)).((($sl = strlen($var)) < 32) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}', '\'.htmlspecialchars($var).\'')],
                    'array' => ['E:::', self::backtrace__printout_WEB_helper('\'.\'Array(\'.sizeof($var).\')\'.\'', '\'.htmlspecialchars(dump::byOutType(OS::OUT_TYPE_BROWSER, $var, null, true)).\'', '<div style=\\\"display: table; border: thick dashed green; border-top: none\\\"', '</div>')],
                    'object' => ['E:::', '\'Object(\'.get_class($var).\')\''],
                    'resource' => ['E:::', '\'Resource(\'.strstr($var, \'#\').\')\''],
                    'boolean' => ['E:::', '$var ? \'True\' : \'False\''],
                    'NULL' => 'Null',
                    'default' => ['n:::', 'Unknown (', ')'],
                ],
            ],

            'FORMAT_CONSOLE' => [
                'A:::' => [
                    "\033[1mBacktrace:\033[0m\n",
                    [
                        'I:::call' => [
                            'A:::' => [
                                "\033[32mcall:\033[0m ",
                                ['sn:::class', "\033[35m", "\033[0m"],
                                ['sn:::type', "\033[33m", "\033[0m"],
                                ['sn:::function', "\033[35;1m", "\033[0m"],
                                ['E:::args', '"\033[33m(\033[0m".$var->formatArgs()."\033[33m)\033[0m"'],
                                "\n",

                                "\t->\033[31;1mfile: \033[0m",
                                ['sp:::file', '%s', '__vAr__'],
                                ':',
                                ['sn:::line', "\033[43;1m", "\033[0m"],
                                "\n",
                            ],
                        ],
                    ],
                ],
            ],
            'FORMAT_FILE' => [
                'A:::' => [
                    "Backtrace:\n",
                    [
                        'I:::call' => [
                            'A:::' => [
                                'call: ',
                                ['s:::class'],
                                ['s:::type'],
                                ['s:::function'],
                                ['E:::args', '\'(\'.$var->formatArgs().\')\''],
                                "\n",

                                'file: ',
                                ['s:::file'],
                                ':',
                                ['s:::line'],
                                "\n",
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes'] = $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB']['argtypes'];
        $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes'] =& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB']['argtypes'];

        // Difference in argTypes
        $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes']['string']
            = $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']['string']
            = ['E:::', '\'\\\'\'.htmlspecialchars(substr($var, 0, 28)).((($sl = strlen($var)) < 28) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}\''];

        $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes']['array']
            = $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']['array']
            = ['E:::', '\'Array(\'.count($var).\')\''];

        $GLOBALS['__CONFIG']['backtrace::printout'][OS::OUT_TYPE_BROWSER] =& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB'];
        $GLOBALS['__CONFIG']['backtrace::printout'][OS::OUT_TYPE_CONSOLE] =& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE'];
        $GLOBALS['__CONFIG']['backtrace::printout'][OS::OUT_TYPE_FILE] =& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE'];
    }
}
