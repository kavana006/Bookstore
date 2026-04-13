<?php
/**
 * logout.php
 * Destroys the current user session and redirects to home.
 *
 * This clears:
 * - user_id, username, email from session
 * - Destroys the entire session
 * NOTE: The cart is also cleared on logout (sessions are per-login).
 */

session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home
header('Location: ../index.php?msg=logged_out');
exit();
