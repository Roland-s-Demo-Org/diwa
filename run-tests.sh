#!/bin/bash

# Test runner script for XSS security tests

echo "=========================================="
echo "Running XSS Security Tests for register.php"
echo "=========================================="
echo ""

# Check if composer dependencies are installed
if [ ! -d "vendor" ]; then
    echo "Installing dependencies..."
    composer install
    echo ""
fi

# Run PHPUnit tests
if [ -f "vendor/bin/phpunit" ]; then
    echo "Running tests..."
    ./vendor/bin/phpunit --colors=always --verbose
    
    EXIT_CODE=$?
    
    echo ""
    echo "=========================================="
    if [ $EXIT_CODE -eq 0 ]; then
        echo "✓ All tests passed!"
    else
        echo "✗ Some tests failed!"
    fi
    echo "=========================================="
    
    exit $EXIT_CODE
else
    echo "Error: PHPUnit not found. Please run 'composer install' first."
    exit 1
fi
