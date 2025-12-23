<?php
session_start();
$host = 'localhost';
$db_name = 'anlıkmasa';
$username_db = 'root';
$password_db = '';
$pdo = null;
$hata_mesaji = '';
$basari_mesaji = '';
$guncellenecek_rezervasyon = null; 


    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (isset($_POST['guncelle_durum']) && $pdo) {
    
    $rezervasyon_id = $_POST['rezervasyon_id'] ?? 0;
    $yeni_durum = trim($_POST['durum'] ?? ''); 

    if (!in_array($yeni_durum, ['Onay Bekliyor', 'Onaylandı', 'Reddedildi'])) {
        $hata_mesaji = "Geçersiz durum değeri.";
    } elseif (!is_numeric($rezervasyon_id) || $rezervasyon_id <= 0) {
         $hata_mesaji = "Geçersiz Rezervasyon ID.";
    }
    
    if (!$hata_mesaji) {
            $stmt = $pdo->prepare("UPDATE rezervasyonlar SET durum=? WHERE rezervasyon_id=?");
            $stmt->execute([$yeni_durum, $rezervasyon_id]);
            
            if ($yeni_durum === 'Onaylandı') {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?status=updated&id=' . $rezervasyon_id . '&alert=onaylandi');
            } else {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?status=updated&id=' . $rezervasyon_id);
            }
            exit;
        
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'edit' && $pdo) {
    $edit_id = $_GET['id'] ?? 0;
    if ($edit_id) {
            $stmt = $pdo->prepare("SELECT * FROM rezervasyonlar WHERE rezervasyon_id = ?");
            $stmt->execute([$edit_id]);
            $guncellenecek_rezervasyon = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$guncellenecek_rezervasyon) {
                $hata_mesaji = "Güncellenecek rezervasyon bulunamadı.";
            }
    }
}

