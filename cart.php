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
    <style>
          * {
            margin: 0;
            padding: 0;
            font-family: "Roboto Slab", serif;
        }

        body {
            background-color: #393E46;
            /* color:white; */

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

        .cart-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        h1{
            color:white;
            padding: 10px;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .cart-table th, .cart-table td {
            padding: 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .cart-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .cart-item img {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary { background: #4facfe; color: white; }
        .btn-danger { border: 2px solid red; color: red; }
        .btn-success { background: #51cf66; color: white; }
        
        .cart-summary {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            float: right;
            width: 300px;
        }
        
        .total-price {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0;
        }
        .not_login{
            border:2px solid #fff3cd; 
            margin:20px;font-size:large; 
            padding:50px; 
            border-radius:10px; 
            margin-bottom:20px; 
            color: #8c8686;
        }
    </style>
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
