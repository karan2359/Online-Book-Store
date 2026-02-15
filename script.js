let cart = [];

// Filter books by category + subcategory
function filterBooks(mainCategory, subcategory = '') {
    const books = document.querySelectorAll('.book-card');
    let visibleCount = 0;
    
    books.forEach(book => {
        const bookCategory = book.dataset.category;
        const bookSubcategory = book.dataset.subcategory || '';
        
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
    
    // Update active button
    document.querySelectorAll('.category-filter button').forEach(btn => {
        btn.classList.remove('active');
    });
    if (event) event.target.classList.add('active');
    
    console.log(`${visibleCount} books shown for ${mainCategory}/${subcategory}`);
}

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
            updateCartCount();
        } else {
            alert('❌ ' + (data.message || 'Failed to add to cart'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Network error. Please try again.');
    });
}

// // Update cart count badge in navbar
// function updateCartCount() {
//     fetch('get_cart.php')
//     .then(response => response.json())
//     .then(items => {
//         cart = items;
//         const totalQuantity = items.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        
//         // Update navbar cart count
//         let cartCount = document.querySelector('.cart-count');
//         if (!cartCount) {
//             // Create cart count if doesn't exist
//             const cartLink = document.querySelector('.card a');
//             if (cartLink) {
//                 cartCount = document.createElement('span');
//                 cartCount.className = 'cart-count';
//                 cartCount.style.cssText = 'background:red;color:white;border-radius:50%;padding:2px 6px;font-size:12px;margin-left:5px;';
//                 cartLink.appendChild(cartCount);
//             }
//         }
//         if (cartCount) {
//             cartCount.textContent = totalQuantity || 0;
//         }
//     })
//     .catch(error => console.error('Cart update error:', error));
// }

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
    updateCartCount(); // Load initial cart count
    
    // Auto-hide alerts after 3 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => alert.style.display = 'none', 3000);
    });
});

// Global functions for login/logout (add to your existing code)
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
    updateCartCount();
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
            // Update cart count in navbar
            updateCartCount(data.cart_count || 0);
            
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

// Helper functions
function updateCartCount(count) {
    const cartBadge = document.querySelector('.cart-count');
    if (cartBadge) {
        cartBadge.textContent = count;
        cartBadge.style.display = count > 0 ? 'inline' : 'none';
    }
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
