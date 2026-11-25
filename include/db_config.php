<?php
define("DB_HOST", "127.0.0.1");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "mikhmon_piyan");
define("DB_CHARSET", "utf8mb4");

function getDBConnection() {
    static $conn = null;
    if ($conn !== null) return $conn;
    try {
        $pdo = new PDO("mysql:host=".DB_HOST.";charset=".DB_CHARSET, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `".DB_NAME."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        $chk = $conn->query("SHOW TABLES LIKE 'billing_customers'")->fetchAll();
        if (empty($chk)) {
            $root = dirname(__DIR__);
            if (!is_dir($root."/database")) $root = dirname($root);
            $sqls = ["database/complete_installation.sql","database/agent_system.sql","database/billing_module.sql","database/payment_gateway_tables.sql","database/public_sales_schema.sql","database/telegram_integration.sql","database/safe_fix_tables.sql"];
            foreach ($sqls as $sf) {
                if (file_exists($root."/".$sf)) {
                    try { $conn->exec(file_get_contents($root."/".$sf)); } catch (Exception $e) {}
                }
            }
        }
        return $conn;
    } catch (PDOException $e) {
        return false;
    }
}
