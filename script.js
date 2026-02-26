let cart = [];

<<<<<<< HEAD
=======
// Filter books by category + subcategory
function filterBooks(mainCategory) {
    const books = document.querySelectorAll('.book-card');
    let visibleCount = 0;
    
    books.forEach(book => {
        const bookCategory = book.dataset.category;
        
        if (mainCategory === 'All') {
            book.style.display = 'block';
            visibleCount++;
        } 
        else if (mainCategory === bookCategory) {
            book.style.display = 'block';
            visibleCount++;
        } 
        else {
            book.style.display = 'none';
        }
    });
    
    // Update active button
    document.querySelectorAll('.category-filter button').forEach(btn => {
        btn.classList.remove('active');
    });
    if (event) event.target.classList.add('active');
    
    console.log(`${visibleCount} books shown for ${mainCategory}`);
}

>>>>>>> 0bf9d8ceb3586113e861d10e5ca0c214f2f17145
// Add to cart function
function addToCart(bookId, title, price) {
    // Check if user is logged in (you'll implement this later)
    if (typeof isLoggedIn === 'function' && !isLoggedIn()) {
        alert('Please login to add to cart');
        if (typeof showLogin === 'function') showLogin();
        return;
    }
    
    // Send to PHP backend
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `book_id=${bookId}&quantity=1&title=${encodeURIComponent(title)}&price=${price}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + title + ' added to cart!');
           
        } else {
            alert('❌ ' + (data.message || 'Failed to add to cart'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Network error. Please try again.');
    });
}

<<<<<<< HEAD

=======
>>>>>>> 0bf9d8ceb3586113e861d10e5ca0c214f2f17145
// Place order function
function placeOrder() {
    if (cart.length === 0) {
        alert('🛒 Your cart is empty!');
        return;
    }
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    if (confirm(`Place order for ₹${total.toFixed(2)}?`)) {
        fetch('place_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                cart: cart,
                total_amount: total
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Order placed successfully! Order ID: ' + data.order_id);
                cart = [];
                updateCartCount();
                window.location.href = 'orders.php';
            } else {
                alert('❌ Order failed: ' + data.message);
            }
        });
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Bookstore JS loaded!');
    
    
    // Auto-hide alerts after 3 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => alert.style.display = 'none', 3000);
    });
});

// Global functions for login/logout 
function isLoggedIn() {
    // Check session (you'll implement PHP check)
    return localStorage.getItem('loggedIn') === 'true';
}

function showLogin() {
    // Show your login modal
    alert('Login modal will open here');
}

function logout() {
    localStorage.removeItem('loggedIn');
    cart = [];
    
}


// Check login status on page load
document.addEventListener('DOMContentLoaded', function() {
    const isLoggedIn = localStorage.getItem('loggedIn') === 'true';
    const userName = localStorage.getItem('userName');
    const logoutBtn = document.getElementById('logoutBtn');
    const userWelcome = document.getElementById('userWelcome');
    
    if (isLoggedIn && userName) {
        if (userWelcome) userWelcome.textContent = `Welcome, ${userName}!`;
        if (logoutBtn) logoutBtn.style.display = 'block';
    }
});

function logout() {
    localStorage.removeItem('loggedIn');
    localStorage.removeItem('userName');
    localStorage.removeItem('isAdmin');
    window.location.reload();
}
function addToCart(bookId) {
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '⏳ Adding...';
    btn.disabled = true;
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `book_id=${bookId}&quantity=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            
            
            // Success message
            showNotification(data.message, 'success');
            
            if (data.guest_cart) {
                showNotification('Login to save your cart permanently!', 'info');
            }
        } else {
            if (data.message.includes('login')) {
                window.location.href = 'login.php';
            } else {
                showNotification(data.message, 'error');
            }
        }
    })
    .catch(error => {
        showNotification('Network error!', 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}



function showNotification(message, type) {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.style.cssText = `
        position:fixed; top:20px; right:20px; padding:15px 20px; 
        background:${type==='success'?'#4facfe':type==='error'?'#e74c3c':'#f39c12'};
        color:white; border-radius:5px; z-index:9999; font-weight:bold;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
 // Filter by Category
        function filterBooks(mainCategory) {
            const books = document.querySelectorAll('.book-card');
            let visibleCount = 0;
            
            books.forEach(book => {
                const bookCategory = book.dataset.category;

                
                if (mainCategory === 'All') {
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