if (isset($_POST['rezervasyonguncelle']) && $pdo) {
    
    $rezervasyon_id = $_POST['rezervasyon_id'] ?? 0;
    $ad_soyad = trim($_POST['ad_soyad'] ?? '');
    $eposta = trim($_POST['eposta'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $kisi_sayisi = trim($_POST['kisi_sayisi'] ?? 0);
    $rezervasyon_tarihi = trim($_POST['rezervasyon_tarihi'] ?? ''); 
    $durum = trim($_POST['durum'] ?? ''); 

    if (!$hata_mesaji) {
            $stmt = $pdo->prepare("
                UPDATE rezervasyonlar 
                SET ad_soyad=?, eposta=?, telefon=?, kisi_sayisi=?, rezervasyon_tarihi=?, durum=? 
                WHERE rezervasyon_id=?
            ");
            $stmt->execute([$ad_soyad, $eposta, $telefon, $kisi_sayisi, $rezervasyon_tarihi, $durum, $rezervasyon_id]);
            
            header('Location: ' . $_SERVER['PHP_SELF'] . '?status=updated');
            exit;

    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && $pdo) {
    $rezervasyon_id = $_GET['id'] ?? 0;
    if ($rezervasyon_id) {
            $stmt_sil = $pdo->prepare("DELETE FROM rezervasyonlar WHERE rezervasyon_id = ?");
            $stmt_sil->execute([$rezervasyon_id]);

            if ($stmt_sil->rowCount()) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?status=deleted');
                exit;
            }
    }
}

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'updated') { 
        $id = $_GET['id'] ?? 'Bilinmeyen';
        $basari_mesaji = "✏️ Rezervasyon (ID: $id) başarıyla güncellendi."; 
    }
    if ($_GET['status'] === 'deleted') { $basari_mesaji = "🗑️ Rezervasyon başarıyla silindi."; }
}

$rezervasyonlar = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM rezervasyonlar ORDER BY FIELD(durum, 'Onay Bekliyor') DESC, rezervasyon_tarihi ASC");
        $rezervasyonlar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $hata_mesaji = "Listeleme hatası: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Rezervasyon Yönetim Paneli</title>
    <style>
        html {
            height: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
            background-image: url("anasayfa.jpeg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            opacity: 0.9;
        }
        body {
            position: relative; 
            height: 100%;
            min-height: 100vh; 
            margin: 0;
            padding: 0;
        }
        .mesaj { 
            padding: 10px; 
            margin-bottom: 15px; 
            border-radius: 5px; 
            font-weight: bold; 
            width: 900px; 
            max-width: 95%;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
        }
        .basari { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .hata { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-container { 
            background: white; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 8px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
            width: 400px; 
            max-width: 90%;
        }
        .guncelle-modunda { border: 2px solid #007bff; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.05);}
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: middle; }
        th { background-color: #343a40; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #e9ecef; }

        /* Pop-up form stili yz */
        .guncelle-formu {
            background: white; border: 1px solid #ccc; padding: 15px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            position: absolute; z-index: 1000; display: none; margin-top: 5px; 
            min-width: 200px; 
        }
        .guncelle-formu select {
             padding: 8px; border: 1px solid #ced4da; border-radius: 4px; width: 100%;
        }
        .btn-guncelle-pop { 
            padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s; 
        }
        .btn-guncelle-pop:hover { background-color: #0056b3; }
        .btn-detayli-duzenle { 
            color: white; 
            background-color: #ffc107; 
            text-decoration: none; 
            padding: 8px 12px; 
            border-radius: 4px; 
            display: inline-block; 
            margin-left: 5px;
        }

        /* Diğer Butonlar ve Kontroller */
        .btn-sil { 
            color: white; background-color: #dc3545; text-decoration: none; padding: 8px 12px; border-radius: 4px; display: inline-block; margin-left: 5px;
        }
        input[type="submit"], input[type="button"] {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }
        .onaybekliyor { color: #4350b1ff; font-weight: bold; } 
        .onaylandi { color: #28a745; font-weight: bold; } 
        .reddedildi { color: #dc3545; font-weight: bold; } 

        /* CONTAINER CSS GÜNCELLEMESİ (İstenen opaklık geri getirildi) */
        .container {
            width: 900px;
            max-width: 95%;
            /* OPASİTE DÜZENLENDİ */
            background-color: rgba(255, 255, 255, 0.65); 
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);

            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin-top: 50px; 
            margin-bottom: 50px;
        }

    </style>
</head>
<body>

    <?php 
    if ($basari_mesaji): ?><div class="mesaj basari"><?= htmlspecialchars($basari_mesaji) ?></div><?php endif; ?>
    <?php if ($hata_mesaji): ?><div class="mesaj hata"><?= htmlspecialchars($hata_mesaji) ?></div><?php endif; ?>

    <?php
    $submit_name = "rezervasyonguncelle";
    $submit_value = "Kaydet ve Güncelle";
    $form_class = 'form-container';
    
    $rez_data = $guncellenecek_rezervasyon ?: [
        'rezervasyon_id' => '', 
        'ad_soyad' => '', 
        'eposta' => '', 
        'telefon' => '', 
        'kisi_sayisi' => '', 
        'rezervasyon_tarihi' => '', 
        'durum' => 'Onay Bekliyor'
    ];
    
    $display_datetime = '';
    if (!empty($rez_data['rezervasyon_tarihi'])) {
        $display_datetime = date('Y-m-d\TH:i', strtotime($rez_data['rezervasyon_tarihi']));
    }
    
    if ($guncellenecek_rezervasyon):
    ?>

    <center>
        <div class="<?= $form_class ?> guncelle-modunda">
            <h2><?= $form_baslik ?> (ID: <?= htmlspecialchars($rez_data['rezervasyon_id']) ?>)</h2>
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                
                <input type="hidden" name="rezervasyon_id" value="<?= htmlspecialchars($rez_data['rezervasyon_id']) ?>">

                Ad Soyad:<br><input type="text" name="ad_soyad" value="<?= htmlspecialchars($rez_data['ad_soyad']) ?>" required><br>
                E-posta:<br><input type="text" name="eposta" value="<?= htmlspecialchars($rez_data['eposta']) ?>" required><br> 
                Telefon:<br><input type="text" name="telefon" value="<?= htmlspecialchars($rez_data['telefon']) ?>" required><br> 
                Kişi Sayısı:<br><input type="number" name="kisi_sayisi" value="<?= htmlspecialchars($rez_data['kisi_sayisi']) ?>" required><br> 
                
                Rez. Tarihi/Saati:<br><input type="datetime-local" name="rezervasyon_tarihi" value="<?= htmlspecialchars($display_datetime) ?>" required><br> 
                
                **Durum Güncelle:**<br>
                <select name="durum" required>
                    <option value="Onay Bekliyor" <?= ($rez_data['durum'] == 'Onay Bekliyor' ? 'selected' : '') ?>>Onay Bekliyor</option>
                    <option value="Onaylandı" <?= ($rez_data['durum'] == 'Onaylandı' ? 'selected' : '') ?>>Onaylandı</option>
                    <option value="Reddedildi" <?= ($rez_data['durum'] == 'Reddedildi' ? 'selected' : '') ?>>Reddedildi</option>
                </select><br><br>

                <input type="submit" name="<?= $submit_name ?>" value="<?= $submit_value ?>">
                
                <a href="rezervasyon_yonet.php" style="text-decoration: none; margin-left: 10px;">
                    <input type="button" value="İptal Et">
                </a>

            </form>
        </div>
    </center>
    <?php endif; ?>

    
<div class="container">
    <a href="admin.php" style="text-decoration: none; font-weight: bold; color: #007bff;">← Ana Panele Dön</a>
    <h1 >📅 Rezervasyonlar Listesi</h1>
    
    <?php if (count($rezervasyonlar) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad Soyad</th>
                <th>E-posta</th>
                <th>Telefon</th>
                <th>Kişi</th>
                <th>Rez. Tarihi</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rezervasyonlar as $rez): ?>
            <tr>
                <td><?= htmlspecialchars($rez['rezervasyon_id']) ?></td>
                <td><?= htmlspecialchars($rez['ad_soyad']) ?></td>
                <td><?= htmlspecialchars($rez['eposta']) ?></td>
                <td><?= htmlspecialchars($rez['telefon']) ?></td>
                <td><?= htmlspecialchars($rez['kisi_sayisi']) ?></td>
                <td><?= htmlspecialchars($rez['rezervasyon_tarihi']) ?></td>
                
                <td class="<?= strtolower(str_replace(' ', '', $rez['durum'])) ?>">
                    <?= htmlspecialchars($rez['durum']) ?>
                </td>
                
                <td style="position: relative;">
                    <button type="button" class="btn-guncelle-pop" onclick="toggleForm(<?= $rez['rezervasyon_id'] ?>)">
                        Durum Güncelle
                    </button>
                    
                    
                    <a href="?action=delete&id=<?= htmlspecialchars($rez['rezervasyon_id']) ?>" class="btn-sil"
                       onclick="return confirm('Bu rezervasyonu silmek istediğinizden emin misiniz?');">Sil</a>

                    <div id="form-<?= $rez['rezervasyon_id'] ?>" class="guncelle-formu">
                        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                             <input type="hidden" name="rezervasyon_id" value="<?= htmlspecialchars($rez['rezervasyon_id']) ?>">
                             **Yeni Durum (ID: <?= $rez['rezervasyon_id'] ?>):**<br>
                             <select name="durum" required style="width: 100%; margin-top: 5px; margin-bottom: 10px;">
                                 <option value="Onay Bekliyor" <?= ($rez['durum'] == 'Onay Bekliyor' ? 'selected' : '') ?>>Onay Bekliyor</option>
                                 <option value="Onaylandı" <?= ($rez['durum'] == 'Onaylandı' ? 'selected' : '') ?>>Onaylandı</option>
                                 <option value="Reddedildi" <?= ($rez['durum'] == 'Reddedildi' ? 'selected' : '') ?>>Reddedildi</option>
                             </select>
                             <button type="submit" name="guncelle_durum" class="btn-guncelle-pop" style="width: 100%;">Kaydet</button>
                         </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>Henüz kayıtlı rezervasyon bulunmamaktadır.</p>
    <?php endif; ?>
</div>

<script>
  
    function toggleForm(id) {
        const form = document.getElementById('form-' + id);
        
        // Eğer form açıksa kapat, kapalıysa aç
        if (form.style.display === 'block') {
            form.style.display = 'none';
        } else {
            // Diğer tüm pop-up formları kapat
            document.querySelectorAll('.guncelle-formu').forEach(f => {
                if (f.id !== 'form-' + id) {
                    f.style.display = 'none';
                }
            });
            
            // Tıklanan pop-up'ı aç
            form.style.display = 'block';
            
            // Konumlandırma
            const button = event.target;
            form.style.top = (button.offsetHeight + 5) + 'px'; 
            form.style.left = '0';
            form.style.position = 'absolute'; 
        }
    } 

    // ALERT KONTROLÜ
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('alert') && urlParams.get('alert') === 'onaylandi') {
        const rezId = urlParams.get('id') || '';
        alert("🔔 Rezervasyon (ID: " + rezId + ") başarıyla ONAYLANDI! Kullanıcıya bildirim gönderilebilir.");
        
        // Alert gösterildikten sonra URL'deki alert parametresini temizle
        const cleanSearch = window.location.search.replace(/&alert=onaylandi/g, '');
        window.history.replaceState({}, document.title, window.location.pathname + cleanSearch);
    }
</script>
</body>
</html>