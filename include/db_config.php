<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
if (!defined("DB_HOST")) define("DB_HOST", "localhost");
if (!defined("DB_USER")) define("DB_USER", "root");
if (!defined("DB_PASS")) define("DB_PASS", "");
if (!defined("DB_NAME")) define("DB_NAME", "mikhmon_piyan");
if (!defined("DB_CHARSET")) define("DB_CHARSET", "utf8mb4");

$db_host = DB_HOST; $db_user = DB_USER; $db_pass = DB_PASS; $db_name = DB_NAME;

function initMikhmonTables($pdo) {
    $chk = $pdo->query("SHOW TABLES LIKE 'billing_customers'")->fetchAll();
    if (empty($chk)) {
        $root = __DIR__;
        while ($root !== "/" && !is_dir($root . "/database")) { $root = dirname($root); }
        if (is_dir($root . "/database")) {
            $order = ["complete_installation.sql","agent_system.sql","billing_module.sql","payment_gateway_tables.sql","insert_tripay_payment_methods.sql","public_sales_schema.sql","telegram_integration.sql","voucher_settings_update.sql","safe_fix_tables.sql"];
            foreach ($order as $f) {
                $fp = $root . "/database/" . $f;
                if (file_exists($fp)) { try { $pdo->exec(file_get_contents($fp)); } catch (Exception $e) {} }
            }
            foreach (glob($root . "/database/*.sql") as $fp) {
                try { $pdo->exec(file_get_contents($fp)); } catch (Exception $e) {}
            }
        }
    }
}

function getDBConnection() {
    static $conn = null;
    if ($conn !== null) return $conn;
    $targets = [
        ["host" => "localhost", "port" => 3306],
        ["host" => "127.0.0.1", "port" => 3306],
        ["host" => "127.0.0.1", "port" => 3307]
    ];
    foreach ($targets as $t) {
        try {
            $pdo = new PDO("mysql:host={$t['host']};port={$t['port']};charset=utf8mb4", DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn = new PDO("mysql:host={$t['host']};port={$t['port']};dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            initMikhmonTables($conn);
            return $conn;
        } catch (PDOException $e) { continue; }
    }
    return false;
}

// Sediakan juga koneksi MySQLi ($conn & $mysqli) untuk file lama yang memakai mysqli_*
$conn = @mysqli_connect("localhost", DB_USER, DB_PASS);
if (!$conn) $conn = @mysqli_connect("127.0.0.1", DB_USER, DB_PASS);
if ($conn) {
    @mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    @mysqli_select_db($conn, DB_NAME);
    @mysqli_set_charset($conn, "utf8mb4");
}
$mysqli = $conn;
