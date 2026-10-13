<?php
/**
 * config.php
 * Central application settings: database connection details and the
 * folders where uploaded files are stored.
 *
 * The defaults match a fresh XAMPP install (user root, no password).
 * If you need different values, copy includes/config.local.example.php
 * to includes/config.local.php and change them there. config.local.php
 * is listed in .gitignore, so it is never uploaded to GitHub.
 */

// Personal settings (optional file, not tracked by git)
$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    require $localConfig;
}

// Defaults - only used if config.local.php did not define them
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_NAME') || define('DB_NAME', 'mathlab');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');

// Folders where uploaded files are stored (must be writable by the web server)
define('UPLOAD_DIR_DOCUMENTS', __DIR__ . '/../files/');
define('UPLOAD_DIR_HOMEWORK', __DIR__ . '/../assignments/');

// Sender address for e-mails sent by the contact form
defined('MAIL_FROM') || define('MAIL_FROM', 'no-reply@mathlab.tutor.gr');
