<?php
// filepath: d:\wamp64\www\raiz\admin\app\views\home\logout.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();
header('Location: ' . BASE_LOGIN);
exit;