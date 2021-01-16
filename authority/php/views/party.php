<?php

function partyOverview($partyID){
    $party = new Party($partyID);
    $bio = $party->getPartyBio();

    echo "
        <pre class='partyBioBox'>$bio</pre>
    ";

}


function partyRoles($partyID){
    $party = new Party($partyID);

    echo "
        <div class='row justify-content-center'>
    ";
    $party->partyRoles->echoRoleCard();

    echo "
        </div>
    ";

}


function partyMembersTable($partyID){
    global $db;
    global $onlineThreshold;
    $party = new Party($partyID);

    echo "
            <br/>   
            <table class='table table-striped table-responsive' id='members' style='vertical-align: center'>
                <thead>
                    <tr>
                        <td>Politician</td>
                        <td>Party Role</td>
                        <td>Region</td>
                        <td>Party Influence</td>
                        <td>Votes</td>
                        <td>Voting For</td>
                    </tr>
                </thead>
            ";
    $query = "SELECT * FROM users WHERE party='$partyID' and lastOnline > '$onlineThreshold'";
    if($result = $db->query($query)) {
        while($row = $result->fetch_assoc()) {
            $user = new User($row['id']);

            $userPic = $user->pictureArray()['picture'];
            $userName = $user->pictureArray()['name'];
            $userID = $user->pictureArray()['id'];
            $userRegion = $user->getUserRow()['state'];
            $userPartyInfluence = $user->getUserRow()['partyInfluence'];
            $userRole = $party->partyRoles->getUserTitle($userID);
            $votes = $user->getUserPartyVotes();


            $votingFor = new User($user->getVariable('partyVotingFor'));
            $votingForPic = $votingFor->pictureArray()['picture'];
            $votingForName = $votingFor->pictureArray()['name'];
            $votingForID = $votingFor->pictureArray()['id'];

            echo "
                <tr>
                    <td>
                        <a href='politician.php?id=$userID'>
                            <img style='max-width:40px;max-height:40px;' src='$userPic'/>
        
                            <p style='margin: 0'>$userName</p>
                        </a>
                    </td>
                    <td>
                        <p style='margin-bottom:0px'>$userRole</p>
                    </td>
                    <td>
                        <p style='vertical-align: center'>$userRegion</p>
                    </td>
                    <td>
                        $userPartyInfluence
                    </td>
                    <td>
                        <span>$votes</span>                        
                        <form method='POST'>
                            <input type='submit' class='btn btn-primary' value='Vote For' name='voteFor'/>
                            <input type='hidden' name='voteForID' value='$userID'/>
                        </form> 
                    </td>
                    <td>
                        <a href='politician.php?id=$votingForID'>
                            <img style='max-width:30px;max-height:30px;' src='$votingForPic'/>
        
                            $votingForName
                        </a>
                    </td>
                </tr>
            ";
        }
    }
    echo "</table>";





}





