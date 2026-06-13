<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Menjaga agar user tidak bisa mengintip invoice/detail order milik pembeli lain
$cekAkses = mysqli_query($koneksi, "SELECT id FROM orders WHERE id='$id' AND user_id='$user_id'");
if(mysqli_num_rows($cekAkses) === 0){
    echo "<script>alert('Akses dilarang!'); window.location='history.php';</script>";
    exit();
}

$query = mysqli_query($koneksi, "
    SELECT order_items.*, produk.nama_produk, produk.foto 
    FROM order_items 
    JOIN produk ON order_items.produk_id = produk.id 
    WHERE order_items.order_id='$id'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Order #<?= $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f4f1eb;">

<div class="container py-5" style="max-width: 800px;">
    <a href="history.php" class="btn btn-sm btn-dark mb-4">← Kembali ke Riwayat</a>
    <h3 class="fw-bold mb-4">Detail Belanja ID #<?= $id; ?></h3>

    <?php
    $total = 0;
    while($data = mysqli_fetch_assoc($query)){
        $subtotal = $data['harga'] * $data['qty'];
        $total += $subtotal;
    ?>
    <div class="card border-0 shadow-sm p-3 mb-3 rounded-4 bg-white">
        <div class="row align-items-center">
            <div class="col-md-2 col-3">
                <img src="uploads/<?= htmlspecialchars($data['foto']); ?>" class="img-fluid rounded">
            </div>
            <div class="col-md-4 col-9">
                <h6 class="fw-bold mb-1"><?= htmlspecialchars($data['nama_produk']); ?></h6>
                <small class="text-muted">Harga: Rp <?= number_format($data['harga'], 0, ',', '.'); ?></small>
            </div>
            <div class="col-md-3 col-6 mt-2 mt-md-0">
                <span class="text-muted">Jumlah:</span> <strong><?= $data['qty']; ?> Pcs</strong>
            </div>
            <div class="col-md-3 col-6 text-md-end mt-2 mt-md-0 fw-bold text-dark">
                Rp <?= number_format($subtotal, 0, ',', '.'); ?>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="card border-0 shadow-sm p-4 mt-4 rounded-4 bg-dark text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold text-white-50">Total Pembayaran :</h5>
            <h3 class="m-0 fw-bold text-warning">Rp <?= number_format($total, 0, ',', '.'); ?></h3>
        </div>
    </div>
</div>
</body>
</html>