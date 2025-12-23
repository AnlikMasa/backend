<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$db_name = "anlıkmasa"; 
$message = ""; 
$pdo = null;

if (isset($_POST['rez_yap'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
        
        $ad_soyad = htmlspecialchars(trim($_POST['ad_soyad'] ?? ''));
        $eposta = htmlspecialchars(trim($_POST['eposta'] ?? ''));
        $telefon = htmlspecialchars(trim($_POST['telefon'] ?? '')); 
        $kisi_sayisi = (int)($_POST['kisi_sayisi'] ?? 0);
        $rezervasyon_tarihi_str = htmlspecialchars(trim($_POST['rezervasyon_tarihi'] ?? ''));
        $durum = 'Onay Bekliyor'; 

        if (empty($ad_soyad) || empty($eposta) || empty($telefon) || empty($rezervasyon_tarihi_str) || $kisi_sayisi <= 0) {
            $message = "<div class='alert error'>Hata: Tüm zorunlu alanları doğru doldurmalısınız.</div>";
        } 
        
        else {
           
                $pdo = new PDO("mysql:host=$servername;dbname=$db_name;charset=utf8mb4", $username_db, $password_db);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = "INSERT INTO rezervasyonlar (ad_soyad, eposta, telefon, kisi_sayisi, rezervasyon_tarihi, durum) 
                        VALUES (:ad_soyad, :eposta, :telefon, :kisi_sayisi, :rezervasyon_tarihi, :durum)";
                
                $stmt = $pdo->prepare($sql);
            
                $stmt->bindParam(':ad_soyad', $ad_soyad);
                $stmt->bindParam(':eposta', $eposta);
                $stmt->bindParam(':telefon', $telefon);
                $stmt->bindParam(':kisi_sayisi', $kisi_sayisi, PDO::PARAM_INT);
                $stmt->bindParam(':rezervasyon_tarihi', $rezervasyon_tarihi_str);
                $stmt->bindParam(':durum', $durum);

                if ($stmt->execute()) {
                   
                    $message = "<div class='alert success'>✅ Rezervasyonunuz başarıyla alınmıştır. </div>";
                    
                    $_POST = array(); 

                } else {
                    $message = "<div class='alert error'>Hata: Kayıt işlemi başarısız oldu. Lütfen tekrar deneyin.</div>";
                }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Rezervasyon Yap</title>
    <style>
    html {
        height: 100%;
        min-height: 100%;
        margin: 0;
        padding: 0;
        background-image: url("e.jpg"); 
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    body {
        min-height: 100vh; 
        margin: 0;
        padding: 0;
        display: flex; 
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .gecisler {
        font-family: 'Times New Roman', Times, serif;
        display: flex;
        flex-direction: row;
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(5px);
        position: fixed; 
        top: 0;
        width: 100%;
        box-sizing: border-box;
        z-index: 1000; 
        gap: 25px;
        align-items: center;
        justify-content: flex-end;
        padding: 20px 20px 20px 0;
    }

    .gecisler .logo {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: auto;
    }

    .gecisler .logo img {
        display: block;
        width: 120px;
        max-width: 12vw;
        height: auto;
    }

   .gecisler a {
  color: 	#D7CCC8;
  font-weight: 500;
  font-size: 120%;
  padding: 5px 0;
  border-bottom: none;
  transition: color 0.3s, border-color 0.3s;
  text-decoration: none;
}

.gecisler a:hover {
  color: #ffd08a;
  border-bottom: none;
}

    .container {
        width: 350px;
        background-color: rgba(255, 255, 255, 0.9); 
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        margin-top: 100px; 
        margin-bottom: 40px;
        position: relative; 
        z-index: 100; /
    }

    .container h2 {
        text-align: center;
        color: #454b35;
        margin-top: 0;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #495057;
        margin-top: 15px;
    }

    input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="datetime-local"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
        background-color: rgba(255, 255, 255, 0.8);
        transition: border-color 0.3s, background-color 0.3s;
    }
    
    input:hover, input:focus { 
        border-color: #aeaba7;
        background-color: rgba(200, 170, 135, 0.15);
        outline: none; 
    }
    
    button {
        width: 100%; 
        padding: 12px; 
        margin-top: 25px;
        background-color: #87a728;
        color: white; 
        border: none; 
        border-radius: 5px; 
        cursor: pointer; 
        font-size: 17px; 
        font-weight: bold;
    }
    
    button:hover { 
        background-color: #216c88;
    }

  
    .alert { 
        padding: 12px; 
        border-radius: 6px; 
        margin-bottom: 20px; 
        font-weight: bold; 
        text-align: center; 
        z-index: 101;
        position: relative;
    }
    .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; } 
    .site-altbilgi {
  
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  z-index: 1000; 
  text-align: center;
  background: rgba(0, 0, 0, 0.4); 
  color: white;
  padding: 15px 0; 
  font-size: 0.9rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(5px); 
}
</style>
</head>
<body>

  <nav class="gecisler">
        <nav class="logo">
    <a href="anasayfa.html"> <img src="logo3.6.png"></a>
        </nav>
    <a href="anasayfa.html">Anasayfa</a>
    <a href="">Menü</a>
    <a href="rezervasyon.HTML">Rezervasyon</a>
    <a href="main.html">İletişim</a>
    <a href="hakkimizda.html">Hakkımızda</a>
    </nav>

    <div class="container">
        <h2>🍽️ Hemen Rezervasyon Yapın</h2>

        <?php 
       
        echo $message; 
        ?>
        
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">

            <div>
                <label for="ad_soyad">Ad Soyad:</label>
                <input type="text" id="ad_soyad" name="ad_soyad" required value="<?= htmlspecialchars($_POST['ad_soyad'] ?? '') ?>">
            </div>
            <div>
                <label for="eposta">E-posta:</label>
                <input type="email" id="eposta" name="eposta" required value="<?= htmlspecialchars($_POST['eposta'] ?? '') ?>">
            </div>
            <div>
                <label for="telefon">Telefon:</label>
                <input type="tel" id="telefon" name="telefon" required value="<?= htmlspecialchars($_POST['telefon'] ?? '') ?>">
            </div>
            <div>
                <label for="kisi_sayisi">Kişi Sayısı:</label>
                <input type="number" id="kisi_sayisi" name="kisi_sayisi" required min="1" value="<?= htmlspecialchars($_POST['kisi_sayisi'] ?? '') ?>">
            </div>
            <div>
                <label for="rezervasyon_tarihi">Rezervasyon Tarihi ve Saati:</label>
                <input type="datetime-local" id="rezervasyon_tarihi" name="rezervasyon_tarihi" required value="<?= htmlspecialchars($_POST['rezervasyon_tarihi'] ?? '') ?>">
            </div>

            <div>
                <button id="reserveBtn" type="submit" name="rez_yap">
                    Rezervasyon Yap (+1 Masa Doldur)
                </button>
            </div>
        </form>
    </div>
<footer class="site-altbilgi">
    <p>© 2025 Carpe Diem | Tum haklari saklidir.</p>
</footer>
</body>
</html>