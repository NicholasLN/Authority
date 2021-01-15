<?php
include 'php/functions.php';
$profileIsLoggedInUser = false;
if (isset($_GET['id'])) {
    $id = numFilter($_GET['id']);
    if (getNumRows("SELECT * FROM users WHERE id=$id") == 1) {

        $profileUser = new User($id);
        $profileRow = $profileUser->getUserRow();

        if (isset($loggedInID) && ($id == $loggedInID)) {
            $profileIsLoggedInUser = true;
        }
    } else {
        invalidPage("Invalid Page","User does not exist.");
    }
} else {
    invalidPage("Invalid Page","User does not exist.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <? politicianMeta($profileUser); ?>
    <title><? echo $profileRow['politicianName'] ?> | AUTHORITY 3.0</title>
    <? echoHeader(); ?>
    <link href="css/profile.css?id=12" rel="stylesheet"/>
</head>
<? echoNavBar() ?>
<body>
<div class="main">
    <div class="gameContainer">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-7">
                <br/>
                <h2><?php echo $profileRow['politicianName'] ?></h2>
                <div class="mainProfileContainer">
                    <img class='profilePicture' src="<? echo $profileRow['profilePic'] ?>" alt="Profile Picture"/>
                    <br/>
                    <?
                    if (!$profileIsLoggedInUser) {
                        $str = lastOnlineString($profileRow['lastOnline']);
                        if ($str == "Online Now") {
                            echo "<div class='lastOnline' style='color:#0b910b'>$str</div>";
                        } else {
                            echo "<div class='lastOnline'>$str</div>";
                        }
                    }
                    ?>
                    <hr/>
                    <pre class="bioBox"><? echo $profileRow['bio'] ?></pre>
                    <hr/>
                </div>
                <table class="table table-striped table-bordered" id="statsTable">
                    <tr>
                        <td><b>Authority</b></td>
                        <td><?php echo $profileRow['authority'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Campaign Funding</b></td>
                        <td><? echo "$<span class='greenFont'>" . number_format($profileRow['campaignFinance']) . "</span>" ?></td>

                    </tr>
                    <tr>
                        <td><b>State Influence</b></td>
                        <td><?php echo $profileRow['hsi'] ?>%</td>
                    </tr>
                    <tr>
                        <td><b>Party</b></td>
                        <td>
                            <?
                            $party = new Party($profileUser->getUserRow()['party']);
                            $partyPic = $party->pictureArray(true)['picture'];
                            $partyName = $party->pictureArray()['name'];
                            $partyID = $party->pictureArray()['id'];

                            echo "
                                <a href='party.php?id=$partyID'><img class='profilePartyPic' src='$partyPic' alt='$partyName Picture'/>$partyName</a>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Region</b></td>
                        <td>
                            <?
                            $abbreviation = $profileRow['state'];
                            $state = getStateByAbbreviation($abbreviation);
                            $flag = $state['flag'];
                            $country = $state['country'];
                            $name = $state['name'];

                            echo "
                                <a href='state.php?state=$abbreviation'><img class='profileStateFlag' src='images/flags/$country/$flag' alt='$name'/>$name</a>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Economic Positions</b></td>
                        <td>
                            <?
                            $ecoPos = $profileRow['ecoPos'];
                            $rgb = getPositionFontColor($ecoPos);
                            $pos = getEcoPositionName($ecoPos);
                            echo "<span style='color:$rgb'><b>$pos ($ecoPos)</b></span>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Social Positions</b></td>
                        <td>
                            <?
                            $socPos = $profileRow['socPos'];
                            $rgb = getPositionFontColor($socPos);
                            $pos = getSocPositionName($socPos);
                            echo "<span style='color:$rgb'><b>$pos ($socPos)</b></span>";
                            ?>
                        </td>
                    </tr>
                </table>
                <br/>
                <br/>
                <br/>
            </div>
            <div class="col-sm"></div>
        </div>
    </div>
    <? echoFooter() ?>
</div>
</body>
</html>


