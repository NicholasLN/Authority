<?php
$userPicDirectory = './../../../images/partyPics';

$files = array();
foreach (scandir($userPicDirectory) as $file) {
    if ($file !== '.' && $file !== '..') {
        $files[] = $file;
    }
}

//var_dump($files);

$photosDeleted = 0;
foreach ($files as $key => $fileName) {
    if ($fileName != "default.png" && $fileName != "independent.png") {
        $fileNameNew = "%$fileName%";
        $stmt = $db->prepare("SELECT * FROM parties WHERE partyPic like ? ");
        $stmt->bind_param('s', $fileNameNew);
        $stmt->execute();

        $rows = $stmt->get_result()->num_rows;

        if ($rows == 0) {
            unlink("./../../../images/partyPics/$fileName");
        }
    }
}
?>