<?
include '../functionsOnly.php';
$serverConfigFile = file_get_contents('./../../config/server.json');
// If there is an error in opening the config for the DB
if ($serverConfigFile == false) {
    echo "There was an error opening the server configuration required for the database.";
} else {
    $serverConfig = json_decode($serverConfigFile, true);

    $dbUser = $serverConfig['user'];
    $dbPassword = $serverConfig['password'];
    $dbHost = $serverConfig['host'];
    $dbDB = $serverConfig['database'];
    $port = $serverConfig['port'];

    $db = @mysqli_connect($dbHost, $dbUser, $dbPassword, $dbDB, $port);
    @mysqli_set_charset($db, "utf8");
}

$stmt = $db->prepare("SELECT * FROM demographics WHERE demographics.State IN (SELECT states.abbreviation FROM states WHERE active = 1)");
$stmt->execute();

$result = $stmt->get_result();

$demos = $result->fetch_all(MYSQLI_ASSOC);

$queryString = "";

foreach ($demos as $demoKeyID => $demoDetails) {
    $demoID = $demoDetails['demoID'];
    $eArray = getPositionsFromNRAND(rand(1000,2500), $demoDetails['EcoPosMean'], $demoDetails['Polarization']);
    //var_dump($eArray);
    $sArray = getPositionsFromNRAND(rand(1000,2500), $demoDetails['SocPosMean'], $demoDetails['Polarization']);

    $e = "
    UPDATE demoPositions 
        SET 
            `-5` = ".$eArray[-5]['percent'].", 
            `-4` = ".$eArray[-4]['percent'].", 
            `-3` = ".$eArray[-3]['percent'].", 
            `-2` = ".$eArray[-2]['percent'].", 
            `-1` = ".$eArray[-1]['percent'].",
            `0` = ".$eArray[0]['percent'].",
            `1` = ".$eArray[1]['percent'].",
            `2` = ".$eArray[2]['percent'].",
            `3` = ".$eArray[3]['percent'].", 
            `4` = ".$eArray[4]['percent'].", 
            `5` = ".$eArray[5]['percent']."
        WHERE demoID = $demoID AND type='economic';
    ";
    $s = "
    UPDATE demoPositions 
        SET 
            `-5` = ".$sArray[-5]['percent'].", 
            `-4` = ".$sArray[-4]['percent'].", 
            `-3` = ".$sArray[-3]['percent'].", 
            `-2` = ".$sArray[-2]['percent'].", 
            `-1` = ".$sArray[-1]['percent'].",
            `0` = ".$sArray[0]['percent'].",
            `1` = ".$sArray[1]['percent'].",
            `2` = ".$sArray[2]['percent'].",
            `3` = ".$sArray[3]['percent'].", 
            `4` = ".$sArray[4]['percent'].", 
            `5` = ".$sArray[5]['percent']."
        WHERE demoID = $demoID AND type='social';
    ";
    $queryString.=$e;
    $queryString.=$s;
}
$db->multi_query($queryString);