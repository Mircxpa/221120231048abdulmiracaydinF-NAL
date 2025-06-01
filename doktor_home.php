<?php
$sql = "SELECT istekler.id, 
                eczacilar.isim AS eczaci_isim, 
                doktorlar.isim AS doktor_isim, 
                istekler.aciklama, 
                istekler.tarih_kayit 
        FROM istekler 
        INNER JOIN eczacilar ON istekler.eczaci_id = eczacilar.id
        INNER JOIN doktorlar ON istekler.doktor_id = doktorlar.id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "Eczacı: " . $row['eczaci_isim'] . "<br>";
        echo "Doktor: " . $row['doktor_isim'] . "<br>";
        echo "Açıklama: " . $row['aciklama'] . "<br>";
        echo "Tarih: " . $row['tarih_kayit'] . "<br>";
        echo "</div><hr>";
    }
} else {
    echo "Hiçbir istek yok.";
}
?>
