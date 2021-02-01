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

    public function changeOccupant($roleID, $userID)
    {
        foreach ($this->partyRoleJson as $name => &$roleDetails) {
            if ($roleDetails['specialID'] == $roleID) {
                $roleDetails['occupant'] = $userID;
            }
        }
    }

    public function roleArray()
    {
        $arr = array();
        foreach ($this->partyRoleJson as $roleName => $roleDetails) {
            $newArr = array($roleName => $roleDetails['specialID']);
            $arr = array_merge($arr, $newArr);
        }
        return $arr;
    }

    public function removeFromAllRoles($userID)
    {
        foreach ($this->partyRoleJson as $name => &$roleDetails) {
            if ($roleDetails['occupant'] == $userID) {
                $roleDetails['occupant'] = 0;
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

    public function getRoleName($roleID)
    {
        foreach ($this->partyRoleJson as $roleName => $roleDetails) {
            if ($roleDetails['specialID'] == $roleID) {
                return $roleName;
            }
        }
        return false;

    }

    public function createNewRole($roleName, $roleOccupant, $rolePerms)
    {
        $secret = rand(0, 9999999999);
        $arr = array(
            $roleName => array(
                "specialID" => $secret,
                "occupant" => (int)$roleOccupant,
                "perms" => array()
            )
        );
        $this->partyRoleJson = array_merge($this->partyRoleJson, $arr);

        foreach ($rolePerms as $key => $value) {
            $this->appendPermission($secret, $value);
        }

        $this->updateRoles();

    }

    public function renameRole($roleID, $newName)
    {
        foreach ($this->partyRoleJson as $roleName => &$roleDetails) {
            if ($roleDetails['specialID'] == $roleID) {
                $originalArray = $this->partyRoleJson[$roleName];
                unset($this->partyRoleJson[$roleName]);
                $newArray = array(
                    $newName => array(
                        "specialID" => $roleID,
                        "occupant" => (int)$originalArray['occupant'],
                        "perms" => $originalArray['perms']
                    )
                );
                $this->partyRoleJson = array_merge($this->partyRoleJson, $newArray);
                var_dump($this->partyRoleJson);
            }
        }
    }

    public function deleteRole($roleID)
    {
        foreach ($this->partyRoleJson as $roleName => &$roleDetails) {
            if ($roleDetails['specialID'] == $roleID) {
                unset($this->partyRoleJson[$roleName]);
            }
        }
    }

    public function appendPermission($roleID, $perm)
    {
        foreach ($this->partyRoleJson as $roleName => &$roleDetails) {
            if ($roleDetails['specialID'] == $roleID) {
                if (isset($roleDetails['perms'][$perm])) {
                    $roleDetails['perms'][$perm] = 1;
                } else {
                    $arr = array($perm => 1);
                    $roleDetails['perms'] += $arr;
                }
            }
        }
    }

    public function removePermission($roleID, $permission)
    {
        foreach ($this->partyRoleJson as $roleName => &$roleDetails) {
            if ($roleDetails['specialID'] == $roleID) {
                if ($roleDetails['perms'][$permission] == 1) {
                    $roleDetails['perms'][$permission] = 0;
                }
            }
        }
    }

    public function updateRoles()
    {
        $update = json_encode($this->partyRoleJson, JSON_PRETTY_PRINT);
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
        if ($this->partyLeaderID() == $userID) {
            $hasPerm = true;
        }
        return $hasPerm;
    }

    public function isRole($roleID)
    {
        foreach ($this->partyRoleJson as $roleName => $roleDetails) {
            if ($roleDetails['specialID'] == $roleID) {
                return true;
            }
        }
        return false;
    }

}