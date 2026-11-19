
<?php
require_once __DIR__ . "/config/db.php";

$categoryTitle = $categoryTitle ?? "Products";

$allowedCategories = [
    "Optical System",
    "R.F System",
    "Power",
    "HDP"
];

if (!in_array($categoryTitle, $allowedCategories, true)) {
    http_response_code(404);
    exit("Invalid product category.");
}

/* Get active products from the new Home Products table */
$stmt = $pdo->prepare("
    SELECT
        id,
        category,
        product_number,
        product_name,
        model_number,
        short_info,
        description,
        specifications,
        key_features,
        image_path,
        datasheet_path,
        application_image_path
    FROM home_products
    WHERE category = ?
      AND is_active = 1
    ORDER BY display_order ASC, id ASC
");

$stmt->execute([$categoryTitle]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Get model variants for the selected products */
$modelsByProduct = [];

if (!empty($products)) {

    $productIds = array_column($products, "id");

    $placeholders = implode(
        ",",
        array_fill(0, count($productIds), "?")
    );

    $modelStmt = $pdo->prepare("
        SELECT
            home_product_id,
            model_number,
            model_description
        FROM home_product_models
        WHERE home_product_id IN ($placeholders)
        ORDER BY display_order ASC, id ASC
    ");

    $modelStmt->execute($productIds);

    foreach ($modelStmt->fetchAll(PDO::FETCH_ASSOC) as $model) {

        $modelsByProduct[$model["home_product_id"]][] = $model;
    }
}

function h($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($categoryTitle) ?> | Amptronix</title>
<style>
:root{
    --brand:#087cf2;
    --brand-dark:#075fc0;
    --navy:#102f55;
    --ink:#142943;
    --muted:#64758b;
    --line:#e3eaf2;
    --surface:#f5f8fc;
    --white:#fff;
    --shadow:0 12px 32px rgba(16,47,85,.08);
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{
    margin:0;
    font-family:Arial,Helvetica,sans-serif;
    color:var(--ink);
    background:#fff;
    -webkit-font-smoothing:antialiased;
}
header.site-header{
    min-height:80px;
    height:80px;
    padding:0 7%;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:28px;
    background:rgba(255,255,255,.97);
    position:sticky;
    top:0;
    z-index:1000;
    border-bottom:1px solid var(--line);
    backdrop-filter:blur(12px);
}
.site-logo{display:flex;align-items:center;flex:0 0 auto;text-decoration:none}
.site-logo img{width:180px;height:auto;max-height:48px;display:block;object-fit:contain}
.site-nav{display:flex;align-items:center;justify-content:flex-end;gap:30px;flex-wrap:wrap}
.site-nav a{
    color:#42536a;
    text-decoration:none;
    font-size:14px;
    font-weight:600;
    transition:color .2s ease;
}
.site-nav a:hover{color:var(--brand)}
.page{
    width:86%;
    max-width:1240px;
    margin:58px auto 88px;
}
.back{
    display:inline-flex;
    align-items:center;
    color:var(--brand);
    text-decoration:none;
    font-weight:700;
    font-size:14px;
    margin-bottom:26px;
    transition:transform .2s ease;
}
.back:hover{transform:translateX(-3px)}
.eyebrow{
    color:var(--brand);
    font-size:12px;
    font-weight:800;
    letter-spacing:2.2px;
    margin:0 0 12px;
}
h1{
    color:var(--ink);
    font-size:clamp(34px,4.5vw,50px);
    line-height:1.1;
    letter-spacing:-1px;
    margin:0 0 14px;
}
.intro{
    color:var(--muted);
    font-size:16px;
    line-height:1.75;
    max-width:700px;
    margin:0 0 38px;
}
.grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:26px;
    align-items:stretch;
}
.card{
    min-width:0;
    border:1px solid var(--line);
    border-radius:15px;
    overflow:hidden;
    background:var(--white);
    box-shadow:var(--shadow);
    display:flex;
    flex-direction:column;
    transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease;
}
.card:hover{
    transform:translateY(-5px);
    box-shadow:0 18px 42px rgba(16,47,85,.13);
    border-color:#cbdcf0;
}
.image{
    height:215px;
    padding:22px;
    background:linear-gradient(145deg,#f7faff,#edf3fa);
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}
.image img{
    width:100%;
    height:100%;
    object-fit:contain;
    transition:transform .35s ease;
}
.card:hover .image img{transform:scale(1.045)}
.image span{
    color:#53657b;
    font-size:15px;
    text-align:center;
}
.info{
    padding:22px;
    display:flex;
    flex-direction:column;
    flex:1;
    min-width:0;
}
.name{
    color:var(--ink);
    font-weight:750;
    font-size:18px;
    line-height:1.35;
    margin:0 0 12px;
    overflow-wrap:anywhere;
}
.model{
    color:var(--muted);
    font-size:14px;
    line-height:1.65;
    margin:0 0 14px;
    overflow-wrap:anywhere;
}
.model strong{color:var(--navy);font-weight:700}
.model p{margin:7px 0 0}
.application-details{
    margin:0 0 16px;
    color:var(--brand);
    font-size:14px;
    line-height:1.5;
}
.application-details summary{
    display:inline-block;
    cursor:pointer;
    font-weight:600;
    color:var(--brand);
    list-style:none;
    text-decoration:underline;
    text-underline-offset:3px;
}
.application-details summary::-webkit-details-marker{display:none}
.application-details summary:hover{color:var(--brand-dark)}
.application-content{
    margin-top:10px;
    padding:12px;
    background:var(--surface);
    border:1px solid var(--line);
    border-radius:9px;
}
.application-content img{
    display:block;
    width:100%;
    max-width:100%;
    max-height:280px;
    object-fit:contain;
    border-radius:6px;
    background:#fff;
}
.application-content p{margin:0;color:var(--muted);font-size:13px}
.application-content img + p{margin-top:8px}
.application-content a{color:var(--brand);font-weight:700;text-decoration:none}
.application-content a:hover{text-decoration:underline}
.product-actions{
    display:flex;
    align-items:stretch;
    gap:10px;
    margin-top:auto;
    padding-top:18px;
}
.product-actions .more,
.product-actions .enquiry-btn{
    flex:1 1 135px;
    min-width:0;
    min-height:42px;
    margin:0;
    padding:11px 12px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    white-space:normal;
    overflow-wrap:anywhere;
    border:0;
    border-radius:7px;
    font-family:inherit;
    font-size:13px;
    line-height:1.25;
    font-weight:700;
}
.product-actions .more{
    background:var(--brand);
    color:#fff;
    text-decoration:none;
    transition:background .2s ease,transform .2s ease;
}
.product-actions .more:hover{background:var(--brand-dark);transform:translateY(-1px)}
.product-actions .enquiry-btn{
    background:var(--navy);
    color:#fff;
    cursor:pointer;
    transition:background .2s ease,transform .2s ease;
}
.product-actions .enquiry-btn:hover{background:var(--brand);transform:translateY(-1px)}
.empty{
    padding:30px;
    background:var(--surface);
    border:1px solid var(--line);
    border-radius:12px;
    color:var(--muted);
    line-height:1.7;
}
footer{
    padding:26px 7%;
    border-top:1px solid var(--line);
    color:#718096;
    font-size:13px;
    text-align:center;
}
@media(max-width:1050px){
    header{padding-left:5%;padding-right:5%}
    nav{gap:17px}
    .page{width:90%}
    .grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
}
@media(max-width:640px){
    header{
        min-height:0;
        height:auto;
        padding:15px 5%;
        flex-wrap:wrap;
        justify-content:center;
    }
    .logo img{width:155px}
    nav{
        width:100%;
        justify-content:center;
        gap:12px 18px;
        padding-top:8px;
    }
    nav a{font-size:13px}
    .page{width:90%;margin:38px auto 58px}
    .intro{font-size:14px;margin-bottom:28px}
    .grid{grid-template-columns:1fr;gap:18px}
    .image{height:205px}
    .info{padding:19px}
    .name{font-size:17px}
    .product-actions{gap:9px}
}
@media(prefers-reduced-motion:reduce){
    *,*::before,*::after{scroll-behavior:auto!important;transition:none!important}
}

/* PRODUCT ENQUIRY MODAL */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    width: 100%;
    height: 100%;
    padding: 24px;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
    background: rgba(5, 18, 38, 0.72);
    backdrop-filter: blur(3px);
}
.modal-content {
    position: relative;
    width: min(760px, 100%);
    max-height: calc(100vh - 48px);
    overflow-y: auto;
    padding: 32px;
    border: 1px solid #dce5ef;
    border-radius: 16px;
    background: #ffffff;
    color: #172b46;
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
}
.modal-close {
    position: absolute;
    top: 14px;
    right: 16px;
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 8px;
    background: #edf3f9;
    color: #183454;
    font-size: 25px;
    line-height: 1;
    cursor: pointer;
}
.modal-close:hover { background: #dce8f4; }
.modal-content h2 {
    margin: 0 48px 8px 0;
    color: #102b4c;
    font-size: 25px;
}
.modal-intro {
    margin: 0 0 24px;
    color: #64748b;
    line-height: 1.6;
}
.enquiry-form {
    display: block;
    width: 100%;
}
.enquiry-form label {
    display: block;
    margin: 15px 0 7px;
    color: #263e5b;
    font-size: 14px;
    font-weight: 650;
}
.enquiry-form input:not([type="hidden"]),
.enquiry-form select,
.enquiry-form textarea {
    display: block;
    width: 100%;
    min-height: 44px;
    padding: 11px 13px;
    border: 1px solid #cbd7e5;
    border-radius: 8px;
    outline: none;
    background: #ffffff;
    color: #172b46;
    font: inherit;
    font-size: 14px;
    box-sizing: border-box;
}
.enquiry-form input:focus,
.enquiry-form select:focus,
.enquiry-form textarea:focus {
    border-color: #087ff5;
    box-shadow: 0 0 0 3px rgba(8, 127, 245, 0.12);
}
.enquiry-form input[readonly] {
    background: #f3f7fb;
    color: #52677f;
}
.enquiry-form textarea {
    min-height: 110px;
    resize: vertical;
}
.submit-enquiry {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    margin-top: 20px;
    padding: 12px 24px;
    border: 0;
    border-radius: 8px;
    background: #087ff5;
    color: #ffffff;
    font: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s ease, transform .2s ease;
}
.submit-enquiry:hover {
    background: #0666c7;
    transform: translateY(-1px);
}
body.modal-open { overflow: hidden; }
@media (max-width: 640px) {
    .modal { padding: 12px; }
    .modal-content {
        max-height: calc(100vh - 24px);
        padding: 24px 18px;
        border-radius: 12px;
    }
    .modal-content h2 { font-size: 21px; }
    .submit-enquiry { width: 100%; }
}


/* Amptronix shared public header standard */
.site-header {
    box-sizing: border-box;
    min-height: 80px;
    height: 80px;
    padding: 0 7%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
    background: rgba(255, 255, 255, 0.96);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid #e8eef5;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
.site-logo {
    display: flex;
    align-items: center;
    flex: 0 0 auto;
    text-decoration: none;
}
.site-logo img {
    display: block;
    width: 180px;
    height: auto;
    max-height: 48px;
    object-fit: contain;
}
.site-nav {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 30px;
    flex-wrap: wrap;
}
.site-nav a {
    color: #42536a;
    text-decoration: none;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    white-space: nowrap;
    transition: color .2s ease;
}
.site-nav a:hover,
.site-nav a:focus-visible {
    color: #087cf2;
}
.site-back-link {
    display: inline-flex;
    align-items: center;
    margin: 0 0 24px;
    color: #087cf2;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
}
.site-back-link:hover { text-decoration: underline; }
@media (max-width: 900px) {
    .site-header {
        height: auto;
        min-height: 80px;
        padding: 14px 5%;
        flex-wrap: wrap;
        row-gap: 12px;
    }
    .site-nav {
        width: 100%;
        justify-content: center;
        gap: 14px 22px;
    }
}
@media (max-width: 600px) {
    .site-header { padding: 13px 5%; }
    .site-logo img { width: 150px; max-height: 42px; }
    .site-nav { justify-content: flex-start; gap: 12px 18px; }
    .site-nav a { font-size: 13px; }
}


/* HDP only: give product photos more display area without cropping */
.category-hdp .image {
    height: 250px;
    padding: 12px;
}
.category-hdp .image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
@media (max-width: 600px) {
    .category-hdp .image {
        height: 190px;
        padding: 10px;
    }
}



/* ===== AMPTRONIX PUBLIC UI/UX REFINEMENT (visual-only) ===== */

/* AMPTRONIX PUBLIC UI polish — visual-only overrides */
:where(a,button,input,select,textarea,summary,[tabindex]):focus-visible {
  outline: 3px solid rgba(8,124,242,.32);
  outline-offset: 3px;
}
:where(button,a) { -webkit-tap-highlight-color: transparent; }
::selection { background: rgba(8,124,242,.18); color: #102b4c; }

/* Consistent category catalogue polish */
body { background: #f8fbff; }
.site-header {
  box-shadow: 0 5px 22px rgba(16,35,61,.045);
}
.site-nav a { position: relative; }
.site-nav a:hover { color: var(--brand-dark); }
.page { margin-top: 48px; }
.back, .site-back-link { transition: color .2s ease, transform .2s ease; }
.card {
  border-radius: 17px;
  box-shadow: 0 8px 24px rgba(16,47,85,.055);
}
.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 36px rgba(16,47,85,.11);
}
.image { background: linear-gradient(145deg,#f9fbff,#edf4fc); }
.info { padding: 20px; }
.name { letter-spacing: -.2px; }
.product-actions .more,
.product-actions .enquiry-btn {
  border-radius: 9px;
  min-height: 44px;
}
.product-actions .more:focus-visible,
.product-actions .enquiry-btn:focus-visible { outline-offset: 3px; }
.application-content { background: #f8fbff; }
footer { background: #fbfdff; }
@media(max-width:640px) {
  .page { margin-top: 32px; }
  .card { border-radius: 14px; }
  .info { padding: 17px; }
}

</style>
</head>
<body class="<?= $categoryTitle === "HDP" ? "category-hdp" : "" ?>">
<?php require_once __DIR__ . '/header.php'; ?>
<main class="page">
  <a class="back" href="index.php#home">&larr; Back to Home</a>
  <p class="eyebrow">AMPTRONIX PRODUCT CATEGORY</p>
  <h1><?= h($categoryTitle) ?></h1>
  <p class="intro">Explore Amptronix <?= h($categoryTitle) ?> products and their specifications.</p>
  <?php if ($products): ?>
  <div class="grid">
    <?php foreach ($products as $product): ?>
    <article class="card">
      <div class="image">
        <?php if (!empty($product["image_path"])): ?>
          <img
    src="<?= h($product["image_path"]) ?>"
    alt="<?= h($product["product_name"]) ?>"
    loading="lazy"
    onerror="this.style.display='none'; this.parentElement.innerHTML='Product image unavailable';"
>
        <?php else: ?><span>Product image unavailable</span><?php endif; ?>
      </div>
      
<div class="info">

    <p class="name">
        <?= h($product["product_name"]) ?>
    </p>

    <?php if (!empty(trim($product["product_number"] ?? ""))): ?>
    <p class="model">
        Datasheet: <?= h($product["product_number"]) ?>
    </p>
<?php endif; ?>

    <?php if (!empty($product["short_info"])): ?>
        <p class="model">
            <?= h($product["short_info"]) ?>
        </p>
    <?php endif; ?>

    <div class="model">
        <strong>Available Models:</strong>

        <?php
        $productModels = $modelsByProduct[$product["id"]] ?? [];
        ?>

        <?php if (!empty($productModels)): ?>

            <?php foreach ($productModels as $model): ?>
                <p>
                    <?= h($model["model_number"]) ?>
                    — <?= h($model["model_description"]) ?>
                </p>
            <?php endforeach; ?>

        <?php elseif (!empty($product["model_number"])): ?>

            <p><?= h($product["model_number"]) ?></p>

        <?php endif; ?>
    </div>

    <details class="application-details">
        <summary>Application</summary>
        <div class="application-content">
            <?php if (!empty($product["application_image_path"])): ?>
                <a
                    href="<?= h($product["application_image_path"]) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Open application image in a new tab"
                >
                    <img
                        src="<?= h($product["application_image_path"]) ?>"
                        alt="Application image for <?= h($product["product_name"]) ?>"
                        loading="lazy"
                    >
                </a>
                <p>Click the image to view it in full size.</p>
            <?php else: ?>
                <p>
                    Contact us:
                    <a href="mailto:sales@amptronix.co.in">sales@amptronix.co.in</a>
                </p>
            <?php endif; ?>
        </div>
    </details>

    <?php if (!empty($product["description"])): ?>
        <p class="model">
            <?= h($product["description"]) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($product["specifications"])): ?>
        <p class="model">
            <strong>Specifications:</strong><br>
            <?= nl2br(h($product["specifications"])) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($product["key_features"])): ?>
        <p class="model">
            <strong>Key Features:</strong><br>
            <?= nl2br(h($product["key_features"])) ?>
        </p>
    <?php endif; ?>
<div class="product-actions">

    <?php if (!empty($product["datasheet_path"])): ?>

        <a
            class="more"
            href="<?= h($product["datasheet_path"]) ?>"
            target="_blank"
            rel="noopener noreferrer"
        >
            View Datasheet
        </a>

    <?php else: ?>

        <span class="more">Datasheet Coming Soon</span>

    <?php endif; ?>

    <button
        type="button"
        class="enquiry-btn"
        onclick="openEnquiry(this, <?= (int)$product['id'] ?>)"
        data-product="<?= h($product['product_name']) ?>"
        data-number="<?= h($product['product_number']) ?>"
        data-models="<?= h(json_encode(
            $modelsByProduct[$product['id']] ?? [],
            JSON_UNESCAPED_UNICODE
        )) ?>"
    >
        Enquire Now
    </button>

</div>
    </article>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <div class="empty">No active products are currently assigned to this category. Add products through the existing admin panel and set their category to <?= h($categoryTitle) ?>.</div>
  <?php endif; ?>
</main>
<!-- Product Enquiry Popup -->
<div class="modal" id="enquiryModal">
    <div class="modal-content">

        <button type="button" class="modal-close"
            onclick="closeEnquiry()">&times;</button>

        <h2>Product Enquiry</h2>

        <p class="modal-intro">
            Please provide your details. Our team will get back to you.
        </p>

        <form class="enquiry-form"
              action="product-enquiry.php"
              method="POST">

            <input type="hidden" name="product_id" id="enquiryProductId">

            <label>Product Name</label>
            <input type="text" id="enquiryProductName" readonly>

            <label>Product Number</label>
            <input type="text" id="enquiryProductNumber" readonly>

            <label>Your Name *</label>
            <input type="text" name="customer_name" required>

            <label>Company Name</label>
            <input type="text" name="company_name">

            <label>Email Address *</label>
            <input type="email" name="customer_email" required>

            <label>Phone Number *</label>
            <input type="tel" name="customer_phone"
                   pattern="[0-9+() -]{7,20}" required>

            <label>Select Model</label>
            <select name="model_number" id="enquiryModel">
                <option value="">Select a model</option>
            </select>

            <label>Your Message *</label>
            <textarea name="message"
                      placeholder="Tell us about your requirements..."
                      required></textarea>

            <button type="submit" class="submit-enquiry">
                Submit Enquiry
            </button>

        </form>
    </div>
</div>
<footer>© <?= date("Y") ?> Amptronix. All rights reserved.</footer>
<script>
function openEnquiry(button, productId) {

    const modal = document.getElementById("enquiryModal");

    document.getElementById("enquiryProductId").value = productId;

    document.getElementById("enquiryProductName").value =
        button.dataset.product;

    document.getElementById("enquiryProductNumber").value =
        button.dataset.number;

    const modelSelect = document.getElementById("enquiryModel");

    modelSelect.innerHTML =
        '<option value="">Select a model</option>';

    let models = [];

    try {
        models = JSON.parse(button.dataset.models || "[]");
    } catch (error) {
        console.error("Unable to load product models.");
    }

    models.forEach(function(model) {

        const option = document.createElement("option");

        option.value = model.model_number;

        option.textContent = model.model_description
            ? model.model_number + " — " + model.model_description
            : model.model_number;

        modelSelect.appendChild(option);
    });

    modal.style.display = "flex";
    document.body.classList.add("modal-open");
}

function closeEnquiry() {
    document.getElementById("enquiryModal").style.display = "none";
    document.body.classList.remove("modal-open");
}

document.getElementById("enquiryModal").addEventListener("click", function(event) {
    if (event.target === this) {
        closeEnquiry();
    }
});
</script>
</body>
</html>
