<?php

function register($username, $password, $politicianName, $ecoPosition, $socPosition, $state)
{
    global $db;
    $username = trim($username);
    if (is_numeric($ecoPosition) || in_range($ecoPosition, -5, 5)) {
        if (is_numeric($socPosition) || in_range($socPosition, -5, 5)) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $initCookie = $_COOKIE['sessionIdentifier'];
            $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
            $country = getStateByAbbreviation($state)['country'];

            if ((getNumRows("SELECT * FROM users WHERE regIP='$ipAddress'") == 0) &&
                (getNumRows("SELECT * FROM users WHERE username='$username'") == 0) &&
                (getNumRows("SELECT * FROM users WHERE politicianName='$politicianName'") == 0)) {

                $stmt = $db->prepare("INSERT into users(
                username, password, regcookie, currentcookie, regip, currentip, 
                politicianname, state, country, 
                ecopos, socpos) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param("sssssssssii", $username, $passwordHashed, $initCookie, $initCookie, $ipAddress, $ipAddress, $politicianName, $state, $country, $ecoPosition, $socPosition);
                $stmt->execute();

                if (!$stmt->error) {
                    $registerID = User::withUsername($username)->getUserRow()['id'];
                    echo $username;

                    $_SESSION['loggedIn'] = true;
                    $_SESSION['loggedInID'] = $registerID;
                    redirect('politician?id=' . $_SESSION['loggedInID']);

                }
            } else {
                alert("Error!", "Sorry, something went wrong. Please message a member of moderation or the developer.");

            }
        }
    }


}

function login($username, $password)
{
    global $db;

    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $cookie = $_COOKIE['sessionIdentifier'];

    if (isset($username) && !empty($username)) {
        if (isset($password) && !empty($password)) {
            $doesUsernameExistStmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $doesUsernameExistStmt->bind_param("s", $username);
            $doesUsernameExistStmt->execute();
            $userRows = $doesUsernameExistStmt->get_result()->num_rows;
            //if the username they supplied exists
            if ($userRows == 1) {
                $theirPasswordStmt = $db->prepare("SELECT * FROM users WHERE username = ?");
                $theirPasswordStmt->bind_param("s", $username);
                $theirPasswordStmt->execute();
                // grabbed password
                $userRow = $theirPasswordStmt->get_result()->fetch_assoc();
                $theirPassword = $userRow['password'];
                if (password_verify($password, $theirPassword)) {
                    // the login password matches their password
                    mysqli_query($db, "UPDATE users SET currentCookie=$cookie, currentIP=$ipAddress WHERE username=$username");
                    $_SESSION['loggedIn'] = true;
                    $_SESSION['loggedInID'] = $userRow['id'];
                    redirect('politician?id=' . $_SESSION['loggedInID']);
                } else {
                    alert("Error!", "Invalid password.");
                }
            } else {
                alert("Error!", "No such user exists.");
            }
        }

    }


}

function logout()
{
    session_destroy();
    redirect('index');
}