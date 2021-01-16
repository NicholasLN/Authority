<?php

/**
 * Class User
 */
class User
{
    public $userID;

    public function __construct(int $userID)
    {
        $this->userID = $userID;
    }
    /**
     * Grabs user object based on their username.
     * Primarily used for registration.
     *
     * @param string username
     * @return User
     */
    public static function withUsername(string $username): User
    {
        global $db;

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
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
        if(array_key_exists($variable,$this->getUserRow())){
            return $this->getUserRow()[$variable];
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
    public function getUserPartyVotes(){
        global $db;
        global $onlineThreshold;
        $stmt = $db->prepare("SELECT * FROM users WHERE partyVotingFor = ? AND lastOnline > ?");
        $stmt->bind_param("ii",$this->userID, $onlineThreshold);
        $stmt->execute();

        return $stmt->get_result()->num_rows;

    }
    public function updateAuthority(int $authority)
    {
        $this->updateVariable("authority", $authority);
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
}