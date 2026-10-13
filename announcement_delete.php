<?php
/**
 * announcement_delete.php
 * Deletes one announcement from the database. Tutors only.
 */
require_once __DIR__ . '/includes/auth.php';
require_tutor();

// Read the id from the URL; (int) makes sure it is a number
$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $db = get_db();
    $stmt = $db->prepare('DELETE FROM announcements WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: announcement.php');
exit;
