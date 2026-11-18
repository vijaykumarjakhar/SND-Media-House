# SND Media — Commercial Space & Studio CRM Guide (Prime Phase 1)
**Audience:** Space Managers, Commercial Production Desk & Sales Executives  
**Portal:** `sndmedia.in/admin/leads.php` & `admin/facilities.php`  
**Campus HQ:** SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102  
**Direct Space Contact:** `+91-7206622711` • `space@sndmedia.in`  

---

## 1. Overview of Commercial Space Operations
SND Media monetizes production infrastructure across Delhi NCR and South Haryana:
1. **Studio A (Chroma & Newsroom Stage):** 3,200 sq.ft acoustically insulated multi-camera stage.
2. **Studio B (Podcast & Voice Suite):** 4-mic Shure SM7B broadcast recording booth.
3. **Campus Highway Billboards & Digital Screens:** Palwal-Sohna Road high-visibility commercial inventory.
4. **Digital Website Ad Banners:** Header leaderboard and sidebar placements on `sndmedia.in`.

---

## 2. Managing Inbound Studio Enquiries (`admin/leads.php`)
Every time a production agency, content creator, or brand submits the rental modal on `sndmedia.in` or `studio-rental.php`, a record is logged instantly in the CRM.

### Viewing & Filtering Leads
1. Navigate to **Studio Leads** in the admin sidebar.
2. View the table columns:
   - **Client & Company:** Name and production agency.
   - **Contact Details:** Email and clickable telephone number (`tel:+91...`).
   - **Facility Needed:** e.g., *Studio A - Newsroom Stage*, *Podcast Suite*, or *Billboard*.
   - **Slot & Date:** Target date requested and duration (Half Day, Full Day, Multi-Day).
   - **Status Badge:** Live lifecycle status.
3. Use the **Filter Bar** at the top:
   - Filter by **Status** (`New`, `Contacted`, `Negotiating`, `Booked`, `Archived`, `Rejected`).
   - Filter by **Facility**.
   - Filter by **Date Range** (From - To).
   - Click **Apply Filter**.

### Updating Status & Logging Notes
1. **Change Status:** Change the dropdown directly on the row. The change is saved via AJAX to the database without reloading the page.
2. **WhatsApp Direct Connect:** Click the green **WhatsApp** icon next to the client's phone number to open a pre-filled chat with the client from your phone or WhatsApp Web.
3. **Internal Notes:** Click **Notes / Expand** to reveal the client's original production brief and type internal follow-up notes (e.g., *"Offered 10% discount for 3-day booking"*). Click **Save Note**.

### Exporting Leads
- Click the **Export CSV** button on the top right.
- Downloads a clean spreadsheet (`leads_YYYY-MM-DD.csv`) matching current filters, suitable for accounting or import into Google Sheets/Excel.

---

## 3. Managing Studio Rates & Specifications (`admin/facilities.php`)
1. Click **Studio Facilities** in the admin sidebar.
2. Click **Edit** next to any facility (or **+ Add Facility**):
   - **Facility Name & Category:** e.g., *Studio A (Chroma & Newsroom)* / `production_studio`.
   - **Daily Commercial Rate:** e.g., `₹35,000 / Day` (Displayed prominently on the public pricing tables).
   - **Floor Area:** e.g. `3,200` sq.ft.
   - **Badge Label:** e.g., `FLAGSHIP STAGE` or `TOP RATED`.
   - **Specs Builder:** Add or remove technical specification bullet points (e.g. *Blackmagic URSA 4K Cameras*, *Chroma Green Cyclorama 24ft*, *Grid Lighting 5600K*).
   - **Active Toggle:** Toggle facility visibility on the public rental showcase.
3. Click **Save Facility**. All updates reflect immediately on `studio-rental.php` and the homepage commercial strip.
