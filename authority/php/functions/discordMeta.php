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
function indexMeta(){
    echo <<<POLMETA
        <meta content="AUTHORITY 3.0" property="og:title">
        <meta content="
        Authority is an indev. political game in which users can register as a politician, run for offices, run countries, play a vital part in the economic system within their countries (and others), and seize power through a variety of methods--legal, or illegal." 
        property="og:description">
        <meta content='https://www.europeanperil.com/authority/images/AuthorityLogoV3.png' property='og:image'>
        <meta name="theme-color" content="#00000">
POLMETA;

}
