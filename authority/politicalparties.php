<?php
include 'php/functions.php';
if(isset($_GET['country'])){
    if(!verifyCountry($_GET['country'])){
        invalidPage();
    }
    else{
        $country = $_GET['country'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Parties | AUTHORITY</title>
    <? echoHeader(); ?>
    <link rel="stylesheet" href="css/partylist.css"/>
</head>
<? echoNavBar() ?>
<body>
<div class="main">
    <div class="gameContainer">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-8">
                <br/>
                <h2>Political Parties</h2>
                <hr/>
                <div class="row justify-content-center">
                <?
                    $query = "SELECT * FROM parties WHERE nation='$country'";
                    if($result = $db->query($query)){
                        while ($row = $result->fetch_assoc()) {

                            $party = new Party($row['id']);
                            $logo = $party->getPartyLogo();
                            $name = $party->getPartyName();
                            $members = $party->getPartyMembers();
                            $bio = $party->getPartyBio();

                            $leader = $party->getPartyLeader();
                            $leaderPic = $leader->getVariable("profilePic");
                            $leaderName = $leader->getVariable("politicianName");
                            $leaderID = $leader->getVariable("id");

                            echo "
                            <div class='col-sm-4'>
                                <div class='card'>
                                    <div class='partyInfo'>
                                        <div class='partyImgContainer'>
                                            <img class='partyImgLogo' src='$logo' alt='$name Logo'>
                                        </div>
                                        <div class='partyNameContainer'>
                                            <p>$name</p>
                                        </div>
                                    </div>
                                    <div class='card-body'>
                                        <span>Leader</span>
                                        <br/>
                                        <img class='leaderImg' src='$leaderPic' alt='$leaderName Logo'>
        
                                        <hr/>
                                        <p class='partyBioContainer'>
                                        $bio
                                        </p>
                                        <hr/>
                                        <span><b>Members: $members</b></span>
                                    </div>
                                </div>                        
                            </div>
                            ";
                        }
                    }
                ?>
                </div>
            </div>
            <div class="col-sm"></div>
        </div>
    </div>
    <? echoFooter() ?>
</div>
</html>



