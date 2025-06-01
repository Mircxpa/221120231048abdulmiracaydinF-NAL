<?php
// Veritabanı bağlantısını dahil ediyoruz
include('config.php');

// Oturum kontrolü: Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendir
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

echo "Hoşgeldiniz, " . $_SESSION['user_name'] . "! <br> Başarılı bir şekilde giriş yaptınız.";
?>
