<?php include_once 'config.php';
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <title>My Orders - BookStore</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            /* font-family: monospace;
            font-size: large; */

            font-family: "Roboto Slab", serif;
        }

        body {
            background-color: #393E46;
            color:white;

        }
/* Navigation Bar */
        .navbar {
            display: flex;
            /* background-color: rgba(42, 238, 81, 0.659); */
            background-color: #222831;
            justify-content: space-between;
            padding: 20px;
            /* font-size:large; */
            font-weight: bold;
        

        }

        .navlist {
            display: flex;
            gap: 30px;
            list-style: none;

        }

        .logo {
            margin-left: 10px;
        }
        .title {
            display: inline-block;
            font-size: 35px;
            position: absolute;
            margin: 10px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        a {
            text-decoration: none;
            color: white;

        }

        ul {
            list-style: none;
        }
        .acc{
            display: flex;
            gap:20px;
            margin:20px;
            font-size:larger;
        }

        .books-grid {

            padding: 20px;
            display: flex; 
            justify-content:space-evenly;
            gap: 45px;
            background-color: #948979;
        }
        .navbar {
            display: flex;
            /* background-color: rgba(42, 238, 81, 0.659); */
            background-color: #222831;
            justify-content: space-between;
            padding: 20px;
            /* font-size:large; */
            font-weight: bold;
        

        }

        .navlist {
            /* position: sticky; */
            display: flex;
            gap: 30px;
            list-style: none;

        }

        .logo {
            margin-left: 10px;
        }

        a {
            text-decoration: none;
            color: white;
            padding:1px;
        }

        ul {
            list-style: none;
        }
/* Body */
        .orders-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .order-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 25px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-left: 5px solid #4facfe;
        }
        
        .order-header {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .order-details {
            padding: 25px;
            color:black;
        }
        
        .order-items {
            margin: 20px 0;

        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #666;
            border-radius: 10px;
            margin-bottom: 10px;
            
        }
        
        .item-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .item-info img {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        
        .no-orders {
            text-align: center;
            padding: 80px 20px;
            /* color: #666; */
            color: #fff;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        
        .btn-primary { background: #4facfe; color: white; }
        .btn-success { background: #51cf66; color: white; }
    </style>
</head>
<body>
     <nav class="navbar">
            <div class="logo"><a href="index.php"> <img src="asset/logo cut.png" alt="logo" height="60px">
                    <p class="title">Book Store</p></a>
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
