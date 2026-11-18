import os
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE

def create_signoff_deck():
    prs = Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_slide_layout = prs.slide_layouts[6]

    # Brand Colors
    C_BG_DARK = RGBColor(8, 13, 26)       # #080d1a
    C_CARD_BG = RGBColor(19, 28, 49)      # #131c31
    C_CARD_BORDER = RGBColor(37, 51, 80)  # #253350
    C_RED = RGBColor(225, 29, 72)         # #e11d48
    C_CRIMSON = RGBColor(190, 18, 60)     # #be123c
    C_GOLD = RGBColor(245, 158, 11)       # #f59e0b
    C_TEXT_WHITE = RGBColor(255, 255, 255)
    C_TEXT_MUTED = RGBColor(148, 163, 184)# #94a3b8
    C_TEXT_DIM = RGBColor(100, 116, 139)  # #64748b
    C_GREEN = RGBColor(16, 185, 129)      # #10b981
    C_BLUE = RGBColor(59, 130, 246)       # #3b82f6

    def add_base_slide(title, category="SND MEDIA HOUSE — PHASE 1 CLIENT SIGNOFF", slide_num=None):
        slide = prs.slides.add_slide(blank_slide_layout)
        
        # Background
        bg = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(13.333), Inches(7.5))
        bg.fill.solid()
        bg.fill.fore_color.rgb = C_BG_DARK
        bg.line.fill.background()

        # Top Red Accent Line
        top_accent = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(13.333), Inches(0.08))
        top_accent.fill.solid()
        top_accent.fill.fore_color.rgb = C_RED
        top_accent.line.fill.background()

        # Category / Breadcrumb
        cat_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.4), Inches(10), Inches(0.3))
        tf = cat_box.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = category.upper()
        p.font.size = Pt(10)
        p.font.bold = True
        p.font.color.rgb = C_GOLD
        p.font.name = "Arial"

        # Slide Title
        title_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.7), Inches(11.5), Inches(0.6))
        tf = title_box.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = title
        p.font.size = Pt(22)
        p.font.bold = True
        p.font.color.rgb = C_TEXT_WHITE
        p.font.name = "Arial"

        # Footer
        footer = slide.shapes.add_textbox(Inches(0.8), Inches(7.0), Inches(11.7), Inches(0.4))
        tf = footer.text_frame
        p = tf.paragraphs[0]
        num_str = f"Slide {slide_num} of 12" if slide_num else ""
        p.text = f"SND Media House Digital Platform | Prime Edition Phase 1 Scope & Architecture Approval   {num_str}"
        p.font.size = Pt(9)
        p.font.color.rgb = C_TEXT_DIM
        p.font.name = "Arial"

        return slide

    def create_card(slide, left, top, width, height, title=None, border_color=C_CARD_BORDER, bg_color=C_CARD_BG):
        card = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(left), Inches(top), Inches(width), Inches(height))
        card.fill.solid()
        card.fill.fore_color.rgb = bg_color
        card.line.color.rgb = border_color
        card.line.width = Pt(1.5)
        
        if title:
            tb = slide.shapes.add_textbox(Inches(left + 0.25), Inches(top + 0.2), Inches(width - 0.5), Inches(0.4))
            tf = tb.text_frame
            tf.word_wrap = True
            p = tf.paragraphs[0]
            p.text = title
            p.font.size = Pt(13)
            p.font.bold = True
            p.font.color.rgb = C_TEXT_WHITE
            p.font.name = "Arial"
        return card

    # ==================== SLIDE 1: COVER SLIDE ====================
    s1 = prs.slides.add_slide(blank_slide_layout)
    bg = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(13.333), Inches(7.5))
    bg.fill.solid()
    bg.fill.fore_color.rgb = C_BG_DARK
    bg.line.fill.background()

    # Red & Amber Accent Bars
    b1 = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.8), Inches(1.2), Inches(0.12), Inches(5.0))
    b1.fill.solid()
    b1.fill.fore_color.rgb = C_RED
    b1.line.fill.background()

    badge = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.2), Inches(1.4), Inches(3.8), Inches(0.45))
    badge.fill.solid()
    badge.fill.fore_color.rgb = RGBColor(40, 15, 25)
    badge.line.color.rgb = C_RED
    tb = s1.shapes.add_textbox(Inches(1.2), Inches(1.45), Inches(3.8), Inches(0.4))
    p = tb.text_frame.paragraphs[0]
    p.alignment = PP_ALIGN.CENTER
    p.text = "CLIENT SIGNOFF & APPROVAL DECK"
    p.font.bold = True
    p.font.size = Pt(11)
    p.font.color.rgb = C_RED

    tb = s1.shapes.add_textbox(Inches(1.2), Inches(2.1), Inches(11), Inches(1.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "SND Media House Digital Platform"
    p.font.size = Pt(36)
    p.font.bold = True
    p.font.color.rgb = C_TEXT_WHITE
    p.font.name = "Arial"

    p2 = tf.add_paragraph()
    p2.text = "Phase 1 Scope, Architecture & Implementation Approval (Prime Edition)"
    p2.font.size = Pt(18)
    p2.font.color.rgb = C_GOLD
    p2.font.name = "Arial"

    # Meta cards on cover
    create_card(s1, 1.2, 4.3, 3.4, 1.8, "Client / Stakeholder")
    tb = s1.shapes.add_textbox(Inches(1.45), Inches(4.9), Inches(3.0), Inches(1.0))
    p = tb.text_frame.paragraphs[0]
    p.text = "SND Media House (sndmedia.in)\nSND Public School Campus, Palwal, Haryana\nCommercial Leasing Division"
    p.font.size = Pt(11)
    p.font.color.rgb = C_TEXT_MUTED

    create_card(s1, 4.9, 4.3, 3.4, 1.8, "Deliverable Scope")
    tb = s1.shapes.add_textbox(Inches(5.15), Inches(4.9), Inches(3.0), Inches(1.0))
    p = tb.text_frame.paragraphs[0]
    p.text = "Concept 01 (SND Prime Broadcast)\nPHP 8.x + MySQL PDO Architecture\nRole-Based CMS & Lead CRM"
    p.font.size = Pt(11)
    p.font.color.rgb = C_TEXT_MUTED

    create_card(s1, 8.6, 4.3, 3.4, 1.8, "Sign-off Milestone")
    tb = s1.shapes.add_textbox(Inches(8.85), Inches(4.9), Inches(3.0), Inches(1.0))
    p = tb.text_frame.paragraphs[0]
    p.text = "Milestone: Phase 1 Architecture Approval\nAI Features: Explicitly Deferred to Phase 2\nTarget Go-Live: Production Ready"
    p.font.size = Pt(11)
    p.font.color.rgb = C_TEXT_MUTED

    # ==================== SLIDE 2: EXECUTIVE VISION & 3 PILLARS ====================
    s2 = add_base_slide("Executive Vision: The 3 Core Business Pillars", "1. Strategic Purpose", 2)
    
    col_w = 3.65
    # Pillar 1
    create_card(s2, 0.8, 1.6, col_w, 4.9, "Pillar 1: 24/7 Digital Newsroom", C_RED)
    tb = s2.shapes.add_textbox(Inches(1.05), Inches(2.2), Inches(col_w - 0.5), Inches(4.0))
    tf = tb.text_frame
    tf.word_wrap = True
    bullets = [
        ("Authoritative Broadcast Identity:", "Urgent, trusted news reporting across National, Business, Tech, and Energy domains."),
        ("24x7 Breaking Ticker Marquee:", "Full-width animated breaking alerts with pause-on-hover and urgency classification."),
        ("Human Editorial Verification:", "3-bullet Key Highlights per article curated by editorial staff for maximum credibility."),
        ("Audience Engagement:", "Social virality via WhatsApp, X, Facebook, and deep category tagging.")
    ]
    for b_title, b_desc in bullets:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {b_title} "
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"  {b_desc}\n"
        p2.font.size = Pt(10)
        p2.font.color.rgb = C_TEXT_MUTED

    # Pillar 2
    create_card(s2, 4.8, 1.6, col_w, 4.9, "Pillar 2: YouTube TV Broadcast Hub", C_BLUE)
    tb = s2.shapes.add_textbox(Inches(5.05), Inches(2.2), Inches(col_w - 0.5), Inches(4.0))
    tf = tb.text_frame
    tf.word_wrap = True
    bullets = [
        ("Direct Channel Synergy:", "Bi-directional audience growth with official @sndschoolmedia YouTube channel (3.36K Subs • 666+ Videos)."),
        ("Embedded 16:9 Live Player:", "Seamless playback with live indicator without high bandwidth hosting bills."),
        ("Instant Video Switcher:", "Users watch debate episodes & bulletins directly on the page without full reloads."),
        ("Zero-Cost API Architecture:", "Pasting standard YouTube URLs fetches thumbnails automatically without paid Cloud APIs.")
    ]
    for b_title, b_desc in bullets:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {b_title} "
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"  {b_desc}\n"
        p2.font.size = Pt(10)
        p2.font.color.rgb = C_TEXT_MUTED

    # Pillar 3
    create_card(s2, 8.8, 1.6, col_w, 4.9, "Pillar 3: Studio & Ad Space Monetization", C_GOLD)
    tb = s2.shapes.add_textbox(Inches(9.05), Inches(2.2), Inches(col_w - 0.5), Inches(4.0))
    tf = tb.text_frame
    tf.word_wrap = True
    bullets = [
        ("Commercial Real Estate USP:", "Unlike typical news sites, monetizes SND's physical studio infrastructure at SND Campus, Palwal (Haryana) & Delhi NCR."),
        ("Showcase Studio Inventory:", "Studio A (2,200 sq.ft stage @ ₹35k/day) & Studio B (Shure 4K Podcast Suite @ ₹3.5k/hr)."),
        ("Digital Ad Inventory:", "Dedicated leaderboard and in-feed sponsored positions with transparent reach figures."),
        ("Immediate Deal Flow:", "Captures leads into database, alerts leasing desk via email, and triggers instant WhatsApp chats.")
    ]
    for b_title, b_desc in bullets:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {b_title} "
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"  {b_desc}\n"
        p2.font.size = Pt(10)
        p2.font.color.rgb = C_TEXT_MUTED

    # ==================== SLIDE 3: CONCEPT 1 SELECTION ====================
    s3 = add_base_slide("Design Foundation: Concept 01 (SND Prime Broadcast) Approved", "2. Visual Identity & Base Template", 3)

    create_card(s3, 0.8, 1.6, 7.5, 4.9, "Why Concept 01 (SND Prime) was Selected")
    tb = s3.shapes.add_textbox(Inches(1.05), Inches(2.2), Inches(7.0), Inches(4.1))
    tf = tb.text_frame
    tf.word_wrap = True
    reasons = [
        ("National Television Authority:", "Engineered to reflect prime-time national broadcast standards (comparable to NDTV, India Today, Times Now) with bold, urgent typography and live indicators."),
        ("Dark Slate Elegance with Red & Gold Accents:", "Dominant `#080d1a` background provides high-contrast legibility, reduces eye fatigue, and elevates commercial amber callouts."),
        ("Unified News & B2B Commercial Layout:", "Integrates studio rental widgets directly alongside breaking feeds without disrupting journalism integrity."),
        ("Flawless Responsiveness:", "Engineered mobile-first with sticky glassmorphism header, smooth marquee ticker, and touch-optimized navigation."),
        ("Strict Brand Typography:", "'Outfit' for bold punchy headlines, 'Inter' for reading clarity, and 'Space Grotesk' for live market indices.")
    ]
    for r_title, r_desc in reasons:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"✓ {r_title} "
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"   {r_desc}\n"
        p2.font.size = Pt(10)
        p2.font.color.rgb = C_TEXT_MUTED

    # Evaluation comparison card
    create_card(s3, 8.6, 1.6, 3.9, 4.9, "Template Evaluation Matrix", C_RED)
    tb = s3.shapes.add_textbox(Inches(8.85), Inches(2.2), Inches(3.4), Inches(4.1))
    tf = tb.text_frame
    tf.word_wrap = True
    comp = [
        ("Concept 01 (SND Prime Broadcast)", "STATUS: APPROVED (100% Fit)\n• Authority: 10/10\n• Commercial Studio Hooks: 10/10\n• YouTube Broadcast Sync: 10/10\n• National Newsroom Feel: Yes"),
        ("Concept 02 (Neo Digital)", "STATUS: ARCHIVED\n• Modern tech feel, but lacked broadcast weight and national authority."),
        ("Concept 03 (Metro Commercial)", "STATUS: ARCHIVED\n• High commercial focus, but too corporate; diluted breaking news urgency.")
    ]
    for c_title, c_desc in comp:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"{c_title}\n"
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_GOLD if "APPROVED" in c_desc else C_TEXT_DIM
        p2 = tf.add_paragraph()
        p2.text = f"{c_desc}\n\n"
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = C_TEXT_WHITE if "APPROVED" in c_desc else C_TEXT_DIM

    # ==================== SLIDE 4: PHASE 1 VS PHASE 2 (AI BOUNDARY) ====================
    s4 = add_base_slide("Scope Demarcation: Phase 1 Scope vs. Phase 2 AI Roadmap", "3. Project Boundaries & Risk Management", 4)

    # Context Card
    create_card(s4, 0.8, 1.5, 11.7, 1.1, "Strategic Decision: AI Features Explicitly Deferred to Phase 2", C_GOLD)
    tb = s4.shapes.add_textbox(Inches(1.05), Inches(1.95), Inches(11.2), Inches(0.6))
    p = tb.text_frame.paragraphs[0]
    p.text = "To guarantee 100% factual accuracy, zero LLM hallucinations, and launch Phase 1 on-time and within budget, all generative AI features have been deferred to Phase 2. Phase 1 focuses on high-speed editorial tools, robust CMS administration, and commercial revenue generation."
    p.font.size = Pt(10.5)
    p.font.color.rgb = C_TEXT_MUTED

    # Table of Boundaries
    t_left, t_top, t_w, t_h = Inches(0.8), Inches(2.8), Inches(11.7), Inches(3.8)
    table_shape = s4.shapes.add_table(6, 3, t_left, t_top, t_w, t_h)
    table = table_shape.table
    table.columns[0].width = Inches(2.8)
    table.columns[1].width = Inches(4.5)
    table.columns[2].width = Inches(4.4)

    headers = ["Capability / Feature", "Phase 1 (Current Approved Scope)", "Phase 2 (Future AI Roadmap)"]
    for i, h in enumerate(headers):
        cell = table.cell(0, i)
        cell.fill.solid()
        cell.fill.fore_color.rgb = RGBColor(30, 41, 65)
        p = cell.text_frame.paragraphs[0]
        p.text = h
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_GOLD if i == 0 else (C_GREEN if i == 1 else C_TEXT_MUTED)

    data = [
        ("Article Summaries & Highlights", "Manual 3-Bullet Highlights: Curated by senior editors directly in CMS before publishing.", "Automated AI TL;DR: Generative summaries via OpenAI/Gemini API."),
        ("Fact-Checking & Credibility", "Editorial Verification Badges: Editors flag articles as 'Verified Report' or 'Investigation'.", "AI Fact-Checking Engine: Automated claim cross-referencing against external databases."),
        ("Audio News Bulletins", "Embedded Video & Manual Audio: Clean YouTube/SoundCloud embeds for debates & reports.", "AI Voice Synthesizer: Automated text-to-speech generating 60-second audio capsules."),
        ("Taxonomy & SEO Tagging", "Structured Manual Taxonomy: Editors assign primary category, sub-slugs, and keywords.", "Semantic AI Auto-Tagging: Automated entity extraction and dynamic internal linking."),
        ("Reader Content Discovery", "Deterministic Category Feeds: 100% reliable SQL queries for related news and top stories.", "Predictive AI Recommendations: Behavioral ML engine tracking reading history.")
    ]

    for row_idx, row in enumerate(data, start=1):
        for col_idx, text in enumerate(row):
            cell = table.cell(row_idx, col_idx)
            cell.fill.solid()
            cell.fill.fore_color.rgb = C_CARD_BG if row_idx % 2 == 0 else RGBColor(14, 20, 36)
            p = cell.text_frame.paragraphs[0]
            p.text = text
            p.font.size = Pt(9.5)
            p.font.color.rgb = C_TEXT_WHITE if col_idx < 2 else C_TEXT_MUTED

    # ==================== SLIDE 5: USER EXPERIENCE & CORE FEATURES ====================
    s5 = add_base_slide("User Experience & High-Impact Frontend Features", "4. Functional Scope", 5)

    features = [
        ("24x7 Breaking News Marquee", "Full-width urgent red ticker with smooth CSS marquee animation. Includes hover-to-pause and instant admin updates.", C_RED),
        ("Dynamic Utility Bar", "IST timestamp, multi-city editions, live SENSEX & NIFTY indices with percentage changes, and YouTube stats.", C_BLUE),
        ("Hero Lead Spotlight", "High-impact visual story display featuring editorial desk byline, read time, and manual 3-bullet Key Takeaways drawer.", C_GOLD),
        ("YouTube TV Broadcast Hub", "Featured 16:9 responsive video player with interactive 3–5 bulletin playlist switcher (zero page reloads).", C_RED),
        ("Continuous Category Desk", "Fast multi-tab filtering across all 6 canonical categories (National, Business, Tech, Energy, Exclusive, World).", C_BLUE),
        ("Studio Showcase & Legal Hub", "Cards for Studio A, B & Ad Inventory with Media Kit PDF download/modal fallback and /legal/* compliance pages.", C_GOLD)
    ]

    for idx, (f_title, f_desc, color) in enumerate(features):
        row = idx // 3
        col = idx % 3
        c_left = 0.8 + col * 4.0
        c_top = 1.6 + row * 2.6
        create_card(s5, c_left, c_top, 3.75, 2.3, f_title, color)
        tb = s5.shapes.add_textbox(Inches(c_left + 0.2), Inches(c_top + 0.65), Inches(3.35), Inches(1.5))
        tf = tb.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = f_desc
        p.font.size = Pt(10.5)
        p.font.color.rgb = C_TEXT_MUTED

    # ==================== SLIDE 6: COMMERCIAL REVENUE ENGINE ====================
    s6 = add_base_slide("B2B Monetization Engine: Commercial Spaces & Studio Rentals", "5. Revenue Operations", 6)

    # Left: Facilities Showcase
    create_card(s6, 0.8, 1.6, 5.8, 4.9, "Showcased Commercial Assets", C_GOLD)
    tb = s6.shapes.add_textbox(Inches(1.05), Inches(2.2), Inches(5.3), Inches(4.1))
    tf = tb.text_frame
    tf.word_wrap = True
    facilities = [
        ("Studio A — Broadcast Newsroom & Stage:", "₹35,000 / Day (Negotiable)", "Acoustically treated 2,200 sq.ft floor, Blackmagic 4K Multicam rig, Chroma cyclorama, makeup rooms, high-speed fiber uplink."),
        ("Studio B — Multi-Cam Podcast Suite:", "₹3,500 / Hour", "Shure SM7B studio microphones, Sony FX3 cinema cameras, live switching sound engineer on duty, instant raw handover."),
        ("Digital Ad Inventory & Media Kit PDF:", "Custom Packages + Downloadable PDF", "CMS-managed Leaderboard & Sidebar MPU ad banners, plus configurable Media Kit PDF download (media_kit_pdf_url) with enquiry modal fallback.")
    ]
    for fac_name, fac_price, fac_details in facilities:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {fac_name}\n"
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"   Price Indicator: {fac_price}\n   Features: {fac_details}\n\n"
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = C_TEXT_MUTED

    # Right: 3-Step Lead Capture & Conversion Workflow
    create_card(s6, 6.9, 1.6, 5.6, 4.9, "3-Tier Lead Capture & Rapid Closing Flow", C_GREEN)
    tb = s6.shapes.add_textbox(Inches(7.15), Inches(2.2), Inches(5.1), Inches(4.1))
    tf = tb.text_frame
    tf.word_wrap = True
    steps = [
        ("Step 1: Frictionless Modal Form", "Accessible via 'Book Studio' and 'Request Rate Card' buttons. Collects Client Name, Production House, facility, date, phone/WhatsApp & scope with CSRF, honeypot & rate-limit checks."),
        ("Step 2: Dual Persistence & Email Alert", "Submissions are instantly stored in the MySQL database with status 'New' and an automated notification is dispatched to space@sndmedia.in."),
        ("Step 3: Direct WhatsApp Redirection", "Prospective clients receive an instant on-screen option to chat directly with the Studio Space Manager (+91-7206622711) with pre-filled enquiry parameters for immediate conversion."),
        ("Sales CRM Management", "Dedicated Space Manager portal to track lead lifecycle: New ➔ Contacted ➔ Negotiating ➔ Booked, with 1-click CSV export.")
    ]
    for s_title, s_desc in steps:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"Step {s_title}:\n" if not s_title.startswith("Step") else f"{s_title}:\n"
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_GOLD
        p2 = tf.add_paragraph()
        p2.text = f"{s_desc}\n\n"
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = C_TEXT_MUTED

    # ==================== SLIDE 7: TECHNICAL ARCHITECTURE ====================
    s7 = add_base_slide("Technical Architecture & Zero-Cost Strategy", "6. Technical Specifications", 7)

    col_w = 3.65
    create_card(s7, 0.8, 1.6, col_w, 4.9, "Core Backend & Database", C_BLUE)
    tb = s7.shapes.add_textbox(Inches(1.05), Inches(2.2), Inches(col_w - 0.5), Inches(4.0))
    tf = tb.text_frame
    tf.word_wrap = True
    points = [
        ("PHP 8.x + Native PDO:", "High performance, zero framework bloat, prepared statements eliminating SQL injection."),
        ("MySQL / MariaDB:", "Standard relational schema with foreign key constraints, UTF8mb4 for Hindi/multilingual text support."),
        ("XAMPP Apache Compatibility:", "Turnkey local deployment ready in d:\\PHP\\WindowXAPP\\htdocs, portable to Linux cPanel.")
    ]
    for p_title, p_desc in points:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {p_title} "
        p.font.bold = True
        p.font.size = Pt(10.5)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"  {p_desc}\n\n"
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = C_TEXT_MUTED

    create_card(s7, 4.8, 1.6, col_w, 4.9, "Zero-Cost YouTube Sync", C_RED)
    tb = s7.shapes.add_textbox(Inches(5.05), Inches(2.2), Inches(col_w - 0.5), Inches(4.0))
    tf = tb.text_frame
    tf.word_wrap = True
    points = [
        ("No Google Cloud API Billing:", "Eliminates monthly API quota limits, billing credit cards, and complex OAuth credential setup."),
        ("Intelligent URL / ID Parser:", "Admins simply paste standard YouTube URLs. System automatically extracts the 11-char ID."),
        ("Dynamic HQ Thumbnails:", "Auto-generates img.youtube.com/vi/{id}/hqdefault.jpg preview cards dynamically.")
    ]
    for p_title, p_desc in points:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {p_title} "
        p.font.bold = True
        p.font.size = Pt(10.5)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"  {p_desc}\n\n"
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = C_TEXT_MUTED

    create_card(s7, 8.8, 1.6, col_w, 4.9, "Market Data & Caching", C_GREEN)
    tb = s7.shapes.add_textbox(Inches(9.05), Inches(2.2), Inches(col_w - 0.5), Inches(4.0))
    tf = tb.text_frame
    tf.word_wrap = True
    points = [
        ("Hybrid Free Feed + Cache:", "Checks lightweight market quote feeds with 15-minute file caching to prevent external rate limits."),
        ("Admin Manual Overrides:", "If free financial APIs are throttled or during market holidays, editors can manually update SENSEX & NIFTY."),
        ("Resilient Fallback:", "Site never breaks or delays page load even if third-party financial feeds go offline.")
    ]
    for p_title, p_desc in points:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {p_title} "
        p.font.bold = True
        p.font.size = Pt(10.5)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"  {p_desc}\n\n"
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = C_TEXT_MUTED

    # ==================== SLIDE 8: RBAC GOVERNANCE ====================
    s8 = add_base_slide("Governance & Role-Based Access Control (RBAC)", "7. Security & Administration", 8)

    t_left, t_top, t_w, t_h = Inches(0.8), Inches(1.55), Inches(11.7), Inches(4.3)
    table_shape = s8.shapes.add_table(8, 5, t_left, t_top, t_w, t_h)
    table = table_shape.table
    table.columns[0].width = Inches(3.7)
    table.columns[1].width = Inches(2.0)
    table.columns[2].width = Inches(2.0)
    table.columns[3].width = Inches(2.0)
    table.columns[4].width = Inches(2.0)

    headers = ["Feature / CMS Module", "Super Admin", "Senior Editor", "Reporter / Stringer", "Space Manager"]
    for i, h in enumerate(headers):
        cell = table.cell(0, i)
        cell.fill.solid()
        cell.fill.fore_color.rgb = RGBColor(30, 41, 65)
        p = cell.text_frame.paragraphs[0]
        p.text = h
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_GOLD if i == 0 else C_TEXT_WHITE

    rbac_rows = [
        ("Manage Users, Roles & Admin Password Reset", "Full Access", "No Access", "No Access", "No Access"),
        ("Global Site Settings, Ads & Legal Policies", "Full Access", "No Access", "No Access", "No Access"),
        ("Publish & Edit Any News Article", "Full Access", "Full Access", "Own Drafts Only", "No Access"),
        ("24x7 Breaking Ticker Marquee", "Full Access", "Full Access", "No Access", "No Access"),
        ("YouTube TV Broadcast Hub", "Full Access", "Full Access", "No Access", "No Access"),
        ("Studio Facilities & Pricing Editor", "Full Access", "No Access", "No Access", "Full Access"),
        ("Commercial Leads CRM & CSV Export", "Full Access", "No Access", "No Access", "Full Access")
    ]

    for row_idx, row in enumerate(rbac_rows, start=1):
        for col_idx, text in enumerate(row):
            cell = table.cell(row_idx, col_idx)
            cell.fill.solid()
            cell.fill.fore_color.rgb = C_CARD_BG if row_idx % 2 == 0 else RGBColor(14, 20, 36)
            p = cell.text_frame.paragraphs[0]
            p.text = text
            p.font.size = Pt(10)
            if "Full Access" in text:
                p.font.color.rgb = C_GREEN
                p.font.bold = True
            elif "Drafts Only" in text:
                p.font.color.rgb = C_GOLD
            elif "No Access" in text:
                p.font.color.rgb = C_TEXT_DIM
            else:
                p.font.color.rgb = C_TEXT_WHITE

    create_card(s8, 0.8, 6.05, 11.7, 0.72, None, C_BLUE)
    tb_s8_note = s8.shapes.add_textbox(Inches(1.0), Inches(6.18), Inches(11.3), Inches(0.5))
    tf_s8_note = tb_s8_note.text_frame
    tf_s8_note.word_wrap = True
    p_s8_note = tf_s8_note.paragraphs[0]
    p_s8_note.text = "Self-Service Recovery (US-20): All 4 staff roles support email-delivered 1-hour cryptographic token password resets (bin2hex(random_bytes(32))) with minimum 10-character BCRYPT (cost >= 12) enforcement."
    p_s8_note.font.size = Pt(9.5)
    p_s8_note.font.color.rgb = C_TEXT_WHITE

    # ==================== SLIDE 9: DYNAMIC CMS CONTROL ====================
    s9 = add_base_slide("Zero Hardcoded Content: 100% Dynamic CMS Control", "8. Content Administration", 9)

    col_w = 3.65
    cms_sections = [
        ("Header & Navigation Settings", [
            "Edition Stamp (Palwal • New Delhi • NCR)",
            "YouTube (@sndschoolmedia: 3.36K Subs • 666+ Videos)",
            "Financial Indices (Sensex / Nifty Override)",
            "Official Logo, Banner & Social URLs"
        ], C_BLUE),
        ("Editorial & Breaking Marquee", [
            "Add / Edit / Delete Breaking Bulletins",
            "Urgency Tag (Breaking, Flash, Routine)",
            "Featured Hero Lead Story Selection",
            "3-Bullet Highlights Drawer Editor"
        ], C_RED),
        ("Studio, Ads & Media Kit", [
            "Studio A/B hourly & daily rates & specs",
            "CMS-managed Leaderboard & Sidebar ads",
            "Media Kit PDF upload / modal fallback",
            "Availability & sort order controls"
        ], C_GOLD),
        ("Leads CRM & Inquiries", [
            "Live Inbound Rental Inquiries Table",
            "Status: New / Contacted / Booked",
            "Internal Follow-Up Notes per Client",
            "1-Click CSV Export for Sales Desk"
        ], C_GREEN),
        ("Legal Policies & Media Desk", [
            "HQ (SND Public School, Palwal) & @sndmedia.in",
            "Dedicated /legal/* compliance routes",
            "Sanitized WYSIWYG Privacy/ToS/Ethics",
            "Studio rental rules & phone (+91-7206622711)"
        ], C_BLUE),
        ("User & Reset Controls", [
            "Add / Deactivate CMS Staff Accounts",
            "Role Assignment across 4 RBAC Tiers",
            "1-hr token self & admin password reset",
            "BCRYPT >=12 & Last Login Audit Trail"
        ], C_RED)
    ]

    for idx, (s_title, items, color) in enumerate(cms_sections):
        row = idx // 3
        col = idx % 3
        c_left = 0.8 + col * 4.0
        c_top = 1.6 + row * 2.6
        create_card(s9, c_left, c_top, 3.75, 2.3, s_title, color)
        tb = s9.shapes.add_textbox(Inches(c_left + 0.2), Inches(c_top + 0.65), Inches(3.35), Inches(1.5))
        tf = tb.text_frame
        tf.word_wrap = True
        for it in items:
            p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
            p.text = f"• {it}"
            p.font.size = Pt(9.5)
            p.font.color.rgb = C_TEXT_MUTED

    # ==================== SLIDE 10: NON-FUNCTIONAL REQUIREMENTS ====================
    s10 = add_base_slide("Non-Functional Requirements: Speed, SEO & Security", "9. Quality & Performance", 10)

    col_w = 3.65
    create_card(s10, 0.8, 1.6, col_w, 4.9, "Performance & WCAG AA UX", C_GREEN)
    tb = s10.shapes.add_textbox(Inches(1.05), Inches(2.2), Inches(col_w - 0.5), Inches(4.0))
    tf = tb.text_frame
    tf.word_wrap = True
    nfr_perf = [
        ("Sub-2.0s LCP & CLS < 0.05:", "Lightweight HTML5 + modular Tailwind CSS; fixed aspect ratios and <30ms PHP PDO TTFB."),
        ("WCAG AA Accessibility (US-25):", "Visible keyboard focus rings, >=44x44px touch targets, WCAG AA contrast, and text form errors."),
        ("Motion & Semantic HTML:", "Honors prefers-reduced-motion for marquee/pulses with screen-reader-friendly controls.")
    ]
    for t, d in nfr_perf:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {t}\n"
        p.font.bold = True
        p.font.size = Pt(10.5)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"  {d}\n\n"
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = C_TEXT_MUTED

    create_card(s10, 4.8, 1.6, col_w, 4.9, "SEO & Social Amplification", C_BLUE)
    tb = s10.shapes.add_textbox(Inches(5.05), Inches(2.2), Inches(col_w - 0.5), Inches(4.0))
    tf = tb.text_frame
    tf.word_wrap = True
    nfr_seo = [
        ("Dynamic OpenGraph & Twitter Cards:", "Automatic og:image, og:title, and og:description generation ensures high CTR on WhatsApp & X."),
        ("NewsArticle Structured Data:", "Schema.org markup enables Google News and Top Stories eligibility."),
        ("LocalBusiness Schema for Studio Rentals:", "Optimized schema targeting high-value commercial searches on sndmedia.in (e.g. 'studio on rent Palwal Delhi NCR').")
    ]
    for t, d in nfr_seo:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {t}\n"
        p.font.bold = True
        p.font.size = Pt(10.5)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"  {d}\n\n"
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = C_TEXT_MUTED

    create_card(s10, 8.8, 1.6, col_w, 4.9, "Security & Anti-Abuse", C_RED)
    tb = s10.shapes.add_textbox(Inches(9.05), Inches(2.2), Inches(col_w - 0.5), Inches(4.0))
    tf = tb.text_frame
    tf.word_wrap = True
    nfr_sec = [
        ("SQLi & XSS Defense:", "100% PDO prepared statements, htmlspecialchars() plain-text escaping, and allowlist HTML sanitizer."),
        ("Anti-Bot & CSRF Protection:", "CSRF tokens, invisible honeypot traps, and 5/hr IP rate limiting stop automated form spam."),
        ("Auth & Recovery Hardening:", "BCRYPT (>=12), 1-hr cryptographic password reset tokens, HttpOnly/HTTPS cookies & RBAC guards.")
    ]
    for t, d in nfr_sec:
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.text = f"• {t}\n"
        p.font.bold = True
        p.font.size = Pt(10.5)
        p.font.color.rgb = C_TEXT_WHITE
        p2 = tf.add_paragraph()
        p2.text = f"  {d}\n\n"
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = C_TEXT_MUTED

    # ==================== SLIDE 11: IMPLEMENTATION ROADMAP ====================
    s11 = add_base_slide("Implementation Roadmap & Delivery Milestones", "10. Execution Plan", 11)

    sprints = [
        ("Sprint 1: Database, Auth & Settings", [
            "MySQL schema (users, password_reset_tokens, articles, facilities, leads, settings)",
            "PDO layer, RBAC guards & 1-hr cryptographic token password reset (US-20)",
            "Global site settings panel, ad banners, Media Kit PDF & free market rate cache"
        ], C_BLUE),
        ("Sprint 2: Modular Frontend, Legal & UX", [
            "Modular PHP templates, header/footer, ticker & Hero Key Takeaways drawer",
            "6-category News Desk (National, Business, Tech, Energy, Exclusive, World)",
            "Dedicated /legal/* compliance routes (US-24) & WCAG AA UX baseline (US-25)"
        ], C_RED),
        ("Sprint 3: Broadcast TV & Commercial Flow", [
            "YouTube TV 16:9 player with 3–5 item bulletin switcher & zero paid API cost",
            "Studio showcase cards, Media Kit PDF direct download / modal fallback",
            "Rental modal (Client Name & Production House), DB log, email & WhatsApp hook"
        ], C_GOLD),
        ("Sprint 4: Role-Based CMS Admin Portal", [
            "Role-aware dashboard & Articles editor with verification badges & uploads",
            "Breaking ticker desk & zero-cost YouTube URL/ID manager",
            "Studio Leads CRM (CSV export, inline status, notes) & Facilities editor"
        ], C_GREEN),
        ("Sprint 5: SEO, Hardening & Go-Live", [
            "OpenGraph, Twitter Cards, NewsArticle & LocalBusiness JSON-LD markup",
            "Allowlist HTML sanitizer audit, security headers & Lighthouse LCP < 2.0s / CLS < 0.05",
            "Cross-browser QA (360px–4K), staff walkthrough & production launch"
        ], C_BLUE)
    ]

    for idx, (sp_title, sp_tasks, color) in enumerate(sprints):
        c_top = 1.5 + idx * 1.05
        create_card(s11, 0.8, c_top, 11.7, 0.95, None, color)
        # title
        tb = s11.shapes.add_textbox(Inches(1.0), Inches(c_top + 0.1), Inches(3.4), Inches(0.75))
        tf = tb.text_frame
        p = tf.paragraphs[0]
        p.text = sp_title
        p.font.bold = True
        p.font.size = Pt(11.5)
        p.font.color.rgb = C_GOLD if "Sprint 1" in sp_title else C_TEXT_WHITE
        
        # tasks
        tb2 = s11.shapes.add_textbox(Inches(4.6), Inches(c_top + 0.08), Inches(7.7), Inches(0.8))
        tf2 = tb2.text_frame
        tf2.word_wrap = True
        for task in sp_tasks:
            p = tf2.add_paragraph() if tf2.paragraphs[0].text else tf2.paragraphs[0]
            p.text = f"• {task}"
            p.font.size = Pt(9.5)
            p.font.color.rgb = C_TEXT_MUTED

    # ==================== SLIDE 12: FORMAL SIGNOFF SHEET ====================
    s12 = add_base_slide("Client Formal Sign-Off & Approval Confirmation", "11. Sign-Off Execution", 12)

    create_card(s12, 0.8, 1.5, 11.7, 1.2, "Phase 1 Scope & Architecture Approval Agreement", C_GOLD)
    tb = s12.shapes.add_textbox(Inches(1.05), Inches(1.95), Inches(11.2), Inches(0.7))
    p = tb.text_frame.paragraphs[0]
    p.text = "By signing below, the Client approves the Phase 1 Scope of Work, Technical Architecture (PHP 8 + MySQL PDO), Concept 01 (SND Prime Broadcast) visual foundation, and confirms the explicit deferral of Artificial Intelligence (AI) features to Phase 2."
    p.font.size = Pt(10.5)
    p.font.color.rgb = C_TEXT_WHITE

    # Sign-off Table
    t_left, t_top, t_w, t_h = Inches(0.8), Inches(2.9), Inches(11.7), Inches(3.6)
    table_shape = s12.shapes.add_table(5, 5, t_left, t_top, t_w, t_h)
    table = table_shape.table
    table.columns[0].width = Inches(2.6)
    table.columns[1].width = Inches(2.3)
    table.columns[2].width = Inches(2.4)
    table.columns[3].width = Inches(1.8)
    table.columns[4].width = Inches(2.6)

    headers = ["Stakeholder Role", "Designated Representative", "Organization", "Approval Date", "Signature / Status"]
    for i, h in enumerate(headers):
        cell = table.cell(0, i)
        cell.fill.solid()
        cell.fill.fore_color.rgb = RGBColor(30, 41, 65)
        p = cell.text_frame.paragraphs[0]
        p.text = h
        p.font.bold = True
        p.font.size = Pt(11)
        p.font.color.rgb = C_GOLD if i == 0 else C_TEXT_WHITE

    stakeholders = [
        ("Executive Project Sponsor", "Client Leadership", "SND Media House", "October 2026", "[   Pending Sign-off   ]"),
        ("Editorial Desk Authority", "Editor-in-Chief / News Desk", "SND Editorial", "October 2026", "[   Pending Sign-off   ]"),
        ("Commercial Leasing Lead", "Space & Sales Director", "SND Commercial Desk", "October 2026", "[   Pending Sign-off   ]"),
        ("Technical Delivery Lead", "Lead Architect & Developer", "Engineering Team", "October 2026", "Approved & Ready")
    ]

    for row_idx, row in enumerate(stakeholders, start=1):
        for col_idx, text in enumerate(row):
            cell = table.cell(row_idx, col_idx)
            cell.fill.solid()
            cell.fill.fore_color.rgb = C_CARD_BG if row_idx % 2 == 0 else RGBColor(14, 20, 36)
            p = cell.text_frame.paragraphs[0]
            p.text = text
            p.font.size = Pt(10)
            if col_idx == 4:
                p.font.bold = True
                p.font.color.rgb = C_GREEN if "Ready" in text else C_GOLD
            else:
                p.font.color.rgb = C_TEXT_WHITE if col_idx == 0 else C_TEXT_MUTED

    output_path = r"d:\PHP\WindowXAPP\htdocs\Websites\SND-Media-House\SND_Media_House_Phase1_Client_Signoff.pptx"
    prs.save(output_path)
    print(f"Presentation saved successfully to: {output_path}")

if __name__ == "__main__":
    create_signoff_deck()
