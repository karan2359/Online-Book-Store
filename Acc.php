<?php
session_start();
include 'config.php';



// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();


// Avatar logic (first letter of name)
$avatar_initial = strtoupper(substr($user['fullname'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Section</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/Acc.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="dash">
                <div class="logo"><a href="index.php"> <img src="asset/logo cut.png" alt="logo"height="60px"><p class="title">Book Store</p></a>
                </div>
            </div>
            <hr>
            <nav class="navbar">
                <ul>
                    <li><a href="index.php">Home</a></li>
                  
                    <li><a href="update_profile.php">Edit</a></li>
                    <?php 
                    
                    if (isLoggedIn()) {
                        if (isAdmin()) {
                            echo "<li><a href='admin/admin.php'> Manage Books</a>  </li>";
                            echo "<li><a href='admin/website_feedback.php'> User Feedbacks</a>  </li>";
                            echo "<li><a href='admin/admin_orders.php' class='admin-btn'>View All Orders</a></li>";
                            echo "<li><a href='admin/User_list.php'> User List</a>  </li>";
                        }else{
                            // echo " <div style='color:white;'> <a href ='Acc.php' style='padding: 0px 15px 0px 0px;'> Hi, {$_SESSION['fullname']}</a>";
                            echo "  <li><a href='orders.php'>Orders</a></li>
                            <li><a href='cart.php'>Carts</a></li>
                            <li><a href='feedback.php'>Feedback</a></li>
                            <li><a href='about.php'>About US</a></li>";
                            }
                    }?>
                    <li style="margin-top:75px;"><a href="logout.php">Log out</a></li>
                </ul>
            </nav>
        </aside>
        <div class="sesions">
            <div class="acc-info">
              <div class="profile-header">
            <!-- LEFT: User Information -->
            <div class="user-info">
                <h1 class="user-name"> Name:<?= htmlspecialchars($user['fullname']) ?></h1>
                
                <div class="user-details">
                    <div class="detail-card">
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                    
                    <div class="detail-card">
                        <div class="detail-label">Mobile</div>
                        <div class="detail-value"><?= $user['mobile'] ?: 'Not provided' ?></div>
                    </div>
                    <div class="detail-card">
                        <div class="detail-label">City/State</div>
                        <div class="detail-value"><?= $user['city'] ?: 'Not provided' ?>,</div>
                        <div class="detail-value"><?= $user['state'] ?: 'Not provided' ?></div>
                    </div>
                    
                    <?php if ($user['is_admin']): ?>
                    <div>
                        <div class="detail-label">Role</div>
                        <div class="detail-value" style="color: #ed8936;">Admin</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- RIGHT: Avatar -->
            <div style="flex-shrink: 0;">
                <div class="user-avatar"><?= $avatar_initial ?></div>
            </div>
        </div>
            </div>
            <div class="count">
                 <div class="stats-grid">
            <?php
            // Orders count
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $order_count = $stmt->fetchColumn();
            
            // Total spent
            $stmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE user_id = ? AND status != 'cancelled'");
            $stmt->execute([$_SESSION['user_id']]);
            $total_spent = $stmt->fetchColumn() ?: 0;
            ?>
            
            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <h3><?= $order_count ?></h3>
                <p>Total Orders</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <h3>₹<?= number_format($total_spent, 2) ?></h3>
                <p>Total Spent</p>
            </div>
            
            <div class="stat-card ">
                <div class="stat-icon">⭐</div>
                <h3>Member</h3>
                <p>Since : <?= date(' d M, Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
            </div>
        </div>
    </div>
</body>

</html>



