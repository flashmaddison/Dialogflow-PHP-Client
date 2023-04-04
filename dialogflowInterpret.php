<?php
/*
	Name:			Simple dialogflow integration REST API
	Author: 	Sean Maddison
	Version: 	2023-04-01:
	Notes:		
	Returns:	Full JSON response from dialogflow API, detectIntent method
*/

use Google\Auth\CredentialsLoader;

// Import configuration, check this file to ensure your settings are correct
require_once 'config.php';

$wwwroot = $_SERVER['DOCUMENT_ROOT'];

header('Content-Type: application/json');

// Retrieve POST variables

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$postData = json_decode(file_get_contents('php://input'), true);
	$input_message = $postData["input_message"];
	$session_id = $postData["session_id"];
} else {
	$errorData = array("error" => "No POST data provided.");
	$errorJson = json_encode($errorData);
	echo $errorJson;
}

if (isset($input_message)) {

	// Import Google API PHP client
	require_once '../../libraries/google-api-php-client/vendor/autoload.php';

	// Authenticate and fetch access token
	$access_token_array = getAccessToken();
	$access_token = $access_token_array[0];
	$expire_time = $access_token_array[1];

	/* MAKE REQUEST */
	// Set the endpoint URL, configure this for your dialogflow agent

	// you can alter this URL for other Dialogflow REST API requests, this is set to detect the intent based on raw user input
	// More info at https://cloud.google.com/dialogflow/cx/docs/reference/rest/v3-overview
	// config.php contains the settings for this URL
	$endpoint_url = "https://" . DF_API_LOCATION . "-dialogflow.googleapis.com/" . DF_API_VERSION . "/projects/" . DF_API_PROJECT_ID . "/locations/" . DF_API_LOCATION . "/agents/" . DF_API_AGENT_ID . "/sessions/" . $session_id . ":detectIntent";
	// echo $endpoint_url;

	// Set the request body as a JSON string
	$request_body = json_encode(array(
	    "queryInput" => array(
	        "text" => array(
	            "text" => $input_message,
	          ),
	        "languageCode" => DF_API_AGENT_LANG
	    )
	));

	// Set the request headers
	$headers = array(
	    "Content-Type: application/json",
	    "Authorization: Bearer " . $access_token,
	);

	// Use cURL to make the request to Dialogflow REST API
	$curl = curl_init();
	// Set the cURL options
	curl_setopt_array($curl, array(
	    CURLOPT_URL => $endpoint_url,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_POST => true,
	    CURLOPT_POSTFIELDS => $request_body,
	    CURLOPT_HTTPHEADER => $headers,
	));

	$response = curl_exec($curl);
	if (curl_error($curl)) {
			$curlErrorData = array("cURL error" => curl_error($curl));
			$curlErrorJson = json_encode($curlErrorData);
			echo $curlErrorJson;
	}
	curl_close($curl);

	// Decode the JSON response
	$json_response = json_decode($response, true);

	if (BOT_ENVIRONMENT == "DEV") {
	  // Append access token info to JSON response when debugging
	  $json_response['access_token'] = $access_token;
	  $json_response['expire_time']['expire_time'] = $expire_time;
		$json_response['expire_time']['human_readable'] = date('Y-m-d H:i:s', $expire_time);
	}

	// Output JSON at two different levels depending on config
	if(JSON_RESPONSE_MODE == "FULL") {
		echo json_encode(json_decode($response), JSON_PRETTY_PRINT);
	} elseif(JSON_RESPONSE_MODE == "ESSENTIAL") {
		echo parseForEssential($response, $access_token, $expire_time);
		
	}
} else {
	$errorData = array("error" => "no input message found");
	$errorJson = json_encode($errorData);
	echo $errorJson;
}

/*
	PARSE FOR ESSENTIAL RESPONSE CONTENT
	 - if json_mode is ESSENTIAL, this function extract only response messages, session info and intent name
	   to pass back to the client
*/
function parseForEssential($response, $access_token, $expire_time) {
  $decodedJson = json_decode($response, true);
  $json_response = [
    "responseId" => $decodedJson["responseId"],
    "queryResult" => [
        "parameters" => "",
    ]
  ];

  // add in responseMessages node if it exists
  if (isset($decodedJson['queryResult']['responseMessages'])) {
    $json_response['queryResult']['responseMessages'] = $decodedJson["queryResult"]["responseMessages"];
  }

  // add in intent node if it exists
  if (isset($decodedJson['queryResult']['intent'])) {
    $json_response['queryResult']['intent'] = [
        "displayName" => $decodedJson["queryResult"]["intent"]["displayName"]
    ];
  }

  // add in parameters node if it exists
  if (isset($decodedJson['queryResult']['parameters'])) {
    $json_response['queryResult']['parameters'] = $decodedJson['queryResult']['parameters'];
  }

	// Append access token info to JSON response when in DEV environment
	if (BOT_ENVIRONMENT == "DEV") {
	  $json_response['access_token'] = $access_token;
	  $json_response['expire_time']['expire_time'] = $expire_time;
		$json_response['expire_time']['human_readable'] = date('Y-m-d H:i:s', $expire_time);
	}

  return json_encode($json_response, JSON_PRETTY_PRINT);
}

/* 
	 AUTHENTICATE & FETCH ACCESS TOKEN 
	  - Retrieves an access token using Google Client PHP API and saves it to the session for 1 hour
	  - Refreshes once the token has expired
*/
function getAccessToken() {
  // I've included this specific client library in the folder
  // if you want to install the Google PHP Client library using composer or elsewhere, alter this
  // More information at https://github.com/googleapis/google-api-php-client/#installation
  require_once 'auth/autoload.php';

  // start session
  session_start();

  // check if access token exists and is not expired
  if(isset($_SESSION['access_token']) && isset($_SESSION['expire_time']) && time() < $_SESSION['expire_time']) {
    // use existing access token from PHP session
    $access_token = $_SESSION['access_token'];
    $expire_time = $_SESSION['expire_time'];
  } else {
    // generate new access token
    $scope = 'https://www.googleapis.com/auth/dialogflow';
    $credentials = CredentialsLoader::makeCredentials($scope, json_decode(file_get_contents(SERVICE_ACCOUNT_KEY_PATH), true));

    $accessTokenJson = $credentials->fetchAuthToken();
    $access_token = $accessTokenJson["access_token"];
    $expire_time = time() + $accessTokenJson["expires_in"];

    // store access token and expiry time in session
    $_SESSION['access_token'] = $access_token;
    $_SESSION['expire_time'] = time() + $accessTokenJson["expires_in"];

  }
  return array($access_token, $expire_time);
}
?>