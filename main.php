<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: Log_form.php");
    exit();
}

// Ambil data user dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT name FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("User tidak ditemukan.");
}

$user = mysqli_fetch_assoc($result);
$name = $user['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lapak Desain</title>
  <link rel="stylesheet" href="main.css?v=1.0">
  <link rel="stylesheet" href="css/main.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <!-- navbar -->
   <?php include 'navbar.php'; ?>
 <!-- navbar -->

  <!-- Section 1 -->
  <section class="category-banner">
    <div class="overlay"></div>
    <div class="banner-content">
      <h2>High-Quality Design, Hassle-Free</h2>
      <p>Find trusted designers and get the perfect visuals for your needs—fast and easy.</p>
    </div>
  </section>

  <!-- Section 2 -->
  <section class="popular-categories container-xl">
    <h3 class="section-title">Most popular in Graphics & Design</h3>
    <div class="category-scroll">
      <a href="shop.php" class="category-card text-decoration-none"><div class="icon">🌱</div><span>Logo Design</span></a>
      <a href="shop.php" class="category-card text-decoration-none"><div class="icon">🎨</div><span>Illustration</span></a>
      <a href="shop.php" class="category-card text-decoration-none"><div class="icon">🏠</div><span>Business Card</span></a>
      <a href="shop.php" class="category-card text-decoration-none"><div class="icon">🧠</div><span>Social Media Design</span></a>
    </div>
  </section>

  <!-- Section 3 -->
  <section class="design-gallery container-xl">
    <h2 class="section-title">Guides To Help U</h2>
    <div class="design-grid">
      <div class="design-card">
        <div class="card-image"><img src="Images/desain1.jpeg" alt="Desain 1" /></div>
        <div class="card-text">
          <h3 class="card-title">Ilustrasi</h3>
          <p class="card-description">Menambah daya tarik visual pada konten pemasaran dan mempermudah penyampaian pesan bisnis secara kreatif dan informatif.</p>
        </div>
      </div>

      <div class="design-card">
        <div class="card-image"><img src="Images/desain2.jpeg" alt="Desain 2" /></div>
        <div class="card-text">
          <h3 class="card-title">Shutterstock Pricing Plans</h3>
          <p class="card-description">Tingkatkan proyek kreatif Anda dengan menggabungkan gambar, audio, dan video stok melalui paket harga Shutterstock yang fleksibel.</p>
        </div>
      </div>

      <div class="design-card">
        <div class="card-image"><img src="Images/desain3.jpeg" alt="Desain 3" /></div>
        <div class="card-text">
          <h3 class="card-title">Logo Design</h3>
          <p class="card-description">Membantu bisnis membangun identitas merek yang kuat dan mudah dikenali.</p>
        </div>
      </div>

      <div class="design-card">
        <div class="card-image"><img src="Images/desain4.jpeg" alt="Desain 4" /></div>
        <div class="card-text">
          <h3 class="card-title">What Is a Color Scheme?</h3>
          <p class="card-description">Pelajari jenis dan contoh skema warna untuk mendukung proyek desain visual Anda.</p>
        </div>
      </div>
    </div>
  </section>


  <!-- Footer -->
<!-- Footer -->
<footer class="bg-light mt-5 pt-5 border-top">
  <div class="container pb-5">
    <div class="row row-cols-2 row-cols-md-5 g-4">

      <div class="col">
        <h6 class="fw-bold mb-3">Categories</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="text-decoration-none text-muted">Graphics & Design</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Digital Marketing</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Writing & Translation</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Video & Animation</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Music & Audio</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Programming & Tech</a></li>
          <li><a href="#" class="text-decoration-none text-muted">AI Services</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Consulting</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Finance</a></li>
        </ul>
      </div>

      <div class="col">
        <h6 class="fw-bold mb-3">For Clients</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="text-decoration-none text-muted">How It Works</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Customer Stories</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Trust & Safety</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Online Courses</a></li>
          <li><a href="#" class="text-decoration-none text-muted">User Guides</a></li>
          <li><a href="#" class="text-decoration-none text-muted">FAQ</a></li>
        </ul>
      </div>

      <div class="col">
        <h6 class="fw-bold mb-3">For Freelancers</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="text-decoration-none text-muted">Become a Freelancer</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Start an Agency</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Community Hub</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Forum</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Events</a></li>
        </ul>
      </div>

      <div class="col">
        <h6 class="fw-bold mb-3">Business Solutions</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="text-decoration-none text-muted">Fiverr Pro</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Project Management</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Expert Sourcing</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Content Marketing</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Creative Talent</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Contact Sales</a></li>
        </ul>
      </div>

      <div class="col">
        <h6 class="fw-bold mb-3">Company</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="text-decoration-none text-muted">About Us</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Help & Support</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Careers</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Terms of Service</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Privacy Policy</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Affiliates</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Press & News</a></li>
        </ul>
      </div>

    </div>

    <div class="text-center mt-5 small text-muted">
      &copy; <?= date('Y') ?> LapakDesain. All rights reserved.
    </div>
  </div>
</footer>


  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/feather-icons"></script>
  <script>feather.replace();</script>
</body>
</html>
