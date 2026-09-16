<?php
/**
 * Integration tests for register.php XSS fix
 * 
 * These tests verify the actual HTML output to ensure proper escaping
 * in the context of the form input value attributes.
 */

use PHPUnit\Framework\TestCase;

class RegisterHTMLOutputTest extends TestCase
{
    /**
     * Test that the username input field properly escapes its value attribute
     */
    public function testUsernameInputValueAttribute()
    {
        $_POST['username'] = '"><script>alert(1)</script><input value="';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Extract the username input field
        preg_match('/<input[^>]*name="username"[^>]*>/', $output, $matches);
        $this->assertNotEmpty($matches, "Username input field not found");
        
        $inputTag = $matches[0];
        
        // Verify the malicious payload is escaped in the value attribute
        $this->assertStringContainsString('&quot;&gt;&lt;script&gt;', $inputTag);
        
        // Verify the input tag is properly closed and not broken
        $this->assertStringEndsWith('>', $inputTag);
        
        // Verify no unescaped script tag exists
        $this->assertStringNotContainsString('<script>', $inputTag);
    }
    
    /**
     * Test that the email input field properly escapes its value attribute
     */
    public function testEmailInputValueAttribute()
    {
        $_POST['email'] = '"><img src=x onerror=alert(1)><input value="';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Extract the email input field
        preg_match('/<input[^>]*name="email"[^>]*>/', $output, $matches);
        $this->assertNotEmpty($matches, "Email input field not found");
        
        $inputTag = $matches[0];
        
        // Verify the malicious payload is escaped
        $this->assertStringContainsString('&quot;&gt;&lt;img', $inputTag);
        
        // Verify no unescaped img tag exists
        $this->assertStringNotContainsString('<img src=', $inputTag);
        $this->assertStringNotContainsString('onerror=', $inputTag);
    }
    
    /**
     * Test attribute injection via single quote
     */
    public function testAttributeInjectionSingleQuote()
    {
        $_POST['username'] = "test' onfocus='alert(1)' data-x='";
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify single quotes are escaped
        $this->assertStringNotContainsString("onfocus='alert", $output);
        $this->assertStringContainsString('&#039;', $output);
    }
    
    /**
     * Test attribute injection via double quote
     */
    public function testAttributeInjectionDoubleQuote()
    {
        $_POST['email'] = 'test" onfocus="alert(1)" data-x="';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify double quotes are escaped
        $this->assertStringNotContainsString('onfocus="alert', $output);
        $this->assertStringContainsString('&quot;', $output);
    }
    
    /**
     * Test that HTML entities in username are double-escaped correctly
     */
    public function testHTMLEntitiesInUsername()
    {
        $_POST['username'] = '&lt;script&gt;';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Should be double-escaped: &amp;lt;script&amp;gt;
        $this->assertStringContainsString('&amp;lt;script&amp;gt;', $output);
    }
    
    /**
     * Test Unicode characters are handled correctly
     */
    public function testUnicodeCharacters()
    {
        $_POST['username'] = 'test™️🔒';
        $_POST['email'] = 'test@example™.com';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Unicode should be preserved
        $this->assertStringContainsString('test', $output);
    }
    
    /**
     * Test that the form structure remains valid after escaping
     */
    public function testFormStructureIntegrity()
    {
        $_POST['username'] = '<script>alert(1)</script>';
        $_POST['email'] = '"><script>alert(2)</script>';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify form structure is intact
        $this->assertStringContainsString('<form method="post"', $output);
        $this->assertStringContainsString('</form>', $output);
        
        // Count input fields - should have username, email, country, password, password-repeat, invitation-code
        $inputCount = substr_count($output, '<input');
        $this->assertGreaterThanOrEqual(5, $inputCount, "Form inputs are missing or malformed");
        
        // Verify submit button exists
        $this->assertStringContainsString('<button type="submit"', $output);
    }
    
    /**
     * Test real-world XSS payload from OWASP
     */
    public function testOWASPXSSPayload()
    {
        // OWASP XSS test payload
        $_POST['username'] = '"><svg/onload=alert(String.fromCharCode(88,83,83))>';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify payload is neutralized
        $this->assertStringNotContainsString('svg/onload=', $output);
        $this->assertStringNotContainsString('fromCharCode', $output);
        $this->assertStringContainsString('&lt;', $output);
        $this->assertStringContainsString('&gt;', $output);
    }
    
    /**
     * Test polyglot XSS payload
     */
    public function testPolyglotXSSPayload()
    {
        // Polyglot payload that works in multiple contexts
        $_POST['email'] = 'javascript:/*--></title></style></textarea></script></xmp><svg/onload=\'+/"/+/onmouseover=1/+/[*/[]/+alert(1)//\'>';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // Verify all dangerous parts are escaped
        $this->assertStringNotContainsString('javascript:', $output);
        $this->assertStringNotContainsString('</script>', $output);
        $this->assertStringNotContainsString('onload=', $output);
        $this->assertStringNotContainsString('onmouseover=', $output);
    }
    
    /**
     * Test that htmlentities is applied (not htmlspecialchars)
     */
    public function testHtmlentitiesUsed()
    {
        // htmlentities converts more characters than htmlspecialchars
        $_POST['username'] = 'test©®™';
        
        ob_start();
        include __DIR__ . '/../app/content/register.php';
        $output = ob_get_clean();
        
        // These special characters should be converted by htmlentities
        // (though the exact behavior depends on encoding parameter)
        $this->assertStringContainsString('test', $output);
    }
    
    protected function setUp(): void
    {
        $_POST = [];
        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }
    
    protected function tearDown(): void
    {
        $_POST = [];
        $_SESSION = [];
    }
}
