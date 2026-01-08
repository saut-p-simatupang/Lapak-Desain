<?php
session_start();
require 'db.php';

if (!isset($_POST['order_id']) || !isset($_FILES['final_file'])) {
    $_SESSION['error'] = 'Data tidak lengkap.';
    header('Location: pesanan_saya.php');
    exit;
}

$order_id = $_POST['order_id'];
$filename = basename($_FILES['final_file']['name']);
$target = 'hasil_upload/' . $filename;

if (move_uploaded_file($_FILES['final_file']['tmp_name'], $target)) {
    // Ambil buyer_id
    $stmt = $conn->prepare("SELECT buyer_id FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($buyer_id);
    $stmt->fetch();
    $stmt->close();

    // Update order
    $stmt = $conn->prepare("UPDATE orders SET final_file = ?, status = 'Selesai' WHERE id = ?");
    $stmt->bind_param("si", $filename, $order_id);
    $stmt->execute();
    $stmt->close();

    // Tambahkan notifikasi ke pembeli
    $message = "Desain Anda telah selesai! Silakan download hasilnya.";
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $buyer_id, $message);
    $stmt->execute();

    $_SESSION['success'] = "Hasil berhasil dikirim ke pembeli.";
    header("Location: pesanan.php");
} else {
    $_SESSION['error'] = "Gagal mengunggah file.";
    header("Location: pesanan_saya.php");
}
