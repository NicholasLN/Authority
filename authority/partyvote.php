<?php include 'php/functions.php'; ?>
<?php
if (isset($_GET['id'])) {
    $voteID = numFilter($_GET['id']);
    $vote = new PartyVote($voteID);
    $partyID = $vote->party->getVariable("id");
    if (!$vote->voteExists) {
        invalidPage("Error!", "Invalid Bill Page!");
    }
} else {
    invalidPage("Error!", "Invalid Bill Page!");
}
?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>Authority</title>
        <? echoHeader(); ?>
    </head>
    <? echoNavBar() ?>
    <body>
    <div class="main">
        <div class="gameContainer">
            <div class="row">
                <div class="col"></div>
                <div class="col-sm-10">
                    <br/>
                    <?
                    $party = $vote->party;
                    $img = $party->pictureArray()['picture'];
                    $name = $party->pictureArray()['name'];
                    $id = $party->pictureArray()["id"];
                    ?>
                    <a href="party.php?id=<? echo $id ?>">
                        <img src="<? echo $img ?>" style="max-width:150px;max-height:150px;">
                        <br/>
                        <h5><? echo $name ?></h5>
                    </a>
                    <a href="partycommittee.php?id=<? echo $partyID ?>">
                        <button style="margin-bottom: 6px" class="btn btn-primary btn-sm">Back to Committee</button>
                    </a>
                    <h3>Party Vote</h3>
                    <hr>
                    <h4><? echo $vote->getBillTitle() ?></h4>
                    <?
                    if (isset($loggedInUser)) {
                        if ($loggedInUser->hasPartyPerm("delayVote") && $loggedInUser->getVariable("party") == $partyID && !$vote->isDelayed && !$vote->votingEnded) {
                            ?>
                            <form method="post">
                                <input type="submit" class="btn btn-danger"
                                       value="Delay Vote (lose 1/6th of influence!)"
                                       name="delaySubmit"/>
                            </form>
                            <?
                        }
                        if ($vote->isDelayed && !$vote->votingEnded) {
                            echo "<span class='redFont'>Delayed! (+12 hours)</span><br/>";
                        } else if ($vote->votingEnded) {
                            if ($vote->hasPassed) {
                                echo "<span class='greenFont'>This vote has passed through the party committee.</span>";
                            } else {
                                echo "<span class='redFont'>This vote has failed to pass through the party committee.</span>";
                            }

                        }

                    }

                    ?>
                    <hr/>
                    <h5>Vote Actions</h5>
                    <p style="margin-bottom: 2px"><? echo str_replace('<hr>', '<br/>', $vote->getBillRundown(true)) ?></p>
                    <br/>
                    <div class="row">
                        <div class="col" style="height:30vh; border-right:1px solid black;">
                            <h5>Ayes</h5>
                            <?
                            if (isset($loggedInUser) && $loggedInUser->getVariable("party") == $partyID && !$vote->votingEnded) {
                                echo "
                                <form method='post'>
                                    <input type='submit' class='btn btn-primary' value='Vote Aye' name='voteAyeSubmit'>
                                </form>       
                            ";

                            }
                            ?>
                            <hr/>
                            <?
                            $ayes = $vote->getAyesArray();
                            foreach ($ayes as $politician => $votes) {
                                $user = User::withPoliticianName($politician);
                                $userID = $user->getVariable("id");
                                $state = $user->getVariable("state");
                                if ($votes >= 1) {
                                    ?>
                                    <div style="margin-top:3px">
                                        <b class="bold">
                                            <a href="politician.php?id=<? echo $userID ?>">
                                                <? echo $politician . " [$state]" ?>
                                            </a>:
                                        </b>
                                        <? echo $votes ?> Votes
                                    </div>
                                    <?
                                }
                            }

                            ?>
                        </div>
                        <div class="col" style="height:30vh; border-left:1px solid black;">
                            <h5>Nays</h5>

                            <?
                            if (isset($loggedInUser) && $loggedInUser->getVariable("party") == $partyID && !$vote->votingEnded) {
                                echo "
                                <form method='post'>
                                    <input type='submit' class='btn btn-danger' value='Vote Nay' name='voteNaySubmit'>
                                </form>       
                            ";

                            }
                            ?>
                            <hr/>
                            <?
                            $nays = $vote->getNaysArray();
                            foreach ($nays as $politician => $votes) {
                                $user = User::withPoliticianName($politician);
                                $userID = $user->getVariable("id");
                                $state = $user->getVariable("state");
                                if ($votes >= 1) {
                                    ?>
                                    <div style="margin-top:3px">
                                        <b class="bold">
                                            <a href="politician.php?id=<? echo $userID ?>">
                                                <? echo $politician . " [$state]" ?>
                                            </a>:
                                        </b>
                                        <? echo $votes ?> Votes
                                    </div>
                                    <?
                                }
                            }

                            ?>
                        </div>
                    </div>
                    <hr style="margin-top:0;margin-bottom: 6px;"/>
                    <div class="row">
                        <div class="col">
                            <b class="bold">TOTAL AYES</b>
                            <br/>
                            <span class="greenFont"><? echo $vote->ayes ?></span>
                        </div>
                        <div class="col">
                            <b class="bold">TOTAL NAYS</b>
                            <br/>
                            <span class="redFont"><? echo $vote->nays ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <b class="bold">PERCENTAGE (51% to pass)</b>
                            <br/>
                            <?
                            if ($vote->ayes > 0 || $vote->nays > 0) {
                                $percent = round(($vote->ayes / $vote->totalVotes) * 100, 2);
                            } else {
                                $percent = 0;
                            }
                            if ($percent < 51) {
                                echo "<span class='redFont'><b>$percent% of existing votes (not enough to pass)</b></span>";
                            } else {
                                echo "<span class='greenFont'><b class='bold'>$percent% of existing votes</b></span>";
                            }
                            ?>
                            <br/>
                            <br/>
                            <b class="bold">AUTO-PASS PERCENTAGE (51% of all party votes)</b>
                            <br/>
                            <?
                            $autoPassPercent = $vote->totalVotesPartyPercentage * 100;
                            if ($autoPassPercent < 51) {
                                echo "<span class='redFont'><b>$autoPassPercent% of party votes (not enough to instapass)</b></span>";
                            } else {
                                echo "<span class='greenFont'><b class='bold'>$autoPassPercent% of party votes</b></span>";
                            }
                            ?>
                        </div>
                    </div>
                    <br/>
                    <br/>
                </div>
                <div class="col"></div>
            </div>
        </div>
        <? echoFooter() ?>
    </div>
    </html>
