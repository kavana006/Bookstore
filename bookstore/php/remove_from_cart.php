<?php
/**
 * remove_from_cart.php
 * Removes an item from the session cart.
 *
 * Accepts POST with: book_id
 * Redirects back to cart.php after processing.
 */

session_start();

if (isset($_POST['book_id'])) {
    $book_id = (int) $_POST['book_id'];
    if (isset($_SESSION['cart'][$book_id])) {
        unset($_SESSION['cart'][$book_id]);
    }
}

header('Location: ../cart.php');
exit();
