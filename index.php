<?php include_once 'config.php';
 ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Shop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/index.css">
   
</head>

<body>
<header>

        <nav class="navbar">
            <div class="logo"><a href="index.php"> <img src="asset/logo cut.png" alt="logo" height="60px">
                    <p class="title">Book Store</p></a>
            </div>
            <div class="menu">
                <div class="search-bar">
                    <input class="searchbar" type="text" id="searchInput" 
                    placeholder="🔍 Search by Name, Author, Category..." 
                    onkeyup="searchBooks(this.value)">
                    <div id="searchResults" style="position:absolute; background:white; width:300px; max-height:200px; overflow-y:auto; top:60px; display:none; z-index:1000; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.2);"><!-- Live results here -->
                    </div>
                </div>
                

                <div class="card">
                    <a href="cart.php">🛒 Cart</a>
                    <a href="orders.php">Orders</a>
                </div>
                <div class="acc card">
                    <?php 
                    include 'config.php';
                    if (isLoggedIn()) {
                        // if (isAdmin()) {
                        //     echo "<a href='admin/admin.php'>➕ Add Book</a>  ";
                        // }
                        echo " <div style='color:white;'> <a href ='Acc.php' style='padding: 0px 15px 0px 0px;'>Account</a> <a href='logout.php'>Log out</a>";
                        // echo " <div style='color:white;'> <a href ='Acc.php' style='padding: 0px 15px 0px 0px;'>Hi, {$_SESSION['fullname']}</a> <a href='log out.php'>Logout</a>";
                    } else {
                        echo "<a href='login.php'>Log in</a> | <a href='signin.php'>Sign Up</a>";
                    }?>
               </div>

            </div>
        </nav>

<ul class="category">
    <li class="dropdown list" onclick="filterBooks('All', '')" class="active"> All Books</li>
            <li class="dropdown list" onclick="filterBooks('Fiction', '')">Fiction
                <ul>
                    <li class="a "  onclick="filterBooks('Fiction', 'Classics')">Classics</li>                  
                    <li  class="a " onclick="filterBooks('Fiction', 'Mythological')">Mythological</li>
                </ul>
            </li>
            <li  class="dropdown list" onclick="filterBooks('Non-Fiction', '')">Non-Fiction
                <ul>
                    <li class="a "  onclick="filterBooks('Non-Fiction', 'Self Improvement')">Self Improvement</li>
                    <li class="a "  onclick="filterBooks('Non-Fiction', 'Biography')">Biography</li>
                    <!-- <li><a href="#"></a></li> class="a " 
                        <li><a href="#"></a></li>
                        <li><a href="#"></a></li> -->
                </ul>
            </li>
            <li class="dropdown list" onclick="filterBooks('Academics', '')">Academics
                <ul>              
                    <li  class="a " onclick="filterBooks('Academics', 'Competitive Exam')">Competitive Exam</li>
                    <li class="a "  onclick="filterBooks('Academics', 'Entrance exam')">Entrance exam</li>
                    <li class="a "  onclick="filterBooks('Academics', 'School')"> School</li>
                    <li class="a "  onclick="filterBooks('Academics', 'General Knowledge')"> General Knowledge</li>
                    <!-- <li><a href="#"></a></li> -->
                </ul>
            </li>
            
            <li class="dropdown list" onclick="filterBooks('Kids', '')">Kids
            <ul>
                <li  class="a"  onclick="filterBooks('Kids', 'Activity &amp; Puzzles','Activity','Puzzles')"> Activity &amp; Puzzles</li>
                <!-- <li><a href="#">Activity &amp; Puzzles</a></li> -->
                <li  class="a"  onclick="filterBooks('Kids', 'Colouring &amp; Art book ','Colouring','Art book')"> Colouring &amp; Art book </li>
                <li  class="a"  onclick="filterBooks('Kids', 'Essay &amp; Letter ','Essay','Letter')"> Essay &amp; Letter </li>
                <li  class="a"  onclick="filterBooks('Kids', 'Work Book')">Work Book</li>
                <!-- <li   class="a"  onclick="filterBooks('Kids', 'General Knowledge')">General Knowledge</li>                     -->
                </ul>
            </li>
            <li class="dropdown list" onclick="filterBooks('Adults', '')">Adults
                <ul>
                    <li  class="a"  onclick="filterBooks('Adults', 'Crime')">Crime</li>
                    <li  class="a"  onclick="filterBooks('Adults', 'Mystery Thriller')">Mystery Thriller</li>
                    <lI  class="a"  onclick="filterBooks('Adults', 'Gen Fiction')">Gen Fiction</li>                    
                    <li  class="a"  onclick="filterBooks('Adults', 'Fantasy Science Fiction')">Fantasy Science Fiction</li>                   
                    <li  class="a"  onclick="filterBooks('Adults', 'Horror')">Horror</li>
                </ul>
            </li>

                <li class="dropdown list" onclick="filterBooks('Comics', '')">Comics
                <ul>
                    <li class="a"  onclick="filterBooks('Comics', 'Superhero Comics')">Superhero Comics</li>
                    <li  class="a" onclick="filterBooks('Comics', 'Manga Comics')">Manga Comics</li>
                    <li class="a"  onclick="filterBooks('Comics', 'Horror Comics')">Horror Comics</li>
                    <!-- <li><a href="#"></a></li>
                        <li><a href="#"></a></li> -->
                </ul>
            </li>
            <li class="dropdown list" onclick="filterBooks('Regional Books', '')">Regional Books
                <ul>
                    <li  class="a" onclick="filterBooks('', 'Marathi')">Marathi</li>
                    <li  class="a" onclick="filterBooks('Regional Books', 'Hindi')">Hindi</li>
                    <li  class="a" onclick="filterBooks('Regional Books', 'Gujarati')">Gujarati</li>
                </ul>
            </li>
