<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
global $db;

function osu_connectSQL() {
    global $db;
    require_once(__DIR__ . DIRECTORY_SEPARATOR . '../config.php');
    if (!$db) $db = new mysqli($sqlHost, $sqlUser, $sqlPassword, $sqlDatabase);
    return $db;
}

function osu_getResult(&$stmt) {
    $result = array();
    $stmt->store_result();

    for ($i = 0; $i < $stmt->num_rows; $i++) {
        $meta = $stmt->result_metadata();
        $params = array();

        while ($field = $meta->fetch_field()) {
            $params[] = &$result[$i][$field->name];
        }

        call_user_func_array(array($stmt, 'bind_result'), $params);
        $stmt->fetch();
    }

    $stmt->close();
    return $result;
}

function osu_getUserDataById($userId) {
    $database = osu_connectSQL();
    
    $stmt = $database->prepare('SELECT * FROM `users` WHERE `id` = ?');
    if (!$stmt):
        error_log($database->error);
        die($database->error);
    endif;

    $stmt->bind_param('i', $userId);
    if (!$stmt->execute()) {
        error_log('Failed to execute $stmt - ' . $stmt->error);
        die('Failed to execute $stmt');
    }

    return osu_getResult($stmt)[0];
}

function osu_getPlayerHighScore($mapHash, $userId) {
    $database = osu_connectSQL();
    
    $stmt = $database->prepare('SELECT * FROM `scores` WHERE `mapHash` = ? AND `playerId` = ? AND `outdated` = 0 AND `pass` = 1');
    if (!$stmt):
        error_log($database->error);
        die($database->error);
    endif;

    $stmt->bind_param('si', $mapHash, $userId);
    if (!$stmt->execute()) {
        error_log('Failed to execute $stmt - ' . $stmt->error);
        die('Failed to execute $stmt');
    }

    return osu_getResult($stmt)[0];
}

function osu_getUserDataByName($username) {
    $database = osu_connectSQL();
    
    $stmt = $database->prepare('SELECT * FROM `users` WHERE `username` = ?');
    if (!$stmt):
        error_log($database->error);
        die($database->error);
    endif;

    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) {
        error_log('Failed to execute $stmt - ' . $stmt->error);
        die('Failed to execute $stmt');
    }

    return osu_getResult($stmt)[0];
}

function osu_getScores($mapHash) {
    $database = osu_connectSQL();
    
    $stmt = $database->prepare('SELECT * FROM `scores` WHERE `mapHash` = ? AND `outdated` = 0 AND `pass` = 1 ORDER BY score DESC');
    if (!$stmt):
        error_log($database->error);
        die($database->error);
    endif;

    $stmt->bind_param('s', $mapHash);
    if (!$stmt->execute()) {
        error_log('Failed to execute $stmt - ' . $stmt->error);
        die('Failed to execute $stmt');
    }

    return osu_getResult($stmt);
}

function osu_checkMapHash($mapHash) {
    $database = osu_connectSQL();
    
    $stmt = $database->prepare('SELECT * FROM `banned_maps` WHERE `mapHash` = ?');
    if (!$stmt):
        error_log($database->error);
        die($database->error);
    endif;

    $stmt->bind_param('s', $mapHash);
    if (!$stmt->execute()) {
        error_log('Failed to execute $stmt - ' . $stmt->error);
        die('Failed to execute $stmt');
    }

    if (osu_getResult($stmt)[0]) {
        return true;
    } else {
        return false;
    }

    return false;
}