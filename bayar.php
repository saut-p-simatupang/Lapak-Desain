<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? $_POST['order_id'] ?? null;

if (!$order_id) {
    die("Order ID tidak ditemukan.");
}

// Ambil data order & desainer
$stmt = $conn->prepare("SELECT orders.*, users.dana_account, users.paypal_account FROM orders 
    JOIN users ON orders.designer_id = users.id 
    WHERE orders.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    die("Data pesanan tidak valid.");
}

$dana_account = $order['dana_account'] ?? '-';
$paypal_account = $order['paypal_account'] ?? '-';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $status_pembayaran = 'Lunas';
    $payment_proof_name = '';
    $target_dir = "uploads_bukti/";

    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
        $temp_name = $_FILES['bukti_pembayaran']['tmp_name'];
        $original_name = basename($_FILES['bukti_pembayaran']['name']);
        $payment_proof_name = time() . '-' . $original_name;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (!move_uploaded_file($temp_name, $target_dir . $payment_proof_name)) {
            die("Gagal mengupload bukti pembayaran.");
        }
    } else {
        die("Bukti pembayaran tidak ditemukan atau gagal diunggah.");
    }

    $stmt = $conn->prepare("UPDATE orders SET metode_pembayaran = ?, payment_proof = ?, status_pembayaran = ? WHERE id = ?");
    $stmt->bind_param("sssi", $metode_pembayaran, $payment_proof_name, $status_pembayaran, $order_id);

    if ($stmt->execute()) {
        header("Location: pesanan.php?pesan=berhasil_bayar");
        exit();
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bayar Pesanan | LapakDesain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container py-5">
    <h3 class="mb-4">💳 Upload Bukti Pembayaran</h3>

    <form method="POST" action="bayar.php?order_id=<?= htmlspecialchars($order_id) ?>" enctype="multipart/form-data">
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">

        <div class="mb-3">
            <label for="metode_pembayaran" class="form-label">Metode Pembayaran:</label>
            <select name="metode_pembayaran" class="form-select" id="metode_pembayaran" required onchange="tampilkanRekening()">
                <option value="">-- Pilih Metode --</option>
                <option value="E-Wallet">Dana</option>
                <option value="PayPal">PayPal</option>
            </select>
        </div>

        <div id="rekening-dana" class="alert alert-info d-none">
            Nomor Dana Desainer: <strong><?= htmlspecialchars($dana_account) ?></strong>
        </div>

        <div id="rekening-paypal" class="alert alert-info d-none">
            Email PayPal Desainer: <strong><?= htmlspecialchars($paypal_account) ?></strong>
        </div>

        <div class="mb-3">
            <label for="payment_proof" class="form-label">📷 Pilih Bukti Transfer (jpg/png):</label>
            <input type="file" name="bukti_pembayaran" class="form-control" id="payment_proof" required>
        </div>

        <button type="submit" class="btn btn-primary">🚀 Upload & Konfirmasi</button>
    </form>
</div>

<script>
function tampilkanRekening() {
    const metode = document.getElementById('metode_pembayaran').value;
    document.getElementById('rekening-dana').classList.add('d-none');
    document.getElementById('rekening-paypal').classList.add('d-none');

    if (metode === 'E-Wallet') {
        document.getElementById('rekening-dana').classList.remove('d-none');
    } else if (metode === 'PayPal') {
        document.getElementById('rekening-paypal').classList.remove('d-none');
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>feather.replace();</script>
</body>
</html>
