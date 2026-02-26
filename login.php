<?php
include_once 'config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header('Location: index.php');
        exit;
    } else {
        $error = "❌ Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/login.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php"> <img src="asset/logo cut.png" alt="logo" height="60px">
                    <p class="title">Book Store</p></a>
            </div>
            <div class="menu">
                <div><a href="index.php">🏚️Home</a></div>
                <div class="center acc"><a href="signin.php">👤 SignIn</a></div>
            </div>
        </nav>
    </header>
    <form method="POST">
     <div class="container">
    <h2>Login</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <div class="data">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Email" required>
            <label for="Password">Password</label>
            <input type="password" name="password" placeholder="Password" required>
        </div>
            <button type="submit">Login</button>

            <p>If You Not Have A Account: <a href="signin.php">Create Account</a></p>
        </div>
    </form>
</body>
</html>