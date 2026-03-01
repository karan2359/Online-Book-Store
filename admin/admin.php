<?php 
include('../config.php');
if (!isAdmin()) { header('Location: ../index.php'); exit; }

if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Create asset folder
    $upload_dir = '../asset/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image = 'asset/' . $image_name;
        }
    }
    
 
    $stmt = $pdo->prepare("INSERT INTO books (title, author, publisher, category, subcategory, price, description, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$title, $author, $publisher, $category,$subcategory, $price, $description, $image]);
    echo "<div class='success'>✅ Book '$title' ($category > $subcategory) added successfully!</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Add Book with Subcategory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <h1>Admin Control Panel</h1>
            <div class="menu">
                <div>
                    <a href="../ACC.php" class="back-btn">← Back to Account</a>
                </div>
            </div>
    
    <!-- Add Book Form with category -->
    <div class="add-book">
        <h2> Add New Book</h2>
        <?php if (isset($_POST['add_book'])) echo "<div class='success'>✅ Book added successfully!</div>"; ?>
        
        <form method="POST" action="admin.php" enctype="multipart/form-data">
            <label>📖 Book Title</label>
            <input type="text" name="title" placeholder="Enter book title" required>
            
            <label>✍️ Author</label>
            <input type="text" name="author" placeholder="Author name" required>
            
            <label>🏢 Publisher</label>
            <input type="text" name="publisher" placeholder="Publisher" required>
            
            <label>📂 Main Category</label>
            <select name="category" id="categorySelect"  onchange="loadSubcategories()" required>
                <option value="">Select Main Category</option>
                <option value="Fiction"> Fiction</option>
                <option value="Non-Fiction"> Non-Fiction</option>
                <option value="Academics">Academics</option>
                <option value="Kids"> Kids</option>
                <option value="Adults"> Adults</option>
                <option value="Comics"> Comics</option>
                <option value="Regional Books"> Regional Books</option>
            </select>
            
           
            <label>📋 Subcategory</label>
            <select name="subcategory" id="subcategorySelect" required>
                <option value="">First select category</option>
            </select>

            
            <label>💰 Price (₹)</label>
            <input type="number" name="price" step="0.01" min="0" placeholder="99.99" required>
            
            <label>📝 Description</label>
            <textarea name="description" maxlength="500" required placeholder="Enter book description..."></textarea>
            
            <label>🖼️ Book Cover Image</label>
            <input type="file" name="image" accept="image/*" required>
            
            <button type="submit" name="add_book">🚀 Add Book Now</button>
        </form>
    </div>

    <!-- Books List -->
    <h3>📚 All Books (<?php echo $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn(); ?>)</h3>
    <div class="books-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC");
        while($book = $stmt->fetch()) {
            echo "<div class='book-item'>";
            echo "<h4>{$book['title']}</h4>";
            echo "<p><strong>✍️ {$book['author']}</strong></p>";
            echo "<p>💰 ₹{$book['price']}</p>";
            echo "<p><strong>📂 {$book['category']} / {$book['subcategory']}</strong></p>";
            if ($book['image']) {
                echo "<img src='../{$book['image']}' alt='{$book['title']}' loading='lazy'>";
            }
            echo "<div style='margin-top: 15px;'>";
            echo "<a href='edit_book.php?id={$book['id']}' class='edit-btn'>✏️ Edit</a>";
            echo "<a href='delete_book.php?id={$book['id']}' class='delete-btn' onclick='return confirm(\"Delete {$book['title']}?\")'>🗑️ Delete</a>";
            echo "</div></div>";
        }
        ?>
    </div>
     <script>
    // Dynamic subcategory loader
    function loadSubcategories() {
        const category = document.getElementById('categorySelect').value;
        const subcatSelect = document.getElementById('subcategorySelect');
        
        const subcategories = {
            'Fiction': ['Classics', 'Mythological'],
            'Non-Fiction': ['Self Improvement', 'Biography'],
            'Academics': ['Competitive Exam', 'Entrance exam', 'School', 'General Knowledge'],
            'Kids': ['Activity & Puzzles', 'Colouring & Art book', 'Essay & Letter', 'Work Book'],
            'Adults': ['Crime', 'Mystery Thriller', 'Gen Fiction', 'Fantasy Science Fiction', 'Horror'],
            'Comics': ['Superhero Comics', 'Manga Comics', 'Horror Comics'],
            'Regional Books': ['Marathi', 'Hindi', 'Gujarati']
        };
        
        subcatSelect.innerHTML = '<option value="">Select Subcategory</option>';
        if (subcategories[category]) {
            subcategories[category].forEach(subcat => {
                subcatSelect.innerHTML += `<option value="${subcat}">${subcat}</option>`;
            });
        }
    }
    </script>    
</body>
</html>
