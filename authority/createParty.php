<?php include 'php/functions.php'; ?>
<?php
if (!$_SESSION['loggedIn']) {
    invalidPage("Error!", "Log in first!");
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>Authority | CREATE PARTY</title>
        <? echoHeader(); ?>
    </head>
    <? echoNavBar() ?>
    <body>
    <div class="main">
        <div class="gameContainer">
            <div class="row">
                <div class="col-sm"></div>
                <div class="col-sm-8">

                    <br/>
                    <img class="img-fluid" style="max-width:200px;" src="images/partyPics/default.png"
                         alt="defaultPartyPic"/>
                    <hr/>
                    <h3>Create a Political Party</h3>
                    <p>Political Parties in Authority are the foundation towards any political success.
                        By working together to achieve common goals, you're more likely to succeed.<br/>So, <b
                                class="bold">make a party today!</b></p>
                    <hr/>
                    <form method="post">
                        <table class="table table-striped">
                            <thead class="dark">
                            <tr>
                                <th style="width:25%">Required Field</th>
                                <th style="width:75%">Input</th>
                            </tr>
                            </thead>
                            <tr>
                                <td>Party Name</td>
                                <td><input class="form-control" type="input" name="partyName" placeholder="Party Name"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Base Economic Positions</td>
                                <td><? economicPositionDropdown() ?></td>
                            </tr>
                            <tr>
                                <td>Base Social Positions</td>
                                <td><? socialPositionDropdown() ?></td>
                            </tr>
                            <tr>
                                <td>
                                    Leader Title
                                    <br/>
                                    <span class="bold">Default: <u>Chairman</u></span>
                                </td>
                                <td><input class="form-control" type="input" name="leaderTitle"
                                           placeholder="Leader Title"/></td>
                            </tr>
                        </table>
                        <br/>
                        <input class="btn btn-primary" type="submit" name="createPartySubmit"
                               value="Create Party (Costs $50,000)"/>
                    </form>

                </div>
                <div class="col-sm"></div>
            </div>
        </div>
        <? echoFooter() ?>
    </div>
    </html>
<?php
if ($_SESSION['loggedIn']) {
    if ($loggedInUser->hasCampaignFunds(50000)) {
        if (isset($_POST['partyName'])) {
            $partyName = trim($_POST['partyName']);
            if (!partyNameAlreadyExists($partyName)) {
                if (strlen($partyName) >= 7) {
                    $ecoPosition = $_POST['ecoPos'];
                    $socPosition = $_POST['socPos'];
                    if (in_range($ecoPosition, -6, 6) === true) {
                        if (in_range($socPosition, -6, 6) === true) {
                            if (isset($_POST['leaderTitle']) && strlen($_POST['leaderTitle']) > 0) {
                                $leaderTitle = trim($_POST['leaderTitle']);
                            } else {
                                $leaderTitle = "Chairman";
                            }
                            $nation = $loggedInUser->getVariable("nation");
                            $rolesArray = json_encode(
                                array(
                                    "$leaderTitle" => array(
                                        "occupant" => $loggedInUser->userID,
                                        "specialID" => 0,
                                        "perms" => array(
                                            "leader" => 1
                                        )
                                    )
                                ), JSON_PRETTY_PRINT);
                            $stmt = $db->prepare("INSERT INTO parties 
                        (nation, name, initialEcoPos, initialSocPos, ecoPos, socPos, partyRoles) VALUES (?,?,?,?,?,?,?)");
                            $stmt->bind_param("ssiiiis", $nation, $partyName, $ecoPosition, $socPosition, $ecoPosition, $socPosition, $rolesArray);
                            $stmt->execute();
                            $newPartyID = $stmt->insert_id;
                            $loggedInUser->leaveCurrentParty();
                            $loggedInUser->updateVariable("party", $newPartyID);
                            $loggedInUser->updateVariable("partyInfluence", 100);
                            $loggedInUser->addCampaignFinance(-50000);
                            redirect("party.php?id=$newPartyID");

                        }
                    }
                }
            } else {
                alert("Error!", "Party name already taken.");
            }

        }
    } else {
        alert("Error!", "Not enough monies. Get more.", "error");
    }

}



