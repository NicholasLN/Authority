<?php include 'php/functions.php';
$state = null;
$stateArray = null;
if (isset($_GET['state'])) {

    $state = new State($_GET['state']);
    $country = $state->getCountry();

    if ($state->doesItExist) {
        $stateArray = $state->stateInfoArray;
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
                <img width="200" src="images/flags/<?= $country ?>/<?= $stateArray['flag'] ?>" alt="State flag"/>
                <hr>
                <a href="demographics.php?state=<?= $state->stateAbbr ?>" class="btn btn-primary ">Demographics</a>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th class="th-sm" style="text-align: left;">Name</th>
                        <th class="th-sm" style="text-align: left;">Party</th>
                        <th class="th-sm" style="text-align: left;">State Influence</th>
                    </tr>
                    </thead>
                    <? $playerList = $state->getStatePlayers(); ?>
                    <tbody>
                    <?
                    foreach ($playerList as $player) {
                        ?>
                        <tr>
                            <td style="border-style: none;">
                                <a href="politician.php?id=<?= $player['id'] ?>"><?= $player['politicianName'] ?></a>
                            </td>

                            <td style="text-align: left; border-style: none;">
                                <?
                                $party = new Party($player['party']);
                                $partyInformation = $party->pictureArray();
                                if ($partyInformation['name'] != "Independent"){
                                ?>
                                <a href="party.php?id=<?= $partyInformation['id'] ?>">
                                    <?
                                    }
                                    ?>
                                    <img style="max-width:30px;" src='<?= $partyInformation['picture'] ?>'
                                         alt='<?= $partyInformation['name'] ?> Picture'/><?= $partyInformation['name'] ?>
                                </a>
                                <?
                                if ($partyInformation['name'] != "Independent") {
                                    echo "</a>";
                                }
                                ?>
                            </td>

                            <td style="text-align: left; border-style: none;">
                                <?= $player['hsi'] ?>%
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>

            <div class="col-sm"></div>
        </div>
    </div>
    <? echoFooter() ?>
</div>
</html>