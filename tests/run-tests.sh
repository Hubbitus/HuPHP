#!/bin/bash

# Скрипт для запуска тестов, можно запускать из директории tests/
# Usage: ./run-tests.sh [--verbose] [--coverage]

# Определяем корень проекта (директория, где находится этот скрипт)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

PHPUNIT="$PROJECT_ROOT/vendor/bin/phpunit"
TESTS_DIR="$SCRIPT_DIR"

COVERAGE=true
VERBOSE=false

# Парсинг аргументов
while [[ $# -gt 0 ]]; do
    case $1 in
        --help)
            echo "Usage: $0 [--verbose] [--no-coverage]"
            echo "Options:"
            echo "  --help          Show this help"
            echo "  --verbose       Enable verbose output"
            echo "  --no-coverage   Disable code coverage report (enabled by default)"
            exit 0
            ;;
        --no-coverage)
            COVERAGE=false
            shift
            ;;
        --verbose)
            VERBOSE=true
            shift
            ;;
        *)
            echo "Unknown option: $1"
            echo "Use --help for usage"
            exit 1
            ;;
    esac
done

echo "Running tests from: $SCRIPT_DIR"
echo "Project root: $PROJECT_ROOT"

# Проверка phpunit
if [[ ! -f "$PHPUNIT" ]]; then
    echo "Error: phpunit not found at $PHPUNIT"
    echo "Run: cd '$PROJECT_ROOT' && composer install"
    exit 1
fi

# Подготовка аргументов
ARGS=()
if [[ "$VERBOSE" == true ]]; then
    ARGS+=("--verbose")
fi

if [[ "$COVERAGE" == true ]]; then
    mkdir -p "$PROJECT_ROOT/build"
    ARGS+=(
        "--coverage-html=$PROJECT_ROOT/build/coverage"
        "--coverage-text=$PROJECT_ROOT/build/coverage.txt"
        "--coverage-text=php://stdout"
    )
    echo "Generating code coverage report (enabled by default)..."
else
    echo "Code coverage disabled (--no-coverage)"
fi

# Запуск тестов
cd "$PROJECT_ROOT"
XDEBUG_MODE=coverage php -d memory_limit=512M "$PHPUNIT" "${ARGS[@]}"

echo "Tests completed successfully."

# Показать информацию о покрытии
if [[ "$COVERAGE" == true ]]; then
    echo ""
    echo "Code coverage report generated:"
    echo "  - HTML: file://$PROJECT_ROOT/build/coverage/index.html"
    echo "  - Text: $PROJECT_ROOT/build/coverage.txt"
fi