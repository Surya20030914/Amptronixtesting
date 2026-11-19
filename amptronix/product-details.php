<?php
require_once __DIR__ . "/config/db.php";

function e($value): string {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    http_response_code(404);
    exit("Product not found.");
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND active = 1 LIMIT 1");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    http_response_code(404);
    exit("Product not found or currently unavailable.");
}

function fileUrl($path): string {
    return trim((string)($path ?? ""));
}

$datasetUrl = fileUrl($product["dataset"] ?? "");
$applicationUrl = fileUrl($product["application"] ?? "");
$imageUrl = fileUrl($product["image"] ?? "");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($product["name"]) ?> | Amptronix</title>
<style>
:root{--blue:#087cf2;--navy:#142b48;--muted:#687d96;--line:#dce7f3;--pale:#f4f8fe}
*{box-sizing:border-box}body{margin:0;background:#f7faff;color:var(--navy);font-family:Arial,Helvetica,sans-serif;line-height:1.6}a{text-decoration:none}
header{height:76px;background:#fff;border-bottom:1px solid #e3ebf5;display:flex;align-items:center;justify-content:space-between;padding:0 7%;position:sticky;top:0;z-index:20}.logo{font-size:25px;font-weight:800;color:#c81020}.back{color:var(--blue);font-weight:700}
.container{max-width:1220px;margin:0 auto;padding:42px 24px 70px}.hero{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center;background:#fff;border:1px solid var(--line);border-radius:18px;padding:30px;margin-bottom:28px}.visual{height:390px;background:linear-gradient(145deg,#edf5ff,#dfedff);border-radius:13px;display:flex;align-items:center;justify-content:center;padding:24px}.visual img{width:100%;height:100%;object-fit:contain}.no-image{color:var(--muted)}.eyebrow{color:var(--blue);font-size:12px;font-weight:800;letter-spacing:1.8px}h1{font-size:clamp(30px,4vw,44px);line-height:1.15;margin:10px 0 18px}.meta{display:flex;flex-wrap:wrap;gap:8px 25px;color:var(--muted);margin-bottom:18px}.meta strong{color:#263f5d}.description{white-space:pre-line;color:#435873;margin:0 0 22px}.hero-quote{display:inline-flex;background:var(--blue);color:#fff;padding:12px 20px;border-radius:8px;font-weight:700}
.actions{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;background:#fff;border:1px solid var(--line);border-radius:14px;padding:15px}.action{min-height:58px;display:flex;align-items:center;justify-content:center;gap:9px;padding:12px 10px;border-radius:9px;color:#29425f;font-weight:700;text-align:center;border:1px solid transparent;background:#fff;cursor:pointer;font:inherit;font-weight:700}.action:hover{background:#edf5ff;color:var(--blue);border-color:#d8e8fb}.action span{font-size:19px}
.footer{padding:25px;text-align:center;color:#8291a4;background:#fff;border-top:1px solid #e3ebf5}
.modal{display:none;position:fixed;inset:0;background:rgba(9,25,45,.62);z-index:1000;align-items:center;justify-content:center;padding:16px}.modal.show{display:flex}.modal-content{position:relative;background:#fff;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;padding:30px;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,.2)}.modal-content h2{margin:0 0 8px;color:var(--navy)}.modal-intro{color:#718096;font-size:14px;margin:0 0 20px}.modal-close{position:absolute;right:15px;top:10px;border:0;background:none;font-size:28px;color:#68788d;cursor:pointer}.quote-form label{display:block;margin:12px 0 6px;font-size:13px;font-weight:700;color:#42536a}.quote-form input,.quote-form select,.quote-form textarea{width:100%;padding:11px;border:1px solid #dce4ed;border-radius:6px;font:14px Arial,sans-serif}.quote-form textarea{min-height:90px;resize:vertical}.submit-quote{width:100%;margin-top:18px;padding:13px;background:var(--blue);color:#fff;border:0;border-radius:7px;font-weight:700;cursor:pointer}.notice{max-width:520px}.notice p{color:#60758d;margin:10px 0 0}
@media(max-width:800px){.hero{grid-template-columns:1fr;gap:25px}.visual{height:300px}.actions{grid-template-columns:repeat(2,minmax(0,1fr))}.container{padding:25px 16px 50px}}
@media(max-width:480px){header{padding:0 16px}.logo{font-size:20px}.hero{padding:18px}.visual{height:230px}.action{font-size:14px;min-height:55px}}


/* ===== AMPTRONIX PUBLIC UI/UX REFINEMENT (visual-only) ===== */

/* AMPTRONIX PUBLIC UI polish — visual-only overrides */
:where(a,button,input,select,textarea,summary,[tabindex]):focus-visible {
  outline: 3px solid rgba(8,124,242,.32);
  outline-offset: 3px;
}
:where(button,a) { -webkit-tap-highlight-color: transparent; }
::selection { background: rgba(8,124,242,.18); color: #102b4c; }

/* Product detail page polish */
body { background: #f7faff; }
header {
  box-shadow: 0 5px 22px rgba(16,35,61,.045);
  backdrop-filter: blur(12px);
}
.hero {
  border-radius: 20px;
  box-shadow: 0 12px 34px rgba(16,47,85,.055);
}
.visual {
  background: radial-gradient(circle at 50% 42%,#fff 0%,#edf5ff 78%);
  border: 1px solid #e1eaf4;
}
.actions { box-shadow: 0 8px 24px rgba(16,47,85,.045); }
.action {
  border: 1px solid #e5edf6;
  border-radius: 11px;
  transition: transform .2s ease, background .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.action:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(16,47,85,.07);
}
.hero-quote, .submit-quote { border-radius: 9px; box-shadow: 0 7px 17px rgba(8,124,242,.16); }
.modal-content { border: 1px solid #e1eaf4; border-radius: 17px; }
.quote-form input, .quote-form select, .quote-form textarea {
  border-radius: 9px;
  transition: border-color .2s ease, box-shadow .2s ease;
}
.quote-form input:focus, .quote-form select:focus, .quote-form textarea:focus {
  outline: none; border-color: #68aef5; box-shadow: 0 0 0 4px rgba(8,124,242,.09);
}
.footer { background: #fff; }

</style>
</head>
<body>
<?php require_once __DIR__ . '/header.php'; ?>
<main class="container">
  <section class="hero">
    <div class="visual">
      <?php if ($imageUrl !== ""): ?><img src="<?= e($imageUrl) ?>" alt="<?= e($product["name"]) ?>">
      <?php else: ?><span class="no-image">Product image is not currently available.</span><?php endif; ?>
    </div>
    <div class="product-info">
      <span class="eyebrow">AMPTRONIX PRODUCT</span>
      <h1><?= e($product["name"]) ?></h1>
      <div class="meta">
        <span>Model No: <strong><?= e($product["model"] ?? "Not specified") ?></strong></span>
        <span>Product No: <strong><?= e($product["product_number"] ?? "Not specified") ?></strong></span>
      </div>
      <?php if (!empty($product["description"])): ?><p class="description"><?= e($product["description"]) ?></p><?php endif; ?>
      <?php if (!empty($product["quote_enabled"])): ?><button class="hero-quote" type="button" onclick="openQuote()">Request a Quote →</button><?php endif; ?>
    </div>
  </section>

  <nav class="actions" aria-label="Product actions">
    <?php if ($datasetUrl !== ""): ?><a class="action" href="<?= e($datasetUrl) ?>" target="_blank" rel="noopener noreferrer"><span>▤</span> Datasheet</a>
    <?php else: ?><button class="action" type="button" onclick="showUnavailable('Datasheet')"><span>▤</span> Datasheet</button><?php endif; ?>

    <?php if ($applicationUrl !== ""): ?><a class="action" href="<?= e($applicationUrl) ?>" target="_blank" rel="noopener noreferrer"><span>⌘</span> Application</a>
    <?php else: ?><button class="action" type="button" onclick="showUnavailable('Application')"><span>⌘</span> Application</button><?php endif; ?>

    <button class="action" type="button" onclick="openQuote()"><span>✉</span> Request a Quote</button>
    <a class="action" href="product-box.php?id=<?= (int)$product["id"] ?>"><span>▣</span> What's in the Box</a>
  </nav>
</main>

<div class="modal" id="quoteModal" role="dialog" aria-modal="true" aria-labelledby="quoteTitle">
  <div class="modal-content">
    <button class="modal-close" type="button" onclick="closeQuote()" aria-label="Close">&times;</button>
    <h2 id="quoteTitle">Request a Quote</h2>
    <p class="modal-intro">Please share your details and requirements. Our team will get back to you.</p>
    <form class="quote-form" action="product-enquiry.php" method="POST">
      <input type="hidden" name="product_id" value="<?= (int)$product["id"] ?>">
      <label>Product Name</label><input type="text" value="<?= e($product["name"]) ?>" readonly>
      <label>Product Number</label><input type="text" value="<?= e($product["product_number"]) ?>" readonly>
      <label>Model Number</label><input type="text" name="model_number" value="<?= e($product["model"]) ?>" readonly>
      <label>Your Name *</label><input type="text" name="customer_name" required>
      <label>Company Name</label><input type="text" name="company_name">
      <label>Email Address *</label><input type="email" name="customer_email" required>
      <label>Phone Number *</label><input type="tel" name="customer_phone" pattern="[0-9+() -]{7,20}" required>
      <label>Your Message *</label><textarea name="message" placeholder="Tell us about your requirements..." required></textarea>
      <button class="submit-quote" type="submit">Submit Enquiry</button>
    </form>
  </div>
</div>
<div class="modal" id="unavailableModal" role="dialog" aria-modal="true" aria-labelledby="unavailableTitle">
  <div class="modal-content notice"><button class="modal-close" type="button" onclick="closeUnavailable()" aria-label="Close">&times;</button><h2 id="unavailableTitle">Document Not Available</h2><p id="unavailableMessage"></p></div>
</div>
<footer class="footer">© <?= date("Y") ?> Amptronix. All rights reserved.</footer>
<script>
function openQuote(){document.getElementById('quoteModal').classList.add('show');}
function closeQuote(){document.getElementById('quoteModal').classList.remove('show');}
function showUnavailable(type){document.getElementById('unavailableMessage').textContent='The '+type.toLowerCase()+' document has not been uploaded for this product yet. Please contact our team if you need further information.';document.getElementById('unavailableModal').classList.add('show');}
function closeUnavailable(){document.getElementById('unavailableModal').classList.remove('show');}
document.querySelectorAll('.modal').forEach(function(modal){modal.addEventListener('click',function(event){if(event.target===modal)modal.classList.remove('show');});});
document.addEventListener('keydown',function(event){if(event.key==='Escape'){closeQuote();closeUnavailable();}});
</script>
</body>
</html>
