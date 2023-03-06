<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME','events');

// Google API configuration
define('GOOGLE_CLIENT_ID','73729047030-vqa38dobo280pavrad7t9itp57hn1fvj.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET','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');
define('GOOGLE_OAUTH_SCOPE','https://www.googleapis.com/auth/calendar');
define('REDIRECT_URI','http://localhost/www/iremnurgocer/google-api-php-client--PHP7.4/google_calendar_event_sync.php');

// Google OAuth URL
$googleOauthURL = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode(GOOGLE_OAUTH_SCOPE) . '&redirect_uri=' . REDIRECT_URI . '&response_type=code&client_id=' . GOOGLE_CLIENT_ID . '&access_type=online';
if(!session_id()) {session_start();}
?>