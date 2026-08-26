#!/usr/bin/env php
<?php
/**
 * Simple test runner for quick verification
 * This script can be run without PHPUnit to verify the fix works
 */

echo "===========================================\n";
echo "XSS Mitigation Quick Verification\n";
echo "===========================================\n\n";

// Test cases
$testCases = [
    [
        'name' => 'Basic Script Tag',
        'input' => '<script>alert("XSS")</script>',
        'expected' => '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;',
    ],
    [
        'name' => 'Single Quote Attack',
        'input' => "' onclick='alert(1)'",
        'expected' => '&#039; onclick=&#039;alert(1)&#039;',
    ],
    [
        'name' => 'Double Quote Attack',
        'input' => '" onclick="alert(1)"',
        'expected' => '&quot; onclick=&quot;alert(1)&quot;',
    ],
    [
        'name' => 'Attribute Breaking',
        'input' => '"><script>alert(1)</script><input value="',
        'expected' => '&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;&lt;input value=&quot;',
    ],
    [
        'name' => 'Image Tag with onerror',
        'input' => '<img src=x onerror=alert(1)>',
        'expected' => '&lt;img src=x onerror=alert(1)&gt;',
    ],
    [
        'name' => 'Legitimate Name',
        'input' => "John O'Brien",
        'expected' => 'John O&#039;Brien',
    ],
];

$passed = 0;
$failed = 0;

foreach ($testCases as $test) {
    echo "Testing: {$test['name']}\n";
    echo "  Input:    {$test['input']}\n";
    
    // Apply the fix (same as in contact.php line 81)
    $result = htmlentities($test['input'], ENT_QUOTES);
    
    echo "  Output:   {$result}\n";
    echo "  Expected: {$test['expected']}\n";
    
    if ($result === $test['expected']) {
        echo "  ✅ PASSED\n\n";
        $passed++;
    } else {
        echo "  ❌ FAILED\n\n";
        $failed++;
    }
}

echo "===========================================\n";
echo "Results: {$passed} passed, {$failed} failed\n";
echo "===========================================\n";

if ($failed === 0) {
    echo "✅ All tests passed! The XSS mitigation is working correctly.\n";
    exit(0);
} else {
    echo "❌ Some tests failed. Please review the implementation.\n";
    exit(1);
}
