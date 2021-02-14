<?php

class partyVote
{
    protected $voteID;
    public $voteExists;
    public $voteActions;
    public $party;
    private $voteRow;

    public $ayes;
    public $nays;
    public $totalVotes;
    public $totalVotesPartyPercentage;

    public $isDelayed;
    public $votingEnded = false;
    public $hasPassed = false;

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
            if ($this->voteRow['passed'] == 1 || $this->voteRow['passed'] == 2) {
                $this->votingEnded = true;
                if ($this->voteRow['passed'] == 1) {
                    $this->hasPassed = true;
                } else {
                    $this->hasPassed = false;
                }
            }
        } else {
            $this->party = null;
            $this->voteExists = false;
        }

        if ($this->voteExists) {
            $array = json_decode($this->voteRow['actions'], true);
            $this->voteActions = $array;
            $this->ayes = $this->getAyes();
            $this->nays = $this->getNays();
            $this->totalVotes = $this->ayes + $this->nays;
            $this->totalVotesPartyPercentage = round($this->ayes / $this->party->getVariable("votes"), 2);
        }
    }

    public function getActionString($action)
    {
        $actionType = $action['actionType'];
        switch ($actionType) {
            case($actionType == "New Chair"):
                $partyTitle = $this->party->partyRoles->partyLeaderTitle();
                $user = new User($action['newChair']);
                $str = "<span class='redFont'>New $partyTitle:</span> <u>" . $user->getVariable("politicianName") . "</u>";
                break;
            case($actionType == "Grant Permission"):
                $str = "<span class='redFont'>Grant Permission:</span> <u>" . $action['permission'] . "</u> <span class='bold'>to <u></span>" . $action['roleName'] . "</u>";
                break;
            case($actionType == "Remove Permission"):
                $str = "<span class='redFont'>Remove Permission:</span> <u>" . $action['permission'] . "</u> <span class='bold'>from </span><u>" . $action['roleName'] . "</u>";
                break;
            case($actionType == "Rename Role"):
                $str = "<span class='redFont'>Rename Role:</span> <u>" . $action['roleToRename'] . "</u> <b class='bold'>to</b> <u>" . $action['renameTo'] . "</u>";
                break;
            case($actionType == "Delete Role"):
                $str = "<span class='redFont'>Delete Role:</span> <u>" . $action['roleName'] . "</u>";
                break;
            case($actionType == "Change Role Occupant"):
                $user = new User($action['newUser']);
                $userName = $user->getVariable("politicianName");
                $str = "<span class='redFont'>Change " . $action['roleName'] . " Occupant</span> <b class='bold'>to</b> <u>$userName</u>";
                break;
            case($actionType == "Change Fees"):
                $str = "<span class='redFont'>Change Fees</span> <b class='bold'>to</b> <u>" . $action['newFees'] . "%</u>";
                break;
            case($actionType == "Rename Party"):
                $str = "<span class='redFont'>Change Party Name</span><b class='bold'> from </b><u>" . $action['oldName'] . "</u><b class='bold'> to </b><u>" . $action['renameTo'] . "</u>";
                break;
            case($actionType == "Change Number of Party Votes"):
                $str = "<span class='redFont'>Change # of Party Votes</span> <b class='bold'> from </b><u>" . $action['oldVotes'] . "</u><b class='bold'> to </b><u>" . $action['votes'] . "</u>";
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
        $invalid = 0;
        foreach ($ayesJSON as $key => $value) {
            $user = new User($value);
            if ($user->getVariable("party") == $this->party->getVariable("id")) {
                $userName = $user->getVariable("politicianName");
                $userVotes = $user->getCommitteeVotes();

                $arr = array($userName => $userVotes);
                $arr2 = array_merge($arr2, $arr);
            } else {
                print_r($key);
                unset($ayesJSON[$key]);
                $invalid = 1;
            }
        }
        if ($invalid) {
            unset($ayesJSON[$key]);
            $this->updateVariable("ayes", json_encode(array_values($ayesJSON)));
        }

        arsort($arr2);
        return $arr2;
    }

    public function getNaysArray()
    {
        $naysJSON = json_decode($this->voteRow['nays'], true);
        $arr2 = array();
        $invalid = 0;
        foreach ($naysJSON as $key => &$value) {
            $user = new User($value);
            if ($user->getVariable("party") == $this->party->getVariable("id")) {
                $userName = $user->getVariable("politicianName");
                $userVotes = $user->getCommitteeVotes();
                $arr = array($userName => $userVotes);
                $arr2 = array_merge($arr2, $arr);
            } else {
                unset($naysJSON[$key]);
                $invalid = 1;
            }
        }
        if ($invalid) {
            $this->updateVariable("nays", json_encode(array_values($naysJSON)));
        }
        arsort($arr2);
        return $arr2;
    }

    public function getAyes()
    {
        $ayes = 0;
        $ayesArray = $this->getAyesArray();
        foreach ($ayesArray as $key => $value) {
            $ayes += $value;
        }
        return $ayes;
    }

    public function getNays()
    {
        $nays = 0;
        $naysArray = $this->getNaysArray();
        foreach ($naysArray as $key => $value) {
            $nays += $value;
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

        $this->updateVariable("ayes", json_encode($ayes));
        $this->updateVariable("nays", json_encode($nays));
    }

    public function delayVote()
    {
        $hours = 12;
        $time = (60 * 60) * $hours; // hours in seconds
        $this->updateVariable("expiresAt", $this->voteRow['expiresAt'] + $time);
        $this->updateVariable('delay', 1);
    }

    public function handleVote()
    {
        foreach ($this->voteActions as $key => $action) {
            $actionType = $action['actionType'];
            switch ($actionType) {
                case($actionType == "New Chair"):
                    $user = new User($action['newChair']);
                    $this->party->partyRoles->removeFromAllRoles($user->userID);
                    $this->party->partyRoles->changeLeader($user->userID);
                    $this->party->partyRoles->updateRoles();
                    break;
                case($actionType == "Grant Permission"):
                    $this->party->partyRoles->appendPermission($action['roleID'], $action['permission']);
                    $this->party->partyRoles->updateRoles();
                    break;
                case($actionType == "Remove Permission"):
                    $this->party->partyRoles->removePermission($action['roleID'], $action['permission']);
                    $this->party->partyRoles->updateRoles();
                    break;
                case($actionType == "Rename Role"):
                    $this->party->partyRoles->renameRole($action['roleID'], $action['renameTo']);
                    $this->party->partyRoles->updateRoles();
                    break;
                case($actionType == "Delete Role"):
                    $this->party->partyRoles->deleteRole($action['roleID']);
                    $this->party->partyRoles->updateRoles();
                    break;
                case($actionType == "Change Role Occupant"):
                    $this->party->partyRoles->changeOccupant($action['roleID'], $action['newUser']);
                    $this->party->partyRoles->updateRoles();
                    break;
                case($actionType == "Rename Party"):
                    $this->party->updateVariable("name", $action['renameTo']);
                    break;
                case($actionType == "Change Fees"):
                    $this->party->updateVariable("fees", $action['newFees']);
                    break;
                case($actionType == "Change Number of Party Votes"):
                    if ($action['votes'] > 1000) {
                        $action['votes'] = 1000;
                    }
                    if ($action['votes'] < 5) {
                        $action['votes'] = 5;
                    }
                    $this->party->updateVariable("votes", $action['votes']);
                    break;
            }
        }
    }

    public function passVote($failed = false)
    {
        if ($failed) {
            $this->updateVariable("passed", 2);
        } else {
            $this->updateVariable("passed", 1);
            $this->handleVote();
        }
    }

}