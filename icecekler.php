<?php
session_start();
$host = 'localhost';
$db_name = 'anlıkmasa'; 
$username_db = 'root'; 
$password_db = '';   

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SADECE İÇECEKLERİ ÇEK (Kategori ID: 2)
    $sql = "SELECT k.kategori_adi, m.urun_adi, m.aciklama, m.fiyat, m.resim 
            FROM `menuler` m 
            LEFT JOIN `menukatagorileri` k ON k.`kategori_id` = m.`kategori_id` 
            WHERE m.kategori_id = 2 
            ORDER BY m.urun_adi ASC";

    $stmt = $pdo->query($sql);
    $urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/tum.css">
    <title>İçecekler</title>
</head>
<body>
    <div class="bg"></div>
    <div class="container">
        <h1 class="site-title">İçecekler Menüsü</h1>
        <div class="gallery">
            <?php if(!empty($urunler)): ?>
                <?php foreach ($urunler as $urun): ?>
                    <div class="card">
                        <?php 
                            // Resim adını temizle ve yolu oluştur
                            $resim_adi = trim($urun['resim']);
                            // Eğer resimlerin 'images' klasöründeyse burayı kullan:
                            $resim_yolu = "uimages/" . $resim_adi; 
                            
                            // Eğer resimler admin/uimages içindeyse üstteki satırı şununla değiştir:
                            // $resim_yolu = "/Public/admin/uimages/" . $resim_adi;
                        ?>
                        <img src="<?= $resim_yolu ?>" alt="<?= htmlspecialchars($urun['urun_adi']) ?>">
                        
                        <div class="overlay">
                            <span><?= htmlspecialchars($urun['urun_adi']) ?></span>
                            <span class="price"><?= number_format($urun['fiyat'], 2, ',', '.') ?> TL</span>
                        </div>
                        <div class="desc"><?= htmlspecialchars($urun['aciklama']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:white; grid-column: 1/-1; text-align:center;">İçecek bulunamadı.</p>
            <?php endif; ?>
        </div>
        <div style="text-align:center; margin-top:20px;">
            <a href="menuler.html" class="back-btn">← Geri Dön</a>
        </div>
    </div>
</body>
</html>