<?php

class partyVote
{
    protected $voteID;
    public $voteExists;
    public $voteActions;
    public $party;
    private $voteRow;
    public $isDelayed;

    public function __construct($voteID)
    {
        global $db;
        $this->voteID = $voteID;
        $statement = $db->prepare("SELECT * FROM partyVotes WHERE id=?");
        $statement->bind_param("i", $this->voteID);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->party = new Party($row['party']);
            $this->voteExists = true;
            $this->voteRow = $row;
            if ($this->voteRow['delay'] == 0) {
                $this->isDelayed = false;
            } else {
                $this->isDelayed = true;
            }
        } else {
            $this->party = null;
            $this->voteExists = false;
        }

        if ($this->voteExists) {
            $array = json_decode($this->voteRow['actions'], true);
            $this->voteActions = $array;
        }
    }

    public function getActionString($action)
    {
        $actionType = $action['actionType'];
        switch ($actionType) {
            case($actionType == "New Chair"):
                $partyTitle = $this->party->partyRoles->partyLeaderTitle();
                $user = new User($action['newChair']);
                $str = "<span class='redFont'>New $partyTitle:</span> " . $user->getVariable("politicianName");
                break;
            case($actionType == "Grant Permission"):
                $str = "<span class='redFont'>Grant Permission:</span> " . $action['permission'] . " <span class='bold'>to </span>" . $action['roleName'];
                break;
        }
        return $str;
    }

    public function getBillID()
    {
        return $this->voteID;
    }

    public function getBillRundown($more = false)
    {
        $str = "";
        foreach ($this->voteActions as $key => $action) {
            if ($key == "0") {
                $str .= $this->getActionString($action);
            }
            if ($more == true && $key > 0) {
                $str .= "<hr>" . $this->getActionString($action);
            }
        }
        if (!$more) {
            $str .= "<hr>(more)";
        }
        return $str;
    }

    public function getBillName()
    {
        return $this->voteRow['name'];
    }

    public function getTimeLeft()
    {
        return getHourDifference($this->voteRow['expiresAt'], time());
    }

    public function getAyesArray()
    {
        $ayesJSON = json_decode($this->voteRow['ayes']);
        $arr2 = array();
        foreach ($ayesJSON as $key => $value) {
            $user = new User($value);
            $userName = $user->getVariable("politicianName");
            $userVotes = $user->getCommitteeVotes();

            $arr = array($userName => $userVotes);
            $arr2 = array_merge($arr2, $arr);
        }
        return $arr2;
    }

    public function getNaysArray()
    {
        $naysJSON = json_decode($this->voteRow['nays']);
        $arr2 = array();
        foreach ($naysJSON as $key => $value) {
            $user = new User($value);
            $userName = $user->getVariable("politicianName");
            $userVotes = $user->getCommitteeVotes();

            $arr = array($userName => $userVotes);
            $arr2 = array_merge($arr2, $arr);
        }
        return $arr2;
    }

    public function getAyes()
    {
        $ayes = 0;
        $ayesJSON = json_decode($this->voteRow['ayes']);
        foreach ($ayesJSON as $key => $value) {
            $user = new User($value);
            $ayes += $user->getCommitteeVotes();
        }
        return $ayes;
    }

    public function getNays()
    {
        $nays = 0;
        $naysJSON = json_decode($this->voteRow['nays'], true);
        foreach ($naysJSON as $key => $nayVote) {
            $user = new User($nayVote);
            $nays += $user->getCommitteeVotes();
        }
        return $nays;
    }

    public function getAuthor(): User
    {
        return new User($this->voteRow['author']);
    }

    public function getBillTitle()
    {
        $PRnum = numHash($this->voteID, 5);
        $name = $this->getBillName();

        return "P.R $PRnum: $name";
    }

    public function updateVariable($variable, $to)
    {
        global $db;
        $st = $db->prepare("UPDATE partyVotes SET " . $variable . " = ? WHERE id=?");
        $st->bind_param($this->type(gettype($to)), $to, $this->voteID);
        $st->execute();
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

    public function addVote($AYEorNAY, $userID)
    {
        $ayes = json_decode($this->voteRow['ayes']);
        $nays = json_decode($this->voteRow['nays']);

        switch ($AYEorNAY) {
            case($AYEorNAY == "aye"):
                $update = "ayes";
                foreach ($nays as $key => &$nay) {
                    if ($nay == $userID) {
                        unset($nays[$key]);
                    }
                }
                $alreadyAye = 0;
                foreach ($ayes as $key => $aye) {
                    if ($aye == $userID) {
                        $alreadyAye = 1;
                    }
                }
                if ($alreadyAye != 1) {
                    array_push($ayes, $userID);
                } else {
                    alert("Error!", "You are already voting AYE on this.");
                }
                break;
            case($AYEorNAY == "nay"):
                $update = "nays";
                foreach ($ayes as $key => &$aye) {
                    if ($aye == $userID) {
                        unset($ayes[$key]);
                    }
                }
                $alreadyNay = 0;
                foreach ($nays as $key => $nay) {
                    if ($nay == $userID) {
                        $alreadyNay = 1;
                    }
                }
                if ($alreadyNay != 1) {
                    array_push($nays, $userID);
                } else {
                    alert("Error!", "You are already voting NAY on this.");
                }
                break;
        }

        $this->updateVariable("ayes", json_encode($ayes, JSON_PRETTY_PRINT));
        $this->updateVariable("nays", json_encode($nays, JSON_PRETTY_PRINT));
    }

    public function delayVote()
    {
        $hours = 12;
        $time = (60 * 60) * $hours; // hours in seconds
        $this->updateVariable("expiresAt", $this->voteRow['expiresAt'] + $time);
        $this->updateVariable('delay', 1);
    }

}