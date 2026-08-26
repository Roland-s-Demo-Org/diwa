#!/bin/bash
# Test runner script for XSS mitigation tests

echo "=========================================="
echo "XSS Mitigation Test Suite"
echo "=========================================="
echo ""

# Check if PHPUnit is installed
if [ ! -f "vendor/bin/phpunit" ] && [ ! -f "app/vendor/bin/phpunit" ]; then
    echo "❌ PHPUnit not found. Installing dependencies..."
    composer install --dev
    echo ""
fi

# Determine PHPUnit path
if [ -f "vendor/bin/phpunit" ]; then
    PHPUNIT="vendor/bin/phpunit"
elif [ -f "app/vendor/bin/phpunit" ]; then
    PHPUNIT="app/vendor/bin/phpunit"
else
    echo "❌ Could not find PHPUnit. Please run 'composer install --dev' first."
    exit 1
fi

# Run tests based on argument
case "$1" in
    "unit")
        echo "Running unit tests only..."
        $PHPUNIT tests/ContactXSSMitigationTest.php
        ;;
    "integration")
        echo "Running integration tests only..."
        $PHPUNIT tests/ContactFormIntegrationTest.php
        ;;
    "coverage")
        echo "Running tests with coverage report..."
        $PHPUNIT --coverage-html coverage --coverage-text
        echo ""
        echo "✅ Coverage report generated in 'coverage' directory"
        ;;
    "verbose")
        echo "Running all tests in verbose mode..."
        $PHPUNIT --verbose
        ;;
    *)
        echo "Running all tests..."
        $PHPUNIT
        ;;
esac

echo ""
echo "=========================================="
echo "Test run complete!"
echo "=========================================="
