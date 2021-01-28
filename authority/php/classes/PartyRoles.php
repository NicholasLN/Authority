<?php

class PartyRoles
{
    private $partyRoleJson;
    private $partyID;

    public function __construct($partyRoles, $partyID)
    {
        $this->partyRoleJson = json_decode($partyRoles, true);
        $this->partyID = $partyID;
    }

    public function changeOccupant($roleName, $userID)
    {
        foreach ($this->partyRoleJson as $name => &$roleDetails) {
            if ($name == $roleName) {
                $roleDetails['occupant'] = $userID;
            }
        }
    }

    public function changeLeader($userID)
    {
        foreach ($this->partyRoleJson as $name => &$roleDetails) {
            if ($name == $this->partyLeaderTitle()) {
                $roleDetails['occupant'] = $userID;
            }
        }
    }

    public function partyLeaderTitle()
    {
        return $this->partyLeaderArray()['title'];
    }

    public function partyLeaderArray(): array
    {
        foreach ($this->partyRoleJson as $roleName => $roleDetails) {
            if (array_key_exists("leader", $roleDetails['perms'])) {
                if ($roleDetails['perms']['leader'] == true) {
                    $array = array(
                        "title" => $roleName,
                        "occupant" => $roleDetails['occupant']
                    );
                    return $array;
                }
            }
        }
    }

    public function userLeave(int $userID)
    {
        foreach ($this->partyRoleJson as $roleName => &$roleDetails) {
            if ($roleDetails['occupant'] == $userID) {
                $roleDetails['occupant'] = 0;
            }
        }
        $this->updateRoles();
    }

    public function echoRoleCard()
    {
        foreach ($this->partyRoleJson as $roleName => $roleDetails) {
            if (!array_key_exists("leader", $roleDetails['perms'])) {
                $occupant = new User($roleDetails['occupant']);
                $occupantPic = $occupant->pictureArray()['picture'];
                $occupantName = $occupant->pictureArray()['name'];
                $occupantID = $occupant->pictureArray()['id'];

                echo "
                <div class='col-sm-3' style='margin-top: 8px'>
                    <span>$roleName</span>
                    <br/>
                    <a href='politician.php?id=$occupantID'>
                        <img style='max-width:80px;height:75px;border:5px ridge darkgrey' src='$occupantPic'/>
                        <p>$occupantName</p>
                    </a>
                </div>    
                ";
            }

        }
    }

    public function partyLeaderID()
    {
        return $this->partyLeaderArray()['occupant'];
    }

    public function getUserTitle(int $userID)
    {
        $hasRole = 0;
        foreach ($this->partyRoleJson as $roleName => $roleDetails) {
            if ($roleDetails['occupant'] == $userID) {
                $hasRole = 1;
                return $roleName;
            }
        }
        if ($hasRole == 0) {
            return "Member";

        }
    }

    public function getRoleCount(): int
    {
        $i = 0;
        foreach ($this->partyRoleJson as $roleName => $roleDetails) {
            if ($roleName != $this->partyLeaderTitle()) {
                $i += 1;
            }
        }
        return $i;
    }

    public function createNewRole($roleName, $roleOccupant, $rolePerms)
    {
        $arr = array(
            $roleName => array(
                "occupant" => (int)$roleOccupant,
                "perms" => array()
            )
        );
        $this->partyRoleJson = array_merge($this->partyRoleJson, $arr);

        foreach ($rolePerms as $key => $value) {
            $permArray = array($value => 1);
            $this->appendPermission($roleName, $permArray);
        }

        $this->updateRoles();

    }

    public function appendPermission($roleName, $arr)
    {
        foreach ($this->partyRoleJson as $key => &$value) {
            if ($key == $roleName) {
                $value['perms'] += $arr;
            }
        }
    }

    public function updateRoles()
    {
        $update = json_encode($this->partyRoleJson,JSON_PRETTY_PRINT);
        if (!json_last_error()) {
            global $db;

            $stmt = $db->prepare("UPDATE parties SET partyRoles = ? WHERE id = ?");
            $stmt->bind_param("si", $update, $this->partyID);
            $stmt->execute();
        }
    }

    public function hasPermission($permission, $userID) :bool
    {
        $hasPerm = false;
        foreach($this->partyRoleJson as $roleName=>$roleDetails){
            if($roleDetails['occupant'] == $userID) {
                if (array_key_exists($permission, $roleDetails['perms'])) {
                    if ($roleDetails['perms'][$permission] == 1) {
                        $hasPerm = true;
                    }
                }
            }
        }
        if($this->partyLeaderID() == $userID){
            $hasPerm = true;
        }
        return $hasPerm;
    }

}