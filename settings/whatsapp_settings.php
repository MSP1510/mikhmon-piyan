<?php
if(isset($_GET["wa_proxy"])){
  header("Content-Type: application/json");
  $cfg_f = __DIR__ . "/../include/wa_qr_cfg.json";
  $cfg = file_exists($cfg_f) ? json_decode(file_get_contents($cfg_f), true) : ["wa_engine" => "http://10.20.30.50:3005"];
  $base = rtrim($cfg["wa_engine"], "/");
  $act = $_GET["wa_proxy"];
  $ctx = stream_context_create(["http"=>["timeout"=>4, "method"=>($act=="status"?"GET":"POST"), "header"=>"Content-Type: application/json", "content"=>file_get_contents("php://input")]]);
  $res = @file_get_contents($base . "/" . $act, false, $ctx);
  echo $res ?: json_encode(["error"=>"Tidak dapat menjangkau Engine WA ($base). Pastikan HP terhubung ke Wi-Fi lokal atau gunakan URL Tunnel!"]);
  exit;
}
?>
<?php
session_start();
error_reporting(0);
$cfg_f = __DIR__ . "/../include/wa_qr_cfg.json";
$cfg = file_exists($cfg_f) ? json_decode(file_get_contents($cfg_f), true) : [
    "wa_engine" => "http://10.20.30.50:3005",
    "bank" => "BCA 1234567890 a/n D.N.A NETWORK",
    "ewallet" => "DANA / OVO / GOPAY: 08123456789"
];
if (isset($_POST["save_wa_tpl"])) {
    $cfg["wa_engine"] = rtrim(trim($_POST["wa_engine"]), "/");
    $cfg["bank"] = trim($_POST["bank"]);
    $cfg["ewallet"] = trim($_POST["ewallet"]);
    file_put_contents($cfg_f, json_encode($cfg, JSON_PRETTY_PRINT));
    $msg_ok = "Pengaturan QR & Template Pembayaran tersimpan!";
}
?>
<div class="row">
<div class="col-12">
  <?php if (!empty($msg_ok)): ?><div class="alert alert-success"><?= $msg_ok ?></div><?php endif; ?>
  <div class="card" style="border-radius:12px;margin-bottom:15px">
    <div class="card-body">
      <h4 style="margin-top:0;font-weight:bold">1. Tautkan Nomor WhatsApp Penagihan</h4>
      <div style="margin-bottom:12px;font-size:13px">Status Gateway: <b id="wa-st-txt" style="color:#d97706">Memuat QR Code...</b></div>
      <div style="text-align:center;background:#fff;padding:16px;border-radius:10px;border:1px solid #e2e8f0;max-width:280px;margin:0 auto">
        <div id="wa-qr-img" style="min-height:220px;display:flex;align-items:center;justify-content:center">
          <span style="color:#64748b;font-size:12px">Menghubungkan ke Engine WA...</span>
        </div>
        <p style="font-size:12px;color:#475569;margin:10px 0 0">WhatsApp HP &rarr; <b>Perangkat Tertaut</b> &rarr; Scan QR di atas</p>
      </div>
      <div style="text-align:center;margin-top:14px">
        <button type="button" onclick="cekWaQr()" class="btn btn-primary"><i class="fa fa-refresh"></i> Refresh QR Code</button>
        <button type="button" onclick="putusWa()" class="btn btn-danger"><i class="fa fa-unlink"></i> Putuskan</button>
      </div>
    </div>
  </div>

  <div class="card" style="border-radius:12px">
    <div class="card-body">
      <h4 style="margin-top:0;font-weight:bold">2. Template Bank, E-Wallet & Alamat Engine WA</h4>
      <form method="POST">
        <div class="form-group">
          <label>URL Engine WhatsApp QR (STB Armbian):</label>
          <input type="text" name="wa_engine" class="form-control" value="<?= htmlspecialchars($cfg["wa_engine"]) ?>" required>
        </div>
        <div class="form-group">
          <label>Rekening Bank ({bank}):</label>
          <input type="text" name="bank" class="form-control" value="<?= htmlspecialchars($cfg["bank"]) ?>">
        </div>
        <div class="form-group">
          <label>Akun E-Wallet ({ewallet}):</label>
          <input type="text" name="ewallet" class="form-control" value="<?= htmlspecialchars($cfg["ewallet"]) ?>">
        </div>
        <button type="submit" name="save_wa_tpl" class="btn btn-success"><i class="fa fa-save"></i> Simpan Template</button>
      </form>
    </div>
  </div>
</div>
</div>
<script>
const WA_URL = <?= json_encode($cfg["wa_engine"]) ?>;
function cekWaQr() {
  fetch("?wa_proxy=status&_=" + Date.now()).then(r => r.json()).then(d => {
    const st = document.getElementById("wa-st-txt");
    const box = document.getElementById("wa-qr-img");
    if (d.connected) {
      st.style.color = "#16a34a"; st.textContent = "Terhubung (" + (d.user || "Aktif") + ")";
      box.innerHTML = "<div style='color:#16a34a;font-weight:bold;padding:30px 10px'><i class='fa fa-check-circle' style='font-size:52px'></i><br><br>WhatsApp Tertaut!</div>";
    } else if (d.qr) {
      st.style.color = "#d97706"; st.textContent = "Scan QR Code";
      box.innerHTML = "<img src='" + d.qr + "' style='width:220px;height:220px'>";
    } else {
      st.style.color = "#0284c7"; st.textContent = "Menyiapkan QR...";
    }
  }).catch(() => {
    document.getElementById("wa-st-txt").textContent = "Engine WA Belum Aktif (Cek URL Engine)";
  });
}
function putusWa() {
  if (confirm("Putuskan perangkat WhatsApp?")) fetch("?wa_proxy=logout", {method:"POST"}).then(() => setTimeout(cekWaQr, 1500));
}
cekWaQr(); setInterval(cekWaQr, 4000);
</script>