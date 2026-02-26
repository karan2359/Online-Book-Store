<?php
session_start();
include 'config.php';


if (isset($_POST['cart_item_id'])) {
    $cart_item_id = $_POST['cart_item_id'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_item_id, $user_id]);
    
    header('Location: cart.php?removed=1');
    exit;
} else {
    header('Location: cart.php?error=1');
}
?>