<?php

if (isset($_POST)) {
    if ($_SESSION['loggedIn']) {
        if (isset($_POST['voteAyeSubmit'])) {
            if ($loggedInUser->getVariable("party") == $partyID) {
                if ($loggedInUser->getCommitteeVotes() > 0) {
                    $vote->addVote("aye", $loggedInID);
                    redirect("partyvote.php?id=$voteID");
                } else {
                    alert("Error!", "You do not have any votes!");
                }
            } else {
                alert("Error!", "Not your party!");
            }
        }
        if (isset($_POST['voteNaySubmit'])) {
            if ($loggedInUser->getVariable("party") == $partyID) {
                if ($loggedInUser->getCommitteeVotes() > 0) {
                    $vote->addVote("nay", $loggedInID);
                    redirect("partyvote.php?id=$voteID");
                } else {
                    alert("Error!", "You do not have any votes!");
                }
            } else {
                alert("Error!", "Not your party!");
            }
        }
        if (isset($_POST['delaySubmit'])) {
            if ($loggedInUser->hasPartyPerm("delayVote")) {
                if (!$vote->isDelayed) {
                    $vote->delayVote();
                    $newPartyInfluence = $loggedInUser->getVariable("partyInfluence")
                        - ($loggedInUser->getVariable("partyInfluence") * (1 / 6));
                    $loggedInUser->updateVariable("partyInfluence", $newPartyInfluence);
                    redirect("partyvote.php?id=$voteID", "Delayed!", "Bill has been delayed.", "success");

                } else {
                    alert("Error!", "It is already delayed.");
                }

            } else {
                alert("Error!", "Go fuck off and go fuck yourself.");
            }

        }
    }
}

