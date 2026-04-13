<?php
/**
 * register.php — User Registration Page
 *
 * Displays the registration form.
 * The form POSTs to php/register_process.php.
 *
 * Session messages:
 * - $_SESSION['register_errors'] : array of validation errors
 */

session_start();

$page_title  = 'Create Account — BookVault';
$active_page = 'register';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get validation errors from session
$errors = $_SESSION['register_errors'] ?? [];
unset($_SESSION['register_errors']);
?>
<?php include 'php/header.php'; ?>

<main class="main-content auth-page">
    <div class="auth-card">

        <!-- Logo -->
        <div class="auth-logo">
            <div class="auth-logo-text">Book<span>Vault</span></div>
        </div>

        <h2 class="auth-title">Create Your Account</h2>
        <p class="auth-subtitle">Join thousands of book lovers today — it's free!</p>

        <!-- Validation errors (array of error messages) -->
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $err): ?>
                <div>⚠️ <?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- ===== REGISTER FORM ===== -->
        <form action="php/register_process.php" method="POST" novalidate>

            <!-- Full Name -->
            <div class="form-group">
                <label for="username">Full Name</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="John Doe"
                    required
                    autocomplete="name"
                    maxlength="50"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                <div class="form-hint">Letters, numbers and spaces only</div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="you@example.com"
                    required
                    autocomplete="email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a strong password"
                    required
                    autocomplete="new-password"
                    minlength="6">
                <div class="form-hint">At least 6 characters</div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Repeat your password"
                    required
                    autocomplete="new-password">
            </div>

            <!-- Terms note -->
            <p style="font-size:0.78rem; color:#888; text-align:center; margin-bottom:10px;">
                By registering, you agree to our <a href="#" style="color:#0066c0;">Terms of Service</a> and <a href="#" style="color:#0066c0;">Privacy Policy</a>.
            </p>

            <!-- Submit -->
            <button type="submit" class="btn-auth">📝 Create Account</button>
        </form>

        <div class="auth-divider">or</div>

        <div class="auth-switch">
            Already have an account?
            <a href="login.php">Sign in here →</a>
        </div>

    </div>
</main>

<?php include 'php/footer.php'; ?>
