<?php
include_once 'config.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Cancel only pending orders
$stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
$stmt->execute([$order_id, $user_id]);

if ($stmt->rowCount() > 0) {
    $message = "Order cancelled successfully!";
} else {
    $message = "Order not found or already processed.";
}

$_SESSION['message'] = $message;
header('Location: orders.php');
exit;
?>
