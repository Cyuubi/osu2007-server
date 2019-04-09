<?php
require_once('../inc/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo '<form action="/register.php" method="post">
Username: <input type="text" name="username"><br>
Password: <input type="password" name="password"><br>
<input type="submit" value="Register">
</form>';
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        if (osu_getUserDataByName($_POST['username'])) {
            echo 'Unfortunately, a user with this name already exists! Try a different username.';
        } else {
            if (strlen($_POST['username']) > 16) {
                echo 'Unfortunately, your username is above 16 characters! Try a different username.';
            } else {
                $database = osu_connectSQL();

                $stmt = $database->prepare('INSERT INTO `users` (`username`, `password`) VALUES (?, ?)');
                if (!$stmt):
                    error_log($database->error);
                    die($database->error);
                endif;

                $passHash = hash('sha256', $_POST['password']);
    
                $stmt->bind_param('ss', $_POST['username'], $passHash);
                if (!$stmt->execute()) {
                    error_log('Failed to execute $stmt - ' . $stmt->error);
                    die('Failed to execute $stmt');
                }

                echo 'Your account has been created, you can download the client <a href="/NostalgiaClient.zip">here</a>!';
            }
        }
    } else {
        echo 'Unfortunately, an error has occured while attempting to register your account!';
    }
}