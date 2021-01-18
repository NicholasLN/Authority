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
        redirect("admin.php","Success!","Successfully deleted ID: $userID","?");
    }
?>
