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
        if ($uresult = $db->query($userQuery)) {
            // for each user
            while ($urow = $uresult->fetch_assoc()) {
                // User positions account for 50% of party drift. 50% is attributed to base party positions.
                $userShare = ($urow['partyInfluence'] / $totalPartyInfluence) / 2;
                $weightedEco += $urow['ecoPos'] * $userShare;
                $weightedSoc += $urow['socPos'] * $userShare;
            }
        }
        // Intial party positions account for 50% of drift //
        $weightedEco += $row['initialEcoPos'] * .5;
        $weightedSoc += $row['initialSocPos'] * .5;


        $party->updateVariable("socPos", round($weightedSoc, 2));
        $party->updateVariable("ecoPos", round($weightedEco, 2));


    }
}