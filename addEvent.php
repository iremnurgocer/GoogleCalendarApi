<?php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME','events');

// Google API configuration
define('GOOGLE_CLIENT_ID','73729047030-vqa38dobo280pavrad7t9itp57hn1fvj.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET','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');
define('GOOGLE_OAUTH_SCOPE','https://www.googleapis.com/auth/calendar');
define('REDIRECT_URI','http://localhost/www/iremnurgocer/google-api-php-client--PHP7.4/login.php');

// Google OAuth URL
$googleOauthURL = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode(GOOGLE_OAUTH_SCOPE) . '&redirect_uri=' . REDIRECT_URI . '&response_type=code&client_id=' . GOOGLE_CLIENT_ID . '&access_type=online';
if(!session_id()) {session_start();}

try{
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e->getMessage());
}

$postData = $statusMsg = $valErr = '';
$status = 'danger';

// If the form is submitted
if(isset($_POST['submit'])){

    // Get event info
    $_SESSION['postData'] = $_POST;
    $title = !empty($_POST['title'])?trim($_POST['title']):'';
    $description = !empty($_POST['description'])?trim($_POST['description']):'';
    $location = !empty($_POST['location'])?trim($_POST['location']):'';
    $date = !empty($_POST['date'])?trim($_POST['date']):'';
    $time_from = !empty($_POST['time_from'])?trim($_POST['time_from']):'';
    $time_to = !empty($_POST['time_to'])?trim($_POST['time_to']):'';

    // Validate form input fields
    if(empty($title)){
        $valErr .= 'Please enter event title.<br/>';
    }
    if(empty($date)){
        $valErr .= 'Please enter event date.<br/>';
    }

    // Check whether user inputs are empty
    if(empty($valErr)){
        // Insert data into the database
        $sqlQ = "INSERT INTO events (title,description,location,date,time_from,time_to,created) VALUES (?,?,?,?,?,?,?)";
        $stmt = $db->prepare($sqlQ);
        $db_title = $title;
        $db_description = $description;
        $db_location = $location;
        $db_date = $date;
        $db_time_from = $time_from;
        $db_time_to = $time_to;
        $cevaplar = array($db_title, $db_description, $db_location, date('Y-m-d' ,strtotime($db_date)), $db_time_from, $db_time_to, date('Y-m-d H:i:s'));
        $insert = $stmt->execute($cevaplar);
            if($insert){
            $event_id = $stmt->insert_id;

            unset($_SESSION['postData']);
            // Store event ID in session
            $_SESSION['last_event_id'] = $event_id;
            header("Location: $googleOauthURL");
            exit();
            }
            else{
                echo 'Error';
            }

    }else{
        $statusMsg = '<p>Please fill all the mandatory fields:</p>'.trim($valErr, '<br/>');
    }
}else{
    $statusMsg = 'Form submission failed!';
}
$_SESSION['status_response'] = array('status' => $status, 'status_msg' => $statusMsg);

header("Location: login.php");
exit();