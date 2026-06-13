<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Mencegah SQL Injection
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){
        $data = mysqli_fetch_assoc($result);

        // Verifikasi password hash (mendukung kompatibilitas md5 lama jika migrasi)
        if(password_verify($password, $data['password']) || md5($password) === $data['password']){
            $_SESSION['user'] = $data;
            header('Location: index.php');
            exit();
        } else {
            echo "<script>alert('Password yang Anda masukkan salah!');</script>";
        }
    } else {
        echo "<script>alert('Email tidak terdaftar!');</script>";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Urban Apparel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f4f1eb;">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4 border-0 shadow rounded-4">
                <h3 class="fw-bold mb-4">Urban Apparel</h3>

                <form method="POST">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button class="btn btn-dark w-100" name="login">Login</button>
                </form>

                <a href="register.php" class="mt-3 text-center text-decoration-none text-muted d-block small">Belum punya akun? Daftar disini</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>