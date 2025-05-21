<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover the World</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            color: #333;
            padding-top: 56px;
        }
        .navbar {
            background-color: #007bff;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }
        .nav-link:hover {
            color: #d4edda !important;
        }
        .hero {
            background-image: url('https://ieltscaptoc.com.vn/wp-content/uploads/2023/02/talk-about-travelling-4.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.7);
            padding: 180px 20px;
            text-align: center;
        }
        .hero h1 {
            font-size: 3rem;
        }
        .btn-explore {
            background-color: #ffc107;
            color: #000;
            padding: 10px 20px;
            font-size: 1.2rem;
        }
        .destination-card img {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Discover</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#destinations">Destinations</a></li>
                    <li class="nav-item"><a class="nav-link" href="#activities">Activities</a></li>
                    <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="signup.php">Signup</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero">
        <h1>Discover Breathtaking Destinations</h1>
        <p>Plan your perfect trip and explore the world like never before.</p>
        <a href="#destinations" class="btn btn-explore">Explore Now</a>
    </div>

    <div class="container mt-5" id="destinations">
        <h2 class="text-center mb-4">Top Destinations</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card destination-card">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT0CLOF1FH-ghR3Qwvn5Ni1VFWTXypXmWciwA&s" class="card-img-top" alt="Paris">
                    <div class="card-body">
                        <h5 class="card-title">Paris, France</h5>
                        <p class="card-text">Experience the city of love with its romantic ambiance and rich history.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card destination-card">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTAwx_rcolfNik6mtO3oUwrmvLT0WdOkIB4tg&s" class="card-img-top" alt="Santorini">
                    <div class="card-body">
                        <h5 class="card-title">Santorini, Greece</h5>
                        <p class="card-text">Enjoy stunning sunsets and the mesmerizing blue and white architecture.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card destination-card">
                    <img src="https://www.outlooktravelmag.com/media/bali-tg.png" class="card-img-top" alt="Bali">
                    <div class="card-body">
                        <h5 class="card-title">Bali, Indonesia</h5>
                        <p class="card-text">Relax in paradise with its lush landscapes and vibrant culture.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <div class="container mt-5" id="activities">
        <h2 class="text-center mb-4">Popular Activities</h2>
        <div class="row text-center">
            <div class="col-md-4">
                <div class="card activity-card">
                    <img src="https://cdn.britannica.com/94/125794-050-FB09B3F4/Hikers-Gore-Range-Mountains-Denver.jpg" class="card-img-top" alt="Hiking">
                    <div class="card-body">
                        <h4>Hiking</h4>
                        <p>Explore scenic trails and breathtaking landscapes.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card activity-card">
                    <img src="https://www.oceanicsociety.org/wp-content/uploads/2021/04/fiji-snorkeling-coral-reef.jpeg" class="card-img-top" alt="Snorkeling">
                    <div class="card-body">
                        <h4>Snorkeling</h4>
                        <p>Discover vibrant marine life in crystal-clear waters.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card activity-card">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSi3VVX2tpNfjBsxYoHstqFp9g8guCLhzFtlw&s" class="card-img-top" alt="Cultural Tours">
                    <div class="card-body">
                        <h4>Cultural Tours</h4>
                        <p>Experience rich traditions and historical landmarks.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5" id="testimonials">
        <h2 class="text-center mb-4">What Our Travelers Say</h2>
        <div class="row">
            <div class="col-md-6">
                <blockquote class="blockquote">
                    <p>"This was the best trip of my life! Everything was perfectly arranged, and I had an unforgettable experience."</p>
                    <footer class="blockquote-footer">Sarah, USA</footer>
                </blockquote>
            </div>
            <div class="col-md-6">
                <blockquote class="blockquote">
                    <p>"I explored so many amazing places! Highly recommend for anyone who loves traveling."</p>
                    <footer class="blockquote-footer">David, UK</footer>
                </blockquote>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 mt-5 bg-dark text-light">
        <p>&copy; 2025 Discover. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>