<?php
/**
 * Bootstrap file for PHPUnit tests
 * Sets up the testing environment
 */

// Define test mode
define('TEST_MODE', true);

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Mock session if not started
if (session_status() === PHP_SESSION_NONE) {
    // Don't actually start session in tests, just initialize the superglobal
    $_SESSION = [];
}

// Initialize superglobals for testing
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

// Mock functions that might be used in register.php
if (!function_exists('getForbiddenMessage')) {
    function getForbiddenMessage($message) {
        return '<div class="alert alert-danger">' . $message . '</div>';
    }
}

if (!function_exists('icon')) {
    function icon($name) {
        return '<i class="fa fa-' . $name . '"></i>';
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        // Don't actually redirect in tests
        return;
    }
}

if (!function_exists('error')) {
    function error($code, $message, $exception = null) {
        throw new Exception($message . ($exception ? ': ' . $exception->getMessage() : ''));
    }
}

// Mock config array
$config = [
    'site' => [
        'invitation_code' => '1234'
    ],
    'system' => [
        'hashing_algorithm' => 'sha256'
    ]
];

// Mock model object with basic methods
class MockModel {
    public function isUserEmailInUse($email) {
        return false;
    }
    
    public function isUsernameInUse($username) {
        return false;
    }
    
    public function createUser($username, $password, $email, $country, $algorithm) {
        return true;
    }
    
    public function userSignIn($email, $password, $algorithm) {
        return [['id' => 1]];
    }
}

$model = new MockModel();

// Autoload PHPUnit if using Composer
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
