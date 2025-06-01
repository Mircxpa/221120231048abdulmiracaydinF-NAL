<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'saglikbakanligi';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$hastabilgileri = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barkod_no'])) {
    $barkod_no = $_POST['barkod_no'];

    $sql = "SELECT hastalar.id AS hasta_id, hastalar.isim AS hasta_isim, hastalar.telefon AS hasta_telefon, 
                   hastalar.barkod_no, doktorlar.id AS doktor_id, doktorlar.isim AS doktor_isim
            FROM hastalar 
            INNER JOIN doktorlar ON hastalar.doktor_id = doktorlar.id 
            WHERE hastalar.barkod_no = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $barkod_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $hastabilgileri = $result->fetch_assoc();
        } else {
            $hastabilgileri = "Bu barkod numarasına ait hasta bulunamadı.";
        }

        $stmt->close();
    } else {
        die("Sorgu hatası: " . $conn->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['istek_gonder']) && isset($hastabilgileri['hasta_id'])) {
    $hasta_id = $hastabilgileri['hasta_id'];
    $doktor_id = $hastabilgileri['doktor_id'];

    $istek_sql = "INSERT INTO istekler (hasta_id, doktor_id, tarih) VALUES (?, ?, NOW())";

    if ($stmt = $conn->prepare($istek_sql)) {
        $stmt->bind_param('ii', $hasta_id, $doktor_id);
        if ($stmt->execute()) {
            $istek_mesaji = "İstek başarıyla gönderildi.";
        } else {
            $istek_mesaji = "İstek gönderilemedi: " . $conn->error;
        }
        $stmt->close();
    } else {
        die("Sorgu hatası: " . $conn->error);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eczacı Sorgulama</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .result {
            margin-top: 30px;
            padding: 15px;
            border-radius: 8px;
            background-color: #f1f1f1;
        }
        .result ul {
            list-style-type: none;
            padding: 0;
        }
        .result li {
            margin: 8px 0;
            font-size: 16px;
        }
        .error-message {
            color: red;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="title">Eczacı Barkod Numarası ile Sorgulama</div>

        <form method="POST" action="">
            <div class="form-group">
                <input type="text" id="barkod_no" name="barkod_no" placeholder="Barkod Numarası" required>
            </div>
            <button class="submit-btn" type="submit">Sorgula</button>
        </form>

        <?php if ($hastabilgileri != null): ?>
            <?php if (is_array($hastabilgileri)): ?>
                <div class="result">
                    <h3>Hasta Bilgileri:</h3>
                    <ul>
                        <li><strong>İsim:</strong> <?php echo $hastabilgileri['hasta_isim']; ?></li>
                        <li><strong>Barkod No:</strong> <?php echo $hastabilgileri['barkod_no']; ?></li>
                        <li><strong>Telefon:</strong> <?php echo $hastabilgileri['hasta_telefon']; ?></li>
                        <li><strong>Doktor:</strong> <?php echo $hastabilgileri['doktor_isim']; ?></li>
                    </ul>
                    <form method="POST" action="">
                        <button class="submit-btn" type="submit" name="istek_gonder">Doktora İstek Gönder</button>
                    </form>
                </div>
                <?php if (isset($istek_mesaji)): ?>
                    <p class="error-message"><?php echo $istek_mesaji; ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="error-message"><?php echo $hastabilgileri; ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>
