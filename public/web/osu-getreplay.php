<?php
if (isset($_GET['c'])) {
    if (file_exists('../../replay/' . $_GET['c'])) {
        echo file_get_contents('../../replay/' . $_GET['c']);
    }
}