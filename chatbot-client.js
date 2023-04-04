function sendMessage() {
  conversation_turns = 0;
  const input_field = document.getElementById("input-field");
  const input_message = input_field.value;
  const session_id = getSessionId();

  displayUserInput(input_message);

  // Make the request to our PHP API
  fetch("dialogflowInterpret.php", {
    method: "POST",
    body: JSON.stringify({ input_message, session_id }),
    headers: {
      "Content-Type": "application/json"
    }
  })
  .then(response => response.text())
  .then(text => {
    displayDebugInfo(text);
    return JSON.parse(text);
  })    
  .then(data => {
    displayBotResponse(data);
  })
  .catch(error => console.error(error));
  input_field.value = "";
  conversation_turns++;
}

/* Calls the Reset PHP session API endpoint. 
   if do_refresh is true it reloads the page.
   if not, it just destroys the PHP session instead
*/
function resetAndRefresh(do_refresh) {
  // Send an AJAX request to reset the session
  const xhr = new XMLHttpRequest();
  xhr.open('POST', '/portfolio/chatbot-demo/reset_session.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  if(do_refresh) {
    xhr.onload = function() {
      // Reload the page
      window.location.reload();
    };
  }
  xhr.send();
}

/* Captures user input, formats it and displays it in the chatbot interface */
function displayUserInput(input_message) {
  // Display user's input once entered
  const topContainer = document.getElementById("botResponse");
  const botInputField = document.createElement("div");
  botInputField.classList.add("userSays");

  const botName = document.createElement("div");
  botName.textContent = "You: ";
  botName.classList.add("botContentNameLeft");
  botInputField.appendChild(botName);

  const messageContent = document.createElement("div");
  const messageContentP = document.createElement("p");
  messageContentP.textContent = input_message;

  messageContent.classList.add("botContentRight");
  messageContent.appendChild(messageContentP);
  botInputField.appendChild(messageContent);

  console.log(JSON.stringify(botInputField));
  topContainer.appendChild(botInputField);

  addToLocalStorage(botName.textContent, input_message);
}

/* Parse the DF JSON returned for messages, and displays it in the chatbot interface */
function displayBotResponse(data) {
  // Process the raw JSON response
  //const responseId = data.responseId;
  //console.log(responseId);

  const responseMessages = data.queryResult.responseMessages;

  const topContainer = document.getElementById("botResponse");

  const botMessageContainer = document.createElement("div");
  botMessageContainer.classList.add("botSays");

  // Add the bot name or logo in
  const botName = document.createElement("div");
  botName.textContent = "Bot: ";
  botName.classList.add("botContentNameLeft");
  botMessageContainer.appendChild(botName);

  const responseContainer = document.createElement("div");
  responseContainer.classList.add("botContentRight");
  const responseContents = [];


  for (let i = 0; i < responseMessages.length; i++) {
    const responseMessage = responseMessages[i];

    if (responseMessage.hasOwnProperty("endInteraction")) {
      // If Dialogflow has ended the session, end the PHP session but don't reload the page
      resetAndRefresh(false);
      continue;
    }

    const textContent = responseMessage.text.text[0];
    responseContents.push(textContent);

    // Add the text contents
    const messageContent = document.createElement("p");
    messageContent.textContent = textContent;
    responseContainer.appendChild(messageContent);
  }

  botMessageContainer.appendChild(responseContainer);
  topContainer.appendChild(botMessageContainer);

  addToLocalStorage(botName.textContent, responseContents);

  // scroll down if it overflows
  topContainer.scrollTop = topContainer.scrollHeight;
}

/* displays debugging information on the page if PHP variable environment == "DEV", 
   this is set on the main chatbot-demo-df.php page */
