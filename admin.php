<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_rol'] !== 'admin') {
    header('Location: admin_giris.html'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli - Yönetim</title>
     <link rel="stylesheet" href="admin.css">
</head>
<body>
<div>
    <div class="container">
    <h1>🌟 YÖNETİCİ KONTROL PANELİ</h1>
    <p>Merhaba, Admin! Veritabanı ile yönetim yapıyorsunuz.</p>
    
        <div class="menu-yonetim">
            <h3>Rezervasyon Yönetimi</h3>
            <a href="rezervasyon_yonet.php">Rezervasyonları Görüntüle</a> 
            
            <h3>Menü Yönetimi</h3>
            <a href="menu_yonet.php">Menüleri Görüntüle</a> <br><br><br>
            <a href="logout.php">Güvenli Çıkış Yap</a>
        </div>
</div>
</body>
</html>