<?php
session_start();
include "db.php"; // koneksi ke database

// === Proses Login ===
if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $query  = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_id'] = $user['id']; 

            header("Location: main.php");
            exit();
        } else {
            echo "<script>alert('Password salah'); window.location.href='Log_form.php';</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan'); window.location.href='Log_form.php';</script>";
    }
}

// === Proses Register ===
if (isset($_POST['register'])) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Email sudah digunakan'); window.location.href='Log_form.php';</script>";
        exit();
    }

    $insert = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    if (mysqli_query($conn, $insert)) {
        header("Location: Log_form.php?register=success");
        exit();
    } else {
        echo "<script>alert('Registrasi gagal'); window.location.href='Log_form.php';</script>";
    }
}
?>