function displayDebugInfo(text) {
  // DEBUG LOGGING
  if (environment == "DEV") {
    // DISPLAY RAW JSON RESPONSE FROM DIALOGFLOW
    // Create a new pre element
    const rawResponseElement = document.createElement("pre");
    rawResponseElement.textContent = "Raw response info\n\n" + text;

    // Set the ID of the pre element
    rawResponseElement.id = "raw-response-pre";

    // Check if an element with the ID "raw-response-pre" already exists on the page
    const existingRawResponseElement = document.getElementById("raw-response-pre");
    if (existingRawResponseElement) {
      // If the element already exists, replace it with the new element
      existingRawResponseElement.parentNode.replaceChild(rawResponseElement, existingRawResponseElement);
    } else {
      // If the element doesn't exist, add it to the raw-response-container
      const rawResponseContainer = document.getElementById("raw-response-container");
      rawResponseContainer.appendChild(rawResponseElement);
    }
  }
}

/* 
  Adds content to browser local storage, so if the page is refreshed the bot content so far can still be displayed 
  clearLocalStorage empties the conversation history and refreshes the page
*/
function addToLocalStorage(name, content) {
  const generatedContent = {
    name: name,
    content: Array.isArray(content) ? content : [content],
  };

  // Add the generated content to local storage
  let currentItems = JSON.parse(localStorage.getItem("chatLog")) || [];

  currentItems.push(generatedContent);

  localStorage.setItem("chatLog", JSON.stringify(currentItems, null, 2));

  // Print it to debugging TODO: if DEV environment
  if(environment == "DEV") {
    const localStorageContainer = document.querySelector("#local-storage-container pre");
    if (localStorageContainer) {
      localStorageContainer.textContent = JSON.stringify(currentItems, null, 2);
    } else {
      const localStorageContainerPre = document.createElement("pre");
      localStorageContainerPre.textContent = JSON.stringify(currentItems, null, 2);
      document.getElementById("local-storage-container").appendChild(localStorageContainerPre);
    }
  }
}

function clearLocalStorage() {
  localStorage.clear();
  console.log("Local storage cleared.");

  // Remove content from page
  const userSaysDivs = document.querySelectorAll(".userSays");
  userSaysDivs.forEach((div) => {
    if (div) {
      div.remove();
    }
  });
  const botSaysDivs = document.querySelectorAll(".botSays");
  botSaysDivs.forEach((div) => {
    if (div) {
      div.remove();
    }
  });
  const logSaysDivs = document.querySelectorAll(".logSays");
  logSaysDivs.forEach((div) => {
    if (div) {
      div.remove();
    }
  });
  // clear debugging info if it's there
  const localStorageContainerPre = document.querySelector("#local-storage-container pre");
  if (localStorageContainerPre) {
    localStorageContainerPre.remove();
  }

  setLogMessage("Conversation history cleared");
}


/*
  Generates a log message in the chat view
 */
function setLogMessage(log_message) {
  // Display a log item
  const topContainer = document.getElementById("botResponse");
  const botInputField = document.createElement("div");
  botInputField.classList.add("logSays");

  const botName = document.createElement("div");
  botName.textContent = "Log: ";
  botName.classList.add("botContentNameLeft");
  botInputField.appendChild(botName);

  const messageContent = document.createElement("div");
  messageContent.textContent = log_message;
  messageContent.classList.add("botContentRight");
  botInputField.appendChild(messageContent);

  console.log(JSON.stringify(botInputField));
  topContainer.appendChild(botInputField);

  // addToLocalStorage(botName.textContent, log_message);
}

/*
  Prints out the conversation history if one exists
  For use when the page is reloaded during a conversation in progress
*/
function printChatLog() {
  const chatLog = JSON.parse(localStorage.getItem("chatLog"));
  if (chatLog && chatLog.length > 0) {
    // Send chatLog to PHP page using AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "processChatlog.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onload = function() {
      if (xhr.status === 200) {
        // Success! Replace HTML content with response
        document.getElementById("botHistory").innerHTML = xhr.responseText;

        // scroll to the bottom once loaded
        const topContainer = document.getElementById("botResponse");
        topContainer.scrollTop = topContainer.scrollHeight;

      } else {
        // Error, handle appropriately
        console.error(xhr.statusText);
      }
    };
    xhr.send(JSON.stringify(chatLog));
  }
}