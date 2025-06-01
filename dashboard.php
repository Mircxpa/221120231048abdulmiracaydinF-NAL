<?php
session_start(); 

if (!isset($_SESSION['email'])) {
    header('Location: login.php'); 
    exit;
}

echo "Hoş geldiniz, " . $_SESSION['email'];
?>
<a href="logout.php">Çıkış Yap</a>
