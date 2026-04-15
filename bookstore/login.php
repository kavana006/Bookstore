<?php
session_start();

$page_title  = 'Login — BookVault';
$active_page = 'login';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Get any error messages
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

$login_required = isset($_GET['msg']) && $_GET['msg'] === 'login_required';
?>
<?php include 'php/header.php'; ?>

<main class="main-content auth-page">
    <div class="auth-card">

        <!-- Logo -->
        <div class="auth-logo">
            <div class="auth-logo-text">Book<span>Vault</span></div>
        </div>

        <h2 class="auth-title">Welcome Back!</h2>
        <p class="auth-subtitle">Sign in to access your account and cart</p>

        <!-- Alert: Login required -->
        <?php if ($login_required): ?>
        <div class="alert alert-warning">
            🔒 Please log in before adding books to your cart.
        </div>
        <?php endif; ?>

        <!-- Alert: Error -->
        <?php if ($error): ?>
        <div class="alert alert-error">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- ===== LOGIN FORM ===== -->
        <!--
            action="php/login_process.php" : sends data to the login handler
            method="POST"                  : use POST (not GET) for credentials
        -->
        <form action="php/login_process.php" method="POST" novalidate>

            <!-- Email Field -->
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

            <!-- Password Field -->
            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Your password"
                    required
                    autocomplete="current-password">
                <div class="form-hint">Minimum 6 characters</div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-auth">🔑 Sign In</button>
        </form>

        <div class="auth-divider">or</div>

        <!-- Register Link -->
        <div class="auth-switch">
            Don't have an account?
            <a href="register.php">Create one for free →</a>
        </div>

        <!-- Demo Hint -->
        <div class="alert alert-info" style="margin-top:18px; font-size:0.82rem;">
            💡 <strong>New here?</strong> Register first to create an account. No credit card needed!
        </div>

    </div>
</main>

<?php include 'php/footer.php'; ?>
