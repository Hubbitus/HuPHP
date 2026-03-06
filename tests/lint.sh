#!/bin/bash
# Static analysis using PHPStan
# This script runs PHPStan static analyzer on the codebase

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

cd "$PROJECT_ROOT"

echo "Running PHPStan static analysis..."
echo "Project root: $PROJECT_ROOT"

# Run PHPStan
./vendor/bin/phpstan analyse --no-progress "$@"

echo ""
echo "PHPStan analysis completed successfully!"
