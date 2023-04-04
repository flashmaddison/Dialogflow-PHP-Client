# Dialogflow-PHP-Client
A simple PHP and JavaScript web client for interacting with Google Dialogflow CX through an interface directly on a web page.

# Introduction
I built this for my personal portfolio website originally, both to build out some demos and brush up on my JS and PHP. I struggled a bit at 
first to get authentication and API calls working (maybe I was reading the wrong documentation haha)! Anyway, once I got it working I decided 
to tidy up the code as best I can, and post it on github in case anyone else finds a use for it.

[Donate if you want!](https://www.paypal.com/donate/?hosted_button_id=PLSQDDZ3MCM6Q)

# Features
 - Authentication using a service account to retrieve an access token
 - PHP for API calls, Javascript & HTML for user interface
 - On-page interface
 - conversation peristence, if the page is refreshed the chat log remains
 - Very simple client so should be easy to understand and customise for your needs
 - Debugging mode that shows raw API responses if needed
 - Live demo at https://seanmaddison.uk/portfolio/chatbot-demo

# Screenshot
![Screenshot of the bot deployed on my website.](https://seanmaddison.uk/portfolio/images/bot-screenshot-1.PNG)

# Installation
1. Enable *curl* module in php.ini
2. Enable *openssl* module in php.ini
3. Optional: install [Google APIs Client Library for PHP](https://github.com/googleapis/google-api-php-client/#installation)
    - I've included the required part of this library in the auth folder and call that, but you can replace it with your own
4. Setup your Dialogflow CX agent
5. Create a service account for your agent using the Google cloud console. It needs to at least have *Dialogflow Client API* permission.
6. Download the .json key file for the service account and place it somewhere the PHP pages can access
    - **Important:** make sure browsers can't view or download this file! I've include a .htaccess file that instructs Apache2.4 to disallow this
7. Edit *config.php* to input your Dialogflow agent information, path to your .json key file, and other settings
8. Browse to index.php to try out the client!
9. Customise and use :)

# Development notes
## To-do list
 - Rich media handling in version 2
 - Making the CSS themeable
 - Tidy up code, variable names etc.
 - Better error handling
 - integrate another provider perhaps, e.g. GPT4
