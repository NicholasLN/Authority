<?php
include './ajaxWrapper.php';

$searchLimit = 10;
mysqli_set_charset($db,"utf8");
$party = $_POST['partyID'];
$pClass = new Party($party);

if(!isset($_POST['searchTerm'])){
    $stmt = $db->prepare("SELECT id, politicianName as `text` FROM users WHERE party = ? AND lastOnline > ? ORDER BY partyInfluence LIMIT ?");
    $stmt->bind_param("iii",$party,$onlineThreshold,$searchLimit);
    $stmt->execute();
    $userList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
else{
    $search = $_POST['searchTerm']."%";// Search text

    // Fetch records
    $stmt = $db->prepare("SELECT id, politicianName as `text` FROM users WHERE politicianName like ? AND party = ? AND lastOnline > ? ORDER BY partyInfluence LIMIT ?");
    $stmt->bind_param("siii",$search, $party, $onlineThreshold,$searchLimit);
    $stmt->execute();

    $userList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
$json = json_encode($userList);
echo $json;
exit();