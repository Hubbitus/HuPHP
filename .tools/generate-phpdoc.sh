#!/bin/bash

# Generate PHPDoc documentation for HuPHP framework
# Compatible with phpdocumentor v3

cd "$(dirname "$0")"
PROJECT_ROOT="$(pwd)/.."

echo "Generating PHPDoc documentation..."

# Remove any problematic config files
rm -f "$PROJECT_ROOT/phpdoc.xml" "$PROJECT_ROOT/phpdoc.xml.dist" "$PROJECT_ROOT/.phpdoc.php"

# Use phpdocumentor v3 with minimal working options
if [ -x "$PROJECT_ROOT/vendor/bin/phpdoc" ]; then
    # Try the most compatible command for v3
    php -d memory_limit=-1 "$PROJECT_ROOT/vendor/bin/phpdoc" project:run \
        --directory "$PROJECT_ROOT/Debug" \
        --directory "$PROJECT_ROOT/Exceptions" \
        --directory "$PROJECT_ROOT/Filesystem" \
        --directory "$PROJECT_ROOT/Macro" \
        --directory "$PROJECT_ROOT/RegExp" \
        --directory "$PROJECT_ROOT/System" \
        --directory "$PROJECT_ROOT/Vars" \
        --target "$PROJECT_ROOT/@phpdocs" \
        --title "Pavel Alexeev aka Pahan-Hubbitus HuPHP framework documentation" \
        --visibility public \
        --parseprivate \
        --sourcecode \
        --template default \
        --ignore @phpdocs,.git,.svn,vendor,phpdocs.log 2>&1 | tee "$PROJECT_ROOT/.tools/phpdocs.log"
    
    echo "PHPDoc generation completed"
else
    echo "ERROR: phpdocumentor not found at $PROJECT_ROOT/vendor/bin/phpdoc"
    exit 1
fi