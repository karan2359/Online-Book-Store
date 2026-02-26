<?php
session_start();
include '../config.php';

// Handle status update
if (isset($_POST['action'])) {
    $feedback_id = $_POST['feedback_id'];
    $status = $_POST['status'] ?? '';

    
    $stmt = $pdo->prepare("UPDATE website_feedback SET status = ? WHERE id = ?");
    $stmt->execute([$status, $feedback_id]);
    
    $message = "Feedback status updated to '$status'";
}

// Fetch all feedback with stats
$stmt = $pdo->query("
    SELECT wf.*, 
           CASE 
               WHEN wf.user_id IS NOT NULL THEN u.fullname 
               ELSE wf.name 
           END as display_name
    FROM website_feedback wf
    LEFT JOIN users u ON wf.user_id = u.id
    ORDER BY wf.created_at DESC
");
$feedbacks = $stmt->fetchAll();

// Stats
$total_feedback = $pdo->query("SELECT COUNT(*) FROM website_feedback")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM website_feedback WHERE status='pending'")->fetchColumn();
$approved = $pdo->query("SELECT COUNT(*) FROM website_feedback WHERE status='approved'")->fetchColumn();
$avg_rating = $pdo->query("SELECT AVG(rating) FROM website_feedback")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Website Feedback - Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Roboto', sans-serif; 
            background-color: #393E46;
            min-height: 100vh; padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { 
            text-align: center; 
            color: white; 
            margin-bottom: 30px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        .stat-card { 
            background-color: #e9d3b4b5; 
            backdrop-filter: blur(20px); 
            padding: 25px; 
            border-radius: 20px; 
            text-align: center; 
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .feedback-table { 
            background: rgba(255,255,255,0.1); 
            backdrop-filter: blur(20px); 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        table { width: 100%; border-collapse: collapse; color: #d0d0d0; }
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
        .stars { color: #fbbf24; font-size: 18px; }
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: #fed7aa; color: #c05621; }
        .status-approved { background: #68d391; color: white; }
        .status-addressed { background: #48bb78; color: white; }
        .customer-info { font-size: 14px; }
        .feedback-text { width: 300px; word-wrap: break-word; }
        .btn { 
            padding: 8px 16px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 500; 
            margin: 2px; 
            transition: all 0.3s ease;
            text-decoration: none; display: inline-block;
        }
        .btn-approve { background: #48bb78; color: white; }
        .btn-address { background: #4299e1; color: white; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { 
            background: rgba(72,187,120,0.3); 
            color: #fff; 
            padding: 15px; 
            border-radius: 12px; 
            text-align: center; 
            margin: 20px 0;
        }
        .no-data { text-align: center; padding: 60px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 Website Feedback Dashboard</h1>
            <p style="margin-bottom:20px;">Total Feedback: <?= $total_feedback ?> | Average Rating: <?= round($avg_rating, 1) ?>/5 ⭐</p>
            <a href="../Acc.php" style="background:rgba(255,255,255,0.2); color:white; padding:12px 24px; border-radius:12px; text-decoration:none; font-weight:500;">← Back to Dashboard</a>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert"><?= $message ?></div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats">
            <div class="stat-card">
                <div style="font-size:48px; color:#fed7aa;">⏳</div>
                <h3><?= $pending ?></h3>
                <p>Pending Review</p>
            </div>
            <div class="stat-card">
                <div style="font-size:48px; color:#68d391;">✅</div>
                <h3><?= $approved ?></h3>
                <p>Approved</p>
            </div>
            <div class="stat-card">
                <div style="font-size:48px; color:#48bb78;">📊</div>
                <h3><?= round($avg_rating, 1) ?>/5</h3>
                <p>Average Rating</p>
            </div>
            <div class="stat-card">
                <div style="font-size:48px; color:#a0aec0;">📅</div>
                <h3><?= date('M d, Y') ?></h3>
                <p>Today's Feedback</p>
            </div>
        </div>

        <!-- Feedback Table -->
        <div class="feedback-table">
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Rating</th>
                        <th>Feedback</th>
                        <th>Suggestion</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($feedbacks)): ?>
                        <tr><td colspan="8" class="no-data">No feedback received yet</td></tr>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $feedback): ?>
                        <tr>
                            <td class="customer-info">
                                <strong><?= htmlspecialchars($feedback['display_name']) ?></strong>
                                <?php if ($feedback['user_id']): ?>
                                    <br><small style="color:#a0aec0;">Registered User</small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($feedback['email']) ?></td>
                            <td>
                                <span class="stars">
                                    <?= str_repeat('⭐', $feedback['rating']) ?>
                                    <?= str_repeat('☆', 5 - $feedback['rating']) ?>
                                </span>
                            </td>
                            <td class="feedback-text"><?= htmlspecialchars(substr($feedback['feedback'], 0, 80)) ?><?= strlen($feedback['feedback']) > 80 ? '' : '' ?></td>
                            <td class="feedback-text suggestion">
                                <?= $feedback['suggestion'] ? htmlspecialchars(substr($feedback['suggestion'], 0, 80)) . '' : 'None' ?>
                            </td>
                           <td><?= date('M d, Y H:i', strtotime($feedback['created_at'])) ?></td>

                            <td>
                                <span class="status-badge status-<?= $feedback['status'] ?>">
                                    <?= ucfirst($feedback['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="feedback_id" value="<?= $feedback['id'] ?>">
                                    <?php if ($feedback['status'] != 'approved'): ?>
                                        <button type="submit" name="action" value="approve" class="btn btn-approve">✅ Approve</button>
                                    <?php endif; ?>
                                    
                                </form>
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
