<?php
// Required to make Zend Developer Tools execution time accurate.
define('REQUEST_MICROTIME', microtime(true));

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
if (!file_exists('vendor/autoload.php')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install`.');
}

include 'vendor/autoload.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
