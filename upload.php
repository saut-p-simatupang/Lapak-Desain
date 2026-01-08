<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Log_form.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Ambil project jika sudah ada
$stmt = $conn->prepare("SELECT * FROM projects WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$has_uploaded = $result->num_rows > 0;
$project = $has_uploaded ? $result->fetch_assoc() : null;

// Notifikasi
$message = '';
$alert_type = 'success';

// Hapus project jika diminta
if (isset($_POST['delete'])) {
    $deleteStmt = $conn->prepare("DELETE FROM projects WHERE user_id = ?");
    $deleteStmt->bind_param("i", $user_id);
    $deleteStmt->execute();
    $has_uploaded = false;
    $project = null;
    $message = "Project berhasil dihapus!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Project | LapakDesain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .card-upload {
            max-width: 700px;
            margin: auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        .form-label {
            font-weight: 500;
        }
        img.preview {
            border-radius: 10px;
            margin-top: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<?php include 'navbar.php' ?>
<div class="container py-5">
    <div class="card-upload">
        <h3 class="mb-4 fw-bold text-center"><?= $has_uploaded ? 'Edit Project' : 'Upload Project Baru' ?></h3>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $alert_type ?> text-center"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($project['title'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="price" class="form-control" step="0.01" required value="<?= htmlspecialchars($project['price'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Gambar Preview</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <?php if ($has_uploaded && $project['image']): ?>
                    <div class="mt-2">
                        <img src="uploads/<?= htmlspecialchars($project['image']) ?>" width="200" class="preview">
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" name="<?= $has_uploaded ? 'update' : 'submit' ?>" class="btn btn-<?= $has_uploaded ? 'primary' : 'success' ?> w-100">
                    <?= $has_uploaded ? '🔄 Update Project' : '⬆️ Upload Project' ?>
                </button>
                <?php if ($has_uploaded): ?>
                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus project ini?')">🗑 Hapus</button>
                <?php endif; ?>
                <a href="profil.php" class="btn btn-secondary">← Kembali</a>
            </div>
        </form>

        <?php
        // Upload baru
        if (isset($_POST['submit']) && !$has_uploaded) {
            $title = $_POST['title'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $imageName = basename($_FILES['image']['name']);
            $target = 'uploads/' . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $insert = $conn->prepare("INSERT INTO projects (user_id, title, price, description, image) VALUES (?, ?, ?, ?, ?)");
                $insert->bind_param("isdss", $user_id, $title, $price, $description, $imageName);
                $insert->execute();
                echo '<div class="alert alert-success mt-3">✅ Upload berhasil!</div>';
            } else {
                echo '<div class="alert alert-danger mt-3">❌ Gagal mengupload gambar!</div>';
            }
        }

        // Update
        if (isset($_POST['update']) && $has_uploaded) {
            $title = $_POST['title'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $imageName = $project['image'];

            if (!empty($_FILES['image']['name'])) {
                $newImage = basename($_FILES['image']['name']);
                $target = 'uploads/' . $newImage;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $imageName = $newImage;
                }
            }

            $update = $conn->prepare("UPDATE projects SET title = ?, price = ?, description = ?, image = ? WHERE user_id = ?");
            $update->bind_param("sdssi", $title, $price, $description, $imageName, $user_id);
            $update->execute();
            echo '<div class="alert alert-success mt-3">✅ Project berhasil diperbarui!</div>';
        }
        ?>
    </div>
</div>

  <script src="js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/feather-icons"></script>
  <script>feather.replace();</script>
</html>
