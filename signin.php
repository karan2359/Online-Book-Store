<?php
include_once 'config.php';


$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $mobile = trim($_POST['mobile']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    
    // VALIDATION
    $errors = [];
    if (empty($fullname)) $errors[] = 'Full name required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
    if (empty($password) || strlen($password) < 6) $errors[] = 'Password 6+ characters';
    if (empty($mobile) || !preg_match('/^[0-9]{10}$/', $mobile)) $errors[] = '10-digit mobile';
    if (empty($city) || empty($state)) $errors[] = 'City/State required';
    
    if (empty($errors)) {
        // CHECK EMAIL EXISTS
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 0) {
            // GENERATE USER ID
            $stmt = $pdo->query("SELECT COALESCE(MAX(userid), 0) + 1 as newid FROM users");
            $userid = $stmt->fetch(PDO::FETCH_ASSOC)['newid'];
            
            // HASH PASSWORD
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // SAVE USER
            $stmt = $pdo->prepare("INSERT INTO users (userid, fullname, email, password, mobile, city, state) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$userid, $fullname, $email, $hashed_password, $mobile, $city, $state])) {
                $success_msg = "✅ Account created! <a href='login.php'>Login now</a>";
            } else {
                $error_msg = "❌ Registration failed. Try again.";
            }
        } else {
            $error_msg = "❌ Email already registered!";
        }
    } else {
        $error_msg = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&amp;display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In Page</title>
    <link rel="stylesheet" href="CSS/signin.css">
</head>
<body>
     <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php"> <img src="asset/logo cut.png" alt="logo" height="60px">
                    <p class="title">Book Store</p>
                </a></div>
            <div class="menu">
                <div><a href="index.php">🏚️Home</a></div>
                <div class="center acc">
                    <a href="login.php">👤 LogIn</a>                    
                </div>
            </div>
        </nav>
    </header>

    <form method="POST">
        <div class="container">
        <h2>Create Account</h2>
        
        <?php if ($success_msg): ?>
            <div class="message success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
            <div class="message error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <?php if (!$success_msg): ?>
        <div class="data">
            <label for="fname">Full Name </label>
            <input type="text" name="fullname" placeholder="Full Name *" 
                   value="<?php echo $_POST['fullname'] ?? ''; ?>" required>
            <label for="Mobile">Mobile</label>
            <input type="tel" name="mobile" placeholder="Mobile Number (10 digits) *" 
                   value="<?php echo $_POST['mobile'] ?? ''; ?>" required>
            <label for="email">Email</label>
                   <input type="email" name="email" placeholder="Email *" 
                   value="<?php echo $_POST['email'] ?? ''; ?>" required>
            <label for="Password">Password</label>       
            <input type="password" name="password" placeholder="Password (6+ chars) *" required>
            <label for="City">City</label>
            <input type="text" name="city" placeholder="City *" 
                   value="<?php echo $_POST['city'] ?? ''; ?>" required>
            <label for="State">State</label>
            <input type="text" name="state" placeholder="State *" 
                   value="<?php echo $_POST['state'] ?? ''; ?>" required>
                 </div>
            <button type="submit">Create Account</button>
            <p>Already have account? <a href="login.php">Login Here</a></p>
            <?php endif; ?>
        </div>
    </form>

</body>
</html>