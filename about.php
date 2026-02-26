<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - BookStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Roboto', sans-serif; 
            line-height: 1.7;
            color: #333;
            overflow-x: hidden;
        }
        
        /* Navigation */
        .navbar {
            position: sticky;
            top: 0;
            width: 100%;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            z-index: 1;
            padding: 1rem 5%;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            text-decoration: none;
        }
        .logo span {
            color: #48bb78;
        }
        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        .nav-links a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }
        .nav-links a:hover {
            color: #48bb78;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background: #48bb78;
            transition: width 0.3s ease;
        }
        .nav-links a:hover::after {
            width: 100%;
        }
        .cta-btn {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(72,187,120,0.4);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><rect fill="%23f7fafc" width="1200" height="800"/><path fill="%2348bb78" d="M0 400Q300 200 600 400T1200 400V800H0z"/><path fill="%232d3748" d="M0 600Q200 500 400 600T800 600V800H0z"/></svg>');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        .hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(3rem, 8vw, 6rem);
            margin-bottom: 1rem;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .hero-content p {
            font-size: 1.5rem;
            max-width: 600px;
            margin: 0 auto 2rem;
            opacity: 0.95;
        }

        /* Sections */
        section {
            padding: 100px 5%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 5vw, 4rem);
            text-align: center;
            margin-bottom: 4rem;
            color: #2d3748;
            position: relative;
        }
        .section-title::after {
            content: '';
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #48bb78, #38a169);
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Our Story */
        .story-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 3rem;
            align-items: center;
        }
        .story-text {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #4a5568;
        }
        .story-image {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8rem;
            color: #cbd5e0;
        }

        /* Mission */
        .mission {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        }
        .mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            text-align: center;
        }
        .mission-card {
            padding: 2.5rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .mission-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
        }
        .mission-icon {
            font-size: 4rem;
            color: #48bb78;
            margin-bottom: 1.5rem;
        }
        .mission-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        /* Team */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
        }
        .team-card {
            text-align: center;
            padding: 2rem;
            background: rgba(255,255,255,0.9);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .team-card:hover {
            transform: translateY(-10px);
        }
        .team-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #48bb78, #38a169);
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            font-weight: 700;
        }
        .team-card h3 {
            font-family: 'Playfair Display', serif;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        .team-role {
            color: #48bb78;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        /* CTA */
        .cta-section {
            text-align: center;
            background: linear-gradient(135deg, #2d3748, #1a202c);
            color: white;
            padding: 80px 5%;
        }
        .cta-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 6vw, 4rem);
            margin-bottom: 1.5rem;
        }
        .cta-buttons {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Footer */
        .footer {
            background: #1a202c;
            color: rgba(255,255,255,0.8);
            padding: 60px 5% 20px;
            text-align: center;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .footer-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .footer-links a:hover {
            color: #48bb78;
        }
        .social-icons {
            margin: 2rem 0;
        }
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.1);
            color: white;
            border-radius: 50%;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }
        .social-icons a:hover {
            background: #48bb78;
            transform: translateY(-3px);
        }


        /* Scroll behavior */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo"><img src="asset/logo cut.png" alt="Logo" width="70px" > Book<span>Store</span></a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="#story">Our Story</a></li>
                <li><a href="#mission">Mission</a></li>
                <li><a href="#team">Team</a></li>
                <li><a href="index.php">Shop Now</a></li>
            </ul>
            <a href="index.php" class="cta-btn">Browse Books</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Where Stories Come Alive</h1>
            <p>Discover your next favorite book with the world's largest collection of stories, knowledge, and inspiration. Curated for every reader.</p>
            <a href="index.php" class="cta-btn" style="font-size: 1.3rem; padding: 1.2rem 3rem;">Explore Books</a>
        </div>
    </section>

    <!-- Our Story -->
    <section id="story">
        <h2 class="section-title">Our Story</h2>
        <div class="story-grid">
            <div class="story-text">
                <h3>Founded with Passion for Reading</h3>
                <p>BookStore was born from a simple idea: make every book lover's dream come true. Starting in a small room with 100 books, we've grown into India's favorite online bookstore serving millions of readers across the country.</p>
                <p>Our mission is to connect readers with stories that transform lives, spark imagination, and expand horizons. Every book on our shelf has been carefully selected to inspire, educate, and entertain.</p>
                <ul style="margin-top: 2rem; padding-left: 2rem; color: #4a5568;">
                    <li><i class="fas fa-check-circle" style="color: #48bb78; margin-right: 0.5rem;"></i>100+  Happy Readers</li>
                    <li><i class="fas fa-check-circle" style="color: #48bb78; margin-right: 0.5rem;"></i>50+ Books Available</li>
                    <li><i class="fas fa-check-circle" style="color: #48bb78; margin-right: 0.5rem;"></i>Free Shipping India-wide</li>
                </ul>
            </div>
            <div class="story-image">
                <!-- <i class="fas fa-book-open-reader"></i> -->
                 <img src="asset/readbook.jpg" alt="Readbook">
            </div>
        </div>
    </section>

    <!-- Mission -->
    <section id="mission" class="mission">
        <h2 class="section-title">Our Mission</h2>
        <div class="mission-grid">
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Curate Quality</h3>
                <p>Handpicked collection of bestsellers, classics, and hidden gems from around the world, ensuring every reader finds their perfect book.</p>
            </div>
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Lightning Delivery</h3>
                <p>Fastest delivery across India with free shipping on orders above ₹299. Your books delivered to your doorstep in 2-5 days.</p>
            </div>
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Trusted Quality</h3>
                <p>100% genuine books with secure payments, easy returns, and 24/7 customer support. Shop with complete peace of mind.</p>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section id="team">
        <h2 class="section-title">Meet Our Team</h2>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-avatar">K</div>
                <h3>Karan Pardeshi</h3>
                <div class="team-role">Leader & Web Developer</div>
                <!-- <p>Passionate reader turned entrepreneur, building the future of reading in India.</p> -->
            </div>
            <div class="team-card">
                <div class="team-avatar">A</div>
                <h3>Abhishek More</h3>
                <div class="team-role">Documentation</div>
                <!-- <p>Book enthusiast with 15+ years experience selecting the best reads for you.</p> -->
            </div>
            <div class="team-card">
                <div class="team-avatar">R</div>
                <h3>Rohit More</h3>
                <div class="team-role">Data Collection</div>
                <!-- <p>Creating seamless shopping experience with cutting-edge technology.</p> -->
            </div>
            <div class="team-card">
                <div class="team-avatar">V</div>
                <h3>Vaishnavi More</h3>
                <div class="team-role">Data Collection</div>
                <!-- <p>Creating seamless shopping experience with cutting-edge technology.</p> -->
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <h2>Ready to Start Your Reading Journey?</h2>
        <p>Join millions of readers discovering their next favorite book</p>
        <div class="cta-buttons">
            <a href="index.php" class="btn" style="background: linear-gradient(135deg, #49504c, #4b6256);; color: white; padding: 1.5rem 3rem; font-size: 1.3rem; border-radius: 50px;">🛒 Shop Now</a>
            <a href="login.php" class="btn" style="background: transparent; color: white; border: 2px solid rgba(255,255,255,0.5); padding: 1.3rem 3rem; font-size: 1.2rem; border-radius: 50px;">👤 Sign Up Free</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="logo" style="font-size: 2rem; margin-bottom: 1rem;"><img src="asset/logo cut.png" alt="Logo" width="90px" >Book<span>Store</span></div>
            <p style="max-width: 600px; margin: 0 auto 2rem; opacity: 0.8;">Your trusted destination for books, stories, and knowledge. Connecting readers across India since 2025.</p>
            
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="#story">Story</a>
                <a href="feedback.php">Feedback</a>
                <a href="contact.php">Contact</a>
            </div>
            
            <!-- <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div> -->
            
            <p style="opacity: 0.6; font-size: 0.9rem;">© 2026 BookStore. All rights reserved. | Made with ❤️ for book lovers</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(255,255,255,0.98)';
                navbar.style.boxShadow = '0 5px 25px rgba(0,0,0,0.15)';
            } else {
                navbar.style.background = 'rgba(255,255,255,0.95)';
                navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
            }
        });
    </script>
</body>
</html>
