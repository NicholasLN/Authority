<?php
$serverConfigFile = file_get_contents('../../config/server.json');
// If there is an error in opening the config for the DB
if($serverConfigFile == false){
    echo "There was an error opening the server configuration required for the database.";
}
else{
    $serverConfig = json_decode($serverConfigFile,true);

    $dbUser = $serverConfig['user'];
    $dbPassword = $serverConfig['password'];
    $dbHost = $serverConfig['host'];
    $dbDB = $serverConfig['database'];
    $port = $serverConfig['port'];

    $db = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbDB, $port);
}

include '../functionsOnly.php';
