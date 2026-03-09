#!/bin/bash

# Generate PHPDoc documentation for HuPHP framework
# Compatible with phpdocumentor v3

cd "$(dirname "$0")"
PROJECT_ROOT="$(pwd)/.."

echo "Generating PHPDoc documentation..."

# Remove any old result to start fresh
rm -rf "$PROJECT_ROOT/@phpdocs"

# Clear phpDocumentor cache
if [ -d "$PROJECT_ROOT/.phpdoc" ]; then
    echo "Clearing phpDocumentor cache..."
    rm -rf "$PROJECT_ROOT/.phpdoc/cache"
fi

# Change to project root first
cd "$PROJECT_ROOT"

# Create target directory (phpDocumentor doesn't create it automatically)
mkdir -p "@phpdocs"

TARGET_DIR="file://$PWD/@phpdocs"

php -d memory_limit=-1 "vendor/bin/phpdoc" \
    -d . \
    -t "$TARGET_DIR" \
    --title 'HuPHP framework' \
    --visibility public,protected \
    --parseprivate \
    --template default \
    --ignore "vendor" \
    --ignore "@tools" \
    --ignore "build" \
    --ignore "tests" \
    --force \
        2>&1 | tee "@tools/phpdocs.log"
echo "PHPDoc generation completed"

# Clear phpDocumentor cache
if [ -d "$PROJECT_ROOT/.phpdoc" ]; then
    echo "Clearing phpDocumentor cache..."
    rm -rf "$PROJECT_ROOT/.phpdoc/cache"
fi