</ul>
</header>
    <main>
<div class="books-grid" id="booksContainer">
<?php
$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC");
while ($book = $stmt->fetch()) {
    $subcategory = explode(',', $book['subcategory'] ?? $book['category'])[0];
    echo "<div class='books-gridd'>
    <div class='book-card' 
         data-category='{$book['category']}' 
         data-subcategory='{$subcategory}'
         data-id='{$book['id']}'>
        <img src='{$book['image']}' alt='{$book['title']}'>

            <div class='book-info'>
                <div class='subcategory-tag'>{$book['category']} / {$subcategory}</div>
                <h3>{$book['title']}</h3>
                <p><strong>✍️ {$book['author']}</strong></p>
                <p>🏢 {$book['publisher']}</p>
                <p class='price' style='font-size:large; font-weight:bold;'>₹{$book['price']}</p>
                <!--<p class='desc'>".substr($book['description'] ?? '', 0, 80)."...</p>-->
                <button class='add-cart-btn' onclick='addToCart({$book['id']})'>🛒 Add to Cart</button>
            </div>
        </div>
    </div>";
}
?>
</div>

    </div>

    <script>
        // Filter by Category + Subcategory
        function filterBooks(mainCategory, subcategory) {
            const books = document.querySelectorAll('.book-card');
            let visibleCount = 0;
            
            books.forEach(book => {
                const bookCategory = book.dataset.category;
                const bookSubcategory = book.dataset.subcategory;
                
                if (mainCategory === 'All') {
                    book.style.display = 'block';
                    visibleCount++;
                } 
                else if (mainCategory === bookCategory && 
                        (subcategory === '' || subcategory === bookSubcategory)) {
                    book.style.display = 'block';
                    visibleCount++;
                } 
                else {
                    book.style.display = 'none';
                }
            });
                  
            // Show result count
            document.getElementById('resultCount').textContent = 
                visibleCount + ' books found';
        }

        // Add to cart with category info
        function addToCart(bookId, title, price, category) {
            // Your existing cart logic
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `book_id=${bookId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`✅ ${title} added to cart!`);
                    updateCartCount();
                }
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
        // LIVE SEARCH - Name, Author, Category
function searchBooks(query) {
    const books = document.querySelectorAll('.book-card');
    const resultsDiv = document.getElementById('searchResults');
    let resultsHTML = '';
    let visibleCount = 0;
    
    // Hide all books first
    books.forEach(book => {
        book.style.display = 'none';
    });
    
    if (query.length < 2) {
        resultsDiv.style.display = 'none';
        return;
    }
    
    // Search Logic
    books.forEach(book => {
        const title = book.querySelector('h3')?.textContent.toLowerCase() || '';
        const author = book.querySelector('p strong')?.textContent.toLowerCase() || '';
        const category = book.dataset.category.toLowerCase();
        const searchTerm = query.toLowerCase();
        
        // Match Title OR Author OR Category
        if (title.includes(searchTerm) || 
            author.includes(searchTerm) || 
            category.includes(searchTerm)) {
            
            book.style.display = 'block';
            visibleCount++;
            
            // Add to dropdown results
            const bookTitle = book.querySelector('h3').textContent;
            const bookPrice = book.querySelector('.price').textContent;
            resultsHTML += `
                <div style="padding:12px; border-bottom:1px solid #eee; cursor:pointer; hover:background:#f0f0f0;"
                     onclick="scrollToBook(${book.dataset.id})">
                    <strong>${bookTitle.substring(0,30)}${bookTitle.length>30?'...':''}</strong><br>
                    <small>${author.substring(0,20)}... | ₹${bookPrice}</small>
                </div>
            `;
        }
    });
    
    // Show results dropdown
    resultsDiv.innerHTML = resultsHTML || '<div style="padding:15px; color:#999;">No books found</div>';
    resultsDiv.style.display = resultsHTML ? 'block' : 'none';
    
    // Show count
    document.getElementById('searchCount')?.textContent(`Found ${visibleCount} books`);
}

// Scroll to specific book
function scrollToBook(bookId) {
    const book = document.querySelector(`[data-id="${bookId}"]`);
    if (book) {
        book.scrollIntoView({ behavior: 'smooth', block: 'center' });
        document.getElementById('searchInput').value = '';
        document.getElementById('searchResults').style.display = 'none';
    }
}

// Close search on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-bar')) {
        document.getElementById('searchResults').style.display = 'none';
    }
});

    </script>
    </main>
    <footer>
        <p>&copy;Footer Page</p>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>