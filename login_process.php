<?php
session_start();

// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "saglikbakanligi");

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Kullanıcının girdiği bilgileri alıyoruz ve boşlukları temizliyoruz
$email = trim($_POST['email']);
$sifre = trim($_POST['sifre']); // `password` yerine `sifre`

// SQL enjeksiyonuna karşı güvenlik önlemi
$email = mysqli_real_escape_string($conn, $email);
$sifre = mysqli_real_escape_string($conn, $sifre);

// Kullanıcıyı kontrol eden SQL sorgusu
$sql = "SELECT * FROM kullanicilar WHERE email = '$email' AND sifre = '$sifre'"; // `password` yerine `sifre` DÜZELTİLDİ
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['email'] = $row['email'];
    $_SESSION['role'] = $row['rol']; // `role` yerine `rol` olması lazım

    if ($_SESSION['role'] == 'doktor') {
        header("Location: doktor_home.php");
    } else if ($_SESSION['role'] == 'eczaci') {
        header("Location: eczaci_home.php");
    }
    exit();
} else {
    echo "Geçersiz e-posta veya şifre!";
}

// Bağlantıyı kapat
$conn->close();
?>
