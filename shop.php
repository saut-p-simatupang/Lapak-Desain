<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Log_form.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT name FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$_SESSION['user_name'] = $user['name']; // memastikan nama tersimpan di sesi
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/shop.css">
</head>
<body>
    <!-- navbar -->
     <?php include 'navbar.php'; ?>
    <!-- navbar -->
    <!-- Shop Content -->
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"> Explore Your Design </h2>
        </div>

        <div class="row g-4">
            <?php
            $sql = "SELECT p.*, u.name FROM projects p JOIN users u ON p.user_id = u.id ORDER BY p.id DESC";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="col-md-4 col-sm-6">
                    <a href="project_detail.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="card product-card h-100">
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top product-img" alt="Project">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="mb-1">By: <strong><?php echo htmlspecialchars($row['name']); ?></strong></p>
                                <p class="card-text">
                                    <strong>Rp<?php echo number_format($row['price'], 0, ',', '.'); ?></strong><br>
                                   
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>feather.replace();</script>
</body>
</html>
