<?php include 'php/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>Index | AUTHORITY</title>
        <? echoHeader(); ?>
    </head>
    <? echoNavBar() ?>
    <body>
        <div class="main">
            <div class="gameContainer">
                <div class="row">
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <br/>
                        <img src="images/AuthorityLogoV3.png" style="width:50vh;" alt="AuthorityLogo">
                        <h1>Authority 3.0</h1>
                        <p>Authority is a political game where you slip on a banana, get shanked in prison, and die. Also, elections and shit.</p>
                        <br/>
                        <? if(!$_SESSION['loggedIn']){
                        echo '<a href="register" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Register Now!</a>';
                        }
                        ?>
                    </div>
                    <div class="col-sm"></div>
                </div>
            </div>
            <? echoFooter() ?>
        </div>
    </body>
</html>

