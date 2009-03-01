#!/bin/bash

DIR=${DIR:-'../'}
OUT_FILE=${OUT_FILE:-"$DIR/__autoload.map.php"}

{
echo "<?
\$GLOBALS['__CONFIG']['__autoload_map'] = array(";
find .. -iname '*.php' -not -name '*.one.php' -not -name '*.phar' -exec grep -iH '^\s*class.*{' {} \; \
	| sed -r "s@${DIR}(.*?):\s*class\s+([^[:space:]{]+)(\s+extends\s+[^[:space:]{]+)?(\s+implements\s+[^[:space:]{]+)*\s*\{\}?\s*(\$|(//|#).*|;)\$@\t'\2'\t=> '\1',@g"
echo -n ");
?>";
} > "$OUT_FILE"

#Check result:
php -l "$OUT_FILE"