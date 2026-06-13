<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$nama_user = $_SESSION['user']['nama'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// AMBIL DATA PRODUK
$stmt = mysqli_prepare($koneksi, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_array(mysqli_stmt_get_result($stmt));

if(!$data){
    echo "<script>alert('Produk tidak ditemukan!'); window.location='index.php';</script>";
    exit();
}

// PROSES INPUT ULASAN BARU
if(isset($_POST['kirim_ulasan'])){
    $rating = (int)$_POST['rating'];
    $komentar = trim($_POST['komentar']);

    if($rating >= 1 && $rating <= 5 && !empty($komentar)){
        $stmtReview = mysqli_prepare($koneksi, "INSERT INTO ulasan (produk_id, user_id, nama_user, rating, komentar) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmtReview, "iisis", $id, $user_id, $nama_user, $rating, $komentar);
        mysqli_stmt_execute($stmtReview);
        
        echo "<script>alert('Terima kasih atas ulasan Anda!'); window.location='detail.php?id=$id';</script>";
        exit();
    }
}

// PROSES HAPUS ULASAN MANDIRI
if(isset($_POST['hapus_ulasan'])){
    $ulasan_id = (int)$_POST['ulasan_id'];

    $stmtHapus = mysqli_prepare($koneksi, "DELETE FROM ulasan WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmtHapus, "ii", $ulasan_id, $user_id);
    
    if(mysqli_stmt_execute($stmtHapus)){
        echo "<script>alert('Ulasan Anda berhasil dihapus!'); window.location='detail.php?id=$id';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menghapus ulasan.'); window.location='detail.php?id=$id';</script>";
        exit();
    }
}

$queryUlasan = mysqli_query($koneksi, "SELECT * FROM ulasan WHERE produk_id = '$id' ORDER BY id DESC");
$queryRata = mysqli_query($koneksi, "SELECT AVG(rating) AS rata_rating, COUNT(id) AS total_ulasan FROM ulasan WHERE produk_id = '$id'");
$dataRata = mysqli_fetch_assoc($queryRata);
$rataRating = round($dataRata['rata_rating'], 1);
$totalUlasan = $dataRata['total_ulasan'];

// BADGE KERANJANG NAVBAR
$queryBadge = mysqli_query($koneksi, "SELECT SUM(qty) AS total_qty FROM cart WHERE user_id = '$user_id'");
$dataBadge = mysqli_fetch_assoc($queryBadge);
$totalCartItems = $dataBadge['total_qty'] ? $dataBadge['total_qty'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['nama_produk']); ?> - Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{ background:#f4f1eb; font-family:'Segoe UI'; }
        .navbar{ background:#4a5320; }
        .product-image{ width:100%; height:480px; object-fit:cover; border-radius:20px; }
        .card-detail{ border:none; border-radius:25px; overflow:hidden; }
        .price{ color:#c08552; font-weight:bold; }
        .btn-earth { background:#8d6e63; color:white; border:none; }
        .btn-earth:hover { background:#6d4c41; color:white; }
        .text-warning { color: #ffc107 !important; }
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
            <a href="history.php" class="text-white text-decoration-none"><i class="fas fa-box"></i> Pesanan</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="card card-detail shadow-lg mb-5">
        <div class="row g-0">
            <div class="col-md-6">
                <img src="uploads/<?= htmlspecialchars($data['foto']); ?>" class="product-image" alt="Produk">
            </div>
            <div class="col-md-6">
                <div class="p-5">
                    <span class="badge bg-secondary mb-2"><?= htmlspecialchars($data['kategori']); ?></span>
                    <h1 class="fw-bold mb-1"><?= htmlspecialchars($data['nama_produk']); ?></h1>
                    
                    <div class="mb-3 small">
                        <span class="text-warning fw-bold fs-5 me-1"><?= $totalUlasan > 0 ? $rataRating : '0'; ?></span>
                        <?php 
                        $stars = $totalUlasan > 0 ? floor($rataRating) : 0;
                        for($i=1; $i<=5; $i++) {
                            echo $i <= $stars ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-muted"></i>';
                        }
                        ?>
                        <span class="text-muted ms-2">(<?= $totalUlasan; ?> Ulasan)</span>
                    </div>

                    <h2 class="price mb-4">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></h2>
                    <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($data['deskripsi'])); ?></p>

                    <form method="POST" action="cart.php?add=<?= $data['id']; ?>">
                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="form-label fw-bold text-muted mb-1">Pilih Ukuran Baju</label>
                                <select name="ukuran_dipilih" class="form-select fw-bold border-dark" required>
                                    <option value="">-- Pilih Size --</option>
                                    <?php 
                                    $listUkuran = explode(',', $data['ukuran']);
                                    foreach($listUkuran as $uk) {
                                        $ukClean = trim($uk);
                                        echo "<option value='$ukClean'>Size $ukClean</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-muted mb-1">Sisa Stok</label>
                                <p class="fw-bold text-dark fs-5 mt-1"><?= htmlspecialchars($data['stok']); ?> Pcs</p>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <?php if($data['stok'] > 0){ ?>
                                <button type="submit" class="btn btn-earth btn-lg">
                                    <i class="fas fa-cart-plus me-2"></i>Masukkan ke Keranjang
                                </button>
                            <?php } else { ?>
                                <button type="submit" class="btn btn-secondary btn-lg" disabled>Stok Habis</button>
                            <?php } ?>
                            <a href="index.php" class="btn btn-light border btn-lg">Kembali Belanja</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4 shadow-sm rounded-4 bg-white border-0">
                <h5 class="fw-bold mb-3">Tulis Ulasan Produk</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Beri Rating Bintang</label>
                        <select name="rating" class="form-select text-warning fw-bold" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Puas)</option>
                            <option value="4">⭐⭐⭐⭐ (4 - Puas)</option>
                            <option value="3">⭐⭐⭐ (3 - Cukup Baik)</option>
                            <option value="2">⭐⭐ (2 - Kurang Puas)</option>
                            <option value="1">⭐ (1 - Buruk)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Komentar / Review</label>
                        <textarea name="komentar" rows="3" class="form-control" placeholder="Tulis pendapatmu..." required></textarea>
                    </div>
                    <button type="submit" name="kirim_ulasan" class="btn btn-dark w-100 btn-sm">Kirim Ulasan</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4 shadow-sm rounded-4 bg-white border-0">
                <h5 class="fw-bold mb-4">Ulasan Pelanggan</h5>
                <?php if(mysqli_num_rows($queryUlasan) > 0) { 
                    while($ulasan = mysqli_fetch_assoc($queryUlasan)) { ?>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <strong class="text-dark small">
                                        <i class="fas fa-user-circle me-1 text-secondary"></i> 
                                        <?= htmlspecialchars($ulasan['nama_user']); ?>
                                        <?php if($ulasan['user_id'] == $user_id) echo '<span class="badge bg-light text-dark border ms-1" style="font-size:10px;">Anda</span>'; ?>
                                    </strong>
                                    <div class="text-warning my-1" style="font-size: 11px;">
                                        <?php 
                                        for($i=1; $i<=5; $i++){
                                            echo $i <= $ulasan['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star text-muted"></i>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <small class="text-muted" style="font-size: 11px;"><?= date('d M Y', strtotime($ulasan['created_at'])); ?></small>
                                    <?php if($ulasan['user_id'] == $user_id) { ?>
                                        <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?');" class="d-inline">
                                            <input type="hidden" name="ulasan_id" value="<?= $ulasan['id']; ?>">
                                            <button type="submit" name="hapus_ulasan" class="btn btn-link text-danger p-0 m-0 text-decoration-none small">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                            <p class="text-secondary small m-0 mt-1">"<?= htmlspecialchars($ulasan['komentar']); ?>"</p>
                        </div>
                    <?php } 
                } else { ?>
                    <p class="text-muted text-center py-4 my-0 small">Belum ada ulasan.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>