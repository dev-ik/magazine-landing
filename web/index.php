<?php

use classes\Application;
use Symfony\Component\Dotenv\Dotenv;

error_reporting(E_ALL);
set_time_limit(180);
mb_internal_encoding("UTF-8");
require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/../.env');
}

$sentry = Application::getSentrySingleton();
$sentry->install();

set_exception_handler(["\classes\Error","myExceptionHandler"]);
set_error_handler(["\classes\Error","myErrorHandler"]);

$application = Application::getInstance();
$application->run();
