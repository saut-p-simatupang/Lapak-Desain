<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Tandai semua notifikasi sebagai sudah dibaca
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");

// Ambil semua notifikasi
$result = $conn->query("SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Notifikasi Anda</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .notif-card {
      border-left: 5px solid #0d6efd;
      background: #fff;
      padding: 15px 20px;
      margin-bottom: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .notif-time {
      font-size: 0.85rem;
      color: #888;
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold">🔔 Notifikasi Anda</h3>
      <a href="main.php" class="btn btn-outline-secondary">← Kembali</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($notif = $result->fetch_assoc()): ?>
        <div class="notif-card">
          <div><?= htmlspecialchars($notif['message']) ?></div>
          <div class="notif-time"><?= date('d M Y, H:i', strtotime($notif['created_at'])) ?></div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="alert alert-info text-center">🎉 Belum ada notifikasi baru.</div>
    <?php endif; ?>
  </div>
</body>
</html>
