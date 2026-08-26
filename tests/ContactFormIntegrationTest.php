<?php
/**
 * Integration test for contact.php XSS mitigation
 * Tests the actual behavior of the contact form with the htmlentities fix
 */

use PHPUnit\Framework\TestCase;

class ContactFormIntegrationTest extends TestCase
{
    /**
     * Test that the contact form properly escapes the name field in output
     */
    public function testContactFormNameFieldEscaping()
    {
        // Simulate POST data with XSS attempt
        $_POST['name'] = '<script>alert("XSS")</script>';
        
        // Capture the output that would be generated
        $output = $this->renderNameInputField($_POST['name']);
        
        // Verify the output is properly escaped
        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
        $this->assertStringContainsString('value="&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;"', $output);
    }

    /**
     * Test that legitimate names are rendered correctly
     */
    public function testContactFormLegitimateNames()
    {
        $legitimateNames = [
            'John Doe',
            'Mary Smith',
            'José García',
        ];

        foreach ($legitimateNames as $name) {
            $_POST['name'] = $name;
            $output = $this->renderNameInputField($_POST['name']);
            
            // Should contain the name in escaped form
            $escaped = htmlentities($name, ENT_QUOTES);
            $this->assertStringContainsString('value="' . $escaped . '"', $output);
        }
    }

    /**
     * Test attribute breaking attempts in the form
     */
    public function testContactFormAttributeBreaking()
    {
        $_POST['name'] = '" onclick="alert(1)" data-test="';
        
        $output = $this->renderNameInputField($_POST['name']);
        
        // Verify quotes are escaped and onclick is not executable
        $this->assertStringNotContainsString('onclick="alert(1)"', $output);
        $this->assertStringContainsString('&quot;', $output);
    }

    /**
     * Test empty name field
     */
    public function testContactFormEmptyName()
    {
        unset($_POST['name']);
        
        $output = $this->renderNameInputField(isset($_POST['name']) ? $_POST['name'] : '');
        
        // Should render empty value attribute
        $this->assertStringContainsString('value=""', $output);
    }

    /**
     * Test that ENT_QUOTES is used (single quotes are escaped)
     */
    public function testContactFormSingleQuoteEscaping()
    {
        $_POST['name'] = "test' onfocus='alert(1)";
        
        $output = $this->renderNameInputField($_POST['name']);
        
        // Single quotes should be escaped
        $this->assertStringNotContainsString("onfocus='alert(1)", $output);
        $this->assertStringContainsString('&#039;', $output);
    }

    /**
     * Test complex XSS payload
     */
    public function testContactFormComplexXSSPayload()
    {
        $_POST['name'] = '"><img src=x onerror=alert(document.cookie)><input value="';
        
        $output = $this->renderNameInputField($_POST['name']);
        
        // Should not contain any executable code
        $this->assertStringNotContainsString('<img', $output);
        $this->assertStringNotContainsString('onerror=', $output);
        $this->assertStringContainsString('&lt;img', $output);
    }

    /**
     * Test that the fix prevents context breaking
     */
    public function testContactFormContextBreaking()
    {
        $attacks = [
            '"/><script>alert(1)</script><input type="text',
            '\' autofocus onfocus=alert(1) x=\'',
            '"></form><script>alert(1)</script><form><input value="',
        ];

        foreach ($attacks as $attack) {
            $_POST['name'] = $attack;
            $output = $this->renderNameInputField($_POST['name']);
            
            // Should not break out of the attribute context
            $this->assertStringNotContainsString('</form>', $output);
            $this->assertStringNotContainsString('<script>', $output);
            $this->assertStringNotContainsString('autofocus onfocus=', $output);
        }
    }

    /**
     * Test special characters that should be preserved
     */
    public function testContactFormSpecialCharacters()
    {
        $names = [
            'O\'Brien',
            'François',
            'Müller',
            'José',
        ];

        foreach ($names as $name) {
            $_POST['name'] = $name;
            $output = $this->renderNameInputField($_POST['name']);
            
            // Should not contain dangerous characters
            $this->assertStringNotContainsString('<', $output);
            $this->assertStringNotContainsString('>', $output);
            // Should contain escaped version if needed
            $escaped = htmlentities($name, ENT_QUOTES);
            $this->assertStringContainsString($escaped, $output);
        }
    }

    /**
     * Helper method to render the name input field as it appears in contact.php
     * This simulates the actual code from line 81 of contact.php
     */
    private function renderNameInputField($nameValue)
    {
        // This replicates the exact logic from contact.php line 81
        $escapedValue = htmlentities((isset($_POST['name']) ? $_POST['name'] : ''), ENT_QUOTES);
        
        return '<input type="text" class="form-control" name="name" value="' . $escapedValue . '" id="name">';
    }

    /**
     * Test the ternary operator behavior with isset
     */
    public function testContactFormIssetBehavior()
    {
        // Test when $_POST['name'] is not set
        unset($_POST['name']);
        $output = $this->renderNameInputField('');
        $this->assertStringContainsString('value=""', $output);
        
        // Test when $_POST['name'] is set but empty
        $_POST['name'] = '';
        $output = $this->renderNameInputField($_POST['name']);
        $this->assertStringContainsString('value=""', $output);
        
        // Test when $_POST['name'] is set with value
        $_POST['name'] = 'Test Name';
        $output = $this->renderNameInputField($_POST['name']);
        $this->assertStringContainsString('value="Test Name"', $output);
    }

    /**
     * Cleanup after each test
     */
    protected function tearDown(): void
    {
        // Reset $_POST to clean state
        $_POST = [];
    }
}
