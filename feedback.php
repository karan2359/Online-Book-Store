<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&amp;display=swap" rel="stylesheet">
    <title>Feedback</title>
    <link rel="stylesheet" href="CSS/feedback.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php"> <img src="asset/logo cut.png" alt="logo" height="60px">
                    <p class="title">Book Store</p></a>
            </div>
            <div class="menu">
                <div><a href="index.php">🏚️Home</a></div>
            </div>
        </nav>
    </header>
    <div class="container">
        <h2></h2>
        <form action="index.php" method="post">
            <input type="text" name="name" id="name" placeholder="Name" required>
            <input type="tel" name="mobile" id="mobile" placeholder="Mobile No." required>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <select name="feedback" id="feedback" required> 
                <option value="#">Select</option>
                <option value="Bad">Bad</option>
                <option value="Not Bad">Not Bad</option>
                <option value="Good">Good</option>
                <!-- <option value="Impressive">Impressive</option> -->
                <option value="Excellent">Excellent</option>
            </select>
            <textarea name="textarea" id="textarea" placeholder="Write The Feedback" required></textarea>
            <button type="submit">Submit</button>
            <button type="reset">Clear</button>
            
        </form>
    </div>
    </body>
    </html>

    <?php
include_once 'config.php';


$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $feedback =($_POST['feedback']);
    $textarea = trim($_POST['textarea']);
}
?>