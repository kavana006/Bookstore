<?php
/**
 * update_cart.php
 * Updates the quantity of a cart item.
 *
 * Accepts POST with: book_id, action ('increase' or 'decrease')
 * Redirects back to cart.php after processing.
 *
 * If qty drops to 0 or below, the item is removed from cart.
 */

session_start();

if (isset($_POST['book_id'], $_POST['action'])) {
    $book_id = (int) $_POST['book_id'];
    $action  = $_POST['action'];

    if (isset($_SESSION['cart'][$book_id])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$book_id]['qty']++;
        } elseif ($action === 'decrease') {
            $_SESSION['cart'][$book_id]['qty']--;
            // Remove if quantity reaches zero
            if ($_SESSION['cart'][$book_id]['qty'] <= 0) {
                unset($_SESSION['cart'][$book_id]);
            }
        }
    }
}

header('Location: ../cart.php');
exit();
