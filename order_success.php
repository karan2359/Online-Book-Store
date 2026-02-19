<?php
session_start();
include 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, oi.quantity, b.title, b.price 
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    LEFT JOIN books b ON oi.book_id = b.id 
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$order_details = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Success - BookStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #48bb78, #38a169); font-family: 'Roboto Slab', serif; color: white; padding: 40px; text-align: center;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="font-size: 80px; margin-bottom: 20px;">✅</div>
        <h1 style="font-size: 3em; margin-bottom: 20px;">Order Placed Successfully!</h1>
        <p style="font-size: 1.3em; margin-bottom: 30px;">Your order <strong>#<?= $order_id ?></strong> has been received!</p>
        
        <?php if (!empty($order_details)): ?>
            <div style="background: rgba(255,255,255,0.1); padding: 30px; border-radius: 20px; margin: 30px 0;">
                <h3>📦 Order Items:</h3>
                <?php foreach ($order_details as $item): ?>
                    <p><strong><?= $item['title'] ?? 'N/A' ?></strong> × <?= $item['quantity'] ?? 1 ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Replace the Continue Shopping link with: -->
<div style="margin-top: 30px;">
    <a href="orders.php" style="background: #4299e1; color: white; padding: 18px 40px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 16px; margin-right: 15px;">📦 View My Orders</a>
    <a href="index.php" style="background: white; color: #38a169; padding: 18px 40px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 16px;">🏠 Continue Shopping</a>
</div>

    </div>
</body>
</html>
