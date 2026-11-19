<?php
/**
 * SND Media — Dedicated Commercial Studio Space & Media Rental Portal
 * Features interactive rate estimator, live floor selector, and technical specs
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/includes/functions.php';

$settings = get_all_settings();
$site_domain = $settings['site_domain'] ?? 'sndmedia.in';
$page_title = 'Studio Rental, Production Floors & Ad Space | Palwal, Delhi NCR';
$seo_desc = 'Commercial broadcast studios, soundproof chroma stages, 4-mic podcast suites & highway outdoor hoardings on rent at SND Public School Campus, Palwal (Delhi NCR). From ₹3,500/hr.';
$canonical_url = 'https://' . $site_domain . '/studio-rental';
$seo_type = 'service';
$seo_keywords = 'studio rental Palwal, podcast studio Delhi NCR, chroma green screen stage hire, video production studio Haryana, highway billboard advertising Palwal';
$breadcrumbs = [
    ['name' => 'Home', 'url' => 'https://' . $site_domain . '/'],
    ['name' => 'Studio Space & Advertising Rentals', 'url' => 'https://' . $site_domain . '/studio-rental']
];
$active_nav = 'rental';

require_once __DIR__ . '/includes/header.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  <!-- PAGE HERO: INTERACTIVE 2-COLUMN SHOWPIECE -->
  <section class="mb-14 bg-gradient-to-br from-slate-900 via-brand-card to-amber-950/20 rounded-3xl p-6 sm:p-10 lg:p-12 border border-slate-800 shadow-2xl relative overflow-hidden">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">
      
      <!-- Left Column (7 cols): Editorial Presentation -->
      <div class="lg:col-span-7">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-4 border border-amber-400/30">
          <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
          <span>Commercial Studio Infrastructure &bull; Palwal &amp; Delhi NCR</span>
        </div>
        
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white leading-tight font-heading">
          Broadcast Studios, Podcast Suites &amp; Prime Ad Space on Rent
        </h1>
        
        <p class="text-sm sm:text-base text-slate-300 mt-4 leading-relaxed font-sans">
          State-of-the-art soundproof production floors, 4K multi-cam switcher setups, and outdoor billboard hoardings located at <strong>SND Public School Campus, Palwal</strong> (Haryana &bull; Delhi NCR). Available for hourly slots, full-day shifts, and multi-week commercial schedules.
        </p>

        <!-- Technical Capability Quick Tags -->
        <div class="mt-6 flex flex-wrap gap-2.5">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-800/80 border border-slate-700 text-slate-300 text-xs font-medium">
            <i class="fa-solid fa-volume-xmark text-amber-400"></i> NC-25 Sound Isolation
          </span>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-800/80 border border-slate-700 text-slate-300 text-xs font-medium">
            <i class="fa-solid fa-video text-amber-400"></i> 4K 12G-SDI Blackmagic Switcher
          </span>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-800/80 border border-slate-700 text-slate-300 text-xs font-medium">
            <i class="fa-solid fa-bolt text-amber-400"></i> 100% Dual DG Power Backup
          </span>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-800/80 border border-slate-700 text-slate-300 text-xs font-medium">
            <i class="fa-solid fa-network-wired text-amber-400"></i> 1 Gbps Leased Line Fiber
          </span>
        </div>
        
        <div class="mt-8 flex flex-wrap items-center gap-3">
          <button onclick="openRentalModal('Full Studio Booking')" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-xs sm:text-sm px-6 py-3.5 rounded-xl shadow-lg shadow-amber-500/20 transition transform active:scale-95 flex items-center gap-2">
            <i class="fa-solid fa-calendar-check"></i> Book Studio Floor Inspection
          </button>
          <a href="tel:<?= sanitize_output($settings['phone_studio'] ?? '+917206622711') ?>" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs sm:text-sm px-5 py-3.5 rounded-xl border border-slate-700 transition flex items-center gap-2">
            <i class="fa-solid fa-phone text-amber-400"></i> Call Desk: <?= sanitize_output($settings['phone_studio'] ?? '+91-7206622711') ?>
          </a>
        </div>
      </div>

      <!-- Right Column (5 cols): Interactive Studio Floor & Rate Estimator -->
      <div class="lg:col-span-5">
        <div class="bg-slate-950/80 backdrop-blur-xl border border-amber-400/30 rounded-2xl p-6 shadow-2xl relative">
          <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
              <span class="text-xs font-black uppercase text-amber-400 tracking-wider">Instant Rate Estimator</span>
            </div>
            <span class="text-[10px] text-slate-400 bg-slate-800 px-2 py-0.5 rounded font-mono">Live Rates</span>
          </div>

          <!-- Studio Floor Switcher Tabs -->
          <div class="grid grid-cols-3 gap-1.5 bg-slate-900 p-1 rounded-xl mb-4 text-xs font-bold">
            <button type="button" onclick="selectEstimatorFloor('studio_a')" id="btn-est-studio_a" class="py-2 px-2 rounded-lg bg-amber-500 text-slate-950 shadow transition text-center truncate">
              Studio A
            </button>
            <button type="button" onclick="selectEstimatorFloor('studio_b')" id="btn-est-studio_b" class="py-2 px-2 rounded-lg text-slate-400 hover:text-white transition text-center truncate">
              Studio B
            </button>
            <button type="button" onclick="selectEstimatorFloor('billboard')" id="btn-est-billboard" class="py-2 px-2 rounded-lg text-slate-400 hover:text-white transition text-center truncate">
              Billboard
            </button>
          </div>

          <!-- Duration Selector Pills -->
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Select Duration</label>
          <div class="grid grid-cols-3 gap-2 mb-5">
            <button type="button" onclick="selectEstimatorDuration('half_day')" id="btn-dur-half" class="py-2 px-1 text-center text-xs font-semibold rounded-lg bg-slate-800 border border-amber-400/50 text-amber-300">
              Half Day (4h)
            </button>
            <button type="button" onclick="selectEstimatorDuration('full_day')" id="btn-dur-full" class="py-2 px-1 text-center text-xs font-semibold rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white">
              Full Shift (8h)
            </button>
            <button type="button" onclick="selectEstimatorDuration('multi_day')" id="btn-dur-multi" class="py-2 px-1 text-center text-xs font-semibold rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white">
              3-Day Block
            </button>
          </div>

          <!-- Dynamic Pricing Box -->
          <div class="bg-gradient-to-r from-slate-900 to-amber-950/40 border border-slate-800 rounded-xl p-4 mb-4 flex items-center justify-between">
            <div>
              <div class="text-[10px] text-slate-400 uppercase font-medium">Estimated Pricing</div>
              <div id="estimator-price" class="text-2xl sm:text-3xl font-black text-amber-400 font-mono mt-0.5">₹26,000</div>
              <div id="estimator-tax-note" class="text-[10px] text-slate-500 mt-0.5">+ Applicable Taxes &bull; All Included</div>
            </div>
            <div id="estimator-badge" class="text-right">
              <span class="inline-block text-[10px] font-bold uppercase bg-amber-400/20 text-amber-300 px-2.5 py-1 rounded-full border border-amber-400/30">
                Studio Floor A
              </span>
            </div>
          </div>

          <!-- Included Features Checklist -->
          <div class="mb-5">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Package Inclusions:</div>
            <ul id="estimator-inclusions" class="space-y-1.5 text-xs text-slate-300">
              <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i> <span>2,200 sq.ft stage with 14ft height</span></li>
              <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i> <span>Chroma Green Cyclorama + Bi-Color Grid</span></li>
              <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i> <span>100% DG Power Backup &amp; Gigabit Fiber</span></li>
            </ul>
          </div>

          <!-- Instant Reserve Button -->
          <button type="button" onclick="lockEstimatedBooking()" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs uppercase tracking-wider py-3 rounded-xl shadow-lg shadow-amber-500/20 transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-bolt"></i> Reserve This Estimated Slot
          </button>
        </div>
      </div>

    </div>
  </section>

  <!-- INTERACTIVE FILTER BAR FOR PRODUCTION FLOORS -->
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 pb-4 border-b border-slate-800">
    <div>
      <span class="text-xs font-black uppercase text-amber-400 tracking-wider">COMMERCIAL PRODUCTION INVENTORY</span>
      <h2 class="text-2xl sm:text-3xl font-extrabold text-white mt-1">Available Facilities &amp; Production Spaces</h2>
    </div>
    
    <!-- Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto max-w-full pb-1">
      <button type="button" onclick="filterFacilities('all')" id="filter-fac-all" class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-400 text-slate-950 transition">
        All Facilities (3)
      </button>
      <button type="button" onclick="filterFacilities('video')" id="filter-fac-video" class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-800 text-slate-300 hover:text-white transition">
        Video Stages
      </button>
      <button type="button" onclick="filterFacilities('audio')" id="filter-fac-audio" class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-800 text-slate-300 hover:text-white transition">
        Podcast &amp; Audio
      </button>
      <button type="button" onclick="filterFacilities('ads')" id="filter-fac-ads" class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-800 text-slate-300 hover:text-white transition">
        Outdoor &amp; Web Ads
      </button>
    </div>
  </div>

  <!-- FACILITY RATE CARDS GRID (3 Columns Widescreen) -->
  <section class="mb-16">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      
      <!-- Facility 1: Studio A -->
      <div id="studio-a" data-category="video" class="facility-card bg-brand-card rounded-2xl overflow-hidden border-2 border-slate-800 hover:border-amber-400/60 transition duration-300 group flex flex-col justify-between shadow-xl">
        <div>
          <div class="relative aspect-video overflow-hidden bg-slate-900">
            <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=800&q=80" alt="Studio A Newsroom Floor" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            <div class="absolute top-3 left-3 bg-amber-400 text-slate-950 font-black text-xs px-2.5 py-1 rounded shadow">STUDIO A &bull; 2,200 SQ FT</div>
            <div class="absolute bottom-3 right-3 bg-black/85 text-amber-300 font-mono text-sm px-3 py-1 rounded font-bold">₹7,500/hr</div>
          </div>
          <div class="p-6">
            <h3 class="text-xl font-bold text-white group-hover:text-amber-300 transition">Main Newsroom &amp; Chroma Stage</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
              2,200 sq.ft stage with 14ft clearance height, motorized overhead lighting grid, cyclorama green screen, and dedicated master control room.
            </p>
            
            <div class="mt-5 space-y-2.5 border-t border-slate-800 pt-4 text-xs text-slate-300">
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Floor Space:</span>
                <span class="font-bold text-white">2,200 sq. ft (NC-25 Acoustic)</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Lighting:</span>
                <span class="font-bold text-white">Cool Bi-Color LED Overhead Grid</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Video Routing:</span>
                <span class="font-bold text-white">12G-SDI 4K Blackmagic Switcher</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Half Day (4 hrs):</span>
                <span class="font-bold text-amber-400">₹26,000</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Full Shift (8 hrs):</span>
                <span class="font-bold text-amber-400">₹48,000</span>
              </div>
            </div>
          </div>
        </div>
        <div class="p-6 pt-0">
          <button onclick="openRentalModal('Studio A - Newsroom Stage (2200 sq ft)')" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs py-3 rounded-xl shadow transition hover:brightness-110">
            Book Studio A Floor
          </button>
        </div>
      </div>

      <!-- Facility 2: Studio B -->
      <div id="studio-b" data-category="audio" class="facility-card bg-brand-card rounded-2xl overflow-hidden border-2 border-slate-800 hover:border-amber-400/60 transition duration-300 group flex flex-col justify-between shadow-xl">
        <div>
          <div class="relative aspect-video overflow-hidden bg-slate-900">
            <img src="https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=800&q=80" alt="Studio B Podcast Suite" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            <div class="absolute top-3 left-3 bg-amber-400 text-slate-950 font-black text-xs px-2.5 py-1 rounded shadow">STUDIO B &bull; 4-MIC PODCAST</div>
            <div class="absolute bottom-3 right-3 bg-black/85 text-amber-300 font-mono text-sm px-3 py-1 rounded font-bold">₹3,500/hr</div>
          </div>
          <div class="p-6">
            <h3 class="text-xl font-bold text-white group-hover:text-amber-300 transition">Multi-Cam Podcast Recording Suite</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
              Tailored for conversational podcasts, corporate talks, and one-on-one video interviews with studio-grade acoustics and multi-angle 4K capture.
            </p>
            
            <div class="mt-5 space-y-2.5 border-t border-slate-800 pt-4 text-xs text-slate-300">
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Capacity:</span>
                <span class="font-bold text-white">4 Hosts/Guests Round-Table</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Microphones:</span>
                <span class="font-bold text-white">4x Shure SM7B + Cloudlifters</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Mixer:</span>
                <span class="font-bold text-white">Rodecaster Pro II Console</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Half Day (4 hrs):</span>
                <span class="font-bold text-amber-400">₹12,000</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Full Shift (8 hrs):</span>
                <span class="font-bold text-amber-400">₹22,000</span>
              </div>
            </div>
          </div>
        </div>
        <div class="p-6 pt-0">
          <button onclick="openRentalModal('Studio B - Multi-Cam Podcast Suite')" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs py-3 rounded-xl shadow transition hover:brightness-110">
            Book Studio B Suite
          </button>
        </div>
      </div>

      <!-- Facility 3: Ad Space & Billboards -->
      <div id="digital-ads" data-category="ads" class="facility-card bg-brand-card rounded-2xl overflow-hidden border-2 border-slate-800 hover:border-amber-400/60 transition duration-300 group flex flex-col justify-between shadow-xl">
        <div>
          <div class="relative aspect-video overflow-hidden bg-slate-900">
            <img src="https://images.unsplash.com/photo-1542744094-3a31f272c490?auto=format&fit=crop&w=800&q=80" alt="Outdoor Billboard & Digital Ads" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            <div class="absolute top-3 left-3 bg-amber-400 text-slate-950 font-black text-xs px-2.5 py-1 rounded shadow">OUTDOOR &amp; WEB ADS</div>
            <div class="absolute bottom-3 right-3 bg-black/85 text-amber-300 font-mono text-sm px-3 py-1 rounded font-bold">Custom Contract</div>
          </div>
          <div class="p-6">
            <h3 class="text-xl font-bold text-white group-hover:text-amber-300 transition">Highway Billboards &amp; Web Ad Slots</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
              Prime outdoor hoardings along high-traffic Palwal-Sohna highway frontage, combined with digital banner impressions on sndmedia.in news portal.
            </p>
            
            <div class="mt-5 space-y-2.5 border-t border-slate-800 pt-4 text-xs text-slate-300">
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Highway Footfall:</span>
                <span class="font-bold text-white">45,000+ Vehicles / Day</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Billboard Size:</span>
                <span class="font-bold text-white">40ft x 20ft Unipole Gantry</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Digital Portal:</span>
                <span class="font-bold text-white">Top Leaderboard &amp; Sidebar</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-400">Monthly Contract:</span>
                <span class="font-bold text-amber-400">Contact For Quotation</span>
              </div>
            </div>
          </div>
        </div>
        <div class="p-6 pt-0">
          <button onclick="openRentalModal('Outdoor Campus Billboard & Ad Hoardings')" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs py-3 rounded-xl shadow transition hover:brightness-110">
            Request Media Space Quote
          </button>
        </div>
      </div>

    </div>
  </section>

  <!-- TECHNICAL INFRASTRUCTURE SPECS (8 Interactive Feature Tiles) -->
  <section id="specs" class="mb-16 bg-slate-950 rounded-3xl p-6 sm:p-10 border border-slate-800">
    <div class="max-w-3xl mb-8">
      <span class="text-xs font-black uppercase text-amber-400 tracking-wider">CAMPUS MEDIA INFRASTRUCTURE</span>
      <h3 class="text-2xl sm:text-3xl font-extrabold text-white mt-1">Broadcast-Grade Technical Specifications</h3>
      <p class="text-xs sm:text-sm text-slate-400 mt-2">
        Built to satisfy international broadcast, OTT feature film, and live uplink standards with zero downtime guarantees.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-xs">
      <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 hover:border-amber-400/40 transition">
        <i class="fa-solid fa-bolt text-amber-400 text-xl mb-3"></i>
        <h4 class="font-bold text-white text-sm">100% DG Power Backup</h4>
        <p class="text-slate-400 mt-1 leading-relaxed">Dual silent 50kVA generator sets with zero-millisecond online UPS cutover for uninterrupted multi-camera shoots.</p>
      </div>

      <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 hover:border-amber-400/40 transition">
        <i class="fa-solid fa-wifi text-amber-400 text-xl mb-3"></i>
        <h4 class="font-bold text-white text-sm">1 Gbps Symmetric Fiber</h4>
        <p class="text-slate-400 mt-1 leading-relaxed">Redundant dual-ISP gigabit lines dedicated for real-time live YouTube/OTT uplinks and fast cloud ingestion.</p>
      </div>

      <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 hover:border-amber-400/40 transition">
        <i class="fa-solid fa-couch text-amber-400 text-xl mb-3"></i>
        <h4 class="font-bold text-white text-sm">Green Rooms &amp; Makeup</h4>
        <p class="text-slate-400 mt-1 leading-relaxed">Furnished green rooms with lighted vanity mirrors, private washrooms, dressing areas, and high-speed Wi-Fi.</p>
      </div>

      <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 hover:border-amber-400/40 transition">
        <i class="fa-solid fa-truck-ramp-box text-amber-400 text-xl mb-3"></i>
        <h4 class="font-bold text-white text-sm">Crew Loading Bay &amp; Ramp</h4>
        <p class="text-slate-400 mt-1 leading-relaxed">Direct ground-floor ramp access for production vans, OB units, heavy flight cases, and secure vehicle parking.</p>
      </div>

      <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 hover:border-amber-400/40 transition">
        <i class="fa-solid fa-sliders text-amber-400 text-xl mb-3"></i>
        <h4 class="font-bold text-white text-sm">Master Control Room (MCR)</h4>
        <p class="text-slate-400 mt-1 leading-relaxed">Isolated control gallery overlooking the main studio floor with multi-view SDI monitoring and talkback inter-com.</p>
      </div>

      <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 hover:border-amber-400/40 transition">
        <i class="fa-solid fa-microphone-lines text-amber-400 text-xl mb-3"></i>
        <h4 class="font-bold text-white text-sm">Multi-Track Audio Console</h4>
        <p class="text-slate-400 mt-1 leading-relaxed">32-channel digital mixer, Shure SM7B broadcast dynamics, wireless lavs, and acoustic treated reflection filters.</p>
      </div>

      <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 hover:border-amber-400/40 transition">
        <i class="fa-solid fa-paint-roller text-amber-400 text-xl mb-3"></i>
        <h4 class="font-bold text-white text-sm">24ft Chroma Cyclorama</h4>
        <p class="text-slate-400 mt-1 leading-relaxed">Pre-lit curved seamless cyclorama green wall with high-CRI 97+ daylight lighting for clean real-time keying.</p>
      </div>

      <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 hover:border-amber-400/40 transition">
        <i class="fa-solid fa-shield-halved text-amber-400 text-xl mb-3"></i>
        <h4 class="font-bold text-white text-sm">Campus Security &amp; Access</h4>
        <p class="text-slate-400 mt-1 leading-relaxed">Gated campus security, 24x7 CCTV monitoring, and restricted access ensuring full privacy for celebrity and VIP shoots.</p>
      </div>
    </div>
  </section>

  <!-- INTERACTIVE FAQ ACCORDION -->
  <section class="mb-16 max-w-4xl mx-auto">
    <div class="text-center mb-8">
      <span class="text-xs font-black uppercase text-amber-400 tracking-wider">FREQUENT QUESTIONS</span>
      <h3 class="text-2xl sm:text-3xl font-extrabold text-white mt-1">Studio Space &amp; Rental FAQ</h3>
    </div>

    <div class="space-y-3 text-sm">
      <div class="bg-brand-card border border-slate-800 rounded-xl overflow-hidden">
        <button type="button" onclick="toggleFaq(1)" class="w-full text-left p-4 sm:p-5 flex items-center justify-between font-bold text-white hover:text-amber-300 transition">
          <span>Can we bring our own camera package, lenses, and crew?</span>
          <i class="fa-solid fa-chevron-down text-xs text-amber-400 transition-transform duration-300" id="faq-icon-1"></i>
        </button>
        <div id="faq-answer-1" class="hidden px-5 pb-5 text-xs text-slate-300 leading-relaxed border-t border-slate-800/60 pt-3">
          Yes, absolutely. Production crews are free to bring their own cameras, lighting fixtures, sound gear, and teleprompters. Our studio engineer is on-site throughout your session to assist with SDI video patch-bays, power tie-ins, and floor logistics.
        </div>
      </div>

      <div class="bg-brand-card border border-slate-800 rounded-xl overflow-hidden">
        <button type="button" onclick="toggleFaq(2)" class="w-full text-left p-4 sm:p-5 flex items-center justify-between font-bold text-white hover:text-amber-300 transition">
          <span>What is the acoustic rating and ambient noise isolation level?</span>
          <i class="fa-solid fa-chevron-down text-xs text-amber-400 transition-transform duration-300" id="faq-icon-2"></i>
        </button>
        <div id="faq-answer-2" class="hidden px-5 pb-5 text-xs text-slate-300 leading-relaxed border-t border-slate-800/60 pt-3">
          Both Studio A and Studio B are acoustically treated with double-stud floating walls, insulated acoustic doors, and decoupled HVAC silencer baffles, achieving a Noise Criterion rating of NC-25. This ensures pristine sync-sound recording without highway or ambient spill.
        </div>
      </div>

      <div class="bg-brand-card border border-slate-800 rounded-xl overflow-hidden">
        <button type="button" onclick="toggleFaq(3)" class="w-full text-left p-4 sm:p-5 flex items-center justify-between font-bold text-white hover:text-amber-300 transition">
          <span>How does the booking process and advance payment work?</span>
          <i class="fa-solid fa-chevron-down text-xs text-amber-400 transition-transform duration-300" id="faq-icon-3"></i>
        </button>
        <div id="faq-answer-3" class="hidden px-5 pb-5 text-xs text-slate-300 leading-relaxed border-t border-slate-800/60 pt-3">
          Submit your desired dates via the online estimator or booking modal. Our Space Desk will confirm availability within 2 hours. A 50% advance booking deposit locks your shift, with the remaining balance due upon completion of shoot wrap.
        </div>
      </div>

      <div class="bg-brand-card border border-slate-800 rounded-xl overflow-hidden">
        <button type="button" onclick="toggleFaq(4)" class="w-full text-left p-4 sm:p-5 flex items-center justify-between font-bold text-white hover:text-amber-300 transition">
          <span>Are night shifts and weekend 24-hour schedules permitted?</span>
          <i class="fa-solid fa-chevron-down text-xs text-amber-400 transition-transform duration-300" id="faq-icon-4"></i>
        </button>
        <div id="faq-answer-4" class="hidden px-5 pb-5 text-xs text-slate-300 leading-relaxed border-t border-slate-800/60 pt-3">
          Yes. The facility operates on a 24x7 basis. Overnight shoots, weekend continuous commercial shifts, and live broadcast marathons are fully supported with round-the-clock campus security, power backup, and catering coordination.
        </div>
      </div>
    </div>
  </section>

  <!-- CAMPUS LOCATION & BOOKING BANNER -->
  <section class="bg-brand-card rounded-3xl p-6 sm:p-10 border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-8 shadow-2xl">
    <div>
      <span class="text-xs font-black uppercase text-amber-400 tracking-wider">CAMPUS LOCATION</span>
      <h3 class="text-2xl font-bold text-white mt-1">Plan a Physical Campus Inspection</h3>
      <p class="text-xs sm:text-sm text-slate-300 mt-2 max-w-xl leading-relaxed">
        <?= sanitize_output($settings['hq_address'] ?? 'SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102') ?><br>
        Direct highway access from Delhi, Gurugram, Faridabad, and Greater Noida.
      </p>
      <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-slate-400">
        <span><i class="fa-solid fa-envelope text-amber-400 mr-1"></i> <?= sanitize_output($settings['email_space'] ?? 'space@sndmedia.in') ?></span>
        <span><i class="fa-solid fa-phone text-amber-400 mr-1"></i> <?= sanitize_output($settings['phone_studio'] ?? '+91-7206622711') ?></span>
      </div>
    </div>

    <div class="shrink-0 flex flex-col sm:flex-row gap-3">
      <button onclick="openRentalModal('Campus Inspection')" class="bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs sm:text-sm px-6 py-3.5 rounded-xl shadow-lg shadow-amber-500/20 transition hover:brightness-110">
        Book Facility Inspection
      </button>
      <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['phone_whatsapp'] ?? '917206622711') ?>" target="_blank" rel="noopener noreferrer" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm px-6 py-3.5 rounded-xl transition flex items-center justify-center gap-2 shadow">
        <i class="fa-brands fa-whatsapp text-lg"></i> Chat on WhatsApp
      </a>
    </div>
  </section>

  <!-- STICKY FLOATING QUICK-CONNECT CONCIERGE PILL -->
  <aside aria-label="Studio desk quick contact" class="fixed bottom-6 right-6 z-40 flex items-center gap-3">
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['phone_whatsapp'] ?? '917206622711') ?>" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-2.5 bg-slate-900/95 hover:bg-slate-900 border border-emerald-500/50 hover:border-emerald-400 text-white text-xs font-bold py-2.5 px-4 rounded-full shadow-2xl backdrop-blur transition-all duration-300 hover:scale-105">
      <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
      <i class="fa-brands fa-whatsapp text-emerald-400 text-base"></i>
      <span class="hidden sm:inline">Studio Space Desk:</span>
      <span class="text-amber-300 font-mono">+91-7206622711</span>
    </a>
  </aside>

</main>

<!-- JAVASCRIPT: INTERACTIVE ESTIMATOR & FILTER LOGIC -->
<script>
const estimatorState = {
  floor: 'studio_a',
  duration: 'half_day'
};

const estimatorData = {
  studio_a: {
    name: 'Studio Floor A (Chroma & Newsroom)',
    rates: {
      half_day: '₹26,000',
      full_day: '₹48,000',
      multi_day: '₹1,35,000'
    },
    inclusions: [
      '2,200 sq.ft stage with 14ft clearance height',
      'Chroma Green Cyclorama + Bi-Color Grid Lighting',
      '100% DG Power Backup & 1 Gbps Gigabit Fiber',
      'Master Control Room (MCR) & Private Green Room'
    ]
  },
  studio_b: {
    name: 'Studio B (Multi-Cam Podcast Suite)',
    rates: {
      half_day: '₹12,000',
      full_day: '₹22,000',
      multi_day: '₹58,000'
    },
    inclusions: [
      '4-Host Acoustic Round Table with NC-25 rating',
      '4x Shure SM7B Microphones + Cloudlifters',
      'Rodecaster Pro II Console + 4K Multi-angle capture',
      'Instant raw WAV/MP4 multi-track offload'
    ]
  },
  billboard: {
    name: 'Highway Billboard & Web Ad Inventory',
    rates: {
      half_day: 'Quote on Request',
      full_day: 'Custom Shift',
      multi_day: 'Monthly Contract'
    },
    inclusions: [
      '40ft x 20ft Highway Unipole (45,000+ daily footfall)',
      'High-impact LED floodlit night illumination',
      'Top Leaderboard & Sidebar on sndmedia.in portal',
      'Detailed monthly audit & impression reporting'
    ]
  }
};

function selectEstimatorFloor(floorKey) {
  estimatorState.floor = floorKey;
  ['studio_a', 'studio_b', 'billboard'].forEach(k => {
    const btn = document.getElementById('btn-est-' + k);
    if (!btn) return;
    if (k === floorKey) {
      btn.className = 'py-2 px-2 rounded-lg bg-amber-500 text-slate-950 font-bold shadow transition text-center truncate';
    } else {
      btn.className = 'py-2 px-2 rounded-lg text-slate-400 hover:text-white transition text-center truncate';
    }
  });
  updateEstimatorView();
}

function selectEstimatorDuration(durKey) {
  estimatorState.duration = durKey;
  const durMap = {
    half_day: 'btn-dur-half',
    full_day: 'btn-dur-full',
    multi_day: 'btn-dur-multi'
  };
  Object.keys(durMap).forEach(k => {
    const btn = document.getElementById(durMap[k]);
    if (!btn) return;
    if (k === durKey) {
      btn.className = 'py-2 px-1 text-center text-xs font-semibold rounded-lg bg-slate-800 border border-amber-400/50 text-amber-300';
    } else {
      btn.className = 'py-2 px-1 text-center text-xs font-semibold rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white';
    }
  });
  updateEstimatorView();
}

function updateEstimatorView() {
  const currentFloor = estimatorData[estimatorState.floor];
  const priceEl = document.getElementById('estimator-price');
  const badgeEl = document.getElementById('estimator-badge');
  const incEl = document.getElementById('estimator-inclusions');

  if (priceEl) {
    priceEl.textContent = currentFloor.rates[estimatorState.duration] || '₹26,000';
  }
  if (badgeEl) {
    badgeEl.innerHTML = `<span class="inline-block text-[10px] font-bold uppercase bg-amber-400/20 text-amber-300 px-2.5 py-1 rounded-full border border-amber-400/30">${currentFloor.name}</span>`;
  }
  if (incEl) {
    incEl.innerHTML = currentFloor.inclusions.map(item => `
      <li class="flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
        <span>${item}</span>
      </li>
    `).join('');
  }
}

function lockEstimatedBooking() {
  const currentFloor = estimatorData[estimatorState.floor];
  const durLabel = estimatorState.duration === 'half_day' ? 'Half Day (4 hrs)' : (estimatorState.duration === 'full_day' ? 'Full Day (8 hrs)' : '3-Day Block');
  const bookingTitle = `${currentFloor.name} — ${durLabel}`;
  openRentalModal(bookingTitle);
}

function filterFacilities(type) {
  const cards = document.querySelectorAll('.facility-card');
  const buttons = {
    all: document.getElementById('filter-fac-all'),
    video: document.getElementById('filter-fac-video'),
    audio: document.getElementById('filter-fac-audio'),
    ads: document.getElementById('filter-fac-ads')
  };

  Object.keys(buttons).forEach(k => {
    if (!buttons[k]) return;
    if (k === type) {
      buttons[k].className = 'px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-400 text-slate-950 transition';
    } else {
      buttons[k].className = 'px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-800 text-slate-300 hover:text-white transition';
    }
  });

  cards.forEach(card => {
    const cat = card.getAttribute('data-category');
    if (type === 'all' || cat === type) {
      card.style.display = 'flex';
      card.classList.add('animate-fadeIn');
    } else {
      card.style.display = 'none';
    }
  });
}

function toggleFaq(id) {
  const answer = document.getElementById('faq-answer-' + id);
  const icon = document.getElementById('faq-icon-' + id);
  if (!answer) return;

  if (answer.classList.contains('hidden')) {
    answer.classList.remove('hidden');
    if (icon) icon.classList.add('rotate-180');
  } else {
    answer.classList.add('hidden');
    if (icon) icon.classList.remove('rotate-180');
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
