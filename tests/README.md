# XSS Mitigation Test Suite

This directory contains unit and integration tests for the XSS mitigation fix applied to the contact form in `app/content/contact.php`.

## Overview

The fix applies `htmlentities()` with the `ENT_QUOTES` flag to the `name` POST parameter before rendering it in the form input's value attribute. This prevents Cross-Site Scripting (XSS) attacks.

## Test Files

### ContactXSSMitigationTest.php
Unit tests that verify the `htmlentities()` function with `ENT_QUOTES` properly escapes various XSS attack vectors:
- Basic script tag injection
- Single and double quote escaping
- Event handler injection (onclick, onload, etc.)
- HTML tag injection (img, svg, iframe, etc.)
- JavaScript protocol injection
- Data URI injection
- Attribute breaking attempts
- Unicode-based XSS attempts
- Mixed attack vectors

### ContactFormIntegrationTest.php
Integration tests that verify the actual behavior of the contact form:
- Name field escaping in HTML output
- Legitimate names are preserved correctly
- Attribute breaking prevention
- Empty field handling
- Complex XSS payload prevention
- Context breaking prevention
- Special character handling

## Running the Tests

### Prerequisites
Install PHPUnit via Composer:
```bash
composer install --dev
```

### Run All Tests
```bash
composer test
# or
./vendor/bin/phpunit
```

### Run Specific Test File
```bash
./vendor/bin/phpunit tests/ContactXSSMitigationTest.php
./vendor/bin/phpunit tests/ContactFormIntegrationTest.php
```

### Run with Coverage Report
```bash
composer test-coverage
# Coverage report will be generated in the 'coverage' directory
```

### Run Specific Test Method
```bash
./vendor/bin/phpunit --filter testBasicXSSEscaping
```

## Test Coverage

The test suite covers:
- ✅ Basic XSS attack vectors
- ✅ Quote escaping (single and double)
- ✅ Event handler injection attempts
- ✅ HTML tag injection
- ✅ JavaScript and data URI protocols
- ✅ Attribute breaking attempts
- ✅ Unicode-based attacks
- ✅ Empty and null value handling
- ✅ Legitimate input preservation
- ✅ Integration with actual form rendering

## Expected Results

All tests should pass, confirming that:
1. The `htmlentities()` function with `ENT_QUOTES` properly escapes dangerous characters
2. XSS attack vectors are neutralized
3. Legitimate user input is preserved correctly
4. The fix works correctly in the HTML attribute context

## Security Note

These tests verify that the specific XSS vulnerability in the contact form's name field has been properly mitigated. However, note that:
- Other fields in the form (email, message, etc.) may still be vulnerable
- This fix only addresses output encoding; input validation is a separate concern
- Regular security audits and updates are recommended
