-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Bulan Mei 2026 pada 04.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pbwfashion`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `ukuran` varchar(20) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_harga` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Menunggu Pembayaran'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_harga`, `created_at`, `status`) VALUES
(1, 1, 260000, '2026-05-10 07:04:39', 'Menunggu Pembayaran'),
(2, 1, 430000, '2026-05-10 07:08:50', 'Menunggu Pembayaran'),
(3, 1, 130000, '2026-05-10 07:15:17', 'Menunggu Pembayaran'),
(4, 1, 560000, '2026-05-10 07:15:48', 'Menunggu Pembayaran'),
(5, 2, 260000, '2026-05-10 07:17:33', 'Menunggu Pembayaran'),
(6, 1, 260000, '2026-05-10 07:20:38', 'Menunggu Pembayaran'),
(7, 1, 260000, '2026-05-10 07:20:56', 'Menunggu Pembayaran'),
(8, 1, 0, '2026-05-10 07:21:01', 'Menunggu Pembayaran'),
(9, 1, 260000, '2026-05-10 07:21:22', 'Menunggu Pembayaran'),
(10, 1, 260000, '2026-05-10 07:21:34', 'Menunggu Pembayaran'),
(11, 1, 260000, '2026-05-10 07:21:46', 'Menunggu Pembayaran'),
(12, 2, 260000, '2026-05-10 07:30:10', 'Menunggu Pembayaran'),
(13, 2, 0, '2026-05-10 07:30:24', 'Menunggu Pembayaran'),
(14, 1, 130000, '2026-05-10 08:07:49', 'Menunggu Pembayaran'),
(15, 1, 1400000, '2026-05-10 08:11:58', 'Menunggu Pembayaran'),
(16, 1, 280000, '2026-05-10 08:14:02', 'Menunggu Pembayaran'),
(17, 1, 130000, '2026-05-19 15:14:24', 'Menunggu Pembayaran'),
(18, 1, 260000, '2026-05-19 15:38:17', 'Menunggu Pembayaran'),
(19, 1, 130000, '2026-05-19 16:15:25', 'Dibatalkan'),
(20, 1, 280000, '2026-05-19 16:20:34', 'Dibatalkan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `produk_id`, `qty`, `harga`) VALUES
(1, 1, 8, 1, 260000),
(2, 2, 3, 1, 280000),
(3, 2, 4, 1, 150000),
(4, 3, 7, 1, 130000),
(5, 4, 8, 1, 260000),
(6, 4, 6, 1, 300000),
(7, 5, 8, 1, 260000),
(8, 6, 8, 1, 260000),
(9, 7, 8, 1, 260000),
(10, 8, 8, 0, 260000),
(11, 9, 8, 1, 260000),
(12, 10, 8, 1, 260000),
(13, 11, 8, 1, 260000),
(14, 12, 8, 1, 260000),
(15, 13, 8, 0, 260000),
(16, 14, 7, 1, 130000),
(17, 15, 3, 5, 280000),
(18, 16, 3, 1, 280000),
(19, 17, 7, 1, 130000),
(20, 18, 7, 2, 130000),
(21, 19, 7, 1, 130000),
(22, 20, 3, 1, 280000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `ukuran` varchar(20) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `sku`, `nama_produk`, `kategori`, `deskripsi`, `ukuran`, `harga`, `stok`, `foto`) VALUES
(1, 'JCK001', 'Clara Jacket', 'Outerwear', 'Jaket urban premium dengan style modern streetwear.', 'XL', 350000, 8, '1776740782_Clara Jacket_updated.jpeg'),
(2, 'KMJ001', 'Flannel Shirt', 'Kemeja', 'Kemeja flannel casual dengan bahan nyaman.', 'L', 220000, 12, '1776741079_Flannel Shirt_updated.jpg'),
(3, 'CLN001', 'Barrel Pants', 'Celana', 'Celana barrel fit dengan model relaxed modern.', 'L', 280000, 9, '1776741094_Barrel Pants_updated.jpg'),
(4, 'TS001', 'Basic Oversize T-Shirt', 'Kaos', 'Kaos oversize basic premium cotton.', 'XL', 150000, 14, '1776741109_Basic Oversize T-Shirt_updated.jpg'),
(5, 'ACC001', 'Urban Cap', 'Aksesoris', 'Topi fashion streetwear urban edition.', 'All Size', 90000, 20, '1776743993_335.jpg'),
(6, 'OUT002', 'Street Hoodie', 'Outerwear', 'Hoodie casual dengan desain clean aesthetic.', 'XL', 300000, 9, '1776744519_951.jpg'),
(7, 'TS002', 'Minimal Tee', 'Kaos', 'Kaos minimalis dengan cutting modern.', 'M', 130000, 13, '1776744994_update.jpg'),
(8, 'CLN002', 'Relax Cargo', 'Celana', 'Celana cargo relaxed fit cocok untuk daily wear.', 'L', 260000, 4, '1776745338_959.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `komentar` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ulasan`
--

INSERT INTO `ulasan` (`id`, `produk_id`, `user_id`, `nama_user`, `rating`, `komentar`, `created_at`) VALUES
(1, 6, 1, 'Akmal Fauzan', 5, 'keren', '2026-05-19 15:48:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`) VALUES
(1, 'Akmal Fauzan', 'gndtakmal@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b'),
(2, 'Althaf', 'althaf@gmail.com', '202cb962ac59075b964b07152d234b70');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
