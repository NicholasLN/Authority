<?php

class Party
{
    protected $partyID;
    public $partyRoles;
    public $partyRow;

    public function getPartyLeader(): ?User
    {;
        $leader = $this->partyRoles->partyLeaderArray();
        if($leader['occupant'] != 0){
            return new User($leader['occupant']);
        }
        else{
            return null;
        }
    }
    public function __construct($partyID)
    {
        $this->partyID = $partyID;
        $this->partyRow = $this->getPartyRowConstructor($partyID);
        $this->partyRoles = new PartyRoles($this->partyRow['partyRoles']);
    }
    public function getPartyRowConstructor($partyID): ?array
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM parties WHERE id=?");
        $stmt->bind_param("i", $partyID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function updatePartyRow(){
        global $db;
        $id = $this->partyID;
        $this->partyRow = $this->getPartyRowConstructor($this->partyID);
    }
    public function getPartyName()
    {
        return $this->partyRow['name'];
    }
    public function getPartyBio(){
        return $this->partyRow['partyBio'];
    }
    public function getPartyLogo()
    {
        global $db;
        return $this->partyRow['partyPic'];
    }
    public function getPartyDiscordCode(){
        return $this->partyRow['discord'];
    }
    public function getPartyMembers()
    {
        global $db;
        $partyID = $this->partyID;
        /// 72 hours
        $onlineThreshold = time() - 259200;
        ///

        $stmt = $db->prepare("SELECT * FROM users WHERE party=? AND lastOnline > ?");
        $stmt->bind_param("ii", $partyID, $onlineThreshold);
        $stmt->execute();
        return $stmt->get_result()->num_rows;
    }
}

