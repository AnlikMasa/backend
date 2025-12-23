<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['user_rol'] !== 'admin') { 
    header('Location: admin_giris.html'); exit; 
}

$host = 'localhost'; $db_name = 'anlıkmasa'; $username_db = 'root'; $password_db = '';
$kayit_klasoru = __DIR__ . DIRECTORY_SEPARATOR . 'uimages' . DIRECTORY_SEPARATOR;
$web_yolu = 'uimages/'; 


    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT resim FROM menuler WHERE menu_id = ?");
    $stmt->execute([$id]);
    $resim = $stmt->fetchColumn();
    if ($resim && file_exists($kayit_klasoru . $resim)) unlink($kayit_klasoru . $resim);
    $pdo->prepare("DELETE FROM menuler WHERE menu_id = ?")->execute([$id]);
    header('Location: ' . $_SERVER['PHP_SELF']); exit;
}

if (isset($_POST['urunekle']) || isset($_POST['urunguncelle'])) {
    $id = $_POST['menu_id'];
    $ad = $_POST['urun_adi'];
    $fiyat = $_POST['fiyat'];
    $kat = $_POST['kat_id'];
    $aciklama = $_POST['aciklama'];
    $resim_adi = $_POST['eski_resim'];

    if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
        $resim_adi = uniqid() . "." . pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['resim']['tmp_name'], $kayit_klasoru . $resim_adi);
    }

    if (isset($_POST['urunguncelle'])) {
        $pdo->prepare("UPDATE menuler SET kategori_id=?, urun_adi=?, aciklama=?, fiyat=?, resim=? WHERE menu_id=?")
            ->execute([$kat, $ad, $aciklama, $fiyat, $resim_adi, $id]);
    } else {
        $pdo->prepare("INSERT INTO menuler (kategori_id, urun_adi, aciklama, fiyat, resim) VALUES (?,?,?,?,?)")
            ->execute([$kat, $ad, $aciklama, $fiyat, $resim_adi]);
    }
    header('Location: ' . $_SERVER['PHP_SELF']); exit;
}

$guncellenecek = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit') {
    $stmt = $pdo->prepare("SELECT * FROM menuler WHERE menu_id = ?");
    $stmt->execute([$_GET['id']]);
    $guncellenecek = $stmt->fetch();
}

$menuler = $pdo->query("SELECT * FROM menuler ORDER BY menu_id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetim</title>
    <style>
        body { 
            font-family: sans-serif; 
            margin: 0; padding: 20px; 
            background: url("anasayfa.jpeg") no-repeat center center fixed; 
            background-size: cover;
            /* ÖNEMLİ: height: 100% burada ASLA olmamalı */
        }
        .container { 
            width: 850px; margin: auto; padding: 20px;
            background: rgba(255, 255, 255, 0.9); border-radius: 10px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #333; color: #fff; }
        input, textarea { width: 100%; margin-bottom: 10px; padding: 8px; box-sizing: border-box; }
        .btn { padding: 8px 15px; text-decoration: none; color: white; border-radius: 3px; border: none; cursor: pointer; }
        .ekle { background: #28a745; }
        .sil { background: #dc3545; }
        .duzenle { background: #007bff; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin.php">← Geri Dön</a>
    <h2><?= $guncellenecek ? "Ürün Düzenle" : "Yeni Ürün Ekle" ?></h2>
    
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="menu_id" value="<?= $guncellenecek['menu_id'] ?? '' ?>">
        <input type="hidden" name="eski_resim" value="<?= $guncellenecek['resim'] ?? '' ?>">
        
        <input type="number" name="kat_id" placeholder="Kategori ID" value="<?= $guncellenecek['kategori_id'] ?? '' ?>" required>
        <input type="text" name="urun_adi" placeholder="Ürün Adı" value="<?= $guncellenecek['urun_adi'] ?? '' ?>" required>
        <textarea name="aciklama" placeholder="Açıklama" required><?= $guncellenecek['aciklama'] ?? '' ?></textarea>
        <input type="number" step="0.01" name="fiyat" placeholder="Fiyat" value="<?= $guncellenecek['fiyat'] ?? '' ?>" required>
        <input type="file" name="resim">
        
        <button type="submit" name="<?= $guncellenecek ? 'urunguncelle' : 'urunekle' ?>" class="btn ekle">
            <?= $guncellenecek ? "Güncellemeyi Kaydet" : "Ürünü Kaydet" ?>
        </button>
        <?php if($guncellenecek): ?> <a href="?">Vazgeç</a> <?php endif; ?>
    </form>

    <hr>

    <h3>Ürün Listesi</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Ürün</th>
            <th>Fiyat</th>
            <th>Resim</th>
            <th>İşlem</th>
        </tr>
        <?php foreach ($menuler as $m): ?>
        <tr>
            <td><?= $m['menu_id'] ?></td>
            <td><?= htmlspecialchars($m['urun_adi']) ?></td>
            <td><?= $m['fiyat'] ?> TL</td>
            <td><img src="<?= $web_yolu . $m['resim'] ?>" width="50"></td>
            <td>
                <a href="?action=edit&id=<?= $m['menu_id'] ?>" class="btn duzenle">Düzenle</a>
                <a href="?action=delete&id=<?= $m['menu_id'] ?>" class="btn sil" onclick="return confirm('Silinsin mi?')">Sil</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>