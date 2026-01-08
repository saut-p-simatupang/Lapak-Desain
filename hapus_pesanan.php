<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Log_form.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['user_id'];

    // Pastikan pesanan milik user dan statusnya "Menunggu Konfirmasi" & belum dibayar
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND buyer_id = ? AND status = 'Menunggu Konfirmasi' AND status_pembayaran = 'Belum Dibayar'");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Hapus terlebih dahulu pembayaran yang terkait (jika ada)
        $del_payment = $conn->prepare("DELETE FROM payments WHERE order_id = ?");
        $del_payment->bind_param("i", $order_id);
        $del_payment->execute(); // tidak perlu cek jumlah baris karena bisa saja tidak ada

        // Baru hapus pesanan dari tabel orders
        $delete = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $delete->bind_param("i", $order_id);

        if ($delete->execute()) {
            $_SESSION['success'] = "Pesanan berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus pesanan.";
        }
    } else {
        $_SESSION['error'] = "Pesanan tidak ditemukan, sudah dibayar, atau tidak bisa dihapus.";
    }
}

header("Location: pesanan.php");
exit();
