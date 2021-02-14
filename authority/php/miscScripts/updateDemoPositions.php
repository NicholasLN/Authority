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

$stmt = $db->prepare("SELECT * FROM demographics");
$stmt->execute();

$result = $stmt->get_result();

$demos = $result->fetch_all(MYSQLI_ASSOC);

foreach ($demos as $demoKeyID => $demoDetails) {
    $demoID = $demoDetails['demoID'];
    $eArray = getPositionsFromNRAND(5000, $demoDetails['EcoPosMean'], 1.4);
    //var_dump($eArray);
    $sArray = getPositionsFromNRAND(5000, $demoDetails['SocPosMean'], 1.4);

    $e = "UPDATE demoPositions 
                SET 
                    `-5` = ?, `-4` = ?, `-3` = ?, `-2` = ?, `-1` = ?,
                    `0` = ?,
                    `1` = ?, `2` = ?, `3` = ?, `4` = ?, `5` = ?
                WHERE demoID = $demoID AND type='economic'
          ";
    $s = "UPDATE demoPositions 
                SET 
                    `-5` = ?, `-4` = ?, `-3` = ?, `-2` = ?, `-1` = ?,
                    `0` = ?,
                    `1` = ?, `2` = ?, `3` = ?, `4` = ?, `5` = ?
                WHERE demoID = $demoID AND type='social'
          ";

    $eArrayStmt = $db->prepare($e);
    $eArrayStmt->bind_param("ddddddddddd",
        $eArray[-5]['percent'], $eArray[-4]['percent'], $eArray[-3]['percent'], $eArray[-2]['percent'], $eArray[-1]['percent'],
        $eArray[0]['percent'],
        $eArray[1]['percent'], $eArray[2]['percent'], $eArray[3]['percent'], $eArray[4]['percent'], $eArray[5]['percent']
    );
    $sArrayStmt = $db->prepare($s);
    echo $sArray[-5]['percent'];
    $sArrayStmt->bind_param("ddddddddddd",
        $sArray[-5]['percent'], $sArray[-4]['percent'], $sArray[-3]['percent'], $sArray[-2]['percent'], $sArray[-1]['percent'],
        $sArray[0]['percent'],
        $sArray[1]['percent'], $sArray[2]['percent'], $sArray[3]['percent'], $sArray[4]['percent'], $sArray[5]['percent']
    );
    $eArrayStmt->execute();
    $sArrayStmt->execute();

}