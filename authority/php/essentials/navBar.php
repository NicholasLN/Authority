<?php

function echoNavBar(): void
{
    global $loggedInRow;
    global $loggedInID;

    if ($_SESSION['loggedIn'] == False) {
        echo <<<NAV
     
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class='navbar-brand' href='index.php' style='margin-left:15px'>
                <b>AUTHORITY<small>3.0 (WIP)</small></b>
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
NAV;
    } else {
        echo "
    
        <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
            <a class='navbar-brand' href='index.php' style='margin-left:15px'>
                <b>AUTHORITY<small>3.0 (WIP)</small></b>
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
                                " . $loggedInRow['politicianName'] . "
                            </a>              
                            <ul class='dropdown-menu'>
                                <a class='dropdown-item' href='politician.php?id=" . $loggedInRow['id'] . "'>Profile</a>
                                <a class='dropdown-item' href='editprofile.php'>Edit Profile</a>
                                <form method='post'><input class='dropdown-item' type='submit' value='Log Out' name='logout'/></form>
                            </ul>            
                        </li>
                        <li class='dropdown'>
                            <a class='nav-link dropdown-toggle' id='navBarDrop' role='button' data-toggle='dropdown'>
                                <i class='fas fa-flag'></i>
                                " . $loggedInRow['country'] . "
                            </a>              
                            <ul class='dropdown-menu'>
                                <a class='dropdown-item' href='politicalparties.php?country=" . $loggedInRow['country'] . "'>Political Parties</a>
                            </ul>            
                        </li>
                    </ul>
                </div>
            </div>
        </nav>   
    ";
    }
}
