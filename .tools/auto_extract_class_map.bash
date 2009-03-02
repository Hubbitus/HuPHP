#!/bin/bash

DIR=${DIR:-'../'}
OUT_FILE=${OUT_FILE:-"$DIR/__autoload.map.php"}

: > "$OUT_FILE"
#{
echo "<?
\$GLOBALS['__CONFIG']['__autoload_map'] = array(" >> "$OUT_FILE"

find .. -iname '*.php' -not -wholename "${DIR}Debug/Phar/*" -not -name 'exec.php' -not -name $( basename "$OUT_FILE" ) -exec egrep -iH '^\s*class|function.*{' {} \; | \
	while read line; do
	echo $line | sed -nr "/function/d;s@${DIR}(.*?\\.php):\s*class\s+([^[:space:]{]+)(\s+extends\s+[^[:space:]{]+)?(\s+implements\s+[^[:space:]]+)*\s*\{?\}?\s*(\$|(//|#).*|;)\$@\t'\2'\t=> '\1',@g;p" \
		| grep -v 'Debug/log_dump.php' >> "$OUT_FILE"
	echo $line; # This must be processed after whole cycle, to filter dupes and do it only once
	done | sed -nr "s@(${DIR}.*\\.php):.*?@\1@g;p" | sort | uniq | xargs -r -I{} ./phpsource.extract_functions.php "{}" "$DIR" >> "$OUT_FILE"

echo -n ");
?>" >> "$OUT_FILE"
#} > "$OUT_FILE"

#Check result:
php -l "$OUT_FILE"