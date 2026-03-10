#!/bin/bash
# Check code style by php-cs-fixer

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")" # "

cd "$PROJECT_ROOT"

echo "### Running hphp-cs-fixer check analysis..."
echo "## Project root: $PROJECT_ROOT"

# Run PHPStan with increased memory limit
./vendor/bin/php-cs-fixer check -vvv

echo ""
echo "## php-cs-fixer analysis completed successfully!"

rm -f .php-cs-fixer.cache
