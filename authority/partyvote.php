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
                    <h3>Party Vote</h3>
                    <hr>
                    <h4><? echo $vote->getBillTitle() ?></h4>
                    <?
                    if (isset($loggedInUser)) {
                        if ($loggedInUser->hasPartyPerm("delayVote") && !$vote->isDelayed) {
                            ?>
                            <form method="post">
                                <input type="submit" class="btn btn-danger"
                                       value="Delay Vote (lose 1/6th of influence!)"
                                       name="delaySubmit"/>
                            </form>
                            <?
                        }
                        if ($vote->isDelayed) {
                            echo "<span class='redFont'>Delayed! (+12 hours)</span><br/>";
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
                            if (isset($loggedInUser) && $loggedInUser->getVariable("party") == $partyID) {
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

                            ?>
                        </div>
                        <div class="col" style="height:30vh; border-left:1px solid black;">
                            <h5>Nays</h5>

                            <?
                            if (isset($loggedInUser) && $loggedInUser->getVariable("party") == $partyID) {
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

                            ?>
                        </div>
                    </div>
                    <hr style="margin-top:0;margin-bottom: 6px;"/>
                    <div class="row">
                        <div class="col">
                            <b class="bold">TOTAL AYES</b>
                            <br/>
                            <span class="greenFont"><? echo $vote->getAyes() ?></span>
                        </div>
                        <div class="col">
                            <b class="bold">TOTAL NAYS</b>
                            <br/>
                            <span class="redFont"><? echo $vote->getNays() ?></span>
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

