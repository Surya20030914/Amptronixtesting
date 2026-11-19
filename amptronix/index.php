<?php

require_once "config/db.php";

/* Load contact information */

$contact_stmt = $pdo->query("
    SELECT setting_key, setting_value
    FROM website_settings
    WHERE setting_key IN (
        'contact_address',
        'contact_address_2',
        'contact_phone_1',
        'contact_phone_2',
        'contact_email'
    )
");

$contact_settings = [];

while ($row = $contact_stmt->fetch(PDO::FETCH_ASSOC)) {

    $contact_settings[$row["setting_key"]] = $row["setting_value"];

}

$contact_address = $contact_settings["contact_address"] ?? "";
$contact_address_2 = $contact_settings["contact_address_2"] ?? "";
$contact_phone_1 = $contact_settings["contact_phone_1"] ?? "";
$contact_phone_2 = $contact_settings["contact_phone_2"] ?? "";
$contact_email = $contact_settings["contact_email"] ?? "";

/* Load social media links */

$social_stmt = $pdo->query("
    SELECT setting_key, setting_value
    FROM website_settings
    WHERE setting_key IN (
        'social_facebook',
        'social_instagram',
        'social_whatsapp',
        'social_x',
        'social_linkedin'
    )
");

$social_settings = [];

while ($row = $social_stmt->fetch(PDO::FETCH_ASSOC)) {

    $social_settings[$row["setting_key"]] = $row["setting_value"];

}

$social_facebook = $social_settings["social_facebook"] ?? "#";
$social_instagram = $social_settings["social_instagram"] ?? "#";
$social_whatsapp = $social_settings["social_whatsapp"] ?? "#";
$social_x = $social_settings["social_x"] ?? "#";
$social_linkedin = $social_settings["social_linkedin"] ?? "#";

/* Check public website status */

$website_status_stmt = $pdo->prepare("
    SELECT setting_value
    FROM website_settings
    WHERE setting_key = ?
    LIMIT 1
");

$website_status_stmt->execute(["public_site_status"]);

$website_status = $website_status_stmt->fetchColumn();

$solution_stmt = $pdo->query("
    SELECT
        solution_number,
        title,
        image,
        pdf_path
    FROM solutions
    WHERE active = 1
    ORDER BY solution_number ASC
");

$solutions = $solution_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($website_status === "inactive") {





    ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Amptronix - Maintenance</title>
    </head>

    <body style="
        margin:0;
        min-height:100vh;
        display:flex;
        align-items:center;
        justify-content:center;
        font-family:Arial,sans-serif;
        background:#f4f7fb;
        color:#172033;
        text-align:center;
    ">

        <div>
            <h1>Amptronix</h1>

            <h2>Website Temporarily Unavailable</h2>

            <p>
                We are currently updating our website.
            </p>

            <p>
                Please check back shortly.
            </p>
        </div>

    

<script>
/* Application image full-screen viewer */
document.addEventListener("DOMContentLoaded", function () {
    const preview = document.getElementById("applicationPreview");
    const previewImage = document.getElementById("applicationPreviewImage");
    const closeButton = document.getElementById("applicationPreviewClose");
    const zoomIn = document.getElementById("applicationZoomIn");
    const zoomOut = document.getElementById("applicationZoomOut");
    const zoomReset = document.getElementById("applicationZoomReset");
    const zoomLevel = document.getElementById("applicationZoomLevel");
    if (!preview || !previewImage || !closeButton) return;

    let scale = 1;
    function updateZoom() {
        previewImage.style.transform = `scale(${scale})`;
        zoomLevel.textContent = `${Math.round(scale * 100)}%`;
        previewImage.style.cursor = scale > 1 ? "grab" : "zoom-in";
    }
    function openPreview(image) {
        previewImage.src = image.currentSrc || image.src;
        previewImage.alt = image.alt || "Application image";
        scale = 1;
        updateZoom();
        preview.classList.add("show");
        preview.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }
    function closePreview() {
        preview.classList.remove("show");
        preview.setAttribute("aria-hidden", "true");
        previewImage.src = "";
        document.body.style.overflow = "";
    }

    document.querySelectorAll(".application-image-card").forEach(function (card) {
        const image = card.querySelector("img");
        if (!image) return;
        card.setAttribute("role", "button");
        card.setAttribute("tabindex", "0");
        card.setAttribute("aria-label", "Open application image full screen");
        card.addEventListener("click", function () { openPreview(image); });
        card.addEventListener("keydown", function (event) {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                openPreview(image);
            }
        });
    });

    closeButton.addEventListener("click", closePreview);
    preview.addEventListener("click", function (event) {
        if (event.target === preview) closePreview();
    });
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && preview.classList.contains("show")) closePreview();
    });
    if (zoomIn) zoomIn.addEventListener("click", function () { scale = Math.min(3, scale + .25); updateZoom(); });
    if (zoomOut) zoomOut.addEventListener("click", function () { scale = Math.max(.5, scale - .25); updateZoom(); });
    if (zoomReset) zoomReset.addEventListener("click", function () { scale = 1; updateZoom(); });
});
</script>

</body>
    </html>

    <?php
    exit;
}

$stmt = $pdo->query("
    SELECT application_number, image
    FROM applications
    WHERE active = 1
    ORDER BY application_number ASC
");

$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* Get active products for the public SATCOM and Broadband sections. */

$product_stmt = $pdo->query("
    SELECT
        id,
        product_number,
        name,
        model,
        description,
        image,
        detail_doc,
        category,
        product_group
    FROM products
    WHERE active = 1
    ORDER BY product_number ASC
");

$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

/* Group active products into the four public product sections. */
$productGroups = [
    "SATCOM" => [],
    "Broadband" => [],
    "Monitoring System" => [],
    "Battery Charger" => []
];

foreach ($products as $product) {
    $group_key = strtolower(trim((string)($product['product_group'] ?? '')));

    if ($group_key === 'satcom' || $group_key === 'satellite') {
        $productGroups["SATCOM"][] = $product;
    } elseif ($group_key === 'broadband' || $group_key === 'broad band') {
        $productGroups["Broadband"][] = $product;
    } elseif ($group_key === 'monitoring system' || $group_key === 'monitoring') {
        $productGroups["Monitoring System"][] = $product;
    } elseif ($group_key === 'battery charger' || $group_key === 'battery chargers') {
        $productGroups["Battery Charger"][] = $product;
    }
}

$satcom_products = $productGroups["SATCOM"];
$broadband_products = $productGroups["Broadband"];
$monitoring_system_products = $productGroups["Monitoring System"];
$battery_charger_products = $productGroups["Battery Charger"];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Amptronix</title>



 <style>

    html {
        scroll-behavior: smooth;
    }

header {
    height: 80px;
    padding: 0 7%;

    display: flex;
    align-items: center;
    justify-content: space-between;

    background: rgba(255, 255, 255, 0.95);

    position: sticky;
    top: 0;

    z-index: 1000;

    border-bottom: 1px solid #e8eef5;

    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}


.logo {
    display: flex;
    align-items: center;
}

.logo img {
    width: 180px;
    height: auto;
    display: block;
}

nav {

    display: flex;

    gap: 30px;

}


nav a {

    text-decoration: none;

    color: #42536a;

    font-size: 14px;

    font-weight: 600;

}


nav a:hover {

    color: #087cf2;

}


.quote {

    background: #087cf2;

    color: white;

    padding: 12px 18px;

    border-radius: 7px;

    text-decoration: none;

    font-size: 14px;

    font-weight: bold;

}
/* =========================
   HERO SECTION
========================= */

.hero {
    min-height: calc(100vh - 80px);

    padding: 55px 7% 70px;

    display: grid;

    grid-template-columns: 1fr 1fr;

    align-items: center;

    gap: 60px;

    background: #ffffff;

    overflow: hidden;
}


/* =========================
   LEFT CONTENT
========================= */

.hero-content {
    padding-left: 20px;

    animation: heroTextUp 0.9s ease-out both;
}


.hero-welcome {
    display: inline-block;

    color: #087cf2;

    font-size: 22px;

    font-weight: 800;

    letter-spacing: 4px;

    position: relative;
}


/* Blue line */

.hero-welcome::after {
    content: "";

    display: block;

    width: 60px;

    height: 3px;

    margin-top: 12px;

    background: #087cf2;

    border-radius: 10px;

    transition: width 0.4s ease;
}


.hero-welcome:hover::after {
    width: 110px;
}


/* =========================
   TAGLINE
========================= */

.hero-tagline {
    margin: 28px 0 35px;

    color: #10233d;

    font-size: 23px;

    font-weight: 600;

    line-height: 1.5;
}


/* =========================
   HIGHLIGHTS
========================= */

.hero-highlights {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 14px;

    max-width: 620px;
}


/* Individual highlight */

.hero-highlight {
    display: flex;

    align-items: center;

    gap: 14px;

    padding: 15px;

    background: #f7fbff;

    border: 1px solid #e3edf6;

    border-radius: 12px;

    cursor: default;

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;

    animation: highlightUp 0.7s ease-out both;
}


/* Different animation delays */

.hero-highlight:nth-child(1) {
    animation-delay: 0.15s;
}

.hero-highlight:nth-child(2) {
    animation-delay: 0.25s;
}

.hero-highlight:nth-child(3) {
    animation-delay: 0.35s;
}

.hero-highlight:nth-child(4) {
    animation-delay: 0.45s;
}


/* Hover */

.hero-highlight:hover {
    transform: translateY(-6px);

    border-color: #bcdcff;

    box-shadow: 0 12px 28px rgba(8, 124, 242, 0.10);
}


/* Icon */

.highlight-icon {
    width: 40px;

    height: 40px;

    min-width: 40px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #e8f4ff;

    color: #087cf2;

    border-radius: 10px;

    font-size: 20px;

    font-weight: 700;

    transition:
        transform 0.35s ease,
        background 0.35s ease;
}


.hero-highlight:hover .highlight-icon {
    transform: translateY(-3px) rotate(5deg);

    background: #087cf2;

    color: white;
}


/* Highlight text */

.hero-highlight h3 {
    margin: 0 0 5px;

    color: #10233d;

    font-size: 12px;

    letter-spacing: 1px;
}


.hero-highlight p {
    margin: 0;

    color: #718096;

    font-size: 12px;

    line-height: 1.4;
}


/* =========================
   PRODUCT AREA
========================= */

.hero-product {
    height: 500px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #e7f1fa;

    border: 1px solid #d4e3f0;

    border-radius: 30px;

    position: relative;

    overflow: hidden;

    animation: heroProductUp 1s ease-out both;
    
    transition: all 0.4s ease;
}


/* Decorative circle */

.hero-product::before {
    content: "";

    position: absolute;

    width: 300px;

    height: 300px;

    border-radius: 50%;

    background: #e8f4ff;

    top: -100px;

    right: -70px;

    transition: transform 0.4s ease;
}


/* Product image */

.hero-product-image {
    width: 92%;

    height: 85%;

    display: flex;

    align-items: center;

    justify-content: center;

    position: relative;

    z-index: 2;

    animation: productFloat 4s ease-in-out infinite;
}


.hero-product-image img {
    width: 100%;
    max-width: 650px;
    height: auto;

    object-fit: contain;
    display: block;

    filter: drop-shadow(
        0 18px 20px rgba(20, 50, 80, 0.18)
    );

    transition:
        transform 0.5s ease,
        filter 0.5s ease;
}

.hero-product-image img:hover {
    transform: scale(1.03);

    filter: drop-shadow(
        0 22px 30px rgba(20, 50, 80, 0.22)
    );
}

/* Product hover */

.hero-product:hover .hero-product-image img {
    transform: scale(1.05) translateY(-6px);

    filter: drop-shadow(
        0 18px 25px rgba(16, 35, 61, 0.16)
    );
}


.hero-product:hover::before {
    transform: scale(1.15);
}


/* =========================
   ANIMATIONS
========================= */

@keyframes heroTextUp {

    from {
        opacity: 0;

        transform: translateY(35px);
    }

    to {
        opacity: 1;

        transform: translateY(0);
    }

}


@keyframes heroProductUp {

    from {
        opacity: 0;

        transform: translateY(45px);
    }

    to {
        opacity: 1;

        transform: translateY(0);
    }

}


@keyframes highlightUp {

    from {
        opacity: 0;

        transform: translateY(25px);
    }

    to {
        opacity: 1;

        transform: translateY(0);
    }

}


@keyframes productFloat {

    0% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-7px);
    }

    100% {
        transform: translateY(0);
    }

}

.device {

    width: 300px;

    height: 185px;

    background: linear-gradient(
        145deg,
        #ffffff,
        #d5dfe7
    );

    border-radius: 12px;

    padding: 20px;

    box-sizing: border-box;

    display: flex;

    flex-direction: column;

    justify-content: center;

    box-shadow:
        0 25px 40px rgba(0,0,0,.15);

    border: 1px solid #c8d3dd;

}

.device-top {

    color: #10233d;

    font-size: 15px;

    font-weight: 800;

    letter-spacing: 2px;

    margin-bottom: 18px;

}


.device-display {

    background: #10233d;

    color: #72b7ff;

    padding: 10px;

    border-radius: 5px;

    font-size: 11px;

    font-weight: bold;

    letter-spacing: 2px;

}


.device-lights {

    display: flex;

    gap: 8px;

    margin-top: 15px;

}


.device-lights span {

    width: 7px;

    height: 7px;

    background: #087cf2;

    border-radius: 50%;

}


.device-label {

    margin-top: 12px;

    color: #718096;

    font-size: 8px;

    letter-spacing: 1.5px;

}

/* =========================
   WHY AMPTRONIX
========================= */

.why {

    padding: 30px 7% 70px;

    background: white;

}

.why-container {
    max-width: none;
    width: 100%;
    margin: 0;

    display: grid;
    grid-template-columns: repeat(4, 1fr);

    border: 1px solid #dfe8f1;
    border-radius: 18px;
    overflow: hidden;

    background: #ffffff;

    box-shadow: 0 12px 35px rgba(16, 35, 61, 0.055);
}

.why-card {

    padding: 30px;

    border-right: 1px solid #e2e8ef;

    background: white;

}


.why-card:last-child {

    border-right: none;

}


.why-icon {

    width: 48px;

    height: 48px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #eaf5ff;

    color: #087cf2;

    border-radius: 10px;

    font-size: 22px;

    margin-bottom: 18px;

}


.why-card h3 {

    font-size: 17px;

    margin-bottom: 7px;

    color: #10233d;

}


.why-card p {

    font-size: 13px;

    color: #718096;

}

/* =========================
   COMPACT SECTION SPACING
========================= */

/* Hero */
.hero {
    min-height: 620px;
    padding: 70px 5%;
}

/* Why Amptronix */
.why {
    padding: 45px 5% 65px;
}

/* Solutions */
.solutions {
    padding: 65px 5%;
}

/* About */
.about {
    padding: 70px 5%;
}

/* Products */
.products {
    padding: 65px 5%;
}

/* Applications */
.applications {
    padding: 65px 5%;
}

/* Contact */
.contact-cta {
    margin: 20px 5% 60px;
}


/* Reduce heading margins */
.solutions-heading,
.products-heading,
.applications-heading {
    margin-bottom: 35px;
}

.about-content {
    margin-top: 0;
}


/* Keep cards visually connected */
.why-container,
.solutions-container,
.products-container,
.applications-container {
    margin-top: 25px;
}

 /* MOBILE FRIENDLY */

        @media(max-width: 900px) {

            nav {
                display: none;
            }

            .hero {
                grid-template-columns: 1fr;
            }

            .why-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .why-card:nth-child(2) {
                border-right: none;
            }

            .why-card:nth-child(1),
            .why-card:nth-child(2) {
                border-bottom: 1px solid #e2e8ef;
            }
            .solutions-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .solutions-heading h2 {
                font-size: 34px;
            }
            .about {

                grid-template-columns: 1fr;

                gap: 40px;

            }

            .about-image {

                height: 320px;

            }

            .about-content h2 {

                font-size: 34px;

            }

            .about-features {

                grid-template-columns: 1fr;

            }

            .products-container {

                grid-template-columns: repeat(2, 1fr);

            }

            .products-heading h2 {

                font-size: 34px;

            }

            .applications-container {

                grid-template-columns: 1fr;

            }

            .applications-heading h2 {

                font-size: 34px;

            }

            .contact-cta {

                margin: 20px 7% 60px;

                padding: 40px 30px;

                flex-direction: column;

                align-items: flex-start;

            }

            .contact-content h2 {

                font-size: 34px;

            }

            .contact-actions {

                flex-wrap: wrap;

            }

            .footer-container {

                grid-template-columns: 1fr;

                gap: 35px;

    	    }

            .footer-bottom {

                flex-direction: column;

            }

            .hero h1 {

                font-size: 42px;

            }

            .hero-product {

                height: 320px;

            }

/* =========================
   SMALL MOBILE DEVICES
========================= */

@media(max-width: 600px) {

    header {
        height: 65px;
        padding: 0 5%;
    }

    .logo {
        font-size: 20px;
    }

    .quote {
        padding: 9px 12px;
        font-size: 11px;
    }


   .hero {
    min-height: auto;

    padding: 60px 5%;

    grid-template-columns: 1fr;

    gap: 40px;
}


.hero-content {
    padding-left: 0;

    text-align: center;
}


.hero-welcome {
    font-size: 17px;

    letter-spacing: 3px;
}


.hero-welcome::after {
    margin-left: auto;

    margin-right: auto;
}


.hero-tagline {
    font-size: 20px;

    margin: 25px 0 30px;
}


.hero-highlights {
    grid-template-columns: 1fr;

    max-width: 100%;

    text-align: left;
}


.hero-product {
    height: 330px;

    border-radius: 22px;
}


.hero-product-image {
    width: 95%;

    height: 90%;
}


.hero-product-image img {
    max-height: 270px;
}

    .device {
        width: 260px;
        height: 165px;
    }


    .why {
        padding: 20px 5% 50px;
    }

    .why-container {
        grid-template-columns: 1fr;
    }

    .why-card {
        border-right: none;
        border-bottom: 1px solid #e2e8ef;
    }

    .why-card:last-child {
        border-bottom: none;
    }


    .solutions,
    .about,
    .products,
    .applications {
        padding: 60px 5%;
    }


    .solutions-container,
    .products-container {
        grid-template-columns: 1fr;
    }

    .about-container {
        grid-template-columns: 1fr;
    }


    .solutions-heading h2,
    .products-heading h2,
    .applications-heading h2,
    .about-content h2 {
        font-size: 32px;
    }


    .about-image {
        height: 280px;
    }

    .about-box {
        width: 190px;
        height: 190px;
    }

    .about-number {
        font-size: 45px;
    }


    .application-card {
        padding: 22px;
    }


    .contact-cta {
        margin: 10px 5% 50px;
        padding: 35px 25px;
    }

    .contact-content h2 {
        font-size: 32px;
    }


    .footer-container {
        gap: 30px;
    }

    footer {
        padding: 50px 5% 0;
    }

}
        }

/* =========================
   OUR SOLUTIONS
========================= */

.solutions {

    padding: 80px 7%;

    background: #f8fbff;

}


.solutions-heading {

    max-width: 650px;

    margin-bottom: 45px;

}


.solutions-heading h2 {

    font-size: 40px;

    color: #10233d;

    margin: 10px 0 15px;

}


.solutions-heading h2 span {

    color: #087cf2;

}


.solutions-heading > p:last-child {

    color: #718096;

    font-size: 16px;

    line-height: 1.6;

}


.solutions-container {

    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 20px;

}


.solution-card {

    background: white;

    padding: 30px;

    border: 1px solid #e2e8ef;

    border-radius: 14px;

    transition: 0.3s;

}


.solution-card:hover {

    transform: translateY(-6px);

    box-shadow: 0 15px 35px rgba(0,0,0,.08);

}


.solution-icon {

    width: 52px;

    height: 52px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #eaf5ff;

    color: #087cf2;

    border-radius: 12px;

    font-size: 24px;

    margin-bottom: 22px;

}


.solution-card h3 {

    color: #10233d;

    font-size: 19px;

    margin-bottom: 12px;

}


.solution-card p {

    color: #718096;

    font-size: 14px;

    line-height: 1.7;

    margin-bottom: 20px;

}


.solution-card a {

    color: #087cf2;

    text-decoration: none;

    font-size: 14px;

    font-weight: bold;

}

.solution-card {
    min-height: 300px;
}

.solutions-container {
    gap: 15px;
}
/* =========================
   ABOUT SECTION
========================= */

.about {
    padding: 100px 7%;
    background: #ffffff;
}

.about-container {
    max-width: 1250px;
    margin: 0 auto;

    display: grid;
    grid-template-columns: 0.9fr 1.1fr;

    gap: 90px;
    align-items: center;
}


/* =========================
   LEFT VISUAL
========================= */

.about-visual {
    position: relative;
    height: 540px;

    display: flex;
    align-items: center;
    justify-content: center;
}

.about-bg {
    position: absolute;

    width: 92%;
    height: 90%;

    background: linear-gradient(
        145deg,
        #eaf6ff,
        #f7fbff
    );

    border-radius: 28px;

    top: 25px;
    left: 0;
}

.about-year-card {
    position: relative;
    z-index: 2;

    width: 440px;
    height: 320px;

    background: white;

    border: 1px solid #dce8f2;
    border-radius: 22px;

    display: flex;
    align-items: center;
    justify-content: center;

    box-shadow: 0 25px 60px rgba(16, 35, 61, 0.12);

    padding: 18px;
    overflow: hidden;
}

.about-company-logo {
    width: 100%;
    height: 100%;

    object-fit: contain;

    display: block;
}

/* =========================
   RIGHT CONTENT
========================= */

.about-content {
    max-width: 650px;
}

.about-tag {
    color: #087cf2;

    font-size: 12px;
    font-weight: 700;

    letter-spacing: 2.5px;
}

.about-content h2 {
    margin: 15px 0 25px;

    color: #10233d;

    font-size: 46px;
    line-height: 1.12;
}

.about-content h2 span {
    color: #087cf2;
}

.about-content p {
    color: #607086;

    font-size: 15px;
    line-height: 1.8;

    margin-bottom: 18px;
}

.about-intro {
    font-size: 17px !important;
    color: #33465d !important;
}


/* =========================
   SPECIALIZATION
========================= */

.specialization-box {
    margin-top: 30px;

    padding: 25px;

    background: #f5f9fd;

    border: 1px solid #e1ebf4;

    border-radius: 14px;
}

.specialization-title {
    margin-bottom: 20px;

    color: #10233d;

    font-size: 13px;
    font-weight: 700;

    letter-spacing: 1.8px;
}

.specialization-items {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 20px;
}

.specialization-items div {
    display: flex;
    flex-direction: column;

    gap: 5px;
}

.specialization-items strong {
    color: #087cf2;

    font-size: 15px;
}

.specialization-items span {
    color: #718096;

    font-size: 12px;
}


/* =========================
   BOTTOM FEATURES
========================= */

.about-bottom {
    display: flex;

    flex-wrap: wrap;

    gap: 20px;

    margin-top: 25px;
}

.about-bottom div {
    display: flex;

    align-items: center;

    gap: 8px;

    color: #33465d;

    font-size: 13px;
    font-weight: 600;
}

.about-bottom span {
    width: 22px;
    height: 22px;

    display: flex;

    align-items: center;
    justify-content: center;

    background: #087cf2;

    color: white;

    border-radius: 50%;

    font-size: 11px;
}
/* =========================
   FEATURED PRODUCTS
========================= */

.products {

    padding: 90px 7%;

    background: #f8fbff;

}


.products-heading {

    max-width: 650px;

    margin-bottom: 45px;

}


.products-heading h2 {

    font-size: 40px;

    color: #10233d;

    margin: 10px 0 15px;

}


.products-heading h2 span {

    color: #087cf2;

}


.products-heading > p:last-child {

    color: #718096;

    font-size: 16px;

    line-height: 1.6;

}


.products-container {

    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 20px;

}


.product-card {

    background: white;

    border: 1px solid #e2e8ef;

    border-radius: 14px;

    overflow: hidden;

    transition: 0.3s;

}


.product-card:hover {

    transform: translateY(-6px);

    box-shadow: 0 15px 35px rgba(0,0,0,.08);

}


.product-image {

    height: 180px;

    background: #eaf6ff;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #087cf2;

    font-size: 24px;

    font-weight: 800;

    letter-spacing: 2px;

}


.product-info {

    padding: 25px;

}


.product-category {

    color: #087cf2;

    font-size: 10px;

    font-weight: bold;

    letter-spacing: 1.5px;

    margin-bottom: 10px;

}


.product-info h3 {

    color: #10233d;

    font-size: 18px;

    margin-bottom: 12px;

}


.product-info > p:not(.product-category) {

    color: #718096;

    font-size: 13px;

    line-height: 1.7;

    margin-bottom: 18px;

}


.product-info a {

    color: #087cf2;

    text-decoration: none;

    font-size: 13px;

    font-weight: bold;

}


.products-button {

    text-align: center;

    margin-top: 40px;

}

/* =========================
   APPLICATIONS
========================= */

.applications {

    padding: 90px 7%;

    background: white;

}


.applications-heading {

    max-width: 650px;

    margin-bottom: 45px;

}


.applications-heading h2 {

    font-size: 40px;

    color: #10233d;

    margin: 10px 0 15px;

}


.applications-heading h2 span {

    color: #087cf2;

}


.applications-heading > p:last-child {

    color: #718096;

    font-size: 16px;

    line-height: 1.6;

}


.applications-container {

    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 20px;

}


.application-card {

    display: flex;

    align-items: flex-start;

    gap: 20px;

    padding: 30px;

    background: #f8fbff;

    border: 1px solid #e2e8ef;

    border-radius: 14px;

    transition: 0.3s;

}


.application-card:hover {

    transform: translateY(-5px);

    box-shadow: 0 15px 35px rgba(0,0,0,.07);

}


.application-icon {

    min-width: 55px;

    height: 55px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #eaf5ff;

    color: #087cf2;

    border-radius: 12px;

    font-size: 24px;

}


.application-card h3 {

    color: #10233d;

    font-size: 18px;

    margin-bottom: 8px;

}


.application-card p {

    color: #718096;

    font-size: 14px;

    line-height: 1.7;

}

/* =========================
   CONTACT CTA
========================= */

.contact-cta {

    margin: 20px 7% 90px;

    padding: 55px 60px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 40px;

    background: #10233d;

    border-radius: 18px;

}


.contact-content {

    max-width: 650px;

}


.contact-content .small-title {

    color: #72b7ff;

}


.contact-content h2 {

    color: white;

    font-size: 40px;

    margin: 10px 0 15px;

}


.contact-content h2 span {

    color: #72b7ff;

}


.contact-content > p:last-child {

    color: #c4d0df;

    font-size: 15px;

    line-height: 1.7;

}


.contact-actions {

    display: flex;

    gap: 12px;

    flex-shrink: 0;

}


.contact-actions .secondary {

    border-color: #6d829b;

    color: white;

}

/* =========================
   FOOTER
========================= */

footer {

    background: #0b1b2e;

    color: white;

    padding: 60px 7% 0;

}


.footer-container {

    display: grid;

    grid-template-columns: 1.5fr 1fr 1fr;

    gap: 60px;

    padding-bottom: 50px;

}


.footer-logo {

    font-size: 24px;

    font-weight: 800;

    letter-spacing: 1px;

    margin-bottom: 18px;

}


.footer-brand p {

    color: #9eafc2;

    font-size: 14px;

    line-height: 1.7;

    max-width: 300px;

}


.footer-links {

    display: flex;

    flex-direction: column;

    gap: 10px;

}


.footer-links h3 {

    font-size: 16px;

    margin-bottom: 10px;

}


.footer-links a {

    color: #9eafc2;

    text-decoration: none;

    font-size: 14px;

}


.footer-links a:hover {

    color: #72b7ff;

}


.footer-links p {

    color: #9eafc2;

    font-size: 14px;

    margin: 0;

    line-height: 1.5;

}


.footer-bottom {

    border-top: 1px solid #24374c;

    padding: 20px 0;

    display: flex;

    justify-content: space-between;

    gap: 20px;

}


.footer-bottom p {

    color: #71869d;

    font-size: 12px;

    margin: 0;

}

@media (max-width: 600px) {

    /* PAGE WIDTH */
    header,
    .hero,
    .why,
    .solutions,
    .about,
    .products,
    .applications {
        width: 100%;
        box-sizing: border-box;
    }


    /* HEADER */
    header {
        padding: 0 20px;
    }


    /* HERO */
    .hero {
        padding: 55px 20px;
        grid-template-columns: 1fr;
        text-align: left;
    }

    .hero h1 {
        font-size: 38px;
        line-height: 1.1;
    }

    .description {
        font-size: 15px;
        line-height: 1.7;
    }

    .hero-product {
        width: 100%;
        height: 280px;
    }


    /* WHY */
    .why {
        padding: 25px 20px 55px;
    }

    .why-container {
        grid-template-columns: 1fr;
    }

    .why-card {
        border-right: none;
        border-bottom: 1px solid #e2e8ef;
        padding: 25px 10px;
    }


    /* SOLUTIONS */
    .solutions {
        padding: 60px 20px;
    }

    .solutions-container {
        grid-template-columns: 1fr !important;
        gap: 18px;
    }

    .solution-card {
        width: 100%;
        min-height: auto;
        box-sizing: border-box;
    }


    /* ABOUT */
    .about {
        padding: 60px 20px;
    }

    .about-container {
        grid-template-columns: 1fr !important;
        gap: 40px;
    }

    .about-image {
        width: 100%;
        height: 280px;
    }


    /* PRODUCTS */
    .products {
        padding: 60px 20px;
    }

    .products-container {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }

    .product-card {
        width: 100%;
        box-sizing: border-box;
    }


    /* APPLICATIONS */
    .applications {
        padding: 60px 20px;
    }

    .applications-container {
        grid-template-columns: 1fr;
    }


    /* CONTACT */
    .contact-cta {
        margin: 10px 20px 50px;
        padding: 40px 25px;
        box-sizing: border-box;
    }


    /* FOOTER */
    footer {
        padding: 50px 20px 0;
    }

    .footer-container {
        grid-template-columns: 1fr;
        gap: 35px;
    }

}

.product-image {
    padding: 0;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.model-no {
    margin-top: -10px;
    margin-bottom: 18px;
    color: #087cf2;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.logo img {
    width: 180px;
}
.about-features {
    grid-template-columns: 1fr;
}

.specialization {
    padding: 18px;
}

/* =========================
   SMOOTH UI / UX
========================= */

html {
    scroll-behavior: smooth;
}


/* =========================
   SECTION REVEAL
========================= */

section {
    scroll-margin-top: 80px;
}


/* =========================
   SMOOTH CARDS
========================= */

.why-card,
.solution-card,
.product-card,
.application-card {
    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}

.why-card:hover,
.solution-card:hover,
.product-card:hover,
.application-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(16, 35, 61, 0.10);
}


/* =========================
   BUTTON ANIMATION
========================= */

.btn,
.quote {
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        background 0.25s ease;
}

