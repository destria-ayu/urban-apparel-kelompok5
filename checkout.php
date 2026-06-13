<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

if(!isset($_POST['selected']) || count($_POST['selected']) === 0){
    echo "<script>alert('Pilih minimal satu produk untuk dicheckout!'); window.location='cart.php';</script>";
    exit();
}

$selected = $_POST['selected'];
$total = 0;
$items = [];

foreach($selected as $cart_id){
    $cart_id = (int)$cart_id;

    $query = mysqli_query($koneksi, "
        SELECT cart.*, produk.nama_produk, produk.harga, produk.stok 
        FROM cart 
        JOIN produk ON cart.produk_id = produk.id 
        WHERE cart.id='$cart_id' AND cart.user_id='$user_id'
    ");
    $data = mysqli_fetch_assoc($query);

    if(!$data) continue;

    if($data['qty'] > $data['stok']){
        echo "<script>alert('Gagal! Stok produk ".$data['nama_produk']." tidak mencukupi.'); window.location='cart.php';</script>";
        exit();
    }

    $subtotal = $data['harga'] * $data['qty'];
    $total += $subtotal;
    $items[] = $data;
}

if(count($items) === 0){
    header('Location: cart.php');
    exit();
}

/* 1. INSERT KE TABEL ORDERS */
$stmtOrder = mysqli_prepare($koneksi, "INSERT INTO orders (user_id, total_harga) VALUES (?, ?)");
mysqli_stmt_bind_param($stmtOrder, "ii", $user_id, $total);
mysqli_stmt_execute($stmtOrder);
$order_id = mysqli_insert_id($koneksi);

/* 2. INSERT ITEMS & KURANGI STOK */
foreach($items as $item){
    // Simpan item pesanan
    $stmtItem = mysqli_prepare($koneksi, "INSERT INTO order_items (order_id, produk_id, qty, harga) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmtItem, "iiii", $order_id, $item['produk_id'], $item['qty'], $item['harga']);
    mysqli_stmt_execute($stmtItem);

    // Kurangi stok barang utama
    mysqli_query($koneksi, "UPDATE produk SET stok = stok - ".$item['qty']." WHERE id='".$item['produk_id']."'");

    // Bersihkan keranjang belanja
    mysqli_query($koneksi, "DELETE FROM cart WHERE id='".$item['id']."'");
}

echo "<script>alert('Checkout berhasil diproses!'); window.location='history.php';</script>";
exit();
?>