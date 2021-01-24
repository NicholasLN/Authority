<?php
include 'php/functions.php';

if (isset($_GET['id'])) {
    $partyID = numFilter($_GET['id']);
    if (getNumRows("SELECT * FROM parties WHERE id=$partyID") == 1) {
        $party = new Party($partyID);
        if (isset($_GET['mode'])) {
            $mode = $_GET['mode'];
            if ($mode != "members" && $mode != "partylegislature" && $mode != "partyControls" && $mode != "overview" && $mode!="partyBank") {
                $mode = "overview";
            }
        } else {
            $mode = "overview";
        }
    } else {
        invalidPage("Not a party", "Invalid party page.");
    }
} else {
    invalidPage("Not a party.", "Invalid party page.");
}
if(isset($mode) && $mode=="partyBank"){
    if(!isset($loggedInUser)){
        redirect("index.php","No.","Log in first.","error","?");
    }
    else{
        if ($loggedInUser->getVariable("party") != $partyID) {
            redirect("index.php", "No.", "You know you should not have gone there..", "error", "?");
        }
    }
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title><? echo $party->getPartyName() ?> | AUTHORITY</title>
        <? echoHeader(); ?>
        <link rel="stylesheet" type="text/css"
              href="https://cdn.datatables.net/v/bs4/dt-1.10.23/b-1.6.5/datatables.min.css"/>
        <link rel="stylesheet" href="css/party.css?id=6"/>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.23/r-2.2.7/datatables.min.js"></script>
        <script src='https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js'></script>
        <script src='https://cdn.datatables.net/plug-ins/1.10.22/sorting/natural.js'></script>

    </head>
    <? echoNavBar() ?>
    <body>
    <div class="main">
        <div class="gameContainer">
            <div class="row">
                <div class="col-sm"></div>
                <div class="col-sm-11">
                    <br/>
                    <h2><? echo $party->getPartyName() ?> </h2>
                    <img style="max-width:150px;max-height:150px;" src="<? echo $party->getPartyLogo() ?>"
                         alt="<? echo $party->getPartyName() ?> Logo"/>


                    <? // Join/Leave Party Button and PHP //
                    if ($_SESSION['loggedIn'] == True) {
                        // if they are within the same nation...
                        if ($loggedInRow['nation'] == $party->partyRow['nation']) {
                            echo "
                            <div style='margin-top: 8px' class='row justify-content-center'>
                                <div class='col-md-4'>
                                    ";
                            // if they are in the party
                            if ($loggedInRow['party'] == $partyID) {
                                echo "<button class='btn btn-danger' onClick='leaveConfirm()'>Leave Party (Lose 50% HSI)</button>";

                            }
                            // if they have no party
                            if ($loggedInRow['party'] == 0) {
                                ?>
                                <form method='post'>
                                    <input type='submit' class='btn btn-primary' name='joinPartySubmit' value='Join Party'/>
                                </form>
                                <?
                            }
                            // if they are in a party, but it is not their own
                            if ($loggedInRow['party'] != 0 && $partyID != $loggedInRow['party']) {
                                echo "<button class='btn btn-danger' onClick='defectConfirm()'>Defect (Lose 50% HSI)</button>";
                            }
                            echo "
                                </div>
                            </div>";
                        }
                    }


                    ?>

                    <hr/>
                    <div class="row justify-content-center">
                        <div class="col">
                            <a href="party.php?id=<? echo $partyID ?>&mode=members#members" class="btn btn-primary">Members</a>
                            <a href="party.php?id=<? echo $partyID ?>&mode=overview"
                               class="btn btn-primary">Overview</a>
                            <?
                            if(isset($loggedInUser) && $loggedInUser->getVariable("party") == $partyID){
                                echo "<a style='margin-right:3px' class='btn btn-primary' href='party.php?id=$partyID&mode=partyBank'>Party Bank</a>";

                            }
                            if(isset($loggedInID) && $loggedInID == $party->partyRoles->partyLeaderID()){
                                echo "<a style='margin-right: 3px' class='btn btn-primary' href='party.php?id=$partyID&mode=partyControls'>Management</a>";
                            }
                            if ($party->getPartyDiscordCode() != "0") {
                                $code = $party->getPartyDiscordCode();
                                echo "<a class='btn btn-danger' href='https://discord.gg/$code' target='_BLANK'>Discord</a>";
                            }
                            ?>
                        </div>
                    </div>
                    <hr/>
                    <?
                    switch ($mode) {
                        case ($mode == "members"):
                            partyMembersTable($partyID);
                            break;
                        case ($mode == "overview"):
                            partyOverview($partyID);
                            break;
                        case ($mode=="partyControls"):
                            partyControls($partyID);
                            break;
                        case ($mode=="partyBank"):
                            bankView($partyID);
                            break;

                    }
                    ?>
                    <br/>
                    <br/>
                    <br/>
                </div>
                <div class="col-sm"></div>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $('#members').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "order": [[3, "desc"]],
                    columnDefs: [
                        {type: 'natural', targets: 0}
                    ]

                });
            });
            table = $('#fundRequestTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "order": [[1, "desc"]],
                "bLengthChange": false,
                "language": {
                    "emptyTable": "No current requests.",
                    "zeroRecords": "No funding requests with this filter."
                }
            });
            $('#searchBoxFunds').on( 'keyup', function () {
                table.search( this.value ).draw();
            } );
            function defectConfirm(){
                Swal.fire({
                    showCancelButton: false,
                    showConfirmButton: false,
                    icon:"warning",
                    title:"Are you sure?",
                    html:"You will lose 50% of your Regional Influence and any positions in the prior party." +
                    "<br><br>" +
                    "<form method='post'>"+
                    "<input type='submit' class='btn btn-danger' name='defectPartySubmit' value='Defect (Lose 50% HSI)'/>"+
                    "</form>"
                })
            }
            function leaveConfirm(){
                Swal.fire({
                    showCancelButton: false,
                    showConfirmButton: false,
                    icon:"warning",
                    title:"Are you sure?",
                    html:"You will lose 50% of your Regional Influence and any positions in the party." +
                        "<br><br>" +
                        "<form method='post'>"+
                        "<input type='submit' class='btn btn-danger' name='leavePartySubmit' value='Leave Party (Lose 50% HSI)'/>"+
                        "</form>"
                })
            }
        </script>
        <? echoFooter() ?>
    </div>
    </html>

