<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Pastikan desainer adalah pemilik project
$cek = mysqli_query($conn, "SELECT o.*, p.user_id AS desainer_id 
    FROM orders o 
    JOIN projects p ON o.project_id = p.id 
    WHERE o.id='$order_id' AND p.user_id='$user_id'");
if (mysqli_num_rows($cek) == 0) {
    echo "<script>alert('Akses ditolak.'); window.location='pesanan.php';</script>";
    exit;
}

mysqli_query($conn, "UPDATE orders SET status_pembayaran='Dibayar' WHERE id='$order_id'");
echo "<script>alert('Pembayaran terverifikasi.'); window.location='pesanan.php';</script>";
