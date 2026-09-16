<?php
/**
 * Manual test file for XSS vulnerability fix
 * 
 * This file demonstrates the before/after behavior of the XSS fix.
 * Run this file directly in a browser to see the difference.
 */

// Test payloads
$testPayloads = [
    'username' => [
        'Normal input' => 'john_doe',
        'Script tag' => '<script>alert("XSS")</script>',
        'Image tag' => '<img src=x onerror=alert(1)>',
        'SVG tag' => '<svg onload=alert(1)>',
        'Single quote injection' => "test' onfocus='alert(1)",
        'Double quote injection' => 'test" onfocus="alert(1)',
        'Attribute breakout' => '"><script>alert(1)</script><input value="',
    ],
    'email' => [
        'Normal input' => 'test@example.com',
        'Script tag' => 'test@example.com"><script>alert("XSS")</script>',
        'Image tag' => '"><img src=x onerror=alert(1)>',
        'Event handler' => 'test" onfocus="alert(1)',
    ]
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>XSS Fix Manual Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .test-case {
            margin: 15px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #4CAF50;
        }
        .test-case.vulnerable {
            border-left-color: #f44336;
        }
        .test-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .test-input {
            margin: 10px 0;
        }
        .test-input input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-family: monospace;
        }
        .code {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 12px;
            overflow-x: auto;
            margin: 5px 0;
        }
        .safe {
            color: #4CAF50;
            font-weight: bold;
        }
        .vulnerable {
            color: #f44336;
            font-weight: bold;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>XSS Vulnerability Fix - Manual Test</h1>
    
    <div class="info">
        <strong>Purpose:</strong> This page demonstrates the XSS fix applied to register.php.
        The fix uses <code>htmlentities()</code> with <code>ENT_QUOTES</code> flag to escape user input.
    </div>

    <div class="test-section">
        <h2>✓ FIXED (With htmlentities + ENT_QUOTES)</h2>
        <p>This is how the input is rendered AFTER the fix:</p>
        
        <?php foreach ($testPayloads['username'] as $label => $payload): ?>
            <div class="test-case">
                <div class="test-label"><?php echo $label; ?>:</div>
                <div class="code">Input: <?php echo htmlspecialchars($payload); ?></div>
                <div class="test-input">
                    <input type="text" 
                           name="username" 
                           value="<?php echo htmlentities($payload, ENT_QUOTES); ?>" 
                           readonly>
                </div>
                <div class="code">
                    Escaped: <?php echo htmlspecialchars(htmlentities($payload, ENT_QUOTES)); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="test-section">
        <h2 class="vulnerable">✗ VULNERABLE (Without escaping)</h2>
        <p><strong>WARNING:</strong> The examples below show what would happen WITHOUT the fix. 
        They are intentionally disabled to prevent actual XSS attacks.</p>
        
        <?php foreach ($testPayloads['username'] as $label => $payload): ?>
            <div class="test-case vulnerable">
                <div class="test-label"><?php echo $label; ?>:</div>
                <div class="code">Input: <?php echo htmlspecialchars($payload); ?></div>
                <div class="test-input">
                    <!-- Intentionally disabled to prevent XSS -->
                    <input type="text" 
                           name="username_vulnerable" 
                           value="<?php echo htmlspecialchars($payload); ?>" 
                           readonly
                           disabled
                           title="This would be vulnerable without htmlentities">
                </div>
                <div class="code">
                    Without fix, this would render as: value="<?php echo htmlspecialchars($payload); ?>"
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="test-section">
        <h2>Email Field Tests</h2>
        
        <?php foreach ($testPayloads['email'] as $label => $payload): ?>
            <div class="test-case">
                <div class="test-label"><?php echo $label; ?>:</div>
                <div class="code">Input: <?php echo htmlspecialchars($payload); ?></div>
                <div class="test-input">
                    <input type="email" 
                           name="email" 
                           value="<?php echo htmlentities($payload, ENT_QUOTES); ?>" 
                           readonly>
                </div>
                <div class="code">
                    Escaped: <?php echo htmlspecialchars(htmlentities($payload, ENT_QUOTES)); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="info">
        <h3>Key Points:</h3>
        <ul>
            <li><strong>&lt;</strong> is converted to <strong>&amp;lt;</strong></li>
            <li><strong>&gt;</strong> is converted to <strong>&amp;gt;</strong></li>
            <li><strong>"</strong> is converted to <strong>&amp;quot;</strong> (due to ENT_QUOTES)</li>
            <li><strong>'</strong> is converted to <strong>&amp;#039;</strong> (due to ENT_QUOTES)</li>
            <li><strong>&amp;</strong> is converted to <strong>&amp;amp;</strong></li>
        </ul>
        <p>This prevents the browser from interpreting the input as HTML/JavaScript code.</p>
    </div>

    <div class="test-section">
        <h2>Verification</h2>
        <p>To verify the fix is working:</p>
        <ol>
            <li>Inspect the input fields above using browser DevTools</li>
            <li>Verify that the <code>value</code> attribute contains escaped entities</li>
            <li>Confirm that no JavaScript alerts appear on this page</li>
            <li>Check that the input fields display the text safely</li>
        </ol>
    </div>
</body>
</html>
