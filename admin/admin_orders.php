<?php
session_start();
include '../config.php';

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    
    // Log admin action
    error_log("Admin updated order $order_id to '$new_status'");
}

// Fetch ALL orders with customer details
$stmt = $pdo->query("
    SELECT o.*, u.fullname, u.email, u.mobile,
           COUNT(oi.id) as item_count,
           SUM(oi.quantity * oi.price) as total_items
    FROM orders o 
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders - BookStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Roboto', sans-serif; 
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            background-color: #393E46;
            min-height: 100vh; 
            padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { 
            text-align: center; 
            color: white; 
            margin-bottom: 30px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .header p{
            margin-bottom:15px;
        }
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .stat-card { 
            background-color: #e9d3b4b5;  
            /* background: rgba(255,255,255,0.15);  */
            backdrop-filter: blur(20px); 
            padding: 25px; 
            border-radius: 20px; 
            text-align: center; 
            border: 1px solid rgba(255,255,255,0.2);
        }
        .orders-table { 
            color: #d0d0d0;  
            background: rgba(255,255,255,0.1); 
            backdrop-filter: blur(20px); 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { 
            padding: 18px 15px; 
            text-align: left; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }
        th { 
            background: rgba(255,255,255,0.2); 
            font-weight: 500; 
            color: white; 
            text-transform: uppercase; 
            font-size: 14px; 
            letter-spacing: 1px;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: #fed7aa; color: #c05621; }
        .status-processing { background: #bee3f8; color: #2b6cb0; }
        .status-shipped { background: #c6f6d5; color: #22543d; }
        .status-delivered { background: #68d391; color: white; }
        .status-cancelled { background: #fed7d7; color: #c53030; }
        .status-select { 
            padding: 6px 12px; 
            border-radius: 8px; 
            border: 1px solid #ddd; 
            background: white; 
            min-width: 120px;
        }
        .customer-info { font-size: 14px; }
        .total { font-weight: bold; color: #48bb78; font-size: 18px; }
        .btn { 
            padding: 8px 16px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 500; 
            transition: all 0.3s ease;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .btn-view { background: #4299e1; color: white; }
        .btn-print { background: #ed8936; color: white; }

        .back-btn{
            margin-top:30px; 
            background:rgba(255,255,255,0.2); 
            color:white; 
            padding:12px 24px; 
            border-radius:12px; 
            text-decoration:none; 
            font-weight:500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Orders Dashboard</h1>
            <p>Manage all customer orders | Total Orders: <?= count($orders) ?></p>
            <a href="../ACC.php" class="back-btn">← Back to Account</a>
        </div>

        <!-- Stats Cards -->
        <?php
        $pending = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
        $cancelled = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='cancelled'")->fetchColumn();
        $today_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()")->fetchColumn();
        $total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status!='cancelled'")->fetchColumn();
        ?>
        <div class="stats">
            <div class="stat-card">
                <div style="font-size:48px; color:#fed7aa;">⏳</div>
                <h3><?= $pending ?></h3>
                <p>Pending Orders</p>
            </div>
            <div class="stat-card">
                <div style="font-size:48px; color:#fed7d7;">❌</div>
                <h3><?= $cancelled ?></h3>
                <p>Cancelled</p>
            </div>
            <div class="stat-card">
                <div style="font-size:48px; color:#48bb78;">📅</div>
                <h3><?= $today_orders ?></h3>
                <p>Today's Orders</p>
            </div>
            <div class="stat-card">
                <div style="font-size:48px; color:#68d391;">💰</div>
                <h3>₹<?= number_format($total_revenue ?? 0, 2) ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="orders-table">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?= $order['id'] ?></strong></td>
                        <td>
                            <div class="customer-info">
                                <strong><?= htmlspecialchars($order['fullname']) ?></strong><br>
                                <?= htmlspecialchars($order['email']) ?><br>
                                <?= $order['mobile'] ? $order['mobile'] : '' ?>
                            </div>
                        </td>
                        <td><?= $order['item_count'] ?> items</td>
                        <td class="total">₹<?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" class="status-select" onchange="this.form.submit()">
                                    <option value="pending" <?= $order['status']=='pending'?'selected':'' ?>>Pending</option>
                                    <option value="processing" <?= $order['status']=='processing'?'selected':'' ?>>Processing</option>
                                    <option value="Confirmed" <?= $order['status']=='Confirmed'?'selected':'' ?>>Confirmed</option>
                                    <option value="shipped" <?= $order['status']=='shipped'?'selected':'' ?>>Shipped</option>
                                    <option value="delivered" <?= $order['status']=='delivered'?'selected':'' ?>>Delivered</option>
                                    <option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                            <br><br>
                            <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-view">View Details</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function printOrder(orderId) {
            window.open('order_print.php?id=' + orderId, '_blank');
        }
    </script>
</body>
</html>
