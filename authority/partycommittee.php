<?php
include 'php/functions.php';
if (isset($_GET['id'])) {
    $partyID = numFilter($_GET['id']);
    if (getNumRows("SELECT * FROM parties WHERE id=$partyID") == 1) {
        $party = new Party($partyID);
        if (isset($_GET['mode'])) {
            $mode = $_GET['mode'];
            if ($mode != "votes" && $mode != "proposeVote") {
                $mode = "votes";
            }
        } else {
            $mode = "votes";
        }
    } else {
        invalidPage("Not a party", "Invalid party page.");
    }
} else {
    invalidPage("Not a party.", "Invalid party page.");
}
if ($mode == "proposeVote") {
    if (isset($loggedInUser)) {
        if ($loggedInUser->getVariable('party') != $partyID) {
            invalidPage();
        }
    } else {
        invalidPage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Authority</title>
    <? echoHeader(); ?>

    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/dt-1.10.23/b-1.6.5/datatables.min.css"/>
    <link rel="stylesheet" href="css/party.css?id=6"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.23/r-2.2.7/datatables.min.js"></script>
    <script src='https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js'></script>
    <script src='https://cdn.datatables.net/plug-ins/1.10.22/sorting/natural.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-shorten@0.3.2/dist/jquery-shorten.min.js"></script>

</head>
<? echoNavBar() ?>
<body>
<div class="main">
    <div class="gameContainer">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-10">
                <br/>
                <a href="party.php?id=<? echo $partyID ?>">
                    <?
                    $image = $party->pictureArray()['picture'];
                    $name = $party->pictureArray()['name'];
                    ?>
                    <img style="max-width:150px;max-height:150px;" src="<? echo $image ?>"/>
                    <h5><? echo $name ?></h5>
                </a>
                <h3>Committee</h3>
                <hr/>
                <a href="partycommittee.php?id=<? echo $partyID ?>&mode=votes" class="btn btn-primary">Active Votes</a>
                <?
                if (isset($loggedInUser) && $loggedInUser->getVariable("party") == $partyID) {
                    echo
                    "<a href='partycommittee.php?id=$partyID&mode=proposeVote' class='btn btn-primary'>Propose Vote</a>";
                }
                echo "<hr/>";
                if (isset($mode) && $mode == "votes") {
                    partyVotesTableView($partyID);
                }
                if (isset($mode) && $mode == "proposeVote") {
                    proposePartyVoteView($partyID);
                }
                ?>
                <hr/>
            </div>
            <div class="col-sm"></div>
        </div>
    </div>
    <? echoFooter() ?>
</div>
</html>
<?php
if (isset($_POST['proposeVoteSubmit'])) {
    var_dump($_POST);
    $voteActionArray = array();
    if (isset($loggedInUser)) {
        $voteName = $_POST['partyVoteName'];
        $voteType = $_POST['proposePartyVoteType'];
        switch ($voteType) {
            case $voteType == "New Chair":
                if (isset($_POST['newChairSearch'])) {
                    $newChair = new User(numFilter($_POST['newChairSearch']));
                    if ($newChair->isUser && $newChair->getVariable("party") == $partyID) {
                        $newArr = array("actionType" => "New Chair", "newChair" => $newChair->userID);
                        array_push($voteActionArray, $newArr);
                    } else {
                        alert("Error!", "They do not exist, or they are not in your party.");
                    }
                }
                break;
            case $voteType == "Rename Party":
                $renamePartyTo = strip_tags(trim($_POST['renamePartyTo']));
                if (strlen($renamePartyTo) > 5) {
                    $oldName = $party->getPartyName();
                    $newArr = array("actionType" => "Rename Party", "renameTo" => $renamePartyTo, "oldName" => $oldName);
                    array_push($voteActionArray, $newArr);
                } else {
                    alert("Error", "New name is too short.");
                }
                break;
            case($voteType == "Change Fees"):
                $fees = numFilter($_POST['changeFeesTo']);
                if (isset($_POST['changeFeesTo']) && $fees >= 0 && $fees <= 100) {
                    $newArr = array("actionType" => "Change Fees", "newFees" => $fees);
                    array_push($voteActionArray, $newArr);
                }
                break;
            case($voteType == "Change Number of Party Votes"):
                if (isset($_POST['changePartyVotesTo'])) {
                    $votes = numFilter($_POST['changePartyVotesTo']);
                    if ($votes >= 5 && $votes <= 1000) {
                        $newArr = array("actionType" => "Change Number of Party Votes", "oldVotes" => $party->getVariable("votes"), "votes" => $votes);
                        array_push($voteActionArray, $newArr);
                    }
                }
                break;
            case($voteType == "Grant Permission"):
                $grantPermission = $_POST['grantPermissionSelect'];
                $roleID = $_POST['grantPermissionRoleSelect'];
                if ($roleID != 0) {
                    if ($party->partyRoles->isRole($roleID)) {
                        $isPermission = 0;
                        foreach (roleOptions("yes") as $key => $value) {
                            if ($grantPermission == $value) {
                                $isPermission = 1;
                            }
                        }
                        if ($isPermission == 1) {
                            $newArr =
                                array(
                                    "actionType" => "Grant Permission",
                                    "roleID" => $roleID,
                                    "roleName" => $party->partyRoles->getRoleName($roleID),
                                    "permission" => $grantPermission
                                );
                            array_push($voteActionArray, $newArr);
                        }
                    } else {
                        alert("Error!", "Not a role. Stop spoofin, yee yee ass mfer. I told a mod.");
                    }
                }
                break;
            case($voteType == "Remove Permission"):
                $removePermission = $_POST['removePermissionSelect'];
                $roleID = $_POST['removePermissionRoleSelect'];
                if ($roleID != 0) {
                    if ($party->partyRoles->isRole($roleID)) {
                        $isPermission = 0;
                        foreach (roleOptions("yes") as $key => $value) {
                            if ($removePermission == $value) {
                                $isPermission = 1;
                            }
                        }
                        if ($isPermission == 1) {
                            $newArr =
                                array(
                                    "actionType" => "Remove Permission",
                                    "roleID" => $roleID,
                                    "roleName" => $party->partyRoles->getRoleName($roleID),
                                    "permission" => $removePermission
                                );
                            array_push($voteActionArray, $newArr);
                        }
                    } else {
                        alert("Error!", "Not a role. Stop spoofin, yee yee ass mfer. I told a mod.");
                    }
                }
                break;
            case($voteType == "Delete Role"):
                $roleID = $_POST['deleteRoleSelect'];
                if ($roleID != 0) {
                    if ($party->partyRoles->isRole($roleID)) {
                        $newArr = array("actionType" => "Delete Role", "roleID" => $roleID, "roleName" => $party->partyRoles->getRoleName($roleID));
                        array_push($voteActionArray, $newArr);
                    }
                }
                break;
            case($voteType == "Rename Role"):
                $roleID = $_POST['renameRoleSelect'];
                $renameTo = strip_tags(trim($_POST['renameRoleTo']));
                if (strlen($renameTo) > 0) {
                    if ($party->partyRoles->isRole($roleID)) {
                        $newArr = array(
                            "actionType" => "Rename Role",
                            "roleToRename" => $party->partyRoles->getRoleName($roleID),
                            "roleID" => $roleID,
                            "renameTo" => $renameTo
                        );
                        array_push($voteActionArray, $newArr);
                    }
                }
                break;
            case($voteType == "Change Role Occupant"):
                if (isset($_POST['changeOccupantSearch'])) {
                    $newOccupant = new User(numFilter($_POST['changeOccupantSearch']));
                    if (isset($_POST['changeOccupantSelect'])) {
                        $roleID = numFilter($_POST['changeOccupantSelect']);
                        if ($newOccupant->isUser && $newOccupant->getVariable("party") == $partyID) {
                            $newArr = array(
                                "actionType" => "Change Role Occupant",
                                "roleName" => $party->partyRoles->getRoleName($roleID),
                                "roleID" => $roleID,
                                "newUser" => $newOccupant->getVariable("id")
                            );
                            array_push($voteActionArray, $newArr);
                        } else {
                            alert("Error!", "They are either not in your party or do not exist.");
                        }
                    }
                }
                break;
        }
        if (sizeof($voteActionArray) > 0) {
            if (getNumRows("SELECT * FROM partyVotes WHERE passed=0 AND author=$loggedInID") < 1) {
                if ($loggedInUser->getVariable("party") == $partyID) {
                    $voteActionArray = json_encode($voteActionArray, JSON_FORCE_OBJECT);
                    $expiresAt = time() + (60 * 60 * 24);
                    $stmt = $db->prepare("INSERT INTO partyVotes (author,party,name,actions,expiresAt) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("iissi", $loggedInID, $partyID, $voteName, $voteActionArray, $expiresAt);
                    $stmt->execute();
                }
            } else {
                alert("Error!", "You already have an ongoing vote. Wait!");
            }
        } else {
            alert("Error!", "No action!");
        }
    }
}