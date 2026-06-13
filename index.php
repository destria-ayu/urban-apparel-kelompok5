<?php 
session_start();
include 'koneksi.php';

if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
// Mengambil nama user langsung dari session login
$nama_user_aktif = $_SESSION['user']['nama'];

// Hitung total kuantitas barang di keranjang untuk badge navbar
$queryBadge = mysqli_query($koneksi, "SELECT SUM(qty) AS total_qty FROM cart WHERE user_id = '$user_id'");
$dataBadge = mysqli_fetch_assoc($queryBadge);
$totalCartItems = $dataBadge['total_qty'] ? $dataBadge['total_qty'] : 0;

/* ====================================================
   LOGIKA FILTER & PENCARIAN (OPSI RENTANG HARGA)
   ==================================================== */
$sql = "SELECT * FROM produk";
$where = [];
$params = [];
$types = "";

if(isset($_GET['search']) && trim($_GET['search']) != ''){
    $search = trim($_GET['search']);
    $where[] = "nama_produk LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

if(isset($_GET['kategori']) && trim($_GET['kategori']) != ''){
    $kategori = trim($_GET['kategori']);
    $where[] = "TRIM(kategori) = ?";
    $params[] = $kategori;
    $types .= "s";
}

if(isset($_GET['range_harga']) && trim($_GET['range_harga']) != ''){
    $range = $_GET['range_harga'];

    if($range == 'under_100'){
        $where[] = "harga < 100000";
    } 
    elseif($range == '100_200'){
        $where[] = "harga >= 100000 AND harga <= 200000";
    } 
    elseif($range == '200_300'){
        $where[] = "harga >= 200000 AND harga <= 300000";
    } 
    elseif($range == '300_500'){
        $where[] = "harga >= 300000 AND harga <= 500000";
    } 
    elseif($range == 'above_500'){
        $where[] = "harga > 500000";
    }
}

if(count($where) > 0){
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($koneksi, $sql);
if(count($params) > 0){
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);

$queryKategori = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM produk");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urban Apparel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{ background:#f4f1eb; font-family:'Segoe UI'; }
        .navbar{ background:#4a5320; }
        .carousel-item{ height:450px; border-radius:25px; overflow:hidden; }
        .carousel-item img{ height:100%; object-fit:cover; filter:brightness(55%); }
        .carousel-caption{ bottom:100px; }
        .carousel-caption h1{ font-size:48px; font-weight:bold; }
        .product-card{ border:none; border-radius:20px; overflow:hidden; transition:.3s; background:white; }
        .product-card:hover{ transform:translateY(-8px); box-shadow:0 15px 30px rgba(0,0,0,.1); }
        .product-img{ height:280px; object-fit:cover; }
        .btn-earth{ background:#8d6e63; color:white; border:none; }
        .btn-earth:hover{ background:#6d4c41; color:white; }
        .price{ color:#c08552; }
        .badge-stock{ position:absolute; top:15px; right:15px; z-index:10; font-size:13px; }
        /* Style Tambahan untuk Teks Nama User */
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
                <i class="fas fa-user-circle me-1 text-light-50"></i> 
                Halo, <?= htmlspecialchars($nama_user_aktif); ?>
            </div>

            <a href="cart.php" class="text-white text-decoration-none position-relative">
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

<div class="container pb-5">
    <div id="bannerCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-inner rounded-4 shadow">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1523398002811-999ca8dec234?q=80&w=1400&auto=format&fit=crop" class="d-block w-100">
                <div class="carousel-caption text-start px-5">
                    <h1>Urban Streetwear</h1>
                    <p class="fs-5">Temukan style terbaikmu hari ini</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=1400&auto=format&fit=crop" class="d-block w-100">
                <div class="carousel-caption px-5">
                    <h1>New Collection</h1>
                    <p class="fs-5">Fashion modern dengan kualitas premium</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <form method="GET" class="bg-white p-4 rounded-4 shadow-sm mb-5" id="filterForm">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted">Cari Nama Produk</label>
                <input type="text" name="search" class="form-control" placeholder="Baju, celana..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Kategori</label>
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php while($kat = mysqli_fetch_array($queryKategori)){ ?>
                        <option value="<?= htmlspecialchars($kat['kategori']); ?>" <?= (isset($_GET['kategori']) && $_GET['kategori'] == $kat['kategori']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($kat['kategori']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Rentang Harga</label>
                <select name="range_harga" class="form-select">
                    <option value="">Semua Harga</option>
                    <option value="under_100" <?= (isset($_GET['range_harga']) && $_GET['range_harga'] == 'under_100') ? 'selected' : ''; ?>>< Rp 100.000</option>
                    <option value="100_200" <?= (isset($_GET['range_harga']) && $_GET['range_harga'] == '100_200') ? 'selected' : ''; ?> border>Rp 100.000 - Rp 200.000</option>
                    <option value="200_300" <?= (isset($_GET['range_harga']) && $_GET['range_harga'] == '200_300') ? 'selected' : ''; ?>>Rp 200.000 - Rp 300.000</option>
                    <option value="300_500" <?= (isset($_GET['range_harga']) && $_GET['range_harga'] == '300_500') ? 'selected' : ''; ?> border>Rp 300.000 - Rp 500.000</option>
                    <option value="above_500" <?= (isset($_GET['range_harga']) && $_GET['range_harga'] == 'above_500') ? 'selected' : ''; ?>>> Rp 500.000</option>
                </select>
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end mt-3">
                <a href="index.php" class="btn btn-light border px-4">Reset Filter</a>
                <button class="btn btn-dark px-5" type="submit">Terapkan Filter</button>
            </div>
        </div>
    </form>

    <div class="row g-4">
        <?php if(mysqli_num_rows($query) > 0){ 
            while($data = mysqli_fetch_array($query)){ ?>
                <div class="col-md-3">
                    <div class="card product-card h-100 shadow-sm position-relative">
                        <?php if($data['stok'] <= 0){ ?>
                            <span class="badge bg-danger badge-stock">Stok Habis</span>
                        <?php } ?>
                        <img src="uploads/<?= htmlspecialchars($data['foto']); ?>" class="card-img-top product-img" alt="Produk">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($data['kategori']); ?></span>
                                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($data['nama_produk']); ?></h5>
                                <p class="text-muted small">Stok: <?= $data['stok']; ?> Pcs</p>
                            </div>
                            <div>
                                <h4 class="price fw-bold mb-3">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></h4>
                                <div class="d-grid gap-2">
                                    <a href="detail.php?id=<?= $data['id']; ?>" class="btn btn-earth btn-sm">Lihat Produk</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } 
        } else { ?>
            <div class="col-12">
                <div class="alert alert-light text-center p-5 rounded-4 shadow-sm">
                    <h4 class="fw-bold text-muted mb-2">Produk tidak ditemukan</h4>
                    <p class="text-muted mb-0">Tidak ada pakaian yang cocok dengan kriteria filter harga Anda.</p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>