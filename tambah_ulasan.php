<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    echo "Project tidak ditemukan.";
    exit;
}

$project_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'] ?? null;

// Ambil detail project
$stmt = $conn->prepare("SELECT p.*, u.username FROM projects p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    echo "Project tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($project['title']) ?> - Detail Project</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">

    <div class="card mb-4 shadow">
        <div class="card-body">
            <h2 class="card-title"><?= htmlspecialchars($project['title']) ?></h2>
            <p class="text-muted">Dibuat oleh <strong><?= htmlspecialchars($project['username']) ?></strong></p>
            <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
            <p class="fs-5 fw-bold">Harga: Rp <?= number_format($project['price'], 0, ',', '.') ?></p>

            <?php if ($current_user_id && $current_user_id != $project['user_id']): ?>
                <form method="POST" action="bayar.php">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                    <input type="hidden" name="designer_id" value="<?= $project['user_id'] ?>">
                    <input type="hidden" name="amount" value="<?= $project['price'] ?>">
                    <button type="submit" class="btn btn-success w-100">💸 Beli Sekarang</button>
                </form>
            <?php elseif (!$current_user_id): ?>
                <div class="alert alert-warning mt-3">Silakan login untuk membeli project ini.</div>
            <?php else: ?>
                <div class="alert alert-info mt-3">Ini adalah project milikmu sendiri.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review Section -->
    <div class="card mb-4 shadow">
        <div class="card-header">
            <h5>📝 Ulasan dan Rating</h5>
        </div>
        <div class="card-body">
            <?php
            $stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.project_id = ?");
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $reviews = $stmt->get_result();

            if ($reviews->num_rows > 0):
                while ($review = $reviews->fetch_assoc()):
            ?>
                <div class="mb-3">
                    <strong><?= htmlspecialchars($review['username']) ?></strong> – 
                    <span>Rating: <?= str_repeat('⭐', (int)$review['rating']) ?></span><br>
                    <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                </div>
            <?php endwhile; else: ?>
                <p>Belum ada ulasan untuk project ini.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Form Tambah Review -->
    <?php if ($current_user_id): ?>
        <?php
        $stmt = $conn->prepare("SELECT * FROM orders WHERE project_id = ? AND buyer_id = ? AND status = 'Selesai'");
        $stmt->bind_param("ii", $project_id, $current_user_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        $stmt = $conn->prepare("SELECT * FROM reviews WHERE project_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $project_id, $current_user_id);
        $stmt->execute();
        $existing_review = $stmt->get_result()->fetch_assoc();
        ?>

        <?php if ($order && !$existing_review): ?>
        <div class="card shadow">
            <div class="card-header">
                <h5>Beri Ulasan</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="tambah_ulasan.php">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                    <div class="mb-3">
                        <label>Rating:</label>
                        <select name="rating" class="form-select" required>
                            <option value="">Pilih Rating</option>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>"><?= $i ?> ⭐</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Komentar:</label>
                        <textarea name="comment" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                </form>
            </div>
        </div>
        <?php elseif ($existing_review): ?>
            <div class="alert alert-info">Kamu sudah memberikan ulasan untuk project ini.</div>
        <?php endif; ?>
    <?php endif; ?>

</div>
</body>
</html>
