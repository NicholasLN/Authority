<?php
include 'php/functions.php';
if ($_SESSION['loggedIn']) {
    redirect('index');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Register | AUTHORITY</title>
    <? echoHeader(); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<? echoNavBar() ?>
<body>
<div class="main">
    <div class="gameContainer">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm">
                <br/>
                <h1>Register Now!</h1>
                <h6>Please do not put any revealing information or important passwords here.
                    <br/>While I try, I can not guarantee absolute safety. Passwords are hashed in the database, so that
                    I can't even see them.
                </h6>
                <div>
                    <br/>
                    <h3>Login Information</h3>
                    <h6>You will login with this information.</h6>
                </div>
                <form method="POST" class="tableForm">
                    <table class="table table-striped table-responsive">
                        <tr>
                            <td><b>Username</b></td>
                            <td><input type='text' name='username' class='form-control'
                                       placeholder='Enter Username Here' required pattern="[^()/><\][\\\x22,;|]+"></td>
                        </tr>
                        <tr>
                            <td><b>Password</b></td>
                            <td><input type='password' name='password' class='form-control'
                                       placeholder='Enter Password Here' required pattern="[^()/><\][\\\x22,;|]+"></td>
                        </tr>
                    </table>
                    <br/>
                    <h3>Politician Information</h3>
                    <h6>This will be your ingame information. Knowing online communities, don't put your real
                        information. Just don't.</h6>
                    <hr/>
                    <table class="table table-striped table-responsive">
                        <tr>
                            <td><b>Politician Name</b></td>
                            <td><input type='text' name='politicianName' class='form-control'
                                       placeholder='Enter Name Here' required pattern="[^()/><\][\\\x22,;|]+"></td>
                        </tr>
                        <tr>
                            <td><b>State/Region</b></td>
                            <td><select class="jqs2" style="width:100%;" name="regionSelect">
                                    <?php
                                    $query = mysqli_query($db, "SELECT * FROM states WHERE active=1");
                                    while ($stateRow = mysqli_fetch_assoc($query)) {
                                        $abv = $stateRow['abbreviation'];
                                        $name = $stateRow['name'];
                                        echo "<option value='$abv'>$name</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Economic Positions</b></td>
                            <td>
                                <select class="form-control" name="ecoPos">
                                    <option value="-5">Collectivism</option>
                                    <option value="-4">Socialism</option>
                                    <option value="-3">Left Wing</option>
                                    <option value="-2">Slightly Left Wing</option>
                                    <option value="-1">Center Left</option>
                                    <option value="0" selected>Mixed Capitalism</option>
                                    <option value="1">Center Right</option>
                                    <option value="2">Slightly Right Wing</option>
                                    <option value="3">Right Wing</option>
                                    <option value="4">Capitalism</option>
                                    <option value="5">Libertarianism</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Social Positions</b></td>
                            <td>
                                <select class="form-control" name="socPos">
                                    <option value="-5">Anarchism</option>
                                    <option value="-4">Communalism</option>
                                    <option value="-3">Left Wing</option>
                                    <option value="-2">Slightly Left Wing</option>
                                    <option value="-1">Center Left</option>
                                    <option value="0" selected>Centrist</option>
                                    <option value="1">Center Right</option>
                                    <option value="2">Slightly Right Wing</option>
                                    <option value="3">Right Wing</option>
                                    <option value="4">Authoritarian Right</option>
                                    <option value="5">Totalitarian Right</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><b>ReCAPTCHA</b></td>
                            <td>
                                <div class="g-recaptcha" data-sitekey="6LdN68EUAAAAAC4IWflwA1qCEUP6IwZTfFCGQp45"
                                     data-callback="enableBtn"></div>
                            </td>
                        </tr>
                    </table>
                    <input class="btn btn-primary" type="submit" value="Register" name="registerSubmit">
                </form>
            </div>
            <div class="col-sm"></div>
        </div>
        <br/>
        <br/>
        <br/>
    </div>
    <? echoFooter() ?>
</div>
</body>
<script>
    $(document).ready(function () {
        $('.jqs2').select2();
    });
</script>
</html>
<?php
// if user clicks register button
if (isset($_POST['registerSubmit'])) {

    $captcha = $_POST['g-recaptcha-response'];
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LdN68EUAAAAALCciY7URV9YFcbJcUy4wkn5lDsX&response=" .
        $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']), true);
    if ($response['success']) {
        if (isset($_POST['username']) && !empty($_POST['username'])) {
            if (isset($_POST['password']) && !empty($_POST['username'])) {
                if (isset($_POST['politicianName']) && !empty($_POST['politicianName'])) {
                    register($_POST['username'], $_POST['password'], $_POST['politicianName'], $_POST['ecoPos'], $_POST['socPos'], $_POST['regionSelect']);

                } else {
                    alert("Error!", "Try putting in a politician name. It's required, ya know?");
                }
            } // User provides no password
            else {
                alert("Error!", "You've supplied no password, ye halfwit scum!");
            }
        } // User provides no username.
        else {
            alert("Error!", "You've supplied no username, ye fucktard!");
        }
    } else {
        alert("Uhhh...", "Your captcha failed...");
    }


}


?>

