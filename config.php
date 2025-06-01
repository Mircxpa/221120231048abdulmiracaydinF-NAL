<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "saglikbakanligi";        

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı başarısız oldu: " . $conn->connect_error);
} else {
   echo "Bağlantı başarılı!";
}
?>
