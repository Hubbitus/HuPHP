#!/bin/bash

DIR=${DIR:-'../'}
OUT_FILE=${OUT_FILE:-"$DIR/__autoload.map.php"}

: > "$OUT_FILE"
#{
echo "<?
\$GLOBALS['__CONFIG']['__autoload_map'] = array(" >> "$OUT_FILE"

# RegExp_base.php contains Function definition in Eval as hack. so, explicit exclude it.
# try-examples.php - only examples and classes like A, B, AA....
# template/template_class_NEW2.php - is old, deprecated must be excluded to use template_class_2.1.php instead (who need old, always may preinclude them explicity).
# autoload.php has class define in eval as hack, and from functions only define __autoload, which is not requre any futher magick :)
find .. -type f -iname '*.php' \
	-not -wholename "${DIR}Debug/Phar/*" -not -name 'exec.php' -not -name $( basename "$OUT_FILE" ) \
	-not -name RegExp_base.php -not -name try-examples.php -not -name '*.example.php' -not -name autoload.php \
	-not -name template_class_NEW2.php \
	-exec egrep -iH '^\s*(abstract)?\s*class|function.*{' {} \; | \
	while read line; do
	echo $line;
	echo $line | sed -nr "/function/d;s@${DIR}(.*?\\.php):\s*(abstract)?\s*class\s+([^[:space:]{]+)(\s+extends\s+[^[:space:]{]+)?(\s+implements\s+[^[:space:]]+)*\s*\{?\}?\s*(\$|(//|#).*|;)\$@\t'\3'\t=> '\1',@g;p" \
		| grep -v 'Debug/log_dump.php' >> "$OUT_FILE"
	echo $line; # This must be processed after whole cycle, to filter dupes and do it only once
	done \
		| sed -nr "s@(${DIR}.*\\.php):.*?@\1@g;p" | sort | uniq | xargs -r -I{} ./phpsource.extract_functions.php "{}" "$DIR" >> "$OUT_FILE"

echo -n ");
?>" >> "$OUT_FILE"
#} > "$OUT_FILE"

#Check result:
php -l "$OUT_FILE"