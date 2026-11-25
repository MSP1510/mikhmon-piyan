<?php error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
session_start();

unset($_SESSION['billing_portal_customer_id'], $_SESSION['billing_portal_last_login']);
$_SESSION['billing_portal_flash_success'] = 'Anda telah keluar dari portal pelanggan.';

header('Location: billing_login.php');
exit;
