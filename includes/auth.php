<?php
/**
 * auth.php
 * Session-based authentication and role checks (tutor / student).
 * Must be included at the top of every protected page, before any
 * output (echo/HTML), because it starts the session.
 */
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Returns true if there is an active, authenticated user session. */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/** Returns true if the logged-in user has the tutor role. */
function is_tutor(): bool
{
    return is_logged_in() && $_SESSION['role'] === 'tutor';
}

/** Returns an array with the details of the currently logged-in user. */
function current_user(): array
{
    return [
        'id'         => $_SESSION['user_id']    ?? null,
        'first_name' => $_SESSION['first_name'] ?? '',
        'last_name'  => $_SESSION['last_name']  ?? '',
        'role'       => $_SESSION['role']       ?? '',
    ];
}

/** Stops the page and redirects to the login page if the user is not logged in. */
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/** Stops the page with an HTTP 403 error if the user is not a tutor. */
function require_tutor(): void
{
    require_login();
    if (!is_tutor()) {
        http_response_code(403);
        die('You do not have permission to access this page (tutors only).');
    }
}

/**
 * Checks a user's credentials against the database.
 * Returns the user row if they are correct, otherwise null.
 */
function attempt_login(string $loginname, string $password): ?array
{
    $db = get_db();
    $stmt = $db->prepare('SELECT id, first_name, last_name, loginname, password, role FROM users WHERE loginname = ? LIMIT 1');
    $stmt->bind_param('s', $loginname);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return null;
}

/** Starts the session of a successfully authenticated user. */
function start_session_for(array $user): void
{
    // New session id after login, to prevent session fixation
    session_regenerate_id(true);
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name']  = $user['last_name'];
    $_SESSION['role']       = $user['role'];
}

/** Ends the user's session (logout). */
function destroy_session(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

/** Escapes text for safe output in HTML (prevents XSS). */
function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
