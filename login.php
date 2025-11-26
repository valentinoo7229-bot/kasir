<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

 $error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'database/koneksi.php';

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT UserID, Username, Password, NamaLengkap, Role FROM kasir_user WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['nama_lengkap'] = $user['NamaLengkap'];
            $_SESSION['role'] = $user['Role'];
            session_regenerate_id(true);
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan!';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kopi Kenangan Senja</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: url('https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1770&q=80') no-repeat center center; background-size: cover; }
        .login-wrapper { width: 420px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 20px; padding: 40px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2); color: #fff; }
        .login-wrapper h1 { font-size: 2.5em; text-align: center; margin-bottom: 10px; }
        .login-wrapper .logo-area { text-align: center; margin-bottom: 20px; }
        .login-wrapper .logo-area img { height: 70px; width: auto; margin-bottom: 10px; }
        .login-wrapper .tagline { font-style: italic; font-size: 0.9em; text-align: center; margin-bottom: 25px; }
        .input-box { position: relative; width: 100%; height: 50px; margin: 25px 0; }
        .input-box input { width: 100%; height: 100%; background: transparent; border: 2px solid rgba(255, 255, 255, 0.3); border-radius: 40px; font-size: 1em; color: #fff; padding: 0 20px 0 45px; outline: none; transition: all 0.3s; }
        .input-box input::placeholder { color: rgba(255, 255, 255, 0.7); }
        .input-box input:focus { border-color: #fff; background: rgba(255, 255, 255, 0.1); }
        .input-box i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); font-size: 1.2em; }
        .btn { width: 100%; height: 45px; background: #fff; border: none; border-radius: 40px; cursor: pointer; font-size: 1em; font-weight: 600; color: #5D4037; transition: all 0.3s; }
        .btn:hover { background: #f0f0f0; transform: translateY(-2px); }
        .alert-error { background-color: rgba(244, 67, 54, 0.8); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="logo-area">
            <img src="assets/logo.png" alt="Logo Kopi Kenangan Senja">
            <h1>Kopi Kenangan Senja</h1>
            <p class="tagline">Setiap Tegukan Adalah Cerita</p>
        </div>
        <form action="login.php" method="POST">
            <?php if ($error): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" class="btn">Masuk</button>
        </form>
    </div>
</body>
</html>