<?php include 'php/functions.php';
if (isset($_GET['state'])) {
    $state = $_GET['state'];
    $state = new State($_GET['state']);

    if ($state -> doesItExist) {
        if(!isset($_GET['gender'])){ $_GET['gender'] = "all"; }
        if(!isset($_GET['race'])){ $_GET['race'] = "all"; }

        $gender = $_GET['gender'];
        $race = $_GET['race'];


        if(Demographic::validGender($gender) && Demographic::validRace($race)){
            $stateDemographics = $state -> getDemographics($gender,$race);
        }
        else{
            if(Demographic::validGender($gender) && !Demographic::validRace($race)){
                $stateDemographics = $state->getDemographics($gender,"all");
            }
            else if(!Demographic::validGender($gender) && Demographic::validRace($race)){
                $stateDemographics = $state->getDemographics("all",$race);
            }
            else{
                $stateDemographics = $state->getDemographics();
            }
        }
    } else {
        invalidPage("Invalid State!", "State does not exist. Fuck off");
    }
} else {
    invalidPage("Invalid State!","Put in a state, dummy!");
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
            <div class="col-sm-8">
                <h1 style="padding-top: 16px"><?=$state->stateInfoArray['name'] ?> Demographics</h1>
                <a href="state.php?state=<?= $state -> stateAbbr?>" class="btn btn-primary ">State Politics</a>
                <hr>
                <table class="table table-striped">
                    <tr>
                        <td><b class="bold">Demographic Race</b></td>
                        <td><b class="bold">Demographic Gender</b></td>
                        <td><b class="bold">Demographic Population</b></td>
                        <td><b class="bold">Demographic Social Mean</b></td>
                        <td><b class="bold">Demographic Economic Mean</b></td>
                    </tr>
                    <?
                    foreach($stateDemographics as $demographic) {
                        ?>
                        <tr>
                            <td>
                                <?= $demographic['Race']; ?>
                            </td>
                            <td>
                                <?= $demographic['Gender']; ?>
                            </td>
                            <td>
                                <?= number_format($demographic['Population']); ?>
                            </td>
                            <td>
                                <?= socPositionString($demographic['SocPosMean']); ?>
                            </td>
                            <td>
                                <? ecoPositionString($demographic['EcoPosMean']); ?>
                            </td>
                        </tr>
                        <?
                    }

                    ?>
                </table>
            </div>
            <div class="col-sm"></div>
        </div>
    </div>
    <? echoFooter() ?>
</div>
</html>
