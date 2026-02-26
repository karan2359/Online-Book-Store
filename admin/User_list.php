<?php
session_start();
include '../config.php';

// Handle user delete
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    
    try {
        // DISABLE FOREIGN KEY CHECKS - MAGIC FIX
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Delete user (children auto-deleted)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        // RE-ENABLE FOREIGN KEY CHECKS
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        header('Location: User_list.php?success=User deleted successfully');
        exit;
        
    } catch (Exception $e) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1"); // Ensure re-enabled
        header('Location: User_list.php?error=Delete failed');
        exit;
    }
}



// Fetch ALL users (exclude current admin)
$stmt = $pdo->prepare("
    SELECT u.*, 
           COUNT(o.id) as order_count,
           SUM(o.total_amount) as total_spent
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id 
    WHERE u.id != ?
    GROUP BY u.id 
    ORDER BY u.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users Management - BookStore Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Roboto', sans-serif; 
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            background-color:#393E46;
            min-height: 100vh; 
            padding: 20px;
        }
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
        }
        .header { 
            text-align: center; 
            color: white; 
            margin-bottom: 30px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .stat-card { 
            background-color: #e9d3b4b5;  
            backdrop-filter: blur(20px); 
            padding: 25px; 
            border-radius: 20px; 
            text-align: center; 
            border: 1px solid rgb(255, 255, 255);
        }
        .users-table { 
            background: rgba(255,255,255,0.1); 
            backdrop-filter: blur(20px); 
            border-radius: 20px; 
            color:white;
            overflow: hidden; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            padding: 18px 15px; 
            text-align: left; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }
        th { 
            background: rgba(255,255,255,0.2); 
            font-weight: 500; 
            color: #d0d0d0;  
            text-transform: uppercase; 
            font-size: 14px; 
            letter-spacing: 1px; }
        .user-avatar { 
            width: 50px; 
            height: 50px; 
            border-radius: 50%; 
            background: rgba(255,255,255,0.3); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            font-weight: bold; 
            font-size: 18px; 
        }
        .status-admin { 
            background: #ed8936; 
            color: white; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600; 
        }
        .status-user { 
            background: #48bb78; 
            color: white; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600; 
        }
        .total-spent { 
            color: #68d391; 
            font-weight: bold; 
            font-size: 16px; 
        }
        .btn { 
            padding: 8px 16px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 500; 
            margin: 2px; 
            transition: all 0.3s ease; 
            text-decoration: none; 
            display: inline-block;
        }
        .btn-edit { 
            background: #4299e1; 
            color: white; 
        }
        .btn-delete { 
            background: #f56565; 
            color: white; 
        }
        .btn:hover { 
            transform: translateY(-1px); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.2); 
        }
        .no-data { 
            text-align: center; 
            padding: 60px; 
            color: #a0aec0; 
            font-size: 18px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 Users Management</h1>
            <p style="margin-bottom:25px;">Total Users: <?= count($users) ?> | Manage customer accounts</p>
            <a href="../ACC.php" style="background:rgba(255,255,255,0.2); color:white; padding:12px 24px; border-radius:12px; text-decoration:none; font-weight:500;">← Back to Account</a>
        </div>

        <!-- Stats -->
        <?php
        $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $admin_count = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin=1")->fetchColumn();
        $active_users = $pdo->query("SELECT COUNT(*) FROM users WHERE id IN (SELECT DISTINCT user_id FROM orders)")->fetchColumn();
        $total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status!='cancelled'")->fetchColumn();
        ?>
        <div class="stats">
            <div class="stat-card">
                <div style="font-size:48px; color:#48bb78;">👥</div>
                <h3><?= $total_users ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <div style="font-size:48px; color:#ed8936;">👑</div>
                <h3><?= $admin_count ?></h3>
                <p>Admins</p>
            </div>
            <div class="stat-card">
                <div style="font-size:48px; color:#68d391;">🛒</div>
                <h3><?= $active_users ?></h3>
                <p>Shopping Users</p>
            </div>
            <div class="stat-card">
                <div style="font-size:48px; color:#a0aec0;">📈</div>
                <h3>₹<?= number_format($total_revenue ?? 0) ?></h3>
                <p>Platform Revenue</p>
            </div>
        </div>

        <!-- Users Table -->
        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>City/State</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="9" class="no-data">No users found</td></tr>
                    <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="user-avatar"><?= strtoupper(substr($user['fullname'], 0, 1)) ?></div>
                            <div style="margin-left:15px; display:inline-block;">
                                <strong><?= htmlspecialchars($user['fullname']) ?></strong>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['mobile'] ?: 'N/A' ?></td>
                        <td>
                            <?= $user['city'] ?: 'N/A' ?> / <?= $user['state'] ?: 'N/A' ?>
                        </td>
                        <td><?= $user['order_count'] ?: 0 ?></td>
                        <td class="total-spent">₹<?= number_format($user['total_spent'] ?? 0, 2) ?></td>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <span class="status-<?= $user['is_admin'] ? 'admin' : 'user' ?>">
                                <?= $user['is_admin'] ? 'Admin' : 'Customer' ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!$user['is_admin']): ?>
                            <a href="?delete=<?= $user['id'] ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Delete <?= $user['fullname'] ?>? All orders/cart will be deleted.')"
                               style="background:#f56565;">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
