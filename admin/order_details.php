

<?php
session_start();
include '../config.php';


$order_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("
    SELECT o.*, u.fullname, u.email, u.mobile
    FROM orders o JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

$items = $pdo->prepare("
    SELECT oi.*, b.title, b.author, b.image
    FROM order_items oi JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$items->execute([$order_id]);
$order_items = $items->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order #<?= $order_id ?> - Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; }
        .order-header { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #666; }
        .total { font-size: 24px; font-weight: bold; color: #28a745; text-align: right; }
        .print-btn { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; }
        .logo {
            margin-left: 10px;
            padding:10px;
        }
        .title {
            display: inline-block;
            padding-left:10px;
            font-size: 35px;
            font-weight:bold;
            position: absolute;
            margin: 10px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>
     <div class="logo"><a href="../index.php"> <img src="../asset/logo cut.png" alt="logo" height="60px">
                    <p class="title">Book Store</p></a>
            </div>
    <h1>Order Details </h1>
    
    <div class="order-header">
        <h2>Customer: <?= htmlspecialchars($order['fullname']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Phone:</strong> <?= $order['mobile'] ?></p>
        <p><strong>Date:</strong> <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>Status:</strong> <span style="color:<?= $order['status']=='delivered'?'green':'orange' ?>"><?= ucfirst($order['status']) ?></span></p>
    </div>
    
    <table class="items-table">
        <thead>
            <tr><th>Book</th><th>Qty</th><th>Price</th><th>Total</th></tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['title']) ?> <br><small>by <?= $item['author'] ?></small></td>
                <td><?= $item['quantity'] ?></td>
                <td>₹<?= number_format($item['price'], 2) ?></td>
                <td>₹<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="total">Grand Total: ₹<?= number_format($order['total_amount'], 2) ?></div>
    
    <br>
    <button class="print-btn" onclick="window.print()">🖨️ Print Receipt</button>
    <a href="admin_orders.php" style="margin-left:20px;">← Back to Orders</a>
</body>
</html>
