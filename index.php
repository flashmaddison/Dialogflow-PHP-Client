<html>
  <head>
    <title>Flashbot - A Dialogflow PHP client</title>
    <link rel="stylesheet" type="text/css" href="bot-style.css" />
  </head>
  <body style="width: 70%; min-width: 800px; margin: auto;">

  <?php
    require_once 'config.php';

    // Create a unique session ID for the API calls and persistence if one doesn't exist
    session_start();
    if (!isset($_SESSION['session_id'])) {
        $_SESSION['session_id'] = uniqid(BOT_NAME, true);
    }
    $session_id = $_SESSION['session_id'];
  ?>

  	<main>
      <script>
        // tells JavaScript what the session ID is
        function getSessionId() {
            return "<?php echo $session_id; ?>";
        }

        // tell JavaScript what the environment is
        const environment = "<?php echo BOT_ENVIRONMENT; ?>";
      </script>

      <script src="chatbot-client.js"></script>

      <section id="chatbot-client">
        <h3>Flashbot web client</h3>
        <div class="botContainer">
          <!-- This is where the bot input and output are displayed -->
          <div class="botTop" id="botResponse">
            <?php if(SHOW_SESSION_ID) { echo "<p class=\"botSessionInfo\">Session ID: $session_id"; } ?></p>
            <?php if(SHOW_ENVIRONMENT) { echo "<p class=\"botSessionInfo\">Environment: " . BOT_ENVIRONMENT; } ?></p>

            <!-- Print conversation history if it exists -->
            <script> printChatLog() </script>
            <div id="botHistory">
          </div>
            
          </div>
          
          <!--The user inputs their query here -->
          <div class="botBottom">
            <input type="text" class="botInputField" id="input-field" placeholder="Tell me something..." onkeydown="if (event.keyCode === 13) sendMessage();">
            <button class="botSubmitButton" onclick="sendMessage()">Submit</button>
          </div>
        </div>

      </section>
      <section id="misc-functions">
        <h2>Useful info & functions</h2>
        <ul>
          <li><a href="#" onclick="resetAndRefresh(true)">Reset/terminate session</a></li>
          <li><a href="#" onclick="clearLocalStorage()">Clear conversation history</a></li>
        </ul>
      </section>
      <?php 
        // Show raw JSON response and other debugging info if not production environment
        if (BOT_ENVIRONMENT == "DEV") { 
      ?>
      <h2>Debugging</h2> 
      <!-- this is populated automatically by Javascript once a request is made -->
      <section id="raw-response-container">
        <h3>Raw JSON Response</h3>
      </section>
      <section id="local-storage-container">
        <h3>Local storage contents</h3>
      </section>

      <?php } ?>
  	</main>
  </body>
</html>