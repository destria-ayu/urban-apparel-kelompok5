<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Validasi data cart milik user terkait
$query = mysqli_query($koneksi, "
    SELECT cart.*, produk.stok 
    FROM cart 
    JOIN produk ON cart.produk_id = produk.id 
    WHERE cart.id='$id' AND cart.user_id='$user_id'
");
$data = mysqli_fetch_assoc($query);

if(!$data){
    header('Location: cart.php');
    exit();
}

/* TOMBOL PLUS */
if($action == 'plus'){
    if($data['qty'] < $data['stok']){
        mysqli_query($koneksi, "UPDATE cart SET qty = qty + 1 WHERE id='$id'");
    }
}

/* TOMBOL MINUS */
if($action == 'minus'){
    // PERUBAHAN DISINI: Hanya kurangi jika qty diatas 1. Jika qty = 1, gausah ngapa-ngapain.
    if($data['qty'] > 1){
        mysqli_query($koneksi, "UPDATE cart SET qty = qty - 1 WHERE id='$id'");
    }
}

header('Location: cart.php');
exit();
?>