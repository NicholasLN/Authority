<?php

function echoNavBar(): void
{
    global $loggedInRow;
    global $loggedInID;

    if ($_SESSION['loggedIn'] == False) {
        echo <<<NAV
     
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index" style="margin-left:15px"><b>AUTHORITY<small>3.0 (WIP)</small></b></a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#myNavbar">
                <span class="sr-only"></span>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="container-fluid">
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="nav-item dropdown">
                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                                    <i class="fas fa-sign-in-alt"></i>
                                    LOG IN
                                </a>
                                <ul class="dropdown-menu" style="width:200px">
                                    <li class="px-3 py-2" style=max-width:100%>
                                        <form method="POST">
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <input name='username' type="username" class="form-control" id="username"
                                                placeholder="Username">
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input name='password' type="password" class="form-control" id="password" 
                                                placeholder="Password">
                                            </div>
                                            <hr/>
                                            <input type="submit" class="btn btn-primary" name="signIn" value="Sign In"/>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
NAV;
    } else {
        echo "
    
        <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
            <a class='navbar-brand' href='index' style='margin-left:15px'><b>AUTHORITY<small>3.0 (WIP)</small></b></a>
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
                                <a class='dropdown-item' href='politician?id=" . $loggedInRow['id'] . "'>Profile</a>
                                <a class='dropdown-item' href='editprofile'>Edit Profile</a>
                                <form method='post'><input class='dropdown-item' type='submit' value='Log Out' name='logout'/></form>
                            </ul>            
                        </li>
                        <li class='dropdown'>
                            <a class='nav-link dropdown-toggle' id='navBarDrop' role='button' data-toggle='dropdown'>
                                <i class='fas fa-flag'></i>
                                " . $loggedInRow['country'] . "
                            </a>              
                            <ul class='dropdown-menu'>
                                <a class='dropdown-item' href='politicalparties?country=" . $loggedInRow['country'] . "'>Political Parties</a>
                            </ul>            
                        </li>
                    </ul>
                </div>
            </div>
        </nav>   
    ";
    }
}
