<?php

function partyRoles($partyID)
{
    $party = new Party($partyID);

    ?>
        <div class='row justify-content-center'>
    <?
    $party->partyRoles->echoRoleCard();
    ?>
        </div>
    <?
}


//TODO: This should become ajax as well. Datatables has an ajax ability.
function partyMembersTable($partyID)
{
    global $db;
    global $onlineThreshold;
    global $loggedInUser;
    $party = new Party($partyID);

    ?>
            <br/>
            <div class="table-responsive">
            <table class='table table-striped' id='members' style='vertical-align: center'>
                <thead class="dark">
                    <tr>
                        <th style="width:22%">Politician</th>
                        <th style="width:16%">Party Role</th>
                        <th style="width:16%">Region</th>
                        <th style="width:16%">Party Influence</th>
                        <th style="width:10%">Votes</th>
                        <th style="width:16%">Voting For</th>
                    </tr>
                </thead>
    <?
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
            ?>
                <tr>
                    <td>
                        <a href='politician.php?id=<? echo $userID ?>'>
                            <img style='max-width:40px;max-height:40px;' src='<? echo $userPic ?>' alt=''/>
        
                            <p style='margin: 0'><? echo $userName ?></p>
                        </a>
                    </td>
                    <td>
                        <p style='margin-bottom:0'><? echo $userRole ?></p>
                    </td>
                    <td>
                        <p style='vertical-align: center'><? echo $userRegion ?></p>
                    </td>
                    <td>
                        <span><? echo $percentage ?></span>
                    </td>
                    <td>
                        <span><? echo $votes ?></span>
            <?
            if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
                if ($loggedInUser->getVariable("party") == $partyID) {
                    ?>
                        <form method='POST'>
                            <input type='submit' class='btn btn-primary' value='Vote For' name='voteFor'/>
                            <input type='hidden' name='voteForID' value='<? echo $userID ?>'/>
                        </form>
                    <?
                }
            }
            ?>
                    </td>
                    <td>
                        <div style='text-align: left'>
                            <a href='politician.php?id=<? echo $votingForID ?>'>
                                <img style='max-width:30px;max-height:30px;' src='<? echo $votingForPic ?>'/>
            
                                <? echo $votingForName ?>
                            </a>
                        </div>
                    </td>
                </tr>
            <?
        }
    }
    ?>
        </table>
        </div>
    <?
    echo '<a href="#" title="Header" data-toggle="popover" data-placement="top" data-content="Content">Click</a>';

}

function partyOverview($partyID)
{
    global $loggedInRow;
    $party = new Party($partyID);

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
            ?>
            <div style='margin-top: 8px' class='row justify-content-center'>
                <div class='col'>
                    <form method='post'>
                        <input type='submit' class='btn btn-primary' name='claimLeaderSubmit' value='Claim'/>
                    </form>
                </div>
            </div>
            <?

        }
    }
    ?>
    <br/>
    <hr/>
    <?

    if ($party->partyRoles->getRoleCount() > 0) {
        echo "<h4>Party Roles</h4>";
        // Party Role View
        partyRoles($partyID);
        //
    }
    $party = new Party($partyID);
    $bio = $party->getPartyBio();

    ?>
        <pre class='partyBioBox'><? echo $bio ?></pre>
    <?

}

function partyControls($partyID){
    global $loggedInUser;
    if(isset($loggedInUser)){
        $party = new Party($partyID);
        if($party->partyRoles->partyLeaderID() == $loggedInUser->userID){
            ?>
                <br/>
                <form method="POST" enctype="multipart/form-data">
                    <table class="table table-striped table-responsive">
                        <thead class="dark">
                        <tr>
                            <th scope="col">Action</th>
                            <th scope="col">Input</th>
                            <th scope="col">Submit</th>
                        </tr>
                        </thead>
                        <tr>
                            <? echo $party->partyRoles->getRoleCount() ?>
                            <td><b>Create New Position</b></td>
                            <td>
                                <div class="row">
                                    <div class="col-sm">
                                        <input class="form-control" name='roleName' type="input" placeholder="Role Name (25 char. max)"/>
                                    </div>
                                    <div class="col-sm">
                                        <?  partySearchAjax($partyID,$loggedInUser->userID) ?>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:8px;">
                                    <hr style="margin-bottom: 0"/>
                                    <h6 style="margin: 8px 0px 8px 0px">Role Permissions (can only choose 3)</h6>
                                    <hr/>
                                    <div class="row" style="text-align: left">
                                        <div class="col-sm-4">
                                            <input class="form-check-input" name="roleCheck[]" type="checkbox" value="sendFundsCheck" id="sendFunds">
                                            <label class="form-check-label" for="sendFunds">
                                                Send Funds
                                            </label>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-check-input" name="roleCheck[]" type="checkbox" value="feeChangeCheck" id="proposeFeeChange">
                                            <label class="form-check-label" for="proposeFeeChange">
                                                Fee Change
                                            </label>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-check-input" name="roleCheck[]" type="checkbox" value="delayVoteCheck" id="delayPartyVote">
                                            <label class="form-check-label" for="delayPartyVote">
                                                Delay Party Votes
                                            </label>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-check-input" name="roleCheck[]" type="checkbox" value="purgeMemberCheck" id="purgeMember">
                                            <label class="form-check-label" for="purgeMember">
                                                Purge Members
                                            </label>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-check-input" name="roleCheck[]" type="checkbox" value="fundingReqCheck" id="fundingReq">
                                            <label class="form-check-label" for="fundingReq">
                                                Approve Funding Req.
                                            </label>
                                        </div>
                                        <div class="col-sm-4">
                                            <input class="form-check-input" name="roleCheck[]" type="checkbox" value="partyAnnounceCheck" id="partyAnnounce">
                                            <label class="form-check-label" for="partyAnnounce">
                                                Make Party Announcements
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                            </td>
                            <td>
                                <input type="submit" class="btn btn-primary" value="Create Position" name="createPosSubmit">
                            </td>

                        </tr>
                        <tr>
                            <td><b>Change Party Picture</b></td>
                            <td>
                                <input class="form-control" type="file" name="newPartyPicture" accept="image/*"/>
                                <p style="text-align:left;margin-bottom:1px;margin-left:2px;">Accepted File Types:
                                    .png, .jpeg, .gif, .bmp</p>
                            </td>
                            <td><input class="btn btn-primary" value="Change Picture" type="submit"
                                       name="newPartyPicSubmit"/></td>
                        </tr>
                        <tr>
                            <td><b>Change Party Description</b></td>
                            <td><textarea rows='6' class='form-control'
                                          name="newPartyDesc"><?php echo $party->getPartyBio() ?></textarea></td>
                            <td><input class="btn btn-primary" value="Change Description" type="submit"
                                       name="newPartyDescSubmit"/></td>
                        </tr>
                        <tr>
                            <td><b>Change Party Discord Code</b></td>
                            <td>
                                <strong style="font-weight:600">Only the code at the end of the link!</strong> Ex: 9v94Fad
                                <input type='input' class='form-control' name="newPartyDiscord" value='<?php echo $party->getPartyDiscordCode() ?>'>
                            </td>
                            <td><input class="btn btn-primary" value="Change Discord" type="submit"
                                       name="newPartyDiscordSubmit"/></td>
                        </tr>
                    </table>
                </form>

            <?

        }
        else{
            redirect("party.php?id=$partyID&mode=overview","Error","You do not have the appropriate permissions to be here.");
        }
    }
    else{
        redirect("party.php?id=$partyID&mode=overview","Error","You do not have the appropriate permissions to be here.");
    }

}



