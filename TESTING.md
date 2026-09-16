# XSS Fix Testing - Quick Start Guide

## What Was Fixed

The XSS vulnerability in `app/content/register.php` has been fixed by applying `htmlentities()` with `ENT_QUOTES` flag to user input in the username and email fields.

## Test Files Created

```
tests/
├── RegisterXSSTest.php          # Unit tests for XSS escaping
├── RegisterHTMLOutputTest.php   # Integration tests for HTML output
├── bootstrap.php                # Test environment setup
├── manual-test.php              # Visual browser-based test
├── README.md                    # Test suite overview
└── TEST_DOCUMENTATION.md        # Comprehensive documentation

phpunit.xml                      # PHPUnit configuration
run-tests.sh                     # Convenient test runner script
composer.json                    # Updated with PHPUnit dependency
```

## Running Tests

### Option 1: Using Composer (Recommended)

```bash
# Install dependencies (first time only)
composer install

# Run all tests
composer test
```

### Option 2: Using the Test Runner Script

```bash
bash run-tests.sh
```

### Option 3: Using PHPUnit Directly

```bash
# Run all tests
./vendor/bin/phpunit

# Run with verbose output
./vendor/bin/phpunit --verbose

# Run specific test file
./vendor/bin/phpunit tests/RegisterXSSTest.php

# Run specific test
./vendor/bin/phpunit --filter testUsernameXSSEscaping
```

### Option 4: Manual Browser Testing

```bash
# Start PHP server
php -S localhost:8000 -t tests/

# Open in browser
# Navigate to: http://localhost:8000/manual-test.php
```

## What the Tests Verify

✅ Script tags are properly escaped  
✅ Single quotes are escaped (prevents attribute injection)  
✅ Double quotes are escaped (prevents attribute injection)  
✅ Various XSS attack vectors are blocked  
✅ Legitimate user input still works correctly  
✅ Form structure remains intact  
✅ OWASP XSS payloads are neutralized  

## Expected Output

When tests pass, you'll see:

```
PHPUnit 9.5.x by Sebastian Bergmann and contributors.

RegisterXSSTest
 ✔ Username XSS escaping
 ✔ Email XSS escaping
 ✔ Username with single quotes
 ✔ Email with double quotes
 ✔ Legitimate input preserved
 ✔ Various XSS vectors in username
 ✔ Various XSS vectors in email
 ✔ ENT quotes flag
 ✔ Empty post values

RegisterHTMLOutputTest
 ✔ Username input value attribute
 ✔ Email input value attribute
 ✔ Attribute injection single quote
 ✔ Attribute injection double quote
 ✔ HTML entities in username
 ✔ Unicode characters
 ✔ Form structure integrity
 ✔ OWASP XSS payload
 ✔ Polyglot XSS payload
 ✔ Htmlentities used

Time: 00:00.123, Memory: 6.00 MB

OK (18 tests, 45 assertions)
```

## Troubleshooting

### "PHPUnit not found"
```bash
composer install
```

### "Class not found" errors
Make sure you're running tests from the project root directory.

### Tests fail
Check that the fix is properly applied in `app/content/register.php`:
- Line 101: Username field should use `htmlentities(..., ENT_QUOTES)`
- Line 105: Email field should use `htmlentities(..., ENT_QUOTES)`

## More Information

- See `tests/README.md` for test suite overview
- See `tests/TEST_DOCUMENTATION.md` for comprehensive documentation
- See `tests/manual-test.php` for visual demonstration

## Security Impact

This fix prevents:
- Session hijacking via cookie theft
- Credential theft via keyloggers
- Phishing attacks via injected forms
- Website defacement
- Malware distribution

The fix ensures all user input is safely escaped before being rendered in HTML, preventing XSS attacks.
