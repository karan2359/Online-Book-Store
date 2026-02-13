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
                <div class="logo"><a href="index.php"> <img src="asset/logo cut.png" alt="logo"
                            height="60px">
                        <p class="title">Book Store</p>
                    </a></div>
               

            </div>
            <hr>
            <nav class="navbar">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="orders.php">Orders</a></li>
                    <li><a href="cart.php">Carts</a></li>
                    <li><a href="#">Setting</a></li>
                    <li><a href="#">Feedback</a></li>
                    <li><a href="#">About US</a></li>
                    <li><a href="logout.php">Log out</a></li>
                </ul>
            </nav>
        </aside>
        <div class="sesions">
            <div class="acc-info">
               <p> Name: username</p>
                <p>Mobile: 1234567890</p>


            </div>
            <div class="count">
                <div class="ototal total">
                    <p><a href="#">Orders :</a></p>
                </div>
                <div class="ctotal total">
                    <p><a href="#">Carts :</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>