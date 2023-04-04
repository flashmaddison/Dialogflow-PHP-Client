<?php
require_once 'config.php';
// clear PHP data
session_start(); // Start the session

// Reset the session data
$_SESSION = array();

// Destroy the session
session_destroy();

// TODO, close DF session
?>