<?php
/**
 * login_process.php
 * Handles the login form POST submission.
 *
 * How it works:
 * 1. Validates that email + password are not empty.
 * 2. Reads users.php (our simple file-based user store) to find the user.
 * 3. Uses password_verify() to check the hashed password.
 * 4. On success: sets session variables and redirects to home or intended page.
 * 5. On failure: stores error in session and redirects back to login.php.
 *
 * NOTE: In a real app, you'd use a MySQL database.
 * For this project, users are stored in php/users_data.php as a PHP array.
 */

session_start();

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

// --- Get and sanitize inputs ---
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

// --- Basic validation ---
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both email and password.';
    header('Location: ../login.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = 'Please enter a valid email address.';
    header('Location: ../login.php');
    exit();
}

// --- Load user data from file ---
$users_file = __DIR__ . '/users_data.php';

// If no users file yet, nobody is registered
if (!file_exists($users_file)) {
    $_SESSION['login_error'] = 'No account found. Please register first.';
    header('Location: ../login.php');
    exit();
}

require $users_file; // loads $users array
// $users is an array of: ['id', 'username', 'email', 'password_hash']

// --- Find user by email ---
$found_user = null;
foreach ($users as $user) {
    if (strtolower($user['email']) === strtolower($email)) {
        $found_user = $user;
        break;
    }
}

if (!$found_user) {
    $_SESSION['login_error'] = 'No account found with that email address.';
    header('Location: ../login.php');
    exit();
}

// --- Verify password ---
if (!password_verify($password, $found_user['password_hash'])) {
    $_SESSION['login_error'] = 'Incorrect password. Please try again.';
    header('Location: ../login.php');
    exit();
}

// --- Success! Set session ---
$_SESSION['user_id']  = $found_user['id'];
$_SESSION['username'] = $found_user['username'];
$_SESSION['email']    = $found_user['email'];

// Clear any previous errors
unset($_SESSION['login_error']);

// Redirect to intended page or home
$redirect = $_SESSION['redirect_after_login'] ?? '../index.php';
unset($_SESSION['redirect_after_login']);

header('Location: ' . $redirect);
exit();
