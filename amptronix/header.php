<?php
/* Shared Amptronix public-site header. Include from every public page. */
?>
<style>
.amp-site-header{
    width:100%; min-height:80px; height:80px; padding:0 7%;
    display:flex; align-items:center; justify-content:space-between; gap:28px;
    position:sticky; top:0; z-index:1000;
    background:rgba(255,255,255,.97); border-bottom:1px solid #e8eef5;
    box-shadow:0 5px 22px rgba(16,35,61,.045);
    backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px);
    box-sizing:border-box;
}
.amp-site-header *{box-sizing:border-box}
.amp-site-logo{display:flex;align-items:center;flex:0 0 auto;text-decoration:none}
.amp-site-logo img{display:block;width:180px;height:auto;max-height:48px;object-fit:contain}
.amp-menu-toggle{
    display:none; border:0; background:transparent; color:#10233d;
    font-size:27px; line-height:1; cursor:pointer; padding:8px;
}
.amp-site-nav{display:flex;align-items:center;justify-content:flex-end;gap:30px;flex-wrap:wrap}
.amp-site-nav a{
    display:inline-flex;align-items:center;gap:7px;
    color:#42536a;text-decoration:none;font-family:Arial,Helvetica,sans-serif;
    font-size:14px;font-weight:600;white-space:nowrap;transition:color .2s ease;
}
.amp-site-nav a:hover{color:#087cf2}
.amp-site-nav .amp-profile-link{margin-left:2px;color:#42536a}
.amp-site-nav .amp-profile-link svg{display:block}
@media(max-width:1050px){
 .amp-site-header{padding-left:5%;padding-right:5%;gap:18px}
 .amp-site-nav{gap:18px}
}
@media(max-width:760px){
 .amp-site-header{height:auto;min-height:72px;padding:12px 5%;flex-wrap:wrap}
 .amp-site-logo img{width:155px;max-height:44px}
 .amp-menu-toggle{display:inline-flex;align-items:center;justify-content:center;margin-left:auto}
 .amp-site-nav{
   display:none;width:100%;flex-direction:column;align-items:stretch;gap:0;
   padding:8px 0 5px;border-top:1px solid #e8eef5;
 }
 .amp-site-nav.is-open{display:flex}
 .amp-site-nav a{width:100%;padding:12px 5px;font-size:14px}
 .amp-site-nav .amp-profile-link{margin:0}
}
@media(prefers-reduced-motion:reduce){.amp-site-header *{transition:none!important}}
</style>
<header class="amp-site-header">
  <a class="amp-site-logo" href="index.php#home" aria-label="Amptronix Home">
    <img src="images/logo-white.png" alt="Amptronix">
  </a>
  <button class="amp-menu-toggle" id="menuToggle" type="button" aria-label="Open menu" aria-expanded="false">☰</button>
  <nav class="amp-site-nav" id="mainNav" aria-label="Main navigation">
    <a href="index.php#home">Home</a>
    <a href="index.php#products">Products</a>
    <a href="index.php#applications">Applications</a>
    <a href="index.php#solutions">Solutions</a>
    <a href="index.php#about">About Us</a>
    <a href="index.php#contact">Contact</a>
    <a href="https://admin.amptronix.co.in/" class="amp-profile-link" target="_blank" rel="noopener noreferrer" aria-label="Login" title="Login">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M5 21v-2a7 7 0 0 1 14 0v2"></path></svg>
    </a>
  </nav>
</header>
<script>
(function(){
 const toggle=document.getElementById('menuToggle');
 const nav=document.getElementById('mainNav');
 if(!toggle||!nav||toggle.dataset.ampBound==='1') return;
 toggle.dataset.ampBound='1';
 toggle.addEventListener('click',function(){
   const open=nav.classList.toggle('is-open');
   toggle.setAttribute('aria-expanded',open?'true':'false');
   toggle.setAttribute('aria-label',open?'Close menu':'Open menu');
   toggle.textContent=open?'×':'☰';
 });
 nav.querySelectorAll('a').forEach(function(link){
   link.addEventListener('click',function(){
     nav.classList.remove('is-open');
     toggle.setAttribute('aria-expanded','false');
     toggle.setAttribute('aria-label','Open menu');
     toggle.textContent='☰';
   });
 });
})();
</script>
