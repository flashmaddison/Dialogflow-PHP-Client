<?php
// Load .env file with secret info

// path to where you installed the dotenv and google-api-php-client packages
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/* 
	Update these values with your preferences 
*/

// Set the environment, can be DEV or PROD so far
// 	define('BOT_ENVIRONMENT', 'DEV');

// alternatively use the .env file to control it:
// 	define('BOT_ENVIRONMENT', $_ENV['BOT_ENVIRONMENT']);

// I use this logic to automatically set environment as dev when working locally, otherwise PROD :)
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'codeprojects') {
  define('BOT_ENVIRONMENT', 'DEV');
} else {
  define('BOT_ENVIRONMENT', 'PROD');
}

// Set the bot name, currently just used to generate session ID
define('BOT_NAME', 'Flashbot');

// When set to FULL, the total Dialogflow JSON is returned from the API for processing. If set to ESSENTIAL, it only includes key items
// See function parseForEssential() in dialogflowInterpret.php
define('JSON_RESPONSE_MODE', 'ESSENTIAL');

// Show extra info in the client window
define('SHOW_SESSION_ID', true);
define('SHOW_ENVIRONMENT', true);

// Set the timezone
date_default_timezone_set('Europe/London');

/* 
	Update the .env file with your Dialogflow agent information and secret key file.
*/

// Path to where you installed the PHP Client composer package
define('GOOGLE_API_PHP_CLIENT_LOCATION', $_ENV['GOOGLE_API_PHP_CLIENT_LOCATION']);

// JSON key from your google cloud service account that has the right permissions for this project
define('SERVICE_ACCOUNT_KEY_PATH', $_ENV['SERVICE_ACCOUNT_KEY_PATH']);

// Set these based on your dialogflow agent configurations
// Mainly use this to build the URL for the API requests
define('DF_API_LOCATION', $_ENV['DF_API_LOCATION']);
define('DF_API_VERSION', $_ENV['DF_API_VERSION']);
define('DF_API_PROJECT_ID', $_ENV['DF_API_PROJECT_ID']);
define('DF_API_AGENT_ID', $_ENV['DF_API_AGENT_ID']);
define('DF_API_AGENT_LANG', $_ENV['DF_API_AGENT_LANG']);

?>