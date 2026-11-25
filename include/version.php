<?php error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
  } else {
        $_SESSION["v"] = "3.20 06-30-2021";
    
    }
