<?php

function politicianMeta($user){
    $name = $user->getVariable('politicianName');
    $bio = $user->getVariable("bio");
    $bio = str_replace('"',"",$bio);
    $pic = "https://www.europeanperil.com/authority/".$user->getVariable("profilePic");
    echo <<<POLMETA
        <meta content="AUTHORITY | $name" property="og:title">
        <meta content="$bio <br>" property="og:description">
        <meta content='$pic' property='og:image'>
        <meta name="theme-color" content="#00000">
POLMETA;
}
