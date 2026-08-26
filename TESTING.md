# XSS Mitigation Testing - Quick Start Guide

## What Was Fixed?

The contact form in `app/content/contact.php` (line 81) was vulnerable to Cross-Site Scripting (XSS) attacks through the `name` field. The fix applies `htmlentities()` with `ENT_QUOTES` flag to escape user input before rendering it in the HTML.

### Before (Vulnerable):
```php
<input type="text" name="name" value="<?php echo (isset($_POST['name']) ? $_POST['name'] : ''); ?>">
```

### After (Fixed):
```php
<input type="text" name="name" value="<?php echo htmlentities((isset($_POST['name']) ? $_POST['name'] : ''), ENT_QUOTES); ?>">
```

## Quick Test Commands

### Install Dependencies
```bash
composer install --dev
```

### Run All Tests
```bash
./run-tests.sh
# or
composer test
```

### Run Specific Test Suites
```bash
./run-tests.sh unit          # Unit tests only
./run-tests.sh integration   # Integration tests only
./run-tests.sh coverage      # With coverage report
./run-tests.sh verbose       # Verbose output
```

### Manual PHPUnit Commands
```bash
vendor/bin/phpunit                                    # All tests
vendor/bin/phpunit tests/ContactXSSMitigationTest.php # Unit tests
vendor/bin/phpunit --filter testBasicXSSEscaping      # Specific test
```

## What Do The Tests Verify?

### Unit Tests (ContactXSSMitigationTest.php)
- ✅ Script tag injection is blocked
- ✅ Single quotes are escaped (prevents attribute breaking)
- ✅ Double quotes are escaped
- ✅ Event handlers (onclick, onload, etc.) are neutralized
- ✅ HTML tags (img, svg, iframe) are escaped
- ✅ JavaScript/data URIs are safe
- ✅ Legitimate names are preserved
- ✅ Empty/null values are handled correctly

### Integration Tests (ContactFormIntegrationTest.php)
- ✅ Form renders escaped output correctly
- ✅ Attribute context is not broken
- ✅ Complex attack payloads are neutralized
- ✅ Special characters in names work correctly

## Example Attack Vectors Tested

| Attack Vector | Input | Expected Output |
|--------------|-------|-----------------|
| Script injection | `<script>alert(1)</script>` | `&lt;script&gt;alert(1)&lt;/script&gt;` |
| Attribute breaking | `" onclick="alert(1)"` | `&quot; onclick=&quot;alert(1)&quot;` |
| Event handler | `' onload='alert(1)` | `&#039; onload=&#039;alert(1)` |
| Image tag | `<img src=x onerror=alert(1)>` | `&lt;img src=x onerror=alert(1)&gt;` |

## Why ENT_QUOTES Is Important

Without `ENT_QUOTES`, single quotes are NOT escaped:
```php
htmlentities("test' onclick='alert(1)")
// Result: test' onclick='alert(1)  ❌ VULNERABLE!

htmlentities("test' onclick='alert(1)", ENT_QUOTES)
// Result: test&#039; onclick=&#039;alert(1)  ✅ SAFE!
```

## Continuous Integration

To integrate these tests into CI/CD:

### GitHub Actions Example
```yaml
- name: Install dependencies
  run: composer install --dev
  
- name: Run XSS mitigation tests
  run: composer test
```

### GitLab CI Example
```yaml
test:
  script:
    - composer install --dev
    - composer test
```

## Further Reading

- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [PHP htmlentities() Documentation](https://www.php.net/manual/en/function.htmlentities.php)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
