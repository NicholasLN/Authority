<?php
include 'php/functions.php';

if (isset($_GET['id'])) {
    $partyID = numFilter($_GET['id']);
    if (getNumRows("SELECT * FROM parties WHERE id=$partyID") == 1) {
        $party = new Party($partyID);
        if (isset($_GET['mode'])) {
            $mode = $_GET['mode'];
            if ($mode != "members" && $mode != "partylegislature" && $mode != "partyControls" && $mode != "overview") {
                $mode = "overview";
            }
        } else {
            $mode = "overview";
        }
    } else {
        invalidPage("Not a party", "Invalid party page.");
    }
} else {
    invalidPage("Not a party.", "Invalid party page.");
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title><? echo $party->getPartyName() ?> | AUTHORITY</title>
        <? echoHeader(); ?>
        <link rel="stylesheet" type="text/css"
              href="https://cdn.datatables.net/v/bs4/dt-1.10.23/b-1.6.5/datatables.min.css"/>
        <link rel="stylesheet" href="css/party.css?id=5"/>
        <script type="text/javascript"
                src="https://cdn.datatables.net/v/bs4/dt-1.10.23/b-1.6.5/datatables.min.js"></script>
        <script src='https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js'></script>
        <script src='https://cdn.datatables.net/plug-ins/1.10.22/sorting/natural.js'></script>

    </head>
    <? echoNavBar() ?>
    <body>
    <div class="main">
        <div class="gameContainer">
            <div class="row">
                <div class="col-sm"></div>
                <div class="col-sm-8">
                    <br/>
                    <h2><? echo $party->getPartyName() ?> </h2>
                    <img style="max-width:150px;max-height:150px;" src="<? echo $party->getPartyLogo() ?>"
                         alt="<? echo $party->getPartyName() ?> Logo"/>


                    <? // Join/Leave Party Button and PHP //
                    // TODO: Confirmation window for leaving or defecting.
                    if ($_SESSION['loggedIn'] == True) {
                        // if they are within the same nation...
                        if ($loggedInRow['nation'] == $party->partyRow['nation']) {
                            echo "
                            <div style='margin-top: 8px' class='row justify-content-center'>
                                <div class='col-md-4'>
                                    ";
                            // if they are in the party
                            if ($loggedInRow['party'] == $partyID) {
                                echo "<button class='btn btn-danger' onClick='leaveConfirm()'>Leave Party (Lose 50% HSI)</button>";

                            }
                            // if they have no party
                            if ($loggedInRow['party'] == 0) {
                                ?>
                                <form method='post'>
                                    <input type='submit' class='btn btn-primary' name='joinPartySubmit' value='Join Party'/>
                                </form>
                                <?
                            }
                            // if they are in a party, but it is not their own
                            if ($loggedInRow['party'] != 0 && $partyID != $loggedInRow['party']) {
                                echo "<button class='btn btn-danger' onClick='defectConfirm()'>Defect (Lose 50% HSI)</button>";
                            }
                            echo "
                                </div>
                            </div>";
                        }
                    }


                    ?>

                    <hr/>
                    <div class="row justify-content-center">
                        <div class="col">
                            <a href="party.php?id=<? echo $partyID ?>&mode=members#members" class="btn btn-primary">Members</a>
                            <a href="party.php?id=<? echo $partyID ?>&mode=overview"
                               class="btn btn-primary">Overview</a>
                            <?
                            if(isset($loggedInID) && $loggedInID == $party->partyRoles->partyLeaderID()){
                                echo "<a style='margin-right: 3px' class='btn btn-primary' href='party.php?id=$partyID&mode=partyControls'>Management</a>";
                            }
                            if ($party->getPartyDiscordCode() != "0") {
                                $code = $party->getPartyDiscordCode();
                                echo "<a class='btn btn-danger' href='https://discord.gg/$code' target='_BLANK'>Discord</a>";

                            }
                            ?>
                        </div>
                    </div>
                    <hr/>
                    <?
                    switch ($mode) {
                        case ($mode == "members"):
                            partyMembersTable($partyID);
                            break;
                        case ($mode == "overview"):
                            partyOverview($partyID);
                            break;
                        case ($mode=="partyControls"):
                            partyControls($partyID);
                            break;
                    }
                    ?>
                    <br/>
                    <br/>
                    <br/>
                </div>
                <div class="col-sm"></div>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $('#members').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "order": [[3, "desc"]],
                    columnDefs: [
                        {type: 'natural', targets: 0}
                    ]

                });
            });
            function defectConfirm(){
                Swal.fire({
                    showCancelButton: false,
                    showConfirmButton: false,
                    icon:"warning",
                    title:"Are you sure?",
                    html:"You will lose 50% of your Regional Influence and any positions in the prior party." +
                    "<br><br>" +
                    "<form method='post'>"+
                    "<input type='submit' class='btn btn-danger' name='defectPartySubmit' value='Defect (Lose 50% HSI)'/>"+
                    "</form>"
                })
            }
            function leaveConfirm(){
                Swal.fire({
                    showCancelButton: false,
                    showConfirmButton: false,
                    icon:"warning",
                    title:"Are you sure?",
                    html:"You will lose 50% of your Regional Influence and any positions in the party." +
                        "<br><br>" +
                        "<form method='post'>"+
                        "<input type='submit' class='btn btn-danger' name='leavePartySubmit' value='Leave Party (Lose 50% HSI)'/>"+
                        "</form>"
                })
            }
        </script>
        <? echoFooter() ?>
    </div>
    </html>

<?php
echo getcwd();
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == True) {
    if (isset($_POST['joinPartySubmit'])) {
        // validate that user isn't already in a party in post
        if ($loggedInRow['party'] == 0) {
            $loggedInUser->updateVariable("party", $partyID);
            redirect("party.php?id=$partyID");
        }
    }
    if (isset($_POST['leavePartySubmit'])) {
        // validate that user is actually in the party
        if ($loggedInRow['party'] == $partyID) {

            $loggedInUser->leaveCurrentParty();
            redirect("party.php?id=$partyID");

        }
    }
    if (isset($_POST['defectPartySubmit'])) {
        if ($partyID != $loggedInRow['party'] && $loggedInRow['party'] != 0) {

            $loggedInUser->leaveCurrentParty();
            $loggedInUser->updateVariable("party",$partyID);
            redirect("party.php?id=$partyID");

        }
    }
    if (isset($_POST['claimLeaderSubmit'])) {
        if ($loggedInRow['party'] == $partyID && $leaderID == 0) {
            $party->partyRoles->changeLeader($loggedInID);
            $party->partyRoles->updateRoles();
            redirect("party.php?id=$partyID");

        }
    }

    //TODO: Turn this into an Ajax form request instead of a PHP Submit.

    if (isset($_POST['voteFor'])) {
        if (isset($_POST['voteForID']) && $_POST['voteForID'] != 0) {
            $votingFor = new User(numFilter($_POST['voteForID']));
            if ($votingFor->getVariable("party") == $loggedInRow['party']) {
                $loggedInUser->updateVariable("partyVotingFor", $votingFor->userID);
                redirect("party.php?id=$partyID&mode=members");
            } else {
                alert("Error", "They are not in your party.");
            }
        }
    }
}





