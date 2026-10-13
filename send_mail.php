<?php
/**
 * send_mail.php
 * Processes the contact form (communication.php) and sends an e-mail to
 * every user with the tutor role, using PHP's mail() function.
 */
require_once __DIR__ . '/includes/auth.php';
require_login();

// Only accept form submissions (POST), not direct visits
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: communication.php');
    exit;
}

// Read the submitted form values
$sender  = trim($_POST['sender'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($sender === '' || $subject === '' || $message === '') {
    header('Location: communication.php');
    exit;
}

// Reject invalid addresses. This also blocks header injection, where a
// newline in the address would let an attacker add extra mail headers.
if (!filter_var($sender, FILTER_VALIDATE_EMAIL)) {
    header('Location: communication.php');
    exit;
}

// The subject goes into a mail header too, so strip line breaks from it
$subject = str_replace(["\r", "\n"], ' ', $subject);

// Find the recipients: every tutor's login name is an e-mail address
$db = get_db();
$result = $db->query("SELECT loginname FROM users WHERE role = 'tutor'");

$body = "Νέο μήνυμα από τη φόρμα επικοινωνίας του MathLab\n\n"
      . "Αποστολέας: {$sender}\n"
      . "Θέμα: {$subject}\n\n"
      . "Κείμενο:\n{$message}\n";

// Mail headers: fixed From address, and Reply-To so tutors can answer directly
$headers = 'From: ' . MAIL_FROM . "\r\n"
         . 'Reply-To: ' . $sender . "\r\n";

while ($row = $result->fetch_assoc()) {
    // mail() only delivers real e-mail if the server has a configured
    // mail transport (SMTP). On a default local XAMPP it silently does nothing.
    @mail($row['loginname'], '[MathLab] ' . $subject, $body, $headers);
}

header('Location: communication.php?sent=1');
exit;
