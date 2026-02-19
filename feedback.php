<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $rating = (int)$_POST['rating'];
    $feedback = trim($_POST['feedback']);
    $suggestion = trim($_POST['suggestion'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;
    
    // Validation
    if (strlen($name) < 2 || strlen($email) < 5 || empty($feedback)) {
        $error = "Please fill all required fields properly";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO website_feedback (user_id, name, email, rating, feedback, suggestion) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $name, $email, $rating, $feedback, $suggestion]);
        
        $success = "Thank you! Your feedback has been submitted. We'll review it soon! 😊";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Website Feedback - BookStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/feedback.css">
</head>
<body>
    <div class="feedback-container">
        <h1>📝 Website Feedback</h1>
        <p style="text-align:center; color:rgba(255,255,255,0.9); margin-bottom:30px;">
            Help us improve! Share your experience with our bookstore.
        </p>
        
        <?php if (isset($success)): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (!isset($success)): ?>
        <form method="POST">
            <div class="form-group">
                <label>Your Name *</label>
                <input type="text" name="name" required 
                       value="<?= $_SESSION['fullname'] ?? '' ?>"
                       placeholder="Enter your full name">
            </div>
            
            <div class="form-group">
                <label>Your Email *</label>
                <input type="email" name="email" required 
                       value="<?= $_SESSION['email'] ?? '' ?>"
                       placeholder="your@email.com">
            </div>
            
            <div class="form-group">
                <label>Overall Rating *</label>
                <div class="stars">
                    <input type="radio" name="rating" value="5" id="star5" required>
                    <label for="star5">⭐5</label>
                    <input type="radio" name="rating" value="4" id="star4">
                    <label for="star4">⭐4</label>
                    <input type="radio" name="rating" value="3" id="star3">
                    <label for="star3">⭐3</label>
                    <input type="radio" name="rating" value="2" id="star2">
                    <label for="star2">⭐2</label>
                    <input type="radio" name="rating" value="1" id="star1">
                    <label for="star1">⭐1</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Your Feedback * (What you liked/disliked)</label>
                <textarea name="feedback" required 
                          placeholder="Example: 'Great book selection but checkout was slow...'"></textarea>
            </div>
            
            <div class="form-group">
                <label>Suggestions (Optional)</label>
                <textarea name="suggestion" 
                          placeholder="Example: 'Add more regional language books...'"></textarea>
            </div>
            
            <button type="submit" class="submit-btn"> Submit Feedback</button>
        </form>
        <?php endif; ?>
        
        <div style="text-align:center; margin-top:30px;">
            <a href="index.php" style="color:rgba(255,255,255,0.8); text-decoration:none;">← Back to Home</a>
        </div>
    </div>
</body>
</html>
