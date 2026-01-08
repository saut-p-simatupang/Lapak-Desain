<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: main.php");
    exit();
}

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$update = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
$update->bind_param("ii", $id, $user_id);
$update->execute();

header("Location: main.php"); // arahkan kembali ke halaman utama
