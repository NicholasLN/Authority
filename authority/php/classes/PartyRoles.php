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
    public function echoRoleCard(){
        foreach($this->partyRoleJson as $roleName=>$roleDetails){
            if(!array_key_exists("leader",$roleDetails['perms'])) {
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
    public function partyLeaderTitle(){
        return $this->partyLeaderArray()['title'];
    }
    public function getUserTitle(int $userID){
        $hasRole = 0;
        foreach($this->partyRoleJson as $roleName=>$roleDetails){
            if($roleDetails['occupant'] == $userID){
                $hasRole = 1;
                return $roleName;
            }
        }
        if($hasRole == 0){
            return "Member";

        }

    }


}