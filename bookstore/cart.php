<?php

session_start();
require_once 'php/functions.php';

$page_title  = 'My Cart — BookVault';
$active_page = 'cart';

// Get cart items from session
$cart  = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = get_cart_total();
$count = get_cart_count();

// Shipping: free over $25, else ₹99
$shipping = $total >= 999 ? 0 : 99;
$grand_total = $total + $shipping;
?>
<?php include 'php/header.php'; ?>

<main class="main-content">

    <div class="cart-layout">

        <!-- ===== LEFT: CART ITEMS ===== -->
        <div class="cart-main">
            <h1>🛒 My Shopping Cart</h1>
            <p class="text-muted"><?php echo $count; ?> item(s) in your cart</p>
            <hr class="cart-divider">

            <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Not logged in: show a prompt -->
            <div class="alert alert-warning">
                🔒 Please <a href="login.php" style="color:#856404; font-weight:700;">log in</a> to manage your cart and checkout.
            </div>
            <?php endif; ?>

            <?php if (empty($cart)): ?>
            <!-- ===== EMPTY CART STATE ===== -->
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Your Cart is Empty</h2>
                <p>Looks like you haven't added any books yet. Start browsing!</p>
                <a href="books.php" class="btn btn-primary">Browse Books →</a>
            </div>

            <?php else: ?>
            <!-- ===== CART ITEMS LIST ===== -->
            <?php foreach ($cart as $book_id => $item): ?>
            <div class="cart-item">

                <!-- Book Cover -->
                <img
                    src="<?php echo htmlspecialchars($item['image']); ?>"
                    alt="<?php echo htmlspecialchars($item['title']); ?>"
                    class="cart-item-img"
                    loading="lazy"
                    onerror="this.style.background='#f0f0f0'">

                <!-- Book Info -->
                <div class="cart-item-info">
                    <div class="cart-item-title">
                        <a href="books.php"><?php echo htmlspecialchars($item['title']); ?></a>
                    </div>
                    <div class="cart-item-author">by <?php echo htmlspecialchars($item['author']); ?></div>
                    <div class="cart-item-price"><?php echo format_price($item['price']); ?> each</div>

                    <!-- Actions Row -->
                    <div class="cart-item-actions">

                        <!-- Quantity Controls -->
                        <form action="php/update_cart.php" method="POST" style="display:inline;">
                            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                            <input type="hidden" name="action" value="decrease">
                            <div class="qty-control">
                                <button type="submit" class="qty-btn" title="Decrease quantity">−</button>
                        </form>
                        <span class="qty-display"><?php echo $item['qty']; ?></span>
                        <form action="php/update_cart.php" method="POST" style="display:inline;">
                            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                            <input type="hidden" name="action" value="increase">
                                <button type="submit" class="qty-btn" title="Increase quantity">+</button>
                            </div>
                        </form>

                        <!-- Item Total -->
                        <span style="font-weight:700; color:#e00; font-size:1rem;">
                            = <?php echo format_price($item['price'] * $item['qty']); ?>
                        </span>

                        <!-- Remove Button -->
                        <form action="php/remove_from_cart.php" method="POST" style="display:inline;" onsubmit="return confirm('Remove this book from cart?');">
                            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                            <button type="submit" class="cart-remove-btn">🗑 Remove</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Continue Shopping -->
            <div style="margin-top:16px;">
                <a href="books.php" class="btn btn-outline" style="background:#f3f3f3; color:#333; border-color:#ddd;">
                    ← Continue Shopping
                </a>
            </div>

            <?php endif; ?>
        </div><!-- /.cart-main -->

        <!-- ===== RIGHT: ORDER SUMMARY ===== -->
        <div class="cart-summary">
            <h2>📋 Order Summary</h2>

            <!-- Item count -->
            <div class="summary-row">
                <span>Items (<?php echo $count; ?>)</span>
                <span><?php echo format_price($total); ?></span>
            </div>

            <!-- Shipping -->
            <div class="summary-row">
                <span>Shipping</span>
                <span>
                    <?php if ($shipping == 0): ?>
                        <span style="color:#067d62; font-weight:600;">FREE ✅</span>
                    <?php else: ?>
                        <?php echo format_price($shipping); ?>
                    <?php endif; ?>
                </span>
            </div>

            <?php if ($total > 0 && $total < 999): ?>
            <div style="font-size:0.78rem; color:#888; margin-bottom:10px; background:#fff8e1; padding:8px 10px; border-radius:6px; border:1px solid #ffe082;">
                🚚 Add <?php echo format_price(999 - $total); ?> more for <strong>FREE shipping!</strong>
            </div>
            <?php endif; ?>

            <!-- Grand Total -->
            <div class="summary-total">
                <span>Total</span>
                <span class="total-price"><?php echo format_price($grand_total); ?></span>
            </div>

            <!-- Checkout Button -->
            <?php if (!empty($cart)): ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn-checkout" onclick="alert('🎉 Thank you for shopping at BookVault!\n\nIn a real app, this would proceed to payment.\n\nOrder Total: <?php echo format_price($grand_total); ?>')">
                    💳 Proceed to Checkout
                </button>
                <?php else: ?>
                <a href="login.php" class="btn-checkout" style="display:block; text-align:center; text-decoration:none;">
                    🔑 Login to Checkout
                </a>
                <?php endif; ?>
            <?php else: ?>
            <button class="btn-checkout" disabled style="opacity:0.5; cursor:not-allowed;">
                Cart is Empty
            </button>
            <?php endif; ?>

            <!-- Security badge -->
            <div class="secure-badge">
                🔒 Secure Checkout — SSL Encrypted
            </div>

            <!-- Accepted payment note -->
            <div style="text-align:center; margin-top:14px; font-size:0.78rem; color:#aaa;">
                We accept: 💳 Visa · Mastercard · PayPal · UPI
            </div>
        </div><!-- /.cart-summary -->

    </div><!-- /.cart-layout -->

</main>

<?php include 'php/footer.php'; ?>
