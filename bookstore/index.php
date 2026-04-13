<?php
/**
 * index.php — BookVault Home Page
 *
 * This is the main landing page. It:
 * - Loads featured books from XML (first 8 books)
 * - Displays a hero banner with CTAs
 * - Shows category cards
 * - Displays a "Featured Books" grid section
 */

// Start session & load helpers
session_start();
require_once 'php/functions.php';

$page_title  = 'BookVault — Millions of Books, One Destination';
$active_page = 'home';

// Load first 8 books for featured section
$all_books      = load_books_from_xml();
$featured_books = array_slice($all_books, 0, 8);

// Show welcome message if just registered
$welcome_msg = $_SESSION['welcome_message'] ?? '';
unset($_SESSION['welcome_message']);

// Show success if just added to cart
$added_book = isset($_GET['added']) ? htmlspecialchars($_GET['added']) : '';
?>
<?php include 'php/header.php'; ?>

<main class="main-content">

    <!-- ===== ALERTS ===== -->
    <?php if ($welcome_msg): ?>
    <div class="alert alert-success" style="margin:0; border-radius:0; padding:14px 24px;">
        🎉 <?php echo htmlspecialchars($welcome_msg); ?>
    </div>
    <?php endif; ?>

    <?php if ($added_book): ?>
    <div class="alert alert-success" style="margin:0; border-radius:0; padding:14px 24px;">
        ✅ <strong><?php echo $added_book; ?></strong> has been added to your cart!
        <a href="cart.php" style="margin-left:12px; color:#067d62; font-weight:700; text-decoration:underline;">View Cart →</a>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out'): ?>
    <div class="alert alert-info" style="margin:0; border-radius:0; padding:14px 24px;">
        👋 You have been logged out successfully. See you soon!
    </div>
    <?php endif; ?>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero">
        <p class="hero-subtitle">📚 Welcome to BookVault</p>
        <h1>Discover Your Next<br><em>Favorite Book</em></h1>
        <p>Explore millions of titles across every genre. Classic literature, modern thrillers, bestselling self-help — all in one place.</p>
        <div class="hero-actions">
            <a href="books.php" class="btn btn-primary">Browse All Books →</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-outline">Join Free Today</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== STATS STRIP ===== -->
    <div class="stats-strip">
        <div class="container">
            <div class="stat-item"><div class="stat-number">1M+</div><div class="stat-label">Books Available</div></div>
            <div class="stat-item"><div class="stat-number">50K+</div><div class="stat-label">Happy Readers</div></div>
            <div class="stat-item"><div class="stat-number">200+</div><div class="stat-label">Categories</div></div>
            <div class="stat-item"><div class="stat-number">Fast</div><div class="stat-label">Delivery</div></div>
        </div>
    </div>

    <!-- ===== CATEGORY CARDS ===== -->
    <div class="categories-strip">
        <a href="books.php?category=English Story Books" class="cat-card">
            <div class="cat-icon">📖</div>
            <div class="cat-name">English Story Books</div>
        </a>
        <a href="books.php?category=Motivational and Positive Thinking" class="cat-card">
            <div class="cat-icon">💡</div>
            <div class="cat-name">Motivational &amp; Positive Thinking</div>
        </a>
        <a href="books.php?category=Engineering Books" class="cat-card">
            <div class="cat-icon">⚙️</div>
            <div class="cat-name">Engineering Books</div>
        </a>
        <a href="books.php?category=Programming and Technology" class="cat-card">
            <div class="cat-icon">💻</div>
            <div class="cat-name">Programming &amp; Technology</div>
        </a>
        <a href="books.php?category=Children's Books" class="cat-card">
            <div class="cat-icon">🧒</div>
            <div class="cat-name">Children's Books</div>
        </a>
    </div>

    <!-- ===== FEATURED BOOKS SECTION ===== -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Featured <span>Books</span></h2>
            <a href="books.php" class="section-link">See all books →</a>
        </div>

        <div class="books-grid">
            <?php foreach ($featured_books as $index => $book): ?>
            <div class="book-card">

                <!-- Badge: "Best Seller" for high-rated books -->
                <?php if ($book['rating'] >= 4.8): ?>
                <span class="book-card-badge">⭐ Best Seller</span>
                <?php elseif ($index < 3): ?>
                <span class="book-card-badge">🔥 Hot</span>
                <?php endif; ?>

                <!-- Book Cover Image -->
                <img
                    src="<?php echo htmlspecialchars($book['image']); ?>"
                    alt="<?php echo htmlspecialchars($book['title']); ?>"
                    class="book-card-img"
                    loading="lazy"
                    onerror="this.style.background='#f0f0f0'">

                <div class="book-card-body">
                    <span class="book-category"><?php echo htmlspecialchars($book['category']); ?></span>
                    <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                    <div class="book-author">by <?php echo htmlspecialchars($book['author']); ?></div>
                    <div class="book-rating">
                        <?php echo render_stars($book['rating']); ?>
                        <span class="rating-count">(<?php echo $book['rating']; ?>)</span>
                    </div>
                    <div class="book-price">
                        <span class="original-price"><?php echo get_fake_original($book['price'], $book['id']); ?></span>
                        <?php echo format_price($book['price']); ?>
                    </div>

                    <!-- Add to Cart form -->
                    <form action="php/add_to_cart.php" method="POST" style="margin-top:auto;">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <button type="submit" class="btn-add-cart">
                            🛒 Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ===== PROMO BANNER ===== -->
    <div style="background: linear-gradient(135deg, #ff9900 0%, #e47911 100%); padding:40px 24px; text-align:center; color:#131921;">
        <h2 style="font-family:'Playfair Display',serif; font-size:1.8rem; margin-bottom:10px;">📦 Free Shipping on Orders Over ₹999</h2>
        <p style="font-size:1rem; opacity:0.85; margin-bottom:20px;">Use code <strong>BOOKS999</strong> at checkout</p>
        <a href="books.php" class="btn" style="background:#131921; color:#ff9900; font-weight:700;">Shop Now</a>
    </div>

</main>

<?php include 'php/footer.php'; ?>
