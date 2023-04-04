<?php
/*
  Receives JSON-formatted conversation log and outputs HTML
*/
// Get chatLog from POST data
$chatLog = json_decode(file_get_contents("php://input"), true);

// Generate HTML output
$output = "";
foreach ($chatLog as $message) {
  $name = $message["name"];
  $content = $message["content"];
  $class = $name === "You: " ? "userSays" : "botSays";
  
  $output .= "<div class=\"$class\">";
  $output .= "<div class=\"botContentNameLeft\">$name</div>";
  $output .= "<div class=\"botContentRight\">";
  foreach ($content as $item) {
    $output .= "<p>$item</p>";
  }
  $output .= "</div></div>";
}

// Send HTML output back to JavaScript
echo $output;
?>