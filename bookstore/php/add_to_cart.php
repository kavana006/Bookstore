<?php
session_start();
require_once 'functions.php';

// --- Step 1: Must be logged in ---
if (!isset($_SESSION['user_id'])) {
    // Save the intended destination so we can redirect back after login
    $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? '../books.php';
    header('Location: ../login.php?msg=login_required');
    exit();
}

// --- Step 2: Validate POST data ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['book_id'])) {
    header('Location: ../books.php');
    exit();
}

$book_id = (int) $_POST['book_id'];

// --- Step 3: Load book from XML ---
$book = get_book_by_id($book_id);
if (!$book) {
    // Book not found, redirect with error
    header('Location: ../books.php?error=book_not_found');
    exit();
}

// --- Step 4: Add to session cart ---
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$book_id])) {
    // Book already in cart — increase quantity
    $_SESSION['cart'][$book_id]['qty']++;
} else {
    // New cart item
    $_SESSION['cart'][$book_id] = [
        'id'     => $book['id'],
        'title'  => $book['title'],
        'author' => $book['author'],
        'price'  => $book['price'],
        'image'  => $book['image'],
        'qty'    => 1,
    ];
}

// --- Step 5: Redirect back with success ---
$referrer = $_SERVER['HTTP_REFERER'] ?? '../books.php';
header('Location: ' . $referrer . '?added=' . urlencode($book['title']));
exit();
