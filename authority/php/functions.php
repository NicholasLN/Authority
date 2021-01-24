<?php
session_start();
$onlineThreshold = time() - 259200;
$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];

$serverConfigFile = file_get_contents('./config/server.json');
// If there is an error in opening the config for the DB
if($serverConfigFile == false){
    echo "There was an error opening the server configuration required for the database.";
}
else{
    $serverConfig = json_decode($serverConfigFile, true);

    $dbUser = $serverConfig['user'];
    $dbPassword = $serverConfig['password'];
    $dbHost = $serverConfig['host'];
    $dbDB = $serverConfig['database'];
    $port = $serverConfig['port'];

    $db = @mysqli_connect($dbHost, $dbUser, $dbPassword, $dbDB, $port);
    @mysqli_set_charset($db, "utf8");


}

include 'functions/queryFunctions.php';
include 'classes/classWrapper.php';


function generateCookie(): string
{
    $randomInt = rand(0, 99999999999999);
    return hash('fnv1a64', $randomInt);
}

if (!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
    setcookie("sessionIdentifier", generateCookie());
}
if (isset($_SESSION['loggedInID'])) {
    if (getNumRows("SELECT * FROM users WHERE id=" . $_SESSION['loggedInID']) != 1) {
        session_destroy();
    } else {
        $user = new User($_SESSION['loggedInID']);
        $loggedInID = $_SESSION['loggedInID'];
        $loggedInRow = $user->getUserRow();
        $loggedInUser = new User($loggedInID);
        $user->updateTime();
    }
}


include 'essentials/registerLogin.php';
include 'essentials/htmlFunctions.php';
include 'essentials/navBar.php';

include 'functions/positionFunctions.php';
include 'functions/usefulFunctions.php';
include 'functions/discordMeta.php';

include 'views/party.php';