# Test Suite Verification Checklist

Use this checklist to verify that the test suite is properly set up and working.

## ✅ File Creation Checklist

- [x] `tests/RegisterXSSTest.php` - Unit tests
- [x] `tests/RegisterHTMLOutputTest.php` - Integration tests
- [x] `tests/bootstrap.php` - Test environment setup
- [x] `tests/manual-test.php` - Manual browser test
- [x] `tests/README.md` - Test suite overview
- [x] `tests/TEST_DOCUMENTATION.md` - Comprehensive docs
- [x] `tests/SUMMARY.md` - Test suite summary
- [x] `phpunit.xml` - PHPUnit configuration
- [x] `composer.json` - Updated with PHPUnit
- [x] `run-tests.sh` - Test runner script
- [x] `TESTING.md` - Quick start guide

## ✅ Setup Verification

### Step 1: Check Files Exist
```bash
ls -la tests/
ls -la phpunit.xml
ls -la run-tests.sh
ls -la TESTING.md
```

Expected: All files should be present

### Step 2: Install Dependencies
```bash
composer install
```

Expected: PHPUnit should be installed in vendor/bin/

### Step 3: Verify PHPUnit Installation
```bash
./vendor/bin/phpunit --version
```

Expected: Should show PHPUnit 9.5.x

### Step 4: Check Test Files Syntax
```bash
php -l tests/RegisterXSSTest.php
php -l tests/RegisterHTMLOutputTest.php
php -l tests/bootstrap.php
```

Expected: No syntax errors

## ✅ Test Execution Checklist

### Run All Tests
```bash
composer test
```

Expected Results:
- [ ] All 18+ tests pass
- [ ] No errors or warnings
- [ ] Output shows "OK (18 tests, 45+ assertions)"

### Run Individual Test Files
```bash
./vendor/bin/phpunit tests/RegisterXSSTest.php
./vendor/bin/phpunit tests/RegisterHTMLOutputTest.php
```

Expected Results:
- [ ] RegisterXSSTest: 9 tests pass
- [ ] RegisterHTMLOutputTest: 10 tests pass

### Run Specific Tests
```bash
./vendor/bin/phpunit --filter testUsernameXSSEscaping
./vendor/bin/phpunit --filter testEmailXSSEscaping
```

Expected Results:
- [ ] Each specific test passes individually

### Run with Verbose Output
```bash
./vendor/bin/phpunit --verbose
```

Expected Results:
- [ ] Detailed test output shown
- [ ] Each test method listed with checkmark

### Run Test Script
```bash
bash run-tests.sh
```

Expected Results:
- [ ] Script runs successfully
- [ ] Shows "✓ All tests passed!"
- [ ] Exit code is 0

## ✅ Manual Testing Checklist

### Start Test Server
```bash
php -S localhost:8000 -t tests/
```

### Open Manual Test Page
Open browser to: http://localhost:8000/manual-test.php

Expected Results:
- [ ] Page loads without errors
- [ ] No JavaScript alerts appear
- [ ] All test cases are displayed
- [ ] Input fields show escaped content
- [ ] Form structure is intact

### Inspect HTML Output
Use browser DevTools to inspect input fields

Expected Results:
- [ ] Username input value contains escaped entities
- [ ] Email input value contains escaped entities
- [ ] No unescaped script tags in HTML
- [ ] Quotes are properly escaped

## ✅ Fix Verification Checklist

### Check register.php Line 101
```bash
grep -n "htmlentities" app/content/register.php | head -1
```

Expected:
- [ ] Line 101 contains: `htmlentities((isset($_POST['username']) ? $_POST['username'] : ''), ENT_QUOTES)`

### Check register.php Line 105
```bash
grep -n "htmlentities" app/content/register.php | tail -1
```

Expected:
- [ ] Line 105 contains: `htmlentities((isset($_POST['email']) ? $_POST['email'] : ''), ENT_QUOTES)`

### Verify ENT_QUOTES Flag
```bash
grep "ENT_QUOTES" app/content/register.php
```

Expected:
- [ ] Two occurrences found (username and email)
- [ ] Both use ENT_QUOTES flag

## ✅ Documentation Checklist

### Verify Documentation Files
```bash
cat tests/README.md | head -20
cat tests/TEST_DOCUMENTATION.md | head -20
cat TESTING.md | head -20
```

Expected:
- [ ] All documentation files are readable
- [ ] Content is relevant and accurate
- [ ] Instructions are clear

### Check Documentation Completeness
- [ ] Installation instructions provided
- [ ] Running tests instructions provided
- [ ] Test case descriptions provided
- [ ] Security context explained
- [ ] Troubleshooting tips included

## ✅ Integration Checklist

### CI/CD Ready
- [ ] phpunit.xml is properly configured
- [ ] composer.json has test script
- [ ] Tests can run without manual intervention
- [ ] Exit codes are properly set

### Code Coverage (Optional)
```bash
./vendor/bin/phpunit --coverage-text
```

Expected:
- [ ] Coverage report generated (requires Xdebug)
- [ ] register.php is included in coverage

## ✅ Security Validation Checklist

### XSS Vectors Tested
- [ ] Script tag injection
- [ ] Image tag injection
- [ ] SVG tag injection
- [ ] Iframe injection
- [ ] Single quote attribute injection
- [ ] Double quote attribute injection
- [ ] Attribute breakout
- [ ] OWASP XSS payloads
- [ ] Polyglot XSS payloads

### Escaping Validation
- [ ] `<` converted to `&lt;`
- [ ] `>` converted to `&gt;`
- [ ] `"` converted to `&quot;`
- [ ] `'` converted to `&#039;`
- [ ] `&` converted to `&amp;`

## ✅ Final Verification

### All Tests Pass
```bash
composer test && echo "SUCCESS" || echo "FAILED"
```

Expected:
- [ ] Output shows "SUCCESS"

### No Regressions
- [ ] Original functionality still works
- [ ] Legitimate input is preserved
- [ ] Form submission still works
- [ ] Error handling still works

### Documentation Complete
- [ ] All documentation files created
- [ ] Instructions are clear and accurate
- [ ] Examples are provided
- [ ] Troubleshooting section included

## Summary

If all items above are checked, the test suite is properly installed and working correctly!

## Troubleshooting

If any checks fail:

1. **Tests fail**: Check that register.php has the fix applied correctly
2. **PHPUnit not found**: Run `composer install`
3. **Syntax errors**: Check PHP version (requires PHP 7.2+)
4. **Manual test doesn't load**: Check PHP server is running
5. **Coverage fails**: Install Xdebug extension

## Support

For more information, see:
- `TESTING.md` - Quick start guide
- `tests/README.md` - Test suite overview
- `tests/TEST_DOCUMENTATION.md` - Comprehensive documentation
