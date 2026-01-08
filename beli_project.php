<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Log_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$project_id = $_POST['project_id'] ?? 0;

// Ambil info project
$stmt = $conn->prepare("SELECT user_id, price FROM projects WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    $_SESSION['error'] = "Project tidak ditemukan.";
    header("Location: shop.php");
    exit();
}

$designer_id = $project['user_id'];
$price = $project['price'];

// Cek apakah sudah ada order sebelumnya
$check = $conn->prepare("SELECT id FROM orders WHERE buyer_id = ? AND project_id = ? AND status != 'Selesai'");
$check->bind_param("ii", $user_id, $project_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $_SESSION['error'] = "Anda sudah memiliki pesanan aktif untuk project ini.";
    header("Location: pesanan.php");
    exit();
}


// Simpan ke tabel orders
$stmt = $conn->prepare("INSERT INTO orders (buyer_id, designer_id, project_id, status, created_at, status_pembayaran) VALUES (?, ?, ?, 'Menunggu Konfirmasi', NOW(), 'Belum Dibayar')");
$stmt->bind_param("iii", $user_id, $designer_id, $project_id);
$stmt->execute();
$order_id = $stmt->insert_id;

// Redirect ke halaman bayar
header("Location: bayar.php?order_id=" . $order_id);
exit();
