<?php
$gercek_sifre = ""; 

$guvenli_hash = password_hash($gercek_sifre, PASSWORD_DEFAULT);

echo "<h2>Şifre Başarıyla Hashlendi!</h2>";
echo "<b>Senin Şifren:</b> " . $gercek_sifre . "<br><br>";
echo "<b>Veritabanına Yapıştıracağın Kod (Hash):</b><br>";
echo $guvenli_hash;
echo "</div>";
echo "<br><small>Not: Bu kodu kopyalayıp veritabanındaki admin_sifre sütununa yapıştırın.</small>";
?>