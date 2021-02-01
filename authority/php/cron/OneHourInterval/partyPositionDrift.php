<?php

$query = "SELECT * FROM parties";
if($result = $db->query($query)) {
    while ($row = $result->fetch_assoc()) {


        $id = $row['id'];

        $weightedEco = 0;
        $weightedSoc = 0;

        //First, we need to extract to total party influence within the party, so call to the party function for this.
        $party = new Party($id);
        $totalPartyInfluence = $party->getTotalPartyInfluence();
        //
        // Now select every ACTIVE user in that party.
        $userQuery = "SELECT * FROM users WHERE party=$id AND lastOnline>$onlineThreshold";
        if($uresult = $db->query($userQuery)){
            // for each user
            while($urow=$uresult->fetch_assoc()){
                $userShare = $urow['partyInfluence']/$totalPartyInfluence;
                $weightedEco += $urow['ecoPos']*$userShare;
                $weightedSoc += $urow['socPos']*$userShare;
            }
        }
        $party->updateVariable("socPos",round($weightedSoc,2));
        $party->updateVariable("ecoPos",round($weightedEco,2));



    }
}