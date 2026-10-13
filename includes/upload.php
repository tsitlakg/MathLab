<?php
/**
 * upload.php
 * Helper for safely handling files uploaded by tutors.
 */

/** File extensions that tutors are allowed to upload. */
const ALLOWED_UPLOAD_EXTENSIONS = [
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
    'odt', 'ods', 'odp', 'txt', 'rtf', 'zip', 'png', 'jpg', 'jpeg',
];

/**
 * Builds a safe file name for an uploaded file, or returns null if the
 * file type is not allowed.
 *
 * - Only whitelisted extensions are accepted, so nobody can upload a
 *   .php script and run it on the server.
 * - Characters other than letters, digits, dot, underscore and dash are
 *   replaced, and a timestamp prefix avoids name collisions.
 */
function safe_upload_name(string $originalName): ?string
{
    $originalName = basename($originalName);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, ALLOWED_UPLOAD_EXTENSIONS, true)) {
        return null;
    }

    return time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
}
