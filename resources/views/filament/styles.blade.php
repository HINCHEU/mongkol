<!-- Morodok Snaeha Atelier Design System for Filament Admin -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Moul&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* ==========================================================================
       1. Base Typography & Khmer Typesetting
       ========================================================================== */
    body, .fi-body {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        background-color: #FAF7F2 !important;
        color: #1A1412 !important;
    }

    html.dark body, html.dark .fi-body {
        background-color: #14110F !important;
        color: #FAF6EE !important;
    }

    h1, h2, h3, .fi-header-heading {
        font-family: 'Playfair Display', Georgia, serif !important;
        letter-spacing: -0.01em !important;
        color: #1A1412 !important;
    }

    html.dark h1, html.dark h2, html.dark h3, html.dark .fi-header-heading {
        color: #FAF6EE !important;
    }

    /* Strict Khmer Typesetting Rules (DESIGN.md) */
    :lang(km), [lang="km"], .km-text, .font-khmer {
        font-family: 'Kantumruy Pro', system-ui, sans-serif !important;
        letter-spacing: 0 !important;
        line-height: 1.65 !important;
    }

    .font-moul {
        font-family: 'Moul', serif !important;
        letter-spacing: 0 !important;
    }

    /* ==========================================================================
       2. Luxury Sidebar & Navigation
       ========================================================================== */
    .fi-sidebar {
        background-color: #FAF6EE !important;
        border-right: 1px solid rgba(197, 160, 89, 0.28) !important;
        box-shadow: 2px 0 12px -2px rgba(88, 17, 26, 0.03) !important;
    }

    html.dark .fi-sidebar {
        background-color: #1A1715 !important;
        border-right: 1px solid rgba(197, 160, 89, 0.2) !important;
        box-shadow: 2px 0 12px -2px rgba(0, 0, 0, 0.5) !important;
    }

    .fi-sidebar-group-label {
        font-size: 0.68rem !important;
        letter-spacing: 0.12em !important;
        text-transform: uppercase !important;
        color: #8C6D23 !important;
        font-weight: 700 !important;
    }

    html.dark .fi-sidebar-group-label {
        color: #D4AF37 !important;
    }

    .fi-sidebar-item-btn {
        border-radius: 0.5rem !important;
        transition: all 0.2s cubic-bezier(0.2, 0, 0, 1) !important;
    }

    .fi-sidebar-item:hover .fi-sidebar-item-btn {
        background-color: rgba(197, 160, 89, 0.14) !important;
    }

    .fi-sidebar-item-active .fi-sidebar-item-btn {
        background: linear-gradient(90deg, rgba(123, 17, 34, 0.12) 0%, rgba(212, 175, 55, 0.08) 100%) !important;
        border-left: 3px solid #7B1122 !important;
        color: #7B1122 !important;
        font-weight: 600 !important;
    }

    html.dark .fi-sidebar-item-active .fi-sidebar-item-btn {
        background: linear-gradient(90deg, rgba(218, 69, 87, 0.2) 0%, rgba(212, 175, 55, 0.12) 100%) !important;
        border-left: 3px solid #DA4557 !important;
        color: #F8CCD1 !important;
    }

    /* ==========================================================================
       3. Atelier Topbar Header
       ========================================================================== */
    .fi-topbar {
        background-color: rgba(250, 246, 238, 0.94) !important;
        backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(197, 160, 89, 0.25) !important;
    }

    html.dark .fi-topbar {
        background-color: rgba(26, 23, 21, 0.94) !important;
        border-bottom: 1px solid rgba(197, 160, 89, 0.2) !important;
    }

    /* ==========================================================================
       4. Cards, Sections & Widgets
       ========================================================================== */
    .fi-section, .fi-wi-stats-overview-stat, .fi-ta-ctn {
        background-color: #FFFFFF !important;
        border: 1px solid rgba(197, 160, 89, 0.35) !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 4px 20px -2px rgba(88, 17, 26, 0.04), 0 2px 6px -1px rgba(140, 109, 35, 0.03) !important;
        position: relative;
    }

    html.dark .fi-section, html.dark .fi-wi-stats-overview-stat, html.dark .fi-ta-ctn {
        background-color: #1C1917 !important;
        border: 1px solid rgba(197, 160, 89, 0.22) !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.4) !important;
    }

    /* Gold hairline accent on top of cards */
    .fi-wi-stats-overview-stat::before, .fi-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 8%;
        right: 8%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.65), transparent);
        pointer-events: none;
    }

    /* ==========================================================================
       5. Elevated Tables & Cells
       ========================================================================== */
    .fi-ta-header-cell {
        background-color: #F7F2E8 !important;
        color: #574141 !important;
        font-size: 0.73rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.06em !important;
        text-transform: uppercase !important;
        border-bottom: 1px solid rgba(197, 160, 89, 0.3) !important;
        padding-top: 0.85rem !important;
        padding-bottom: 0.85rem !important;
    }

    html.dark .fi-ta-header-cell {
        background-color: #24201D !important;
        color: #D6C7B2 !important;
        border-bottom: 1px solid rgba(197, 160, 89, 0.2) !important;
    }

    .fi-ta-row {
        transition: background-color 0.15s ease-in-out !important;
    }

    .fi-ta-row:hover {
        background-color: rgba(246, 238, 219, 0.45) !important;
    }

    html.dark .fi-ta-row:hover {
        background-color: rgba(212, 175, 55, 0.06) !important;
    }

    /* ==========================================================================
       6. Royal Primary Buttons & Badges
       ========================================================================== */
    .fi-btn.fi-color-primary:not(.fi-btn-outlined) {
        background: linear-gradient(135deg, #8B1426 0%, #7B1122 55%, #58111A 100%) !important;
        border: 1px solid rgba(212, 175, 55, 0.45) !important;
        box-shadow: 0 2px 8px rgba(123, 17, 34, 0.3) !important;
        color: #FDFBF7 !important;
        font-weight: 600 !important;
        transition: all 0.2s cubic-bezier(0.2, 0, 0, 1) !important;
    }

    .fi-btn.fi-color-primary:not(.fi-btn-outlined):hover {
        background: linear-gradient(135deg, #9C182D 0%, #8B1426 55%, #691520 100%) !important;
        box-shadow: 0 4px 14px rgba(123, 17, 34, 0.45) !important;
        transform: translateY(-1px);
    }

    .fi-badge {
        font-weight: 600 !important;
        letter-spacing: 0.02em !important;
        border-radius: 9999px !important;
        padding-left: 0.65rem !important;
        padding-right: 0.65rem !important;
    }

    /* ==========================================================================
       7. Form Controls & Focus States
       ========================================================================== */
    .fi-input, .fi-select-input, .fi-textarea {
        border-radius: 0.5rem !important;
        border-color: rgba(197, 160, 89, 0.45) !important;
        background-color: #FFFFFF !important;
        transition: all 0.15s ease-in-out !important;
    }

    html.dark .fi-input, html.dark .fi-select-input, html.dark .fi-textarea {
        border-color: rgba(197, 160, 89, 0.25) !important;
        background-color: #211E1C !important;
    }

    .fi-input:focus, .fi-select-input:focus, .fi-textarea:focus {
        border-color: #8C6D23 !important;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.28) !important;
    }

    /* Scrollbars */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #FAF6EE;
    }
    ::-webkit-scrollbar-thumb {
        background: #C5A059;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #8C6D23;
    }
</style>
