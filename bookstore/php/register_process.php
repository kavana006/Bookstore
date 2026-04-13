<?php
/**
 * register_process.php
 * Handles the registration form POST submission.
 *
 * How it works:
 * 1. Validates all fields: name, email, password, confirm password.
 * 2. Checks that email isn't already registered.
 * 3. Hashes the password securely using PHP's password_hash().
 * 4. Appends the new user to php/users_data.php (file-based storage).
 * 5. Logs the user in immediately and redirects to home.
 *
 * NOTE: This uses a PHP file as a simple database (no MySQL needed).
 * In a production app, always use a proper database.
 */

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit();
}

// --- Get and sanitize inputs ---
$username  = trim($_POST['username']  ?? '');
$email     = trim($_POST['email']     ?? '');
$password  = trim($_POST['password']  ?? '');
$confirm   = trim($_POST['confirm_password'] ?? '');

// --- Validation ---
$errors = [];

if (empty($username) || strlen($username) < 2) {
    $errors[] = 'Full name must be at least 2 characters.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}
if (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}
if ($password !== $confirm) {
    $errors[] = 'Passwords do not match.';
}

// Special character check for username
if (!empty($username) && !preg_match('/^[a-zA-Z0-9 ]+$/', $username)) {
    $errors[] = 'Name can only contain letters, numbers, and spaces.';
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    header('Location: ../register.php');
    exit();
}

// --- Load existing users ---
$users_file = __DIR__ . '/users_data.php';
$users = [];

if (file_exists($users_file)) {
    require $users_file; // loads $users array
}

// --- Check for duplicate email ---
foreach ($users as $user) {
    if (strtolower($user['email']) === strtolower($email)) {
        $_SESSION['register_errors'] = ['An account with that email already exists. Please login.'];
        header('Location: ../register.php');
        exit();
    }
}

// --- Hash password & create new user ---
$new_user = [
    'id'            => count($users) + 1,
    'username'      => htmlspecialchars($username),
    'email'         => strtolower($email),
    'password_hash' => password_hash($password, PASSWORD_BCRYPT),
];

$users[] = $new_user;

// --- Save users back to file ---
// We store as a PHP file that can be required
$php_content = "<?php\n";
$php_content .= "// Auto-generated user database — DO NOT EDIT MANUALLY\n";
$php_content .= "\$users = " . var_export($users, true) . ";\n";

if (file_put_contents($users_file, $php_content) === false) {
    $_SESSION['register_errors'] = ['Registration failed due to a server error. Please try again.'];
    header('Location: ../register.php');
    exit();
}

// --- Auto-login the new user ---
$_SESSION['user_id']  = $new_user['id'];
$_SESSION['username'] = $new_user['username'];
$_SESSION['email']    = $new_user['email'];

// Clear errors
unset($_SESSION['register_errors']);

// Redirect to home with welcome message
$_SESSION['welcome_message'] = 'Welcome to BookVault, ' . $new_user['username'] . '! 🎉';
header('Location: ../index.php');
exit();
