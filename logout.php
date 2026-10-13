<?php
/**
 * logout.php
 * Ends the user's session and redirects to the login page.
 */
require_once __DIR__ . '/includes/auth.php';
destroy_session();
header('Location: login.php');
exit;
