<?php include 'php/functions.php'; ?>
<?php
    if(isset($loggedInUser)){
        if($loggedInUser->getVariable("admin") != 1) {
            invalidPage("No", "Fuck off.");
        }
    }
    else{
        invalidPage();
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Authority</title>
    <? echoHeader(); ?>
</head>
<? echoNavBar() ?>
<body>
<div class="main">
    <div class="gameContainer">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-6">
                <br/>
                <form method="post">
                    <table class="table table-striped">
                        <tr>
                            <td><b>Action</b></td>
                            <td><b>Input</b></td>
                            <td><b>Submit</b></td>
                        </tr>
                        <tr>
                            <td><b>Delete User</b></td>
                            <td>
                                <label for="delete">
                                    <input id='delete' type="input" class="form-control" placeholder="User ID" name="deleteUserID"/>
                                </label>
                            </td>
                            <td>
                                <input type="submit" class="btn btn-primary" value="Delete User" name="deleteUserSubmit"/>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Assume Control of User</b></td>
                            <td>
                                <label for="control">
                                    <input id='control' type="input" class="form-control" placeholder="User ID" name="assumeControlUserID"/>
                                </label>
                            </td>
                            <td>
                                <input type="submit" class="btn btn-primary" value="Control User" name="controlUserSubmit"/>
                            </td>
                        </tr>
                    </table>
                </form>


            </div>
            <div class="col-sm"></div>
        </div>
    </div>
    <? echoFooter() ?>
</div>
</html>

<?php
    if(isset($_POST['deleteUserSubmit'])){
        $userID = numFilter($_POST['deleteUserID']);
        $user = new User($userID);

        $user->deleteUser();
        redirect("admin.php","Success!","Successfully deleted ID: $userID","success","?");
    }
    if(isset($_POST['controlUserSubmit'])){
        if($loggedInRow['admin']==1) {
            $_SESSION['loggedInID'] = $_POST['assumeControlUserID'];
            redirect("politician.php?id=" . $_SESSION['loggedInID']);
        }
    }
