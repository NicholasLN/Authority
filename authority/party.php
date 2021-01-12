<?php
include 'php/functions.php';

if (isset($_GET['id'])) {
    $partyID = numFilter($_GET['id']);
    if (getNumRows("SELECT * FROM parties WHERE id=$partyID") == 1) {
        $party = new Party($partyID);
        if (isset($_GET['mode'])) {
            $mode = $_GET['mode'];
            if ($mode != "members" && $mode != "partylegislature" && $mode != "controls" && $mode != "overview") {
                $mode = "overview";
            }
        } else {
            $mode = "overview";
        }
    } else {
        invalidPage();
    }
} else {
    invalidPage();
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
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.23/b-1.6.5/datatables.min.js"></script>
    <script src='https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js'></script>

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


                <?  // Join/Leave Party Button and PHP //
                    if($_SESSION['loggedIn'] == True){
                        // if they are within the same nation...
                        if($loggedInRow['nation'] == $party->partyRow['nation']){
                            echo "
                            <div style='margin-top: 8px' class='row justify-content-center'>
                                <div class='col-md-4'>
                                    <form method='post'>";
                            // if they are in the party
                            if($loggedInRow['party'] == $partyID){
                                echo "
                                        <input type='submit' class='btn btn-danger' name='leavePartySubmit' value='Leave Party (50% reduction in SI)'/>
                                ";
                            }
                            // if they have no party
                            if($loggedInRow['party'] == 0){
                                echo "
                                        <input type='submit' class='btn btn-primary' name='joinPartySubmit' value='Join Party'/>
                                ";
                            }
                            // if they are in a party, but it is not their own
                            if($loggedInRow['party'] != 0 && $partyID != $loggedInRow['party']){
                                echo "
                                        <input type='submit' class='btn btn-danger' name='defectPartySubmit' value='Defect (50% reduction in SI)'/>
                                ";
                            }
                            echo "
                                    </form>
                                </div>
                            </div>";
                        }
                    }



                ?>

                <hr/>
                <h3><? echo $party->partyRoles->partyLeaderTitle(); ?></h3>

                <?
                $leader = $party->getPartyLeader();

                $leaderPic = $leader->pictureArray()['picture'];
                $leaderName = $leader->pictureArray()['name'];
                $leaderID = $leader->pictureArray()['id'];

                echo
                "
                    <a href='politician.php?id=$leaderID'>
                        <img style='max-width:120px;max-height:120px; border:4px ridge yellow;' src='$leaderPic' alt='$leaderName Logo'>
                        <br/>
                        <span>$leaderName</span>
                    </a> 
                    <br/>
                    <hr/>
                    <h4>Party Roles</h4>
                ";
                // Party Role View
                partyRoles($partyID);
                //
                ?>

                <hr/>
                <div class="row justify-content-center">
                    <div class="col">
                        <a href="party.php?id=<? echo $partyID ?>&mode=members" class="btn btn-primary">Members</a>
                        <a href="party.php?id=<? echo $partyID ?>&mode=overview" class="btn btn-primary">Overview</a>
                        <?
                            if($party->getPartyDiscordCode() != "0"){
                                $code = $party->getPartyDiscordCode();
                                echo "<a class='btn btn-danger' href='https://discord.gg/$code' target='_BLANK'>Discord</a>";

                            }
                        ?>
                    </div>
                </div>
                <hr/>
                <?
                switch ($mode){
                    case ($mode == "members"):
                        partyMembersTable($partyID);
                        break;
                    case ($mode == "overview"):
                        partyOverview($partyID);
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
                "autoWidth": false

            });
        });
    </script>
    <? echoFooter() ?>
</div>
</html>
