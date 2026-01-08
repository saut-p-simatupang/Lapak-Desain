<?php
session_start();
require 'db.php';

if (!isset($_POST['order_id'])) {
    $_SESSION['error'] = 'ID pesanan tidak ditemukan.';
    header('Location: pesanan_saya.php');
    exit;
}

$order_id = $_POST['order_id'];

// Ambil ID pembeli
$stmt = $conn->prepare("SELECT buyer_id FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->bind_result($buyer_id);
$stmt->fetch();
$stmt->close();

// Update status
$stmt = $conn->prepare("UPDATE orders SET status = 'Proses Desain' WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();

// Tambah notifikasi ke pembeli
$message = "Pesanan Anda telah dikonfirmasi dan sedang diproses.";
$stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
$stmt->bind_param("is", $buyer_id, $message);
$stmt->execute();

$_SESSION['success'] = "Pesanan dikonfirmasi!";
header("Location: pesanan.php");
