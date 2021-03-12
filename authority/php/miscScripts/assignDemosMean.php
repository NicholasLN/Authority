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


$queryString = "";

$demos = $result->fetch_all(MYSQLI_ASSOC);
foreach ($demos as $demoID => $demoDetails) {
    $id = $demoDetails['demoID'];
    $ecoPosMean = Demographic::getDemographicMean($demoDetails, "economic");
    $socPosMean = Demographic::getDemographicMean($demoDetails, "social");

    $queryEco = "UPDATE demographics SET EcoPosMean = '$ecoPosMean' WHERE demoID=$demoID;";
    $querySoc = "UPDATE demographics SET SocPosMean = '$socPosMean' WHERE demoID=$demoID;";

    $queryString.=$queryEco;
    $queryString.=$querySoc;
}
$db->multi_query($queryString);

