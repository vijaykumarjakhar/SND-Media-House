<?php
/**
 * SND Media — Commercial Studio & Space Rental Modal Component
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/settings.php';

$settings = get_all_settings();
?>
<!-- Commercial Space Rental Enquiry Modal -->
<div id="rental-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
  <div class="bg-brand-card border border-slate-700 w-full max-w-lg rounded-2xl p-6 shadow-2xl relative max-h-[92vh] overflow-y-auto custom-scrollbar">
    
    <!-- Close Button -->
    <button onclick="closeRentalModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white p-2 text-lg focus:outline-none" aria-label="Close Modal">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <!-- Modal Header -->
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-lg">
        <i class="fa-solid fa-building-circle-arrow-right"></i>
      </div>
      <div>
        <h4 class="font-bold text-lg text-white" id="modal-title">Studio Space & Media Rental</h4>
        <p class="text-xs text-slate-400">SND Media Commercial Leasing Desk &bull; Palwal Campus</p>
      </div>
    </div>

    <!-- Alert Message Container -->
    <div id="modal-alert" class="hidden mb-4 p-3 rounded-lg text-xs font-semibold"></div>

    <!-- Enquiry Form -->
    <form id="rental-enquiry-form" onsubmit="submitRentalForm(event)" class="space-y-3.5 text-xs">
      <?= csrf_field() ?>
      
      <!-- Honeypot Field (Spam prevention) -->
      <div class="hidden" aria-hidden="true">
        <label for="website_url_hp">Leave this empty</label>
        <input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label for="modal-client-name" class="block text-slate-300 font-semibold mb-1">Your Full Name <span class="text-brand-red">*</span></label>
          <input id="modal-client-name" name="client_name" type="text" required minlength="2" placeholder="e.g. Rahul Verma" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white focus:outline-none focus:border-amber-400">
        </div>
        <div>
          <label for="modal-company-name" class="block text-slate-300 font-semibold mb-1">Company / Organization</label>
          <input id="modal-company-name" name="company_name" type="text" placeholder="e.g. Creative Media Works (Optional)" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white focus:outline-none focus:border-amber-400">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label for="modal-email" class="block text-slate-300 font-semibold mb-1">Email Address <span class="text-brand-red">*</span></label>
          <input id="modal-email" name="email" type="email" required placeholder="contact@company.com" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white focus:outline-none focus:border-amber-400">
        </div>
        <div>
          <label for="modal-phone" class="block text-slate-300 font-semibold mb-1">Phone / WhatsApp <span class="text-brand-red">*</span></label>
          <input id="modal-phone" name="phone" type="tel" required placeholder="+91-7206622711" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white focus:outline-none focus:border-amber-400">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label for="modal-facility" class="block text-slate-300 font-semibold mb-1">Facility Required <span class="text-brand-red">*</span></label>
          <select id="modal-facility" name="facility_needed" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            <option value="Studio A - Newsroom Stage (2200 sq ft)">Studio A - Newsroom Stage (2200 sq ft)</option>
            <option value="Studio B - Multi-Cam Podcast Suite">Studio B - Multi-Cam Podcast Suite</option>
            <option value="Outdoor Campus Billboard & Ad Hoardings">Outdoor Campus Billboard & Ad Hoardings</option>
            <option value="Digital Website Ad Banners & Sponsorships">Digital Website Ad Banners & Sponsorships</option>
            <option value="OB Van & Live Uplink Broadcast Kit">OB Van & Live Uplink Broadcast Kit</option>
            <option value="Full Media Campus Floor Inspection">Full Media Campus Floor Inspection</option>
          </select>
        </div>
        <div>
          <label for="modal-date" class="block text-slate-300 font-semibold mb-1">Estimated Shoot / Start Date</label>
          <input id="modal-date" name="preferred_date" type="date" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white focus:outline-none focus:border-amber-400">
        </div>
      </div>

      <div>
        <label for="modal-duration" class="block text-slate-300 font-semibold mb-1">Estimated Slot Duration</label>
        <select id="modal-duration" name="slot_duration" class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white focus:outline-none focus:border-amber-400">
          <option value="Half Day (4 Hours)">Half Day (4 Hours)</option>
          <option value="Full Day (8-10 Hours)">Full Day (8-10 Hours)</option>
          <option value="Multi-Day Production Shoot">Multi-Day Production Shoot</option>
          <option value="Hourly Podcast Session (2-3 Hours)">Hourly Podcast Session (2-3 Hours)</option>
          <option value="Monthly Long-Term Commercial Lease">Monthly Long-Term Commercial Lease</option>
        </select>
      </div>

      <div>
        <label for="modal-notes" class="block text-slate-300 font-semibold mb-1">Project Scope & Equipment Needs</label>
        <textarea id="modal-notes" name="notes" rows="3" placeholder="Tell us about your production: camera crew needs, chroma keying, live stream requirements..." class="w-full bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white focus:outline-none focus:border-amber-400"></textarea>
      </div>

      <div class="pt-2">
        <button id="modal-submit-btn" type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold py-2.5 rounded-lg shadow-lg shadow-amber-500/20 transition transform active:scale-98 flex items-center justify-center gap-2 text-xs">
          <i class="fa-solid fa-paper-plane"></i>
          <span id="modal-submit-text">Submit Commercial Rental Enquiry</span>
        </button>
      </div>

      <div class="text-[11px] text-slate-500 text-center flex items-center justify-center gap-2 pt-1">
        <i class="fa-solid fa-lock text-amber-400"></i>
        <span>Direct SLA Response &bull; Instant Confirmation via WhatsApp</span>
      </div>
    </form>

    <!-- Success Container with Direct WhatsApp Button -->
    <div id="modal-success-box" class="hidden text-center py-6 space-y-4">
      <div class="w-14 h-14 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto text-2xl border border-emerald-500/40">
        <i class="fa-solid fa-circle-check"></i>
      </div>
      <div>
        <h4 class="text-base font-bold text-white">Enquiry Received!</h4>
        <p class="text-xs text-slate-300 mt-1 max-w-sm mx-auto" id="modal-success-msg">
          Our Studio & Space Manager has received your request. We will contact you within 2 hours.
        </p>
      </div>
      <div class="pt-2 flex flex-col sm:flex-row gap-2.5 justify-center">
        <a id="modal-whatsapp-direct" href="#" target="_blank" rel="noopener noreferrer" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-4 py-2.5 rounded-lg transition inline-flex items-center justify-center gap-2 shadow">
          <i class="fa-brands fa-whatsapp text-base"></i> Open Instant WhatsApp Chat
        </a>
        <button onclick="closeRentalModal()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs px-4 py-2.5 rounded-lg transition">
          Close Window
        </button>
      </div>
    </div>

  </div>
</div>

<script>
  function openRentalModal(facilityName) {
    const modal = document.getElementById('rental-modal');
    if (!modal) return;
    
    const facilitySelect = document.getElementById('modal-facility');
    if (facilitySelect && facilityName) {
      for (let i = 0; i < facilitySelect.options.length; i++) {
        if (facilitySelect.options[i].text.toLowerCase().includes(facilityName.toLowerCase())) {
          facilitySelect.selectedIndex = i;
          break;
        }
      }
    }
    
    // Reset view
    const form = document.getElementById('rental-enquiry-form');
    const successBox = document.getElementById('modal-success-box');
    const alertBox = document.getElementById('modal-alert');
    if (form) form.classList.remove('hidden');
    if (successBox) successBox.classList.add('hidden');
    if (alertBox) {
      alertBox.classList.add('hidden');
      alertBox.textContent = '';
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }

  function closeRentalModal() {
    const modal = document.getElementById('rental-modal');
    if (modal) {
      modal.classList.add('hidden');
      document.body.style.overflow = '';
    }
  }

  // Close modal on Escape
  window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeRentalModal();
    }
  });

  // Close when clicking modal backdrop
  document.getElementById('rental-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      closeRentalModal();
    }
  });

  async function submitRentalForm(e) {
    e.preventDefault();
    const form = e.target;
    const submitBtn = document.getElementById('modal-submit-btn');
    const submitText = document.getElementById('modal-submit-text');
    const alertBox = document.getElementById('modal-alert');
    const successBox = document.getElementById('modal-success-box');
    const successMsg = document.getElementById('modal-success-msg');
    const whatsappLink = document.getElementById('modal-whatsapp-direct');

    const formData = new FormData(form);

    submitBtn.disabled = true;
    submitText.textContent = 'Submitting Enquiry...';
    if (alertBox) alertBox.classList.add('hidden');

    try {
      const response = await fetch('api/submit_rental.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.status === 'success') {
        form.classList.add('hidden');
        if (successBox) successBox.classList.remove('hidden');
        if (successMsg) successMsg.textContent = data.message || 'Your studio enquiry has been registered.';
        if (whatsappLink && data.whatsapp_url) {
          whatsappLink.href = data.whatsapp_url;
        }
        form.reset();
      } else {
        if (alertBox) {
          alertBox.className = 'mb-4 p-3 rounded-lg text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30';
          alertBox.textContent = data.message || 'Could not submit enquiry. Please try again.';
          alertBox.classList.remove('hidden');
        }
      }
    } catch (err) {
      if (alertBox) {
        alertBox.className = 'mb-4 p-3 rounded-lg text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30';
        alertBox.textContent = 'Network error or server unreachable. Please reach out to us at +91-7206622711.';
        alertBox.classList.remove('hidden');
      }
    } finally {
      submitBtn.disabled = false;
      submitText.textContent = 'Submit Commercial Rental Enquiry';
    }
  }
</script>
