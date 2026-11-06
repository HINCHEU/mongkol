<!-- Morodok Snaeha Atelier Design System for Filament Admin -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Moul&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* ==========================================================================
       1. Base Typography & Khmer Typesetting
       ========================================================================== */
    :root {
        color-scheme: light !important;
    }

    body, .fi-body, .fi-main {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        background-color: #FAF6EE !important;
        color: #1A1412 !important;
    }

    h1, h2, h3, .fi-header-heading {
        font-family: 'Playfair Display', Georgia, serif !important;
        letter-spacing: -0.01em !important;
        color: #1A1412 !important;
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
       2. Brand Logo Size Containment
       ========================================================================== */
    .fi-logo {
        height: 2.4rem !important;
        max-height: 2.4rem !important;
        display: flex !important;
        align-items: center !important;
        overflow: visible !important;
    }

    .fi-logo svg {
        max-height: 100% !important;
    }

    /* ==========================================================================
       3. Luxury Sidebar & Navigation
       ========================================================================== */
    .fi-sidebar {
        background-color: #FAF6EE !important;
        border-right: 1px solid rgba(197, 160, 89, 0.28) !important;
        box-shadow: 2px 0 12px -2px rgba(88, 17, 26, 0.03) !important;
    }

    .fi-sidebar-group-label {
        font-size: 0.68rem !important;
        letter-spacing: 0.12em !important;
        text-transform: uppercase !important;
        color: #8C6D23 !important;
        font-weight: 700 !important;
    }

    .fi-sidebar-item-label {
        color: #574141 !important;
        font-weight: 500 !important;
    }

    .fi-sidebar-item-btn {
        border-radius: 0.5rem !important;
        transition: all 0.2s cubic-bezier(0.2, 0, 0, 1) !important;
    }

    .fi-sidebar-item:hover .fi-sidebar-item-btn {
        background-color: rgba(197, 160, 89, 0.14) !important;
    }

    .fi-sidebar-item:hover .fi-sidebar-item-label {
        color: #1A1412 !important;
    }

    .fi-sidebar-item-active .fi-sidebar-item-btn {
        background: linear-gradient(90deg, rgba(123, 17, 34, 0.08) 0%, rgba(212, 175, 55, 0.06) 100%) !important;
        border-left: 3px solid #7B1122 !important;
    }

    .fi-sidebar-item-active .fi-sidebar-item-label {
        color: #7B1122 !important;
        font-weight: 600 !important;
    }

    .fi-sidebar-item-active .fi-sidebar-item-icon {
        color: #7B1122 !important;
    }

    /* ==========================================================================
       4. Atelier Topbar Header
       ========================================================================== */
    .fi-topbar {
        background-color: rgba(250, 246, 238, 0.96) !important;
        backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(197, 160, 89, 0.25) !important;
    }

    /* ==========================================================================
       5. Cards, Sections & Widgets
       ========================================================================== */
    .fi-section, .fi-wi-stats-overview-stat, .fi-ta-ctn {
        background-color: #FFFFFF !important;
        border: 1px solid rgba(197, 160, 89, 0.35) !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 4px 20px -2px rgba(88, 17, 26, 0.04), 0 2px 6px -1px rgba(140, 109, 35, 0.03) !important;
        position: relative;
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
       6. Elevated Tables & Cells
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

    .fi-ta-row {
        transition: background-color 0.15s ease-in-out !important;
    }

    .fi-ta-row:hover {
        background-color: rgba(246, 238, 219, 0.45) !important;
    }

    /* ==========================================================================
       7. Royal Primary Buttons & Badges
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
       8. Form Controls & Focus States
       ========================================================================== */
    .fi-input, .fi-select-input, .fi-textarea {
        border-radius: 0.5rem !important;
        border-color: rgba(197, 160, 89, 0.45) !important;
        background-color: #FFFFFF !important;
        transition: all 0.15s ease-in-out !important;
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
