<?php
/**
 * functions.php
 * Helper functions used across the entire BookVault project.
 *
 * Functions:
 *   - load_books_from_xml()   : Reads books.xml and returns array of books
 *   - render_stars($rating)   : Converts a numeric rating to ★ star icons
 *   - get_cart_total()        : Calculates total price in session cart
 *   - is_in_cart($book_id)    : Checks if a book is already in the cart
 *   - format_price($price)    : Formats a float as "₹999"
 *   - get_fake_original($p)   : Returns a fake "was" price for display
 */

// --- Load All Books from XML ---
/**
 * Reads xml/books.xml and returns an array of associative arrays.
 * Each book has: id, title, author, price, category, rating, image, description
 *
 * @param string $category  Optional filter by category
 * @param string $search    Optional search term (title/author)
 * @return array            Array of book arrays
 */
function load_books_from_xml($category = '', $search = '') {
    // Path to the XML file (relative to project root)
    $xml_path = __DIR__ . '/../xml/books.xml';

    // Check file exists
    if (!file_exists($xml_path)) {
        return [];
    }

    // Load XML using PHP's SimpleXML extension
    $xml = simplexml_load_file($xml_path);
    if ($xml === false) {
        return [];
    }

    $books = [];
    foreach ($xml->book as $book) {
        // Cast XML elements to PHP types
        $b = [
            'id'          => (int)    $book->id,
            'title'       => (string) $book->title,
            'author'      => (string) $book->author,
            'price'       => (float)  $book->price,
            'category'    => (string) $book->category,
            'rating'      => (float)  $book->rating,
            'image'       => (string) $book->image,
            'description' => (string) $book->description,
        ];

        // Apply category filter if specified
        if (!empty($category) && strtolower($b['category']) !== strtolower($category)) {
            continue;
        }

        // Apply search filter if specified (searches title and author)
        if (!empty($search)) {
            $s = strtolower($search);
            if (strpos(strtolower($b['title']), $s) === false &&
                strpos(strtolower($b['author']), $s) === false) {
                continue;
            }
        }

        $books[] = $b;
    }

    return $books;
}

// --- Load a Single Book by ID ---
/**
 * Returns a single book array for a given book ID, or null if not found.
 */
function get_book_by_id($id) {
    $books = load_books_from_xml();
    foreach ($books as $book) {
        if ($book['id'] == $id) return $book;
    }
    return null;
}

// --- Render Star Rating ---
/**
 * Converts a float rating (e.g. 4.5) to HTML stars.
 * Returns ★★★★½ style star string.
 */
function render_stars($rating) {
    $full  = floor($rating);
    $half  = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;

    $html  = '<span class="stars">';
    $html .= str_repeat('★', $full);
    $html .= $half ? '½' : '';
    $html .= str_repeat('☆', $empty);
    $html .= '</span>';
    return $html;
}

// --- Format Price ---
/**
 * Formats a numeric price to a currency string: $12.99
 */
function format_price($price) {
    // Indian Rupee symbol
    $p = (float)$price;
    if ($p == floor($p)) {
        return '&#x20B9;' . number_format((int)$p);
    }
    return '&#x20B9;' . number_format($p, 2);
}

// --- Get Fake Original Price ---
/**
 * Returns a "was" price that's ~20-35% higher, for visual appeal.
 * Uses book ID for deterministic pseudo-randomness.
 */
function get_fake_original($price, $id = 1) {
    $multipliers = [1.20, 1.25, 1.30, 1.35, 1.28, 1.22, 1.32, 1.18];
    $m = $multipliers[$id % count($multipliers)];
    return '&#x20B9;' . number_format((int)round($price * $m));
}

// --- Cart: Calculate Total ---
/**
 * Sums up total price of all items in the session cart.
 * Returns float total.
 */
function get_cart_total() {
    $total = 0.0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['qty'];
        }
    }
    return $total;
}

// --- Cart: Check if Book is Already in Cart ---
function is_in_cart($book_id) {
    if (!isset($_SESSION['cart'])) return false;
    return isset($_SESSION['cart'][$book_id]);
}

// --- Cart: Item Count ---
function get_cart_count() {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['qty'];
        }
    }
    return $count;
}
