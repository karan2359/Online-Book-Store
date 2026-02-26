<?php
session_start();

// Destroy all session data
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Redirect after 3 seconds
header("Refresh:5; url=index.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - BookStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eeac9 0%, #764ba2b5 50%, #f193fbb7 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .logout-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 60px 50px;
            text-align: center;
            box-shadow: 0 25px 45px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
            animation: slideUp 1s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .checkmark {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: conic-gradient(from 0deg, #4facfe, #00f2fe, #43e97b, #fa709a, #fe8a39, #4facfe);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: spin 2s linear infinite;
            position: relative;
        }
        
        .checkmark::after {
            content: '✓';
            font-size: 60px;
            font-weight: bold;
            color: white;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            animation: checkmarkBounce 0.6s ease-out 0.5s both;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @keyframes checkmarkBounce {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        h1 {
            font-family: 'Roboto Slab', serif;
            font-size: 2.5em;
            font-weight: 800;
            background: linear-gradient(135deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }
        
        .message {
            font-size: 1.3em;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .countdown {
            font-size: 4em;
            font-weight: 700;
            background: linear-gradient(135deg, #43e97b, #38a169);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 20px 0;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .home-link {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .home-link:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="logout-card">
        <div class="checkmark"></div>
        <h1>Logged Out Successfully!</h1>
        <div class="message">
            👋 Thank you for shopping with us!<br>
            See you soon! 📚✨
        </div>
        <div class="countdown" id="countdown">5</div>
        <a href="index.php" class="home-link">🏠 Go to Home</a>
    </div>

    <script>
        let timeLeft = 5;
        const countdownEl = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            timeLeft--;
            countdownEl.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                window.location.href = 'index.php';
            }
        }, 1000);
    </script>
</body>
</html>

