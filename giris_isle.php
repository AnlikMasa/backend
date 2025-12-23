<?php
session_start();

$host = 'localhost'; 
$db_name = 'anlıkmasa'; 
$username_db = 'root'; 
$password_db = ''; 
$error_message = '';

    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (isset($_POST['girisyap'])) {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = "Lütfen kullanıcı adı ve şifrenizi girin.";
    } else {   
        $sql = "SELECT admin_sifre FROM Admin WHERE admin_adi = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $db_password = $user['admin_sifre'];
            
            if ($password === $db_password) {
                $_SESSION['user_rol'] = 'admin';
                $_SESSION['logged_in'] = true; 
                
                header("Location: admin.php"); 
                exit; 
            } else {
               
                $error_message = "Kullanıcı adı veya şifre yanlış.";
            }
        } else {
            $error_message = "Kullanıcı adı veya şifre yanlış.";
        }
    }
}
?>