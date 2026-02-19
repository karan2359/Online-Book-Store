<?php
session_start();
include_once 'config.php';

$book_id = $_GET['id'] ?? 0;
if (!$book_id || !is_numeric($book_id)) {
    header('Location: index.php');
    exit;
}

// Fetch book details
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: index.php');
    exit;
}

// Fetch 3 related books (same category) - NO REVIEWS TABLE NEEDED
$related_stmt = $pdo->prepare("
    SELECT * FROM books 
    WHERE category = ? AND id != ? 
    ORDER BY RAND() 
    LIMIT 3
");
$related_stmt->execute([$book['category'], $book_id]);
$related_books = $related_stmt->fetchAll();

// Reviews - SAFE (no table needed)
$reviews = []; // Empty array if no reviews table
$has_reviews_table = false;
try {
    $reviews_stmt = $pdo->prepare("
        SELECT r.*, u.fullname 
        FROM reviews r 
        LEFT JOIN users u ON r.user_id = u.id 
        WHERE r.book_id = ? 
        ORDER BY r.created_at DESC 
        LIMIT 5
    ");
    $reviews_stmt->execute([$book_id]);
    $reviews = $reviews_stmt->fetchAll();
    $has_reviews_table = true;
} catch (PDOException $e) {
    // No reviews table = no problem!
    $reviews = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?> - BookStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/index.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Roboto Slab', serif; 
            background: linear-gradient(135deg, #393E46 0%, #4a5568 100%);
            color: #333; 
            line-height: 1.7;
        }
        .navbar {
            background: #222831;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .logo a { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            color: white; 
            text-decoration: none; 
            font-size: 28px;
            font-weight: bold;
        }
        .logo img { height: 60px; }
        .back-btn { 
            background: #48bb78; 
            color: white; 
            padding: 12px 25px; 
            border-radius: 25px; 
            text-decoration: none; 
            font-weight: bold;
        }
        .main-container {
            margin-top: 120px;
            max-width: 1200px;
            margin: 40px auto;
            padding: 40px 20px;
        }
        
        /* Book Hero */
        .book-hero {
            background: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 25px;
            margin-bottom: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }
        .book-hero-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 50px;
        }
        .book-image-hero {
            width: 100%;
            max-width: 400px;
            height: 500px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .book-title-hero {
            font-size: 2.5rem;
            color: #2d3748;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .book-price-hero {
            font-size: 2.8rem;
            font-weight: 900;
            color: #e74c3c;
            margin: 25px 0;
        }
        .book-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .meta-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s;
        }
        .meta-card:hover {
            border-color: #48bb78;
            transform: translateY(-5px);
        }
        .buy-section {
            background: linear-gradient(135deg, #48bb78, #38a169);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            color: white;
            margin: 30px 0;
        }
        .buy-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 18px 35px;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            margin: 0 10px;
            box-shadow: 0 10px 30px rgba(231,76,60,0.4);
            transition: all 0.3s;
        }
        .buy-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(231,76,60,0.6);
        }
        
        /* Related Books */
        .related-section {
            background: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 25px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .related-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-top: 30px;
        }
        .related-card {
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .related-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .related-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
        }
        .related-title {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: #2d3748;
        }
        .related-price {
            font-size: 1.4rem;
            font-weight: bold;
            color: #e74c3c;
        }
        
        @media (max-width: 768px) {
            .book-hero-grid { grid-template-columns: 1fr; gap: 30px; }
            .related-grid { grid-template-columns: 1fr; }
            .main-container { padding: 20px 15px; margin-top: 100px; }
            .book-title-hero { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">
                <img src="asset/logo cut.png" alt="logo" height="60px">
                <span>Book Store</span>
            </a>
        </div>
        <a href="javascript:history.back()" class="back-btn">← Back to Books</a>
    </nav>

    <div class="main-container">
        <!-- Book Details Hero -->
        <div class="book-hero">
            <div class="book-hero-grid">
                <img src="<?= $book['image'] ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>" 
                     class="book-image-hero"
                     onerror="this.src='https://via.placeholder.com/400x500/eee/ccc?text=No+Image'">
                
                <div>
                    <h1 class="book-title-hero"><?= htmlspecialchars($book['title']) ?></h1>
                    
                    <div class="book-price-hero">₹<?= number_format($book['price'], 2) ?></div>
                    
                    <div class="book-meta-grid">
                        <div class="meta-card">
                            <div style="font-size: 14px; color: #666; margin-bottom: 10px; text-transform: uppercase;">Author</div>
                            <div style="font-size: 22px; font-weight: 700; color: #2d3748;"><?= htmlspecialchars($book['author']) ?></div>
                        </div>
                        <div class="meta-card">
                            <div style="font-size: 14px; color: #666; margin-bottom: 10px; text-transform: uppercase;">Publisher</div>
                            <div style="font-size: 22px; font-weight: 700; color: #2d3748;"><?= htmlspecialchars($book['publisher']) ?></div>
                        </div>
                        <div class="meta-card">
                            <div style="font-size: 14px; color: #666; margin-bottom: 10px; text-transform: uppercase;">Category</div>
                            <div style="font-size: 22px; font-weight: 700; color: #2d3748;"><?= htmlspecialchars($book['category']) ?></div>
                        </div>
                        <?php if ($book['subcategory']): ?>
                        <div class="meta-card">
                            <div style="font-size: 14px; color: #666; margin-bottom: 10px; text-transform: uppercase;">Subcategory</div>
                            <div style="font-size: 20px; font-weight: 700; color: #2d3748;"><?= htmlspecialchars($book['subcategory']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Buy Section -->
                    <div class="buy-section">
                        <h3 style="margin-bottom: 25px; font-size: 1.6rem;">Ready to Read?</h3>
                        <form method="POST" action="add_to_cart.php" style="display: inline-block; margin-right: 15px;">
                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                            <!-- <button class="add-cart-btn" onclick="addToCart({$book['id']}">🛒 Add to Cart</button> -->
                            <button type="submit" name="add_to_cart" class="buy-btn">🛒 Add to Cart</button>
                        </form>
                        <button onclick="buyNow(<?= $book['id'] ?>)" class="buy-btn">💰 Buy Now</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Related Books (Bottom) -->
        <?php if (!empty($related_books)): ?>
        <div class="related-section">
            <h2 style="font-size: 2.2rem; color: #2d3748; margin-bottom: 30px;">
                📚 More <?= htmlspecialchars($book['category']) ?> Books
            </h2>
            <div class="related-grid">
                <?php foreach ($related_books as $related): ?>
                <a href="book_details.php?id=<?= $related['id'] ?>" class="related-card">
                    <img src="<?= htmlspecialchars($related['image']) ?>" 
                         alt="<?= htmlspecialchars($related['title']) ?>" 
                         class="related-image">
                    <h4 class="related-title"><?= htmlspecialchars($related['title']) ?></h4>
                    <p style="color: #666; margin-bottom: 12px; font-size: 15px;">
                        by <?= htmlspecialchars($related['author']) ?>
                    </p>
                    <div class="related-price">₹<?= number_format($related['price'], 2) ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function buyNow(bookId) {
            // Add to cart and redirect
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'add_to_cart.php';
            form.innerHTML = `
                <input type="hidden" name="book_id" value="${bookId}">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="buy_now" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
