<?php
$pdo = new PDO('mysql:host=localhost;dbname=saglikbakanligi', 'root', ''); // Veritabanı bilgilerinizi buraya yazın
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $istek_turu = $_POST['istek_turu'];
    $tarih_baslangic = $_POST['tarih_baslangic'];
    $tarih_bitis = $_POST['tarih_bitis'];
    $doktor_hasta = $_POST['doktor_hasta'];
    $barkod_no = $_POST['barkod_no'];

    $eczaci_id = 1; 

    $sql = "SELECT * FROM istekler WHERE eczaci_id = ?";

    if (!empty($istek_turu)) {
        $sql .= " AND istek_turu = ?";
    }
    if (!empty($tarih_baslangic) && !empty($tarih_bitis)) {
        $sql .= " AND tarih_kayit BETWEEN ? AND ?";
    }
    if (!empty($doktor_hasta)) {
        $sql .= " AND (doktor_id IN (SELECT id FROM doktorlar WHERE isim LIKE ?)
                    OR hasta_id IN (SELECT id FROM hastalar WHERE isim LIKE ?))";
    }
    if (!empty($barkod_no)) {
        $sql .= " AND barkod_no LIKE ?";
    }

    $stmt = $pdo->prepare($sql);
    $params = [$eczaci_id];

    if (!empty($istek_turu)) {
        $params[] = $istek_turu;
    }
    if (!empty($tarih_baslangic) && !empty($tarih_bitis)) {
        $params[] = $tarih_baslangic;
        $params[] = $tarih_bitis;
    }
    if (!empty($doktor_hasta)) {
        $params[] = "%$doktor_hasta%";
        $params[] = "%$doktor_hasta%";
    }
    if (!empty($barkod_no)) {
        $params[] = "%$barkod_no%";
    }

    $stmt->execute($params);

    echo "<table border='1'>
            <tr>
                <th>İstek Türü</th>
                <th>Tarih</th>
                <th>Doktor</th>
                <th>Hasta</th>
                <th>Barkod No</th>
            </tr>";

    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['istek_turu']) . "</td>";
        echo "<td>" . htmlspecialchars($row['tarih_kayit']) . "</td>";

        $doktor_stmt = $pdo->prepare("SELECT isim FROM doktorlar WHERE id = ?");
        $doktor_stmt->execute([$row['doktor_id']]);
        $doktor = $doktor_stmt->fetch();

        $hasta_stmt = $pdo->prepare("SELECT isim FROM hastalar WHERE id = ?");
        $hasta_stmt->execute([$row['hasta_id']]);
        $hasta = $hasta_stmt->fetch();

        echo "<td>" . htmlspecialchars($doktor['isim']) . "</td>";
        echo "<td>" . htmlspecialchars($hasta['isim']) . "</td>";
        echo "<td>" . htmlspecialchars($row['barkod_no']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eczacı Sorgulama</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Eczacı Sorgulama</h2>
    <form action="sorgula.php" method="POST">
        <label for="istek_turu">İstek Türü:</label>
        <select name="istek_turu" id="istek_turu">
            <option value="ilaç">İlaç</option>
            <option value="reçete">Reçete</option>
            <option value="diğer">Diğer</option>
        </select><br><br>

        <label for="tarih_baslangic">Başlangıç Tarihi:</label>
        <input type="date" name="tarih_baslangic" id="tarih_baslangic"><br><br>

        <label for="tarih_bitis">Bitiş Tarihi:</label>
        <input type="date" name="tarih_bitis" id="tarih_bitis"><br><br>

        <label for="doktor_hasta">Doktor/Hasta Adı:</label>
        <input type="text" name="doktor_hasta" id="doktor_hasta"><br><br>

        <label for="barkod_no">Barkod Numarası:</label>
        <input type="text" name="barkod_no" id="barkod_no"><br><br>

        <input type="submit" value="Sorgula">
    </form>

    <div>
        <h3>Sonuçlar:</h3>
        <?php
        ?>
    </div>
</body>
</html>
