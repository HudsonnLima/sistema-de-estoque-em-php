<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../app/init.php';




$url = $_GET['url'] ?? '';
if ($url === '') {
    header('Location:' . HOME);
    exit;
}

$app = new App;

?>




