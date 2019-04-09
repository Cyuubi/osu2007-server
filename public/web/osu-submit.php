<?php
require_once('../../inc/database.php');

//TODO: verify checksum?
if (isset($_REQUEST['score']) && isset($_REQUEST['pass'])) {
    if (strlen($_REQUEST['pass']) == 64) {
        $score = explode(':', $_REQUEST['score']);
        $userData = osu_getUserDataByName($score[1]);
        if ($userData) {
            if (!strcmp($userData['password'], $_REQUEST['pass'])) {
                if ($userData['banned'] != 0) return;

                $highScore = osu_getPlayerHighScore($score[0], $userData['id']);
                if ($highScore) {
                    if ($highScore['score'] > $score[9]) return;

                    $database = osu_connectSQL();

                    $stmt = $database->prepare('UPDATE `scores` SET `outdated` = ? WHERE `id` = ?');
                    if (!$stmt):
                        error_log($database->error);
                        die($database->error);
                    endif;

                    $outdated = 1;
        
                    $stmt->bind_param('ii', $outdated, $highScore['id']);
                    if (!$stmt->execute()) {
                        error_log('Failed to execute $stmt - ' . $stmt->error);
                        die('Failed to execute $stmt');
                    }
                }

                $database = osu_connectSQL();

                $stmt = $database->prepare('INSERT INTO `scores` (`mapHash`, `playerId`, `score`, `combo`, `count50`, `count100`, `count300`, `countMiss`, `countKatu`, `countGeki`, `perfect`, `mods`, `pass`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                if (!$stmt):
                    error_log($database->error);
                    die($database->error);
                endif;

                $perfect = 0;
                if (strtolower($score[11]) == 'true') $perfect = 1;

                $pass = 0;
                if (strtolower($score[14]) == 'true') $pass = 1;
    
                $stmt->bind_param('siiiiiiiiiiii', $score[0], $userData['id'], $score[9], $score[10], $score[5], $score[4], $score[3], $score[8], $score[7], $score[6], $perfect, $score[13], $pass);
                if (!$stmt->execute()) {
                    error_log('Failed to execute $stmt - ' . $stmt->error);
                    die('Failed to execute $stmt');
                }

                if (filesize($_FILES['score']['tmp_name']) != 0) {
                    move_uploaded_file($_FILES['score']['tmp_name'], '../../replay/' . $database->insert_id);
                }
            }
        }
    }
}