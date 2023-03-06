<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="style/bootstrap.min.css">
</head>
<body>
<?php
session_start();
include_once 'vendor/autoload.php';
include_once 'config.php';
$postData = '';
if(!empty($_SESSION['postData'])){
    $postData = $_SESSION['postData'];
    unset($_SESSION['postData']);
}

$status = $statusMsg = '';
if(!empty($_SESSION['status_response'])){
    $status_response = $_SESSION['status_response'];
    $status = $status_response['status'];
    $statusMsg = $status_response['status_msg'];
}

if(!empty($statusMsg)){ ?>
    <div class="alert alert-<?php echo $status; ?>"><?php echo $statusMsg; ?></div>
<?php }
$redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$client = new Google\Client();

$client->setAuthConfig('client_secret_73729047030-vqa38dobo280pavrad7t9itp57hn1fvj.apps.googleusercontent.com.json');

$client->setRedirectUri($redirect_uri);

$client->addScope(Google_Service_Calendar::CALENDAR);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
    $_SESSION['token'] = $token;
    header('Location: '.filter_var($redirect_uri,FILTER_SANITIZE_URL));
}

if (!empty($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
    if ($client->isAccessTokenExpired()) {
        unset($_SESSION['token']);
    }
} else {
    $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken()) {
    $service = new Google_Service_Calendar($client);
    $calendarList = $service->calendarList->listCalendarList();

    foreach ($calendarList->getItems() as $calendarListEntry) {

        echo '<pre>'.$calendarListEntry->getSummary().'</pre>';

    }
}
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e->getMessage());
}

$postData = $statusMsg = $valErr = '';
$status = 'danger';

// If the form is submitted
if (isset($_POST['submit'])) {
    echo $_POST['submit'];
    // Get event info
    $_SESSION['postData'] = $_POST;
    $title = !empty($_POST['title']) ? trim($_POST['title']) : '';
    $description = !empty($_POST['description']) ? trim($_POST['description']) : '';
    $location = !empty($_POST['location']) ? trim($_POST['location']) : '';
    $date = !empty($_POST['date']) ? trim($_POST['date']) : '';
    $time_from = !empty($_POST['time_from']) ? trim($_POST['time_from']) : '';
    $time_to = !empty($_POST['time_to']) ? trim($_POST['time_to']) : '';

    // Validate form input fields
    if (empty($title)) {
        $valErr .= 'Please enter event title.<br/>';
    }
    if (empty($date)) {
        $valErr .= 'Please enter event date.<br/>';
    }
    // Check whether user inputs are empty
    if (empty($valErr)) {

        // Insert data into the database
        $sqlQ = "INSERT INTO events (title,description,location,date,time_from,time_to,created) VALUES (?,?,?,?,?,?,?)";
        $stmt = $db->prepare($sqlQ);
        $db_title = $title;
        $db_description = $description;
        $db_location = $location;
        $db_date = $date;
        $db_time_from = $time_from;
        $db_time_to = $time_to;
        $cevaplar = array($db_title, $db_description, $db_location, date('Y-m-d', strtotime($db_date)), $db_time_from, $db_time_to, date('Y-m-d H:i:s'));
        $insert = $stmt->execute($cevaplar);
        if ($insert) {
            $event_id = $db->lastInsertId();
            $service = new Google_Service_Calendar($client);
            $event = new Google_Service_Calendar_Event(array(
                'summary' => 'Google I/O 2015',
                'description' => 'A chance to hear more about Google\'s developer products.',
                'start' => array(
                    'dateTime' => '2023-02-28T09:00:00Z'
                ),
                'end' => array(
                    'dateTime' => '2023-02-28T17:00:00Z'
                )
            ));
            $calendarId = 'd91a66e5acda30f3827caa454335d943f74feb574f14ebdc5d66af491a9dd3ba@group.calendar.google.com';
            $event = $service->events->insert($calendarId, $event);
            $event->getId();

            printf('Event created: %s\n', $event->htmlLink);

            unset($_SESSION['postData']);
            // Store event ID in session
            $_SESSION['last_event_id'] = $event_id;
           // header("Location: $googleOauthURL");
            $status = 'success';
            $statusMsg = 'Form submission';


        } else {
            $statusMsg = 'Form submission failed!';
        }

    } else {
        $statusMsg = '<p>Please fill all the mandatory fields:</p>' . trim($valErr, '<br/>');
    }
}
$_SESSION['status_response'] = array('status' => $status, 'status_msg' => $statusMsg);
?>
<?php
if (isset($authUrl)){
    ?>
    <div class="card" style="padding: 100px;padding-left:300px;">
        <img class="card-img-top img-fluid" src="https://www.freepnglogos.com/uploads/google-logo-png/google-logo-png-google-sva-scholarship-20.png" style="max-height:40% ; width:40%; display: block;">
        <div class="card-body">
            <h5 class="card-title">Google Takvim için bağlan</h5>
            <p class="card-text">Google takvim'i çağırmak için bağlan ve izin ver!</p>

            <a class="btn btn-primary" href="<?=$authUrl?>" role="button" >bağlan</a>

        </div>
    </div>
<?php

}
else{
    ?>
<div class="container">
    <h1>ADD EVENT</h1>
    <div class="wrapper">
        <div class="col-md-12">
            <form method="post" class="form">
                <div class="form-group">
                    <label>Event Title</label>
                    <input type="text" class="form-control" name ="title" value ="<?php echo !empty($postData['title'])?$postData['title']:''; ?>" required =""'>
                </div>
                <div class="form-group">
                    <label>Event Description</label>
                    <textarea name="description" class ="form-control"><?php echo !empty($postData['description'])?$postData['description']:''; ?></textarea>
                </div>
                <div class ="form-group">
                    <label>Location</label>
                    <input type="text" name ="location" class ="form-control" value="<?php echo !empty($postData['location'])?$postData['location']:''; ?>">
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input tvpe= "date" name ="date" class ="form-control" value="<?php echo !empty($postData['date'])?$postData['date']:''; ?>" required ="">
                </div>
                <div class="form-group time">
                    <label>Time</label>
                    <input type="time" name="time from" class="form-control" value="<?php echo !empty($postData['time_from'])?$postData['time_from']:''; ?>">
                    <span>TO</span>
                    <input type="time" name="time to" class ="form-control" value="<?php echo !empty($postData['time_to'])?$postData['time_to']:''; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" class="form-control btn-primary" name="submit" value ="Add Event"/>
                </div>
            </form>
        </div>
    </div>
    <div>

    <?php
}?>


</body>
</html>
