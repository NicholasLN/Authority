<? include 'php/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <? indexMeta() ?>
    <title>Index | AUTHORITY</title>
    <? echoHeader(); ?>
</head>
<? echoNavBar() ?>

<body>
    <div class="main">
        <div class="gameContainer">
            <div class="row">
                <div class="col-sm"></div>
                <div class="col-sm-8">
                    <br />
                    <img src="images/AuthorityLogoV3.png" style="width:50vh;" alt="AuthorityLogo">
                    <h1>Authority 3.0</h1>
                    <p>Authority is a WIP political game in which users can register as a politician, run for offices,
                        run countries, play a vital part in the economic system within their countries (and others), and
                        seize power through a variety of methods--legal, or illegal.
                    </p>
                    <? if(!$_SESSION['loggedIn']){
                        echo '
                            <hr/>
                            <a href="register.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Register Now!</a>
                            <br/>
                            <br/>
                            <p>Already have an account? <a href="login.php">Login Here</a></p>';
                        }
                        ?>

                    <hr />
                    <h4>Join The Discord!</h4>
                    <iframe style='width:50%;min-height:49vh;'
                        src="https://discord.com/widget?id=600212077461897216&theme=dark" allowtransparency="true"
                        frameborder="0"
                        sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
                    <br />
                    <br />
                </div>
                <div class="col-sm"></div>
            </div>
        </div>
        <? echoFooter() ?>
    </div>
</body>

</html>