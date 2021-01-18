<?php


/// PARTY POWER
$query = "SELECT * FROM users WHERE lastOnline > $onlineThreshold";
if($result = $db->query($query)) {
    while ($row = $result->fetch_assoc()) {

        $user = new User($row['id']);
        $party = new Party($row['party']);

        $baseGain = 2;

        $partyInfluenceGross = $user->getUserPartyVotes() * 3 + $baseGain;


        $socDiff = abs($user->getVariable("socPos") - $party->partyRow['socPos']);
        $ecoDiff = abs($user->getVariable("ecoPos") - $party->partyRow['ecoPos']);

        $socPerDebuff = round(($socDiff*3)+($socDiff**1.4));
        $ecoPerDebuff = round(($ecoDiff*3)+($ecoDiff**1.4));

        $totalDebuff = $socPerDebuff+$ecoPerDebuff;
        if($totalDebuff > 90){
            $totalDebuff = 90;
        }
        echo $row['politicianName'].': '.$totalDebuff."<br/>";

        $netGain = $partyInfluenceGross - ($partyInfluenceGross*($totalDebuff/100));
        $user->updateVariable("partyInfluence",$row['partyInfluence']+$netGain);


    }
}