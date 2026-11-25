<?php error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
// Prevent directory listing
header("Location: ../admin.php?id=login");
exit;
