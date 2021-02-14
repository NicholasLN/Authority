<?php
$userPicDirectory = './../../../images/userPics';

$files = array();
foreach (scandir($userPicDirectory) as $file) {
    if ($file !== '.' && $file !== '..') {
        $files[] = $file;
    }
}

//var_dump($files);

$photosDeleted = 0;
foreach ($files as $key => $fileName) {
    if ($fileName != "default.jpg") {
        $fileNameNew = "%$fileName%";
        $stmt = $db->prepare("SELECT * FROM users WHERE profilePic like ? ");
        $stmt->bind_param('s', $fileNameNew);
        $stmt->execute();

        $rows = $stmt->get_result()->num_rows;

        if ($rows == 0) {
            unlink("./../../../images/userPics/$fileName");
        }
    }
}
?>