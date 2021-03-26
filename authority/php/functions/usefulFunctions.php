<?php

function in_range($number, $min, $max, $inclusive = FALSE): bool
{
    if (is_numeric($number)) {
        if ($inclusive) {
            return ($number >= $min && $number <= $max);
        } else {
            return ($number > $min && $number < $max);
        }
    } else {
        return false;
    }
}

function numFilter($number)
{
    return preg_replace("/[^0-9.]/", "", $number);
}

function numFilterNeg($number)
{
    return preg_replace("/[^0-9\-]/", "", $number);
}

function invalidPage($alertHeader = "Invalid Page", $alertMsg = "")
{
    global $loggedInID;
    if ($_SESSION['loggedIn']) {
        alert("Error", "Profile doesn't exist");
        redirect("politician.php?id=$loggedInID&alertHeader=$alertHeader&alertMsg=$alertMsg&noAlert=false");
    } else {
        redirect("index.php?alertHeader=$alertHeader&alertMsg=$alertMsg&noAlert=false");
    }

}

function getMinDifference($time1, $time2)
{
    $difference = abs($time1 - $time2);
    // in unix time, a minute is equivalent to 60 (60 seconds)
    return $difference / 60;
}

function getHourDifference($time1, $time2)
{
    $mins = getMinDifference($time1, $time2);
    return $mins / 60;

}

function getDayDifference($time1, $time2)
{
    $hours = getHourDifference($time1, $time2);
    return $hours / 24;
}

function numHash($str, $len = null)
{
    $binhash = md5($str, true);
    $numhash = unpack('N2', $binhash);
    $hash = $numhash[1] . $numhash[2];
    if ($len && is_int($len)) {
        $hash = substr($hash, 0, $len);
    }
    return $hash;
}

// random number from a normal distribution
function nrand($mean, $sd, $limit = 5, $lowerLimit = -5)
{
    $x = mt_rand() / mt_getrandmax();
    $y = mt_rand() / mt_getrandmax();
    $z = sqrt(-2 * log($x)) * cos(2 * pi() * $y) * $sd + $mean;
    if ($z > $limit) {
        return $limit;
    } else if ($z < $lowerLimit) {
        return $lowerLimit;
    } else {
        return $z;
    }
}

function nrandAverage($mean, $sd, $upperLimit = 5, $lowerLimit = -5)
{
    $total = 0;
    $iterations = 200;
    for ($i = 0; $i <= $iterations; $i++) {
        $total += nrand($mean, $sd, $upperLimit, $lowerLimit);
    }
    return $total / $iterations;
}

function array_avg($array, $round = 2)
{
    $num = count($array);
    return array_map(
        function ($val) use ($num, $round) {
            return array('percent' => round($val / $num * 100, $round));
        },
        array_count_values($array));
}

function getPositionsFromNRAND($iterations, $mean, $sd, $limit = 5, $lowerLimit = -5)
{
    $randArray = array();
    for ($i = 0; $i < $iterations; $i++) {
        array_push($randArray, (int)round(nrand($mean, $sd, $limit, $lowerLimit), 2));
    }
    $percentageArray = array_avg($randArray);
    for ($i = -5; $i <= 5; $i++) {
        if (!key_exists($i, $percentageArray)) {
            $percentageArray += array($i => array("percent" => 0));
        }
    }
    ksort($percentageArray);
    return $percentageArray;
}


function roleOptions($arr = "no")
{
    if ($arr == "no") {
        echo
        '<option value = "sendFunds" selected > Send Funds </option>
        <option value = "proposeFees" > Propose Fee Change </option>
        <option value = "delayVote" > Delay Vote </option>
        <option value = "purgeMember" > Purge Member </option >
        <option value = "fundingReq" > Fulfill Funding Requests </option>
        <option value = "sendAnnouncement" > Send Party Announcements </option>';
    }
    if ($arr == "yes") {
        return array("sendFunds", "proposeFees", "delayVote", "purgeMember", "fundingReq", "sendAnnouncement");
    }
}

function economicPositionDropdown()
{
    ?>
    <select class="form-control" name="ecoPos">
        <option value="-5">Collectivism</option>
        <option value="-4">Socialism</option>
        <option value="-3">Left Wing</option>
        <option value="-2">Slightly Left Wing</option>
        <option value="-1">Center Left</option>
        <option value="0" selected>Mixed Capitalism</option>
        <option value="1">Center Right</option>
        <option value="2">Slightly Right Wing</option>
        <option value="3">Right Wing</option>
        <option value="4">Capitalism</option>
        <option value="5">Libertarianism</option>
    </select>
    <?
}

function socialPositionDropdown()
{
    ?>
    <select class="form-control" name="socPos">
        <option value="-5">Anarchism</option>
        <option value="-4">Communalism</option>
        <option value="-3">Left Wing</option>
        <option value="-2">Slightly Left Wing</option>
        <option value="-1">Center Left</option>
        <option value="0" selected>Centrist</option>
        <option value="1">Center Right</option>
        <option value="2">Slightly Right Wing</option>
        <option value="3">Right Wing</option>
        <option value="4">Authoritarian Right</option>
        <option value="5">Totalitarian Right</option>
    </select>
    <?
}

function getPartyFromId(int $partyId): array
{
    global $db;

    $stmt = $db->prepare("SELECT * FROM parties WHERE id = ?");
    $stmt->bind_param("i", $partyId);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_array();
}

function changeKey($array, $oldKey, $newKey): array
{
    if(!array_key_exists($oldKey, $array)) {
        return $array;
    }
    $keys = array_keys($array);
    $keys[array_search($oldKey, $keys, false)] = $newKey;

    return array_combine($keys, $array);
}

// I'll mainly use this for database storage.
function compressObject($object, $level=9): string
{
    return base64_encode(gzcompress(serialize($object),$level));
}
function decompressObject($object, $class, $level=9){
    $array = array("allowed_classes"=>true);

    return unserialize(gzuncompress(base64_decode($object)),$array);
}