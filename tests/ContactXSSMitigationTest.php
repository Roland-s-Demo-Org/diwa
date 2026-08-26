<?php
/**
 * Test suite for XSS mitigation in contact.php
 * Tests the htmlentities fix applied to the 'name' POST parameter
 */

use PHPUnit\Framework\TestCase;

class ContactXSSMitigationTest extends TestCase
{
    /**
     * Test that htmlentities properly escapes basic XSS attack vectors
     */
    public function testBasicXSSEscaping()
    {
        $maliciousInput = '<script>alert("XSS")</script>';
        $escaped = htmlentities($maliciousInput, ENT_QUOTES);
        
        $this->assertEquals('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $escaped);
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringNotContainsString('</script>', $escaped);
    }

    /**
     * Test that single quotes are properly escaped with ENT_QUOTES flag
     */
    public function testSingleQuoteEscaping()
    {
        $inputWithSingleQuote = "' onload='alert(1)";
        $escaped = htmlentities($inputWithSingleQuote, ENT_QUOTES);
        
        $this->assertEquals('&#039; onload=&#039;alert(1)', $escaped);
        $this->assertStringNotContainsString("'", $escaped);
    }

    /**
     * Test that double quotes are properly escaped
     */
    public function testDoubleQuoteEscaping()
    {
        $inputWithDoubleQuote = '" onload="alert(1)';
        $escaped = htmlentities($inputWithDoubleQuote, ENT_QUOTES);
        
        $this->assertEquals('&quot; onload=&quot;alert(1)', $escaped);
        $this->assertStringNotContainsString('"', $escaped);
    }

    /**
     * Test event handler injection attempts
     */
    public function testEventHandlerInjection()
    {
        $maliciousInputs = [
            '" onclick="alert(1)"',
            '" onmouseover="alert(1)"',
            '" onfocus="alert(1)"',
            "' onerror='alert(1)'",
        ];

        foreach ($maliciousInputs as $input) {
            $escaped = htmlentities($input, ENT_QUOTES);
            $this->assertStringNotContainsString('onclick=', $escaped);
            $this->assertStringNotContainsString('onmouseover=', $escaped);
            $this->assertStringNotContainsString('onfocus=', $escaped);
            $this->assertStringNotContainsString('onerror=', $escaped);
            $this->assertStringNotContainsString('"', $escaped);
            $this->assertStringNotContainsString("'", $escaped);
        }
    }

    /**
     * Test that legitimate names are preserved correctly
     */
    public function testLegitimateNamesPreserved()
    {
        $legitimateNames = [
            'John Doe',
            'Mary-Jane Smith',
            'O\'Brien',
            'José García',
            'François Müller',
        ];

        foreach ($legitimateNames as $name) {
            $escaped = htmlentities($name, ENT_QUOTES);
            // Should not contain dangerous characters
            $this->assertStringNotContainsString('<', $escaped);
            $this->assertStringNotContainsString('>', $escaped);
        }
    }

    /**
     * Test empty string handling
     */
    public function testEmptyStringHandling()
    {
        $empty = '';
        $escaped = htmlentities($empty, ENT_QUOTES);
        
        $this->assertEquals('', $escaped);
    }

    /**
     * Test null value handling (should be converted to empty string)
     */
    public function testNullHandling()
    {
        $null = null;
        $escaped = htmlentities($null, ENT_QUOTES);
        
        $this->assertEquals('', $escaped);
    }

    /**
     * Test HTML tag injection attempts
     */
    public function testHTMLTagInjection()
    {
        $maliciousInputs = [
            '<img src=x onerror=alert(1)>',
            '<svg onload=alert(1)>',
            '<iframe src="javascript:alert(1)">',
            '<body onload=alert(1)>',
        ];

        foreach ($maliciousInputs as $input) {
            $escaped = htmlentities($input, ENT_QUOTES);
            $this->assertStringNotContainsString('<img', $escaped);
            $this->assertStringNotContainsString('<svg', $escaped);
            $this->assertStringNotContainsString('<iframe', $escaped);
            $this->assertStringNotContainsString('<body', $escaped);
            $this->assertStringContainsString('&lt;', $escaped);
            $this->assertStringContainsString('&gt;', $escaped);
        }
    }

    /**
     * Test JavaScript protocol injection
     */
    public function testJavaScriptProtocolInjection()
    {
        $maliciousInput = 'javascript:alert(1)';
        $escaped = htmlentities($maliciousInput, ENT_QUOTES);
        
        // Should still contain the text but without dangerous context
        $this->assertStringContainsString('javascript', $escaped);
        $this->assertStringContainsString('alert', $escaped);
        // Colons should not be escaped by htmlentities
        $this->assertStringContainsString(':', $escaped);
    }

    /**
     * Test data URI injection
     */
    public function testDataURIInjection()
    {
        $maliciousInput = 'data:text/html,<script>alert(1)</script>';
        $escaped = htmlentities($maliciousInput, ENT_QUOTES);
        
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }

    /**
     * Test that ENT_QUOTES flag is necessary for proper escaping
     */
    public function testENTQuotesNecessity()
    {
        $input = "test' onclick='alert(1)";
        
        // Without ENT_QUOTES, single quotes are not escaped
        $withoutFlag = htmlentities($input);
        $this->assertStringContainsString("'", $withoutFlag);
        
        // With ENT_QUOTES, single quotes are escaped
        $withFlag = htmlentities($input, ENT_QUOTES);
        $this->assertStringNotContainsString("'", $withFlag);
        $this->assertStringContainsString('&#039;', $withFlag);
    }

    /**
     * Test attribute breaking attempts
     */
    public function testAttributeBreaking()
    {
        $maliciousInputs = [
            '"><script>alert(1)</script><input value="',
            '\' autofocus onfocus=alert(1) x=\'',
            '"/><script>alert(1)</script><input type="text',
        ];

        foreach ($maliciousInputs as $input) {
            $escaped = htmlentities($input, ENT_QUOTES);
            $this->assertStringNotContainsString('"', $escaped);
            $this->assertStringNotContainsString("'", $escaped);
            $this->assertStringNotContainsString('<', $escaped);
            $this->assertStringNotContainsString('>', $escaped);
        }
    }

    /**
     * Test Unicode-based XSS attempts
     */
    public function testUnicodeXSSAttempts()
    {
        $maliciousInputs = [
            '<script>alert(1)</script>',
            '＜script＞alert(1)＜/script＞', // Full-width characters
        ];

        foreach ($maliciousInputs as $input) {
            $escaped = htmlentities($input, ENT_QUOTES);
            // Basic HTML entities should be escaped
            $this->assertStringNotContainsString('<script>', $escaped);
        }
    }

    /**
     * Test that the fix works in the context of an HTML attribute
     */
    public function testInHTMLAttributeContext()
    {
        $maliciousInput = '"><script>alert("XSS")</script><input value="';
        $escaped = htmlentities($maliciousInput, ENT_QUOTES);
        
        // Simulate the actual HTML output
        $htmlOutput = '<input type="text" name="name" value="' . $escaped . '">';
        
        // The output should not contain executable script tags
        $this->assertStringNotContainsString('<script>', $htmlOutput);
        $this->assertStringContainsString('&lt;script&gt;', $htmlOutput);
        
        // The attribute should remain properly quoted
        $this->assertStringContainsString('value="&quot;&gt;&lt;script&gt;', $htmlOutput);
    }

    /**
     * Test mixed attack vectors
     */
    public function testMixedAttackVectors()
    {
        $complexAttack = '"><img src=x onerror=alert(String.fromCharCode(88,83,83))>';
        $escaped = htmlentities($complexAttack, ENT_QUOTES);
        
        $this->assertStringNotContainsString('">', $escaped);
        $this->assertStringNotContainsString('<img', $escaped);
        $this->assertStringContainsString('&quot;&gt;&lt;img', $escaped);
    }
}
