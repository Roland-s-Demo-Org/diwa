# XSS Mitigation Test Suite - File Index

## Complete List of Test-Related Files

### Core Test Files
| File | Purpose | Lines of Code |
|------|---------|---------------|
| `tests/ContactXSSMitigationTest.php` | Unit tests for htmlentities() with ENT_QUOTES | ~240 |
| `tests/ContactFormIntegrationTest.php` | Integration tests for contact form behavior | ~180 |
| `tests/bootstrap.php` | PHPUnit bootstrap and environment setup | ~32 |

### Configuration Files
| File | Purpose |
|------|---------|
| `phpunit.xml` | PHPUnit test runner configuration |
| `composer.json` | Updated with PHPUnit dependency and test scripts |
| `.gitignore` | Updated to exclude test artifacts (vendor/, coverage/, .phpunit.result.cache) |

### Documentation Files
| File | Purpose |
|------|---------|
| `tests/README.md` | Detailed test suite documentation with examples |
| `TESTING.md` | Quick start guide for developers |
| `TEST_SUITE_SUMMARY.md` | Complete overview of test coverage |
| `FILE_INDEX.md` | This file - index of all test files |

### Utility Scripts
| File | Purpose | Usage |
|------|---------|-------|
| `run-tests.sh` | Bash script for running tests | `./run-tests.sh [unit\|integration\|coverage\|verbose]` |
| `verify-fix.php` | Standalone PHP script to verify fix | `php verify-fix.php` |

## File Locations

```
project-root/
├── tests/
│   ├── ContactXSSMitigationTest.php      # Unit tests
│   ├── ContactFormIntegrationTest.php    # Integration tests
│   ├── bootstrap.php                      # Test bootstrap
│   └── README.md                          # Test documentation
├── phpunit.xml                            # PHPUnit config
├── composer.json                          # Updated with PHPUnit
├── .gitignore                             # Updated for test artifacts
├── run-tests.sh                           # Test runner script
├── verify-fix.php                         # Quick verification script
├── TESTING.md                             # Quick start guide
├── TEST_SUITE_SUMMARY.md                  # Test coverage summary
└── FILE_INDEX.md                          # This file
```

## Quick Commands Reference

### Setup
```bash
# Install test dependencies
composer install --dev

# Make scripts executable (Linux/Mac)
chmod +x run-tests.sh
```

### Running Tests
```bash
# All tests
composer test
./run-tests.sh
vendor/bin/phpunit

# Specific test suites
./run-tests.sh unit
./run-tests.sh integration

# With coverage
./run-tests.sh coverage
composer test-coverage

# Quick verification (no PHPUnit needed)
php verify-fix.php
```

### Individual Test Methods
```bash
# Run a specific test method
vendor/bin/phpunit --filter testBasicXSSEscaping

# Run a specific test class
vendor/bin/phpunit tests/ContactXSSMitigationTest.php
```

## Test Statistics

- **Total Test Files**: 2
- **Total Test Methods**: 24
- **Total Assertions**: 100+
- **Code Coverage Target**: app/content/contact.php (line 81)
- **Attack Vectors Tested**: 15+

## What Gets Tested

### XSS Attack Vectors
1. ✅ Script tag injection (`<script>alert(1)</script>`)
2. ✅ Event handler injection (`onclick`, `onload`, `onerror`, etc.)
3. ✅ HTML tag injection (`<img>`, `<svg>`, `<iframe>`, etc.)
4. ✅ Attribute breaking (`"`, `'`, `">`, `'/>`)
5. ✅ JavaScript protocol (`javascript:alert(1)`)
6. ✅ Data URI (`data:text/html,<script>`)
7. ✅ Unicode-based attacks
8. ✅ Mixed/complex payloads

### Functionality Tests
9. ✅ Legitimate names preserved
10. ✅ Empty string handling
11. ✅ Null value handling
12. ✅ Special characters (accents, apostrophes)
13. ✅ HTML attribute context safety
14. ✅ ENT_QUOTES flag necessity
15. ✅ isset() ternary operator behavior

## Integration with Development Workflow

### Pre-commit Hook
Add to `.git/hooks/pre-commit`:
```bash
#!/bin/bash
composer test
if [ $? -ne 0 ]; then
    echo "Tests failed. Commit aborted."
    exit 1
fi
```

### CI/CD Integration
```yaml
# GitHub Actions
- run: composer install --dev
- run: composer test

# GitLab CI
test:
  script:
    - composer install --dev
    - composer test
```

## Maintenance

### Adding New Tests
1. Add test method to appropriate test class
2. Follow naming convention: `test[Description]`
3. Include docblock explaining what is tested
4. Run tests to verify: `composer test`

### Updating Tests
1. Modify existing test methods as needed
2. Ensure all tests still pass
3. Update documentation if behavior changes

## Support

For questions or issues with the test suite:
1. Check `TESTING.md` for quick start guide
2. Review `tests/README.md` for detailed documentation
3. Run `php verify-fix.php` for quick verification
4. Check PHPUnit output for specific failures

## License

These tests are part of the DIWA project and follow the same MIT license.
