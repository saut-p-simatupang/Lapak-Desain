<?php
session_start();
include "db.php";

$login_error = "";
$register_error = "";
$register_success = "";

// ==== LOGIN ====
if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['login_email']);
    $password = $_POST['login_password'];

    if (empty($email) || empty($password)) {
        $login_error = "Email dan password wajib diisi.";
    } else {
        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        $user = mysqli_fetch_assoc($query);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: main.php");
            exit();
        } else {
            $login_error = "Email atau password salah.";
        }
    }
}

// ==== REGISTER ====
if (isset($_POST['register'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $register_error = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $register_error = "Format email tidak valid.";
    } elseif ($password !== $confirm) {
        $register_error = "Konfirmasi password tidak cocok.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $register_error = "Email sudah digunakan.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')");

            if ($insert) {
                $register_success = "Registrasi berhasil. Silakan login.";
            } else {
                $register_error = "Terjadi kesalahan saat menyimpan data.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Login / Register - LapakDesain</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap");

    * {
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(to right, #e2e2e2, #c9d6ff);
    }

    .container {
      width: 100%;
      max-width: 420px;
      padding: 0 15px;
    }

    .form-box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      display: none;
    }

    .form-box.active {
      display: block;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-weight: 600;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      background: #f2f2f2;
      border: none;
      border-radius: 6px;
      font-size: 16px;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #7494ec;
      border: none;
      border-radius: 6px;
      color: white;
      font-weight: 600;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #5c7de0;
    }

    .switch-link {
      text-align: center;
      font-size: 14px;
      margin-top: 10px;
    }

    .switch-link a {
      color: #7494ec;
      text-decoration: none;
      font-weight: 500;
    }

    .switch-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Login Form -->
  <div class="form-box active" id="loginForm">
    <h2>Login</h2>
    <?php if ($login_error): ?>
      <div class="alert alert-danger"><?= $login_error ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="email" name="login_email" placeholder="Email" required />
      <input type="password" name="login_password" placeholder="Password" required />
      <button type="submit" name="login">Masuk</button>
    </form>
    <p class="switch-link">Belum punya akun? <a href="#" onclick="showForm('registerForm')">Daftar di sini</a></p>
  </div>

  <!-- Register Form -->
  <div class="form-box" id="registerForm">
    <h2>Daftar</h2>
    <?php if ($register_error): ?>
      <div class="alert alert-danger"><?= $register_error ?></div>
    <?php elseif ($register_success): ?>
      <div class="alert alert-success"><?= $register_success ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="name" placeholder="Nama Lengkap" required />
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required />
      <button type="submit" name="register">Daftar</button>
    </form>
    <p class="switch-link">Sudah punya akun? <a href="#" onclick="showForm('loginForm')">Login di sini</a></p>
  </div>
</div>

<!-- JS -->
<script>
  function showForm(formId) {
    document.querySelectorAll('.form-box').forEach(form => form.classList.remove('active'));
    document.getElementById(formId).classList.add('active');
  }

  <?php if ($register_success): ?>
    showForm('loginForm');
  <?php endif; ?>
</script>

</body>
</html>
