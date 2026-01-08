<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: Log_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $language = mysqli_real_escape_string($conn, $_POST['language']);
    $dana_account = mysqli_real_escape_string($conn, $_POST['dana_account']);
    $paypal_account = mysqli_real_escape_string($conn, $_POST['paypal_account']);

    $update_query = "UPDATE users SET 
        skills='$skills', 
        language='$language', 
        dana_account='$dana_account', 
        paypal_account='$paypal_account' 
        WHERE id=$user_id";
        
    if (mysqli_query($conn, $update_query)) {
        header("Location: profil.php");
        exit();
    } else {
        die("Update gagal: " . mysqli_error($conn));
    }
}

$query = "SELECT skills, language, dana_account, paypal_account FROM users WHERE id=$user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Profil</title>
  <link rel="stylesheet" href="css/edit_profil.css">
</head>
<body>
  <div class="edit-profile-container">
    <h2>Edit Profil</h2>
    <form method="POST" action="edit_profil.php">
      <label for="skills">Keahlian (Skills)</label>
      <input type="text" id="skills" name="skills" value="<?= htmlspecialchars($user['skills'] ?? '') ?>" required>

      <label for="language">Bahasa (Language)</label>
      <input type="text" id="language" name="language" value="<?= htmlspecialchars($user['language'] ?? '') ?>" required>

      <label for="dana_account">Nomor Dana</label>
      <input type="text" id="dana_account" name="dana_account" value="<?= htmlspecialchars($user['dana_account'] ?? '') ?>">

      <label for="paypal_account">Email PayPal</label>
      <input type="email" id="paypal_account" name="paypal_account" value="<?= htmlspecialchars($user['paypal_account'] ?? '') ?>">

      <button type="submit">💾 Simpan Perubahan</button>
    </form>
    <div style="text-align: center; margin-top: 15px;">
      <a href="profil.php" style="text-decoration: none; color: #0984e3;">🔙 Kembali ke Profil</a>
    </div>
  </div>
</body>
</html>
