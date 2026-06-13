<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$nama_user = $_SESSION['user']['nama'];

/* PROSES ADD TO CART */
if(isset($_GET['add'])){
    $produk_id = (int)$_GET['add'];
    $ukuran_dipilih = isset($_POST['ukuran_dipilih']) ? trim($_POST['ukuran_dipilih']) : 'All Size';

    $stmtProduk = mysqli_prepare($koneksi, "SELECT * FROM produk WHERE id = ?");
    mysqli_stmt_bind_param($stmtProduk, "i", $produk_id);
    mysqli_stmt_execute($stmtProduk);
    $produk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtProduk));

    if(!$produk || $produk['stok'] <= 0){
        echo "<script>alert('Stok habis atau produk tidak tersedia'); window.location='index.php';</script>";
        exit();
    }

    $stmtCart = mysqli_prepare($koneksi, "SELECT * FROM cart WHERE user_id = ? AND produk_id = ? AND ukuran = ?");
    mysqli_stmt_bind_param($stmtCart, "iis", $user_id, $produk_id, $ukuran_dipilih);
    mysqli_stmt_execute($stmtCart);
    $cekCart = mysqli_stmt_get_result($stmtCart);

    if(mysqli_num_rows($cekCart) > 0){
        $cart = mysqli_fetch_assoc($cekCart);
        if($cart['qty'] < $produk['stok']){
            mysqli_query($koneksi, "UPDATE cart SET qty = qty + 1 WHERE id = '".$cart['id']."'");
        }
    } else {
        mysqli_query($koneksi, "INSERT INTO cart (user_id, produk_id, ukuran, qty) VALUES ('$user_id', '$produk_id', '$ukuran_dipilih', 1)");
    }

    header('Location: cart.php');
    exit();
}

/* PROSES HAPUS ITEM */
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM cart WHERE id = '$id' AND user_id = '$user_id'");
    header('Location: cart.php');
    exit();
}

$query = mysqli_query($koneksi, "
    SELECT cart.*, produk.nama_produk, produk.harga, produk.foto, produk.stok 
    FROM cart 
    JOIN produk ON cart.produk_id = produk.id 
    WHERE cart.user_id='$user_id'
");

// HITUNG BADGE JUMLAH KERANJANG
$queryBadge = mysqli_query($koneksi, "SELECT SUM(qty) AS total_qty FROM cart WHERE user_id = '$user_id'");
$dataBadge = mysqli_fetch_assoc($queryBadge);
$totalCartItems = $dataBadge['total_qty'] ? $dataBadge['total_qty'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - Urban Apparel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{ background:#f4f1eb; font-family:'Segoe UI'; }
        .navbar{ background:#4a5320; }
        .card{ border:none; border-radius:20px; }
        .btn-earth { background:#8d6e63; color:white; }
        .btn-earth:hover { background:#6d4c41; color:white; }
        .user-indicator { background: rgba(255, 255, 255, 0.15); padding: 6px 14px; border-radius: 30px; font-size: 14px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark py-3 mb-4 shadow-sm">
    <div class="container">
        <a href="index.php" class="navbar-brand fw-bold fs-3">
            <i class="fas fa-store me-2"></i>Urban Apparel
        </a>
        <div class="d-flex gap-4 align-items-center">
            <div class="text-white user-indicator fw-semibold">
                <i class="fas fa-user-circle me-1 text-light-50"></i> Halo, <?= htmlspecialchars($nama_user); ?>
            </div>

            <a href="cart.php" class="text-white text-decoration-none position-relative fw-bold">
                <i class="fas fa-shopping-cart me-1"></i>Keranjang
                <?php if($totalCartItems > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                        <?= $totalCartItems; ?>
                    </span>
                <?php endif; ?>
            </a>
            <a href="history.php" class="text-white text-decoration-none">
                <i class="fas fa-box me-1"></i>Pesanan
            </a>
            <a href="logout.php" class="btn btn-sm btn-light fw-bold px-3">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">Keranjang Belanja</h2>
        <a href="index.php" class="btn btn-dark">Kembali Belanja</a>
    </div>

    <?php if(mysqli_num_rows($query) > 0) { ?>
    <form method="POST" action="checkout.php">
        <div class="card p-4 shadow-sm bg-white">
            <?php 
            while($data = mysqli_fetch_assoc($query)){ 
                $subtotal = $data['harga'] * $data['qty'];
            ?>
            <div class="row align-items-center mb-4">
                <div class="col-md-1 text-center">
                    <input type="checkbox" name="selected[]" value="<?= $data['id']; ?>" class="form-check-input ms-0">
                </div>
                <div class="col-md-2">
                    <img src="uploads/<?= htmlspecialchars($data['foto']); ?>" class="img-fluid rounded shadow-sm">
                </div>
                <div class="col-md-3">
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($data['nama_produk']); ?></h5>
                    <p class="mb-1"><span class="badge bg-light text-dark border fw-semibold">Ukuran: <?= htmlspecialchars($data['ukuran']); ?></span></p>
                    <p class="text-muted small mb-0">Sisa Stok Barang: <?= $data['stok']; ?></p>
                </div>
                <div class="col-md-2 text-secondary">
                    Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                </div>
                
                <div class="col-md-2">
                    <div class="input-group" style="max-width: 120px;">
                        <a href="update_quantity.php?id=<?= $data['id']; ?>&action=minus" class="btn btn-outline-secondary btn-sm fw-bold <?= ($data['qty'] <= 1) ? 'disabled text-muted' : ''; ?>">-</a>
                        <input type="text" class="form-control form-control-sm text-center bg-white" value="<?= $data['qty']; ?>" readonly>
                        <a href="update_quantity.php?id=<?= $data['id']; ?>&action=plus" class="btn btn-outline-secondary btn-sm fw-bold <?= ($data['qty'] >= $data['stok']) ? 'disabled text-muted' : ''; ?>">+</a>
                    </div>
                </div>

                <div class="col-md-1 fw-bold text-dark">
                    Rp <?= number_format($subtotal, 0, ',', '.'); ?>
                </div>
                <div class="col-md-1 text-end">
                    <a href="cart.php?hapus=<?= $data['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk dari keranjang?')">X</a>
                </div>
            </div>
            <hr class="text-muted">
            <?php } ?>

            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-earth px-4 py-2 fw-bold rounded-3">
                    Checkout Item Terpilih
                </button>
            </div>
        </div>
    </form>
    <?php } else { ?>
        <div class="card p-5 text-center shadow-sm rounded-4 bg-white">
            <h4 class="text-muted fw-bold mb-2">Keranjang Belanja Kosong</h4>
            <div class="mt-2">
                <a href="index.php" class="btn btn-dark px-4">Mulai Belanja</a>
            </div>
        </div>
    <?php } ?>
</div>
</body>
</html>