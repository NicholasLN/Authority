<?php

function in_range($number, $min, $max, $inclusive = FALSE): bool
{
    if (is_int($number) && is_int($min) && is_int($max))
    {
        if ($inclusive) {
            return ($number >= $min && $number <= $max);
        } else {
            return ($number > $min && $number < $max);
        }
    }
    return FALSE;
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
        redirect("index.php?alertHeader=$alertHeader&alertMsg=$alertMsg");
    }

}

function getMinDifference($time1, $time2){
    $difference = abs($time1-$time2);
    // in unix time, a minute is equivalent to 60 (60 seconds)
    return $difference/60;
}
function getHourDifference($time1,$time2){
    $mins = getMinDifference($time1, $time2);
    return $mins/60;

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