<?php
require_once('../../inc/database.php');

$mapHash = $_REQUEST['c'];

if (!isset($_REQUEST['c'])) die('-1');
if (strlen($mapHash) != 32) die('-1');
if (osu_checkMapHash($mapHash)) die('-1');

$scores = osu_getScores($mapHash);

if (count($scores) != 0) {
    for ($i = 0; $i < count($scores); $i++) {
        $score = $scores[$i];
        $playerData = osu_getUserDataById($score['playerId']);

        if ($playerData['banned'] != 0) return;

        $perfect = 'False';
        if ($score['perfect'] != 0) $perfect = 'True';

        echo $score['id'] . ':' . $playerData['username']  . ':' . $score['score'] . ':' . $score['combo'] . ':' . $score['count50'] . ':' . $score['count100'] . ':' . $score['count300'] . ':' . $score['countMiss'] . ':' . $score['countKatu'] . ':' . $score['countGeki'] . ':' . $perfect . ':' . $score['mods'] . "\n";
    }
}