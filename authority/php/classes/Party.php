<?php

class Party
{
    protected $partyID;
    public $partyRoles;
    public $partyRow;
    public $partyExists;

    public function __construct($partyID)
    {
        $this->partyID = $partyID;
        $this->partyRow = $this->getPartyRowConstructor($partyID);
        if (getNumRows("SELECT * FROM parties WHERE id=$partyID") == 1) {
            $this->partyExists = true;
            $this->partyRoles = new PartyRoles($this->partyRow['partyRoles'], $partyID);
        } else {
            $this->partyExists = false;
        }
    }

    public function getPartyRowConstructor($partyID): ?array
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM parties WHERE id=?");
        $stmt->bind_param("i", $partyID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updatePartyRow()
    {
        global $db;
        $id = $this->partyID;
        $this->partyRow = $this->getPartyRowConstructor($this->partyID);
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

    public function updateVariable($variable, $to)
    {
        global $db;
        $st = $db->prepare("UPDATE parties SET " . $variable . " = ? WHERE id=?");
        $st->bind_param($this->type(gettype($to)), $to, $this->partyID);
        $st->execute();
    }

    public function pictureArray($profile = false): array
    {
        $partyID = $this->partyID;
        $array = array("picture" => 0, "name" => 0, "id" => 0);
        if ($partyID != 0) {
            $array['picture'] = $this->getVariable("partyPic");
            $array['name'] = $this->getVariable("name");
            $array['id'] = $this->getVariable("id");
        } else {
            if ($profile == true) {
                $array['picture'] = 'images/partyPics/independent.png?ver=1';
            } else {
                $array['picture'] = "images/partyPics/default.png";
            }
            $array['name'] = "Independent";
            $array['id'] = 0;
        }
        return $array;
    }

    public function getTotalPartyInfluence()
    {
        global $db;
        global $onlineThreshold;
        $total = $db->query("SELECT SUM(partyInfluence) as partyInfluenceSum FROM users WHERE party = " . $this->partyID . " AND lastOnline>$onlineThreshold");
        $total = $total->fetch_assoc();
        return round($total['partyInfluenceSum'], 2) + 0.000001;
    }

    public function getPartyLeader(): ?User
    {
        $leader = $this->partyRoles->partyLeaderArray();
        if ($leader['occupant'] != 0) {
            return new User($leader['occupant']);
        } else {
            return new User(0);
        }
    }

    public function getPartyName()
    {
        return $this->partyRow['name'];
    }

    public function getPartyBio()
    {
        return $this->partyRow['partyBio'];
    }

    public function getPartyLogo()
    {
        global $db;
        if($this->partyRow['partyPic'] == ""){
            return "images/partyPics/independent.png";
        }
        else {
            return $this->partyRow['partyPic'];
        }
    }

    public function getFundingRequestsArray() : array{
        global $db;
        $stmt = $db->prepare("SELECT * FROM fundRequests WHERE party = ? AND fulfilled = 0");
        $stmt->bind_param("i",$this->partyID);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPartyDiscordCode()
    {
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

    public function getVariable($variable)
    {
        if (array_key_exists($variable, $this->partyRow)) {
            return $this->partyRow[$variable];
        } else {
            return null;
        }
    }

    public function echoRoleOptions($chair = "false")
    {
        foreach ($this->partyRoles->roleArray() as $roleName => $roleID) {
            if ($roleID == 0) {
                if ($chair == "true") {
                    echo "<option value='$roleID'>$roleName</option>";
                }
            } else {
                echo "<option value='$roleID'>$roleName</option>";
            }
        }
    }

    public function getCommitteeData()
    {
        global $db;
        global $onlineThreshold;
        $query = "SELECT * FROM users WHERE party=" . $this->partyID . " AND lastOnline>=$onlineThreshold ORDER BY partyInfluence DESC";
        $result = $db->query($query);
        $committeeSeats = $this->getVariable("votes");
        $arr = array();
        while ($urow = $result->fetch_assoc()) {
            $userShare = ($urow['partyInfluence'] / $this->getTotalPartyInfluence());
            $userVotes = round($userShare * $committeeSeats);
            if ($userVotes > 0) {
                $arr2 = array("y" => $userVotes, "label" => $urow['politicianName'], "share" => $userShare);
                array_push($arr, $arr2);
            }
        }
        $sum = 0;
        foreach ($arr as $item) {
            $sum += $item['y'];
        }
        if ($sum < $committeeSeats) {
            $diff = $committeeSeats - $sum;
            $x = end($arr);
            foreach ($arr as &$item) {
                if ($item['label'] == $x['label']) {
                    $item['y'] += $diff;
                }
            }


        }
        $arr = json_encode($arr);
        return $arr;
    }

    public function getActiveVotes(): array
    {
        global $db;
        $statement = $db->prepare("SELECT * FROM partyVotes WHERE party=?");
        $statement->bind_param("i", $this->partyID);

        $statement->execute();
        $result = $statement->get_result();
        $assoc = $result->fetch_all(MYSQLI_ASSOC);

        $voteArray = array();
        foreach ($assoc as $vote) {
            $voteClass = new PartyVote($vote['id']);
            array_push($voteArray, $voteClass);
        }
        return $voteArray;

    }
}

