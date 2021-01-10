<?php
include 'php/functions.php';
if(isset($_GET['country'])){
    if(!verifyCountry($_GET['country'])){
        invalidPage();
    }
    else{
        $country = $_GET['country'];

        $defunct = False;
        if(isset($_GET['defunct']) && ($_GET['defunct'] == "true" || $_GET['defunct'] == "True")){
            $defunct = True;
        }

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
                <? if(!$defunct){
                    echo "
                    <b><i>Parties that are NOT defunct (more than 0 members)</i></b>
                    <br/>
                    <a class='btn btn-primary' style='margin-top:4px' href='politicalparties?country=$country&defunct=True'>Defunct Parties</a>
                    ";
                }
                else{
                    echo "
                    <b><i>Parties that ARE defunct (no active members)</i></b>
                    <br/>
                    <a class='btn btn-primary' style='margin-top:4px' href='politicalparties?country=$country&defunct=False'>Active Parties</a>
                    ";
                }
                ?>
                <hr/>
                <div class="row justify-content-center">
                <?
                    $query = "SELECT * FROM parties WHERE nation='$country'";
                    if($result = $db->query($query)){
                        while ($row = $result->fetch_assoc()) {

                            $partyID = $row['id'];
                            $party = new Party($partyID);
                            $logo = $party->getPartyLogo();
                            $name = $party->getPartyName();
                            $members = $party->getPartyMembers();
                            $bio = $party->getPartyBio();

                            $leader = $party->getPartyLeader();

                            // TODO: More programmatic way to echo invalid user details.
                            if($leader) {
                                $leaderPic = $leader->getVariable("profilePic");
                                $leaderName = $leader->getVariable("politicianName");
                                $leaderID = $leader->getVariable("id");
                            }
                            else{
                                $leaderPic = "images/userPics/default.jpg";
                                $leaderName = "No Leader";
                                $leaderID = 0;

                            }
                            if($members > 0 && !$defunct) {
                                echo "
                                <div class='col-sm-4'>
                                    <div class='card'>
                                        <div class='partyInfo'>
                                            <div class='partyImgContainer'>
                                                <img class='partyImgLogo' src='$logo' alt='$name Logo'>
                                            </div>
                                            <div class='partyNameContainer'>
                                                <a href='party?id=$partyID'>$name</a>
                                            </div>
                                        </div>
                                        <div class='card-body'>
                                            <span>Leader</span>
                                            <br/>
                                            <a href='politician?id=$leaderID'>
                                                <img class='leaderImg' src='$leaderPic' alt='$leaderName Logo'>
                                                <br/>
                                                <span>$leaderName</span>
                                            </a>
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
                            if($defunct && $members == 0){
                                echo "
                                <div class='col-sm-4'>
                                    <div class='card'>
                                        <div class='partyInfo'>
                                            <div class='partyImgContainer'>
                                                <img class='partyImgLogo' src='$logo' alt='$name Logo'>
                                            </div>
                                            <div class='partyNameContainer'>
                                                <a href='party?id=$partyID'>$name</a>
                                            </div>
                                        </div>
                                        <div class='card-body'>
                                            <span>Leader</span>
                                            <br/>
                                            <a href='politician?id=$leaderID'>
                                                <img class='leaderImg' src='$leaderPic' alt='$leaderName Logo'>
                                                <br/>
                                                <span>$leaderName</span>
                                            </a>
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



