# XSS Security Tests for register.php

## Overview

This test suite validates the XSS vulnerability fix applied to `app/content/register.php`. The fix uses `htmlentities()` with the `ENT_QUOTES` flag to properly escape user input in the username and email fields.

## What is Being Tested

The tests verify that:

1. **Script tags are escaped**: XSS payloads like `<script>alert("XSS")</script>` are converted to safe HTML entities
2. **Single quotes are escaped**: Prevents attribute injection attacks using single quotes
3. **Double quotes are escaped**: Prevents attribute injection attacks using double quotes
4. **Various XSS vectors are blocked**: Tests multiple common XSS attack patterns
5. **Legitimate input is preserved**: Normal usernames and emails still work correctly
6. **Empty values are handled**: No errors occur when POST values are not set

## Installation

Install PHPUnit and dependencies:

```bash
composer install
```

## Running the Tests

Run all tests:

```bash
composer test
```

Or run PHPUnit directly:

```bash
./vendor/bin/phpunit
```

Run with verbose output:

```bash
./vendor/bin/phpunit --verbose
```

Run with code coverage (requires Xdebug):

```bash
./vendor/bin/phpunit --coverage-html coverage/
```

## Test Cases

### RegisterXSSTest

- `testUsernameXSSEscaping()` - Verifies script tags in username are escaped
- `testEmailXSSEscaping()` - Verifies script tags in email are escaped
- `testUsernameWithSingleQuotes()` - Verifies single quotes are escaped with `&#039;`
- `testEmailWithDoubleQuotes()` - Verifies double quotes are escaped with `&quot;`
- `testLegitimateInputPreserved()` - Ensures normal input still works
- `testVariousXSSVectorsInUsername()` - Tests multiple XSS attack patterns in username
- `testVariousXSSVectorsInEmail()` - Tests multiple XSS attack patterns in email
- `testENTQuotesFlag()` - Confirms both quote types are escaped (ENT_QUOTES behavior)
- `testEmptyPostValues()` - Ensures no errors with empty/unset POST data

## Expected Behavior

When user input contains malicious code like:
```
<script>alert("XSS")</script>
```

It should be rendered as:
```
&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;
```

This prevents the browser from executing the malicious JavaScript while still displaying the text safely.

## Security Context

The fix addresses **CWE-79: Improper Neutralization of Input During Web Page Generation ('Cross-site Scripting')**.

Without proper escaping, attackers could:
- Steal session cookies
- Perform actions on behalf of users
- Deface the website
- Redirect users to malicious sites

The `htmlentities()` function with `ENT_QUOTES` flag ensures all HTML special characters are converted to their entity equivalents, preventing XSS attacks.
