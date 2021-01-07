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
    <title>Authority</title>
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
                <div class="party-container">
                    <?
                        $stmt = $db->prepare("SELECT * FROM parties WHERE nation=?");
                        $stmt->bind_param("s",$country);
                        $stmt->execute();
                        $array = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        foreach($array as $key){
                            $id = $key['id'];
                            $party = new Party($id);
                            $partyName = $party->partyRow['name'];
                            $members = $party->getPartyMembers();
                            $logo = $party->getPartyLogo();
                            $bio = $party->partyRow['partyBio'];

                            echo <<< PARTYHTML
                    <div class="row party-card">
                        <div class="col-sm-2" style="margin-right: 2%">
                            <img class="party-container-img" src="$logo" alt=""/>
                        </div>
                        <div class="col-sm">
                            <div>
                                <div class="col-12"><h4><a href="party?id=$id">$partyName</a></h4></div>
                                <div class="col-12">
                                    $bio
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="col-12" style="text-align: center">
                            <div class="row" style="width: 100%">
                                <div class="col-sm">
                                    <b>Members:</b> $members
                                </div>
                                <div class="col-sm">
                                    <b>Nationwide Approval:</b> 
                                </div>
                                <div class="col-sm">
                                    <b>Strength:</b> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
PARTYHTML;


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



