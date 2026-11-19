<?php
require_once __DIR__ . "/config/db.php";
function e($value): string { return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8"); }
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) { http_response_code(404); exit("Product not found."); }
$stmt = $pdo->prepare("SELECT id, name, model, product_number, whats_in_box, image FROM products WHERE id = ? AND active = 1 LIMIT 1");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) { http_response_code(404); exit("Product not found or currently unavailable."); }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>What's in the Box | <?= e($product['name']) ?> | Amptronix</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f7faff;color:#172b45;font-family:Arial,Helvetica,sans-serif;line-height:1.65}header{height:76px;background:#fff;border-bottom:1px solid #e3ebf5;display:flex;align-items:center;justify-content:space-between;padding:0 7%}.logo{font-size:25px;font-weight:800;color:#c81020;text-decoration:none}.back{color:#087cf2;font-weight:700;text-decoration:none}.container{max-width:1000px;margin:48px auto;padding:0 24px 65px}.card{background:#fff;border:1px solid #dce7f3;border-radius:16px;padding:32px}.eyebrow{color:#087cf2;font-size:12px;font-weight:800;letter-spacing:1.5px}h1{font-size:34px;color:#142b48;margin:9px 0 12px}.meta{color:#687d96;display:flex;gap:25px;flex-wrap:wrap;margin-bottom:25px}.meta strong{color:#263f5d}.contents{white-space:pre-line;color:#344b67;background:#f7faff;border:1px solid #e1eaf4;border-radius:10px;padding:22px;min-height:90px}.empty{color:#71839a}.footer{text-align:center;background:#fff;border-top:1px solid #e3ebf5;color:#8291a4;padding:25px}@media(max-width:600px){header{padding:0 16px}.logo{font-size:20px}.container{margin:28px auto;padding:0 15px 45px}.card{padding:22px}h1{font-size:28px}}


/* ===== AMPTRONIX PUBLIC UI/UX REFINEMENT (visual-only) ===== */

/* AMPTRONIX PUBLIC UI polish — visual-only overrides */
:where(a,button,input,select,textarea,summary,[tabindex]):focus-visible {
  outline: 3px solid rgba(8,124,242,.32);
  outline-offset: 3px;
}
:where(button,a) { -webkit-tap-highlight-color: transparent; }
::selection { background: rgba(8,124,242,.18); color: #102b4c; }

/* What's-in-the-box page polish */
body { background: linear-gradient(180deg,#f7faff 0%,#f3f8fe 100%); }
header {
  position: sticky; top: 0; z-index: 30;
  box-shadow: 0 5px 22px rgba(16,35,61,.045);
}
.logo { letter-spacing: -.5px; }
.back { transition: color .2s ease, transform .2s ease; }
.back:hover { transform: translateX(-2px); }
.container { max-width: 1080px; }
.card {
  border-radius: 18px;
  padding: clamp(22px,3vw,36px);
  box-shadow: 0 14px 38px rgba(16,47,85,.07);
}
.contents {
  border-radius: 12px;
  line-height: 1.8;
  background: linear-gradient(145deg,#f9fbff,#f1f7fe);
}
.footer { background: rgba(255,255,255,.9); }

</style></head><body>
<?php require_once __DIR__ . '/header.php'; ?>
<main class="container"><section class="card"><span class="eyebrow">PRODUCT INFORMATION</span><h1>What's in the Box</h1><div class="meta"><span>Product: <strong><?= e($product['name']) ?></strong></span><span>Model No: <strong><?= e($product['model']) ?></strong></span><span>Product No: <strong><?= e($product['product_number']) ?></strong></span></div>
<?php if (!empty(trim((string)($product['whats_in_box'] ?? '')))): ?><div class="contents"><?= e($product['whats_in_box']) ?></div><?php else: ?><div class="contents empty">Package contents have not yet been provided for this product. Please contact our team for further information.</div><?php endif; ?>
</section></main><footer class="footer">© <?= date('Y') ?> Amptronix. All rights reserved.</footer></body></html>
