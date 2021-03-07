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

        if ($result -> num_rows == 1) {
            $this -> doesItExist = true;
            $this -> stateAbbr = $abbreviation;
            $this -> stateInfoArray = $result -> fetch_array(MYSQLI_ASSOC);
        } else {
            $this -> doesItExist = false;
        }
    }

    public function getDemographics(): array
    {
        global $db;

        $stmt = $db -> prepare("SELECT * FROM demographics WHERE State = ?");
        $stmt -> bind_param("s", $this -> stateAbbr);
        $stmt -> execute();

        $result = $stmt -> get_result();
        return $result -> fetch_all(MYSQLI_ASSOC);
    }

    public function getStatePlayers(): array
    {
        global $db;

        $stmt = $db -> prepare("SELECT * FROM users WHERE state = ?");
        $stmt -> bind_param("s", $this -> stateAbbr);
        $stmt -> execute();

        $result = $stmt -> get_result();
        return $result -> fetch_all(MYSQLI_ASSOC);
    }
}