<?php
session_start();
require_once 'php/functions.php';

$page_title  = 'Browse Books — BookVault';
$active_page = 'books';

// --- Read filter params from URL ---
$search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// --- Load books with filters applied ---
$books = load_books_from_xml($category, $search);

// --- Build page subtitle ---
if ($search) {
    $page_subtitle = 'Search results for: "' . htmlspecialchars($search) . '"';
} elseif ($category) {
    $page_subtitle = 'Category: ' . htmlspecialchars($category);
} else {
    $page_subtitle = 'All Books — Explore our complete collection';
}

$added_book = isset($_GET['added']) ? htmlspecialchars($_GET['added']) : '';

// All unique categories (for filter buttons)
$all_books_raw  = load_books_from_xml();
$all_categories = array_unique(array_column($all_books_raw, 'category'));
sort($all_categories);
?>
<?php include 'php/header.php'; ?>

<main class="main-content">

    <!-- Success message -->
    <?php if ($added_book): ?>
    <div class="alert alert-success" style="margin:0; border-radius:0; padding:14px 24px;">
        ✅ <strong><?php echo $added_book; ?></strong> added to cart!
        <a href="cart.php" style="margin-left:12px; color:#067d62; font-weight:700; text-decoration:underline;">View Cart →</a>
    </div>
    <?php endif; ?>

    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header">
        <h1>📚 Browse Our Collection</h1>
        <p><?php echo $page_subtitle; ?></p>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep">›</span>
            <span>Books</span>
            <?php if ($category): ?>
            <span class="breadcrumb-sep">›</span>
            <span><?php echo htmlspecialchars($category); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== FILTER BAR ===== -->
    <div class="filter-bar">
        <div class="container">
            <span class="filter-label">Filter:</span>

            <!-- "All" button -->
            <a href="books.php"
               class="filter-btn <?php echo empty($category) && empty($search) ? 'active' : ''; ?>">
               All
            </a>

            <!-- Category filter buttons -->
            <?php foreach ($all_categories as $cat): ?>
            <a href="books.php?category=<?php echo urlencode($cat); ?>"
               class="filter-btn <?php echo $category === $cat ? 'active' : ''; ?>">
               <?php echo htmlspecialchars($cat); ?>
            </a>
            <?php endforeach; ?>

            <!-- Results count -->
            <span class="results-count"><?php echo count($books); ?> book(s) found</span>
        </div>
    </div>

    <!-- ===== BOOKS GRID ===== -->
    <div class="section" style="padding-top:32px;">

        <?php if (empty($books)): ?>
        <!-- Empty state -->
        <div class="empty-cart" style="max-width:500px; margin:0 auto;">
            <div class="empty-cart-icon">🔍</div>
            <h2>No Books Found</h2>
            <p>
                <?php if ($search): ?>
                    No books match your search for <strong>"<?php echo htmlspecialchars($search); ?>"</strong>.
                <?php else: ?>
                    No books found in this category.
                <?php endif; ?>
            </p>
            <a href="books.php" class="btn btn-primary">View All Books</a>
        </div>

        <?php else: ?>
        <div class="books-grid">
            <?php foreach ($books as $book): ?>
            <div class="book-card">

                <!-- Best Seller badge -->
                <?php if ($book['rating'] >= 4.8): ?>
                <span class="book-card-badge">⭐ Best Seller</span>
                <?php endif; ?>

                <!-- Cover image -->
                <img
                    src="<?php echo htmlspecialchars($book['image']); ?>"
                    alt="<?php echo htmlspecialchars($book['title']); ?>"
                    class="book-card-img"
                    loading="lazy"
                    onerror="this.style.background='#f0f0f0'">

                <div class="book-card-body">
                    <!-- Category -->
                    <span class="book-category"><?php echo htmlspecialchars($book['category']); ?></span>

                    <!-- Title -->
                    <div class="book-title" title="<?php echo htmlspecialchars($book['title']); ?>">
                        <?php echo htmlspecialchars($book['title']); ?>
                    </div>

                    <!-- Author -->
                    <div class="book-author">by <?php echo htmlspecialchars($book['author']); ?></div>

                    <!-- Star rating -->
                    <div class="book-rating">
                        <?php echo render_stars($book['rating']); ?>
                        <span class="rating-count">(<?php echo $book['rating']; ?>)</span>
                    </div>

                    <!-- Description (truncated) -->
                    <div style="font-size:0.78rem; color:#777; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                        <?php echo htmlspecialchars($book['description']); ?>
                    </div>

                    <!-- Price -->
                    <div class="book-price">
                        <span class="original-price"><?php echo get_fake_original($book['price'], $book['id']); ?></span>
                        <?php echo format_price($book['price']); ?>
                    </div>

                    <!-- Add to Cart Button -->
                    <form action="php/add_to_cart.php" method="POST" style="margin-top:auto;">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <button type="submit" class="btn-add-cart">
                            <?php echo is_in_cart($book['id']) ? '✅ In Cart' : '🛒 Add to Cart'; ?>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div><!-- /.section -->

</main>

<?php include 'php/footer.php'; ?>
