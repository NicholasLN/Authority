<?php

function echoNavBar(): void
{
    global $loggedInRow;
    global $loggedInID;
    global $loggedInUser;

    if ($_SESSION['loggedIn'] == False) {
        ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class='navbar-brand' href='index.php' style='margin-left:15px'>
                <b>AUTHORITY<small>It's Not POWER, I Swear!</small></b>
            </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#myNavbar">
                <span class="sr-only"></span>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="nav-item active">
                            <a class="nav-link" href="login.php">LOGIN<span class="sr-only">(current)</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?
    } else {
    ?>
    
        <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
            <a class='navbar-brand' href='index.php' style='margin-left:15px'>
                <b>AUTHORITY<small>It's Not POWER, I Swear!</small></b>
            </a>
            <button type='button' class='navbar-toggler' data-toggle='collapse' data-target='#myNavbar'>
                <span class='sr-only'></span>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='container-fluid'>
                <div class='collapse navbar-collapse' id='myNavbar'>
                    <ul class='nav navbar-nav navbar-right'>
                        <li class='dropdown'>
                            <a class='nav-link dropdown-toggle' id='navBarDrop' role='button' data-toggle='dropdown'>
                                <i class='fas fa-user'></i>
                                <? echo $loggedInRow['politicianName'] ?>
                            </a>              
                            <ul class='dropdown-menu'>
                                <a class='dropdown-item' href='politician.php?id=<? echo $loggedInID ?>'>Profile</a>
                                <a class='dropdown-item' href='editprofile.php'>Edit Profile</a>
                                <form method='post'><input class='dropdown-item' type='submit' value='Log Out' name='logout'/></form>
                            </ul>            
                        </li>
                        <li class='dropdown'>
                            <a class='nav-link dropdown-toggle' id='navBarDrop' role='button' data-toggle='dropdown'>
                                <i class='fas fa-flag'></i>
                                <? echo $loggedInRow['nation'] ?>
                            </a>              
                            <ul class='dropdown-menu'>
                                <a class='dropdown-item' href='politicalparties.php?country=<? echo $loggedInRow['nation'] ?>'>Political Parties</a>
                            </ul>            
                        </li>
                        <?
                        if($loggedInRow['party'] != 0){
                            $party = new Party($loggedInRow['party']);
                            if($party->partyExists){
                                $partyName = $party->getPartyName();
                                $partyID = $party->getVariable("id");
                        ?>
                        <li class='dropdown'>
                            <a class='nav-link dropdown-toggle' id='navBarDrop' role='button' data-toggle='dropdown'>
                                <i class='fas fa-handshake'></i>
                                <? echo $partyName ?>
                            </a>
                            <ul class='dropdown-menu'>
                                <a class='dropdown-item' href='party.php?id=<? echo $partyID ?>#overview'>Party Overview</a>
                                <a class='dropdown-item' href='party.php?id=<? echo $partyID ?>&mode=members#members'>Party Members</a>
                                <a class='dropdown-item' href='party.php?id=<? echo $partyID ?>&mode=partyBank'>Party Treasury</a>
                                <?
                                if($loggedInUser->hasPartyPerm("leader")){
                                echo "                                
                                <a class='dropdown-item' href='party.php?id=$partyID&mode=partyControls'>Party Management</a>";
                                }
                                ?>
                            </ul>
                        </li>
                        <?
                            }
                        else{
                        $loggedInUser->updateVariable("party",0);}
                        }
                        echo "
                    </ul>";
                    echo"
                </div>
            </div>
        </nav>   
    ";
    }
}
