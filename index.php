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
    <!-- Navigation Bar -->
        <nav class="navbar">
            <div class="logo"><a href="index.php"> <img src="asset/logo cut.png" alt="logo" height="60px">
                    <p class="title">Book Store</p></a>
            </div>
            <div class="menu">
                <!-- Search Bar -->
                <div class="search-bar">
                    <input class="searchbar" type="text" id="searchInput" 
                    placeholder="🔍 Search by Name, Author, Category..." 
                    onkeyup="searchBooks(this.value)">
                    <div id="searchResults" style="position:absolute; background:white; width:300px; max-height:200px; overflow-y:auto; top:60px; display:none; z-index:1000; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.2);">
                    </div>
                </div>
                

                <div class="card">
                    <a href="cart.php">🛒 Cart</a>
                    <a href="orders.php">📦Orders</a>
                </div>
                <div class="acc card">
                    <?php 
                    include 'config.php';
                    if (isLoggedIn()) {
                       
                        echo " <div style='color:white;'> <a href ='Acc.php' style='padding: 0px 15px 0px 0px;'>👤 Account</a> <a href='logout.php'> Log out</a>";
                
                    } else {
                        echo "<a href='login.php'>Log in</a> | <a href='signin.php'>Sign Up</a>";
                    }?>
               </div>

            </div>
        </nav>
<!-- Category -->
<ul class="category">
    <li class="dropdown list" onclick="filterBooks('All', '')" class="active"> All Books</li>
            <li class="dropdown list" onclick="filterBooks('Fiction', '')">Fiction</li>
            <li  class="dropdown list" onclick="filterBooks('Non-Fiction', '')">Non-Fiction</li>
            <li class="dropdown list" onclick="filterBooks('Academics', '')">Academics</li>            
            <li class="dropdown list" onclick="filterBooks('Kids', '')">Kids</li>
            <li class="dropdown list" onclick="filterBooks('Adults', '')">Adults</li>
            <li class="dropdown list" onclick="filterBooks('Comics', '')">Comics</li>
            <li class="dropdown list" onclick="filterBooks('Regional Books', '')">Regional Books</li>
</ul>
</header>
    <main>
<div class="books-grid" id="booksContainer">
<?php
$stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC");
$booksArray = []; // For JS access
while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $booksArray[] = $book; // Store for quick view
    $safeBook = json_encode($book, JSON_HEX_APOS | JSON_HEX_QUOT); // Safe JSON
    echo "<div class='books-gridd'>
    <div class='book-card' data-category='{$book['category']}' data-id='{$book['id']}'
    onclick='openQuickView(" . $safeBook . ")' style='cursor:pointer;'>
        <img src='{$book['image']}' alt='{$book['title']}'>

            <div class='book-info'>
                <div class='subcategory-tag'>Category:{$book['category']} </div>
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
<script>window.books = <?php echo json_encode($booksArray); ?>;</script>
</div>
    </div>
    </main>
    <footer>
        <ul>
            <li><a href="about.php">About Us </a></li>
            <li>|</li>
            <li><a href="feedback.php">Feedback </a></li>
            <li>|</li>
            <li><a href="Acc.php">Account</a></li>
        </ul>
        <p>&copy;2026 BookStore. All rights reserved. | Made with ❤️ for book lovers</p>
    </footer>
    
    
    <!-- Quick View Modal -->
<div id="quickViewModal" class="quick-view-modal" style="display: none;">
    <div class="modal-overlay" onclick="closeQuickView()"></div>
    <div class="modal-content">
        <span class="close-btn" onclick="closeQuickView()">&times;</span>
        <div class="qv-book-details">
            <img id="qvBookImage" src="" alt="Book Cover" class="qv-image">
            <div class="qv-info">
                <p><h2 id="qvTitle"></h2></p>
                <p><strong>Author:</strong> <span id="qvAuthor"></span></p>
                <p><strong>Publisher:</strong> <span id="qvPublisher"></span></p>
                <p><strong>Category:</strong> <span id="qvCategory"></span></p>
                <p><strong>Price:</strong> <span id="qvPrice" class="price"></span></p>
                <div id="qvDescription" class="description"></div>
                <div class="qv-actions">
                    <?php 
                    include 'config.php';
                    if (isLoggedIn()) {                       
                        echo "<button onclick='addToCartSilent()' class='btn-add-cart'>🛒 Add to Cart</button>
                                <button onclick='placeOrderDirect()' class='btn-buy'>🚀 Place Order</button>";                
                    } else {
                        echo "<p style='color:red;'>⚠️ Please Login to Buy your Book </p>";
                    }?>    
