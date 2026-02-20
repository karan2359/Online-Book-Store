<?php
include_once 'config.php';
header('Content-Type: application/json');

// Get book ID and quantity
$book_id = $_POST['book_id'] ?? $_GET['book_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

// if (!$book_id || !is_numeric($book_id)) {
//     echo json_encode(['success' => false, 'message' => 'Invalid book']);
//     exit;
// }
if (!$book_id || !is_numeric($book_id)) {
    echo json_encode(['success' => false]);
    exit;
}

// Verify book exists
$stmt = $pdo->prepare("SELECT id FROM books WHERE id = ?");
$stmt->execute([$book_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false]);
    exit;
}

// GUEST CART (Session-based if not logged in)
if (!isLoggedIn()) {
    // Use session cart for guests
    if (!isset($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }
    
    if (isset($_SESSION['guest_cart'][$book_id])) {
        $_SESSION['guest_cart'][$book_id] += $quantity;
    } else {
        $_SESSION['guest_cart'][$book_id] = $quantity;
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Added to cart! Login to save cart.',
        'guest_cart' => true,
        'cart_count' => array_sum($_SESSION['guest_cart'])
    ]);
    exit;
}

// LOGGED IN USER - Save to DATABASE
try {
    // Check if already in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND book_id = ?");
    $stmt->execute([$_SESSION['user_id'], $book_id]);
    $cart_item = $stmt->fetch();
    
    if ($cart_item) {
        // Update quantity
        $new_qty = $cart_item['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_qty, $cart_item['id']]);
    } else {
        // Add new item
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $book_id, $quantity]);
    }
    
    // Get total cart count
    // $stmt = $pdo->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = {$_SESSION['user_id']}");
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);

    $total = $stmt->fetch()['total'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'message' => 'Added to cart!',
        'cart_count' => $total
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Cart error: ' . $e->getMessage()]);
}
?>
