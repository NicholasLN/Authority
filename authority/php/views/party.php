<?php

function partyRoles($partyID)
{
    $party = new Party($partyID);

    echo "
        <div class='row justify-content-center'>
    ";
    $party->partyRoles->echoRoleCard();

    echo "
        </div>
    ";

}


function partyMembersTable($partyID)
{
    global $db;
    global $onlineThreshold;
    global $loggedInUser;
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
    if ($result = $db->query($query)) {
        while ($row = $result->fetch_assoc()) {
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


            $totalInfluence = $party->getTotalPartyInfluence();
            $percentage = round($user->getVariable("partyInfluence") / $totalInfluence * 100,2) . "%";
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
                        <span>$percentage</span>
                    </td>
                    <td>
                        <span>$votes</span>      
                        ";
            if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
                if ($loggedInUser->getVariable("party") == $partyID) {
                    echo "                  
                        <form method='POST'>
                            <input type='submit' class='btn btn-primary' value='Vote For' name='voteFor'/>
                            <input type='hidden' name='voteForID' value='$userID'/>
                        </form>";
                }
            }
            echo "
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
    echo '<a href="#" title="Header" data-toggle="popover" data-placement="top" data-content="Content">Click</a>';

}

function partyOverview($partyID)
{
    global $loggedInRow;
    $party = new Party($partyID);

    echo "
    ";

    $leader = $party->getPartyLeader();

    $leaderPic = $leader->pictureArray()['picture'];
    $leaderName = $leader->pictureArray()['name'];
    $leaderID = $leader->pictureArray()['id'];

    echo
        "    
    <h3>" . $party->partyRoles->partyLeaderTitle() . "</h3>
    <a href='politician.php?id=$leaderID'>
        <img style='max-width:120px;max-height:120px; border:4px ridge yellow;' src='$leaderPic' alt='$leaderName Logo'>
        <br/>
        <span>$leaderName</span>
    </a> 
    ";
    if ($_SESSION['loggedIn'] == true) {
        if ($leaderID == 0 && $loggedInRow['party'] == $partyID) {
            echo "
            <div style='margin-top: 8px' class='row justify-content-center'>
                <div class='col'>
                    <form method='post'>
                        <input type='submit' class='btn btn-primary' name='claimLeaderSubmit' value='Claim'/>
                    </form>
                </div>
            </div>
            ";

        }
    }
    echo "
    <br/>
    <hr/>
    ";

    if ($party->partyRoles->getRoleCount() > 0) {
        echo "<h4>Party Roles</h4>";
        // Party Role View
        partyRoles($partyID);
        //
    }
    $party = new Party($partyID);
    $bio = $party->getPartyBio();

    echo "
        <pre class='partyBioBox'>$bio</pre>
    ";

}





