<?php
function getUserByID($userID): ?array
{
    global $db;
    $st = $db->prepare("SELECT * FROM users WHERE id=?");
    $st->bind_param('i', $userID);
    $st->execute();
    return $st->get_result()->fetch_assoc();
}

function getUserByPoliticianName($userName): ?array
{
    global $db;
    $st = $db->prepare("SELECT * FROM users WHERE politicianName=?");
    $st->bind_param('s', $userName);
    $st->execute();
    return $st->get_result()->fetch_assoc();
}

function query($query)
{
    global $db;
    mysqli_query($db, $query);
}

function partyNameAlreadyExists($partyName)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM parties WHERE name=?");
    $stmt->bind_param("s", $partyName);
    $stmt->execute();

    $rows = $stmt->get_result()->num_rows;
    if ($rows >= 1) {
        return true;
    } else {
        return false;
    }
}


function getNumRows($query): ?int
{
    global $db;
    $query = mysqli_query($db, $query);
    if ($query) {
        return mysqli_num_rows($query);
    } else {
        echo "<script>console.log($query)</script>";
        return 0;
    }
}

function getStateByAbbreviation($abv): ?array
{
    global $db;
    $st = $db->prepare("SELECT * FROM states WHERE abbreviation=?");
    $st->bind_param('s', $abv);
    $st->execute();
    return $st->get_result()->fetch_assoc();
}

function verifyCountry($country): ?bool{
    global $db;
    $stmt = $db->prepare("SELECT * FROM countries WHERE name=?");
    $stmt->bind_param("s",$country);
    $stmt->execute();
    if($stmt->get_result()->num_rows == 1){
        return true;
    }
    else{
        return false;
    }

}