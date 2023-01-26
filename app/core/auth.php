<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    header('Location:' . $host);
}