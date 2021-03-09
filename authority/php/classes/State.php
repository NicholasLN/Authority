<?php

class State
{
    public bool $doesItExist;
    public String $stateAbbr;
    public array $stateInfoArray;

    public function __construct(String $abbreviation)
    {
        global $db;

        $stmt = $db -> prepare("SELECT * FROM states WHERE abbreviation = ?");
        $stmt -> bind_param("s", $abbreviation);
        $stmt -> execute();

        $result = $stmt -> get_result();

        if ($result->num_rows == 1) {
            $this->doesItExist = true;
            $this->stateAbbr = $abbreviation;
            $this->stateInfoArray = $result->fetch_array(MYSQLI_ASSOC);
        } else {
            $this->doesItExist = false;
        }
    }

    public function getCountry(): string
    {
        global $db;
        $stmt = $db->prepare("SELECT country FROM states WHERE abbreviation = ?");
        $stmt->bind_param("s", $this->stateAbbr);

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_array()['country'];

    }

    public function getDemographics(String $gender = "all", String $race = "all"): array
    {
        global $db;

        if($gender=="all" && $race=="all"){
            $stmt = $db->prepare("SELECT * FROM demographics WHERE State = ?");
            $stmt->bind_param("s",$this->stateAbbr);
        }
        else if($gender!="all"){
            if($race == "all"){
                $stmt = $db->prepare("SELECT * FROM demographics WHERE Gender=? AND State=?");
                $stmt -> bind_param("ss",$gender,$this->stateAbbr);
            }
            else{
                $stmt = $db->prepare("SELECT * FROM demographics WHERE Gender=? AND Race=? AND State=?");
                $stmt -> bind_param("sss",$gender,$race,$this->stateAbbr);
            }
        }
        else if($race != "all"){
            if($gender == "all"){
                $stmt = $db->prepare("SELECT * FROM demographics WHERE Race=? AND State=?");
                $stmt -> bind_param("ss",$race,$this->stateAbbr);
            }
            else{
                $stmt = $db->prepare("SELECT * FROM demographics WHERE Race=? AND Gender = ? AND State=?");
                $stmt -> bind_param("sss",$race,$gender,$this->stateAbbr);
            }
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    }

    public function getStatePlayers(bool $active = True, bool $sortByInfluence = True): array
    {
        global $db;
        // for hiding inactives
        global $onlineThreshold;

        $stmt = $db->prepare("SELECT * FROM users WHERE state = ? AND lastOnline > ?");
        $stmt->bind_param("si", $this->stateAbbr, $onlineThreshold);
        $stmt->execute();

        $result = $stmt->get_result();
        $array = $result->fetch_all(MYSQLI_ASSOC);
        if ($sortByInfluence) {
            usort($array, function ($item1, $item2) {
                return $item2['hsi'] <=> $item1['hsi'];
            });
        }
        return $array;
    }
}