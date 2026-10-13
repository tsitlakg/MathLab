<?php
/**
 * user_delete.php
 * Deletes a user. Tutors only. You cannot delete your own account or the
 * last remaining tutor, so the site can never be left without an admin.
 */
require_once __DIR__ . '/includes/auth.php';
require_tutor();

$id = (int) ($_GET['id'] ?? 0);
$db = get_db();

// Only delete if the id is valid and it is not the logged-in tutor
if ($id > 0 && $id !== (int) current_user()['id']) {
    $stmt = $db->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $target = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $canDelete = true;
    if ($target && $target['role'] === 'tutor') {
        $countResult = $db->query("SELECT COUNT(*) AS n FROM users WHERE role = 'tutor'");
        $count = (int) $countResult->fetch_assoc()['n'];
        if ($count <= 1) {
            $canDelete = false; // never leave the site without a tutor
        }
    }

    if ($canDelete) {
        $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: users.php');
exit;
