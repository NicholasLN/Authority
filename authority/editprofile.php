<?php
include 'php/functions.php';
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {

} else {
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
                <div class="col-sm-7"><br/>
                    <h2>Edit Profile</h2>
                    <hr/>
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
                                <td><b>Change Profile Picture</b></td>
                                <td>
                                    <input class="form-control" type="file" name="newProfilePicture" accept="image/*"/>
                                    <p style="text-align:left;margin-bottom:1px;margin-left:2px;">Accepted File Types:
                                        .png, .jpeg, .gif, .bmp</p>
                                </td>
                                <td><input class="btn btn-primary" value="Change Picture" type="submit"
                                           name="newProfilePicSubmit"/></td>
                            </tr>
                            <tr>
                                <td><b>Change Profile Biography</b></td>
                                <td><textarea rows='10' class='form-control'
                                              name="newProfileBio"><?php echo $loggedInRow['bio'] ?></textarea></td>
                                <td><input class="btn btn-primary" value="Change Bio" type="submit"
                                           name="newBioSubmit"/></td>
                            </tr>
                        </table>
                    </form>

                </div>
                <div class="col-sm"></div>
            </div>
        </div>
        <? echoFooter() ?>
    </div>
    </body>
    </html>

<?
if (isset($_POST)) {
    if (isset($_POST['newProfilePicSubmit'])) {
        $directory = "images/userPics";
        $file = $directory . basename($_FILES['newProfilePicture']["name"]);
        $imageFileType = pathinfo($file, PATHINFO_EXTENSION);
        $check = getimagesize($_FILES['newProfilePicture']["tmp_name"]);
        // is image
        if (($check !== false)) {
            if ($_FILES['newProfilePicture']['size'] < 500000) {
                $name = $loggedInRow['politicianName'];
                if ($loggedInRow['profilePic'] != "images/userPics/default.jpg") {
                    $pic = substr($loggedInRow['profilePic'], 0, strpos($loggedInRow['profilePic'], "?ver="));
                    unlink($pic);
                }
                try {
                    $rand = random_int(PHP_INT_MIN, PHP_INT_MAX);
                } catch (Exception $e) {
                    $rand = 92393;
                }
                $moved = move_uploaded_file($_FILES["newProfilePicture"]["tmp_name"], "$directory/$name.$imageFileType");

                $fileString = "images/userPics/$name.$imageFileType?ver=$rand";
                $user->updateVariable("profilePic", $fileString);

                if ($moved) {
                    redirect("politician?id=$loggedInID");
                } else {
                    echo $_FILES['file']['error'];
                }
            } else {
                $kb = round($_FILES['newProfilePicture']['size'] / 1000);
                $mb = $kb / 1000;
                alert("Error!", "File is too large. Can not be over 500kb (0.5mb)<br/>Your file is $kb kb ($mb mb)");
            }
        } else {
            alert("h", print_r($check));
        }
    }
    if (isset($_POST['newBioSubmit'])) {
        if (isset($_POST['newProfileBio'])) {
            $newBio = strip_tags($_POST['newProfileBio']);
            $newBio = preg_replace('/(\r\n|\n|\r){3,}/', "$1$1$1", $newBio);

            $user->updateVariable("bio", $newBio);

            redirect("politician.php?id=$loggedInID");

        }

    }
}