.btn:hover,
.quote:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(8, 124, 242, 0.18);
}


/* =========================
   PRODUCT IMAGE SMOOTHNESS
========================= */

.product-image img {
    transition: transform 0.45s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.04);
}


/* =========================
   NAVIGATION
========================= */

nav a {
    transition:
        color 0.25s ease,
        opacity 0.25s ease;
}

nav a:hover {
    color: #087cf2;
}


/* =========================
   IMAGE HOVER
========================= */

.about-company-logo {
    transition: transform 0.5s ease;
}

.about-year-card:hover .about-company-logo {
    transform: scale(1.03);
}


/* =========================
   SPECIALIZATION
========================= */

.specialization-box {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

.specialization-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(16, 35, 61, 0.08);
}


/* =========================
   CONTACT
========================= */

.contact-cta {
    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease;
}

.contact-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 45px rgba(16, 35, 61, 0.10);
}

html {
    scroll-behavior: smooth;
    scroll-padding-top: 100px;
}

/* =========================
   FINAL SPACING FIX
========================= */

.hero {
    min-height: 560px !important;
    padding: 40px 7% !important;
}

.why {
    padding: 25px 7% 45px !important;
}

.solutions {
    padding: 50px 7% !important;
}

.about {
    padding: 55px 7% !important;
}

.products {
    padding: 50px 7% !important;
}

.applications {
    padding: 50px 7% !important;
}

.contact-cta {
    margin-top: 15px !important;
    margin-bottom: 45px !important;
}


/* Reduce gaps inside sections */

.solutions-heading {
    margin-bottom: 25px !important;
}

.products-heading {
    margin-bottom: 25px !important;
}

.applications-heading {
    margin-bottom: 25px !important;
}


/* Keep content closer together */

.solutions-container,
.products-container,
.applications-container {
    margin-top: 0 !important;
}


/* About section */

.about-container {
    gap: 60px !important;
}

/* =========================
   PREMIUM HERO PRODUCT
========================= */

.hero-product {
    position: relative;
    overflow: hidden;

    background: #eef6fd;

    border: 1px solid #d7e7f5;
    border-radius: 28px;

    min-height: 560px;

    display: flex;
    align-items: center;
    justify-content: center;

    box-shadow: 0 20px 50px rgba(20, 60, 100, 0.08);

    transition: transform 0.4s ease,
                box-shadow 0.4s ease;
}


/* Hover effect */

.hero-product:hover {
    transform: translateY(-8px);

    box-shadow: 0 30px 70px rgba(20, 60, 100, 0.15);
}


/* Soft background glow */

.product-glow {
    position: absolute;

    width: 300px;
    height: 300px;

    background: #dceefe;

    border-radius: 50%;

    top: -100px;
    right: -80px;

    opacity: 0.8;
}


/* Product image area */

.hero-product-image {
    position: relative;
    z-index: 2;

    width: 90%;

    display: flex;
    align-items: center;
    justify-content: center;

    transition: transform 0.5s ease;
}


/* Product moves slightly upward */

.hero-product:hover .hero-product-image {
    transform: translateY(-10px);
}


/* Actual product image */

.hero-product-image img {
    width: 100%;
    max-width: 650px;

    height: auto;

    object-fit: contain;

    display: block;

    filter: drop-shadow(0 18px 20px rgba(20, 50, 80, 0.18));

    transition: transform 0.5s ease;
}


/* Product information */

.hero-product-label {
    position: absolute;

    left: 35px;
    bottom: 30px;

    z-index: 3;

    display: flex;
    flex-direction: column;

    gap: 5px;

    background: rgba(255, 255, 255, 0.92);

    padding: 14px 18px;

    border-radius: 12px;

    border: 1px solid #dce8f2;

    box-shadow: 0 10px 30px rgba(20, 50, 80, 0.08);

    transition: transform 0.4s ease;
}


/* Label hover */

.hero-product:hover .hero-product-label {
    transform: translateY(-5px);
}


/* Small title */

.hero-product-label span {
    font-size: 10px;

    letter-spacing: 2px;

    color: #087cf2;

    font-weight: bold;
}


/* Product name */

.hero-product-label strong {
    font-size: 16px;

    color: #10233d;
}


/* Model number */

.hero-product-label small {
    color: #6c7f95;

    font-size: 12px;
}


/* =========================
   MOBILE
========================= */

@media(max-width: 600px) {

    .hero-product {
        min-height: 360px;

        border-radius: 20px;
    }

    .hero-product-image {
        width: 90%;
    }

    .hero-product-label {
        left: 18px;
        bottom: 18px;
    }

}

/* =========================
   PREMIUM NAVIGATION
========================= */

nav a {
    position: relative;
    transition: color 0.3s ease;
}

nav a::after {
    content: "";

    position: absolute;

    left: 50%;
    bottom: -8px;

    width: 0;
    height: 2px;

    background: #087cf2;

    border-radius: 10px;

    transform: translateX(-50%);

    transition: width 0.3s ease;
}

nav a:hover {
    color: #087cf2;
}

nav a:hover::after {
    width: 70%;
}


/* Quote button */

