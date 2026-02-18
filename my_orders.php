<?php
session_start();
include 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle cancel order
if (isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];
    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
}

// Fetch user's orders
$stmt = $pdo->prepare("
    SELECT o.*, COUNT(oi.id) as item_count 
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    WHERE o.user_id = ? 
    GROUP BY o.id 
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders - BookStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; font-family: "Roboto Slab", serif; }
        body { background: linear-gradient(135deg, #393E46, #4a5568); color: white; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 40px; }
        .order-card { 
            background: rgba(255,255,255,0.1); 
            border-radius: 20px; 
            padding: 25px; 
            margin-bottom: 20px; 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .order-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
            flex-wrap: wrap; 
            gap: 15px;
        }
        .status-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 14px;
        }
        .status-pending { background: #fed7aa; color: #c05621; }
        .status-processing { background: #bee3f8; color: #2b6cb0; }
        .status-shipped { background: #c6f6d5; color: #22543d; }
        .status-delivered { background: #9ae6b4; color: #22543d; }
        .status-cancelled { background: #fed7d7; color: #c53030; }
        .items-count { background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 15px; }
        .order-items { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
            gap: 15px; 
            margin-top: 20px; 
        }
        .order-item { 
            background: rgba(255,255,255,0.05); 
            padding: 15px; 
            border-radius: 12px; 
            text-align: center;
        }
        .btn { 
            background: linear-gradient(135deg, #48bb78, #38a169); 
            color: white; 
            padding: 12px 25px; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            font-weight: bold; 
            text-decoration: none; 
            display: inline-block;
        }
        .btn-cancel { 
            background: linear-gradient(135deg, #f56565, #e53e3e) !important; 
        }
        .no-orders { text-align: center; padding: 60px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="font-size: 2.5em; margin-bottom: 10px;">📦 My Orders</h1>
            <a href="index.php" class="btn">🏠 Continue Shopping</a>
        </div>

        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <h2>📭 No orders yet</h2>
                <p>Browse books and place your first order!</p>
                <a href="index.php" class="btn">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <strong>Order #<?= $order['id'] ?></strong><br>
                            <small>Placed: <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></small>
                        </div>
                        <div style="text-align: right;">
                            <div class="items-count"><?= $order['item_count'] ?> items</div>
                            <div class="status-badge status-<?= strtolower($order['status']) ?>">
                                <?= ucfirst($order['status']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <div class="order-items">
                            <?php
                            // Fetch order items
                            $stmt = $pdo->prepare("
                                SELECT b.title, oi.quantity, oi.price 
                                FROM order_items oi 
                                JOIN books b ON oi.book_id = b.id 
                                WHERE oi.order_id = ?
                            ");
                            $stmt->execute([$order['id']]);
                            $items = $stmt->fetchAll();
                            ?>
                            
                            <?php foreach ($items as $item): ?>
                                <div class="order-item">
                                    <strong><?= htmlspecialchars($item['title']) ?></strong><br>
                                    <small>Qty: <?= $item['quantity'] ?> × ₹<?= $item['price'] ?></small><br>
                                    <small><strong>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></strong></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div style="margin-top: 20px; text-align: right;">
                            <strong>Total: ₹<?= number_format($order['total_amount'], 2) ?></strong>
                        </div>
                        
                       <!-- IMPROVED CANCEL + STATUS DISPLAY -->
<?php if (in_array($order['status'], ['pending', 'processing'])): ?>
    <form method="POST" style="display: inline; margin-top: 15px;">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <button type="submit" name="cancel_order" class="btn btn-cancel" 
                onclick="return confirm('Cancel order #<?= $order['id'] ?>?\nThis cannot be undone.')">
            ❌ Cancel Order
        </button>
    </form>
<?php elseif ($order['status'] == 'cancelled'): ?>
    <div style="background: #fed7d7; color: #c53030; padding: 10px 20px; border-radius: 25px; font-weight: bold; display: inline-block; margin-top: 15px;">
        ✅ Order Cancelled
    </div>
<?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
