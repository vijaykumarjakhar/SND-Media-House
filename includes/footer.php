<?php
/**
 * SND Media — Modular Footer Component
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/functions.php';

$settings = get_all_settings();
?>
  <!-- FOOTER -->
  <footer class="bg-slate-950 border-t border-slate-900 pt-12 pb-8 text-xs text-slate-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        
        <!-- Col 1: Brand & Socials -->
        <div>
          <div class="flex items-center gap-2.5 mb-3">
            <img src="assets/images/snd-logo.svg" onerror="this.onerror=null;this.src='https://yt3.googleusercontent.com/SrQdaVJkuZ6tMqEnEEOeFdW1MWR_YVT7xPSXR_skO36sMI48AkqieclVh7TZRgd49ZePERdz=s160-c-k-c0x00ffffff-no-rj'" alt="SND Media Logo" class="w-9 h-9 rounded-full object-cover border border-amber-400 bg-white">
            <span class="text-white font-heading font-extrabold text-lg"><span class="text-amber-400">SND</span> MEDIA</span>
          </div>
          <p class="text-slate-400 leading-relaxed mb-4">
            Where Students Lead Media &mdash; Speak. Narrate. Discover. Integrated news publishing, <code class="text-amber-300">@sndschoolmedia</code> broadcast network, and production studio facilities at SND Public School Campus, Palwal.
          </p>
          <div class="flex gap-3 text-slate-300 text-base">
            <a href="<?= sanitize_output($settings['social_youtube'] ?? 'https://www.youtube.com/@sndschoolmedia') ?>" target="_blank" rel="noopener noreferrer" class="hover:text-red-500 transition" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
            <a href="<?= sanitize_output($settings['social_twitter'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" class="hover:text-blue-400 transition" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
            <a href="<?= sanitize_output($settings['social_instagram'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" class="hover:text-pink-500 transition" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="<?= sanitize_output($settings['social_linkedin'] ?? '#') ?>" target="_blank" rel="noopener noreferrer" class="hover:text-blue-600 transition" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
          </div>
        </div>

        <!-- Col 2: News Sections -->
        <div>
          <h4 class="text-white font-bold mb-3 uppercase tracking-wider text-[11px]">News Desks</h4>
          <ul class="space-y-2">
            <li><a href="index.php#national" class="hover:text-white transition">National & Politics</a></li>
            <li><a href="index.php#business" class="hover:text-white transition">Economy, Markets & Budget</a></li>
            <li><a href="index.php#business" class="hover:text-white transition">Technology & AI Startups</a></li>
            <li><a href="index.php#youtube-hub" class="hover:text-white transition">Broadcast Bulletins (@sndschoolmedia)</a></li>
            <li><a href="index.php#special-reports" class="hover:text-white transition">Special Ground Investigations</a></li>
          </ul>
        </div>

        <!-- Col 3: Space Rental -->
        <div>
          <h4 class="text-white font-bold mb-3 uppercase tracking-wider text-[11px]">Commercial Space on Rent</h4>
          <ul class="space-y-2">
            <li><a href="studio-rental#studio-a" class="hover:text-amber-400 transition">Studio Floor A (Chroma & Newsroom)</a></li>
            <li><a href="studio-rental#studio-b" class="hover:text-amber-400 transition">Podcast Suite Studio B</a></li>
            <li><a href="studio-rental#digital-ads" class="hover:text-amber-400 transition">Digital Ad Inventory & Sponsorships</a></li>
            <li><a href="studio-rental#specs" class="hover:text-amber-400 transition">OB Van & Live Uplink Infrastructure</a></li>
            <li><a href="studio-rental" class="hover:text-amber-400 transition">Commercial Rate Card (From ₹3,500/hr)</a></li>
          </ul>
        </div>

        <!-- Col 4: Contact & Campus -->
        <div>
          <h4 class="text-white font-bold mb-3 uppercase tracking-wider text-[11px]">Editorial & Studio Offices</h4>
          <p class="leading-relaxed">
            <strong>Headquarters:</strong><br>
            <?= sanitize_output($settings['hq_address'] ?? 'SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102') ?>
          </p>
          <p class="mt-2.5 space-y-1">
            <span class="block"><strong>Website:</strong> <a href="https://<?= sanitize_output($settings['site_domain'] ?? 'sndmedia.in') ?>" target="_blank" rel="noopener noreferrer" class="text-slate-300 hover:text-white underline"><?= sanitize_output($settings['site_domain'] ?? 'sndmedia.in') ?></a></span>
            <span class="block"><strong>Studio & Desk Phone:</strong> <a href="tel:<?= sanitize_output($settings['phone_studio'] ?? '+917206622711') ?>" class="text-slate-300 hover:text-white"><?= sanitize_output($settings['phone_studio'] ?? '+91-7206622711') ?></a></span>
            <span class="block"><strong>News Desk:</strong> <?= sanitize_output($settings['email_newsdesk'] ?? 'newsdesk@sndmedia.in') ?></span>
            <span class="block"><strong>Leasing Desk:</strong> <?= sanitize_output($settings['email_space'] ?? 'space@sndmedia.in') ?></span>
          </p>
        </div>

      </div>

      <!-- Legal Links & Copyright -->
      <div class="border-t border-slate-900 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-[11px] text-slate-500">
        <div class="flex flex-col sm:flex-row items-center gap-2 text-center sm:text-left">
          <span>&copy; <?= date('Y') ?> SND Media Pvt. Ltd. (<?= sanitize_output($settings['site_domain'] ?? 'sndmedia.in') ?>). All Rights Reserved.</span>
          <span class="hidden sm:inline text-slate-700">&bull;</span>
          <span>Developed by <a href="https://securetechnology.in" target="_blank" rel="noopener noreferrer" class="font-bold hover:underline transition inline-flex items-center gap-1 group"><i class="fa-solid fa-shield-halved text-[10px] text-emerald-400"></i> <span class="text-emerald-400 group-hover:text-emerald-300" style="color: #34d399;">Secure</span><span class="text-sky-400 group-hover:text-sky-300" style="color: #38bdf8;">Technology</span></a></span>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-4">
          <a href="legal/privacy-policy" class="hover:underline hover:text-slate-300">Privacy Policy</a>
          <a href="legal/terms-of-service" class="hover:underline hover:text-slate-300">Terms of Service</a>
          <a href="legal/rental-rules" class="hover:underline hover:text-slate-300">Rental Agreement Rules</a>
          <a href="legal/editorial-ethics" class="hover:underline hover:text-slate-300">Editorial Ethics Code</a>
          <a href="admin/login" class="hover:text-amber-400 font-medium"><i class="fa-solid fa-lock text-[9px] mr-1"></i>Staff CMS</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Include Commercial Space Rental Modal -->
  <?php require_once __DIR__ . '/rental_modal.php'; ?>

  <!-- Global Frontend JavaScript -->
  <script src="assets/js/main.js"></script>
  <script>
    function toggleMobileMenu() {
      const drawer = document.getElementById('mobile-nav-drawer');
      const icon = document.getElementById('mobile-menu-icon');
      if (!drawer) return;
      
      if (drawer.classList.contains('hidden')) {
        drawer.classList.remove('hidden');
        if (icon) {
          icon.classList.remove('fa-bars');
          icon.classList.add('fa-xmark');
        }
      } else {
        drawer.classList.add('hidden');
        if (icon) {
          icon.classList.remove('fa-xmark');
          icon.classList.add('fa-bars');
        }
      }
    }

    function toggleTickerPlay() {
      const marquee = document.getElementById('ticker-marquee');
      const icon = document.getElementById('ticker-play-icon');
      if (!marquee) return;
      const current = window.getComputedStyle(marquee).animationPlayState;
      if (current === 'paused' || marquee.style.animationPlayState === 'paused') {
        marquee.style.animationPlayState = 'running';
        if (icon) icon.className = 'fa-solid fa-pause text-[10px]';
      } else {
        marquee.style.animationPlayState = 'paused';
        if (icon) icon.className = 'fa-solid fa-play text-[10px]';
      }
    }
  </script>
</body>
</html>
