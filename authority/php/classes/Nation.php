<?php

class Nation
{
    public $nationID;
    public $nationName;
    public $nationExists;
    private $nationRow;

    public function __construct($nationName)
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM countries WHERE name=?");
        $stmt->bind_param("s", $nationName);
        $stmt->execute();
        $results = $stmt->get_result();
        if ($results->num_rows >= 1) {
            $this->nationExists = true;
            $this->nationRow = $results->fetch_assoc();
            $this->nationName = $nationName;
            $this->nationID = $this->nationRow['id'];
        } else {
            $this->nationExists = false;
            $this->nationName = "Invalid Country";
            $this->nationID = -1;
        }
    }

    public function getPartiesArray()
    {
        global $db;
        $query = "SELECT * FROM parties WHERE nation=?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $this->nationName);
        $stmt->execute();
        $result = $stmt->get_result();

        $partyArray = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($partyArray as &$party) {
            $partyClass = new Party($party['id']);
            $members = array("members" => $partyClass->getPartyMembers());
            $party += $members;
        }
        usort($partyArray, function ($item1, $item2) {
            return $item2['members'] <=> $item1['members'];
        });
        return $partyArray;


    }
}