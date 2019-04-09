<?php
require_once('../../inc/database.php');

$isValid = false;

if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
    if (strlen($_REQUEST['password']) == 64) {
        $userData = osu_getUserDataByName($_REQUEST['username']);
        if ($userData) {
            if (!strcmp($userData['username'], $_REQUEST['username'])) {
                if (!strcmp($userData['password'], $_REQUEST['password'])) {
                    if ($userData['banned'] != 0) return;
                    $isValid = true;
                }
            }
        }
    }
}

if ($isValid) {
    echo '1';
} else {
    echo '0';
}

die();