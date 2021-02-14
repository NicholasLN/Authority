<?php
$query = "SELECT * FROM partyVotes WHERE passed=0";
if ($result = $db->query($query)) {
    while ($voteRow = $result->fetch_assoc()) {
        $expiresAt = $voteRow['expiresAt'];
        $vote = new PartyVote($voteRow['id']);
        $minDifference = getMinDifference($expiresAt, time());

        $percentage = 51;
        $autoPassPercent = $vote->totalVotesPartyPercentage * 100;
        $regularPassPercent = round(($vote->ayes / $vote->totalVotes) * 100, 2);

        if (time() > $expiresAt) {
            if ($regularPassPercent > $percentage) {
                $vote->passVote(false);
            } else {
                $vote->passVote(true);
            }
        } else {
            // auto pass
            if ($autoPassPercent > $percentage) {
                $vote->passVote(false);
            }
        }
    }
}