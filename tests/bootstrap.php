<?php
/**
 * PHPUnit Bootstrap File
 * Sets up the testing environment for XSS mitigation tests
 */

// Define constants if not already defined
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Set error reporting for tests
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Autoload PHPUnit if using Composer
if (file_exists(ROOT_PATH . '/app/vendor/autoload.php')) {
    require_once ROOT_PATH . '/app/vendor/autoload.php';
}

// Mock $_POST superglobal for testing if needed
if (!isset($_POST)) {
    $_POST = [];
}

// Mock $_SERVER superglobal for testing if needed
if (!isset($_SERVER)) {
    $_SERVER = [];
}

echo "PHPUnit Bootstrap loaded successfully.\n";
