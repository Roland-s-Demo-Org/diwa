<?php
/**
 * Unit tests for XSS vulnerability fix in register.php
 * 
 * Tests verify that user input (username and email) is properly escaped
 * using htmlentities with ENT_QUOTES flag to prevent XSS attacks.
 */

use PHPUnit\Framework\TestCase;

class RegisterXSSTest extends TestCase
{
    /**
     * Test that username input is properly escaped with htmlentities
     */
    public function testUsernameXSSEscaping()
    {
        // Simulate XSS attack payload in username
        $_POST['username'] = '<script>alert("XSS")</script>';
        
        // Capture the output from register.php
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify that the script tag is escaped
        $this->assertStringContainsString('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $output);
        
        // Verify that the raw script tag is NOT present
        $this->assertStringNotContainsString('<script>alert("XSS")</script>', $output);
    }
    
    /**
     * Test that email input is properly escaped with htmlentities
     */
    public function testEmailXSSEscaping()
    {
        // Simulate XSS attack payload in email
        $_POST['email'] = '"><script>alert("XSS")</script>';
        
        // Capture the output from register.php
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify that the script tag is escaped
        $this->assertStringContainsString('&quot;&gt;&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $output);
        
        // Verify that the raw script tag is NOT present
        $this->assertStringNotContainsString('<script>alert("XSS")</script>', $output);
    }
    
    /**
     * Test that single quotes in username are properly escaped
     */
    public function testUsernameWithSingleQuotes()
    {
        $_POST['username'] = "test' onload='alert(1)";
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify single quotes are escaped
        $this->assertStringContainsString('&#039;', $output);
        $this->assertStringNotContainsString("' onload='", $output);
    }
    
    /**
     * Test that double quotes in email are properly escaped
     */
    public function testEmailWithDoubleQuotes()
    {
        $_POST['email'] = 'test" onload="alert(1)';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify double quotes are escaped
        $this->assertStringContainsString('&quot;', $output);
        $this->assertStringNotContainsString('" onload="', $output);
    }
    
    /**
     * Test that legitimate input is preserved correctly
     */
    public function testLegitimateInputPreserved()
    {
        $_POST['username'] = 'john_doe123';
        $_POST['email'] = 'john@example.com';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify legitimate input is still present
        $this->assertStringContainsString('john_doe123', $output);
        $this->assertStringContainsString('john@example.com', $output);
    }
    
    /**
     * Test various XSS attack vectors in username field
     */
    public function testVariousXSSVectorsInUsername()
    {
        $xssVectors = [
            '<img src=x onerror=alert(1)>',
            '<svg onload=alert(1)>',
            'javascript:alert(1)',
            '<iframe src="javascript:alert(1)">',
            '<body onload=alert(1)>',
        ];
        
        foreach ($xssVectors as $vector) {
            $_POST['username'] = $vector;
            
            ob_start();
            include __DIR__ . '/../app/content/register.php';
            $output = ob_get_clean();
            
            // Verify that HTML entities are escaped
            $this->assertStringContainsString('&lt;', $output, "Failed to escape: $vector");
            $this->assertStringNotContainsString($vector, $output, "XSS vector not escaped: $vector");
        }
    }
    
    /**
     * Test various XSS attack vectors in email field
     */
    public function testVariousXSSVectorsInEmail()
    {
        $xssVectors = [
            '"><img src=x onerror=alert(1)>',
            '"><svg onload=alert(1)>',
            '\' onload=\'alert(1)',
            '"><iframe src="javascript:alert(1)">',
        ];
        
        foreach ($xssVectors as $vector) {
            $_POST['email'] = $vector;
            
            ob_start();
            include __DIR__ . '/../app/content/register.php';
            $output = ob_get_clean();
            
            // Verify that HTML entities are escaped
            $this->assertStringContainsString('&', $output, "Failed to escape: $vector");
            $this->assertStringNotContainsString('onerror=alert', $output, "XSS vector not escaped: $vector");
            $this->assertStringNotContainsString('onload=alert', $output, "XSS vector not escaped: $vector");
        }
    }
    
    /**
     * Test that ENT_QUOTES flag is working (both single and double quotes escaped)
     */
    public function testENTQuotesFlag()
    {
        $_POST['username'] = "test'\"quotes";
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify both single and double quotes are escaped
        $this->assertStringContainsString('&#039;', $output, "Single quotes not escaped");
        $this->assertStringContainsString('&quot;', $output, "Double quotes not escaped");
    }
    
    /**
     * Test empty POST values don't cause issues
     */
    public function testEmptyPostValues()
    {
        unset($_POST['username']);
        unset($_POST['email']);
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Should not throw errors and should render empty form
        $this->assertStringContainsString('name="username"', $output);
        $this->assertStringContainsString('name="email"', $output);
    }
    
    protected function setUp(): void
    {
        // Reset POST data before each test
        $_POST = [];
        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }
    
    protected function tearDown(): void
    {
        // Clean up after each test
        $_POST = [];
        $_SESSION = [];
    }
}
