<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$nama_user = $_SESSION['user']['nama'];

// Hitung total kuantitas barang di keranjang untuk badge navbar
$queryBadge = mysqli_query($koneksi, "SELECT SUM(qty) AS total_qty FROM cart WHERE user_id = '$user_id'");
$dataBadge = mysqli_fetch_assoc($queryBadge);
$totalCartItems = $dataBadge['total_qty'] ? $dataBadge['total_qty'] : 0;

/* PROSES LOGIKA PEMBATALAN PESANAN */
if(isset($_POST['batalkan_pesanan'])){
    $order_id = (int)$_POST['order_id'];

    $stmtCek = mysqli_prepare($koneksi, "SELECT id, status FROM orders WHERE id = ? AND user_id = ? AND status = 'Menunggu Pembayaran'");
    mysqli_stmt_bind_param($stmtCek, "ii", $order_id, $user_id);
    mysqli_stmt_execute($stmtCek);
    $resCek = mysqli_stmt_get_result($stmtCek);

    if(mysqli_num_rows($resCek) > 0) {
        mysqli_begin_transaction($koneksi);
        try {
            $queryItems = mysqli_query($koneksi, "SELECT produk_id, qty FROM order_items WHERE order_id = '$order_id'");
            while($item = mysqli_fetch_assoc($queryItems)) {
                $produk_id = $item['produk_id'];
                $qty_kembali = $item['qty'];
                mysqli_query($koneksi, "UPDATE produk SET stok = stok + $qty_kembali WHERE id = '$produk_id'");
            }

            mysqli_query($koneksi, "UPDATE orders SET status = 'Dibatalkan' WHERE id = '$order_id'");
            mysqli_commit($koneksi);
            echo "<script>alert('Pesanan berhasil dibatalkan. Stok produk telah dikembalikan!'); window.location='history.php';</script>";
            exit();
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            echo "<script>alert('Gagal membatalkan pesanan. Silakan coba lagi.'); window.location='history.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Pesanan tidak dapat dibatalkan karena sudah diproses oleh admin!'); window.location='history.php';</script>";
        exit();
    }
}

$queryOrders = mysqli_query($koneksi, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Urban Apparel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{ background:#f4f1eb; font-family:'Segoe UI'; }
        .navbar{ background:#4a5320; }
        .card-order{ border:none; border-radius:20px; background: white; transition: 0.2s; }
        .card-order:hover{ box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .status-badge { font-size: 13px; padding: 6px 14px; border-radius: 30px; font-weight: bold; }
        .bg-waiting { background: #fff3cd; color: #856404; }
        .bg-success-custom { background: #d4edda; color: #155724; }
        .bg-danger-custom { background: #f8d7da; color: #721c24; }
        .user-indicator { background: rgba(255, 255, 255, 0.15); padding: 6px 14px; border-radius: 30px; font-size: 14px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark py-3 mb-5 shadow-sm">
    <div class="container">
        <a href="index.php" class="navbar-brand fw-bold fs-3">
            <i class="fas fa-store me-2"></i>Urban Apparel
        </a>
        <div class="d-flex gap-4 align-items-center">
            <div class="text-white user-indicator fw-semibold">
                <i class="fas fa-user-circle me-1 text-light-50"></i> Halo, <?= htmlspecialchars($nama_user); ?>
            </div>

            <a href="cart.php" class="text-white text-decoration-none position-relative">
                <i class="fas fa-shopping-cart me-1"></i>Keranjang
                <?php if($totalCartItems > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                        <?= $totalCartItems; ?>
                    </span>
                <?php endif; ?>
            </a>
            <a href="history.php" class="text-white text-decoration-none fw-bold">
                <i class="fas fa-box me-1"></i>Pesanan
            </a>
            <a href="logout.php" class="btn btn-sm btn-light fw-bold px-3">Logout</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <h2 class="fw-bold text-dark mb-4"><i class="fas fa-receipt me-2 text-secondary"></i>Riwayat Pesanan Anda</h2>

    <?php if(mysqli_num_rows($queryOrders) > 0) { 
        while($order = mysqli_fetch_assoc($queryOrders)) { 
            $badgeClass = "bg-secondary text-white";
            if($order['status'] == 'Menunggu Pembayaran') {
                $badgeClass = "bg-waiting";
            } elseif($order['status'] == 'Dibatalkan') {
                $badgeClass = "bg-danger-custom";
            } elseif($order['status'] == 'Selesai' || $order['status'] == 'Diproses' || $order['status'] == 'Dikirim') {
                $badgeClass = "bg-success-custom";
            }
    ?>
            <div class="card card-order p-4 shadow-sm mb-4">
                <div class="row align-items-center">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <span class="text-muted small d-block">Nomor Nota / ID</span>
                        <strong class="text-dark">#UA-<?= $order['id']; ?></strong>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <span class="text-muted small d-block">Tanggal Transaksi</span>
                        <span class="text-dark fw-semibold"><?= date('d M Y, H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <span class="text-muted small d-block">Total Bayar</span>
                        <span class="fw-bold text-dark">Rp <?= number_format($order['total_harga'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="col-md-2 text-md-center mb-3 mb-md-0">
                        <span class="text-muted small d-block mb-1">Status</span>
                        <span class="status-badge <?= $badgeClass; ?>"><?= htmlspecialchars($order['status']); ?></span>
                    </div>

                    <div class="col-md-2 text-end">
                        <?php if($order['status'] == 'Menunggu Pembayaran') { ?>
                            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok yang terkunci akan dikembalikan ke toko.');">
                                <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                <button type="submit" name="batalkan_pesanan" class="btn btn-sm btn-outline-danger w-100 py-2 fw-semibold rounded-3">
                                    <i class="fas fa-times-circle me-1"></i>Batalkan
                                </button>
                            </form>
                        <?php } else { ?>
                            <button class="btn btn-sm btn-light border text-muted w-100 py-2 rounded-3" disabled>
                                Tidak Ada Aksi
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
    <?php 
        } 
    } else { ?>
        <div class="card p-5 text-center shadow-sm rounded-4 bg-white">
            <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
            <h4 class="text-muted fw-bold">Belum Ada Transaksi</h4>
            <div>
                <a href="index.php" class="btn btn-dark px-4">Cari Produk Sekarang</a>
            </div>
        </div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>