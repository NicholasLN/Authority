<?php

class PartyRoles
{
    private $partyRoleJson;

    public function __construct($partyRoles)
    {
        $this->partyRoleJson = json_decode($partyRoles, true);
    }

    public function partyLeaderArray(): array
    {
        foreach($this->partyRoleJson as $roleName => $roleDetails) {
            if(array_key_exists("leader",$roleDetails['perms'])) {
                if($roleDetails['perms']['leader'] == true) {
                    $array = array(
                        "title"=>$roleName,
                        "occupant"=>$roleDetails['occupant']
                    );
                    return $array;
                }
            }
        }
    }
    public function partyLeaderTitle(){
        return $this->partyLeaderArray()['title'];
    }



}