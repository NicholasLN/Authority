<?php include 'php/functions.php';
$state = null;
$stateArray = null;
if (isset($_GET['state'])) {
    $state = new State($_GET['state']);

    if ($state -> doesItExist) {
        $stateArray = $state -> stateInfoArray;
    } else {
        invalidPage("Invalid State!", "Invalid State asshole.");
    }
} else {
    invalidPage("Invalid State!", "You forgot to put in a State, dumbass");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Authority</title>
    <? echoHeader(); ?>
    <style>
        tr {
            width: 100%;
            display: inline-table;
            table-layout: fixed;
        }

        table thead {
            height: 300px;
            display: -moz-groupbox;
            table-layout: fixed;
        }

        tbody {
            flex: 1 1 auto;
            display: block;
            height: 250px;
            overflow-y: scroll;
        }
        table thead tbody td tr {
            border-style: none !important;
        }

         td {
            padding: 10px !important;
        }
    </style>
</head>
<? echoNavBar() ?>
<body>
<div class="main">
    <div class="gameContainer">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-10">
                <h4 style="padding-top: 16px"><i>The State of</i></h4>
                <h1><b><?= $stateArray['name'] ?></b></h1>
                <img width="200" src="/images/flags/United%20States/<?= $stateArray['flag'] ?>" alt="State flag"/>
                <hr>
                <a href="demographics.php?state=<?= $state -> stateAbbr?>" class="btn btn-primary ">Demographics</a>
                <div class="col-sm-4">
                <table class="table table-striped table-bordered table-sm">
                    <thead>
                        <tr>
                            <th class="th-sm" style="text-align: left;">Name</th>
                            <th class="th-sm" style="text-align: left;">Party</th>
                            <th class="th-sm" style="text-align: left;">State Influence</th>
                        </tr>
                    </thead>
                        <? $playerList = $state -> getStatePlayers(); ?>
                    <tbody>
                    <?
                    foreach ($playerList as $player)
                    {?>
                    <tr>
                        <td style="border-style: none;">
                            <a href="politician.php?id=<?=$player['id']?>"><?=$player['politicianName']?></a>
                        </td>

                        <td style="text-align: left; border-style: none;">
                            <? $party = getPartyFromId($player['party']) ?>
                            <a href="party.php?id=<?=$party['id']?>"><?=$party['name']?></a>
                        </td>

                        <td style="text-align: left; border-style: none;">
                            <?= $player['hsi'] ?>%
                        </td>
                    </tr>
                    <?}?>
                    </tbody>
                </table>
                </div>
            </div>
            <div class="col-sm"></div>
        </div>
    </div>
    <? echoFooter() ?>
</div>
</html>