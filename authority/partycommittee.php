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
                ?>
                <hr/>
            </div>
            <div class="col-sm"></div>
        </div>
    </div>
    <? echoFooter() ?>
</div>
</html>