</div>
        </div>
        </div>
        <div class="qv-suggestions">
            <h3>More Suggestions</h3>
            <div id="qvSuggestions" class="suggestion-grid"></div>
        </div>
    </div>
</div>
<script>
    window.books = <?php echo json_encode($booksArray); ?>;
// Filter by Category + Subcategory
        function filterBooks(mainCategory) {
            const books = document.querySelectorAll('.book-card');
            let visibleCount = 0;
            
            books.forEach(book => {
                const bookCategory = book.dataset.category;
              
                
                if (mainCategory === 'All') {
                    book.style.display = 'block';
                    visibleCount++;
                } 
                else if (mainCategory === bookCategory ) {
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
            //  cart logic
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `book_id=${bookId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`✅ ${title} added to cart!`);
                }
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
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


// FIXED Quick View Functions - No alerts + Direct Order
function openQuickView(book) {
    document.getElementById('qvTitle').textContent = book.title || 'N/A';
    document.getElementById('qvAuthor').textContent = book.author || 'N/A';
    document.getElementById('qvPublisher').textContent = book.publisher || 'N/A';
    document.getElementById('qvCategory').textContent = book.category || 'N/A';
    document.getElementById('qvPrice').textContent = '₹' + (book.price || 0);
    document.getElementById('qvDescription').textContent = book.description || 'No description available.';
    document.getElementById('qvBookImage').src = book.image || 'asset/no-image.png';
    
    // Store current book ID globally for cart/order
    window.currentQuickViewBook = book;
    
    loadSuggestions(book.category, book.id);
    document.getElementById('quickViewModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeQuickView() {
    document.getElementById('quickViewModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ✅ SILENT Add to Cart
function addToCartSilent() {
    const book = window.currentQuickViewBook;
    if (!book || !book.id) return;
    
    const btn = event.target;
    const originalText = btn.textContent;
    
    // Immediate visual feedback
    btn.textContent = '⏳ Adding...';
    btn.disabled = true;
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `book_id=${book.id}&quantity=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count silently
            updateCartCount();
            // Success feedback
            btn.textContent = '✅ Added!';
            btn.style.background = '#38a169';
            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '';
                btn.disabled = false;
            }, 2000);
        } else {
            btn.textContent = '❌ Error';
            setTimeout(() => {
                btn.textContent = originalText;
                btn.disabled = false;
            }, 1500);
        }
    })
    .catch(() => {
        btn.textContent = '❌ Failed';
        setTimeout(() => {
            btn.textContent = originalText;
            btn.disabled = false;
        }, 1500);
    });
}


function loadSuggestions(category, excludeId) {
    const suggestions = window.books.filter(b => 
        b.category === category && parseInt(b.id) !== parseInt(excludeId)
    ).slice(0, 4);
    
    const container = document.getElementById('qvSuggestions');
    container.innerHTML = '';
    suggestions.forEach(book => {
        const card = document.createElement('div');
        card.className = 'suggestion-card';
        card.onclick = () => openQuickView(book);
        card.style.cursor = 'pointer';
        card.innerHTML = `
            <img src="${book.image}" alt="${book.title}">
            <h4>${book.title.substring(0,25)}${book.title.length>25?'...':''}</h4>
            <p>₹${book.price}</p>
        `;
        container.appendChild(card);
    });
}
function placeOrderDirect() {
    const book = window.currentQuickViewBook;
    if (!book || !book.id) {
        alert('Book not found!');
        return;
    }
    
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = '⏳ Processing...';
    btn.disabled = true;
    
    // Add to cart first, then go to checkout
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `book_id=${book.id}&quantity=1`
    })
    .then(() => {
        closeQuickView();
        window.location.href = 'cart.php';
    })
    .catch(() => {
        btn.textContent = 'Login Required';
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 1000);
    });
}
function updateCartCount() {
    // Update navbar cart badge (if you have one)
    fetch('get_cart_count.php')
    .then(r => r.json())
    .then(data => {
        console.log('Cart count:', data.count);
    })
    .catch(() => {}); // Silent fail
}

// ESC to close
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeQuickView();
});
</script><script src="script.js"></script>
</body>
</html>