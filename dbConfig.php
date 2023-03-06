<?php
require_once 'config.php';
$db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($db->connect_error) {
    die("Connection failed: ". $db->connect_error);
}