<?php
include 'php/functions.php';

if (isset($_GET['id'])) {
    $id = numFilter($_GET['id']);
    if(getNumRows("SELECT * FROM parties WHERE id=$id") == 1) {
        $party = new Party($id);
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
                <img style="max-width:200px;max-height:200px;" src="<? echo $party->getPartyLogo()?>" alt="<? echo $party->getPartyName() ?> Logo"/>
                <hr/>
                <? echo $party->partyRoles->partyLeaderTitle(); ?>
            </div>
            <div class="col-sm"></div>
        </div>
    </div>
    <? echoFooter() ?>
</div>
</html>
