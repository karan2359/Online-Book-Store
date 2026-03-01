<?php include_once 'config.php';
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/orders.css">
    <title>My Orders - BookStore</title>
</head>
<body>
     <nav class="navbar">
            <div class="logo"><a href="index.php"> <img src="asset/logo cut.png" alt="logo" height="60px">
                    <p class="title">Book <span>Store</span></p></a>
            </div>
                <div class="menu">
                    </div>
                    
                    <div class="acc">
                    <a href="index.php">🏚️Home</a>
                    <?php 
                    include 'config.php';
                    if (isLoggedIn()) {                       
                        echo " <div style='color:white; padding:4px;'>Hi, {$_SESSION['fullname']}  | <a href='logout.php'>Logout</a>";
                    } else {
                        echo "<a href='login.php'>Login</a> | <a href='signin.php'>Sign Up</a>";
                    }?>
               </div>

            </div>
        </nav>

    
    <div class="orders-container">
        <div style=" display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>📦 My Orders</h1>
            <a href="index.php" class="btn btn-primary">← Continue Shopping</a>
        </div>
        
        <?php if (!isLoggedIn()): ?>
            <div style="font-size:large; border:2px solid #fff3cd; padding:30px; border-radius:15px; text-align:center;">
                <h3 style="padding:10px">🔐 Please login to view your orders</h3>
                <p><a href="#" onclick="showLogin()" >Click here to <a style="color:#4facfe;" href="login.php">login</a></a></p>
            </div>
        <?php else: ?>
        
        <?php
        $user_id = $_SESSION['user_id'];
        
        // Fetch user's orders
        $stmt = $pdo->prepare("
            SELECT o.*, COUNT(oi.id) as item_count 
            FROM orders o 
            LEFT JOIN order_items oi ON o.id = oi.order_id 
            WHERE o.user_id = ? 
            GROUP BY o.id 
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll();
        
        if (empty($orders)): ?>
            <div class="no-orders">
                <h2>📭 No orders yet</h2>
                <p style="padding:10px;">Your order history will appear here once you place an order.</p>
                <a href="index.php" class="btn btn-primary" style="padding:15px 30px; font-size:18px;">Start Shopping</a>
            </div>
        <?php else: ?>
        
        <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <div class="order-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                        <span style="margin-left:20px; font-size:14px; opacity:0.9;">
                            Placed on <?= date('M d, Y \a\t g:i A', strtotime($order['order_date'])) ?>
                        </span>
                    </div>
                    <div class="status-badge status-<?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                    </div>
                </div>
            </div>
            
            <div class="order-details">
                <div class="order-items">
                    <?php
                    // Fetch order items
                    $stmt = $pdo->prepare("
                        SELECT oi.*, b.title, b.image, b.author 
                        FROM order_items oi 
                        JOIN books b ON oi.book_id = b.id 
                        WHERE oi.order_id = ? 
                        ORDER BY oi.id
                    ");
                    $stmt->execute([$order['id']]);
                    $items = $stmt->fetchAll();
                    
                    foreach ($items as $item): 
                        $item_total = $item['price'] * $item['quantity'];
                    ?>
                    <div class="order-item">
                        <div class="item-info">
                            <img src="<?= $item['image'] ?>" alt="<?= $item['title'] ?>">
                            <div>
                                <strong><?= $item['title'] ?></strong><br>
                                <small>by <?= $item['author'] ?></small>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size:18px; font-weight:bold;">Qty: <?= $item['quantity'] ?></div>
                            <div>₹<?= number_format($item_total, 2) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="border-top: 2px solid #666; padding-top:20px;">
                    <div style="display: flex; justify-content: space-between; font-size:18px; font-weight:bold;">
                        <span>Total Amount:</span>
                        <span>₹<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                    
                    <?php if ($order['status'] === 'pending'): ?>
                    <div style="margin-top:15px;">
                        <a href="cancel_order.php?id=<?= $order['id'] ?>" 
                           class="btn" style="background:#ff6b6b; color:white;"
                           onclick="return confirm('Cancel this order?')">❌ Cancel Order</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>
</html>
