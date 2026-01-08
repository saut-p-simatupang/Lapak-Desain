<?php
session_start();
include 'db.php';

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data project dan user pembuat
$stmt = $conn->prepare("SELECT projects.*, users.name, users.skills, users.language FROM projects JOIN users ON projects.user_id = users.id WHERE projects.id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    die("Project tidak ditemukan.");
}

$current_user_id = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($project['title']) ?> | LapakDesain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .project-image {
            width: 100%;
            border-radius: 12px;
            max-height: 500px;
            object-fit: cover;
        }
        .project-box {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .price-tag {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }
        .username {
            font-weight: 500;
            font-size: 1.1rem;
            color: #555;
        }
        body {
            background-color: #f8f9fa;
        }
        .faq-item {
            margin-bottom: 1rem;
        }
        .faq-item h6 {
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include 'navbar.php' ?>
<div class="container py-5">
    <div class="row g-4">
        <!-- Gambar Project -->
        <div class="col-md-7">
            <img src="uploads/<?= htmlspecialchars($project['image']) ?>" class="project-image" alt="Preview Project">
        </div>

        <!-- Detail Project -->
        <div class="col-md-5">
            <div class="project-box">
                <h3><?= htmlspecialchars($project['title']) ?></h3>
                <p class="username mb-2">Oleh <strong><?= htmlspecialchars($project['name']) ?></strong></p>
                <div class="price-tag mb-3">Rp <?= number_format($project['price'], 0, ',', '.') ?></div>

                <?php
                $avg_stmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM reviews WHERE project_id = ?");
                $avg_stmt->bind_param("i", $project_id);
                $avg_stmt->execute();
                $avg_result = $avg_stmt->get_result()->fetch_assoc();
                ?>

                <?php if ($avg_result['total'] > 0): ?>
                    <div class="mb-2">
                        <span class="text-warning"><?= round($avg_result['avg_rating'], 1) ?> ⭐</span> dari <?= $avg_result['total'] ?> ulasan
                    </div>
                <?php endif; ?>

                <?php if ($current_user_id && $current_user_id != $project['user_id']): ?>
                    <form method="POST" action="beli_project.php">
                        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                        <button type="submit" class="btn btn-success w-100">💸 Beli Sekarang</button>
                    </form>
                <?php elseif (!$current_user_id): ?>
                    <a href="Log_form.php" class="btn btn-warning w-100">Login untuk membeli</a>
                <?php endif; ?>

                <h6 class="mt-4">Deskripsi Project</h6>
                <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>

                <hr>
                <h6>👨‍💻 Keahlian</h6>
                <p><?= htmlspecialchars($project['skills']) ?: "Tidak tersedia" ?></p>

                <h6>🌐 Bahasa</h6>
                <p><?= htmlspecialchars($project['language']) ?: "Tidak tersedia" ?></p>

                <a href="shop.php" class="btn btn-outline-secondary w-100 mt-4">Kembali ke Shop</a>
            </div>
        </div>
    </div>

    <!-- Ulasan -->
    <div class="mt-5">
        <h4>⭐ Ulasan Pembeli</h4>
        <?php
        $review_stmt = $conn->prepare("SELECT reviews.*, users.name FROM reviews JOIN users ON reviews.user_id = users.id WHERE reviews.project_id = ? ORDER BY reviews.created_at DESC");
        $review_stmt->bind_param("i", $project_id);
        $review_stmt->execute();
        $review_result = $review_stmt->get_result();
        ?>

        <?php if ($review_result->num_rows === 0): ?>
            <p class="text-muted">Belum ada ulasan.</p>
        <?php else: ?>
            <?php while ($rev = $review_result->fetch_assoc()): ?>
                <div class="border rounded p-3 mb-3 bg-light">
                    <strong><?= htmlspecialchars($rev['name']) ?></strong>
                    <div class="text-warning mb-1">
                        <?= str_repeat("⭐", $rev['rating']) . str_repeat("☆", 5 - $rev['rating']) ?>
                    </div>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($rev['review'])) ?></p>
                    <small class="text-muted"><?= date('d M Y, H:i', strtotime($rev['created_at'])) ?></small>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/feather-icons"></script>
  <script>feather.replace();</script>
</body>
</html>