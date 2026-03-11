#!/bin/bash
# Check code style by php-cs-fixer

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")" # "

cd "$PROJECT_ROOT"

echo "### Running php-cs-fixer check analysis..."
echo "## Project root: $PROJECT_ROOT"

# Run php-cs-fixer in dry-run mode to check code style
# --allow-risky=yes is required for native_function_invocation rule
./vendor/bin/php-cs-fixer fix --dry-run --allow-risky=yes -vvv

echo ""
echo "## php-cs-fixer analysis completed successfully!"

rm -f .php-cs-fixer.cache
