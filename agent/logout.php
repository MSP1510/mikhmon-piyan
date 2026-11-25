<?php error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
session_start();
session_destroy();
header("Location: index.php");
exit();
?>