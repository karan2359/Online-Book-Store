<?php
session_start();
include 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.*, b.title, b.price, b.image 
    FROM cart c 
    JOIN books b ON c.book_id = b.id 
    WHERE c.user_id = ? 
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    header('Location: cart.php?error=empty');
    exit;
}

// Calculate total
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // 1. Create ORDER
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, status, created_at) 
            VALUES (?, ?, 'pending', NOW())
        ");
        $stmt->execute([$user_id, $total_amount]);
        $order_id = $pdo->lastInsertId();
        
        // 2. Create ORDER ITEMS (each book)
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, book_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
        }
        
        // 3. Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        
        // Success - redirect to order success
        header('Location: order_success.php?order_id=' . $order_id);
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Order failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Place Order - BookStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            font-family: "Roboto Slab", serif; 
        }
        body { 
            background: linear-gradient(135deg, #393E46, #4a5568); 
            color: white; 
            padding: 20px; 
        }
        .container { 
            max-width: 900px; 
            margin: 40px auto; 
        }
        .order-summary { 
            background: rgba(255,255,255,0.1); 
            padding: 30px; 
            border-radius: 20px; 
            margin-bottom: 30px; 
            backdrop-filter: blur(10px);
        }
        .order-item { 
            display: flex; 
            justify-content: space-between; 
            padding: 15px 0; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }
        .total { 
            font-size: 24px; 
            font-weight: bold; 
            color: #48bb78; 
            margin: 20px 0; 
        }
        .btn { 
            background: linear-gradient(135deg, #48bb78, #38a169); 
            color: white; 
            padding: 18px 50px; 
            border: none; 
            border-radius: 12px; 
            font-size: 18px; 
            font-weight: bold; 
            cursor: pointer; 
            width: 100%; 
            max-width: 300px;
        }
        .btn:hover { background: linear-gradient(135deg, #38a169, #2f855a); }
        .back-btn { 
            background: #6b7280; 
            display: inline-block; 
            padding: 12px 25px; 
            margin: 10px; 
            text-decoration: none; 
            border-radius: 8px; 
            color: white; 
        }
        .error { background: rgba(220,38,38,0.2); 
        color: #fc8181; 
        padding: 15px; 
        border-radius: 10px; 
        margin: 20px 0; 
    }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; margin-bottom: 40px;">📦 Place Your Order</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="order-summary">
            <h2>📚 Order Summary</h2>
            
            <?php foreach ($cart_items as $item): 
                $subtotal = $item['price'] * $item['quantity'];
            ?>
                <div class="order-item">
                    <div>
                        <strong><?= $item['title'] ?></strong><br>
                        <small>Qty: <?= $item['quantity'] ?> × ₹<?= $item['price'] ?></small>
                    </div>
                    <div>₹<?= number_format($subtotal, 2) ?></div>
                </div>
            <?php endforeach; ?>
            
            <div class="total">
                💰 Total: ₹<?= number_format($total_amount, 2) ?>
            </div>
        </div>
        
        <div style="display: flex; gap: 20px; justify-content: center; align-items: center; margin-top: 30px;">
    <!-- Cancel Button -->
    <a href="cart.php" class="btn-cancel" style="
        background: linear-gradient(135deg, #f56565, #e53e3e);
        color: white; padding: 18px 50px; border-radius: 12px;
        font-size: 18px; font-weight: bold; cursor: pointer;
        text-decoration: none; text-align: center; min-width: 200px;
    " onclick="return confirm('Are you sure you want to cancel?')">
        ❌ Cancel Order
    </a>
    
    <!-- Confirm Button -->
    <form method="POST" style="display: inline;">
        <button type="submit" class="btn" style="min-width: 250px;">
            ✅ Confirm & Place Order
        </button>
    </form>
</div>

<a href="cart.php" class="back-btn" style="display: block; text-align: center; margin-top: 20px;">
    ← Back to Cart (Edit Items)
</a>
    </div>
</body>
</html>
