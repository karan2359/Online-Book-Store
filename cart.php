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
    <title>Shopping Cart - BookStore</title>
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <!--  NAVBAR -->
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
    
    <div class="cart-container">
        <h1>🛒 Shopping Cart</h1>
        
        <?php if (!isLoggedIn()): ?>
            <div class="not_login">
                <p>⚠️ Please <a href="login.php" onclick="showLogin()">login</a> to view your cart</p>
            </div>
        <?php else: ?>
        
        <?php
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("
            SELECT c.*, b.title, b.price, b.image, b.author 
            FROM cart c 
            JOIN books b ON c.book_id = b.id 
            WHERE c.user_id = ? 
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll();
        
        if (empty($cart_items)): ?>
            <div style="text-align:center; padding:50px; color:#666; font-size:large;">
                <h2>🛒 Your cart is empty</h2>
                <p>Add some books from the <a href="index.php">store</a></p>
            </div>
        <?php else: ?>
        
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                foreach ($cart_items as $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $grand_total += $subtotal;
                ?>
                <tr>
                    <td>
                        <div class="cart-item">
                            <img src="<?= $item['image'] ?>" alt="<?= $item['title'] ?>">
                            <div>
                                <h4><?= $item['title'] ?></h4>
                                <p><?= $item['author'] ?></p>
                            </div>
                        </div>
                    </td>
                    <td>₹<?= number_format($item['price'], 2) ?></td>
                    <td>
                        <input type="number" class="quantity-input" 
                               value="<?= $item['quantity'] ?>" 
                               min="1" onchange="updateQuantity(<?= $item['book_id'] ?>, this.value)">
                    </td>
                    <td>₹<?= number_format($subtotal, 2) ?></td>
                    <td>
    <form method="POST" action="remove_from_cart.php" style="display:inline;">
        <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
        <button type="submit" class="btn btn-danger" onclick="return confirm('Remove?')">
            ❌ Remove
        </button>
    </form>


                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- CART SUMMARY -->
        <div class="cart-summary">
            <h3>Order Summary</h3>
            <div style="margin:20px 0;">
                <p>Subtotal: <strong>₹<?= number_format($grand_total, 2) ?></strong></p>
                <p>Shipping: <strong>FREE</strong></p>
                <hr>
                <div class="total-price">
                    Total: <strong>₹<?= number_format($grand_total, 2) ?></strong>
                </div>
            </div>
            <a href="place_order.php" class="btn btn-success" style="width:85%; text-align:center;">🚀 Place Order</a>
            <a href="index.php" class="btn btn-primary" style="width:85%; text-align:center; margin-top:10px;">Continue Shopping</a>
        </div>
        
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Update quantity
        function updateQuantity(bookId, quantity) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `book_id=${bookId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh cart page
                }
            });
        }
        function removeFromCart(cartItemId) {
    if (confirm('Remove this item from cart?')) {
        fetch('remove_from_cart.php', {
            **method: 'POST',**  // ✅ MUST BE POST
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            **body: 'cart_item_id=' + cartItemId**  // ✅ POST data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();  // Refresh cart page
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Network error: ' + error);
        });
    }
}

    </script>
    <script src="script.js"></script>
</body>
</body>
</html>
