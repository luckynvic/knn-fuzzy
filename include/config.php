<?php
define('DEBUG', true);
$app_name = 'kNN - Fuzzy';
// initial value
$page_title = $app_name;

$route = basename($_SERVER["SCRIPT_FILENAME"], '.php');

include('db.php');
?>