.quote {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

.quote:hover {
    transform: translateY(-3px);

    box-shadow: 0 10px 25px rgba(8, 124, 242, 0.22);
}

/* =========================
   PREMIUM HEADER
========================= */

header {
    transition:
        box-shadow 0.3s ease,
        background 0.3s ease;
}

header:hover {
    box-shadow: 0 8px 30px rgba(16, 35, 61, 0.06);
}

.logo img {
    transition: transform 0.3s ease;
}

.logo img:hover {
    transform: scale(1.04);
}

nav {
    display: flex;
    align-items: center;
    gap: 30px;
}

nav a {
    font-weight: 500;
    letter-spacing: 0.2px;
}

/* =========================
   HERO WELCOME ANIMATION
========================= */

.hero-welcome {
    display: inline-block;

    font-size: 52px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #10233d;

    animation: heroWelcomeUp 0.9s ease forwards;
}

@keyframes heroWelcomeUp {
    from {
        opacity: 0;
        transform: translateY(35px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* =========================
   HERO TAGLINE ANIMATION
========================= */

.hero-tagline {
    animation: heroTaglineUp 1s ease forwards;
    animation-delay: 0.25s;
    opacity: 0;
}

@keyframes heroTaglineUp {
    from {
        opacity: 0;
        transform: translateY(25px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* =========================
   HERO PRODUCT ANIMATION
========================= */

.hero-product {
    animation: heroProductUp 1s ease forwards;
    animation-delay: 0.2s;
    opacity: 0;
}

@keyframes heroProductUp {
    from {
        opacity: 0;
        transform: translateY(45px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* =========================
   HERO HIGHLIGHTS HOVER
========================= */

.hero-highlight {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        border-color 0.3s ease;
}

.hero-highlight:hover {
    transform: translateY(-6px);

    box-shadow: 0 12px 30px rgba(16, 35, 61, 0.10);

    border-color: #c7dff5;
}

.highlight-icon {
    transition:
        transform 0.3s ease,
        background 0.3s ease;
}

.hero-highlight:hover .highlight-icon {
    transform: scale(1.08);
}

/* =========================
   WHY AMPTRONIX CARD HOVER
========================= */

.why-card {
    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}

.why-card:hover {
    transform: translateY(-8px);

    box-shadow: 0 18px 40px rgba(16, 35, 61, 0.10);

    border-color: #c7dff5;
}

.why-card h3 {
    transition: color 0.3s ease;
}

.why-card:hover h3 {
    color: #087cf2;
}

/* =========================
   SOLUTIONS CARD HOVER
========================= */

.solution-card {
    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}

.solution-card:hover {
    transform: translateY(-8px);

    box-shadow: 0 18px 40px rgba(16, 35, 61, 0.10);

    border-color: #c7dff5;
}

.solution-card h3 {
    transition: color 0.3s ease;
}

.solution-card:hover h3 {
    color: #087cf2;
}

/* =========================
   PRODUCT CARD HOVER
========================= */

.product-card {
    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}

.product-card:hover {
    transform: translateY(-8px);

    box-shadow: 0 18px 40px rgba(16, 35, 61, 0.12);

    border-color: #c7dff5;
}

.product-image {
    overflow: hidden;
}

.product-image img {
    transition:
        transform 0.45s ease,
        filter 0.45s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-card h3 {
    transition: color 0.3s ease;
}

.product-card:hover h3 {
    color: #087cf2;
}

/* =========================
   APPLICATION CARD HOVER
========================= */

.application-card {
    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}

.application-card:hover {
    transform: translateY(-7px);

    box-shadow: 0 18px 40px rgba(16, 35, 61, 0.10);

    border-color: #c7dff5;
}

.application-card h3 {
    transition: color 0.3s ease;
}

.application-card:hover h3 {
    color: #087cf2;
}

/* =========================
   ABOUT IMAGE HOVER
========================= */

.about-year-card {
    transition:
        transform 0.4s ease,
        box-shadow 0.4s ease;
}

.about-year-card:hover {
    transform: translateY(-8px);

    box-shadow: 0 30px 65px rgba(16, 35, 61, 0.15);
}

.about-company-logo {
    transition: transform 0.5s ease;
}

.about-year-card:hover .about-company-logo {
    transform: scale(1.03);
}

/* =========================
   CONTACT SECTION
========================= */

.contact-cta {
    transition:
        transform 0.4s ease,
        box-shadow 0.4s ease;
}

.contact-cta:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(16, 35, 61, 0.10);
}

.contact-cta h2 {
    transition: transform 0.3s ease;
}

.contact-cta:hover h2 {
    transform: translateY(-2px);
}

.contact-cta a {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

.contact-cta a:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(8, 124, 242, 0.20);
}

/* =========================
   PREMIUM CONTACT SECTION
========================= */

.contact-section {
    padding: 80px 7%;
    background: #f7fbff;
}

.contact-container {
    max-width: 1200px;
    margin: 0 auto;

    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 70px;

    align-items: center;
}


/* =========================
   CONTACT LEFT
========================= */

.contact-info {
    padding: 20px 0;
}

.contact-label {
    display: inline-block;

    font-size: 13px;
    font-weight: 700;
    letter-spacing: 2px;

    color: #087cf2;

    margin-bottom: 18px;
}

.contact-info h2 {
    margin: 0;

    font-size: 48px;
    line-height: 1.15;

    color: #10233d;
}

.contact-info h2 span {
    color: #087cf2;
}

.contact-info > p {
    margin-top: 22px;

    max-width: 540px;

    font-size: 16px;
    line-height: 1.8;

    color: #65788d;
}


/* =========================
   CONTACT DETAILS
========================= */

.contact-details {
    margin-top: 35px;

    display: flex;
    flex-direction: column;
    gap: 18px;
}

.contact-detail {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.contact-detail strong {
    font-size: 13px;

    text-transform: uppercase;
    letter-spacing: 1px;

    color: #10233d;
}

.contact-detail a {
    width: fit-content;

    color: #087cf2;

    text-decoration: none;

    transition: color 0.3s ease;
}

.contact-detail a:hover {
    color: #10233d;
}


/* =========================
   SOCIAL MEDIA
========================= */

.social-section {
    margin-top: 38px;
}

.social-section h3 {
    margin-bottom: 15px;

    font-size: 15px;
    color: #10233d;
}

.social-icons {
    display: flex;
    gap: 12px;
}

.social-icon {
    width: 45px;
    height: 45px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: white;
    border: 1px solid #dce8f2;

    color: #10233d;

    text-decoration: none;

    font-size: 20px;
    font-weight: 600;

    transition:
        transform 0.3s ease,
        background 0.3s ease,
        color 0.3s ease,
        box-shadow 0.3s ease;
}

.social-icon:hover {
    transform: translateY(-5px);

    background: #087cf2;
    color: white;

    box-shadow: 0 10px 25px rgba(8, 124, 242, 0.20);
}


/* =========================
   CONTACT FORM
========================= */

.contact-form-box {
    background: white;

    padding: 38px;

    border-radius: 24px;

    border: 1px solid #dce8f2;

    box-shadow: 0 20px 50px rgba(16, 35, 61, 0.08);

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease;
}

.contact-form-box:hover {
    transform: translateY(-5px);

    box-shadow: 0 28px 60px rgba(16, 35, 61, 0.12);
}


/* =========================
   FORM ROW
========================= */

.form-row {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 18px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;

    margin-bottom: 8px;

    font-size: 13px;
    font-weight: 600;

    color: #10233d;
}

.form-group input,
.form-group textarea {
    width: 100%;

    box-sizing: border-box;

    padding: 14px 15px;

    font-family: inherit;
    font-size: 14px;

    color: #10233d;

    background: #f8fbfe;

    border: 1px solid #dce8f2;

    border-radius: 10px;

    outline: none;

    transition:
        border-color 0.3s ease,
        box-shadow 0.3s ease,
        background 0.3s ease;
}

.form-group input {
    height: 48px;
}

.form-group textarea {
    resize: vertical;

    min-height: 140px;
}

.form-group input:focus,
.form-group textarea:focus {
    background: white;

    border-color: #087cf2;

    box-shadow: 0 0 0 3px rgba(8, 124, 242, 0.10);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #9aabba;
}


/* =========================
   SUBMIT BUTTON
========================= */

.contact-submit {
    width: 100%;

    height: 52px;

    border: none;
    border-radius: 10px;

    background: #087cf2;

    color: white;

    font-size: 15px;
    font-weight: 600;

    cursor: pointer;

    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;

    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        background 0.3s ease;
}

.contact-submit span {
    font-size: 20px;

    transition: transform 0.3s ease;
}

.contact-submit:hover {
    background: #066ed8;

    transform: translateY(-3px);

    box-shadow: 0 12px 28px rgba(8, 124, 242, 0.25);
}

.contact-submit:hover span {
    transform: translateX(5px);
}


/* =========================
   MOBILE
========================= */

@media (max-width: 600px) {

    .contact-container {
        grid-template-columns: 1fr;

        gap: 40px;
    }

    .contact-info h2 {
        font-size: 40px;
    }

}


@media (max-width: 600px) {

    .contact-section {
        padding: 55px 6%;
    }

    .contact-info h2 {
        font-size: 34px;
    }

    .contact-form-box {
        padding: 25px;
        border-radius: 18px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .social-icon {
        width: 42px;
        height: 42px;
    }

}

/* =========================
   PREMIUM FOOTER
========================= */

.site-footer {
    background: #10233d;
    color: white;
    padding: 70px 7% 0;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;

    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1.3fr;
    gap: 50px;
}


/* COMPANY */

.footer-company {
    max-width: 320px;
}

.footer-logo {
    width: 180px;
    height: auto;
    margin-bottom: 20px;

    background: white;
    padding: 8px 12px;
    border-radius: 8px;
}

.footer-company p {
    color: #b8c7d8;
    line-height: 1.7;
    font-size: 14px;
}


/* HEADINGS */

.footer-links h3,
.footer-contact h3 {
    margin: 0 0 20px;

    font-size: 15px;
    color: white;
}


/* LINKS */

.footer-links,
.footer-contact {
    display: flex;
    flex-direction: column;
}

.footer-links a,
.footer-contact a {
    width: fit-content;

    margin-bottom: 12px;

    color: #b8c7d8;

    text-decoration: none;

    font-size: 14px;

    transition:
        color 0.3s ease,
        transform 0.3s ease;
}

.footer-links a:hover,
.footer-contact a:hover {
    color: #66b2ff;
    transform: translateX(4px);
}


/* ADDRESS */

.footer-contact p {
    margin: 0 0 18px;

    color: #b8c7d8;

    font-size: 14px;
    line-height: 1.7;
}


/* BOTTOM */

.footer-bottom {
    max-width: 1200px;

    margin: 55px auto 0;

    padding: 22px 0;

    border-top: 1px solid rgba(255,255,255,0.12);

    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.footer-bottom p {
    margin: 0;

    color: #91a5ba;

    font-size: 12px;
}


/* =========================
   FOOTER MOBILE
========================= */

@media (max-width: 900px) {

    .footer-container {
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

}


@media (max-width: 600px) {

    .site-footer {
        padding: 50px 6% 0;
    }

    .footer-container {
        grid-template-columns: 1fr;
        gap: 35px;
    }

    .footer-company {
        max-width: 100%;
    }

    .footer-bottom {
        flex-direction: column;
        gap: 8px;
        margin-top: 40px;
    }

}

/* =========================
   FINAL MOBILE IMPROVEMENTS
========================= */

@media (max-width: 600px) {

    /* Header */

    header {
        padding: 0 5%;
    }

    .logo img {
        max-width: 150px;
    }

    nav {
        gap: 12px;
    }


    /* Hero */

    .hero-welcome {
        font-size: 34px;
    }

    .hero-tagline {
        font-size: 15px;
    }


    /* Contact */

    .contact-info > p {
        font-size: 14px;
    }

    .contact-form-box {
        width: 100%;
        box-sizing: border-box;
    }


    /* Footer */

    .footer-logo {
        width: 160px;
    }

}

/* =========================
   MOBILE HAMBURGER MENU
========================= */

.menu-toggle {
    display: block;

    position: relative;
    z-index: 2000;

    border: none;
    background: transparent;

    font-size: 30px;
    line-height: 1;

    color: #10233d;

    cursor: pointer;

    padding: 8px;

    pointer-events: auto;
}

/* MOBILE */

@media (max-width: 600px) {

    header {
        position: sticky;
    }

    .menu-toggle {
        display: block;
        z-index: 1100;
    }

    header nav {
        position: absolute;

        top: 80px;
        left: 0;
        right: 0;

        display: none;

        flex-direction: column;
        align-items: center;

        gap: 0;

        background: white;

        border-top: 1px solid #e8eef5;
        border-bottom: 1px solid #e8eef5;

        box-shadow: 0 15px 30px rgba(16, 35, 61, 0.10);

        padding: 15px 0;
    }

    header nav.active {
        display: flex;
    }

    header nav a {
        width: 100%;

        padding: 14px 0;

        text-align: center;
    }

    header nav .quote {
        width: auto;

        margin: 8px 0;
    }

}

/* =========================
   MOBILE MENU ANIMATION
========================= */

@media (max-width: 600px) {

    header nav {
        position: absolute;

        top: 80px;
        left: 0;
        right: 0;

        display: flex;
        flex-direction: column;
        align-items: center;

        gap: 0;

        background: white;

        border-top: 1px solid #e8eef5;
        border-bottom: 1px solid #e8eef5;

        box-shadow: 0 15px 30px rgba(16, 35, 61, 0.10);

        padding: 15px 0;

        /* Animation */
        opacity: 0;
        visibility: hidden;

        transform: translateY(-15px);

        transition:
            opacity 0.3s ease,
            transform 0.3s ease,
            visibility 0.3s ease;
    }

    header nav.active {
        opacity: 1;
        visibility: visible;

        transform: translateY(0);
    }

    header nav a {
        width: 100%;

        padding: 14px 0;

        text-align: center;
    }

    header nav .quote {
        width: auto;

        margin: 8px 0;
    }

}

/* =========================
   ABOUT SECTION - MOBILE
========================= */

@media (max-width: 600px) {

    .about {
        padding: 45px 6% !important;
    }

    .about-container {
        gap: 30px !important;
    }

    .about-visual {
        height: 280px !important;
    }

    .about-year-card {
        width: 92% !important;
        height: 230px !important;
        padding: 10px !important;
    }

    .about-company-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .about-content {
        margin-top: 0 !important;
    }

    .about-content h2 {
        margin-bottom: 15px;
    }

    .about-content p {
        margin-bottom: 12px;
    }

}

/* =========================
   HERO - MOBILE BALANCE
========================= */

@media (max-width: 600px) {

    .hero {
        min-height: auto !important;
        padding: 45px 6% 55px !important;

        gap: 35px;
    }

    .hero-content {
        text-align: center;
    }

    .hero-welcome {
        font-size: 32px;
        line-height: 1.2;
    }

    .hero-tagline {
        font-size: 15px;
        line-height: 1.6;
    }

    .hero-highlights {
        margin-top: 25px;
    }

    .hero-product {
        min-height: 330px !important;
    }

    .hero-product-image {
        width: 88%;
    }

    .hero-product-label {
        left: 15px;
        bottom: 15px;
    }

}

/* =========================
   PRODUCTS - MOBILE
========================= */

@media (max-width: 600px) {

    .products {
        padding: 45px 6% !important;
    }

    .products-container {
        grid-template-columns: 1fr !important;
        gap: 25px;
    }

    .product-card {
        width: 100%;
        box-sizing: border-box;
    }

    .product-image {
        height: 220px;
    }

    .product-image img {
        max-width: 85%;
        max-height: 190px;
        object-fit: contain;
    }

}

/* =========================
   APPLICATIONS - MOBILE
========================= */

@media (max-width: 600px) {

    .applications {
        padding: 45px 6% !important;
    }

    .applications-container {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }

    .application-card {
        width: 100%;
        box-sizing: border-box;
        padding: 22px;
    }

    .application-card h3 {
        font-size: 18px;
    }

    .application-card p {
        font-size: 14px;
        line-height: 1.6;
    }

}

/* =========================
   CONTACT FORM - MOBILE
========================= */

@media (max-width: 600px) {

    .contact-section {
        padding: 45px 6% !important;
    }

    .contact-container {
        gap: 30px !important;
    }

    .contact-info h2 {
        font-size: 32px !important;
        line-height: 1.2;
    }

    .contact-info > p {
        line-height: 1.7;
    }

    .contact-details {
        margin-top: 25px;
        gap: 15px;
    }

    .social-section {
        margin-top: 28px;
    }

    .social-icons {
        gap: 10px;
    }

    .contact-form-box {
        padding: 22px !important;
        border-radius: 18px;
    }

    .form-group {
        margin-bottom: 17px;
    }

    .form-group input {
        height: 46px;
    }

    .form-group textarea {
        min-height: 120px;
    }

    .contact-submit {
        height: 50px;
    }

}

/* =========================
   SCROLL REVEAL ANIMATION
========================= */

.scroll-reveal {
    opacity: 0;
    transform: translateY(60px);

    transition:
        opacity 0.8s ease,
        transform 0.8s ease;
}

.scroll-reveal.show {
    opacity: 1;
    transform: translateY(0);
}

/* MENU BUTTON - DESKTOP HIDE */

.menu-toggle {
    display: none !important;
}

/* MENU BUTTON - MOBILE SHOW */

@media (max-width: 600px) {
    .menu-toggle {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        font-size: 26px;
        border: none;
        background: transparent;
        color: #10233d;
        cursor: pointer;
        padding: 0;
    }
}

/* MOBILE HERO IMPROVEMENT */

@media (max-width: 600px) {

    .hero {
        min-height: auto !important;
        padding: 40px 6% 50px !important;
        gap: 30px !important;
    }

    .hero-content {
        text-align: center;
    }

    .hero-welcome {
        font-size: 30px !important;
        line-height: 1.2;
        letter-spacing: 0.5px;
    }

    .hero-tagline {
        font-size: 15px !important;
        line-height: 1.6;
        margin-top: 12px;
    }

    .hero-highlights {
        margin-top: 22px !important;
        gap: 12px !important;
    }

    .hero-highlight {
        padding: 14px !important;
        text-align: left;
    }

    .hero-highlight h3 {
        font-size: 13px !important;
    }

    .hero-highlight p {
        font-size: 12px !important;
        line-height: 1.4;
    }

    .highlight-icon {
        min-width: 38px;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-product {
        min-height: 320px !important;
        border-radius: 20px !important;
    }

    .hero-product-image {
        width: 88% !important;
    }

    .hero-product-label {
        left: 14px !important;
        bottom: 14px !important;
        padding: 10px 13px !important;
    }

    .hero-product-label strong {
        font-size: 13px !important;
    }

    .hero-product-label small {
        font-size: 11px !important;
    }
}

/* =========================
   APPLICATION IMAGE SLIDER
========================= */

.applications-slider {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
}


/* SLIDES */

.application-slide {
    display: none;

    grid-template-columns: repeat(4, 1fr);

    gap: 20px;

    animation: applicationSlideUp 0.6s ease;
}

.application-slide.active {
    display: grid;
}


/* =========================
   APPLICATION IMAGE CARD
========================= */

.application-image-card {
    width: 100%;
    aspect-ratio: 1228 / 2048;
    background: #f7fbff;
    border: 1px solid #dce8f2;
    border-radius: 18px;
    overflow: hidden;
    box-sizing: border-box;
    box-shadow: 0 8px 25px rgba(16, 35, 61, 0.06);

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;

    cursor: zoom-in;
}

/* IMAGE */

.application-image-card img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: contain;
    object-position: center;

    transition:
        transform 0.4s ease;
}

/* HOVER */

.application-image-card:hover {
    transform: translateY(-6px);

    border-color: #c7dff5;

    box-shadow:
        0 18px 40px rgba(16, 35, 61, 0.12);
}

.application-image-card:hover img {
    transform: scale(1.03);
}


/* =========================
   DESKTOP
========================= */

@media (min-width: 901px) {

    .application-slide {
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }

}


/* =========================
   TABLET
========================= */

@media (max-width: 900px) {

    .application-slide {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

}


/* =========================
   MOBILE
========================= */

@media (max-width: 600px) {

    .application-slide {
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .application-image-card {
        border-radius: 14px;
    }

}

/* =========================
   APPLICATION PREVIEW
========================= */

.application-preview {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.92);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    padding: 25px;
    box-sizing: border-box;
}

.application-preview.show {
    display: flex;
}

.application-preview-image {
    max-width: 90vw;
    max-height: 85vh;
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
    transform: scale(1);
    transition: transform 0.2s ease;
    cursor: zoom-in;
    user-select: none;
}


/* =========================
   CLOSE BUTTON
========================= */

.application-preview-close {
    position: absolute;
    top: 20px;
    right: 25px;
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 50%;
    background: white;
    color: #10233d;
    font-size: 30px;
    line-height: 48px;
    text-align: center;
    cursor: pointer;
    z-index: 100000;
    transition:
        background 0.25s ease,
        color 0.25s ease,
        transform 0.25s ease;
}

.application-preview-close:hover {
    background: #087cf2;
    color: white;
    transform: rotate(90deg);
}


/* =========================
   ZOOM CONTROLS
========================= */

.application-zoom-controls {
    position: absolute;
    left: 50%;
    bottom: 25px;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    background: white;
    border-radius: 12px;
    z-index: 100000;
}

.application-zoom-button {
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 8px;
    background: #f0f5fa;
    color: #10233d;
    font-size: 22px;
    font-weight: 700;
    cursor: pointer;
}

.application-zoom-button:hover {
    background: #087cf2;
    color: white;
}

.application-zoom-level {
    min-width: 55px;
    text-align: center;
    color: #10233d;
    font-size: 13px;
    font-weight: 700;
}


/* =========================
   MOBILE
========================= */

@media (max-width: 600px) {

    .application-image-card {
        aspect-ratio: 1228 / 2048;
        border-radius: 14px;
    }

    .application-preview {
        padding: 15px;
    }

    .application-preview-image {
        max-width: 94vw;
        max-height: 82vh;
    }

    .application-preview-close {
        top: 12px;
        right: 12px;
        width: 42px;
        height: 42px;
        line-height: 42px;
        font-size: 26px;
    }

    .application-zoom-controls {
        bottom: 15px;
    }

    .application-zoom-button {
        width: 40px;
        height: 40px;
    }
}

/* HOVER */

.application-image-card:hover {
    transform: translateY(-8px);

    border-color: #c7dff5;

    box-shadow:
        0 20px 45px rgba(16, 35, 61, 0.12);
}

.application-image-card:hover img {
    transform: scale(1.06);
}


/* CONTROLS */

.application-controls {
    display: flex;

    align-items: center;

    justify-content: center;

    gap: 25px;

    margin-top: 30px;
}


/* ARROWS */

.application-arrow {
    width: 44px;
    height: 44px;

    display: flex;

    align-items: center;

    justify-content: center;

    border: 1px solid #dce8f2;

    border-radius: 50%;

    background: white;

    color: #10233d;

    font-size: 20px;

    cursor: pointer;

    transition:
        transform 0.3s ease,
        background 0.3s ease,
        color 0.3s ease,
        box-shadow 0.3s ease;
}

.application-arrow:hover {
    transform: translateY(-3px);

    background: #087cf2;

    color: white;

    box-shadow:
        0 10px 25px rgba(8, 124, 242, 0.20);
}


/* DOTS */

.application-dots {
    display: flex;

    align-items: center;

    gap: 8px;
}

.application-dot {
    width: 8px;
    height: 8px;

    padding: 0;

    border: none;

    border-radius: 50%;

    background: #cbd8e5;

    cursor: pointer;

    transition:
        width 0.3s ease,
        background 0.3s ease;
}

.application-dot.active {
    width: 25px;

    border-radius: 10px;

    background: #087cf2;
}


/* SLIDE ANIMATION */

@keyframes applicationSlideUp {

    from {
        opacity: 0;

        transform: translateY(25px);
    }

    to {
        opacity: 1;

        transform: translateY(0);
    }

}


/* TABLET */

@media (max-width: 900px) {

    .application-slide {
        grid-template-columns: repeat(2, 1fr);
    }

    .application-image-card {
        height: 230px;
    }

}


/* MOBILE */

@media (max-width: 600px) {

    .applications {
        padding: 45px 6% !important;
    }

    .applications-heading {
        margin-bottom: 25px !important;
    }

    .application-slide {
        grid-template-columns: 1fr 1fr;

        gap: 12px;
    }

    .application-image-card {
        height: 160px;

        border-radius: 12px;
    }

    .application-controls {
        margin-top: 22px;

        gap: 18px;
    }

    .application-arrow {
        width: 40px;
        height: 40px;

        font-size: 18px;
    }

}

/* =========================
   PRODUCT SLIDER
========================= */

.products-slider {
    max-width: 1200px;
    min-height: 410px;
    margin: 0 auto;
}

.product-slide {
    display: none;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    width: 100%;
    box-sizing: border-box;
    animation: productSlideUp 0.6s ease;
}

.product-slide.active {
    display: grid;
}

/* PRODUCT CARD */

.product-slide .product-card {
    width: 100%;
    box-sizing: border-box;
}


/* PRODUCT IMAGE */

.product-slide .product-image {
    height: 220px;
    padding: 15px;
    background: #eaf6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.product-slide .product-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    transition: transform 0.45s ease;
}

.product-slide .product-card:hover
.product-image img {
    transform: scale(1.06);
}


/* CONTROLS */

.product-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 25px;
    margin-top: 30px;
}


/* ARROWS */

.product-arrow {
    width: 44px;
    height: 44px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #dce8f2;
    border-radius: 50%;

    background: white;
    color: #10233d;

    font-size: 20px;

    cursor: pointer;

    transition:
        transform 0.3s ease,
        background 0.3s ease,
        color 0.3s ease,
        box-shadow 0.3s ease;
}

.product-arrow:hover {
    transform: translateY(-3px);

    background: #087cf2;
    color: white;

    box-shadow:
        0 10px 25px rgba(8, 124, 242, 0.20);
}


/* DOTS */

.product-dots {
    display: flex;
    align-items: center;
    gap: 8px;
}

.product-dot {
    width: 8px;
    height: 8px;

    padding: 0;

    border: none;
    border-radius: 50%;

    background: #cbd8e5;

    cursor: pointer;

    transition:
        width 0.3s ease,
        background 0.3s ease;
}

.product-dot.active {
    width: 25px;

    border-radius: 10px;

    background: #087cf2;
}


/* ANIMATION */

@keyframes productSlideUp {

    from {
        opacity: 0;
        transform: translateY(25px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }

}


/* TABLET */

@media (max-width: 900px) {

    .product-slide {
        grid-template-columns: repeat(2, 1fr);
    }

}


/* MOBILE */

@media (max-width: 600px) {

    .products {
        padding: 45px 6% !important;
    }

    .product-slide {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .product-slide .product-image {
        height: 150px;
        padding: 10px;
    }

    .product-slide .product-info {
        padding: 15px;
    }

    .product-slide .product-info h3 {
        font-size: 15px;
    }

    .product-slide .product-info > p:not(.product-category) {
        font-size: 12px;
    }

    .product-controls {
        margin-top: 22px;
        gap: 18px;
    }

    .product-arrow {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }

}

/* PRODUCT IMAGE AREA */

.product-slide .product-image {
    height: 290px;
    padding: 0;
    background: #eaf6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.product-slide .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.45s ease;
}

.product-slide .product-card:hover .product-image img {
    transform: scale(1.04);
}


/* MOBILE */

@media (max-width: 600px) {

    .product-slide .product-image {
        height: 170px;
        padding: 0;
    }

    .product-slide .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

}

/* =========================
   PRODUCT KNOW MORE BUTTON
========================= */

.product-learn-more {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    padding: 10px 0;
    color: #087cf2;
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
}

/* =========================
   NEW FEATURED PRODUCTS
========================= */

.featured-products {
    padding: 50px 7%;
    background: #f8fbff;
    overflow: hidden;
}

.featured-products-heading {
    max-width: 650px;
    margin: 0 auto 30px 0;
}

.featured-products-heading .section-label {
    color: #087cf2;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 2px;
}

.featured-products-heading h2 {
    margin: 10px 0 15px;
    color: #10233d;
    font-size: 40px;
    line-height: 1.2;
}

.featured-products-heading h2 span {
    color: #087cf2;
}

.featured-products-heading p {
    margin: 0;
    color: #718096;
    font-size: 16px;
    line-height: 1.6;
}


/* PRODUCT SHOWCASE */

.product-showcase {
    max-width: 1200px;
    margin: 0 auto;
}


/* PRODUCT PAGE */

.product-page {
    display: none;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    width: 100%;
    box-sizing: border-box;
}

.product-page.active {
    display: grid;
    animation: newProductUp 0.5s ease;
}


/* =========================
   PRODUCT CARD
========================= */

.new-product-card {
    background: #ffffff;
    border: 1px solid #e2e8ef;
    border-radius: 18px;
    overflow: hidden;
    box-sizing: border-box;

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}


/* HOVER */

.new-product-card:hover {
    transform: translateY(-8px);

    border-color: #c7dff5;

    box-shadow:
        0 20px 45px rgba(16, 35, 61, 0.12);
}


/* PRODUCT IMAGE */

.new-product-image {
    width: 100%;
    height: 300px;
    padding: 0;
    margin: 0;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-sizing: border-box;
}

.new-product-image img {
    width: 100%;
    height: 100%;
    padding: 0;
    margin: 0;
    display: block;
    object-fit: cover;
    object-position: center;
    transition: transform 0.45s ease;
}

.new-product-card:hover .new-product-image img {
    transform: scale(1.03);
}


/* PRODUCT INFORMATION */

.new-product-info {
    padding: 22px;
}

.new-product-info > span {
    display: block;

    margin-bottom: 9px;

    color: #087cf2;

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 1.5px;
}

.new-product-info h3 {
    margin: 0 0 10px;

    color: #10233d;

    font-size: 17px;
    line-height: 1.35;
}

.new-product-info p {
    margin: 0 0 16px;

    color: #718096;

    font-size: 13px;
    line-height: 1.5;
}

.new-product-info p strong {
    color: #33465d;
}


/* KNOW MORE */

.new-product-info a {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    color: #087cf2;

    text-decoration: none;

    font-size: 13px;
    font-weight: 700;

    transition:
        color 0.3s ease;
}

.new-product-info a b {
    font-size: 17px;

    transition:
        transform 0.3s ease;
}

.new-product-info a:hover {
    color: #0568cf;
}

.new-product-info a:hover b {
    transform: translateX(5px);
}


/* MOBILE */

@media (max-width: 600px) {

    .new-product-image {
        height: 260px;
    }

    .new-product-info {
        padding: 20px;
    }

}

/* EMPTY CARD */

.empty-product-card {
    visibility: hidden;
}


/* PRODUCT CONTROLS */

.new-product-controls {
    margin-top: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 30px;
}


/* ARROWS */

.new-product-arrow {
    width: 56px;
    height: 56px;
    border: 1px solid #d7e5f2;
    border-radius: 50%;
    background: white;
    color: #10233d;
    font-size: 24px;
    cursor: pointer;

    display: flex;
    align-items: center;
    justify-content: center;

    transition:
        transform 0.3s ease,
        background 0.3s ease,
        color 0.3s ease,
        box-shadow 0.3s ease;
}

.new-product-arrow:hover {
    background: #087cf2;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(8, 124, 242, 0.20);
}


/* DOTS */

.new-product-dots {
    display: flex;
    align-items: center;
    gap: 9px;
}

.new-product-dot {
    width: 10px;
    height: 10px;
    padding: 0;
    border: none;
    border-radius: 50%;
    background: #cbd9e8;
    cursor: pointer;
    transition:
        width 0.3s ease,
        background 0.3s ease;
}

.new-product-dot.active {
    width: 30px;
    border-radius: 10px;
    background: #087cf2;
}


/* ANIMATION */

@keyframes newProductUp {

    from {
        opacity: 0;
        transform: translateY(25px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }

}


/* =========================
   NEW PRODUCTS MOBILE
========================= */

@media (max-width: 900px) {

    .product-page {
        grid-template-columns: repeat(2, 1fr);
    }

}


@media (max-width: 600px) {

    .featured-products {
        padding: 50px 20px;
    }

    .featured-products-heading h2 {
        font-size: 32px;
    }

    .product-page {
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .new-product-image {
        height: 220px;
    }

    .new-product-info {
        padding: 20px;
    }

    .new-product-controls {
        gap: 20px;
        margin-top: 25px;
    }

    .new-product-arrow {
        width: 50px;
        height: 50px;
    }

    .empty-product-card {
        display: none;
    }

}

.application-zoom-controls {
    pointer-events: auto;
}

.application-zoom-button {
    position: relative;
    z-index: 100002;
    pointer-events: auto;
    cursor: pointer;
}

.application-preview-image {
    pointer-events: auto;
}

/* =========================
   PREMIUM ABOUT SECTION
========================= */

.about {
    position: relative;
    overflow: hidden;
}


/* ABOUT VISUAL */

.about-visual {
    position: relative;
}


/* BACKGROUND PANEL */

.about-bg {
    transition:
        transform 0.5s ease,
        box-shadow 0.5s ease;
}

.about-visual:hover .about-bg {
    transform: translateY(-5px);
    box-shadow: 0 20px 45px rgba(16, 35, 61, 0.08);
}


/* LOGO CARD */

.about-year-card {
    transition:
        transform 0.4s ease,
        box-shadow 0.4s ease,
        border-color 0.4s ease;
}

.about-year-card:hover {
    transform: translateY(-8px);
    border-color: #c7dff5;
    box-shadow:
        0 30px 65px rgba(16, 35, 61, 0.15);
}


/* LOGO */

.about-company-logo {
    transition:
        transform 0.5s ease;
}

.about-year-card:hover .about-company-logo {
    transform: scale(1.04);
}


/* ABOUT TEXT */

.about-content h2 {
    letter-spacing: -0.5px;
}

.about-content p {
    transition: color 0.3s ease;
}


/* SPECIALIZATION */

.specialization-box {
    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}

.specialization-box:hover {
    transform: translateY(-4px);
    border-color: #c7dff5;
    box-shadow:
        0 15px 35px rgba(16, 35, 61, 0.08);
}


/* SPECIALIZATION ITEMS */

.specialization-items div {
    transition:
        transform 0.3s ease;
}

.specialization-items div:hover {
    transform: translateX(5px);
}


/* BOTTOM FEATURES */

.about-bottom div {
    transition:
        transform 0.3s ease;
}

.about-bottom div:hover {
    transform: translateY(-3px);
}


/* MOBILE */

@media (max-width: 600px) {

    .about-visual {
        min-height: 350px;
    }

    .about-year-card {
        width: 90%;
        height: 260px;
        padding: 14px;
    }

    .about-bg {
        width: 90%;
        height: 85%;
    }

}

/* =========================
   WHY AMPTRONIX - POLISH
========================= */

.why {
    position: relative;
    overflow: hidden;
}


.why-container {
    max-width: none;
    width: 100%;
    margin: 0;

    display: grid;
    grid-template-columns: repeat(4, 1fr);

    border: 1px solid #dfe8f1;
    border-radius: 18px;
    overflow: hidden;

    background: #ffffff;
    box-shadow: 0 12px 35px rgba(16, 35, 61, 0.055);
}


/* WHY CARD */

.why-card {
    position: relative;
    padding: 32px 28px;
    background: #ffffff;

    transition:
        transform 0.35s ease,
        background 0.35s ease,
        box-shadow 0.35s ease;
}


/* TOP BLUE LINE */

.why-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 3px;
    background: #087cf2;

    transition: width 0.35s ease;
}


/* CARD HOVER */

.why-card:hover {
    background: #fbfdff;
    transform: translateY(-4px);
    box-shadow: 0 15px 30px rgba(16, 35, 61, 0.08);
    z-index: 2;
}

.why-card:hover::before {
    width: 100%;
}


/* ICON */

.why-icon {
    width: 54px;
    height: 54px;
    margin-bottom: 20px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #eaf5ff;
    color: #087cf2;

    border: 1px solid #d7e9f8;
    border-radius: 12px;

    font-size: 23px;

    transition:
        transform 0.35s ease,
        background 0.35s ease,
        color 0.35s ease;
}


/* ICON HOVER */

.why-card:hover .why-icon {
    background: #087cf2;
    color: #ffffff;
    transform: translateY(-3px) rotate(-3deg);
}


/* TITLE */

.why-card h3 {
    margin: 0 0 9px;

    color: #10233d;

    font-size: 18px;
    line-height: 1.35;
}


/* DESCRIPTION */

.why-card p {
    margin: 0;

    color: #718096;

    font-size: 13px;
    line-height: 1.6;
}


/* MOBILE */

@media (max-width: 900px) {

    .why-container {
        grid-template-columns: repeat(2, 1fr);
    }

    .why-card {
        padding: 28px 24px;
    }

}


@media (max-width: 600px) {

    .why {
        padding-left: 20px;
        padding-right: 20px;
    }

    .why-container {
        grid-template-columns: 1fr;
    }

    .why-card {
        padding: 25px 22px;
        border-right: none;
        border-bottom: 1px solid #e2e8ef;
    }

    .why-card:last-child {
        border-bottom: none;
    }

}

/* =========================
   OUR SOLUTIONS - POLISH
========================= */

.solutions {
    position: relative;
    overflow: hidden;
}


/* HEADING */

.solutions-heading {
    max-width: 680px;
}

.solutions-heading h2 {
    letter-spacing: -0.6px;
}


/* SOLUTION GRID */

.solutions-container {
    gap: 22px;
}


/* SOLUTION CARD */

.solution-card {
    position: relative;

    padding: 30px;

    background: #ffffff;

    border: 1px solid #dfe8f0;
    border-radius: 18px;

    overflow: hidden;

    box-sizing: border-box;

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}


/* TOP BLUE LINE */

.solution-card::before {
    content: "";

    position: absolute;
    top: 0;
    left: 0;

    width: 0;
    height: 3px;

    background: #087cf2;

    transition: width 0.35s ease;
}


/* HOVER */

.solution-card:hover {
    transform: translateY(-8px);

    border-color: #c7dff5;

    box-shadow:
        0 20px 45px rgba(16, 35, 61, 0.10);
}

.solution-card:hover::before {
    width: 100%;
}


/* ICON */

.solution-icon {
    width: 56px;
    height: 56px;

    margin-bottom: 20px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #eaf5ff;

    color: #087cf2;

    border: 1px solid #d7e9f8;

    border-radius: 13px;

    font-size: 23px;

    transition:
        transform 0.35s ease,
        background 0.35s ease,
        color 0.35s ease;
}


/* ICON HOVER */

.solution-card:hover .solution-icon {
    background: #087cf2;
    color: #ffffff;

    transform:
        translateY(-3px)
        rotate(-3deg);
}


/* TITLE */

.solution-card h3 {
    margin: 0 0 10px;

    color: #10233d;

    font-size: 18px;

    line-height: 1.35;
}


/* DESCRIPTION */

.solution-card p {
    margin: 0;

    color: #718096;

    font-size: 13px;

    line-height: 1.65;
}


/* TABLET */

@media (max-width: 900px) {

    .solutions-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

}


/* MOBILE */

@media (max-width: 600px) {

    .solutions {
        padding-left: 20px;
        padding-right: 20px;
    }

    .solutions-container {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .solution-card {
        padding: 26px 22px;
    }

}

/* =========================
   CONTACT SECTION - POLISH
========================= */

.contact-cta {
    position: relative;
    overflow: hidden;

    border-radius: 22px;

    box-shadow:
        0 20px 50px rgba(16, 35, 61, 0.10);

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease;
}

.contact-cta:hover {
    transform: translateY(-4px);

    box-shadow:
        0 28px 60px rgba(16, 35, 61, 0.14);
}


/* CONTACT CONTENT */

.contact-content {
    position: relative;
    z-index: 2;
}

.contact-content h2 {
    letter-spacing: -0.5px;
}

.contact-content p {
    line-height: 1.7;
}


/* CONTACT ACTIONS */

.contact-actions {
    position: relative;
    z-index: 2;

    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}


/* CONTACT BUTTONS */

.contact-actions a,
.contact-actions button {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        background 0.3s ease;
}

.contact-actions a:hover,
.contact-actions button:hover {
    transform: translateY(-3px);

    box-shadow:
        0 10px 25px rgba(8, 124, 242, 0.18);
}


/* CONTACT INFO */

.contact-info {
    position: relative;
    z-index: 2;
}

.contact-info div {
    transition:
        transform 0.3s ease;
}

.contact-info div:hover {
    transform: translateX(4px);
}


/* MOBILE */

@media (max-width: 900px) {

    .contact-cta {
        border-radius: 18px;
    }

    .contact-actions {
        width: 100%;
    }

}


@media (max-width: 600px) {

    .contact-cta {
        margin-left: 20px;
        margin-right: 20px;
        border-radius: 16px;
    }

    .contact-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .contact-actions a,
    .contact-actions button {
        width: 100%;
        box-sizing: border-box;
        text-align: center;
    }

}

/* =========================
   HEADER - POLISH
========================= */

header {
    transition:
        background 0.3s ease,
        box-shadow 0.3s ease;
}


/* NAVIGATION LINKS */

nav a {
    position: relative;
    transition:
        color 0.25s ease,
        transform 0.25s ease;
}

nav a:not(.quote)::after {
    content: "";

    position: absolute;
    left: 50%;
    bottom: -7px;

    width: 0;
    height: 2px;

    background: #087cf2;

    transform: translateX(-50%);

    transition:
        width 0.25s ease;
}

nav a:not(.quote):hover {
    color: #087cf2;
}

nav a:not(.quote):hover::after {
    width: 70%;
}


/* QUOTE BUTTON */

nav .quote {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        background 0.3s ease;
}

nav .quote:hover {
    transform: translateY(-2px);

    box-shadow:
        0 10px 25px rgba(8, 124, 242, 0.20);
}


/* LOGO */

.logo {
    transition:
        transform 0.3s ease;
}

.logo:hover {
    transform: translateY(-2px);
}


/* MOBILE */

@media (max-width: 900px) {

    nav a:not(.quote)::after {
        display: none;
    }

}

/* =========================
   FOOTER - POLISH
========================= */

footer {
    position: relative;
    overflow: hidden;
}


/* FOOTER LINKS */

footer a {
    transition:
        color 0.25s ease,
        transform 0.25s ease;
}

footer a:hover {
    color: #087cf2;
}


/* FOOTER COLUMNS */

.footer-container > div {
    transition:
        transform 0.3s ease;
}

.footer-container > div:hover {
    transform: translateY(-3px);
}


/* FOOTER HEADING */

footer h3,
footer h4 {
    letter-spacing: 0.2px;
}


/* FOOTER BOTTOM */

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.12);
    margin-top: 35px;
    padding-top: 20px;

    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}


/* SOCIAL / ICON LINKS */

footer .social-links a {
    transition:
        transform 0.3s ease,
        color 0.3s ease;
}

footer .social-links a:hover {
    transform: translateY(-3px);
    color: #087cf2;
}


/* MOBILE */

@media (max-width: 600px) {

    .footer-bottom {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }

}

/* =========================================
   FINAL RESPONSIVE OPTIMIZATION
========================================= */


/* =========================
   TABLET
   601px - 900px
========================= */

@media (min-width: 601px) and (max-width: 900px) {

    .hero {
        grid-template-columns: 1fr;
        min-height: auto;
        gap: 40px;
        padding: 60px 6%;
    }

    .hero-content {
        max-width: 700px;
    }

    .hero-product {
        width: 100%;
        max-width: 650px;
        margin: 0 auto;
    }


    /* WHY */

    .why {
        padding-left: 6%;
        padding-right: 6%;
    }

    .why-container {
        grid-template-columns: repeat(2, 1fr);
    }


    /* SOLUTIONS */

    .solutions {
        padding-left: 6%;
        padding-right: 6%;
    }

    .solutions-container {
        grid-template-columns: repeat(2, 1fr);
    }


    /* ABOUT */

    .about {
        padding-left: 6%;
        padding-right: 6%;
    }


    /* PRODUCTS */

    .featured-products {
        padding-left: 6%;
        padding-right: 6%;
    }

    .product-page {
        grid-template-columns: repeat(2, 1fr);
    }


    /* APPLICATIONS */

    .applications {
        padding-left: 6%;
        padding-right: 6%;
    }

}


/* =========================
   MOBILE
   600px AND BELOW
========================= */

@media (max-width: 600px) {

    /* GENERAL */

    html {
        overflow-x: hidden;
    }

    body {
        overflow-x: hidden;
    }


    /* HEADER */

    header {
        height: 65px;
        padding: 0 20px;
    }

    .logo {
        font-size: 20px;
    }


    /* HERO */

    .hero {
        min-height: auto;
        padding: 50px 20px 60px;
        gap: 35px;
    }

    .hero h1,
    .hero-title {
        font-size: 34px;
        line-height: 1.15;
    }

    .hero p,
    .hero-description,
    .description {
        font-size: 14px;
        line-height: 1.7;
    }

    .hero-buttons {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
    }

    .hero-buttons a,
    .hero-buttons button {
        width: 100%;
        box-sizing: border-box;
        text-align: center;
    }

    .hero-product {
        width: 100%;
        min-height: 300px;
        height: auto;
    }

    .hero-product-image {
        padding: 15px;
    }

    .hero-product-image img {
        width: 100%;
        max-width: 380px;
    }


    /* WHY AMPTRONIX */

    .why {
        padding: 45px 20px 55px;
    }

    .why-container {
        grid-template-columns: 1fr;
    }

    .why-card {
        padding: 25px 22px;
    }


    /* SOLUTIONS */

    .solutions {
        padding: 55px 20px;
    }

    .solutions-heading h2 {
        font-size: 32px;
    }

    .solutions-container {
        grid-template-columns: 1fr;
        gap: 16px;
    }


    /* ABOUT */

    .about {
        padding: 55px 20px;
    }

    .about-container {
        grid-template-columns: 1fr !important;
        gap: 35px;
    }

    .about-year-card {
        width: 90%;
        height: 260px;
        margin: 0 auto;
        padding: 14px;
        box-sizing: border-box;
    }

    .about-bg {
        width: 90%;
    }

    .about-content h2 {
        font-size: 32px;
    }


    /* PRODUCTS */

    .featured-products {
        padding: 55px 20px;
    }

    .featured-products-heading h2 {
        font-size: 32px;
    }

    .product-page {
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .new-product-image {
        height: 260px;
    }


    /* APPLICATIONS */

    .applications {
        padding: 55px 20px;
    }

    .applications-heading h2 {
        font-size: 32px;
    }

    .application-slide {
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }


    /* CONTACT */

    .contact-cta {
        margin: 20px;
        padding: 35px 22px;
        box-sizing: border-box;
    }

    .contact-content h2 {
        font-size: 30px;
    }

    .contact-actions {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
    }

    .contact-actions a,
    .contact-actions button {
        width: 100%;
        box-sizing: border-box;
    }


    /* FOOTER */

    footer {
        padding-left: 20px;
        padding-right: 20px;
    }

    .footer-container {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .footer-bottom {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }

}


/* =========================
   VERY SMALL PHONES
   400px AND BELOW
========================= */

@media (max-width: 400px) {

    .hero h1,
    .hero-title {
        font-size: 30px;
    }

    .solutions-heading h2,
    .featured-products-heading h2,
    .about-content h2 {
        font-size: 29px;
    }

    .application-slide {
        grid-template-columns: 1fr;
    }

    .new-product-image {
        height: 240px;
    }

}

/* =========================================================
   STEP 2 — PREMIUM CAPABILITIES
   Lightweight / Fast / Responsive
========================================================= */

.why {
    position: relative;
    padding: 55px 7% 65px;
    background: #ffffff;
    overflow: hidden;
}


.why-header {
    max-width: none;
    width: 100%;
    margin: 0 0 30px;
    display: block;
    text-align: left;
}	


.why-eyebrow {
    margin: 0 0 10px;

    color: #087cf2;

    font-size: 11px;
    font-weight: 800;

    letter-spacing: 2.5px;
    text-transform: uppercase;
}


.why-header h2 {
    margin: 0;

    max-width: 700px;

    color: #10233d;

    font-size: 36px;
    line-height: 1.15;
    letter-spacing: -0.7px;
}


.why-header h2 span {
    color: #087cf2;
}


.why-intro {
    max-width: 850px;

    margin: 15px 0 0;

    color: #718096;

    font-size: 14px;
    line-height: 1.7;
}


/* MAIN CONTAINER */

.why-container {
    width: 100%;
    max-width: none;
    margin: 25px 0 0;

    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));

    border: 1px solid #dfe8f1;
    border-radius: 18px;
    overflow: hidden;

    background: #ffffff;

    box-shadow: 0 12px 35px rgba(16, 35, 61, 0.055);

    box-sizing: border-box;
}


/* CARD */

.why-card {
    position: relative;

    min-height: 205px;

    padding: 27px 24px;

    background: #ffffff;

    border-right: 1px solid #dfe8f1;

    box-sizing: border-box;

    transition:
        transform 0.25s ease,
        background 0.25s ease,
        box-shadow 0.25s ease;
}


.why-card:last-child {
    border-right: none;
}


/* TOP LINE */

.why-card::before {
    content: "";

    position: absolute;

    top: 0;
    left: 0;

    width: 0;
    height: 3px;

    background: #087cf2;

    transition: width 0.25s ease;
}


/* HOVER */

.why-card:hover {
    background: #fafdff;

    transform: translateY(-4px);

    box-shadow: 0 15px 30px rgba(16, 35, 61, 0.08);

    z-index: 2;
}


.why-card:hover::before {
    width: 100%;
}


/* ICON */

.why-icon {
    width: 46px;
    height: 46px;

    margin-bottom: 22px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #eaf5ff;

    border: 1px solid #d6e8f7;

    border-radius: 12px;

    color: #087cf2;

    font-size: 20px;
    font-weight: 700;

    transition:
        transform 0.25s ease,
        background 0.25s ease,
        color 0.25s ease;
}


.why-card:hover .why-icon {
    background: #087cf2;

    color: #ffffff;

    transform: translateY(-2px);
}


/* NUMBER */

.why-number {
    display: block;

    margin-bottom: 5px;

    color: #9aabba;

    font-size: 10px;
    font-weight: 700;

    letter-spacing: 1.5px;
}


/* TITLE */

.why-card h3 {
    margin: 0 0 9px;

    color: #10233d;

    font-size: 17px;
    line-height: 1.35;

    transition: color 0.25s ease;
}


.why-card:hover h3 {
    color: #087cf2;
}


/* DESCRIPTION */

.why-card p {
    margin: 0;

    color: #718096;

    font-size: 12.5px;

    line-height: 1.65;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 1000px) {

    .why {
        padding-left: 5%;
        padding-right: 5%;
    }


    .why-container {
        grid-template-columns: repeat(2, 1fr);
    }


    .why-card:nth-child(2) {
        border-right: none;
    }


    .why-card:nth-child(1),
    .why-card:nth-child(2) {
        border-bottom: 1px solid #dfe8f1;
    }


    .why-card:nth-child(3) {
        border-right: 1px solid #dfe8f1;
    }


    .why-header {
        align-items: flex-start;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 650px) {

    .why {
        padding: 45px 20px 50px;
    }


    .why-header {
        display: block;

        margin-bottom: 25px;
    }


    .why-header h2 {
        font-size: 30px;

        line-height: 1.18;
    }


    .why-intro {
        max-width: 100%;

        margin-top: 14px;

        font-size: 13px;
    }


    .why-container {
        grid-template-columns: 1fr;

        border-radius: 15px;
    }


    .why-card,
    .why-card:nth-child(2),
    .why-card:nth-child(3) {
        min-height: auto;

        padding: 24px 21px;

        border-right: none;
        border-bottom: 1px solid #dfe8f1;
    }


    .why-card:last-child {
        border-bottom: none;
    }


    .why-icon {
        width: 44px;
        height: 44px;

        margin-bottom: 17px;
    }


    .why-card h3 {
        font-size: 16px;
    }

}


/* =========================================================
   REDUCED MOTION
========================================================= */

@media (prefers-reduced-motion: reduce) {

    .why-card,
    .why-icon,
    .why-card::before {
        transition: none;
    }

}

/* =========================================================
   STEP 1A — PREMIUM HEADER
   Fast / Lightweight / Responsive
========================================================= */

header {
    height: 76px;
    padding: 0 6%;

    display: flex;
    align-items: center;
    justify-content: space-between;

    position: sticky;
    top: 0;
    z-index: 1000;

    background: rgba(255, 255, 255, 0.97);

    border-bottom: 1px solid #e7edf4;

    box-shadow: 0 4px 20px rgba(16, 35, 61, 0.04);

    box-sizing: border-box;

    backdrop-filter: none;
    -webkit-backdrop-filter: none;
}


/* LOGO */

.logo {
    display: flex;
    align-items: center;

    flex-shrink: 0;
}


.logo img {
    width: 165px;
    height: auto;

    display: block;

    object-fit: contain;

    transition: transform 0.25s ease;
}


.logo img:hover {
    transform: scale(1.025);
}


/* NAVIGATION */

nav {
    display: flex;
    align-items: center;

    gap: 28px;

    margin-left: auto;
    margin-right: 30px;
}


nav a {
    position: relative;

    padding: 8px 0;

    color: #42536a;

    text-decoration: none;

    font-size: 13px;
    font-weight: 600;

    letter-spacing: 0.15px;

    transition:
        color 0.2s ease,
        transform 0.2s ease;
}


nav a::after {
    content: "";

    position: absolute;

    left: 50%;
    bottom: 0;

    width: 0;
    height: 2px;

    background: #087cf2;

    border-radius: 2px;

    transform: translateX(-50%);

    transition: width 0.2s ease;
}


nav a:hover {
    color: #087cf2;

    transform: translateY(-1px);
}


nav a:hover::after {
    width: 100%;
}


/* QUOTE BUTTON */

.quote {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    min-height: 42px;

    padding: 0 20px;

    box-sizing: border-box;

    background: #087cf2;

    color: #ffffff;

    border: 1px solid #087cf2;

    border-radius: 8px;

    text-decoration: none;

    font-size: 13px;
    font-weight: 700;

    white-space: nowrap;

    box-shadow: 0 6px 18px rgba(8, 124, 242, 0.15);

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease,
        background 0.2s ease;
}


.quote:hover {
    background: #056bd2;

    transform: translateY(-2px);

    box-shadow: 0 10px 24px rgba(8, 124, 242, 0.22);
}


/* =========================================================
   MOBILE HEADER
========================================================= */

@media (max-width: 900px) {

    header {
        height: 68px;

        padding: 0 20px;
    }


    nav {
        display: none;
    }


    .logo img {
        width: 150px;
    }


    .quote {
        min-height: 40px;

        padding: 0 15px;

        font-size: 12px;
    }

}


@media (max-width: 500px) {

    header {
        height: 64px;

        padding: 0 16px;
    }


    .logo img {
        width: 135px;
    }


    .quote {
        min-height: 38px;

        padding: 0 12px;

        border-radius: 7px;

        font-size: 11px;
    }

}


/* REDUCED MOTION */

@media (prefers-reduced-motion: reduce) {

    header,
    .logo img,
    nav a,
    nav a::after,
    .quote {
        transition: none;
    }

}

/* =========================================================
   STEP 1B — PREMIUM HERO
   Lightweight / Fast / Responsive
========================================================= */

.hero {
    position: relative;

    min-height: 610px !important;

    padding: 65px 7% 75px !important;

    display: grid;

    grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.08fr);

    align-items: center;

    gap: 65px;

    background:
        radial-gradient(
            circle at 15% 20%,
            rgba(8, 124, 242, 0.055),
            transparent 32%
        ),
        #ffffff;

    overflow: hidden;

    box-sizing: border-box;
}


/* subtle background line */

.hero::before {
    content: "";

    position: absolute;

    width: 420px;
    height: 420px;

    right: -220px;
    bottom: -240px;

    border-radius: 50%;

    border: 1px solid rgba(8, 124, 242, 0.08);

    pointer-events: none;
}


/* =========================================================
   HERO CONTENT
========================================================= */

.hero-content {
    position: relative;

    z-index: 2;

    padding-left: 0 !important;

    max-width: 620px;

    animation: heroContentIn 0.75s ease-out both;
}


/* eyebrow */

.hero-welcome {
    display: inline-flex;

    align-items: center;

    gap: 10px;

    margin: 0;

    color: #087cf2;

    font-size: 12px !important;

    font-weight: 800;

    letter-spacing: 2.8px !important;

    line-height: 1.4;

    white-space: nowrap;
}


.hero-welcome::before {
    content: "";

    width: 28px;
    height: 2px;

    background: #087cf2;

    border-radius: 2px;
}


.hero-welcome::after {
    display: none !important;
}


/* =========================================================
   MAIN HERO MESSAGE
========================================================= */

.hero-tagline {
    margin: 20px 0 28px !important;

    max-width: 610px;

    color: #10233d;

    font-size: 48px !important;

    font-weight: 800 !important;

    line-height: 1.08 !important;

    letter-spacing: -1.5px;

    animation: heroTaglineIn 0.8s ease-out 0.12s both;
}


.hero-tagline br {
    display: none;
}


/* =========================================================
   HIGHLIGHTS
========================================================= */

.hero-highlights {
    display: grid;

    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: 12px;

    max-width: 600px;

    animation: heroHighlightsIn 0.8s ease-out 0.25s both;
}


.hero-highlight {
    min-height: 72px;

    padding: 13px 14px !important;

    display: flex;

    align-items: center;

    gap: 12px;

    background: #ffffff;

    border: 1px solid #e2ebf3;

    border-radius: 12px;

    box-sizing: border-box;

    transition:
        transform 0.22s ease,
        border-color 0.22s ease,
        box-shadow 0.22s ease;
}


.hero-highlight:hover {
    transform: translateY(-3px);

    border-color: #c5ddf3;

    box-shadow: 0 10px 25px rgba(16, 35, 61, 0.07);
}


.highlight-icon {
    width: 38px !important;
    height: 38px !important;

    min-width: 38px !important;

    margin: 0 !important;

    display: flex;

    align-items: center;
    justify-content: center;

    background: #eaf5ff;

    color: #087cf2;

    border-radius: 9px;

    font-size: 17px !important;

    font-weight: 700;

    transition:
        transform 0.22s ease,
        background 0.22s ease,
        color 0.22s ease;
}


.hero-highlight:hover .highlight-icon {
    background: #087cf2;

    color: #ffffff;

    transform: scale(1.06);
}


.hero-highlight h3 {
    margin: 0 0 3px !important;

    color: #10233d;

    font-size: 11px !important;

    font-weight: 800;

    letter-spacing: 0.7px;
}


.hero-highlight p {
    margin: 0;

    color: #718096;

    font-size: 11px !important;

    line-height: 1.4;
}


/* =========================================================
   HERO PRODUCT AREA
========================================================= */

.hero-product {
    position: relative;

    min-height: 500px !important;

    height: auto !important;

    border-radius: 26px !important;

    background:
        linear-gradient(
            145deg,
            #f1f8ff 0%,
            #e7f2fb 100%
        ) !important;

    border: 1px solid #d8e7f3 !important;

    box-shadow:
        0 20px 50px rgba(16, 35, 61, 0.08) !important;

    overflow: hidden;

    animation: heroProductIn 0.9s ease-out 0.15s both;

    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}


.hero-product:hover {
    transform: translateY(-5px);

    box-shadow:
        0 28px 60px rgba(16, 35, 61, 0.12) !important;
}


/* decorative glow */

.hero-product::before {
    width: 360px !important;
    height: 360px !important;

    top: -145px !important;
    right: -100px !important;

    background: #dceefe !important;

    opacity: 0.9;

    pointer-events: none;
}


/* product image */

.hero-product-image {
    width: 88% !important;

    height: auto !important;

    min-height: 400px;

    display: flex;

    align-items: center;

    justify-content: center;

    position: relative;

    z-index: 2;

    animation: none !important;

    transition: transform 0.35s ease;
}


.hero-product-image img {
    width: 100% !important;

    max-width: 620px !important;

    height: auto !important;

    max-height: 420px;

    object-fit: contain;

    display: block;

    filter:
        drop-shadow(
            0 20px 24px rgba(20, 50, 80, 0.16)
        );

    transition:
        transform 0.35s ease,
        filter 0.35s ease;
}


.hero-product:hover .hero-product-image img {
    transform: scale(1.025) !important;

    filter:
        drop-shadow(
            0 24px 30px rgba(20, 50, 80, 0.20)
        );
}


/* =========================================================
   PRODUCT LABEL
========================================================= */

.hero-product-label {
    left: 24px !important;

    bottom: 22px !important;

    padding: 12px 16px !important;

    background: rgba(255, 255, 255, 0.96) !important;

    border: 1px solid #dbe7f0 !important;

    border-radius: 10px !important;

    box-shadow:
        0 8px 24px rgba(16, 35, 61, 0.08) !important;

    z-index: 5;
}


.hero-product-label span {
    font-size: 9px !important;

    letter-spacing: 1.6px !important;
}


.hero-product-label strong {
    font-size: 14px !important;
}


.hero-product-label small {
    font-size: 11px !important;
}


/* =========================================================
   HERO ENTRANCE ANIMATIONS
========================================================= */

@keyframes heroContentIn {

    from {
        opacity: 0;

        transform: translateY(22px);
    }

    to {
        opacity: 1;

        transform: translateY(0);
    }

}


@keyframes heroTaglineIn {

    from {
        opacity: 0;

        transform: translateY(18px);
    }

    to {
        opacity: 1;

        transform: translateY(0);
    }

}


@keyframes heroHighlightsIn {

    from {
        opacity: 0;

        transform: translateY(14px);
    }

    to {
        opacity: 1;

        transform: translateY(0);
    }

}


@keyframes heroProductIn {

    from {
        opacity: 0;

        transform: translateY(28px);
    }

    to {
        opacity: 1;

        transform: translateY(0);
    }

}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 1000px) {

    .hero {
        grid-template-columns: 1fr;

        gap: 40px;

        min-height: auto !important;

        padding: 60px 6% !important;
    }


    .hero-content {
        max-width: 700px;
    }


    .hero-product {
        min-height: 430px !important;
    }


    .hero-product-image {
        min-height: 350px;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .hero {
        min-height: auto !important;

        padding: 48px 20px 55px !important;

        gap: 30px;

        background: #ffffff;
    }


    .hero::before {
        display: none;
    }


    .hero-content {
        width: 100%;

        max-width: none;

        text-align: left;
    }


    .hero-welcome {
        font-size: 10px !important;

        letter-spacing: 2px !important;
    }


    .hero-welcome::before {
        width: 20px;
    }


    .hero-tagline {
        margin: 16px 0 23px !important;

        font-size: 34px !important;

        line-height: 1.12 !important;

        letter-spacing: -0.8px;
    }


    .hero-highlights {
        grid-template-columns: 1fr;

        gap: 10px;

        max-width: none;
    }


    .hero-highlight {
        min-height: 66px;

        padding: 11px 12px !important;
    }


    .hero-highlight h3 {
        font-size: 10.5px !important;
    }


    .hero-highlight p {
        font-size: 10.5px !important;
    }


    .hero-product {
        width: 100%;

        min-height: 330px !important;

        border-radius: 20px !important;
    }


    .hero-product-image {
        width: 92% !important;

        min-height: 270px;

        padding-bottom: 45px;

        box-sizing: border-box;
    }


    .hero-product-image img {
        max-height: 250px !important;
    }


    .hero-product-label {
        left: 15px !important;

        bottom: 15px !important;

        padding: 10px 12px !important;
    }


    .hero-product-label strong {
        font-size: 12px !important;
    }


    .hero-product-label small {
        font-size: 10px !important;
    }

}


/* =========================================================
   ACCESSIBILITY
========================================================= */

@media (prefers-reduced-motion: reduce) {

    .hero,
    .hero-content,
    .hero-tagline,
    .hero-highlights,
    .hero-product,
    .hero-highlight,
    .highlight-icon,
    .hero-product-image,
    .hero-product-image img {
        animation: none !important;

        transition: none !important;
    }

}

/* =====================================================
   PREMIUM SOLUTION CARDS
===================================================== */

.solutions-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 22px;
}


/* CARD */

.solution-card {
    position: relative;

    flex: 1 1 250px;
    max-width: 285px;
    min-height: 300px;

    padding: 30px;

    display: flex;
    flex-direction: column;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #dfe8f0;
    border-radius: 18px;

    overflow: hidden;

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}


/* TOP BLUE ACCENT */

.solution-card::before {
    content: "";

    position: absolute;

    top: 0;
    left: 0;

    width: 0;
    height: 3px;

    background: #087cf2;

    transition: width 0.4s ease;
}


/* HOVER */

.solution-card:hover {
    transform: translateY(-8px);

    border-color: #c7dff5;

    box-shadow:
        0 20px 45px rgba(16, 35, 61, 0.11);
}

.solution-card:hover::before {
    width: 100%;
}


/* ICON */

.solution-icon {
    width: 58px;
    height: 58px;

    margin-bottom: 24px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #eaf5ff;

    color: #087cf2;

    border: 1px solid #d7e9f8;

    border-radius: 14px;

    font-size: 24px;

    transition:
        transform 0.35s ease,
        background 0.35s ease,
        color 0.35s ease,
        box-shadow 0.35s ease;
}


/* ICON HOVER */

.solution-card:hover .solution-icon {
    background: #087cf2;

    color: #ffffff;

    transform: translateY(-3px) rotate(-4deg);

    box-shadow:
        0 8px 20px rgba(8, 124, 242, 0.20);
}


/* TITLE */

.solution-card h3 {
    margin: 0 0 12px;

    color: #10233d;

    font-size: 19px;

    line-height: 1.35;

    transition: color 0.3s ease;
}


/* TITLE HOVER */

.solution-card:hover h3 {
    color: #087cf2;
}


/* DESCRIPTION */

.solution-card p {
    margin: 0;

    color: #718096;

    font-size: 13px;

    line-height: 1.7;
}


/* EXPLORE LINK */

.solution-card a {
    margin-top: auto;

    padding-top: 24px;

    display: inline-flex;
    align-items: center;

    width: fit-content;

    color: #087cf2;

    text-decoration: none;

    font-size: 13px;

    font-weight: 700;

    transition:
        gap 0.3s ease,
        color 0.3s ease;
}


/* ARROW ANIMATION */

.solution-card a:hover {
    color: #005fc4;
}


/* =====================================================
   TABLET
===================================================== */

@media (max-width: 1000px) {

    .solutions-container {
        gap: 18px;
    }

    .solution-card {
        flex: 1 1 40%;
        max-width: 45%;
    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 600px) {

    .solutions-container {
        display: grid;

        grid-template-columns: 1fr;

        gap: 18px;
    }

    .solution-card {
        width: 100%;
        max-width: none;

        min-height: 280px;

        padding: 26px;
    }

}

/* =====================================================
   ABOUT AMPTRONIX — PREMIUM REDESIGN
===================================================== */

.about {
    position: relative;
    overflow: hidden;
    background: #ffffff;
}


/* MAIN CONTAINER */

.about-container {
    max-width: 1250px;
    margin: 0 auto;

    display: grid;
    grid-template-columns: 0.9fr 1.1fr;

    gap: 70px;
    align-items: center;
}


/* =====================================================
   LEFT VISUAL
===================================================== */

.about-visual {
    position: relative;

    min-height: 500px;

    display: flex;
    align-items: center;
    justify-content: center;
}


/* BACKGROUND PANEL */

.about-bg {
    position: absolute;

    width: 88%;
    height: 88%;

    top: 6%;
    left: 0;

    background:
        radial-gradient(
            circle at 80% 20%,
            #dceefe 0,
            transparent 35%
        ),
        linear-gradient(
            145deg,
            #eaf6ff,
            #f8fbff
        );

    border: 1px solid #dceaf5;

    border-radius: 30px;

    transition:
        transform 0.5s ease,
        box-shadow 0.5s ease;
}


/* DECORATIVE BLUE LINE */

.about-bg::after {
    content: "";

    position: absolute;

    width: 100px;
    height: 4px;

    background: #087cf2;

    border-radius: 10px;

    left: 35px;
    bottom: 35px;

    transition: width 0.4s ease;
}


/* BACKGROUND HOVER */

.about-visual:hover .about-bg {
    transform: translateY(-6px);

    box-shadow:
        0 25px 55px rgba(16, 35, 61, 0.08);
}

.about-visual:hover .about-bg::after {
    width: 150px;
}


/* =====================================================
   LOGO CARD
===================================================== */

.about-year-card {
    position: relative;
    z-index: 2;

    width: 420px;
    height: 300px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(255, 255, 255, 0.96);

    border: 1px solid #dce8f2;

    border-radius: 24px;

    padding: 25px;

    box-sizing: border-box;

    box-shadow:
        0 25px 60px rgba(16, 35, 61, 0.12);

    transition:
        transform 0.45s ease,
        box-shadow 0.45s ease,
        border-color 0.45s ease;
}


/* CARD HOVER */

.about-year-card:hover {
    transform: translateY(-10px);

    border-color: #c4def5;

    box-shadow:
        0 30px 70px rgba(16, 35, 61, 0.16);
}


/* LOGO */

.about-company-logo {
    width: 88%;
    height: 88%;

    object-fit: contain;

    display: block;

    transition:
        transform 0.5s ease;
}

.about-year-card:hover .about-company-logo {
    transform: scale(1.05);
}


/* =====================================================
   RIGHT CONTENT
===================================================== */

.about-content {
    max-width: 680px;
}


.about-tag {
    display: inline-block;

    color: #087cf2;

    font-size: 12px;

    font-weight: 800;

    letter-spacing: 2.5px;

    margin-bottom: 10px;
}


.about-content h2 {
    margin: 10px 0 22px;

    color: #10233d;

    font-size: 46px;

    line-height: 1.12;

    letter-spacing: -0.8px;
}


.about-content h2 span {
    color: #087cf2;
}


/* INTRO */

.about-intro {
    max-width: 620px;

    font-size: 17px !important;

    line-height: 1.75 !important;

    color: #33465d !important;

    margin-bottom: 18px !important;
}


/* NORMAL TEXT */

.about-content > p:not(.about-intro) {
    max-width: 650px;

    color: #718096;

    font-size: 14px;

    line-height: 1.8;

    margin-bottom: 20px;
}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 900px) {

    .about-container {
        grid-template-columns: 1fr;

        gap: 45px;
    }

    .about-visual {
        min-height: 430px;
    }

    .about-year-card {
        width: min(420px, 85%);
        height: 290px;
    }

    .about-content {
        max-width: 100%;
    }

}


@media (max-width: 600px) {

    .about {
        padding: 60px 20px !important;
    }

    .about-container {
        gap: 35px;
    }

    .about-visual {
        min-height: 330px;
    }

    .about-bg {
        width: 92%;
        height: 88%;
    }

    .about-year-card {
        width: 90%;
        height: 230px;
        padding: 18px;
    }

    .about-content h2 {
        font-size: 32px;
        line-height: 1.15;
    }

    .about-intro {
        font-size: 16px !important;
    }

}

/* =====================================================
   PREMIUM OUR SPECIALIZATION
===================================================== */

.specialization-box {
    position: relative;

    margin-top: 28px;

    padding: 25px 26px;

    background: #f8fbff;

    border: 1px solid #dce8f2;

    border-radius: 16px;

    overflow: hidden;

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}


/* BLUE TOP ACCENT */

.specialization-box::before {
    content: "";

    position: absolute;

    top: 0;
    left: 0;

    width: 60px;
    height: 3px;

    background: #087cf2;

    border-radius: 0 0 5px 0;

    transition: width 0.4s ease;
}


/* HOVER */

.specialization-box:hover {
    transform: translateY(-4px);

    border-color: #c8def1;

    box-shadow:
        0 18px 40px rgba(16, 35, 61, 0.08);
}

.specialization-box:hover::before {
    width: 120px;
}


/* TITLE */

.specialization-title {
    margin-bottom: 20px;

    color: #10233d;

    font-size: 12px;

    font-weight: 800;

    letter-spacing: 2px;
}


/* GRID */

.specialization-items {
    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 12px;
}


/* INDIVIDUAL ITEM */

.specialization-items div {
    position: relative;

    padding: 15px 16px;

    background: #ffffff;

    border: 1px solid #e4edf5;

    border-radius: 11px;

    display: flex;

    flex-direction: column;

    gap: 5px;

    transition:
        transform 0.3s ease,
        border-color 0.3s ease,
        box-shadow 0.3s ease;
}


/* ITEM HOVER */

.specialization-items div:hover {
    transform: translateY(-3px);

    border-color: #bcdcff;

    box-shadow:
        0 8px 20px rgba(8, 124, 242, 0.08);
}


/* BLUE SIDE INDICATOR */

.specialization-items div::before {
    content: "";

    position: absolute;

    left: 0;
    top: 12px;
    bottom: 12px;

    width: 3px;

    background: #087cf2;

    border-radius: 0 4px 4px 0;

    opacity: 0;

    transition: opacity 0.3s ease;
}

.specialization-items div:hover::before {
    opacity: 1;
}


/* SPECIALIZATION NAME */

.specialization-items strong {
    color: #087cf2;

    font-size: 15px;

    font-weight: 800;

    letter-spacing: 0.5px;
}


/* DESCRIPTION */

.specialization-items span {
    color: #718096;

    font-size: 11px;

    line-height: 1.5;
}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 600px) {

    .specialization-box {
        padding: 22px 18px;
    }

    .specialization-items {
        grid-template-columns: 1fr;

        gap: 10px;
    }

    .specialization-items div {
        padding: 14px;
    }

}

/* =========================================
   PREMIUM OUR PRODUCTS
========================================= */

.featured-products {
    position: relative;
    padding: 70px 7% !important;
    background: #f8fbff;
    overflow: hidden;
}

/* Soft background decoration */

.featured-products::before {
    content: "";
    position: absolute;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: #eaf5ff;
    top: -160px;
    right: -100px;
    opacity: 0.65;
    pointer-events: none;
}

.featured-products::after {
    content: "";
    position: absolute;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: #eef7ff;
    bottom: -120px;
    left: -80px;
    opacity: 0.7;
    pointer-events: none;
}


/* =========================================
   SECTION HEADING
========================================= */

.featured-products-heading {
    position: relative;
    z-index: 2;
    max-width: 700px;
    margin: 0 auto 35px 0 !important;
}

.featured-products-heading .section-label {
    position: relative;
    display: inline-block;
    color: #087cf2;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 2.5px;
}

.featured-products-heading .section-label::after {
    content: "";
    display: block;
    width: 55px;
    height: 3px;
    margin-top: 10px;
    background: #087cf2;
    border-radius: 10px;
    transition: width 0.35s ease;
}

.featured-products-heading:hover .section-label::after {
    width: 100px;
}

.featured-products-heading h2 {
    margin: 14px 0 15px;
    color: #10233d;
    font-size: 42px;
    line-height: 1.15;
    letter-spacing: -0.5px;
}

.featured-products-heading h2 span {
    color: #087cf2;
}

.featured-products-heading p {
    max-width: 620px;
    margin: 0;
    color: #718096;
    font-size: 15px;
    line-height: 1.7;
}


/* =========================================
   PRODUCT SHOWCASE
========================================= */

.product-showcase {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
}


/* =========================================
   PRODUCT PAGE
========================================= */

.product-page {
    gap: 22px !important;
}

.product-page.active {
    animation: premiumProductReveal 0.55s ease both;
}

@keyframes premiumProductReveal {
    from {
        opacity: 0;
        transform: translateY(18px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* =========================================
   PRODUCT CARD
========================================= */

.new-product-card {
    position: relative;
    background: #ffffff;
    border: 1px solid #dfe8f1;
    border-radius: 18px;
    overflow: hidden;

    box-shadow:
        0 8px 25px rgba(16, 35, 61, 0.04);

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease,
        border-color 0.35s ease;
}


/* Blue top accent */

.new-product-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;

    width: 0;
    height: 3px;

    background: #087cf2;
    border-radius: 0 0 5px 0;

    transition: width 0.4s ease;

    z-index: 5;
}

.new-product-card:hover::before {
    width: 100%;
}

.new-product-card:hover {
    transform: translateY(-8px);

    border-color: #c5ddf3;

    box-shadow:
        0 20px 45px rgba(16, 35, 61, 0.12);
}


/* =========================================
   PRODUCT IMAGE
========================================= */

.new-product-image {
    position: relative;
    height: 280px !important;

    background:
        linear-gradient(
            145deg,
            #f5faff,
            #eaf4fc
        ) !important;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;
}


/* Soft image glow */

.new-product-image::before {
    content: "";
    position: absolute;

    width: 170px;
    height: 170px;

    border-radius: 50%;

    background: #ffffff;
    opacity: 0.65;

    transition:
        transform 0.5s ease,
        opacity 0.5s ease;
}

.new-product-card:hover .new-product-image::before {
    transform: scale(1.25);
    opacity: 0.9;
}


/* Product image */

.new-product-image img {
    position: relative;
    z-index: 2;

    width: 100%;
    height: 100%;

    object-fit: contain !important;

    padding: 18px;
    box-sizing: border-box;

    display: block;

    filter:
        drop-shadow(
            0 12px 18px rgba(16, 35, 61, 0.14)
        );

    transition:
        transform 0.5s ease,
        filter 0.5s ease;
}

.new-product-card:hover .new-product-image img {
    transform: scale(1.06) translateY(-4px);

    filter:
        drop-shadow(
            0 18px 25px rgba(16, 35, 61, 0.18)
        );
}


/* =========================================
   PRODUCT INFORMATION
========================================= */

.new-product-info {
    position: relative;

    padding: 22px 22px 24px;

    background: #ffffff;
}


/* Category */

.new-product-info > span {
    display: inline-block;

    margin-bottom: 9px;

    color: #087cf2;

    font-size: 10px;
    font-weight: 800;

    letter-spacing: 1.6px;
    text-transform: uppercase;
}


/* Product name */

.new-product-info h3 {
    margin: 0 0 10px;

    color: #10233d;

    font-size: 17px;
    line-height: 1.35;
    font-weight: 800;

    transition: color 0.3s ease;
}

.new-product-card:hover .new-product-info h3 {
    color: #087cf2;
}


/* Model */

.new-product-info p {
    margin: 0 0 16px;

    color: #718096;

    font-size: 12px;
    line-height: 1.5;
}

.new-product-info p strong {
    color: #33465d;
    font-weight: 700;
}


/* Know More */

.new-product-info a {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    color: #087cf2;

    text-decoration: none;

    font-size: 13px;
    font-weight: 800;

    transition:
        gap 0.3s ease,
        color 0.3s ease;
}

.new-product-info a b {
    font-size: 17px;
    font-weight: 500;

    transition: transform 0.3s ease;
}

.new-product-info a:hover {
    color: #005fc4;
}

.new-product-info a:hover {
    gap: 12px;
}

.new-product-info a:hover b {
    transform: translateX(3px);
}


/* =========================================
   PRODUCT CONTROLS
========================================= */

.new-product-controls {
    position: relative;
    z-index: 3;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 18px;

    margin-top: 30px;
}


/* Arrow buttons */

.new-product-arrow {
    width: 42px;
    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #d5e3ef;
    border-radius: 50%;

    background: #ffffff;
    color: #087cf2;

    font-size: 18px;
    font-weight: 700;

    cursor: pointer;

    transition:
        transform 0.3s ease,
        background 0.3s ease,
        color 0.3s ease,
        box-shadow 0.3s ease;
}

.new-product-arrow:hover {
    transform: translateY(-3px);

    background: #087cf2;
    color: #ffffff;

    box-shadow:
        0 10px 22px rgba(8, 124, 242, 0.20);
}


/* Dots */

.new-product-dots {
    display: flex;
    align-items: center;
    gap: 7px;
}

.new-product-dot {
    width: 8px;
    height: 8px;

    padding: 0;

    border: none;
    border-radius: 50%;

    background: #c9d8e6;

    cursor: pointer;

    transition:
        width 0.3s ease,
        background 0.3s ease;
}

.new-product-dot.active {
    width: 25px;
    border-radius: 10px;
    background: #087cf2;
}


/* =========================================
   TABLET
========================================= */

@media (max-width: 900px) {

    .featured-products {
        padding: 60px 5% !important;
    }

    .product-page {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .new-product-image {
        height: 250px !important;
    }

}


/* =========================================
   MOBILE
========================================= */

@media (max-width: 600px) {

    .featured-products {
        padding: 55px 20px !important;
    }

    .featured-products-heading {
        margin-bottom: 28px !important;
    }

    .featured-products-heading h2 {
        font-size: 32px;
    }

    .featured-products-heading p {
        font-size: 14px;
        line-height: 1.65;
    }

    .product-page {
        grid-template-columns: 1fr !important;
        gap: 18px !important;
    }

    .new-product-image {
        height: 240px !important;
    }

    .new-product-info {
        padding: 20px;
    }

    .new-product-info h3 {
        font-size: 17px;
    }

    .new-product-controls {
        margin-top: 25px;
    }

}

/* =========================================
   PREMIUM PRODUCT NAVIGATION
========================================= */

.new-product-controls {
    padding-top: 8px;
}

/* Arrow buttons */

.new-product-arrow {
    position: relative;
    overflow: hidden;
}

.new-product-arrow::before {
    content: "";

    position: absolute;
    inset: 0;

    background: #eaf5ff;

    border-radius: 50%;

    transform: scale(0);

    transition: transform 0.3s ease;

    z-index: 0;
}

.new-product-arrow:hover::before {
    transform: scale(1);
}

.new-product-arrow {
    position: relative;
    z-index: 1;
}

.new-product-arrow:hover {
    color: #087cf2;
    background: #ffffff;
}

.new-product-arrow {
    font-family: Arial, sans-serif;
}


/* Keep arrow above hover layer */

.new-product-arrow::after {
    position: relative;
    z-index: 2;
}


/* =========================================
   PRODUCT DOTS
========================================= */

.new-product-dots {
    padding: 7px 12px;

    background: rgba(255,255,255,0.85);

    border: 1px solid #e1eaf2;

    border-radius: 30px;

    box-shadow:
        0 5px 18px rgba(16,35,61,0.05);
}

.new-product-dot {
    position: relative;
}

.new-product-dot:hover {
    background: #8bbcf0;
}

.new-product-dot.active {
    box-shadow:
        0 4px 10px rgba(8,124,242,0.25);
}


/* =========================================
   PRODUCT PAGE TRANSITION
========================================= */

.product-page.active .new-product-card {
    animation: productCardEnter 0.5s ease both;
}

.product-page.active .new-product-card:nth-child(1) {
    animation-delay: 0.05s;
}

.product-page.active .new-product-card:nth-child(2) {
    animation-delay: 0.10s;
}

.product-page.active .new-product-card:nth-child(3) {
    animation-delay: 0.15s;
}

.product-page.active .new-product-card:nth-child(4) {
    animation-delay: 0.20s;
}

@keyframes productCardEnter {

    from {
        opacity: 0;
        transform: translateY(18px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }

}


/* =========================================
   MOBILE NAVIGATION
========================================= */

@media (max-width: 600px) {

    .new-product-controls {
        gap: 14px;
        margin-top: 24px;
    }

    .new-product-arrow {
        width: 40px;
        height: 40px;
    }

    .new-product-dots {
        padding: 6px 10px;
    }

}


/* =========================================
   PRODUCT CARD — PERFECT ALIGNMENT
========================================= */

.new-product-card {
    height: 100%;
    min-height: 500px;
    display: flex;
    flex-direction: column;
}

.new-product-image {
    flex-shrink: 0;
}

.new-product-info {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.new-product-info p {
    margin-bottom: 18px;
}

.new-product-info a {
    margin-top: auto;
}

/* Keep product cards aligned on every page */
.product-page {
    align-items: stretch;
}

/* Mobile */
@media (max-width: 600px) {

    .new-product-card {
        min-height: 450px;
    }

}


/* =========================================
   PRODUCT IMAGE — SPOTLIGHT HOVER
========================================= */

.new-product-image {
    position: relative;
    overflow: hidden;
    isolation: isolate;
}

/* Premium spotlight */
.new-product-image::before {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%) scale(0.4);
    background: radial-gradient(
        circle,
        rgba(8, 124, 242, 0.18) 0%,
        rgba(8, 124, 242, 0.08) 35%,
        transparent 70%
    );
    opacity: 0;
    transition:
        transform 0.55s ease,
        opacity 0.45s ease;
    z-index: 1;
}

/* Product image */
.new-product-image img {
    position: relative;
    z-index: 2;
    transition:
        transform 0.55s cubic-bezier(.2,.8,.2,1),
        filter 0.55s ease;
}

/* Hover spotlight */
.new-product-card:hover .new-product-image::before {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.5);
}

/* Product rises slightly */
.new-product-card:hover .new-product-image img {
    transform: translateY(-8px) scale(1.025);
    filter:
        drop-shadow(0 14px 20px rgba(8, 80, 150, 0.18));
}

/* =========================================
   PRODUCT CARD — PREMIUM HOVER
========================================= */

.new-product-card {
    position: relative;
    overflow: hidden;
    transition:
        transform 0.4s cubic-bezier(.2,.8,.2,1),
        box-shadow 0.4s ease,
        border-color 0.4s ease;
}

/* Soft blue light at bottom */
.new-product-card::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: -80px;
    width: 220px;
    height: 120px;
    transform: translateX(-50%);
    background: rgba(8, 124, 242, 0.08);
    filter: blur(35px);
    opacity: 0;
    transition: all 0.45s ease;
    pointer-events: none;
}

/* Hover */
.new-product-card:hover {
    transform: translateY(-10px);
    border-color: rgba(8, 124, 242, 0.28);
    box-shadow:
        0 22px 45px rgba(16, 35, 61, 0.12),
        0 5px 15px rgba(8, 124, 242, 0.06);
}

/* Activate bottom glow */
.new-product-card:hover::after {
    opacity: 1;
    bottom: -45px;
}

/* Keep content above glow */
.new-product-card > * {
    position: relative;
    z-index: 2;
}


/* =========================================
   PRODUCT — KNOW MORE BUTTON
========================================= */

.new-product-info a {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: fit-content;
    padding: 9px 15px;
    border-radius: 8px;
    color: #087cf2;
    background: #f3f8fd;
    text-decoration: none;
    font-weight: 700;
    overflow: hidden;
    transition:
        color 0.3s ease,
        background 0.3s ease,
        transform 0.3s ease,
        gap 0.3s ease;
}

.new-product-info a b {
    font-size: 18px;
    line-height: 1;
    transition: transform 0.3s ease;
}

/* Hover */
.new-product-info a:hover {
    color: #ffffff;
    background: #087cf2;
    gap: 13px;
    transform: translateX(3px);
}

.new-product-info a:hover b {
    transform: translateX(3px);
}

/* =========================================
   PRODUCT TITLE — PREMIUM TYPOGRAPHY
========================================= */

.new-product-info h3 {
    position: relative;
    line-height: 1.25;
    letter-spacing: -0.3px;
    transition:
        color 0.3s ease,
        transform 0.3s ease;
}

/* Small accent line */
.new-product-info h3::after {
    content: "";
    display: block;
    width: 0;
    height: 2px;
    margin-top: 8px;
    border-radius: 10px;
    background: #087cf2;
    transition: width 0.35s ease;
}

/* Card hover */
.new-product-card:hover .new-product-info h3 {
    color: #087cf2;
    transform: translateX(3px);
}

.new-product-card:hover .new-product-info h3::after {
    width: 38px;
}

/* =========================================
   PRODUCT CATEGORY — PREMIUM LABEL
========================================= */

.new-product-info > span {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    padding: 5px 10px;
    margin-bottom: 10px;
    border: 1px solid rgba(8, 124, 242, 0.15);
    border-radius: 20px;
    background: #f4f9fe;
    color: #087cf2;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    transition:
        background 0.3s ease,
        color 0.3s ease,
        border-color 0.3s ease;
}

/* Card hover */
.new-product-card:hover .new-product-info > span {
    color: #ffffff;
    background: #087cf2;
    border-color: #087cf2;
}

/* =========================================
   PRODUCT MODEL — PREMIUM STYLE
========================================= */

.new-product-info p {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 4px;
    padding: 8px 11px;
    width: fit-content;
    border-left: 3px solid #087cf2;
    border-radius: 5px;
    background: #f7faff;
    color: #66788a;
    font-size: 12px;
    transition:
        background 0.3s ease,
        border-color 0.3s ease,
        transform 0.3s ease;
}

.new-product-info p strong {
    color: #1d344d;
    font-weight: 700;
}

/* Card hover */
.new-product-card:hover .new-product-info p {
    background: #eef6ff;
    border-color: #087cf2;
    transform: translateX(3px);
}

/* =========================================================
   AMPTRONIX — PREMIUM PRODUCT SHOWCASE
   FINAL VISUAL OVERRIDE
========================================================= */

/* ---------------------------------------------------------
   SECTION
--------------------------------------------------------- */

.featured-products {
    position: relative;
    padding: 90px 7% 75px;
    background:
        radial-gradient(
            circle at 10% 20%,
            rgba(8, 124, 242, 0.055),
            transparent 28%
        ),
        radial-gradient(
            circle at 90% 80%,
            rgba(8, 124, 242, 0.045),
            transparent 30%
        ),
        #f8fbff;
    overflow: hidden;
}

/* Subtle background grid */
.featured-products::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(
            rgba(8, 124, 242, 0.025) 1px,
            transparent 1px
        ),
        linear-gradient(
            90deg,
            rgba(8, 124, 242, 0.025) 1px,
            transparent 1px
        );
    background-size: 45px 45px;
    pointer-events: none;
    mask-image: linear-gradient(
        to bottom,
        transparent,
        black 20%,
        black 80%,
        transparent
    );
}

/* ---------------------------------------------------------
   HEADING
--------------------------------------------------------- */

.featured-products-heading {
    position: relative;
    z-index: 2;
    max-width: 720px;
    margin: 0 auto 48px;
    text-align: center;
}

.featured-products-heading .section-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    margin-bottom: 16px;
    border: 1px solid rgba(8, 124, 242, 0.15);
    border-radius: 30px;
    background: rgba(255, 255, 255, 0.8);
    color: #087cf2;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.8px;
}

.featured-products-heading .section-label::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #087cf2;
    box-shadow: 0 0 0 5px rgba(8, 124, 242, 0.08);
}

.featured-products-heading h2 {
    margin: 0;
    color: #10233d;
    font-size: clamp(34px, 4vw, 52px);
    line-height: 1.08;
    letter-spacing: -1.8px;
}

.featured-products-heading h2 span {
    color: #087cf2;
}

.featured-products-heading p {
    max-width: 600px;
    margin: 18px auto 0;
    color: #687b90;
    font-size: 15px;
    line-height: 1.8;
}


/* ---------------------------------------------------------
   PRODUCT SHOWCASE
--------------------------------------------------------- */

.product-showcase {
    position: relative;
    z-index: 2;
    max-width: 1500px;
    margin: 0 auto;
}

.product-page {
    gap: 24px;
}


/* ---------------------------------------------------------
   PRODUCT CARD
--------------------------------------------------------- */

.new-product-card {
    position: relative;
    min-width: 0;
    min-height: 530px;

    display: flex;
    flex-direction: column;

    border: 1px solid #dce7f2;
    border-radius: 22px;

    background: rgba(255, 255, 255, 0.96);

    box-shadow:
        0 8px 30px rgba(16, 35, 61, 0.055);

    overflow: hidden;

    transition:
        transform 0.45s cubic-bezier(.2,.8,.2,1),
        box-shadow 0.45s ease,
        border-color 0.45s ease;
}

/* Blue top line */
.new-product-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;

    background: linear-gradient(
        90deg,
        #087cf2,
        #52b8ff,
        #087cf2
    );

    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.45s ease;

    z-index: 5;
}

/* Hover card */
.new-product-card:hover {
    transform: translateY(-10px);

    border-color: rgba(8, 124, 242, 0.25);

    box-shadow:
        0 25px 55px rgba(16, 35, 61, 0.11),
        0 8px 22px rgba(8, 124, 242, 0.06);
}

.new-product-card:hover::before {
    transform: scaleX(1);
}


/* ---------------------------------------------------------
   PRODUCT IMAGE AREA
--------------------------------------------------------- */

.new-product-image {
    position: relative;

    height: 300px;
    min-height: 300px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 22px;

    background:
        radial-gradient(
            circle at center,
            rgba(255,255,255,0.95),
            rgba(235,244,251,0.95)
        );

    border-bottom: 1px solid #e5edf5;

    overflow: hidden;
    isolation: isolate;
}

/* Soft center spotlight */
.new-product-image::before {
    content: "";
    position: absolute;

    width: 260px;
    height: 260px;

    left: 50%;
    top: 50%;

    transform:
        translate(-50%, -50%)
        scale(.75);

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(8,124,242,0.12),
            rgba(8,124,242,0.035) 42%,
            transparent 70%
        );

    opacity: .8;

    transition:
        transform .6s ease,
        opacity .6s ease;

    z-index: 0;
}

/* Product image */
.new-product-image img {
    position: relative;
    z-index: 2;

    width: 92%;
    height: 92%;

    object-fit: contain;

    transform: scale(1.04);

    transition:
        transform .6s cubic-bezier(.2,.8,.2,1),
        filter .6s ease;
}

/* Image hover */
.new-product-card:hover .new-product-image::before {
    transform:
        translate(-50%, -50%)
        scale(1.15);

    opacity: 1;
}

.new-product-card:hover .new-product-image img {
    transform:
        translateY(-5px)
        scale(1.10);

    filter:
        drop-shadow(
            0 18px 22px
            rgba(20,70,120,.18)
        );
}


/* ---------------------------------------------------------
   PRODUCT INFORMATION
--------------------------------------------------------- */

.new-product-info {
    position: relative;
    z-index: 3;

    display: flex;
    flex-direction: column;
    flex: 1;

    padding: 24px 26px 25px;

    background: #ffffff;
}


/* Category */

.new-product-info > span {
    display: inline-flex;
    align-items: center;

    width: fit-content;

    padding: 6px 11px;
    margin-bottom: 13px;

    border: 1px solid rgba(8,124,242,.15);
    border-radius: 30px;

    background: #f3f8fd;

    color: #087cf2;

    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;

    transition:
        background .3s ease,
        color .3s ease,
        border-color .3s ease;
}

.new-product-card:hover .new-product-info > span {
    color: #ffffff;
    background: #087cf2;
    border-color: #087cf2;
}


/* Product title */

.new-product-info h3 {
    margin: 0;

    min-height: 54px;

    color: #10233d;

    font-family: Arial, sans-serif;
    font-size: 19px;
    font-weight: 750;

    line-height: 1.35;
    letter-spacing: -.3px;

    transition:
        color .3s ease,
        transform .3s ease;
}

.new-product-card:hover .new-product-info h3 {
    color: #087cf2;
    transform: translateX(2px);
}


/* Remove old title underline effect */

.new-product-info h3::after {
    display: none;
}


/* ---------------------------------------------------------
   MODEL NUMBER
--------------------------------------------------------- */

.new-product-info p {
    display: flex;
    align-items: center;
    gap: 5px;

    width: fit-content;

    margin: 18px 0 22px;
    padding: 8px 11px;

    border-left: 3px solid #087cf2;
    border-radius: 5px;

    background: #f6f9fc;

    color: #718397;

    font-size: 11px;

    transition:
        background .3s ease,
        transform .3s ease;
}

.new-product-info p strong {
    color: #19334e;
    font-weight: 800;
}

.new-product-card:hover .new-product-info p {
    background: #eef6ff;
    transform: translateX(3px);
}


/* ---------------------------------------------------------
   KNOW MORE
--------------------------------------------------------- */

.new-product-info a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;

    width: fit-content;

    margin-top: auto;

    padding: 10px 15px;

    border: 1px solid #dceafa;
    border-radius: 9px;

    background: #f3f8fd;

    color: #087cf2;

    font-size: 12px;
    font-weight: 800;

    text-decoration: none;

    transition:
        background .3s ease,
        color .3s ease,
        border-color .3s ease,
        transform .3s ease,
        gap .3s ease;
}

.new-product-info a b {
    font-size: 17px;
    line-height: 1;

    transition: transform .3s ease;
}

.new-product-info a:hover {
    color: #ffffff;
    background: #087cf2;
    border-color: #087cf2;

    gap: 13px;

    transform: translateX(2px);
}

.new-product-info a:hover b {
    transform: translateX(3px);
}


/* ---------------------------------------------------------
   DISABLED / NO DOCUMENT
--------------------------------------------------------- */

/*
   Products without a PDF should not look broken.
   They remain visually consistent but clearly unavailable.
*/

.new-product-info a[style*="not-allowed"] {
    opacity: 1 !important;

    color: #91a4b7 !important;
    background: #f5f7fa !important;
    border-color: #e5ebf1 !important;

    cursor: not-allowed !important;
}

.new-product-info a[style*="not-allowed"] b {
    color: #a6b6c5;
}


/* ---------------------------------------------------------
   PRODUCT NAVIGATION
--------------------------------------------------------- */

.new-product-controls {
    position: relative;
    z-index: 5;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 18px;

    margin-top: 38px;
}


/* Arrow buttons */

.new-product-arrow {
    position: relative;

    width: 52px;
    height: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #dce8f3;
    border-radius: 50%;

    background: #ffffff;

    color: #087cf2;

    font-size: 21px;
    font-weight: 500;

    cursor: pointer;

    box-shadow:
        0 8px 22px rgba(16,35,61,.07);

    transition:
        transform .3s ease,
        background .3s ease,
        color .3s ease,
        box-shadow .3s ease;
}

.new-product-arrow:hover {
    color: #ffffff;
    background: #087cf2;

    transform: translateY(-3px);

    box-shadow:
        0 12px 25px rgba(8,124,242,.22);
}


/* Dots */

.new-product-dots {
    display: flex;
    align-items: center;
    gap: 8px;

    padding: 8px 14px;

    border: 1px solid #e0e9f2;
    border-radius: 30px;

    background: rgba(255,255,255,.9);

    box-shadow:
        0 8px 22px rgba(16,35,61,.05);
}

.new-product-dot {
    width: 9px;
    height: 9px;

    padding: 0;

    border: 0;
    border-radius: 50%;

    background: #c9d9e8;

    cursor: pointer;

    transition:
        width .3s ease,
        background .3s ease,
        transform .3s ease;
}

.new-product-dot:hover {
    background: #7fb7e9;
}

.new-product-dot.active {
    width: 30px;

    border-radius: 10px;

    background: #087cf2;

    box-shadow:
        0 4px 12px rgba(8,124,242,.25);
}


/* ---------------------------------------------------------
   PRODUCT PAGE ANIMATION
--------------------------------------------------------- */

.product-page.active .new-product-card {
    animation: premiumProductIn .55s ease both;
}

.product-page.active .new-product-card:nth-child(1) {
    animation-delay: .05s;
}

.product-page.active .new-product-card:nth-child(2) {
    animation-delay: .10s;
}

.product-page.active .new-product-card:nth-child(3) {
    animation-delay: .15s;
}

.product-page.active .new-product-card:nth-child(4) {
    animation-delay: .20s;
}

@keyframes premiumProductIn {

    from {
        opacity: 0;
        transform: translateY(22px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* ---------------------------------------------------------
   TABLET
--------------------------------------------------------- */

@media (max-width: 1100px) {

    .product-page {
        grid-template-columns: repeat(2, 1fr);
    }

    .new-product-card {
        min-height: 510px;
    }

}


/* ---------------------------------------------------------
   MOBILE
--------------------------------------------------------- */

@media (max-width: 650px) {

    .featured-products {
        padding: 70px 5% 60px;
    }

    .featured-products-heading {
        margin-bottom: 35px;
    }

    .featured-products-heading h2 {
        font-size: 34px;
    }

    .featured-products-heading p {
        font-size: 14px;
    }

    .product-page {
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .new-product-card {
        min-height: 0;
    }

    .new-product-image {
        height: 270px;
        min-height: 270px;
    }

    .new-product-image img {
        width: 88%;
        height: 88%;
    }

    .new-product-info {
        padding: 22px;
    }

    .new-product-info h3 {
        min-height: auto;
        font-size: 18px;
    }

    .new-product-controls {
        margin-top: 28px;
        gap: 12px;
    }

    .new-product-arrow {
        width: 44px;
        height: 44px;
    }

}

/* =========================================================
   AMPTRONIX PRODUCT IMAGE — FINAL CLEAN DESIGN
========================================================= */

/* Product image container */
.new-product-image {
    position: relative;

    width: 100%;
    height: 300px;
    min-height: 300px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 8px;

    overflow: hidden;

    /* Soft background matching Amptronix theme */
    background:
        radial-gradient(
            circle at center,
            #f8fbfe 0%,
            #eef5fa 55%,
            #e8f1f7 100%
        );

    border-bottom: 1px solid #e0eaf2;

    isolation: isolate;
}


/* Very subtle background light */
.new-product-image::before {
    content: "";

    position: absolute;

    width: 240px;
    height: 240px;

    left: 50%;
    top: 50%;

    transform: translate(-50%, -50%);

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(255,255,255,0.85) 0%,
            rgba(255,255,255,0.35) 45%,
            transparent 72%
        );

    pointer-events: none;

    z-index: 0;
}


/* =========================================
   PRODUCT IMAGE
========================================= */

.new-product-image img {
    position: relative;

    z-index: 2;

    /* Use almost the complete image area */
    width: 108%;
    height: 108%;

    max-width: none;
    max-height: none;

    object-fit: contain;

    transform: scale(1.08);

    transform-origin: center center;

    transition:
        transform 0.55s cubic-bezier(.2,.8,.2,1),
        filter 0.55s ease;
}


/* =========================================
   HOVER
========================================= */

.new-product-card:hover .new-product-image img {
    transform:
        scale(1.15)
        translateY(-3px);

    filter:
        drop-shadow(
            0 14px 18px
            rgba(35,75,110,0.16)
        );
}


/* =========================================
   REMOVE EXCESSIVE OLD EFFECTS
========================================= */

.new-product-image::after {
    display: none !important;
}


/* =========================================
   MOBILE
========================================= */

@media (max-width: 650px) {

    .new-product-image {
        height: 270px;
        min-height: 270px;
        padding: 6px;
    }

    .new-product-image img {
        width: 108%;
        height: 108%;
        transform: scale(1.06);
    }

    .new-product-card:hover .new-product-image img {
        transform: scale(1.12) translateY(-2px);
    }
}

/* =========================================
   PRODUCT BUTTONS — CONSISTENT UI
========================================= */

/* Every available Know More button */
.new-product-info a[href]:not([style*="not-allowed"]) {
    color: #087cf2 !important;
    background: #f3f8fd !important;
    border: 1px solid #d7e8f7 !important;
    opacity: 1 !important;
}

/* Hover */
.new-product-info a[href]:not([style*="not-allowed"]):hover {
    color: #ffffff !important;
    background: #087cf2 !important;
    border-color: #087cf2 !important;

    transform: translateX(3px);
    gap: 13px;

    box-shadow:
        0 8px 18px rgba(8, 124, 242, 0.18);
}

/* Arrow */
.new-product-info a[href]:not([style*="not-allowed"]) b {
    color: #087cf2;
    transition: transform 0.3s ease;
}

.new-product-info a[href]:not([style*="not-allowed"]):hover b {
    color: #ffffff;
    transform: translateX(3px);
}

/* =========================================
   PRODUCTS WITHOUT PDF
========================================= */

.new-product-info a[style*="not-allowed"] {
    opacity: 0.55 !important;

    color: #8798a8 !important;
    background: #f4f6f8 !important;
    border-color: #e2e8ed !important;

    cursor: not-allowed !important;
}

/* =========================================================
   AMPTRONIX — PREMIUM APPLICATIONS SECTION
========================================================= */

.applications {
    position: relative;
    padding: 90px 7% 80px !important;
    background:
        radial-gradient(
            circle at 8% 20%,
            rgba(8,124,242,.045),
            transparent 28%
        ),
        radial-gradient(
            circle at 92% 80%,
            rgba(8,124,242,.04),
            transparent 28%
        ),
        #ffffff;

    overflow: hidden;
}


/* Background grid */

.applications::before {
    content: "";

    position: absolute;
    inset: 0;

    background-image:
        linear-gradient(
            rgba(8,124,242,.022) 1px,
            transparent 1px
        ),
        linear-gradient(
            90deg,
            rgba(8,124,242,.022) 1px,
            transparent 1px
        );

    background-size: 45px 45px;

    pointer-events: none;

    mask-image:
        linear-gradient(
            to bottom,
            transparent,
            black 20%,
            black 80%,
            transparent
        );
}


/* =========================================================
   HEADING
========================================================= */

.applications-heading {
    position: relative;
    z-index: 2;

    max-width: 720px;

    margin: 0 auto 48px !important;

    text-align: center;
}

.applications-heading .section-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 7px 14px;
    margin-bottom: 15px;

    border: 1px solid rgba(8,124,242,.15);
    border-radius: 30px;

    background: #f7fbff;

    color: #087cf2;

    font-size: 11px;
    font-weight: 800;

    letter-spacing: 1.8px;
}

.applications-heading .section-label::before {
    content: "";

    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #087cf2;

    box-shadow:
        0 0 0 5px rgba(8,124,242,.08);
}

.applications-heading h2 {
    margin: 0;

    color: #10233d;

    font-size: clamp(34px,4vw,50px);

    line-height: 1.08;

    letter-spacing: -1.6px;
}

.applications-heading h2 span {
    color: #087cf2;
}

.applications-heading p {
    max-width: 620px;

    margin: 18px auto 0;

    color: #6c7e92;

    font-size: 15px;
    line-height: 1.8;
}


/* =========================================================
   SLIDER
========================================================= */

.applications-slider {
    position: relative;
    z-index: 2;

    max-width: 1500px;

    margin: 0 auto;
}

.application-slide {
    display: none;

    grid-template-columns: repeat(4,1fr);

    gap: 24px;

    width: 100%;
}

.application-slide.active {
    display: grid;

    animation: applicationPremiumIn .55s ease both;
}


/* =========================================================
   APPLICATION CARD
========================================================= */

.application-image-card {
    position: relative;

    height: 330px;

    border: 1px solid #dce7f1;

    border-radius: 22px;

    background:
        linear-gradient(
            145deg,
            #f8fbfe,
            #edf5fa
        );

    overflow: hidden;

    cursor: zoom-in;

    box-shadow:
        0 8px 28px rgba(16,35,61,.055);

    transition:
        transform .45s cubic-bezier(.2,.8,.2,1),
        box-shadow .45s ease,
        border-color .45s ease;
}


/* Top accent */

.application-image-card::before {
    content: "";

    position: absolute;

    left: 0;
    top: 0;

    width: 100%;
    height: 3px;

    background:
        linear-gradient(
            90deg,
            #087cf2,
            #63bdff,
            #087cf2
        );

    transform: scaleX(0);

    transform-origin: left;

    transition: transform .45s ease;

    z-index: 5;
}


/* Soft spotlight */

.application-image-card::after {
    content: "";

    position: absolute;

    width: 230px;
    height: 230px;

    left: 50%;
    top: 50%;

    transform:
        translate(-50%,-50%)
        scale(.65);

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(255,255,255,.95),
            rgba(255,255,255,.25) 48%,
            transparent 72%
        );

    pointer-events: none;

    transition:
        transform .5s ease,
        opacity .5s ease;

    z-index: 0;
}


/* Application image */

.application-image-card img {
    position: relative;

    z-index: 2;

    width: 100%;
    height: 100%;

    object-fit: cover;

    display: block;

    transition:
        transform .6s cubic-bezier(.2,.8,.2,1),
        filter .5s ease;
}


/* =========================================================
   HOVER
========================================================= */

.application-image-card:hover {
    transform: translateY(-9px);

    border-color: rgba(8,124,242,.28);

    box-shadow:
        0 24px 50px rgba(16,35,61,.12),
        0 8px 22px rgba(8,124,242,.05);
}

.application-image-card:hover::before {
    transform: scaleX(1);
}

.application-image-card:hover::after {
    transform:
        translate(-50%,-50%)
        scale(1.15);
}

.application-image-card:hover img {
    transform: scale(1.055);

    filter:
        saturate(1.04);
}


/* =========================================================
   SLIDER CONTROLS
========================================================= */

.application-controls {
    position: relative;
    z-index: 3;

    display: flex;

    align-items: center;
    justify-content: center;

    gap: 18px;

    margin-top: 38px !important;
}


/* Arrow */

.application-arrow {
    width: 50px;
    height: 50px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #dce7f1;

    border-radius: 50%;

    background: #ffffff;

    color: #087cf2;

    font-size: 21px;

    cursor: pointer;

    box-shadow:
        0 8px 20px rgba(16,35,61,.06);

    transition:
        transform .3s ease,
        background .3s ease,
        color .3s ease,
        box-shadow .3s ease;
}

.application-arrow:hover {
    transform: translateY(-3px);

    color: #ffffff;

    background: #087cf2;

    box-shadow:
        0 12px 25px rgba(8,124,242,.20);
}


/* Dots container */

.application-dots {
    display: flex;
    align-items: center;

    gap: 7px;

    padding: 8px 13px;

    border: 1px solid #e1eaf2;

    border-radius: 30px;

    background: rgba(255,255,255,.92);

    box-shadow:
        0 8px 20px rgba(16,35,61,.045);
}


/* Dot */

.application-dot {
    width: 8px;
    height: 8px;

    padding: 0;

    border: none;

    border-radius: 10px;

    background: #c8d8e7;

    cursor: pointer;

    transition:
        width .3s ease,
        background .3s ease;
}

.application-dot:hover {
    background: #7fb7e9;
}

.application-dot.active {
    width: 28px;

    background: #087cf2;

    box-shadow:
        0 4px 10px rgba(8,124,242,.20);
}


/* =========================================================
   SLIDE ANIMATION
========================================================= */

@keyframes applicationPremiumIn {

    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }

}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 900px) {

    .applications {
        padding: 75px 5% 65px !important;
    }

    .application-slide {
        grid-template-columns: repeat(2,1fr);
        gap: 20px;
    }

    .application-image-card {
        height: 280px;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .applications {
        padding: 65px 20px 55px !important;
    }

    .applications-heading {
        margin-bottom: 32px !important;
    }

    .applications-heading h2 {
        font-size: 32px;
    }

    .applications-heading p {
        font-size: 14px;
    }

    .application-slide {
        grid-template-columns: 1fr 1fr;
        gap: 13px;
    }

    .application-image-card {
        height: 190px;
        border-radius: 15px;
    }

    .application-controls {
        margin-top: 25px !important;
        gap: 12px;
    }

    .application-arrow {
        width: 42px;
        height: 42px;
        font-size: 18px;
    }

    .application-dots {
        padding: 7px 10px;
    }

}

/* =========================================================
   AMPTRONIX — PREMIUM PORTRAIT APPLICATION CARDS
   FULL IMAGE / NO CROPPING
========================================================= */

/* APPLICATION SECTION */

.applications {
    position: relative;

    padding: 95px 6% 85px !important;

    background:
        radial-gradient(
            circle at 5% 15%,
            rgba(8,124,242,.06),
            transparent 28%
        ),
        radial-gradient(
            circle at 95% 85%,
            rgba(8,124,242,.05),
            transparent 28%
        ),
        #ffffff;

    overflow: hidden;
}


/* TECH GRID BACKGROUND */

.applications::before {
    content: "";

    position: absolute;
    inset: 0;

    background-image:
        linear-gradient(
            rgba(8,124,242,.025) 1px,
            transparent 1px
        ),
        linear-gradient(
            90deg,
            rgba(8,124,242,.025) 1px,
            transparent 1px
        );

    background-size: 42px 42px;

    pointer-events: none;
}


/* =========================================================
   HEADING
========================================================= */

.applications-heading {
    position: relative;
    z-index: 2;

    max-width: 800px;

    margin: 0 auto 55px !important;

    text-align: center;
}

.applications-heading .section-label {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 8px;

    padding: 7px 15px;

    margin-bottom: 16px;

    border: 1px solid rgba(8,124,242,.18);

    border-radius: 30px;

    background: rgba(255,255,255,.85);

    color: #087cf2;

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 1.8px;

    box-shadow:
        0 6px 20px rgba(8,124,242,.05);
}

.applications-heading .section-label::before {
    content: "";

    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #087cf2;

    box-shadow:
        0 0 0 5px rgba(8,124,242,.08);
}


.applications-heading h2 {
    margin: 0;

    color: #10233d;

    font-size: clamp(36px, 4vw, 54px);

    line-height: 1.08;

    letter-spacing: -1.8px;
}

.applications-heading h2 span {
    color: #087cf2;
}


.applications-heading p {
    max-width: 700px;

    margin: 20px auto 0;

    color: #6c7e92;

    font-size: 15px;

    line-height: 1.8;
}


/* =========================================================
   SLIDER
========================================================= */

.applications-slider {
    position: relative;
    z-index: 2;

    width: 100%;

    max-width: 1500px;

    margin: 0 auto;
}


.application-slide {
    display: none;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 26px;

    width: 100%;
}

.application-slide.active {
    display: grid;

    animation:
        applicationPremiumEnter .55s ease both;
}


/* =========================================================
   PORTRAIT APPLICATION CARD
========================================================= */

.application-image-card {

    position: relative;

    width: 100%;

    /* IMPORTANT:
       Portrait ratio of your uploaded application images
    */

    aspect-ratio: 1228 / 2048;

    height: auto !important;

    min-height: 0;

    box-sizing: border-box;

    overflow: hidden;

    border-radius: 22px;

    border: 1px solid #d9e6f0;

    background:
        linear-gradient(
            145deg,
            #f7fbfe,
            #eaf3f8
        );

    cursor: zoom-in;

    box-shadow:
        0 10px 30px rgba(16,35,61,.07);

    transition:
        transform .45s cubic-bezier(.2,.8,.2,1),
        box-shadow .45s ease,
        border-color .45s ease;
}


/* Premium top line */

.application-image-card::before {

    content: "";

    position: absolute;

    left: 0;
    top: 0;

    width: 100%;
    height: 4px;

    z-index: 5;

    background:
        linear-gradient(
            90deg,
            #087cf2,
            #51b8ff,
            #087cf2
        );

    transform: scaleX(0);

    transform-origin: left;

    transition:
        transform .45s ease;
}


/* Soft border glow */

.application-image-card::after {

    content: "";

    position: absolute;

    inset: 0;

    z-index: 3;

    pointer-events: none;

    border-radius: inherit;

    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,.5);

    transition:
        box-shadow .45s ease;
}


/* =========================================================
   FULL PORTRAIT IMAGE
========================================================= */

.application-image-card img {

    position: relative;

    z-index: 2;

    display: block;

    width: 100% !important;

    height: 100% !important;

    /*
       VERY IMPORTANT:
       contain = complete image visible
       cover = image gets cropped
    */

    object-fit: contain !important;

    object-position: center center;

    background: #edf5fa;

    transition:
        transform .55s cubic-bezier(.2,.8,.2,1),
        filter .4s ease;
}


/* =========================================================
   HOVER
========================================================= */

.application-image-card:hover {

    transform: translateY(-10px);

    border-color:
        rgba(8,124,242,.30);

    box-shadow:
        0 25px 55px rgba(16,35,61,.14),
        0 8px 25px rgba(8,124,242,.06);
}


.application-image-card:hover::before {

    transform: scaleX(1);
}


.application-image-card:hover::after {

    box-shadow:
        inset 0 0 0 1px rgba(8,124,242,.18),
        inset 0 0 35px rgba(8,124,242,.04);
}


.application-image-card:hover img {

    transform: scale(1.025);

    filter:
        saturate(1.03)
        brightness(1.01);
}


/* =========================================================
   CONTROLS
========================================================= */

.application-controls {

    position: relative;

    z-index: 4;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 18px;

    margin-top: 42px !important;
}


/* ARROWS */

.application-arrow {

    width: 50px !important;
    height: 50px !important;

    display: flex;

    align-items: center;
    justify-content: center;

    padding: 0;

    border: 1px solid #d8e5ef;

    border-radius: 50%;

    background: rgba(255,255,255,.95);

    color: #10233d;

    font-size: 20px;

    cursor: pointer;

    box-shadow:
        0 8px 22px rgba(16,35,61,.07);

    transition:
        transform .3s ease,
        background .3s ease,
        color .3s ease,
        border-color .3s ease,
        box-shadow .3s ease;
}


.application-arrow:hover {

    transform:
        translateY(-4px)
        scale(1.04);

    background: #087cf2;

    border-color: #087cf2;

    color: white;

    box-shadow:
        0 14px 30px rgba(8,124,242,.22);
}


/* =========================================================
   DOTS
========================================================= */

.application-dots {

    display: flex;

    align-items: center;

    gap: 7px;

    padding: 8px 13px;

    border: 1px solid #e0eaf2;

    border-radius: 30px;

    background: rgba(255,255,255,.95);

    box-shadow:
        0 8px 22px rgba(16,35,61,.06);
}


.application-dot {

    width: 8px !important;
    height: 8px !important;

    padding: 0;

    border: none;

    border-radius: 20px;

    background: #c9d8e5;

    cursor: pointer;

    transition:
        width .3s ease,
        background .3s ease,
        transform .3s ease;
}


.application-dot:hover {

    background: #72b1e9;

    transform: scale(1.15);
}


.application-dot.active {

    width: 28px !important;

    background: #087cf2;

    box-shadow:
        0 4px 12px rgba(8,124,242,.25);
}


/* =========================================================
   SLIDE ANIMATION
========================================================= */

@keyframes applicationPremiumEnter {

    from {
        opacity: 0;
        transform: translateY(18px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }

}


/* =========================================================
   LARGE SCREEN
========================================================= */

@media (min-width: 1400px) {

    .application-slide {

        gap: 30px;
    }

}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 1100px) {

    .applications {

        padding:
            80px 5% 70px !important;
    }

    .application-slide {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 24px;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .applications {

        padding:
            65px 20px 55px !important;
    }


    .applications-heading {

        margin-bottom: 35px !important;
    }


    .applications-heading h2 {

        font-size: 32px;

        letter-spacing: -1px;
    }


    .applications-heading p {

        font-size: 14px;

        line-height: 1.7;
    }


    .application-slide {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 14px;
    }


    /*
       Keep the original portrait ratio
       even on mobile
    */

    .application-image-card {

        aspect-ratio: 1228 / 2048;

        height: auto !important;

        border-radius: 15px;
    }


    .application-image-card:hover {

        transform: translateY(-5px);
    }


    .application-image-card:hover img {

        transform: scale(1.015);
    }


    .application-controls {

        margin-top: 28px !important;

        gap: 12px;
    }


    .application-arrow {

        width: 42px !important;

        height: 42px !important;

        font-size: 18px;
    }


    .application-dots {

        padding: 7px 10px;
    }

}


/* =========================================================
   APPLICATIONS — CLEAN PREMIUM HOVER
========================================================= */

/* Remove the previous card movement */
.application-image-card {
    transform: none !important;

    transition:
        border-color 0.35s ease,
        box-shadow 0.35s ease;
}


/* Remove the animated top blue line */
.application-image-card::before {
    display: none !important;
}


/* Remove the previous inner glow */
.application-image-card::after {
    display: none !important;
}


/* Clean card hover */
.application-image-card:hover {
    transform: none !important;

    border-color: #cbddea;

    box-shadow:
        0 12px 30px rgba(16, 35, 61, 0.09);
}


/* Image stays almost still */
.application-image-card img {
    transition:
        transform 0.45s ease,
        filter 0.45s ease;
}


/* Very subtle image movement */
.application-image-card:hover img {
    transform: scale(1.025) !important;

    filter: brightness(1.02);
}


/* =========================================================
   CLEAN ARROW HOVER
========================================================= */

.application-arrow {
    transition:
        background 0.25s ease,
        color 0.25s ease,
        border-color 0.25s ease,
        box-shadow 0.25s ease;
}

.application-arrow:hover {
    transform: none !important;

    background: #087cf2;

    color: #ffffff;

    border-color: #087cf2;

    box-shadow:
        0 7px 18px rgba(8, 124, 242, 0.16);
}


/* =========================================================
   CLEAN DOTS
========================================================= */

.application-dot {
    transition:
        width 0.25s ease,
        background 0.25s ease;
}

.application-dot:hover {
    transform: none !important;

    background: #8ebee8;
}

.application-dot.active {
    box-shadow: none !important;
}

/* =========================================================
   APPLICATION NAVIGATION
   REFERENCE-STYLE ARROWS + DOTS
========================================================= */

.application-controls {
    display: flex !important;
    align-items: center;
    justify-content: center;

    gap: 24px !important;

    margin-top: 42px !important;
}


/* =========================================================
   ARROW BUTTON
========================================================= */

.application-arrow {
    position: relative;

    width: 64px !important;
    height: 64px !important;

    flex: 0 0 64px;

    display: flex !important;
    align-items: center;
    justify-content: center;

    padding: 0 !important;

    border: 1px solid #dbe7f1 !important;
    border-radius: 50% !important;

    background: #ffffff !important;

    color: #087cf2 !important;

    font-size: 22px !important;
    font-weight: 400;

    cursor: pointer;

    overflow: hidden;

    isolation: isolate;

    box-shadow:
        0 8px 22px rgba(16, 35, 61, 0.07);

    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        border-color 0.3s ease;
}


/* =========================================================
   INNER BLUE HOVER CIRCLE
========================================================= */

.application-arrow::before {
    content: "";

    position: absolute;

    width: 100%;
    height: 100%;

    left: 0;
    top: 0;

    border-radius: 50%;

    background: #eaf5ff;

    transform: scale(0);

    transform-origin: center;

    transition:
        transform 0.35s cubic-bezier(.2,.8,.2,1);

    z-index: -1;
}


/* =========================================================
   HOVER
========================================================= */

.application-arrow:hover {
    transform: translateY(-4px) !important;

    background: #ffffff !important;

    color: #087cf2 !important;

    border-color: #c8e0f5 !important;

    box-shadow:
        0 12px 28px rgba(8, 124, 242, 0.13);
}


.application-arrow:hover::before {
    transform: scale(1);
}


/* =========================================================
   CLICK / PRESS
========================================================= */

.application-arrow:active {
    transform: translateY(-1px) scale(0.95) !important;

    box-shadow:
        0 5px 14px rgba(8, 124, 242, 0.10);
}


/* =========================================================
   DOT CONTAINER
========================================================= */

.application-dots {
    height: 64px !important;

    min-width: 150px;

    display: flex !important;

    align-items: center;
    justify-content: center;

    gap: 9px;

    padding: 0 18px !important;

    border: 1px solid #dfe9f2 !important;

    border-radius: 34px !important;

    background: #ffffff !important;

    box-shadow:
        0 8px 22px rgba(16, 35, 61, 0.06);

    box-sizing: border-box;
}


/* =========================================================
   DOTS
========================================================= */

.application-dot {
    width: 11px !important;
    height: 11px !important;

    flex: 0 0 auto;

    padding: 0 !important;

    border: none !important;

    border-radius: 20px !important;

    background: #c8d8e7 !important;

    cursor: pointer;

    transition:
        width 0.3s ease,
        background 0.3s ease,
        transform 0.3s ease;
}


/* Dot hover */

.application-dot:hover {
    transform: scale(1.08);

    background: #9fc5e8 !important;
}


/* Active dot */

.application-dot.active {
    width: 38px !important;
    height: 11px !important;

    border-radius: 20px !important;

    background: #087cf2 !important;

    box-shadow:
        0 5px 12px rgba(8, 124, 242, 0.22);
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .application-controls {
        gap: 12px !important;
        margin-top: 28px !important;
    }

    .application-arrow {
        width: 48px !important;
        height: 48px !important;

        flex-basis: 48px;

        font-size: 18px !important;
    }

    .application-dots {
        height: 48px !important;

        min-width: 120px;

        padding: 0 13px !important;

        gap: 7px;
    }

    .application-dot {
        width: 8px !important;
        height: 8px !important;
    }

    .application-dot.active {
        width: 28px !important;
        height: 8px !important;
    }
}

/* =========================================================
   AMPTRONIX — PREMIUM APPLICATION IMAGE PREVIEW
========================================================= */

/* Full-screen overlay */
.application-preview {
    position: fixed !important;
    inset: 0;

    display: none;

    align-items: center;
    justify-content: center;

    padding: 30px;

    box-sizing: border-box;

    background:
        rgba(8, 18, 32, 0.94);

    backdrop-filter: blur(8px);

    z-index: 99999;

    opacity: 0;

    transition:
        opacity 0.3s ease;
}

/* Open state */
.application-preview.show {
    display: flex;

    opacity: 1;
}


/* =========================================================
   PREVIEW IMAGE
========================================================= */

.application-preview-image {
    max-width: 96vw !important;
    max-height: 92vh !important;

    width: auto !important;
    height: auto !important;

    object-fit: contain !important;

    display: block;

    border-radius: 12px;

    box-shadow:
        0 25px 70px rgba(0,0,0,.35);

    transform:
        scale(.94);

    transition:
        transform .35s cubic-bezier(.2,.8,.2,1);

    cursor: zoom-in;

    user-select: none;
}

.application-preview.show .application-preview-image {
    transform: scale(1);
}


/* =========================================================
   CLOSE BUTTON
========================================================= */

.application-preview-close {
    position: absolute;

    top: 24px;
    right: 28px;

    width: 50px;
    height: 50px;

    display: flex !important;

    align-items: center;
    justify-content: center;

    padding: 0;

    border: 1px solid rgba(255,255,255,.18);

    border-radius: 50%;

    background:
        rgba(255,255,255,.10);

    color: #ffffff;

    font-size: 28px;

    line-height: 1;

    cursor: pointer;

    z-index: 100001;

    backdrop-filter: blur(10px);

    transition:
        background .25s ease,
        transform .25s ease,
        border-color .25s ease;
}

.application-preview-close:hover {

    background: #087cf2;

    border-color: #087cf2;

    transform:
        rotate(90deg)
        scale(1.05);
}


/* =========================================================
   ZOOM CONTROL BAR
========================================================= */

.application-zoom-controls {

    position: absolute;

    left: 50%;
    bottom: 28px;

    transform: translateX(-50%);

    display: flex;

    align-items: center;

    gap: 6px;

    padding: 7px;

    border: 1px solid rgba(255,255,255,.12);

    border-radius: 15px;

    background:
        rgba(255,255,255,.94);

    box-shadow:
        0 12px 35px rgba(0,0,0,.22);

    z-index: 100001;
}


/* Zoom buttons */

.application-zoom-button {

    width: 44px;
    height: 44px;

    display: flex;

    align-items: center;
    justify-content: center;

    padding: 0;

    border: none;

    border-radius: 10px;

    background: #f1f6fa;

    color: #10233d;

    font-size: 21px;

    font-weight: 700;

    cursor: pointer;

    transition:
        background .25s ease,
        color .25s ease,
        transform .2s ease;
}

.application-zoom-button:hover {

    background: #087cf2;

    color: #ffffff;

    transform: translateY(-2px);
}


/* Zoom percentage */

.application-zoom-level {

    min-width: 58px;

    text-align: center;

    color: #10233d;

    font-size: 13px;

    font-weight: 800;
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .application-preview {

        padding: 15px;
    }

    .application-preview-image {

        max-width: 94vw !important;

        max-height: 80vh !important;

        border-radius: 9px;
    }

    .application-preview-close {

        top: 14px;

        right: 14px;

        width: 44px;

        height: 44px;

        font-size: 24px;
    }

    .application-zoom-controls {

        bottom: 16px;

        padding: 6px;
    }

    .application-zoom-button {

        width: 40px;

        height: 40px;
    }

    .application-zoom-level {

        min-width: 50px;

        font-size: 12px;
    }

}

/* =========================================================
   AMPTRONIX — PREMIUM CONTACT SECTION
========================================================= */

.contact-section {
    position: relative;

    padding: 100px 7% 95px !important;

    background:
        radial-gradient(
            circle at 8% 20%,
            rgba(8,124,242,.055),
            transparent 28%
        ),
        radial-gradient(
            circle at 92% 80%,
            rgba(8,124,242,.045),
            transparent 30%
        ),
        #f8fbfe;

    overflow: hidden;
}


/* Subtle technology grid */

.contact-section::before {
    content: "";

    position: absolute;
    inset: 0;

    background-image:
        linear-gradient(
            rgba(8,124,242,.022) 1px,
            transparent 1px
        ),
        linear-gradient(
            90deg,
            rgba(8,124,242,.022) 1px,
            transparent 1px
        );

    background-size: 45px 45px;

    pointer-events: none;
}


/* =========================================================
   CONTACT CONTAINER
========================================================= */

.contact-container {
    position: relative;
    z-index: 2;

    max-width: 1250px;

    margin: 0 auto;

    display: grid;

    grid-template-columns:
        .9fr 1.1fr;

    gap: 70px;

    align-items: center;
}


/* =========================================================
   LEFT SIDE
========================================================= */

.contact-info {
    max-width: 520px;
}


.contact-info .section-label {
    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 7px 14px;

    margin-bottom: 17px;

    border: 1px solid rgba(8,124,242,.15);

    border-radius: 30px;

    background: #ffffff;

    color: #087cf2;

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 1.7px;
}


.contact-info .section-label::before {
    content: "";

    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #087cf2;

    box-shadow:
        0 0 0 5px rgba(8,124,242,.08);
}


/* Heading */

.contact-info h2 {
    margin: 0;

    color: #10233d;

    font-size: clamp(36px,4vw,54px);

    line-height: 1.08;

    letter-spacing: -1.8px;
}


.contact-info h2 span {
    color: #087cf2;
}


/* Description */

.contact-info > p {
    max-width: 500px;

    margin: 20px 0 0;

    color: #6b7e91;

    font-size: 15px;

    line-height: 1.85;
}


/* =========================================================
   CONTACT DETAILS
========================================================= */

.contact-details {
    display: flex;

    flex-direction: column;

    gap: 13px;

    margin-top: 32px;
}


.contact-detail {
    display: flex;

    align-items: center;

    gap: 15px;

    padding: 14px 16px;

    width: fit-content;

    border: 1px solid #e1eaf2;

    border-radius: 13px;

    background: rgba(255,255,255,.82);

    box-shadow:
        0 5px 18px rgba(16,35,61,.035);

    transition:
        transform .3s ease,
        border-color .3s ease,
        box-shadow .3s ease;
}


.contact-detail:hover {
    transform: translateX(5px);

    border-color: #c9e0f4;

    box-shadow:
        0 10px 25px rgba(8,124,242,.07);
}


/* Detail icon */

.contact-detail-icon {
    width: 42px;
    height: 42px;

    flex: 0 0 42px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 11px;

    background: #edf6fd;

    color: #087cf2;

    font-size: 17px;
}


/* =========================================================
   SOCIAL
========================================================= */

.social-section {
    margin-top: 30px;
}


.social-section h3 {
    margin: 0 0 13px;

    color: #10233d;

    font-size: 13px;

    font-weight: 800;

    letter-spacing: .4px;
}


.social-icons {
    display: flex;

    align-items: center;

    gap: 9px;
}


.social-icon {
    width: 43px;
    height: 43px;

    display: flex;

    align-items: center;
    justify-content: center;

    border: 1px solid #dce7f0;

    border-radius: 11px;

    background: #ffffff;

    color: #087cf2;

    text-decoration: none;

    font-size: 16px;

    box-shadow:
        0 5px 15px rgba(16,35,61,.045);

    transition:
        transform .3s ease,
        background .3s ease,
        color .3s ease,
        border-color .3s ease,
        box-shadow .3s ease;
}


.social-icon:hover {
    transform: translateY(-4px);

    background: #087cf2;

    border-color: #087cf2;

    color: #ffffff;

    box-shadow:
        0 10px 22px rgba(8,124,242,.18);
}


/* =========================================================
   FORM BOX
========================================================= */

.contact-form-box {
    position: relative;

    padding: 38px;

    border: 1px solid #dce7f1;

    border-radius: 24px;

    background:
        rgba(255,255,255,.96);

    box-shadow:
        0 20px 55px rgba(16,35,61,.08);

    overflow: hidden;
}


/* Blue accent */

.contact-form-box::before {
    content: "";

    position: absolute;

    top: 0;
    left: 0;

    width: 100%;
    height: 3px;

    background:
        linear-gradient(
            90deg,
            #087cf2,
            #55baff,
            #087cf2
        );
}


/* Form heading */

.contact-form-box h3 {
    margin: 0;

    color: #10233d;

    font-size: 24px;

    letter-spacing: -.5px;
}


.contact-form-box > p {
    margin: 8px 0 25px;

    color: #78899a;

    font-size: 13px;

    line-height: 1.6;
}


/* =========================================================
   FORM
========================================================= */

.form-row {
    display: grid;

    grid-template-columns:
        repeat(2,1fr);

    gap: 16px;
}


.form-group {
    margin-bottom: 18px;
}


.form-group label {
    display: block;

    margin-bottom: 7px;

    color: #30465c;

    font-size: 12px;

    font-weight: 700;
}


.form-group input,
.form-group textarea {

    width: 100%;

    box-sizing: border-box;

    border: 1px solid #dce6ef;

    border-radius: 10px;

    background: #f9fbfd;

    color: #10233d;

    font-family: inherit;

    font-size: 13px;

    outline: none;

    transition:
        border-color .25s ease,
        background .25s ease,
        box-shadow .25s ease;
}


.form-group input {
    height: 48px;

    padding: 0 14px;
}


.form-group textarea {
    min-height: 135px;

    padding: 13px 14px;

    resize: vertical;
}


/* Placeholder */

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #a0afbd;
}


/* Focus */

.form-group input:focus,
.form-group textarea:focus {

    border-color: #087cf2;

    background: #ffffff;

    box-shadow:
        0 0 0 4px rgba(8,124,242,.08);
}


/* =========================================================
   SEND BUTTON
========================================================= */

.contact-submit {

    width: 100%;

    height: 52px;

    display: flex;

    align-items: center;
    justify-content: center;

    gap: 10px;

    border: none;

    border-radius: 10px;

    background: #087cf2;

    color: #ffffff;

    font-size: 13px;

    font-weight: 800;

    cursor: pointer;

    box-shadow:
        0 10px 24px rgba(8,124,242,.18);

    transition:
        transform .3s ease,
        background .3s ease,
        box-shadow .3s ease,
        gap .3s ease;
}


.contact-submit span {
    font-size: 18px;

    transition:
        transform .3s ease;
}


.contact-submit:hover {

    transform: translateY(-3px);

    background: #066fd8;

    gap: 14px;

    box-shadow:
        0 14px 30px rgba(8,124,242,.25);
}


.contact-submit:hover span {
    transform: translateX(3px);
}


.contact-submit:active {
    transform: translateY(-1px);
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 900px) {

    .contact-section {
        padding: 80px 5% !important;
    }

    .contact-container {
        grid-template-columns: 1fr;

        gap: 45px;
    }

    .contact-info {
        max-width: 700px;
    }

    .contact-form-box {
        max-width: 700px;

        width: 100%;

        box-sizing: border-box;

        margin: 0 auto;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .contact-section {
        padding: 65px 20px !important;
    }

    .contact-info h2 {
        font-size: 34px !important;
    }

    .contact-info > p {
        font-size: 14px;
    }

    .contact-details {
        margin-top: 25px;
    }

    .contact-detail {
        width: 100%;

        box-sizing: border-box;
    }

    .contact-form-box {
        padding: 24px !important;

        border-radius: 18px;
    }

    .contact-form-box h3 {
        font-size: 21px;
    }

    .form-row {
        grid-template-columns: 1fr;

        gap: 0;
    }

    .social-icon {
        width: 42px;
        height: 42px;
    }

}

/* =========================================================
   AMPTRONIX — CONTACT SECTION FINAL UI/UX
========================================================= */


/* =========================================================
   MAIN SECTION
========================================================= */

.contact-section {
    position: relative;

    padding: 90px 7% 85px !important;

    background:
        radial-gradient(
            circle at 8% 20%,
            rgba(8,124,242,.055),
            transparent 30%
        ),
        radial-gradient(
            circle at 92% 80%,
            rgba(8,124,242,.045),
            transparent 30%
        ),
        #f8fbfe;

    overflow: hidden;
}


/* Background grid */

.contact-section::before {
    content: "";

    position: absolute;
    inset: 0;

    background-image:
        linear-gradient(
            rgba(8,124,242,.022) 1px,
            transparent 1px
        ),
        linear-gradient(
            90deg,
            rgba(8,124,242,.022) 1px,
            transparent 1px
        );

    background-size: 45px 45px;

    pointer-events: none;
}


/* =========================================================
   MAIN LAYOUT
========================================================= */

.contact-container {
    position: relative;
    z-index: 2;

    max-width: 1320px;

    margin: 0 auto;

    display: grid;

    grid-template-columns:
        minmax(0, .95fr)
        minmax(0, 1.05fr);

    gap: 75px;

    align-items: center;
}


/* =========================================================
   LEFT CONTENT
========================================================= */

.contact-info {
    width: 100%;
    max-width: 560px;
}


.contact-info .section-label {
    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 7px 14px;

    margin-bottom: 16px;

    border: 1px solid rgba(8,124,242,.16);

    border-radius: 30px;

    background: #ffffff;

    color: #087cf2;

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 1.8px;

    box-shadow:
        0 5px 15px rgba(8,124,242,.04);
}


.contact-info .section-label::before {
    content: "";

    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #087cf2;

    box-shadow:
        0 0 0 5px rgba(8,124,242,.08);
}


/* Heading */

.contact-info h2 {
    margin: 0;

    color: #10233d;

    font-size: clamp(38px,4vw,56px);

    line-height: 1.05;

    letter-spacing: -2px;
}


.contact-info h2 span {
    color: #087cf2;
}


/* Description */

.contact-info > p {
    max-width: 540px;

    margin: 20px 0 0;

    color: #687b90;

    font-size: 15px;

    line-height: 1.85;
}


/* =========================================================
   CONTACT DETAILS — 2 COLUMN
========================================================= */

.contact-details {

    display: grid !important;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 12px;

    margin-top: 30px !important;
}


/* Individual cards */

.contact-detail {

    position: relative;

    width: 100% !important;

    min-height: 78px;

    box-sizing: border-box;

    display: flex;

    align-items: center;

    gap: 13px;

    padding: 13px 15px !important;

    border: 1px solid #dfe9f2;

    border-radius: 14px;

    background: rgba(255,255,255,.92);

    box-shadow:
        0 6px 18px rgba(16,35,61,.035);

    overflow: hidden;

    transition:
        border-color .3s ease,
        box-shadow .3s ease,
        transform .3s ease;
}


/* Email takes full width */

.contact-detail:nth-child(3) {
    grid-column: 1 / -1;
}


/* Left blue indicator */

.contact-detail::before {

    content: "";

    width: 3px;
    height: 30px;

    flex: 0 0 3px;

    border-radius: 5px;

    background: #087cf2;

    opacity: .85;
}


/* Hover */

.contact-detail:hover {

    transform: translateY(-3px);

    border-color: #c9e0f3;

    box-shadow:
        0 12px 25px rgba(8,124,242,.07);
}


/* =========================================================
   CONTACT DETAIL TEXT
========================================================= */

.contact-detail h4,
.contact-detail strong,
.contact-detail .label {

    margin: 0;

    color: #6f8193;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1.1px;

    text-transform: uppercase;
}


.contact-detail p,
.contact-detail a {

    margin: 3px 0 0;

    color: #087cf2;

    font-size: 13px;

    font-weight: 700;

    text-decoration: none;

    word-break: break-word;
}


/* =========================================================
   SOCIAL SECTION
========================================================= */

.social-section {

    margin-top: 25px !important;
}


.social-section h3 {

    margin: 0 0 11px;

    color: #10233d;

    font-size: 12px;

    font-weight: 800;

    letter-spacing: .7px;
}


.social-icons {

    display: flex;

    align-items: center;

    gap: 8px;
}


.social-icon {

    width: 42px;
    height: 42px;

    display: flex;

    align-items: center;
    justify-content: center;

    border: 1px solid #dce7f0;

    border-radius: 11px;

    background: #ffffff;

    color: #087cf2;

    text-decoration: none;

    font-size: 15px;

    box-shadow:
        0 5px 15px rgba(16,35,61,.04);

    transition:
        transform .25s ease,
        background .25s ease,
        color .25s ease,
        border-color .25s ease,
        box-shadow .25s ease;
}


.social-icon:hover {

    transform: translateY(-3px);

    background: #087cf2;

    color: #ffffff;

    border-color: #087cf2;

    box-shadow:
        0 9px 20px rgba(8,124,242,.17);
}


/* =========================================================
   FORM CARD
========================================================= */

.contact-form-box {

    position: relative;

    width: 100%;

    box-sizing: border-box;

    padding: 38px 42px 40px !important;

    border: 1px solid #d9e5ef;

    border-radius: 24px;

    background: rgba(255,255,255,.97);

    box-shadow:
        0 22px 55px rgba(16,35,61,.085);

    overflow: hidden;
}


/* Top accent */

.contact-form-box::before {

    content: "";

    position: absolute;

    top: 0;
    left: 0;

    width: 100%;
    height: 3px;

    background:
        linear-gradient(
            90deg,
            #087cf2,
            #5bbcff,
            #087cf2
        );
}


/* =========================================================
   FORM HEADING
========================================================= */

/*
   Adds a heading without changing your HTML/PHP.
*/

.contact-form-box form::before {

    content: "Send Us an Enquiry";

    display: block;

    margin-bottom: 5px;

    color: #10233d;

    font-family: Arial, sans-serif;

    font-size: 23px;

    font-weight: 800;

    letter-spacing: -.5px;
}


.contact-form-box form::after {

    content: "Tell us about your requirement and our team will get back to you.";

    display: block;

    margin-bottom: 25px;

    color: #7a8b9b;

    font-size: 13px;

    line-height: 1.6;
}


/* =========================================================
   FORM ROW
========================================================= */

.form-row {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 16px;
}


/* =========================================================
   FORM GROUP
========================================================= */

.form-group {

    margin-bottom: 17px;
}


.form-group label {

    display: block;

    margin-bottom: 7px;

    color: #30465c;

    font-size: 11px;

    font-weight: 800;

    letter-spacing: .3px;
}


/* Inputs */

.form-group input,
.form-group textarea {

    width: 100%;

    box-sizing: border-box;

    border: 1px solid #dbe6ef;

    border-radius: 10px;

    background: #f9fbfd;

    color: #10233d;

    font-family: inherit;

    font-size: 13px;

    outline: none;

    transition:
        border-color .25s ease,
        background .25s ease,
        box-shadow .25s ease;
}


.form-group input {

    height: 48px;

    padding: 0 14px;
}


.form-group textarea {

    min-height: 125px;

    padding: 13px 14px;

    resize: vertical;
}


.form-group input::placeholder,
.form-group textarea::placeholder {

    color: #9aabba;
}


/* Focus */

.form-group input:focus,
.form-group textarea:focus {

    border-color: #087cf2;

    background: #ffffff;

    box-shadow:
        0 0 0 4px rgba(8,124,242,.07);
}


/* =========================================================
   SEND BUTTON
========================================================= */

.contact-submit {

    width: 100%;

    height: 52px;

    display: flex;

    align-items: center;
    justify-content: center;

    gap: 10px;

    margin-top: 2px;

    border: none;

    border-radius: 10px;

    background: #087cf2;

    color: #ffffff;

    font-size: 13px;

    font-weight: 800;

    cursor: pointer;

    box-shadow:
        0 10px 25px rgba(8,124,242,.18);

    transition:
        transform .3s ease,
        background .3s ease,
        box-shadow .3s ease,
        gap .3s ease;
}


.contact-submit span {

    font-size: 18px;

    transition:
        transform .3s ease;
}


.contact-submit:hover {

    transform: translateY(-3px);

    background: #066fd8;

    gap: 14px;

    box-shadow:
        0 15px 30px rgba(8,124,242,.24);
}


.contact-submit:hover span {

    transform: translateX(4px);
}


.contact-submit:active {

    transform: translateY(-1px);
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 950px) {

    .contact-container {

        grid-template-columns: 1fr;

        gap: 45px;
    }

    .contact-info {

        max-width: 750px;
    }

    .contact-form-box {

        max-width: 750px;

        margin: 0 auto;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .contact-section {

        padding:
            65px 20px 60px !important;
    }


    .contact-container {

        gap: 35px;
    }


    .contact-info h2 {

        font-size: 34px !important;

        line-height: 1.12;

        letter-spacing: -1px;
    }


    .contact-info > p {

        font-size: 14px;

        line-height: 1.75;
    }


    .contact-details {

        grid-template-columns: 1fr;

        gap: 10px;

        margin-top: 25px !important;
    }


    .contact-detail:nth-child(3) {

        grid-column: auto;
    }


    .contact-detail {

        min-height: 70px;
    }


    .social-section {

        margin-top: 24px !important;
    }


    .contact-form-box {

        padding:
            28px 22px 30px !important;

        border-radius: 19px;
    }


    .contact-form-box form::before {

        font-size: 21px;
    }


    .form-row {

        grid-template-columns: 1fr;

        gap: 0;
    }


    .form-group input {

        height: 46px;
    }


    .form-group textarea {

        min-height: 120px;
    }

}

/* =========================================================
   CONTACT DETAILS — PROFESSIONAL AMPTRONIX STYLE
========================================================= */

.contact-details {
    display: flex !important;
    flex-direction: column !important;
    gap: 0 !important;

    margin-top: 28px !important;

    width: 100%;
}


/* Individual contact item */

.contact-detail {
    position: relative;

    display: flex !important;
    align-items: center !important;

    width: 100% !important;
    min-height: auto !important;

    padding: 16px 4px !important;

    box-sizing: border-box;

    border: none !important;
    border-bottom: 1px solid #e5edf4 !important;

    border-radius: 0 !important;

    background: transparent !important;

    box-shadow: none !important;

    gap: 15px !important;

    transition:
        padding-left .25s ease,
        background .25s ease;
}


/* Remove the previous blue vertical line */

.contact-detail::before {
    display: none !important;
}


/* Remove the old full-width email rule */

.contact-detail:nth-child(3) {
    grid-column: auto !important;
}


/* =========================================================
   PROFESSIONAL ICON CIRCLE
========================================================= */

.contact-detail::after {

    content: "•";

    position: absolute;

    left: 0;
    top: 50%;

    transform: translateY(-50%);

    width: 34px;
    height: 34px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #edf6ff;

    color: #087cf2;

    font-size: 17px;

    font-weight: 700;
}


/* =========================================================
   CONTACT TEXT
========================================================= */

.contact-detail h4,
.contact-detail strong,
.contact-detail .label {

    display: block;

    margin: 0 0 3px 48px !important;

    color: #718396 !important;

    font-family: Arial, sans-serif !important;

    font-size: 10px !important;

    font-weight: 700 !important;

    letter-spacing: 1.2px;

    text-transform: uppercase;
}


.contact-detail p,
.contact-detail a {

    display: block;

    margin: 0 0 0 48px !important;

    color: #18324b !important;

    font-family: Arial, sans-serif !important;

    font-size: 14px !important;

    font-weight: 600 !important;

    text-decoration: none;

    word-break: break-word;
}


/* =========================================================
   HOVER
========================================================= */

.contact-detail:hover {

    padding-left: 7px !important;

    background: linear-gradient(
        90deg,
        rgba(8,124,242,.035),
        transparent
    ) !important;
}


.contact-detail:hover::after {

    background: #087cf2;

    color: #ffffff;
}


/* =========================================================
   DIFFERENT ICON SYMBOLS
========================================================= */

/* Phone */

.contact-detail:nth-child(1)::after {
    content: "☎";
}


/* Mobile */

.contact-detail:nth-child(2)::after {
    content: "✆";
}


/* Email */

.contact-detail:nth-child(3)::after {
    content: "✉";
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .contact-details {
        margin-top: 22px !important;
    }

    .contact-detail {
        padding: 14px 3px !important;
    }

    .contact-detail h4,
    .contact-detail strong,
    .contact-detail .label {
        margin-left: 45px !important;
    }

    .contact-detail p,
    .contact-detail a {
        margin-left: 45px !important;

        font-size: 13px !important;
    }

}

/* =========================================================
   AMPTRONIX — PROFESSIONAL SOCIAL MEDIA
========================================================= */

.social-section {
    margin-top: 28px !important;
    padding-top: 20px;

    border-top: 1px solid #e3ebf2;

    width: 100%;
}

.social-section h3 {
    margin: 0 0 14px;

    color: #687b8e;

    font-family: Arial, sans-serif;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1.6px;

    text-transform: uppercase;
}


/* Social row */

.social-icons {
    display: flex;

    align-items: center;

    gap: 8px;
}


/* Individual icon */

.social-icon {
    position: relative;

    width: 38px;
    height: 38px;

    display: flex;

    align-items: center;
    justify-content: center;

    padding: 0;

    border: 1px solid #dce6ee;

    border-radius: 50%;

    background: #ffffff;

    color: #50677c;

    font-size: 13px;

    text-decoration: none;

    box-shadow: none;

    overflow: hidden;

    transition:
        color .25s ease,
        border-color .25s ease,
        background .25s ease,
        transform .25s ease;
}


/* Blue accent circle */

.social-icon::before {
    content: "";

    position: absolute;

    width: 100%;
    height: 100%;

    left: 0;
    top: 0;

    border-radius: 50%;

    background: #edf6ff;

    transform: scale(0);

    transition:
        transform .3s ease;

    z-index: 0;
}


/* Keep icon visible */

.social-icon {
    isolation: isolate;
}

.social-icon i,
.social-icon svg,
.social-icon span {
    position: relative;

    z-index: 2;
}


/* Hover */

.social-icon:hover {
    color: #087cf2;

    border-color: #bcdaf2;

    background: #ffffff;

    transform: translateY(-3px);

    box-shadow:
        0 7px 16px rgba(8,124,242,.09);
}

.social-icon:hover::before {
    transform: scale(1);
}


/* Active / click */

.social-icon:active {
    transform: translateY(-1px) scale(.96);
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .social-section {
        margin-top: 22px !important;
        padding-top: 17px;
    }

    .social-icons {
        gap: 7px;
    }

    .social-icon {
        width: 36px;
        height: 36px;
    }

}

/* =========================================================
   AMPTRONIX — PROFESSIONAL ENQUIRY FORM
========================================================= */

.contact-form-box {
    position: relative;

    padding: 38px 40px !important;

    border: 1px solid #dce6ef !important;

    border-radius: 22px !important;

    background: #ffffff !important;

    box-shadow:
        0 15px 40px rgba(16,35,61,.07) !important;

    overflow: hidden;
}


/* Clean top accent */

.contact-form-box::before {
    height: 2px !important;

    background: #087cf2 !important;
}


/* Form heading */

.contact-form-box form::before {
    margin-bottom: 7px !important;

    color: #10233d !important;

    font-family: Arial, sans-serif !important;

    font-size: 22px !important;

    font-weight: 700 !important;

    letter-spacing: -.3px;
}


/* Description */

.contact-form-box form::after {
    margin-bottom: 25px !important;

    color: #7a8a99 !important;

    font-family: Arial, sans-serif !important;

    font-size: 12px !important;

    line-height: 1.6;
}


/* =========================================================
   LABELS
========================================================= */

.form-group label {
    margin-bottom: 7px !important;

    color: #52687b !important;

    font-family: Arial, sans-serif !important;

    font-size: 11px !important;

    font-weight: 700 !important;

    letter-spacing: .3px;
}


/* =========================================================
   INPUTS
========================================================= */

.form-group input,
.form-group textarea {
    border: 1px solid #dce6ee !important;

    border-radius: 9px !important;

    background: #fbfcfd !important;

    color: #18324b !important;

    font-family: Arial, sans-serif !important;

    font-size: 13px !important;

    box-shadow: none !important;

    transition:
        border-color .25s ease,
        background .25s ease,
        box-shadow .25s ease;
}


/* Input */

.form-group input {
    height: 47px !important;

    padding: 0 13px !important;
}


/* Message */

.form-group textarea {
    min-height: 120px !important;

    padding: 13px !important;

    line-height: 1.6;
}


/* Placeholder */

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #9aaab8 !important;
}


/* Focus */

.form-group input:focus,
.form-group textarea:focus {
    border-color: #087cf2 !important;

    background: #ffffff !important;

    box-shadow:
        0 0 0 3px rgba(8,124,242,.07) !important;
}


/* =========================================================
   SEND BUTTON
========================================================= */

.contact-submit {
    height: 50px !important;

    margin-top: 3px;

    border-radius: 9px !important;

    background: #087cf2 !important;

    color: #ffffff !important;

    font-family: Arial, sans-serif !important;

    font-size: 13px !important;

    font-weight: 700 !important;

    box-shadow:
        0 8px 20px rgba(8,124,242,.16) !important;

    transition:
        background .25s ease,
        transform .25s ease,
        box-shadow .25s ease;
}


.contact-submit:hover {
    background: #066fd8 !important;

    transform: translateY(-2px);

    box-shadow:
        0 11px 24px rgba(8,124,242,.21) !important;
}


.contact-submit:active {
    transform: translateY(0);
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .contact-form-box {
        padding: 28px 22px !important;

        border-radius: 18px !important;
    }

    .contact-form-box form::before {
        font-size: 20px !important;
    }

    .form-group input {
        height: 45px !important;
    }

    .form-group textarea {
        min-height: 115px !important;
    }

}

/* =========================================================
   AMPTRONIX — PREMIUM CORPORATE FOOTER
========================================================= */

.site-footer {
    position: relative;

    background:
        linear-gradient(
            135deg,
            #0b1b2e 0%,
            #10233d 55%,
            #0c2138 100%
        ) !important;

    color: #ffffff;

    padding: 70px 7% 0 !important;

    overflow: hidden;
}


/* Subtle technical glow */

.site-footer::before {
    content: "";

    position: absolute;

    width: 420px;
    height: 420px;

    top: -250px;
    right: -120px;

    border-radius: 50%;

    background: rgba(8,124,242,.08);

    pointer-events: none;
}


/* Small blue accent line */

.site-footer::after {
    content: "";

    position: absolute;

    top: 0;
    left: 7%;

    width: 90px;
    height: 2px;

    background: #087cf2;

    border-radius: 10px;
}


/* =========================================================
   FOOTER GRID
========================================================= */

.site-footer .footer-container {
    position: relative;
    z-index: 2;

    max-width: 1250px;

    margin: 0 auto;

    display: grid;

    grid-template-columns:
        1.45fr
        .8fr
        .9fr
        1.15fr;

    gap: 65px;

    padding-bottom: 55px;
}


/* =========================================================
   COMPANY
========================================================= */

.footer-company {
    max-width: 340px;
}

.footer-company .footer-logo {
    width: 165px !important;

    height: auto;

    margin-bottom: 20px;

    padding: 7px 10px;

    background: #ffffff;

    border-radius: 7px;

    transition:
        transform .3s ease,
        box-shadow .3s ease;
}

.footer-company .footer-logo:hover {
    transform: translateY(-2px);

    box-shadow:
        0 8px 20px rgba(0,0,0,.18);
}


.footer-company p {
    margin: 0;

    max-width: 310px;

    color: #aebfd0;

    font-family: Arial, sans-serif;

    font-size: 13px;

    line-height: 1.8;
}


/* =========================================================
   COLUMN HEADINGS
========================================================= */

.site-footer .footer-links h3,
.site-footer .footer-contact h3 {

    position: relative;

    margin: 0 0 22px;

    color: #ffffff;

    font-family: Arial, sans-serif;

    font-size: 12px;

    font-weight: 700;

    letter-spacing: 1.5px;

    text-transform: uppercase;
}


/* Tiny heading accent */

.site-footer .footer-links h3::after,
.site-footer .footer-contact h3::after {

    content: "";

    display: block;

    width: 28px;
    height: 2px;

    margin-top: 9px;

    background: #087cf2;

    border-radius: 10px;
}


/* =========================================================
   LINKS
========================================================= */

.site-footer .footer-links {
    display: flex;

    flex-direction: column;

    align-items: flex-start;

    gap: 0;
}


.site-footer .footer-links a {

    position: relative;

    width: fit-content;

    margin: 0 0 13px;

    padding-left: 0;

    color: #9eb1c4;

    font-family: Arial, sans-serif;

    font-size: 13px;

    line-height: 1.5;

    text-decoration: none;

    transition:
        color .25s ease,
        padding-left .25s ease;
}


.site-footer .footer-links a::before {

    content: "";

    position: absolute;

    left: 0;

    top: 50%;

    width: 0;

    height: 1px;

    background: #087cf2;

    transform: translateY(-50%);

    transition: width .25s ease;
}


.site-footer .footer-links a:hover {

    color: #ffffff;

    padding-left: 13px;
}


.site-footer .footer-links a:hover::before {

    width: 7px;
}


/* =========================================================
   CONTACT
========================================================= */

.site-footer .footer-contact {
    display: flex;

    flex-direction: column;

    align-items: flex-start;
}


.site-footer .footer-contact p {

    margin: 0 0 18px;

    max-width: 250px;

    color: #9eb1c4;

    font-family: Arial, sans-serif;

    font-size: 13px;

    line-height: 1.75;
}


.site-footer .footer-contact a {

    width: fit-content;

    margin-bottom: 11px;

    color: #b7c7d7;

    font-family: Arial, sans-serif;

    font-size: 13px;

    text-decoration: none;

    transition:
        color .25s ease,
        transform .25s ease;
}


.site-footer .footer-contact a:hover {

    color: #66b2ff;

    transform: translateX(3px);
}


/* =========================================================
   FOOTER BOTTOM
========================================================= */

.site-footer .footer-bottom {

    position: relative;

    z-index: 2;

    max-width: 1250px;

    margin: 0 auto;

    padding: 20px 0;

    border-top:
        1px solid rgba(255,255,255,.10);

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;
}


.site-footer .footer-bottom p {

    margin: 0;

    color: #71869a;

    font-family: Arial, sans-serif;

    font-size: 11px;

    letter-spacing: .2px;
}


.site-footer .footer-bottom p:last-child {

    color: #8297aa;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1000px) {

    .site-footer .footer-container {

        grid-template-columns:
            1.4fr 1fr 1fr;

        gap: 45px;
    }

    .footer-contact {

        grid-column: 1 / -1;
    }

}


@media (max-width: 600px) {

    .site-footer {

        padding: 55px 6% 0 !important;
    }

    .site-footer::after {

        left: 6%;
    }

    .site-footer .footer-container {

        grid-template-columns: 1fr;

        gap: 35px;
    }

    .footer-company {

        max-width: 100%;
    }

    .footer-contact {

        grid-column: auto;
    }

    .site-footer .footer-bottom {

        flex-direction: column;

        align-items: flex-start;

        gap: 8px;

        padding: 18px 0;
    }

}

/* =========================================================
   AMPTRONIX — FINAL UI/UX POLISH
========================================================= */

/* Consistent section headings */

.solutions-heading,
.products-heading,
.applications-heading {
    position: relative;
}

.solutions-heading h2,
.products-heading h2,
.applications-heading h2,
.about-content h2 {
    letter-spacing: -0.5px;
}


/* Small section label */

.section-label,
.about-tag,
.small-title {
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 2px !important;
    text-transform: uppercase;
}


/* Cleaner paragraphs */

.solutions-heading > p:last-child,
.products-heading > p:last-child,
.applications-heading > p:last-child {
    max-width: 620px;

    color: #718096;

    line-height: 1.75;
}


/* =========================================================
   SECTION SEPARATORS
========================================================= */

.solutions {
    position: relative;
}

.about {
    position: relative;
}

.products {
    position: relative;
}

.applications {
    position: relative;
}


/* Very subtle transition between sections */

.solutions::after,
.products::after {
    content: "";

    position: absolute;

    left: 7%;

    bottom: 0;

    width: 86%;

    height: 1px;

    background: #e6edf4;
}


/* =========================================================
   GLOBAL FOCUS ACCESSIBILITY
========================================================= */

a:focus-visible,
button:focus-visible,
input:focus-visible,
textarea:focus-visible {

    outline: 2px solid #087cf2;

    outline-offset: 3px;

}


/* =========================================================
   IMAGE RENDERING
========================================================= */

img {
    image-rendering: auto;

    -webkit-backface-visibility: hidden;

    backface-visibility: hidden;
}


/* =========================================================
   REDUCE MOTION FOR USERS WHO PREFER IT
========================================================= */

@media (prefers-reduced-motion: reduce) {

    *,
    *::before,
    *::after {

        animation-duration: .01ms !important;

        animation-iteration-count: 1 !important;

        scroll-behavior: auto !important;

        transition-duration: .01ms !important;
    }

}




/* ========================================
   AMPTRONIX — RESPONSIVE PROFILE ICON
======================================== */

.profile-link {
    position: relative;

    width: 44px;
    height: 44px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    color: #0878f9;
    background: transparent;

    border: 1px solid transparent;
    border-radius: 14px;

    text-decoration: none;
    flex-shrink: 0;

    transition:
        color 0.25s ease,
        background 0.25s ease,
        border-color 0.25s ease,
        transform 0.25s ease;
}

/* Profile icon */
.profile-link svg {
    width: 21px;
    height: 21px;

    stroke-width: 1.8;

    transition: transform 0.25s ease;
}

/* Desktop hover */
@media (hover: hover) and (pointer: fine) {
    .profile-link:hover {
        color: #0064d9;
        background: #eef6ff;
        border-color: #dcecff;

        transform: translateY(-2px);
    }

    .profile-link:hover svg {
        transform: scale(1.08);
    }
}

/* Click interaction */
.profile-link:active {
    transform: scale(0.96);
}

/* Keyboard accessibility */
.profile-link:focus-visible {
    outline: 3px solid rgba(8, 120, 249, 0.25);
    outline-offset: 3px;
}

/* Tooltip */
.profile-tooltip {
    position: absolute;

    top: calc(100% + 10px);
    left: 50%;

    transform: translateX(-50%) translateY(-4px);

    padding: 8px 12px;

    background: #10233f;
    color: #ffffff;

    font-family: Arial, sans-serif;
    font-size: 12px;
    font-weight: 500;

    white-space: nowrap;
    border-radius: 7px;

    opacity: 0;
    visibility: hidden;
    pointer-events: none;

    transition:
        opacity 0.2s ease,
        transform 0.2s ease;

    z-index: 1000;
}

/* Tooltip arrow */
.profile-tooltip::before {
    content: "";

    position: absolute;

    bottom: 100%;
    left: 50%;

    transform: translateX(-50%);

    border: 5px solid transparent;
    border-bottom-color: #10233f;
}

/* Desktop tooltip */
@media (hover: hover) and (pointer: fine) {
    .profile-link:hover .profile-tooltip,
    .profile-link:focus-visible .profile-tooltip {
        opacity: 1;
        visibility: visible;

        transform: translateX(-50%) translateY(0);
    }
}

/* Mobile profile icon size */
@media (max-width: 768px) {
    .profile-link {
        width: 36px;
        height: 36px;

        min-width: 36px;
        min-height: 36px;

        padding: 0;
        margin: 0 auto;

        border-radius: 10px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .profile-link svg {
        width: 17px;
        height: 17px;
    }

    .profile-tooltip {
        display: none;
    }
}



/* =========================================
   AMPTRONIX BLUE & WHITE PRODUCT SECTION
========================================= */

.amp-products {
    padding: 80px 7%;
    background: #ffffff;
    overflow: hidden;
}

.amp-products-heading {
    margin-bottom: 38px;
}

.amp-small-title {
    color: #087cf2;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1.5px;
    margin-bottom: 12px;
}

.amp-products-heading h2 {
    color: #10233d;
    font-size: clamp(30px, 4vw, 42px);
    font-weight: 700;
    margin: 0 0 15px;
}

.amp-products-heading h2 span {
    color: #087cf2;
}

.amp-products-description {
    color: #718096;
    font-size: 15px;
    line-height: 1.7;
    max-width: 700px;
    margin: 0;
}


/* PRODUCT CATEGORY */

.amp-product-category {
    margin-top: 35px;
}

.amp-category-title {
    color: #10233d;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.7px;
    margin: 0 0 22px;
}


/* PRODUCT GRID */

.amp-product-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
}


/* PRODUCT CARD */

.amp-product-card {
    background: #ffffff;
    border: 1px solid #dce7f1;
    border-radius: 12px;
    overflow: hidden;
    min-width: 0;

    display: flex;
    flex-direction: column;

    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        border-color 0.3s ease;
}

.amp-product-card:hover {
    transform: translateY(-5px);
    border-color: #087cf2;

    box-shadow:
        0 12px 30px rgba(8, 124, 242, 0.10);
}


/* LIGHT BLUE IMAGE PLACEHOLDER */

.amp-product-image {
    width: 100%;
    height: 180px;

    background:
        linear-gradient(
            145deg,
            #f4f9ff 0%,
            #e8f3ff 100%
        );

    border-bottom: 1px solid #dce7f1;
}


/* PRODUCT INFORMATION */

.amp-product-info {
    padding: 12px;

    display: flex;
    flex-direction: column;
    align-items: flex-start;

    flex: 1;
}

.amp-product-name {
    color: #10233d;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    margin: 0 0 5px;
}

.amp-product-model {
    color: #718096;
    font-size: 12px;
    margin: 0 0 12px;
}


/* KNOW MORE BUTTON */

.amp-know-more {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    margin-top: auto;

    padding: 8px 13px;

    background: #087cf2;
    color: #ffffff;

    border: 1px solid #087cf2;
    border-radius: 6px;

    text-decoration: none;
    font-size: 12px;
    font-weight: 600;

    transition: 0.3s ease;
}

.amp-know-more:hover {
    background: #ffffff;
    color: #087cf2;
}


/* CAROUSEL CONTROLS */

.amp-carousel-controls {
    display: flex;
    align-items: center;
    justify-content: center;

    gap: 18px;
    margin-top: 22px;
}

.amp-arrow {
    width: 40px;
    height: 40px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #cbd8e5;
    border-radius: 50%;

    background: #ffffff;
    color: #087cf2;

    font-size: 22px;
    cursor: pointer;

    transition: 0.3s ease;
}

.amp-arrow:hover {
    background: #087cf2;
    color: #ffffff;
    border-color: #087cf2;
}


/* DOTS */

.amp-dots {
    display: flex;
    align-items: center;
    gap: 8px;
}

.amp-dot {
    width: 11px;
    height: 11px;

    padding: 0;

    border: none;
    border-radius: 50%;

    background: #d5e2ef;
    cursor: pointer;

    transition: 0.3s ease;
}

.amp-dot.active {
    width: 23px;
    border-radius: 10px;
    background: #087cf2;
}


/* TABLET */

@media (max-width: 900px) {

    .amp-products {
        padding: 65px 5%;
    }

    .amp-product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

}


/* MOBILE */

@media (max-width: 600px) {

    .amp-products {
        padding: 45px 5%;
    }

    .amp-products-heading {
        margin-bottom: 30px;
    }

    .amp-products-heading h2 {
        font-size: 30px;
    }

    .amp-products-description {
        font-size: 14px;
    }

    .amp-product-category {
        margin-top: 30px;
    }

    .amp-product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .amp-product-image {
        height: 140px;
    }

    .amp-product-info {
        padding: 10px;
    }

    .amp-product-name {
        font-size: 12px;
    }

    .amp-product-model {
        font-size: 11px;
    }

    .amp-know-more {
        font-size: 11px;
        padding: 7px 10px;
    }

    .amp-carousel-controls {
        gap: 12px;
    }

}


/* VERY SMALL MOBILE */

@media (max-width: 360px) {

    .amp-product-grid {
        grid-template-columns: 1fr;
    }

    .amp-product-image {
        height: 180px;
    }

}





/* Dynamic product navigation: hide cards outside the active page. */
.amp-product-card[hidden],
.amp-carousel-controls[hidden] {
    display: none !important;
}

.amp-product-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}

.amp-arrow:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

a.hero-highlight {
    text-decoration: none;
    color: inherit;
}

/* FIX: RESTORE DESKTOP HERO TWO-COLUMN LAYOUT */

@media (min-width: 992px) {

    .hero {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
        align-items: center !important;
        gap: 60px !important;
    }

    .hero-content {
        width: 100%;
        max-width: 100%;
        text-align: left;
    }

    .hero-product {
        width: 100%;
        max-width: 100%;
        margin: 0;
    }

}

/* TABLET AND MOBILE */

@media (max-width: 991px) {

    .hero {
        grid-template-columns: 1fr !important;
    }

    .hero-content,
    .hero-product {
        width: 100%;
        max-width: 100%;
    }

}



/* =====================================================
   SOLUTIONS — RESTORED CARD DESIGN
   ===================================================== */
.solutions-container {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 24px !important;
    align-items: stretch;
    max-width: 1450px;
    margin: 34px auto 0;
}

.solutions-container .solution-card {
    position: relative;
    width: 100%;
    min-width: 0;
    min-height: 300px !important;
    height: 100%;
    margin: 0 !important;
    padding: 26px !important;
    display: flex !important;
    flex-direction: column;
    align-items: stretch !important;
    justify-content: flex-start !important;
    text-align: center !important;
    background: #ffffff !important;
    border: 1px solid #dfe8f0 !important;
    border-radius: 18px !important;
    box-shadow: 0 8px 24px rgba(16, 35, 61, .045) !important;
    overflow: hidden;
    transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
}

.solutions-container .solution-card::before,
.solutions-container .solution-card::after {
    content: none !important;
    display: none !important;
}

.solutions-container .solution-card:hover {
    transform: translateY(-6px) !important;
    border-color: #c7dff5 !important;
    box-shadow: 0 16px 34px rgba(16, 35, 61, .10) !important;
}

.solutions-container .solution-card .solution-image {
    display: block;
    width: 100%;
    height: 230px;
    max-width: 100%;
    margin: 0 auto 20px;
    object-fit: contain;
    object-position: center;
    border-radius: 10px;
    flex-shrink: 0;
}

.solutions-container .solution-card h3 {
    width: 100%;
    margin: auto 0 0 !important;
    color: #10233d !important;
    text-align: center !important;
    font-size: 24px !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
    white-space: normal !important;
    overflow-wrap: anywhere !important;
}

.solutions-container .solution-card h3 a {
    color: inherit !important;
    text-decoration: none;
    cursor: pointer;
}

.solutions-container .solution-card h3 a:hover {
    color: #087cf2 !important;
    text-decoration: underline;
    text-underline-offset: 5px;
}

@media (max-width: 900px) {
    .solutions-container {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 20px !important;
    }
}

@media (max-width: 600px) {
    .solutions-container {
        grid-template-columns: 1fr !important;
        gap: 18px !important;
    }
    .solutions-container .solution-card {
        min-height: 260px !important;
        padding: 22px !important;
    }
}


/* =========================
   APPLICATIONS GALLERY REFRESH
   Balanced gallery; one image stays centered.
========================= */
.applications-slider {
    width: min(100%, 1180px);
    margin: 42px auto 0;
    padding: 0 18px;
    box-sizing: border-box;
}

.application-slide {
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 360px));
    justify-content: center;
    align-items: stretch;
    gap: 28px;
}

.application-image-card {
    position: relative;
    width: 100%;
    max-width: 360px;
    aspect-ratio: 4 / 5;
    margin: 0 auto;
    padding: 12px;
    background: linear-gradient(145deg, #ffffff, #edf5fc);
    border: 1px solid #d8e6f4;
    border-radius: 22px;
    box-shadow: 0 14px 38px rgba(16, 35, 61, 0.09);
    overflow: hidden;
    cursor: zoom-in;
}

.application-image-card::after {
    content: "VIEW FULL IMAGE  ↗";
    position: absolute;
    left: 12px;
    right: 12px;
    bottom: 12px;
    padding: 13px 16px;
    border-radius: 0 0 13px 13px;
    color: #ffffff;
    background: linear-gradient(transparent, rgba(10, 28, 52, 0.82));
    font: 700 11px/1.2 Arial, sans-serif;
    letter-spacing: 1.5px;
    text-align: center;
    opacity: 0;
    transform: translateY(8px);
    transition: opacity .25s ease, transform .25s ease;
    pointer-events: none;
}

.application-image-card img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 13px;
    background: #ffffff;
    transition: transform .35s ease;
}

.application-image-card:hover {
    transform: translateY(-5px);
    border-color: #9bc9f5;
    box-shadow: 0 22px 48px rgba(16, 35, 61, 0.15);
}

.application-image-card:hover::after,
.application-image-card:focus-within::after {
    opacity: 1;
    transform: translateY(0);
}

.application-preview {
    background: rgba(5, 15, 30, .96);
    backdrop-filter: blur(8px);
}

.application-preview-image {
    max-width: 94vw;
    max-height: 90vh;
}

@media (max-width: 600px) {
    .applications-slider { padding: 0 14px; margin-top: 28px; }
    .application-slide { grid-template-columns: minmax(0, 1fr); gap: 18px; }
    .application-image-card { max-width: 340px; aspect-ratio: 4 / 5; }
}


/* ===== AMPTRONIX COMPANY TIMELINE (compact, scoped styles only) ===== */
.amp-history-demo { padding:48px 5%; background:#f7faff; }
.amp-history-demo .amp-history-inner { max-width:1040px; margin:0 auto; }
.amp-history-demo .amp-history-heading { text-align:center; margin-bottom:30px; }
.amp-history-demo .amp-history-kicker { display:inline-block; color:#087cf2; font-size:11px; font-weight:800; letter-spacing:2px; margin-bottom:8px; }
.amp-history-demo .amp-history-heading h2 { color:#10233d; font-size:32px; margin:0 0 10px; }
.amp-history-demo .amp-history-heading p { color:#718096; max-width:650px; margin:0 auto; line-height:1.55; }
.amp-history-demo .amp-history-track { position:relative; display:flex; flex-direction:column; gap:18px; }
.amp-history-demo .amp-history-track:before { content:''; position:absolute; top:8px; bottom:8px; left:50%; width:3px; background:#c9def5; transform:translateX(-50%); border-radius:5px; }
.amp-history-demo .amp-history-item.is-visible .amp-history-node { background:#42b5ff; box-shadow:0 0 0 3px #087cf2,0 0 18px rgba(8,124,242,.4); }
.amp-history-demo .amp-history-progress { position:absolute; top:8px; left:50%; width:3px; height:0; background:linear-gradient(180deg,#42b5ff,#087cf2); transform:translateX(-50%); border-radius:5px; z-index:1; box-shadow:0 0 12px rgba(8,124,242,.35); transition:height .12s linear; }
.amp-history-demo .amp-history-item.timeline-animate .amp-history-card { opacity:0; transform:translateY(28px); transition:opacity .65s ease,transform .65s cubic-bezier(.2,.8,.2,1); }
.amp-history-demo .amp-history-item.timeline-animate.is-visible .amp-history-card { opacity:1; transform:translateY(0); }
.amp-history-demo .amp-history-item.timeline-animate .amp-history-node { transition:box-shadow .35s ease,background .35s ease; }
.amp-history-demo .amp-history-item.is-visible .amp-history-node { background:#42b5ff; box-shadow:0 0 0 3px #087cf2,0 0 18px rgba(8,124,242,.4); }
.amp-history-demo .amp-history-item.is-visible .amp-history-year { text-shadow:0 0 18px rgba(8,124,242,.15); }
.amp-history-demo .amp-history-item { position:relative; width:100%; display:flex; box-sizing:border-box; }
.amp-history-demo .amp-history-item:nth-child(odd) { justify-content:flex-start; padding-right:calc(50% + 34px); }
.amp-history-demo .amp-history-item:nth-child(even) { justify-content:flex-end; padding-left:calc(50% + 34px); }
.amp-history-demo .amp-history-node { position:absolute; left:50%; top:22px; width:13px; height:13px; border:4px solid #fff; background:#087cf2; border-radius:50%; box-shadow:0 0 0 2px #087cf2; transform:translateX(-50%); z-index:1; }
.amp-history-demo .amp-history-card { width:100%; box-sizing:border-box; background:#fff; border:1px solid #e2eaf4; border-radius:14px; padding:17px 19px; box-shadow:0 10px 26px rgba(16,35,61,.045); transition:transform .25s ease,box-shadow .25s ease; }
.amp-history-demo .amp-history-image-wrap { width:100%; height:115px; margin-bottom:10px; overflow:hidden; border-radius:12px; background:linear-gradient(135deg,#eef5fc,#dceafa); }
.amp-history-demo .amp-history-image { width:100%; height:100%; display:block; object-fit:cover; }
.amp-history-demo .amp-history-image-placeholder { width:100%; height:100%; align-items:center; justify-content:center; text-align:center; color:#6f8fb3; font-size:12px; font-weight:800; letter-spacing:1.5px; background:linear-gradient(135deg,#eef5fc,#dceafa); }
@media(max-width:700px){.amp-history-demo .amp-history-image-wrap{height:130px;}}
.amp-history-demo .amp-history-card:hover { transform:translateY(-4px); box-shadow:0 16px 32px rgba(16,35,61,.08); }
.amp-history-demo .amp-history-year { color:#087cf2; font-size:22px; font-weight:800; margin-bottom:5px; }
.amp-history-demo .amp-history-card h3 { color:#10233d; font-size:16px; margin:0 0 5px; }
.amp-history-demo .amp-history-card p { color:#718096; font-size:13px; line-height:1.5; margin:0; }
.amp-history-demo .amp-history-demo-label { display:inline-block; margin-top:12px; padding:5px 9px; border-radius:20px; background:#f0f7ff; color:#5480aa; font-size:10px; font-weight:700; letter-spacing:.5px; }
@media(max-width:700px){ .amp-history-demo {padding:36px 5%;} .amp-history-demo .amp-history-heading {margin-bottom:22px;} .amp-history-demo .amp-history-heading h2 {font-size:27px;} .amp-history-demo .amp-history-track:before {left:12px;} .amp-history-demo .amp-history-item:nth-child(odd), .amp-history-demo .amp-history-item:nth-child(even) {padding:0 0 0 36px; justify-content:flex-start;} .amp-history-demo .amp-history-node {left:12px; top:20px;} .amp-history-demo .amp-history-card {padding:14px;} .amp-history-demo .amp-history-image-wrap{height:105px;} }

/* Timeline card proportions: compact card body, preserve existing image dimensions */
.amp-history-demo .amp-history-inner { max-width:900px; }
.amp-history-demo .amp-history-card { padding:14px 16px; border-radius:13px; }
.amp-history-demo .amp-history-image-wrap { height:115px; margin-bottom:8px; }
.amp-history-demo .amp-history-year { margin-bottom:3px; }
.amp-history-demo .amp-history-card h3 { margin-bottom:4px; }
.amp-history-demo .amp-history-card p { line-height:1.42; }
@media(max-width:700px){
  .amp-history-demo .amp-history-card { padding:13px; }
  .amp-history-demo .amp-history-image-wrap { height:105px; }
}



/* ===== AMPTRONIX PUBLIC UI/UX REFINEMENT (visual-only) ===== */

/* AMPTRONIX PUBLIC UI polish — visual-only overrides */
:where(a,button,input,select,textarea,summary,[tabindex]):focus-visible {
  outline: 3px solid rgba(8,124,242,.32);
  outline-offset: 3px;
}
:where(button,a) { -webkit-tap-highlight-color: transparent; }
::selection { background: rgba(8,124,242,.18); color: #102b4c; }

/* Consistent visual language across the public homepage */
header {
  box-shadow: 0 5px 22px rgba(16,35,61,.045);
}
nav a, header a { transition: color .2s ease, background-color .2s ease, transform .2s ease; }
.hero-highlight {
  background: linear-gradient(145deg,#fff 0%,#f5faff 100%);
  border-color: #e1ebf5;
  box-shadow: 0 8px 22px rgba(16,35,61,.045);
}
.hero-highlight:focus-visible { outline-offset: 4px; }
.why-container, .why-card, .solution-card, .product-card,
.application-card, .contact-card {
  border-color: #e1eaf4;
}
.why-card, .solution-card, .product-card, .application-card {
  transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease;
}
.why-card:hover, .solution-card:hover, .product-card:hover, .application-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 14px 34px rgba(16,47,85,.09);
  border-color: #c9def3;
}
button, .quote, .btn, .button, .submit-btn {
  transition: background-color .2s ease, border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}
.quote:hover, .btn:hover, .button:hover, .submit-btn:hover {
  box-shadow: 0 8px 18px rgba(8,124,242,.18);
  transform: translateY(-1px);
}
input, select, textarea {
  border-color: #d7e2ee;
  border-radius: 9px;
}
input:focus, select:focus, textarea:focus {
  border-color: #68aef5;
  box-shadow: 0 0 0 4px rgba(8,124,242,.09);
  outline: none;
}
img { max-width: 100%; }
@media (prefers-reduced-motion: reduce) {
  *,*::before,*::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; transition-duration: .01ms !important; }
}

</style>

</head>


<body>

    
<?php require_once __DIR__ . '/header.php'; ?>

<section class="hero scroll-reveal" id="home">

    <!-- LEFT SIDE -->
    <div class="hero-content">

        <span class="hero-welcome">
            WELCOME TO AMPTRONIX
        </span>

        <p class="hero-tagline">
            Connecting technology.<br>
            Powering a better tomorrow.
        </p>


        
<div class="hero-highlights">

    <a href="optical-system.php" class="hero-highlight">
        <div class="highlight-icon">◉</div>
        <div>
            <h3>OPTICAL SYSTEM</h3>
            <p>Optical system solutions</p>
        </div>
    </a>

    <a href="rf-system.php" class="hero-highlight">
        <div class="highlight-icon">⌁</div>
        <div>
            <h3>R.F SYSTEM</h3>
            <p>RF system solutions</p>
        </div>
    </a>

    <a href="power.php" class="hero-highlight">
        <div class="highlight-icon">✳</div>
        <div>
            <h3>POWER</h3>
            <p>Power solutions</p>
        </div>
    </a>

    <a href="hdp.php" class="hero-highlight">
        <div class="highlight-icon">✦</div>
        <div>
            <h3>HDP</h3>
            <p>HDP solutions</p>
        </div>
    </a>

</div>


    </div>


    <!-- RIGHT SIDE -->
    <div class="hero-product">

    <div class="product-glow"></div>

    <div class="hero-product-image">
        <img src="images/DSCN1148.JPG">
    </div>

</div>

</section>

<!-- WHY AMPTRONIX / CAPABILITIES SECTION -->

<section class="why scroll-reveal" id="capabilities">

    <div class="why-header">

        <p class="why-eyebrow">AMPTRONIX CAPABILITIES</p>

        <h2>
            Technology Built for
            <span>Reliable Infrastructure</span>
        </h2>

        <p class="why-intro">
            Engineered solutions for connectivity, energy,
            lighting and modern infrastructure requirements.
        </p>

    </div>


    <div class="why-container">


        <div class="why-card">

            <div class="why-icon">⚙</div>

            <div class="why-card-content">

                <span class="why-number">01</span>

                <h3>Reliable Performance</h3>

                <p>
                    Designed for dependable operation in
                    real-world infrastructure environments.
                </p>

            </div>

        </div>


        <div class="why-card">

            <div class="why-icon">⚡</div>

            <div class="why-card-content">

                <span class="why-number">02</span>

                <h3>Energy Efficient</h3>

                <p>
                    Technology designed to support efficient
                    and sustainable infrastructure.
                </p>

            </div>

        </div>


        <div class="why-card">

            <div class="why-icon">✓</div>

            <div class="why-card-content">

                <span class="why-number">03</span>

                <h3>Trusted Technology</h3>

                <p>
                    Practical solutions developed for
                    demanding connectivity and infrastructure needs.
                </p>

            </div>

        </div>


        <div class="why-card">

            <div class="why-icon">◈</div>

            <div class="why-card-content">

                <span class="why-number">04</span>

                <h3>Future Ready</h3>

                <p>
                    Built with scalable technology for
                    evolving infrastructure requirements.
                </p>

            </div>

        </div>


    </div>

</section>



<!-- =========================
     AMPTRONIX PRODUCTS
     Uses products table (SATCOM + Broadband)
========================= -->

<section class="amp-products scroll-reveal" id="products">

    <div class="amp-products-heading">

        <p class="amp-small-title">OUR PRODUCTS</p>

        <h2>
            Explore Our <span>Products.</span>
        </h2>

        <p class="amp-products-description">
            Discover Amptronix products designed for reliable,
            efficient and high-performance applications.
        </p>

    </div>

    <?php
    $publicProductSections = [
        "SATCOM PRODUCTS" => $satcom_products,
        "BROAD BAND PRODUCTS" => $broadband_products,
        "MONITORING SYSTEM" => $monitoring_system_products,
        "BATTERY CHARGER" => $battery_charger_products
    ];
    ?>

    <?php foreach ($publicProductSections as $sectionTitle => $sectionProducts): ?>

        <?php if (!empty($sectionProducts)): ?>

            <div class="amp-product-category">

                <h3 class="amp-category-title">
                    <?= htmlspecialchars($sectionTitle, ENT_QUOTES, "UTF-8") ?>
                </h3>

                <div class="amp-carousel" data-page-size="4">

                    <div class="amp-product-grid">

                        <?php foreach ($sectionProducts as $product): ?>

                            <div class="amp-product-card">

                                <div class="amp-product-image">

                                    <?php if (!empty($product["image"])): ?>

                                        <img
                                            src="<?= htmlspecialchars($product["image"], ENT_QUOTES, "UTF-8") ?>"
                                            alt="<?= htmlspecialchars($product["name"] ?: "Product image", ENT_QUOTES, "UTF-8") ?>"
                                            loading="lazy"
                                            onerror="this.style.display='none'"
                                            style="width:100%;height:100%;object-fit:contain;padding:15px;"
                                        >

                                    <?php endif; ?>

                                </div>

                                <div class="amp-product-info">

                                    <p class="amp-product-name">
                                        <?= htmlspecialchars($product["name"] ?? "", ENT_QUOTES, "UTF-8") ?>
                                    </p>

                                    <?php if (!empty($product["product_number"])): ?>
                                        <p class="amp-product-model">
                                            Product No:
                                            <?= htmlspecialchars($product["product_number"], ENT_QUOTES, "UTF-8") ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($product["model"])): ?>
                                        <p class="amp-product-model">
                                            Model:
                                            <?= htmlspecialchars($product["model"], ENT_QUOTES, "UTF-8") ?>
                                        </p>
                                    <?php endif; ?>

                                    <a
                                        href="product-details.php?id=<?= (int)$product["id"] ?>"
                                        class="amp-know-more"
                                    >
                                        Know More
                                    </a>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <div class="amp-carousel-controls" hidden>

                        <button
                            type="button"
                            class="amp-arrow amp-prev"
                            aria-label="Previous products"
                        >←</button>

                        <div class="amp-dots" aria-label="Product pages"></div>

                        <button
                            type="button"
                            class="amp-arrow amp-next"
                            aria-label="Next products"
                        >→</button>

                    </div>

                </div>

            </div>

        <?php endif; ?>

    <?php endforeach; ?>

</section>


<!-- =========================
     APPLICATIONS SECTION
========================= -->

<section class="applications scroll-reveal" id="applications">

    <div class="applications-heading">
        <span class="section-label">APPLICATIONS</span>

        <h2>
            Applications Across
            <span>Industries.</span>
        </h2>

        <p>
            Explore the applications of Amptronix solutions
            across communication, energy and industrial sectors.
        </p>
    </div>


    <div class="applications-slider">

        <!-- SLIDE 1 -->
<?php
$application_chunks = array_chunk($applications, 4);
?>

<?php foreach ($application_chunks as $slide_index => $slide_applications): ?>

    <div class="application-slide <?= $slide_index === 0 ? 'active' : '' ?>">

        <?php foreach ($slide_applications as $application): ?>

            <div class="application-image-card">

                <img
                    src="<?= htmlspecialchars($application['image']) ?>"
                    alt="Application <?= (int)$application['application_number'] ?>"
                    class="application-preview-trigger"
                    onerror="this.style.display='none'"
                >

            </div>

        <?php endforeach; ?>

    </div>

<?php endforeach; ?>


        <!-- Show carousel controls only when there is more than one slide. -->
        <?php if (count($application_chunks) > 1): ?>
            <div class="application-controls">

                <button
                    type="button"
                    class="application-arrow"
                    id="applicationPrev"
                    aria-label="Previous applications">
                    ←
                </button>

                <div class="application-dots">
                    <?php foreach ($application_chunks as $slide_index => $slide_applications): ?>
                        <button
                            type="button"
                            class="application-dot <?= $slide_index === 0 ? 'active' : '' ?>"
                            data-slide="<?= $slide_index ?>"
                            aria-label="Go to application slide <?= $slide_index + 1 ?>"
                        ></button>
                    <?php endforeach; ?>
                </div>

                <button
                    type="button"
                    class="application-arrow"
                    id="applicationNext"
                    aria-label="Next applications">
                    →
                </button>

            </div>
        <?php endif; ?>

    </div>

</section>


<!-- OUR SOLUTIONS SECTION -->

<section class="solutions scroll-reveal" id="solutions">

    <div class="solutions-heading">

        <p class="small-title">
            WHAT WE DO
        </p>

        <h2>
            Our <span>Solutions</span>
        </h2>

        <p>
            Technology solutions designed for
            connectivity, energy efficiency and
            modern infrastructure.
        </p>

    </div>


    <div class="solutions-container">

    <?php foreach ($solutions as $solution): ?>

        <div class="solution-card">

            <?php if (!empty($solution["image"])): ?>
                <img
                    class="solution-image"
                    src="<?= htmlspecialchars($solution["image"], ENT_QUOTES, "UTF-8") ?>"
                    alt="<?= htmlspecialchars($solution["title"] ?? "Solution image", ENT_QUOTES, "UTF-8") ?>"
                    loading="lazy"
                >
            <?php endif; ?>

            <?php if (trim((string)($solution["title"] ?? "")) !== ""): ?>
                <?php
                    // Open the PDF when available; otherwise open the uploaded image.
                    $solution_file = !empty($solution["pdf_path"])
                        ? $solution["pdf_path"]
                        : ($solution["image"] ?? "");
                ?>

                <?php if ($solution_file !== ""): ?>
                    <h3>
                        <a
                            href="<?= htmlspecialchars($solution_file, ENT_QUOTES, "UTF-8") ?>"
                            target="_blank"
                            rel="noopener"
                            aria-label="Open <?= htmlspecialchars($solution["title"], ENT_QUOTES, "UTF-8") ?>"
                        ><?= htmlspecialchars($solution["title"], ENT_QUOTES, "UTF-8") ?></a>
                    </h3>
                <?php else: ?>
                    <h3><?= htmlspecialchars($solution["title"], ENT_QUOTES, "UTF-8") ?></h3>
                <?php endif; ?>
            <?php endif; ?>

        </div>

    <?php endforeach; ?>

</div>

</section>
<!-- ABOUT AMPTRONIX SECTION -->
<section class="about scroll-reveal" id="about">

    <div class="about-container">

        <!-- LEFT SIDE -->
        <div class="about-visual">

            <div class="about-bg"></div>

            <div class="about-year-card">

    <img 
        src="images/logo1.png" 
        alt="Amptronix Company Logo"
        class="about-company-logo"
    >

</div>

        </div>


        <!-- RIGHT SIDE -->
        <div class="about-content">

            <span class="about-tag">ABOUT AMPTRONIX</span>

            <h2>
                Technology That
                <span>Connects & Powers</span>
                the Future
            </h2>

            <p class="about-intro">
                AMPTRONIX is an original equipment manufacturing engineering company founded at India in 2002. It design, develop, engineered, manufactured and marketed the high performance – reliable user friendly energy efficient Optical and R.F products for Satellite downlink, Broad band and Power management applications. The Technical and Engineering heads having more than three decade industrial experience in the real time engineering needs and fulfillment of the customer demands.
            </p>


            <div class="specialization-box">

                <div class="specialization-title">
                    OUR SPECIALIZATION
                </div>

                <div class="specialization-items">

                    <div>
                        <strong>Satellite Downlink</strong>
                    </div>

                    <div>
                        <strong>CATV</strong>
                        <span>Cable Television</span>
                    </div>

                    <div>
                        <strong>Remote Monitoring</strong>
                    </div>

                    <div>
                        <strong>Battery Management</strong>
                    </div>

                </div>

            </div>


            <div class="about-bottom">

                <div>
                    <span>✓</span>
                    Energy Efficient
                </div>

                <div>
                    <span>✓</span>
                    Field-Proven Technology
                </div>

                <div>
                    <span>✓</span>
                    Solar & Grid Power
                </div>

            </div>

        </div>

    </div>

</section>




<!-- AMPTRONIX COMPANY TIMELINE: database-driven -->
<?php
$timeline_stmt = $pdo->query("SELECT year,title,description,image_path FROM company_timeline WHERE active=1 ORDER BY display_order ASC, year ASC");
$timeline_milestones = $timeline_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="amp-history-demo scroll-reveal" id="company-timeline">
  <div class="amp-history-inner">
    <div class="amp-history-heading">
      <span class="amp-history-kicker">OUR JOURNEY</span>
      <h2>Milestones That Move Us Forward</h2>
      <p>Our company journey and milestones.</p>
    </div>
    <div class="amp-history-track">
      <div class="amp-history-progress" aria-hidden="true"></div>
      <?php foreach ($timeline_milestones as $milestone): ?>
      <article class="amp-history-item">
        <div class="amp-history-node" aria-hidden="true"></div>
        <div class="amp-history-card">
          <div class="amp-history-image-wrap">
            <?php if (!empty($milestone['image_path'])): ?>
            <img class="amp-history-image" src="<?php echo htmlspecialchars($milestone['image_path']); ?>" alt="AMPTRONIX <?php echo htmlspecialchars((string)$milestone['year']); ?> milestone" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
            <div class="amp-history-image-placeholder" style="display:none"><span>IMAGE FOR <?php echo htmlspecialchars((string)$milestone['year']); ?></span></div>
            <?php else: ?>
            <div class="amp-history-image-placeholder" style="display:flex"><span>IMAGE FOR <?php echo htmlspecialchars((string)$milestone['year']); ?></span></div>
            <?php endif; ?>
          </div>
          <div class="amp-history-year"><?php echo htmlspecialchars((string)$milestone['year']); ?></div>
          <h3><?php echo htmlspecialchars($milestone['title']); ?></h3>
          <p><?php echo htmlspecialchars($milestone['description']); ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script>
/* AMPTRONIX timeline interaction: scroll-driven line + milestone reveal */
(function(){
  const section = document.querySelector('#company-timeline');
  if (!section) return;
  const track = section.querySelector('.amp-history-track');
  const fill = section.querySelector('.amp-history-progress');
  const items = section.querySelectorAll('.amp-history-item');
  items.forEach(item => item.classList.add('timeline-animate'));
  let ticking = false;
  function updateLine(){
    const rect = track.getBoundingClientRect();
    const viewportProgress = (window.innerHeight * 0.68 - rect.top) / rect.height;
    const progress = Math.max(0, Math.min(1, viewportProgress));
    fill.style.height = (progress * Math.max(0, rect.height - 16)) + 'px';
    ticking = false;
  }
  function requestUpdate(){ if(!ticking){ticking=true; requestAnimationFrame(updateLine);} }
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => entries.forEach(entry => {
      if(entry.isIntersecting){ entry.target.classList.add('is-visible'); observer.unobserve(entry.target); }
    }), {threshold:0.18});
    items.forEach(item => observer.observe(item));
  } else { items.forEach(item => item.classList.add('is-visible')); }
  window.addEventListener('scroll', requestUpdate, {passive:true});
  window.addEventListener('resize', requestUpdate);
  requestUpdate();
})();
</script>

<!-- =========================
     CONTACT SECTION
========================= -->

<section class="contact-section scroll-reveal" id="contact">

    <div class="contact-container">

        <!-- LEFT SIDE -->
        <div class="contact-info">

            <span class="contact-label">GET IN TOUCH</span>

            <h2>
                Let's Build Something
                <span>Great Together.</span>
            </h2>

            <p>
                Have a requirement or want to know more about our
                optical, RF, CATV, solar power and Satellite solutions?
                Get in touch with our team.
            </p>

            <div class="contact-details">

    <div class="contact-detail">
        <strong>Mobile</strong>
        <a href="tel:<?= htmlspecialchars($contact_phone_2) ?>">
            <?= htmlspecialchars($contact_phone_2) ?>
        </a>
    </div>

    <div class="contact-detail">
        <strong>Email</strong>
        <a href="mailto:<?= htmlspecialchars($contact_email) ?>">
            <?= htmlspecialchars($contact_email) ?>
        </a>
    </div>

</div>

            <!-- SOCIAL MEDIA -->

<div class="social-section">

    <h3>Follow Us</h3>

    <div class="social-icons">

        <!-- Facebook -->
        <a
            href="<?= htmlspecialchars($social_facebook) ?>"
            target="_blank"
            rel="noopener noreferrer"
            class="social-icon"
            aria-label="Facebook"
        >
            f
        </a>


        <!-- Instagram -->
        <a
            href="<?= htmlspecialchars($social_instagram) ?>"
            target="_blank"
            rel="noopener noreferrer"
            class="social-icon"
            aria-label="Instagram"
        >
            ◎
        </a>


        <!-- WhatsApp -->
        <a
            href="<?= htmlspecialchars($social_whatsapp) ?>"
            target="_blank"
            rel="noopener noreferrer"
            class="social-icon"
            aria-label="WhatsApp"
        >
            ☎
        </a>


        <!-- X -->
        <a
            href="<?= htmlspecialchars($social_x) ?>"
            target="_blank"
            rel="noopener noreferrer"
            class="social-icon"
            aria-label="X"
        >
            𝕏
        </a>


        <!-- Email -->
        <a
            href="mailto:<?= htmlspecialchars($contact_email) ?>"
            class="social-icon"
            aria-label="Email"
        >
            ✉
        </a>


        <!-- LinkedIn -->
        <a
            href="<?= htmlspecialchars($social_linkedin) ?>"
            target="_blank"
            rel="noopener noreferrer"
            class="social-icon"
            aria-label="LinkedIn"
        >
            in
        </a>

    </div>

</div>
</div>

        <!-- RIGHT SIDE - FORM -->

        <div class="contact-form-box">

            <form
    action="https://formsubmit.co/suryakumaran22334455@gmail.com"
    method="POST"
>

<input
    type="hidden"
    name="_subject"
    value="New Amptronix Website Enquiry"
>

<input
    type="hidden"
    name="_captcha"
    value="false"
>

<input
    type="hidden"
    name="_template"
    value="table"
>

<input
    type="hidden"
    name="_next"
    value="http://localhost:8000/#contact"
>



                <div class="form-row">

                    <div class="form-group">

                        <label for="name">Name</label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Your name"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="phone">Phone</label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            placeholder="Your phone number"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Your email address"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="message">Message</label>

                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        placeholder="Tell us about your requirement..."
                        required
                    ></textarea>

                </div>


                <button type="submit" class="contact-submit">
                    Send Message
                    <span>→</span>
                </button>

            </form>

        </div>

    </div>

</section>
<!-- =========================
     PREMIUM FOOTER
========================= -->

<footer class="site-footer">

    <div class="footer-container">

        <!-- COMPANY -->

        <div class="footer-company">

            <img
                src="images/logo.png"
                alt="Amptronix"
                class="footer-logo"
            >

            <p>
                Innovative and energy-efficient technology
                solutions for optical, RF, CATV, solar power
                and Satellite applications.
            </p>

        </div>


        <!-- QUICK LINKS -->

        <div class="footer-links">

            <h3>Quick Links</h3>

            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#solutions">Solutions</a>
            <a href="#products">Products</a>
            <a href="#applications">Applications</a>
            <a href="#contact">Contact</a>

        </div>


        <!-- SOLUTIONS -->

        <div class="footer-links">

            <h3>Solutions</h3>

            <a href="#solutions">Optical Networks</a>
            <a href="#solutions">RF & CATV</a>
            <a href="#solutions">Solar Power</a>
            <a href="#solutions">Satellite</a>

        </div>


       <!-- CONTACT -->

<div class="footer-contact">

    <h3>Contact</h3>

    <?php if ($contact_address !== ""): ?>
        <p><?= nl2br(htmlspecialchars($contact_address, ENT_QUOTES, "UTF-8")) ?></p>
    <?php endif; ?>

    <?php if ($contact_address_2 !== ""): ?>
        <p><?= nl2br(htmlspecialchars($contact_address_2, ENT_QUOTES, "UTF-8")) ?></p>
    <?php endif; ?>

    <?php if ($contact_phone_1 !== ""): ?>
        <a href="tel:<?= htmlspecialchars($contact_phone_1, ENT_QUOTES, "UTF-8") ?>">
            <?= htmlspecialchars($contact_phone_1, ENT_QUOTES, "UTF-8") ?>
        </a>
    <?php endif; ?>

    <?php if ($contact_phone_2 !== ""): ?>
        <a href="tel:<?= htmlspecialchars($contact_phone_2, ENT_QUOTES, "UTF-8") ?>">
            <?= htmlspecialchars($contact_phone_2, ENT_QUOTES, "UTF-8") ?>
        </a>
    <?php endif; ?>

    <?php if ($contact_email !== ""): ?>
        <a href="mailto:<?= htmlspecialchars($contact_email, ENT_QUOTES, "UTF-8") ?>">
            <?= htmlspecialchars($contact_email, ENT_QUOTES, "UTF-8") ?>
        </a>
    <?php endif; ?>

</div>


    <!-- FOOTER BOTTOM -->

    <div class="footer-bottom">

        <p>
            © 2026 Amptronix. All Rights Reserved.
        </p>

        <p>
            Technology • Energy • Innovation
        </p>

    </div>

</footer>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const menuToggle = document.getElementById("menuToggle");
    const nav = document.querySelector("header nav");

    if (!menuToggle || !nav) {
        return;
    }

    menuToggle.addEventListener("click", function () {

        nav.classList.toggle("active");

        if (nav.classList.contains("active")) {

            menuToggle.textContent = "✕";
            menuToggle.setAttribute(
                "aria-label",
                "Close menu"
            );

        } else {

            menuToggle.textContent = "☰";
            menuToggle.setAttribute(
                "aria-label",
                "Open menu"
            );

        }

    });


    /* Close menu when a link is clicked */

    nav.querySelectorAll("a").forEach(function (link) {

        link.addEventListener("click", function () {

            nav.classList.remove("active");

            menuToggle.textContent = "☰";

            menuToggle.setAttribute(
                "aria-label",
                "Open menu"
            );

        });

    });

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const revealSections =
        document.querySelectorAll(".scroll-reveal");

    const observer = new IntersectionObserver(
        function (entries) {

            entries.forEach(function (entry) {

                if (entry.isIntersecting) {

                    entry.target.classList.add("show");

                    observer.unobserve(entry.target);
                }

            });

        },
        {
            threshold: 0.15
        }
    );


    revealSections.forEach(function (section) {

        observer.observe(section);

    });

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const pages = document.querySelectorAll(".product-page");
    const dots = document.querySelectorAll(".new-product-dot");

    const nextButton =
        document.getElementById("newProductNext");

    const prevButton =
        document.getElementById("newProductPrev");

    let currentPage = 0;


    function showPage(index) {

        if (index >= pages.length) {
            index = 0;
        }

        if (index < 0) {
            index = pages.length - 1;
        }

        currentPage = index;


        pages.forEach(function (page) {
            page.classList.remove("active");
        });


        dots.forEach(function (dot) {
            dot.classList.remove("active");
        });


        pages[currentPage].classList.add("active");


        if (dots[currentPage]) {
            dots[currentPage].classList.add("active");
        }

    }


    if (nextButton) {

        nextButton.addEventListener("click", function () {

            showPage(currentPage + 1);

        });

    }


    if (prevButton) {

        prevButton.addEventListener("click", function () {

            showPage(currentPage - 1);

        });

    }


    dots.forEach(function (dot, index) {

        dot.addEventListener("click", function () {

            showPage(index);

        });

    });


    showPage(0);

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const applicationSlider =
        document.querySelector(".applications-slider");

    if (!applicationSlider) {
        return;
    }

    const applicationSlides =
        applicationSlider.querySelectorAll(".application-slide");

    const applicationDots =
        applicationSlider.querySelectorAll(".application-dot");

    const applicationNext =
        document.getElementById("applicationNext");

    const applicationPrev =
        document.getElementById("applicationPrev");

    let currentApplicationSlide = 0;


    function showApplicationSlide(index) {

        if (index >= applicationSlides.length) {
            currentApplicationSlide = 0;
        }
        else if (index < 0) {
            currentApplicationSlide =
                applicationSlides.length - 1;
        }
        else {
            currentApplicationSlide = index;
        }


        applicationSlides.forEach(function (slide) {
            slide.classList.remove("active");
        });


        applicationDots.forEach(function (dot) {
            dot.classList.remove("active");
        });


        applicationSlides[currentApplicationSlide]
            .classList.add("active");


        applicationDots[currentApplicationSlide]
            .classList.add("active");

    }


    if (applicationNext) {

        applicationNext.addEventListener("click", function () {

            showApplicationSlide(
                currentApplicationSlide + 1
            );

        });

    }


    if (applicationPrev) {

        applicationPrev.addEventListener("click", function () {

            showApplicationSlide(
                currentApplicationSlide - 1
            );

        });

    }


    applicationDots.forEach(function (dot, index) {

        dot.addEventListener("click", function () {

            showApplicationSlide(index);

        });

    });

});
</script>

<div class="application-preview" id="applicationPreview">

    <button
        type="button"
        class="application-preview-close"
        id="applicationPreviewClose"
        aria-label="Close preview"
    >
        ×
    </button>

    <img
        src=""
        alt="Application Preview"
        class="application-preview-image"
        id="applicationPreviewImage"
    >

    <div class="application-zoom-controls">

        <button
            type="button"
            class="application-zoom-button"
            id="applicationZoomOut"
        >
            −
        </button>

        <span
            class="application-zoom-level"
            id="applicationZoomLevel"
        >
            100%
        </span>

        <button
            type="button"
            class="application-zoom-button"
            id="applicationZoomIn"
        >
            +
        </button>

        <button
            type="button"
            class="application-zoom-button"
            id="applicationZoomReset"
        >
            ↺
        </button>

    </div>

</div>

<script>
/* Application image viewer — attached after the gallery and viewer markup exist. */
(function () {
    const gallery = document.querySelector(".applications-slider");
    const viewer = document.getElementById("applicationPreview");
    const viewerImage = document.getElementById("applicationPreviewImage");
    const closeButton = document.getElementById("applicationPreviewClose");
    const zoomIn = document.getElementById("applicationZoomIn");
    const zoomOut = document.getElementById("applicationZoomOut");
    const zoomReset = document.getElementById("applicationZoomReset");
    const zoomLevel = document.getElementById("applicationZoomLevel");

    if (!gallery || !viewer || !viewerImage || !closeButton) return;

    let scale = 1;
    function updateZoom() {
        viewerImage.style.transform = `scale(${scale})`;
        if (zoomLevel) zoomLevel.textContent = `${Math.round(scale * 100)}%`;
        viewerImage.style.cursor = scale > 1 ? "grab" : "zoom-in";
    }
    function openViewer(image) {
        const source = image.currentSrc || image.src;
        if (!source) return;
        viewerImage.src = source;
        viewerImage.alt = image.alt || "Application image";
        scale = 1;
        updateZoom();
        viewer.classList.add("show");
        viewer.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
    }
    function closeViewer() {
        viewer.classList.remove("show");
        viewer.setAttribute("aria-hidden", "true");
        viewerImage.removeAttribute("src");
        document.body.style.overflow = "";
    }

    gallery.addEventListener("click", function (event) {
        const image = event.target.closest(".application-image-card img");
        if (image) openViewer(image);
    });
    gallery.querySelectorAll(".application-image-card").forEach(function (card) {
        card.setAttribute("role", "button");
        card.setAttribute("tabindex", "0");
        card.setAttribute("aria-label", "Open application image full screen");
        card.addEventListener("keydown", function (event) {
            if (event.target !== card) return;
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                const image = card.querySelector("img");
                if (image) openViewer(image);
            }
        });
    });

    closeButton.addEventListener("click", closeViewer);
    viewer.addEventListener("click", function (event) {
        if (event.target === viewer) closeViewer();
    });
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && viewer.classList.contains("show")) closeViewer();
    });
    if (zoomIn) zoomIn.addEventListener("click", function () { scale = Math.min(4, scale + 0.25); updateZoom(); });
    if (zoomOut) zoomOut.addEventListener("click", function () { scale = Math.max(0.5, scale - 0.25); updateZoom(); });
    if (zoomReset) zoomReset.addEventListener("click", function () { scale = 1; updateZoom(); });
})();
</script>

<script>
/* =========================================
   AMP PRODUCT CAROUSELS
   Show controls only when more than 4 products exist.
========================================= */

document.querySelectorAll(".amp-carousel").forEach((carousel) => {

    const grid = carousel.querySelector(".amp-product-grid");
    const cards = Array.from(carousel.querySelectorAll(".amp-product-card"));
    const controls = carousel.querySelector(".amp-carousel-controls");
    const dotsContainer = carousel.querySelector(".amp-dots");
    const previousButton = carousel.querySelector(".amp-prev");
    const nextButton = carousel.querySelector(".amp-next");

    if (!grid || !controls || !dotsContainer || !previousButton || !nextButton) {
        return;
    }

    const pageSize = Math.max(1, Number(carousel.dataset.pageSize) || 4);
    const pageCount = Math.ceil(cards.length / pageSize);
    let currentPage = 0;

    // Four or fewer products fit on one page, so navigation stays hidden.
    if (pageCount <= 1) {
        cards.forEach((card) => { card.hidden = false; });
        controls.hidden = true;
        return;
    }

    controls.hidden = false;

    // Build the exact number of page dots from the current product count.
    dotsContainer.replaceChildren();
    const dots = [];

    for (let page = 0; page < pageCount; page++) {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.className = "amp-dot";
        dot.setAttribute("aria-label", `Go to product page ${page + 1}`);
        dot.addEventListener("click", () => showPage(page));
        dotsContainer.appendChild(dot);
        dots.push(dot);
    }

    function showPage(page) {
        currentPage = Math.min(Math.max(page, 0), pageCount - 1);
        const firstCard = currentPage * pageSize;
        const lastCard = firstCard + pageSize;

        cards.forEach((card, index) => {
            card.hidden = index < firstCard || index >= lastCard;
        });

        dots.forEach((dot, index) => {
            const active = index === currentPage;
            dot.classList.toggle("active", active);
            dot.setAttribute("aria-current", active ? "page" : "false");
        });

        previousButton.disabled = currentPage === 0;
        nextButton.disabled = currentPage === pageCount - 1;
    }

    previousButton.addEventListener("click", () => showPage(currentPage - 1));
    nextButton.addEventListener("click", () => showPage(currentPage + 1));

    showPage(0);
});
</script>

</body>

</html>