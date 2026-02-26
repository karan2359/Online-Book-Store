<?php
include('../config.php');
if (!isAdmin()) { 
    header('Location: ../index.php'); 
    exit; 
}

$id = $_GET['id'] ?? 0;
$book = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
}

if (!$book) {
    header('Location: admin.php');
    exit;
}

if (isset($_POST['update_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'] ?? '';
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    $image = $book['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../asset/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image = 'asset/' . $image_name;
            // Delete old image
            if ($book['image'] && file_exists('../' . $book['image'])) {
                unlink('../' . $book['image']);
            }
        }
    }
    
    $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, publisher=?, category=?, subcategory=null, price=?, description=?, image=? WHERE id=?");
    $stmt->execute([$title, $author, $publisher, $category, $price, $description, $image, $id]);
    
    header('Location: admin.php?updated=1');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Book - Admin Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            font-family: "Roboto Slab", serif; 
        }
        body { 
            background: linear-gradient(135deg, #393E46, #4a5568); 
            color: white; 
            padding: 20px; 
        }
        .container { 
            max-width: 700px;
            margin: 40px auto; 
            padding: 30px; 
            background: rgba(148, 137, 121, 0.2); 
            border-radius: 20px; 
            border: 3px solid #2d3748; 
        }
        h1 { 
            text-align: center; 
            margin-bottom: 30px; 
            color: #f7fafc; 
        }
        label { 
            display: block; 
            font-weight: bold; 
            margin: 15px 0 5px 0; 
            color: #f7fafc; 
        }
        input, select, textarea { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 15px; 
            border: 2px solid #4a5568; 
            border-radius: 10px; 
            font-size: 16px; 
            box-sizing: border-box; 
            background: rgba(255,255,255,0.9); 
        }
        button { 
            background: linear-gradient(135deg, #ed8936, #dd6b20); 
            color: white; 
            padding: 15px 40px; 
            border: none; 
            border-radius: 10px; 
            font-size: 18px; 
            font-weight: bold; 
            cursor: pointer; 
            width: 100%; 
            max-width: 300px; 
            margin: 20px auto; 
            display: block; 
        }
        button:hover { 
            background: linear-gradient(135deg, #dd6b20, #c05621); 
        }
        .back-btn { 
            background: #6b7280; 
            display: inline-block; 
            padding: 12px 25px; 
            margin: 10px; 
            text-decoration: none; 
            border-radius: 8px; 
            color: white; 
        }
        .book-preview { 
            text-align: center; 
            margin: 20px 0; 
        }
        .book-preview img { 
            width: 120px; 
            height: 160px; 
            object-fit: cover; 
            border-radius: 8px; 
            border: 3px solid #ed8936; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Book: <?php echo htmlspecialchars($book['title']); ?></h1>
        
        <div class="book-preview">
            <img src="../<?php echo htmlspecialchars($book['image']); ?>" alt="Current book cover">
            <p>Current: <?php echo htmlspecialchars($book['category']); ?> </p>
        </div>
        
        <form method="POST" action="edit_book.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
            <label>📖 Book Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            
            <label>✍️ Author</label>
            <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            
            <label>🏢 Publisher</label>
            <input type="text" name="publisher" value="<?php echo htmlspecialchars($book['publisher']); ?>" required>
            
            <label>📂 Main Category</label>
            <select name="category" id="categorySelect" onchange="loadSubcategories()" required>
                <option value="">Select Category</option>
                <option value="Fiction" <?php echo $book['category']=='Fiction'?'selected':'';?>>📚 Fiction</option>
                <option value="Non-Fiction" <?php echo $book['category']=='Non-Fiction'?'selected':'';?>>📖 Non-Fiction</option>
                <option value="Academics" <?php echo $book['category']=='Academics'?'selected':'';?>>🎓 Academics</option>
                <option value="Kids" <?php echo $book['category']=='Kids'?'selected':'';?>>👶 Kids</option>
                <option value="Adults" <?php echo $book['category']=='Adults'?'selected':'';?>>👨 Adults</option>
                <option value="Comics" <?php echo $book['category']=='Comics'?'selected':'';?>>🦸 Comics</option>
                <option value="Regional Books" <?php echo $book['category']=='Regional Books'?'selected':'';?>>🌍 Regional Books</option>
            </select>
            
            <!-- <label>📋 Subcategory</label>
            <input type="text" name="subcategory" value="<?php echo htmlspecialchars($book['subcategory']); ?>" placeholder="Enter subcategory" required> -->
            
            <label>💰 Price (₹)</label>
            <input type="number" name="price" step="0.01" min="0" value="<?php echo $book['price']; ?>" required>
            
            <label>📝 Description</label>
            <textarea name="description" maxlength="500" required><?php echo htmlspecialchars($book['description']); ?></textarea>
            
            <label>🖼️ New Book Cover Image (optional)</label>
            <input type="file" name="image" accept="image/*">
            <small>Leave empty to keep current image</small>
            
            <button type="submit" name="update_book">💾 Update Book</button>
        </form>
        
        <a href="admin.php" class="back-btn">← Back to Admin Panel</a>
    </div>


</body>
</html>
