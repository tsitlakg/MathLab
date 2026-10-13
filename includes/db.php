<?php
/**
 * db.php
 * Creates and returns a single shared mysqli connection to the database,
 * using the settings from config.php.
 */
require_once __DIR__ . '/config.php';

function get_db(): mysqli
{
    // One connection per request, reused on every call
    static $conn = null;

    if ($conn === null) {
        // Make mysqli throw exceptions instead of printing warnings
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            http_response_code(500);
            // Do not leak details; show a generic message
            die('Database connection error. Please check includes/config.php and that MySQL is running.');
        }
    }

    return $conn;
}
