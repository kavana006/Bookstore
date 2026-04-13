<?php
/**
 * header.php
 * Reusable header include for all pages.
 * - Starts the PHP session
 * - Outputs the <head> section with CSS links
 * - Renders the navigation bar with dynamic login/logout links
 *
 * USAGE: Include at the top of every page:
 *   include('php/header.php');
 *
 * Pass $page_title before including to set the <title> tag.
 * Pass $active_page to highlight the current nav link.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default page title if not set by calling page
$page_title = isset($page_title) ? $page_title : 'BookVault — Online Book Store';
$active_page = isset($active_page) ? $active_page : '';

// Count cart items for badge
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['qty'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Favicon emoji via SVG -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📚</text></svg>">
</head>
<body>

<!-- ===== NAVIGATION BAR ===== -->
<nav class="navbar">
    <div class="navbar-top">

        <!-- Logo -->
        <a href="index.php" class="navbar-logo">Book<span>Vault</span></a>

        <!-- Search Bar -->
        <form class="navbar-search" action="books.php" method="GET">
            <input
                type="text"
                name="search"
                placeholder="Search books, authors, categories..."
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" title="Search">🔍</button>
        </form>

        <!-- Nav Links -->
        <div class="navbar-links">
            <a href="index.php" class="<?php echo $active_page === 'home' ? 'active' : ''; ?>">
                <span class="label">Home</span>
            </a>
            <a href="books.php" class="<?php echo $active_page === 'books' ? 'active' : ''; ?>">
                <span class="label">Books</span>
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Logged in: show username + logout -->
                <a href="#" title="My Account">
                    👤 <span class="label"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </a>
                <a href="php/logout.php" title="Logout">
                    🚪 <span class="label">Logout</span>
                </a>
            <?php else: ?>
                <!-- Not logged in: show login + register -->
                <a href="login.php" class="<?php echo $active_page === 'login' ? 'active' : ''; ?>">
                    🔑 <span class="label">Login</span>
                </a>
                <a href="register.php" class="<?php echo $active_page === 'register' ? 'active' : ''; ?>">
                    📝 <span class="label">Register</span>
                </a>
            <?php endif; ?>

            <!-- Cart with badge -->
            <a href="cart.php" class="btn-cart" title="My Cart">
                🛒 Cart
                <?php if ($cart_count > 0): ?>
                    <span class="cart-badge"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Category strip -->
    <div class="navbar-categories">
        <ul>
            <li><a href="books.php?category=English Story Books">📖 English Story Books</a></li>
            <li><a href="books.php?category=Motivational and Positive Thinking">💡 Motivational</a></li>
            <li><a href="books.php?category=Engineering Books">⚙️ Engineering</a></li>
            <li><a href="books.php?category=Programming and Technology">💻 Programming & Tech</a></li>
            <li><a href="books.php?category=Children's Books">🧒 Children's Books</a></li>
        </ul>
    </div>
</nav>
<!-- ===== END NAVBAR ===== -->
