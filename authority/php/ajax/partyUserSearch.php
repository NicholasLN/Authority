<?php
include './ajaxWrapper.php';

$searchLimit = 15;
mysqli_set_charset($db,"utf8");
$party = $_POST['partyID'];
$loggedInID = $_POST['loggedInID'];

if(!isset($_POST['searchTerm'])){
    $stmt = $db->prepare("SELECT id, politicianName as `text` FROM users WHERE party = ? AND id != ? AND lastOnline > ? ORDER BY id LIMIT ?");
    $stmt->bind_param("iiii",$party,$loggedInID,$onlineThreshold,$searchLimit);
    $stmt->execute();
    $userList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
else{
    $search = $_POST['searchTerm']."%";// Search text

    // Fetch records
    $stmt = $db->prepare("SELECT id, politicianName as `text` FROM users WHERE politicianName like ? AND party = ? AND id != ? AND lastOnline > ? ORDER BY id LIMIT ?");
    $stmt->bind_param("siiii",$search, $party, $loggedInID,$onlineThreshold,$searchLimit);
    $stmt->execute();

    $userList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$json = json_encode($userList);
echo $json;
exit();