<?php
/*
	Update this file with the config details for your dialogflow agent and other settings
*/

date_default_timezone_set('Europe/London');

// Set the environment, can be DEV or PROD so far
//define('BOT_ENVIRONMENT', 'PROD');
// for my purposes, I always assume localhost is the DEV environnment - remove this if you want to control this manually
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
  define('BOT_ENVIRONMENT', 'DEV');
} else {
	define('BOT_ENVIRONMENT', 'PROD');
}

// When set to FULL, the total Dialogflow JSON is returned from the API. If set to ESSENTIAL, it only includes key items
// See function parseForEssential() in dialogflowInterpret.php
define('JSON_RESPONSE_MODE', 'ESSENTIAL');

// Set these based on your dialogflow agent configurations
// Mainly use this to build the URL for the API requests
define('DF_API_LOCATION', '<<<YOUR_DF_LOCATION');													// e.g. "europe-west2"
define('DF_API_VERSION', 'v3beta1');																// alternatively "v3"
define('DF_API_PROJECT_ID', '<<<YOUR_DF_PROJECT_ID>>>');							// e.g. "my-project-123456"
define('DF_API_AGENT_ID', '<<<YOUR_DF_AGENT_ID>>>');	// e.g. "aaaaaaaa-1b2b-1b2b-abcd-1b2b3d4d5e6e"
define('DF_API_AGENT_LANG', 'en-GB');																// This should match the language of your agent

// JSON key from your google cloud service account that has the right permissions for this project
define('SERVICE_ACCOUNT_KEY_PATH', 'PATH/TO/KEYFILE.JSON');

// Currently just used to generate session ID
define('BOT_NAME', 'Flashbot');

// Show extra info in the client window
define('SHOW_SESSION_ID', true);
define('SHOW_ENVIRONMENT', true);
?>