<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Log_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pesanan sebagai pembeli
$stmt_buyer = $conn->prepare("SELECT orders.*, users.name AS designer_name, projects.title, projects.price FROM orders
    JOIN users ON orders.designer_id = users.id
    JOIN projects ON orders.project_id = projects.id
    WHERE orders.buyer_id = ? ORDER BY orders.created_at DESC");
$stmt_buyer->bind_param("i", $user_id);
$stmt_buyer->execute();
$buyer_orders = $stmt_buyer->get_result();

// Pesanan sebagai desainer
$stmt_designer = $conn->prepare("SELECT orders.*, users.name AS buyer_name, projects.title, projects.price FROM orders
    JOIN users ON orders.buyer_id = users.id
    JOIN projects ON orders.project_id = projects.id
    WHERE orders.designer_id = ? ORDER BY orders.created_at DESC");
$stmt_designer->bind_param("i", $user_id);
$stmt_designer->execute();
$designer_orders = $stmt_designer->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan | LapakDesain</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #eef1f5; }
        .order-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            transition: all 0.3s;
        }
        .order-card:hover { transform: translateY(-2px); }
        .status-badge {
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 50rem;
            font-weight: 500;
        }
        .btn-action { width: 100%; }
        .review-box {
            border-top: 1px solid #ddd;
            margin-top: 15px;
            padding-top: 15px;
        }
        textarea { resize: none; }
        .text-center{
            margin-top: 40px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">📦 Kelola Pesanan Anda</h2>
        <p class="text-muted">Pantau semua aktivitas pemesanan Anda sebagai pembeli maupun desainer.</p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- PEMBELI -->
    <h4 class="mb-3">🛒 Pesanan Saya</h4>
    <?php if ($buyer_orders->num_rows === 0): ?>
        <div class="alert alert-info">Belum ada pesanan yang Anda buat.</div>
    <?php endif; ?>

    <?php while ($row = $buyer_orders->fetch_assoc()): ?>
        <div class="order-card">
            <h5 class="fw-semibold"><?= htmlspecialchars($row['title']) ?></h5>
            <p class="mb-1">Desainer: <strong><?= htmlspecialchars($row['designer_name']) ?></strong></p>
            <p>Status:
                <span class="badge bg-<?= $row['status'] === 'Selesai' ? 'success' : ($row['status'] === 'Proses Desain' ? 'primary' : 'warning') ?> status-badge">
                    <?= htmlspecialchars($row['status']) ?>
                </span>
            </p>

            <div class="row g-2 mt-3">
                <?php if ($row['status_pembayaran'] === 'Belum Dibayar'): ?>
                    <div class="col-auto">
                        <a href="bayar.php?order_id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                            💳 Bayar Sekarang
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($row['final_file']): ?>
                    <div class="col-auto">
                        <a href="hasil_upload/<?= htmlspecialchars($row['final_file']) ?>" class="btn btn-success btn-sm" download>
                            📥 Download Hasil
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php
            $review_check = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND project_id = ?");
            $review_check->bind_param("ii", $user_id, $row['project_id']);
            $review_check->execute();
            $review_exists = $review_check->get_result()->num_rows > 0;
            ?>

            <?php if ($row['status'] === 'Selesai' && !$review_exists): ?>
                <div class="review-box">
                    <form method="POST" action="proses_ulasan.php">
                        <input type="hidden" name="project_id" value="<?= $row['project_id'] ?>">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">

                        <div class="mb-2">
                            <label for="rating_<?= $row['id'] ?>" class="form-label">⭐ Beri Rating</label>
                            <select name="rating" id="rating_<?= $row['id'] ?>" class="form-select" required>
                                <option value="">Pilih rating</option>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?> bintang</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="review_<?= $row['id'] ?>" class="form-label">📝 Ulasan</label>
                            <textarea name="review" id="review_<?= $row['id'] ?>" rows="3" class="form-control" placeholder="Tulis ulasan..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Kirim Ulasan</button>
                    </form>
                </div>
            <?php elseif ($row['status'] === 'Selesai'): ?>
                <div class="text-success mt-2">✅ Ulasan telah dikirim.</div>
            <?php endif; ?>

            <?php if ($row['status'] === 'Menunggu Konfirmasi'): ?>
                <form method="POST" action="hapus_pesanan.php" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')" class="mt-2">
                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm">🗑️ Hapus Pesanan</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>

<!-- DESAINER -->
<h4 class="mt-5 mb-3">🎨 Pesanan Masuk</h4>
<?php if ($designer_orders->num_rows === 0): ?>
    <div class="alert alert-info">Belum ada pesanan masuk.</div>
<?php endif; ?>

<?php while ($row = $designer_orders->fetch_assoc()): ?>
    <div class="order-card">
        <h5 class="fw-semibold"><?= htmlspecialchars($row['title']) ?></h5>
        <p class="mb-1">Pembeli: <strong><?= htmlspecialchars($row['buyer_name']) ?></strong></p>
        <p>Status:
            <span class="badge bg-<?= $row['status'] === 'Menunggu Konfirmasi' ? 'warning' : ($row['status'] === 'Proses Desain' ? 'primary' : 'success') ?> status-badge">
                <?= htmlspecialchars($row['status']) ?>
            </span>
        </p>

        <p>Pembayaran: <strong><?= $row['status_pembayaran'] === 'Lunas' ? '✅ Lunas' : '❌ Belum Dibayar' ?></strong></p>


        <?php if ($row['status_pembayaran'] === 'Lunas' && !empty($row['payment_proof'])): ?>
            <p>Bukti Transfer:
                <a href="uploads_bukti/<?= htmlspecialchars($row['payment_proof']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                    🔍 Lihat Bukti
                </a>
            </p>
        <?php endif; ?>

        <?php if ($row['status'] === 'Menunggu Konfirmasi' && $row['status_pembayaran'] === 'Lunas'): ?>
            <form method="POST" action="konfirmasi_pesanan.php" class="mt-2">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <button type="submit" class="btn btn-outline-primary btn-action">✅ Konfirmasi & Mulai Desain</button>
            </form>
        <?php elseif ($row['status_pembayaran'] === 'Belum Dibayar'): ?>
            <div class="text-danger">⏳ Menunggu pembayaran dari pembeli</div>
        <?php endif; ?>

        <?php if ($row['status'] === 'Proses Desain'): ?>
            <form method="POST" action="upload_hasil.php" enctype="multipart/form-data" class="mt-3">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <div class="mb-2">
                    <label for="final_file_<?= $row['id'] ?>" class="form-label">📤 Upload Hasil Desain</label>
                    <input type="file" name="final_file" id="final_file_<?= $row['id'] ?>" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success btn-action">🚀 Kirim ke Pembeli</button>
            </form>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

</div>

<!-- Scripts -->
<script src="js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>feather.replace();</script>
</body>
</html>
