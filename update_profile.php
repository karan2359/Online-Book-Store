<?php
session_start();
include 'config.php';

// Fetch current user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile'] ?? '');
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    
    // Validation
    if (empty($fullname) || empty($email)) {
        $error = "Name and Email are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter valid email";
    } else {
        // Check if email already exists 
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        
        if ($stmt->fetch()) {
            $error = "Email already registered ";
        } else {
            // Update user info
            $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, mobile = ? ,city = ? , state = ? WHERE id = ?");
            $stmt->execute([$fullname, $email, $mobile,$city,$state, $_SESSION['user_id']]);
            
            // Update session
            $_SESSION['fullname'] = $fullname;
            $_SESSION['email'] = $email;
            
            $message = "✅ Profile updated successfully!";
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - BookStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/update_profile.css">
</head>
<body>
    <div class="edit-container">
        <div class="edit-header">
            <h1>👤 Edit Profile</h1>
            <p>Update your personal information</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($message): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <!-- Edit Form -->
        <form method="POST" class="form-grid">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>City *</label>
                    <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>" required>
                </div>
                <div class="form-group">
                    <label>State *</label>
                    <input type="text" name="state" value="<?= htmlspecialchars($user['state']) ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Mobile Number</label>
                <input type="tel" name="mobile" value="<?= htmlspecialchars($user['mobile']) ?>" placeholder="9999999999">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-save"> Save Changes</button>
                <a href="ACC.php" class="btn btn-cancel"> Cancel</a>
            </div>
        </form>

        <div style="text-align: center; margin-top: 30px; color: rgba(255,255,255,0.8);">
            <p><a href="ACC.php" style="color: rgba(255,255,255,0.9); text-decoration: none;">← Back to Profile</a></p>
        </div>
    </div>
</body>
</html>
