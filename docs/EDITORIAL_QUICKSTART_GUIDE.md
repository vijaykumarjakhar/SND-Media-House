# SND Media — Editorial Staff Quick-Start Guide (Prime Phase 1)
**Audience:** Editors & Newsroom Reporters  
**Portal:** `sndmedia.in/admin/`  
**Brand:** SND Media (*"Where Students Lead Media"*)  
**Editorial Policy:** 100% Human-Verified Journalism • Zero Unverified Generative AI Stories  

---

## 1. Access & Authentication
1. Navigate to: `https://sndmedia.in/admin/login.php` (or local: `http://localhost/Websites/SND-Media-House/admin/login.php`).
2. Log in using your assigned Editorial credentials (`editor` or `reporter`).
3. Sessions automatically guard your privileges:
   - **Editor:** Can publish, edit, delete articles, manage breaking ticker, and curate YouTube bulletins.
   - **Reporter:** Can draft and edit their own articles.

---

## 2. Publishing a News Story (`admin/articles.php`)
1. Click **News Articles** in the admin sidebar navigation.
2. Click the red **+ New Story** button at the top right.
3. Fill in the core newsroom fields:
   - **Headline / Title:** Clear, high-impact broadcast headline. (Slug is automatically generated).
   - **Category:** Select from `National`, `Business`, `Tech`, `Energy`, `Exclusive`, or `World`.
   - **Subcategory / Beat:** Specific subject (e.g. *Infrastructure*, *Taxation*, *AI & Robotics*).
   - **Verification Badge:** Choose from:
     - `verified_story` (Verified Story)
     - `breaking` (Breaking News)
     - `special_investigation` (Special Investigation)
     - `on_the_ground` (Ground Report)
     - `exclusive` (Exclusive Desk)
   - **Read Time:** e.g. `4 MIN READ`.
   - **Hero Lead Toggle (`is_hero_lead`):** Check this box if this article should take over the #1 primary 16:9 slot on the homepage. Only one article occupies this slot at any time.
   - **Featured Image URL:** Enter a high-resolution 16:9 image link or upload a photo directly.
   - **YouTube Video ID (Optional):** Enter an 11-character YouTube video ID (e.g., `25tvHivopPE`) or full YouTube URL to embed a broadcast video directly in the article header.

### Crucial: The 3-Bullet Editorial Highlights
Every SND Media article must feature 3 verified human takeaways displayed in the reader drawer:
- **Highlight Bullet 1:** The primary development or key quantifiable fact.
- **Highlight Bullet 2:** Strategic context, industry impact, or policy implication.
- **Highlight Bullet 3:** Ground reality, next steps, or timeline.

*Note: In accordance with SND Media Editorial Ethics, all bullets must be verified by editorial staff. Do not use unreviewed generative AI text.*

4. **Story Body:** Format using paragraphs, quotes, and subheadings in the story content box.
5. **Status:** Select `Draft` for ongoing reviews or `Published` for immediate broadcast.
6. Click **Save Article**.

---

## 3. Managing the Breaking News Ticker (`admin/ticker.php`)
The breaking ticker runs horizontally across the top of every public page on `sndmedia.in`:
1. Click **Breaking Ticker** in the sidebar.
2. **Add Ticker Alert:**
   - **Headline Text:** e.g. `PARLIAMENT: Union Cabinet passes Digital Industrial Corridor Framework`.
   - **Category Tag:** e.g. `NATIONAL`, `DEVELOPING`, `EXCLUSIVE`.
   - **Link (Optional):** Link directly to an article (`article.php?slug=...`) or external reference.
   - **Sort Priority:** Lower numbers appear first (e.g. `1`, `2`).
   - Click **Save Alert**.
3. **Quick Toggles:**
   - Click the **Active / Paused** toggle button beside any alert to instantly publish or silence it on the live site without deleting it.
   - Click the red trash can icon to permanently remove resolved alerts.

---

## 4. Curating the YouTube Broadcast Hub (`admin/youtube.php`)
SND Media syncs with official YouTube channel **@sndschoolmedia** (`https://www.youtube.com/@sndschoolmedia`):
1. Click **YouTube Hub** in the sidebar.
2. **Add New Video Broadcast:**
   - **YouTube Video URL or ID:** Paste the video link (e.g., `https://www.youtube.com/watch?v=25tvHivopPE`) or the 11-character video ID.
   - The CMS automatically extracts the ID and fetches the high-definition thumbnail from YouTube CDN.
   - **Title:** The display title for the video player and playlist.
   - **Duration:** e.g. `14:20`.
   - **Views Label:** e.g. `12K views` or `Ground Coverage`.
   - **Main Featured:** Check this if this video should be the large active player on the homepage.
3. **Change Main Featured Video:**
   - Click the **Star icon ("Set Featured")** next to any video in the list to make it the default player on `index.php`.

---

## 5. Editorial Ethics & Compliance
- **Corrections Policy:** If a correction is required on a published story, update the content and append an `[Editorial Correction: Date - Details]` notice at the foot of the article.
- **Urgent Retraction:** Switch article status from `Published` to `Draft` or `Archived` to remove it from public indexing immediately.
