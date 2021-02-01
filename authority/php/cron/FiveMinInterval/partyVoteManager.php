<?php
$query = "SELECT * FROM partyVotes WHERE passed=0";
if ($result = $db->query($query)) {
    while ($voteRow = $result->fetch_assoc()) {
        $expiresAt = $voteRow['expiresAt'];
        $vote = new PartyVote($voteRow['id']);
        $minDifference = getMinDifference($expiresAt, time());

        $percentage = 51;
        $currentVotePercentage = ($vote->ayes / $vote->party->getVariable("votes")) * 100;

        if (time() > $expiresAt) {
            echo $currentVotePercentage;
            if ($currentVotePercentage > $percentage) {
                $vote->passVote(false);
            } else {
                $vote->passVote(true);
            }
        } else {
            // auto pass
            if ($currentVotePercentage > $percentage) {
                $vote->passVote(false);
            }
        }
    }
}