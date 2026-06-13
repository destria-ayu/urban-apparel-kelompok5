<?php
include 'koneksi.php';

if(isset($_POST['register'])){
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    
    // Menggunakan password_hash (Standar keamanan industri modern)
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Mencegah SQL Injection dengan Prepared Statements
    $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $password);
    
    if(mysqli_stmt_execute($stmt)){
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Registrasi gagal! Email mungkin sudah terdaftar.');</script>";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Urban Apparel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f4f1eb;">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow rounded-4">
                <h3 class="fw-bold mb-4">Register</h3>

                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button class="btn btn-dark w-100" name="register">Register</button>
                </form>

                <a href="login.php" class="mt-3 text-center text-decoration-none text-muted d-block small">Sudah punya akun? Login disini</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>