<?php
echo getcwd();
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == True) {
    // Join Party
    if (isset($_POST['joinPartySubmit'])) {
        // validate that user isn't already in a party in post
        if ($loggedInRow['party'] == 0) {
            $loggedInUser->updateVariable("party", $partyID);
            redirect("party.php?id=$partyID");
        }
    }
    // Leave Party
    if (isset($_POST['leavePartySubmit'])) {
        // validate that user is actually in the party
        if ($loggedInRow['party'] == $partyID) {

            $loggedInUser->leaveCurrentParty();
            redirect("party.php?id=$partyID");

        }
    }
    // Defect to Party
    if (isset($_POST['defectPartySubmit'])) {
        if ($partyID != $loggedInRow['party'] && $loggedInRow['party'] != 0) {

            $loggedInUser->leaveCurrentParty();
            $loggedInUser->updateVariable("party",$partyID);
            redirect("party.php?id=$partyID");

        }
    }
    // Claim party leadership
    if (isset($_POST['claimLeaderSubmit'])) {
        if ($loggedInRow['party'] == $partyID && $leaderID == 0) {
            $party->partyRoles->changeLeader($loggedInID);
            $party->partyRoles->updateRoles();
            redirect("party.php?id=$partyID");

        }
    }
    //TODO: Turn this into an Ajax form request instead of a PHP Submit.

    // Vote for user in party.
    if (isset($_POST['voteFor'])) {
        if (isset($_POST['voteForID']) && $_POST['voteForID'] != 0) {
            $votingFor = new User(numFilter($_POST['voteForID']));
            if ($votingFor->getVariable("party") == $loggedInRow['party']) {
                $loggedInUser->updateVariable("partyVotingFor", $votingFor->userID);
                redirect("party.php?id=$partyID&mode=members");
            } else {
                alert("Error", "They are not in your party.");
            }
        }
    }
    // Create position
    if (isset($_POST['createPosSubmit'])){
        //var_dump($_POST);
        if(isset($_POST['roleCheck']) && sizeof($_POST['roleCheck']) <= 3){
            $illegitimate = 0;
            foreach ($_POST['roleCheck'] as $key=>$value){
                switch ($value) {
                    case "sendFunds":
                    case "proposeFees":
                    case "purgeMember":
                    case "delayVote":
                    case "fundingReq":
                    case "sendAnnouncement":
                        break;
                    default:
                        $illegitimate = 1;
                        break;
                }
            }
            if($illegitimate==0){
                $partyRoleUser = new User(numFilter($_POST['partySearch']));
                if($partyRoleUser->isUser){
                    if($partyRoleUser->getVariable("party")==$partyID){
                        if($party->partyRoles->getRoleCount() < 3) {
                            if($party->partyRoles->getUserTitle($partyRoleUser->userID) == "Member") {
                                $party->partyRoles->createNewRole($_POST['roleName'], $_POST['partySearch'], $_POST['roleCheck']);
                            }
                            else{
                                alert("Error!","They already have a role.");
                            }
                        }
                        else{
                            alert("Error!","You currently have too many roles. This limit will soon be related to national party influence in coming updates.");
                        }
                    }
                    else{
                        alert("Error!","Not within ya party. Do not HTML Spoof!");
                    }
                }
                else{
                    alert("Error!","Not a user. Do not HTML Spoof next time.");
                }
            }
            else{
                alert("Caught ya!","Thought you could get away with HTML Spoofing? Well, guess what, I just told a mod!");
            }
        }
    }
    // Submit new party description
    if (isset($_POST['newPartyDescSubmit'])){
        if(!isset($_POST['newPartyDesc'])){
            $newDesc = "";
        }
        else{
            $newDesc= $_POST['newPartyDesc'];
        }
        $newDesc = strip_tags($newDesc);
        $newDesc = preg_replace('/(\r\n|\n|\r){3,}/', "$1$1$1", $newDesc);
        $party->updateVariable("partyBio",$newDesc);
        redirect("party.php?id=$partyID","Success!","Changed Party Bio");

    }
    // Submit new party picture
    if (isset($_POST['newPartyPicSubmit'])){
        $directory = "images/partyPics";
        $file = $directory . basename($_FILES['newPartyPicture']["name"]);
        $imageFileType = pathinfo($file, PATHINFO_EXTENSION);
        $check = getimagesize($_FILES['newPartyPicture']["tmp_name"]);
        // is image
        if (($check !== false)) {
            if ($_FILES['newPartyPicture']['size'] < 500000) {
                $name = $party->getVariable("name");
                $pic = $party->getVariable("partyPic");
                if($party->getVariable("partyPic") != "images/partyPics/independent.png") {
                    $pic = substr($pic, 0, strpos($pic, "?ver="));
                    unlink($pic);
                }
                try {
                    $rand = random_int(PHP_INT_MIN, PHP_INT_MAX);
                } catch (Exception $e) {
                    $rand = 92393;
                }
                $moved = move_uploaded_file($_FILES["newPartyPicture"]["tmp_name"], "$directory/$name.$imageFileType");

                $fileString = "images/partyPics/$name.$imageFileType?ver=$rand";
                $party->updateVariable("partyPic", $fileString);

                if ($moved) {
                    redirect("party.php?id=$partyID");
                } else {
                    echo $_FILES['file']['error'];
                }
            } else {
                $kb = round($_FILES['newPartyPicture']['size'] / 1000);
                $mb = $kb / 1000;
                alert("Error!", "File is too large. Can not be over 500kb (0.5mb)<br/>Your file is $kb kb ($mb mb)");
            }
        } else {
            alert("h", print_r($check));
        }
    }
    // Accept Fund Request
    if(isset($_POST['acceptFundRequest'])){
        if(isset($_POST['secretValue'])){
            $secret = numFilterNeg($_POST['secretValue']);
            $stmt = $db->prepare("SELECT * FROM fundRequests WHERE secret = ? AND fulfilled = 0");
            $stmt->bind_param("d", $secret);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows == 1){
                $request = $result->fetch_array(MYSQLI_ASSOC);
                $requestingUser = new User($request['requester']);
                $requestAmount = $request['requesting'];

                if($party->getVariable("partyTreasury") >= $requestAmount){
                    $requestingUser->addCampaignFinance($requestAmount);
                    $party->updateVariable("partyTreasury",$party->getVariable("partyTreasury")-$requestAmount);

                    $query = "UPDATE fundRequests SET fulfilled = 1 WHERE secret=$secret";
                    $db->query($query);

                    redirect("party.php?id=$partyID&mode=partyBank","Success!","Fulfilled request!","success");
                }
                else{
                    alert("Not enough money!","Get more money, nerd.");
                }
            }
            else{
                alert("Error!","Invalid Secret!","error");

            }
        }
        else{
            alert("Error!","No secret.","error");
        }

    }
    // Deny Fund Request
    if(isset($_POST['denyFundRequest'])){
        if(isset($_POST['secretValue'])){
            $secret = numFilter($_POST['secretValue']);
            $stmt = $db->prepare("SELECT * FROM fundRequests WHERE secret = ? AND fulfilled = 0");
            $stmt->bind_param("i",$secret);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows == 1){
                $query = "UPDATE fundRequests SET fulfilled=1 WHERE secret=$secret";
                $db->query($query);

                redirect("party.php?id=$partyID&mode=partyBank","Dismissed.","Request dismissed. Good riddance!","success");

            }
            else{
                alert("Error!","Invalid Secret!","error");

            }
        }
        else{
            alert("Error!","No secret.","error");
        }
    }
    // Make a request
    if(isset($_POST['requestSubmit'])){
        if(isset($_POST['requestAmount'])){
            $amount = numFilter($_POST['requestAmount']);
            if(isset($_POST['requestReason'])){
                $requestReason = strip_tags($_POST['requestReason']);
            }
            else{
                $requestReason = "Need funds!";
            }
            $stmt = $db->prepare("SELECT * FROM fundRequests WHERE requester = ? AND party = ? AND fulfilled=0");
            $stmt->bind_param("is",$loggedInID,$partyID);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows >= 1){
                $newRequesting = $result->fetch_array(MYSQLI_ASSOC)['requesting'] + $amount;
                $newStmt = $db->prepare("UPDATE fundRequests SET requesting = ?, reason=? WHERE requester=? AND party=? AND fulfilled=0");
                $newStmt->bind_param("isii",$newRequesting,$requestReason,$loggedInID,$partyID);
                $newStmt->execute();

                redirect("party.php?id=$partyID&mode=partyBank","Success!","Request made. Let us hope they accept it!");
            }
            else{
                $rand = random_int(-99999999,999999999);
                $newStmt = $db->prepare("INSERT INTO fundRequests (party, requester, requesting, reason, secret) VALUES(?,?,?,?, ?) ");
                $newStmt->bind_param("iiisi",$partyID,$loggedInID,$amount,$requestReason,$rand);
                $newStmt->execute();

                redirect("party.php?id=$partyID&mode=partyBank", "Success!", "Request made. Let us hope they accept it!");

            }
        } else {
            alert("Error", "Must put in an amount!");
        }
    }
    // Send out money from the treasury
    if (isset($_POST['sendFundSubmit'])) {
        if (isset($_POST['sendFundsAmount'])) {
            $amount = numFilter($_POST['sendFundsAmount']);
            if (isset($_POST['partySearch'])) {
                $user = new User(numFilter($_POST['partySearch']));
                if ($user->isUser && $user->getVariable("party") == $partyID) {
                    if ($amount <= $party->getVariable("partyTreasury")) {
                        $party->updateVariable("partyTreasury", $party->getVariable("partyTreasury") - $amount);
                        $user->addCampaignFinance($amount);
                        redirect("party.php?id=$partyID&mode=partyBank", "Success!", "Sent out $" . number_format($amount) . " to " . $user->getVariable("politicianName") . "!");
                    } else {
                        alert("Error!", "Not enough money, dipshitfucklord..", "error");
                    }
                } else {
                    alert("Error!", "Can not find that user within your party.", "error");
                }

            }
        }
    }
    // Send out money to the treasury
    if (isset($_POST['donateFundSubmit'])) {
        if (isset($_POST['donateFundsAmount'])) {

            $amount = numFilter($_POST['donateFundsAmount']);
            if ($amount <= $loggedInUser->getVariable("campaignFinance")) {
                $party->updateVariable("partyTreasury", $party->getVariable("partyTreasury") + $amount);
                $loggedInUser->addCampaignFinance(-$amount);
                redirect("party.php?id=$partyID&mode=partyBank", "Success!", "Sent out $" . number_format($amount) . " to the party!");
            } else {
                alert("Error!", "Not enough money, fuckwad.", "error");
            }
        }
    }

}





