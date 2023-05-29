<?php


ob_start();

require_once "./Cache/RedisMySQLSessionHandler.php";

$handler = new RedisMySQLSessionHandler();
session_set_save_handler($handler, true);

session_start();

echo "session_id: " . session_id() . " ";
$count = isset($_SESSION['count']) ? $_SESSION['count'] : 0;

$_SESSION['count'] = ++$count;

echo $count;