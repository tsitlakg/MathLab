<?php
/**
 * document_delete.php
 * Deletes a course document (the database row and, if it exists, the
 * file on disk). Tutors only.
 */
require_once __DIR__ . '/includes/auth.php';
require_tutor();

// Read the id from the URL; (int) makes sure it is a number
$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $db = get_db();

    $stmt = $db->prepare('SELECT file_path FROM documents WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Remove the file from disk first, then the database row
    if ($row) {
        $fullPath = __DIR__ . '/' . $row['file_path'];
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
        $stmt = $db->prepare('DELETE FROM documents WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: documents.php');
exit;
