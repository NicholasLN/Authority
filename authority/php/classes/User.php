<?php

/**
 * Class User
 */
class User
{
    public $userID;
    public $isUser;

    public function __construct(int $userID)
    {
        global $db;
        $this->userID = $userID;


        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i",$userID);
        $stmt->execute();
        if($stmt->get_result()->num_rows == 1){
            $this->isUser = true;
        }
        else{
            $this->isUser = false;
        }

    }

    public static function withUsername(string $username): User
    {
        global $db;

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $id = $stmt->get_result()->fetch_assoc()['id'];
        return new self($id);
    }

    public static function withPoliticianName(string $politicianName): User
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM users WHERE politicianName = ?");
        $stmt->bind_param("s", $politicianName);
        $stmt->execute();
        $id = $stmt->get_result()->fetch_assoc()['id'];
        return new self($id);
    }

    public function getUserRow()
    {
        global $db;
        $st = $db->prepare("SELECT * FROM users WHERE id=?");
        $st->bind_param('i', $this->userID);
        $st->execute();
        return $st->get_result()->fetch_assoc();
    }

    public function updateTime()
    {
        global $db;
        $this->updateVariable("lastOnline", time());
    }
    public function updateVariable($variable, $to)
    {
        global $db;
        $st = $db->prepare("UPDATE users SET " . $variable . " = ? WHERE id=?");
        $st->bind_param($this->type(gettype($to)), $to, $this->userID);
        $st->execute();
    }
    public function getVariable($variable)
    {
        if($this->isUser) {
            if (array_key_exists($variable, $this->getUserRow())) {
                return $this->getUserRow()[$variable];
            } else {
                return null;
            }
        }
        else{
            return null;
        }
    }
    private function type($var): string
    {
        if ($var == "string") {
            return "si";
        }
        if ($var == "integer") {
            return "ii";
        }
        if ($var == "double") {
            return "di";
        }
    }
    public function updateSI($newSI)
    {
        if($newSI < 1){
            $newSI = 1;
        }
        if($newSI > 100){
            $newSI = 100;
        }
        $this->updateVariable("hsi", $newSI);
    }
    public function updateAuthority(int $authority)
    {
        $this->updateVariable("authority", $authority);
    }
    public function addCampaignFinance(int $funding){
        $this->updateVariable("campaignFinance", ($this->getVariable("campaignFinance") +$funding ));
    }
    public function pictureArray(): array
    {
        $userID = $this->userID;
        $array = array("picture"=>0, "name"=>0,"id"=>0);
        if($userID != 0){
            $array['picture'] = $this->getVariable("profilePic");
            $array['name'] = $this->getVariable("politicianName");
            $array['id'] = $this->getVariable("id");
        }
        else{
            $array['picture'] = "images/userPics/default.jpg";
            $array['name'] = "None";
            $array['id'] = 0;
        }
        return $array;
    }
    public function deleteUser(){
        global $db;
        if ($this->getUserRow()['profilePic'] != "images/userPics/default.jpg") {
            $pic = substr($this->getUserRow()['profilePic'], 0, strpos($this->getUserRow()[ 'profilePic'], "?ver="));
            try {
                unlink($pic);
            } catch (Exception $exception) {

            }
        }
        $query = "DELETE FROM users WHERE id=" . $this->userID;
        $db->query($query);
    }

    // Party related methods.
    public function getUserPartyVotes()
    {
        global $db;
        global $onlineThreshold;
        $stmt = $db->prepare("SELECT * FROM users WHERE partyVotingFor = ? AND lastOnline > ?");
        $stmt->bind_param("ii", $this->userID, $onlineThreshold);
        $stmt->execute();

        return $stmt->get_result()->num_rows;

    }

    public function leaveCurrentParty()
    {
        $party = new Party($this->getUserRow()['party']);
        $party->partyRoles->userLeave($this->userID);

        $this->updateVariable("party", 0);
        $this->updateVariable("partyInfluence", 0);
        $this->updateVariable("partyVotingFor", 0);
        $this->updateSI($this->getUserRow()['hsi'] * .50);

        global $db;
        $query = "UPDATE users SET partyVotingFor = 0 WHERE partyVotingFor=" . $this->userID;
        $query2 = "DELETE FROM partyVotes WHERE author=" . $this->userID . " AND party=" . $this->getVariable("party");
        $db->query($query);

    }
    public function hasPartyPerm($permission)
    {
        $party = new Party($this->getVariable("party"));
        return $party->partyRoles->hasPermission($permission, $this->userID);


    }
    public function getCommitteeVotes()
    {
        $party = new Party($this->getUserRow()['party']);
        $data = json_decode($party->getCommitteeData(), true);
        foreach ($data as $arr) {
            if ($arr['label'] == $this->getUserRow()['politicianName']) {
                return $arr['y'];
            }
        }

    }
    //

    // CF/LC related methods.
    public function hasCampaignFunds($amount)
    {
        $currentUserCampaignFunds = $this->getVariable("campaignFinance");
        if ((double)$currentUserCampaignFunds >= $amount) {
            return true;
        } else {
            return false;
        }
    }

}