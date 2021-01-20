<?php
include './ajaxWrapper.php';

$searchLimit = 15;
mysqli_set_charset($db,"utf8");

if(!isset($_POST['searchTerm'])){
    $stmt = $db->prepare("SELECT id, politicianName as `text` FROM users ORDER BY id LIMIT ?");
    $stmt->bind_param("i",$searchLimit);
    $stmt->execute();
    $userList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
else{
    $search = $_POST['searchTerm']."%";// Search text

    // Fetch records
    $stmt = $db->prepare("SELECT id, politicianName as `text` FROM users WHERE politicianName like ? ORDER BY id LIMIT ?");
    $stmt->bind_param("si",$search,$searchLimit);
    $stmt->execute();

    $userList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$json = json_encode($userList);
echo $json;
exit();