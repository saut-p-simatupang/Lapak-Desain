<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

$query = "SELECT name FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("User tidak ditemukan.");
}

$user = mysqli_fetch_assoc($result);
$name = $user['name'];

// Hitung notifikasi belum dibaca
$notif_count = 0;
$notif_sql = "SELECT COUNT(*) AS unread FROM notifications WHERE user_id = $user_id AND is_read = 0";
$notif_result = $conn->query($notif_sql);
if ($notif_result && $row = $notif_result->fetch_assoc()) {
    $notif_count = $row['unread'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LapakDesain</title>
  <link rel="stylesheet" href="main.css?v=1.0">
  <link rel="stylesheet" href="css/main.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<!-- Navbar -->
<nav class="navbar bg-light shadow-sm">
  <div class="container-xl d-flex justify-content-between align-items-center">
    
    <!-- Kiri -->
    <div class="d-flex align-items-center gap-3">
      <a class="navbar-brand d-flex align-items-center" href="main.php">
        <img src="Images/logo-transparent.png" alt="logo" width="50" height="38" />
        <span class="ms-2 fw-bold">LapakDesain</span>
      </a>
      <form action="shop.php" method="GET" class="search-container d-none d-lg-flex">
        <input type="text" name="query" class="form-control" placeholder="What service are you looking for today?" required />
        <button class="btn btn-dark ms-2" type="submit">Cari</button>
      </form>
    </div>

    <!-- Kanan -->
    <div class="d-flex align-items-center gap-3">
      <div class="icon-group d-flex align-items-center gap-3">

        <!-- SHOP -->
        <a href="shop.php" class="text-dark text-decoration-none d-flex align-items-center gap-1">
          <i data-feather="shopping-bag"></i>
          <span class="d-none d-md-inline">Shop</span>
        </a>

        <!-- Notifikasi Bell -->
        <div class="dropdown position-relative">
          <a href="lihat_notifikasi.php" class="position-relative">
            <i data-feather="mail"></i>
            <span class="d-none d-md-inline">Notification</span>
            <?php if ($notif_count > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?= $notif_count ?>
              </span>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow small">
            <?php
            $notif_items = mysqli_query($conn, "SELECT id, message FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5");
            if (mysqli_num_rows($notif_items) === 0):
            ?>
              <li><span class="dropdown-item-text text-muted">Tidak ada notifikasi</span></li>
            <?php else:
              while ($n = mysqli_fetch_assoc($notif_items)): ?>
                <li><a class="dropdown-item" href="notif_read.php?id=<?= $n['id'] ?>"><?= htmlspecialchars($n['message']) ?></a></li>
            <?php endwhile; endif; ?>
          </ul>
        </div>

        <!-- Link Pesanan -->
        <a class="pesanan-link hide-on-mobile" href="pesanan.php">Pesanan</a>
      </div>

      <!-- Dropdown Profil -->
      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
           id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
               style="width: 36px; height: 36px; font-weight: bold;">
            <?= strtoupper(substr($name, 0, 1)) ?>
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
          <li><a class="dropdown-item" href="profil.php">Profil</a></li>
          <li><a class="dropdown-item" href="logout.php">Logout</a></li>
        </ul>
      </div>

    </div>
  </div>
</nav>


</body>
</html>
