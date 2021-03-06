<?php

class State
{
    public bool $doesItExist;
    public String $stateName;

    public function __construct(String $name)
    {
        $this -> stateName = $name;

        global $db;

        // TODO: Change demographic State column to use abbreviations instead.
        $stmt = $db -> prepare("SELECT * FROM states WHERE name = ?");
        $stmt -> bind_param("s", $name);
        $stmt -> execute();

        if ($stmt -> get_result() -> num_rows == 1) {
            $this -> doesItExist = true;
            $this -> stateName = $name;
        } else {
            $this -> doesItExist = false;
        }
    }

    public function getDemographics() {
        global $db;

        $stmt = $db -> prepare("SELECT * FROM demographics WHERE State = ?");
        $stmt -> bind_param("s", $this -> stateName);
        $stmt -> execute();

        $result = $stmt -> get_result();
        $demographicArray = $result -> fetch_all(MYSQLI_ASSOC);

        usort($demographicArray, function ($item1, $item2) {
            return $item2['Population'] <=> $item1['Population'];
        });

        return $demographicArray;
    }
}