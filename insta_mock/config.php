<?php
// config.php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'insta_mock_db');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);

// If DB not selected, try to create & select
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}
if (!$mysqli->select_db(DB_NAME)) {
    // try to create database
    $mysqli->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $mysqli->select_db(DB_NAME);
}

// ensure required columns/tables (safe to run each time)
$mysqli->set_charset('utf8mb4');

// function to ensure schema exists (simple)
$schemaSql = file_exists(__DIR__ . '/db.sql') ? file_get_contents(__DIR__ . '/db.sql') : '';
if ($schemaSql) {
    // Try to run statements - ignore errors if already created
    $mysqli->multi_query($schemaSql);
    // drain results
    do { if ($res = $mysqli->store_result()) { $res->free(); } } while ($mysqli->more_results() && $mysqli->next_result());
}

// safe output
function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
