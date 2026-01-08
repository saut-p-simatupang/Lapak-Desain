<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'] ?? null;
    $order_id = $_POST['order_id'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $review = $_POST['review'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$project_id || !$order_id || !$rating || !$review || !$user_id) {
        $_SESSION['error'] = "Data tidak lengkap.";
        header("Location: pesanan.php");
        exit();
    }

    // Cek apakah pesanan benar milik user dan sudah selesai
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND buyer_id = ? AND project_id = ? AND status = 'Selesai'");
    $stmt->bind_param("iii", $order_id, $user_id, $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Pesanan tidak valid atau belum selesai.";
        header("Location: pesanan.php");
        exit();
    }

    // Cek apakah user sudah pernah memberi ulasan
    $stmt = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND project_id = ?");
    $stmt->bind_param("ii", $user_id, $project_id);
    $stmt->execute();
    $review_exists = $stmt->get_result()->num_rows > 0;

    if ($review_exists) {
        $_SESSION['error'] = "Anda sudah memberikan ulasan.";
        header("Location: pesanan.php");
        exit();
    }

    // Simpan ulasan
    $stmt = $conn->prepare("INSERT INTO reviews (order_id, project_id, user_id, rating, review, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiiis", $order_id, $project_id, $user_id, $rating, $review);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Ulasan berhasil dikirim.";
    } else {
        $_SESSION['error'] = "Gagal menyimpan ulasan.";
    }

    header("Location: pesanan.php");
    exit();
}
