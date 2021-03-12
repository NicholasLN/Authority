<?php
$query = "SELECT * FROM partyVotes WHERE passed=0";
if ($result = $db->query($query)) {
    while ($voteRow = $result->fetch_assoc()) {
        $expiresAt = $voteRow['expiresAt'];
        $vote = new PartyVote($voteRow['id']);
        $minDifference = getMinDifference($expiresAt, time());

        $percentage = 51;
        $autoPassPercent = $vote->totalVotesPartyPercentage * 100;
        
        if($vote->totalVotes > 0){
            $regularPassPercent = round(($vote->ayes / $vote->totalVotes) * 100, 2);
        }
        else{ 
            $regularPassPercent = 0;
        }


        // If the bill has run out of time.
        if (time() > $expiresAt) {
            if ($regularPassPercent > $percentage) {
                $vote->passVote(false);
            } else {
                $vote->passVote(true);
            }
        } else {
            // auto pass
            if ($autoPassPercent > $percentage) {
                echo "trueeeee th";
                $vote->passVote(false);
            }
        }
    }
}