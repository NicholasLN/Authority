<?php
include './ajaxWrapper.php';

$searchLimit = 10;
mysqli_set_charset($db,"utf8");
$party = $_POST['partyID'];
$pClass = new Party($party);
$loggedInID = $_POST['loggedInID'];

if(!isset($_POST['searchTerm'])){
    $stmt = $db->prepare("SELECT id, politicianName as `text` FROM users WHERE party = ? AND id != ? AND lastOnline > ? ORDER BY partyInfluence LIMIT ?");
    $stmt->bind_param("iiii",$party,$loggedInID,$onlineThreshold,$searchLimit);
    $stmt->execute();
    $userList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
else{
    $search = $_POST['searchTerm']."%";// Search text

    // Fetch records
    $stmt = $db->prepare("SELECT id, politicianName as `text` FROM users WHERE politicianName like ? AND party = ? AND id != ? AND lastOnline > ? ORDER BY partyInfluence LIMIT ?");
    $stmt->bind_param("siiii",$search, $party, $loggedInID,$onlineThreshold,$searchLimit);
    $stmt->execute();

    $userList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
$userList2 = array();

foreach($userList as &$user){
    if($pClass->partyRoles->getUserTitle($user['id']) == "Member"){
        $arr = array("id"=>$user['id'],"text"=>$user['text']);
        array_push($userList2,$arr);
    }
}
//var_dump($userList);

$json = json_encode($userList2);
echo $json;
exit();