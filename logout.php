<?php
session_start();
$_SESSION = array();
session_destroy();
header('Location: adminanasayfa.html');
exit;
?>