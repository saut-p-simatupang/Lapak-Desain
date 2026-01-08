<?php
session_start();
include "db.php";

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: log_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Inisialisasi variabel default
$name = "User";
$email = "Belum diisi";
$skills = "Belum diisi";
$language = "Belum diisi";

// Ambil data user dari database
$query = "SELECT name, email, skills, language FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $name = $user['name'] ?? $name;
    $email = $user['email'] ?? $email;
    $skills = $user['skills'] ?? $skills;
    $language = $user['language'] ?? $language;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil</title>
  <link rel="stylesheet" href="css/profil.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="css/main.css" rel="stylesheet" />
</head>
<body>

<?php include 'navbar.php' ?>

<div class="container my-5">
  <div class="row g-4">
    
    <!-- Kiri: Profil -->
    <div class="col-md-4">
      <div class="card profile-card p-4 shadow-sm">
        <div class="text-center mb-4">
          <div class="avatar-circle mx-auto mb-2" style="width: 70px; height: 70px; border-radius: 50%; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center; font-size: 32px;">
            <?php echo strtoupper(substr($name, 0, 1)); ?>
          </div>
          <h4 class="mb-0"><?php echo htmlspecialchars($name); ?></h4>
          <p class="text-muted">@<?php echo htmlspecialchars($email); ?></p>
        </div>

        <table class="table">
          <tr>
              <th>Keahlian (Skills)</th>
              <td><?php echo htmlspecialchars($skills); ?></td>
          </tr>
          <tr>
              <th>Bahasa (Language)</th>
              <td><?php echo htmlspecialchars($language); ?></td>
          </tr>
        </table>

        <div class="text-center">
          <a href="edit_profil.php" class="btn btn-outline-dark">Edit Profil</a>
        </div>
      </div>
    </div>

    <!-- Kanan: Header dan Checklist -->
    <div class="col-md-8">
      <div class="header-section mb-4">
        <h3 class="mt-2">Hi 👋, Ayo Mulai Desainmu!</h3>
        <p class="text-muted">Lengkapi profilmu dan mulai berkarya.</p>
      </div>

      <div class="card p-4 shadow-sm">
        <h5 class="fw-bold mb-3">Checklist Profil</h5>
      
        <div class="checklist-item p-3 border rounded mb-3">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="mb-1">Mulai Usaha Desainmu</h6>
              <p class="text-muted mb-0">Lengkapi keahlianmu agar mudah ditemukan.</p>
            </div>
            <a href="upload.php" class="text-primary fw-bold">Tambah</a>
          </div>
        </div>

        <div class="checklist-item p-3 border rounded mb-3">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="mb-1">Lengkapi Informasi</h6>
              <p class="text-muted mb-0">Tambahkan info tambahan untuk tampilan profesional.</p>
            </div>
            <a href="edit_profil.php" class="text-primary fw-bold">Tambah</a>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
  feather.replace();
</script>
</body>
</html>
