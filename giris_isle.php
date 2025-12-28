<?php
session_start();

$host = 'localhost'; 
$db_name = 'anlıkmasa'; 
$username_db = 'root'; 
$password_db = ''; 
$error_message = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}

if (isset($_SESSION['deneme_sayisi']) && $_SESSION['deneme_sayisi'] >= 2) {
    $error_message = "❌ 2 kez hatalı giriş yaptınız. Güvenlik nedeniyle girişiniz engellendi!";
} 
else if (isset($_POST['girisyap'])) {
    
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error_message = "Lütfen kullanıcı adı ve şifrenizi girin.";
    } else {   
        $sql = "SELECT admin_sifre FROM Admin WHERE admin_adi = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['admin_sifre'])) {
          
                $_SESSION['deneme_sayisi'] = 0;
                $_SESSION['user_rol'] = 'admin';
                $_SESSION['logged_in'] = true; 
                
                header("Location: admin.php"); 
                exit; 
            } else {
                $_SESSION['deneme_sayisi'] = ($_SESSION['deneme_sayisi'] ?? 0) + 1;
                $kalan = 2 - $_SESSION['deneme_sayisi'];
                $error_message = "Hatalı şifre! Kalan deneme hakkınız: " . $kalan;
            }
        } else {
            $_SESSION['deneme_sayisi'] = ($_SESSION['deneme_sayisi'] ?? 0) + 1;
            $error_message = "Kullanıcı adı veya şifre yanlış.";
        }
    }
}
?>

<?php if($error_message): ?>
    <div style="color: red; font-weight: bold; text-align: center; margin-top: 50px; background: #fff5f5; padding: 15px; border: 1px solid red; display: inline-block; width: 100%;">
        ⚠️ <?php echo $error_message; ?>
    </div>
<?php endif; ?>
