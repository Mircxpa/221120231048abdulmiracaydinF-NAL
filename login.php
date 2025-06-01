<?php
// Veritabanı bağlantısını dahil et
include('config.php');
session_start();

// Giriş işlemi
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $sifre = trim($_POST['password']); // `password` yerine `sifre` kullanıyoruz

    // SQL enjeksiyonuna karşı koruma
    $email = mysqli_real_escape_string($conn, $email);
    $sifre = mysqli_real_escape_string($conn, $sifre);

    // Kullanıcıyı veritabanında kontrol et
    $sql = "SELECT * FROM kullanicilar WHERE email = '$email' AND sifre = '$sifre' AND (rol = 'doktor' OR rol = 'eczaci')";
    $result = $conn->query($sql);

    // Eğer kullanıcı varsa yönlendir
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['rol']; // `role` yerine `rol`

        // Kullanıcı rolüne göre yönlendirme
        if ($_SESSION['role'] == 'doktor') {
            header("Location: doktor_home.php");
        } elseif ($_SESSION['role'] == 'eczaci') {
            header("Location: eczaci_home.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        // Hata mesajı
        $error_message = "Geçersiz email veya şifre!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sağlık Bakanlığı - Giriş Yap</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <img src="https://upload.wikimedia.org/wikipedia/tr/4/41/Sa%C4%9Fl%C4%B1k_Bakanl%C4%B1%C4%9F%C4%B1_bayra%C4%9F%C4%B1.png" alt="Sağlık Bakanlığı Logo" class="logo">
        <h2>Giriş Yap</h2>

        <?php
        // Hata mesajı göster
        if (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>

        <form method="POST">
            <div class="input-group">
                <label for="email">E-posta:</label>
                <input type="email" id="email" name="email" placeholder="E-posta adresinizi girin" required>
            </div>

            <div class="input-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" placeholder="Şifrenizi girin" required>
            </div>

            <button type="submit" name="login" class="login-btn">Giriş Yap</button>
        </form>
    </div>
</div>

</body>
</html>
