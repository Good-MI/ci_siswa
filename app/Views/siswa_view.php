<?php
// Master class options grouped by major
$classList = [
    'RPL' => ['10 RPL', '11 RPL', '12 RPL'],
    'Broadcast' => ['10 Broadcast', '11 Broadcast', '12 Broadcast'],
    'Animation' => ['10 Animation', '11 Animation', '12 Animation'],
];

// Helper: convert numeric grade to Roman numeral for display (10 -> X, 11 -> XI, 12 -> XII)
$toRomanClass = function (?string $class): string {
    if (empty($class)) {
        return '';
    }
    $pattern = ['/^10\b/', '/^11\b/', '/^12\b/'];
    $replace = ['X', 'XI', 'XII'];
    return (string) preg_replace($pattern, $replace, $class);
};

// Helper: sort key for Class / Major (RPL=1, Broadcast=2, Animation=3, followed by zero-padded grade)
$getClassOrderKey = function (?string $class): string {
    if (empty($class)) {
        return '9-Other-00';
    }
    $majorOrder = '9';
    $majorName = 'Other';
    if (stripos($class, 'RPL') !== false) {
        $majorOrder = '1';
        $majorName = 'RPL';
    } elseif (stripos($class, 'Broadcast') !== false || stripos($class, 'BC') !== false) {
        $majorOrder = '2';
        $majorName = 'Broadcast';
    } elseif (stripos($class, 'Animation') !== false || stripos($class, 'ANM') !== false) {
        $majorOrder = '3';
        $majorName = 'Animation';
    }
    preg_match('/\b(\d+)\b/', $class, $m);
    $grade = isset($m[1]) ? sprintf('%02d', (int) $m[1]) : '00';
    return "{$majorOrder}-{$majorName}-{$grade}";
};

// Stats calculation from $siswa
$totalCount = !empty($siswa) ? count($siswa) : 0;
$rplCount = 0;
$broadcastCount = 0;
$animationCount = 0;

if (!empty($siswa)) {
    foreach ($siswa as $s) {
        $k = $s['kelas'] ?? '';
        if (stripos($k, 'RPL') !== false) {
            $rplCount++;
        } elseif (stripos($k, 'Broadcast') !== false || stripos($k, 'BC') !== false) {
            $broadcastCount++;
        } elseif (stripos($k, 'Animation') !== false || stripos($k, 'ANM') !== false) {
            $animationCount++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Budi Luhur - Student Data</title>

    <!-- Google Fonts: Inter (body), Space Grotesk (headings), JetBrains Mono (NIS) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Space+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- CDN CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CDN Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- CDN CSS DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            /* Bold, masculine palette */
            --brand-navy: #0B1B3A;
            --brand-navy-dark: #071228;
            --brand-royal: #1D4ED8;
            --primary-blue: #2563EB;
            --primary-blue-hover: #1E40AF;
            --accent-orange: #F97316;
            --accent-orange-hover: #EA580C;
            --page-bg: #EDF1F7;
            --card-surface: #FFFFFF;
            --border-slate: #D5DDE8;
            --shadow-navy: rgba(11, 27, 58, 0.08);
            --shadow-navy-lg: rgba(11, 27, 58, 0.14);

            /* Major colors: NO purple/violet/pink */
            --major-rpl: #2563EB;
            --major-rpl-bg: #DBEAFE;
            --major-rpl-text: #1E3A8A;

            --major-bc: #EA580C;
            --major-bc-bg: #FFEDD5;
            --major-bc-text: #9A3412;

            --major-anim: #0D9488;
            --major-anim-bg: #CCFBF1;
            --major-anim-text: #115E59;

            /* Functional colors */
            --color-success: #16A34A;
            --color-danger: #DC2626;
            --color-danger-hover: #B91C1C;
            --color-warning: #D97706;

            /* Text colors */
            --text-navy: #0B1220;
            --text-slate: #475569;
            --text-muted: #64748B;
        }

        /* Base Typography & Background */
        body {
            background-color: var(--page-bg);
            color: var(--text-navy);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .brand-title,
        .stat-value,
        .hero-title {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            letter-spacing: -0.02em;
        }

        /* Selection & Scrollbar */
        ::selection {
            background-color: var(--primary-blue);
            color: #FFFFFF;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #E2E8F0;
        }

        ::-webkit-scrollbar-thumb {
            background: #94A3B8;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #64748B;
        }

        /* Motion keyframes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @media (prefers-reduced-motion: reduce) {
            .animate-fade-in-up {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }

            * {
                transition-duration: 0.01ms !important;
            }
        }

        /* Navbar */
        .navbar-custom {
            background-color: var(--brand-navy) !important;
            border-bottom: 2px solid var(--accent-orange);
            box-shadow: 0 4px 15px rgba(11, 27, 58, 0.15);
        }

        .logo-box {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: var(--primary-blue);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
        }

        .brand-title {
            color: #FFFFFF;
            font-size: 1.15rem;
            font-weight: 700;
            line-height: 1.15;
        }

        .brand-subtitle {
            color: #94A3B8;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.04em;
        }

        .admin-pill {
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            transition: background-color 0.15s ease;
        }

        .admin-pill:hover {
            background-color: rgba(255, 255, 255, 0.12);
        }

        .admin-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--primary-blue);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .online-dot {
            position: absolute;
            bottom: -1px;
            right: -1px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #22C55E;
            border: 2px solid var(--brand-navy);
        }

        .admin-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #FFFFFF;
        }

        /* Hero Banner */
        .hero-card {
            background: linear-gradient(135deg, #0B1B3A 0%, #112854 50%, #1D4ED8 100%);
            border-radius: 14px;
            position: relative;
            overflow: hidden;
            color: #FFFFFF;
            box-shadow: 0 10px 25px -5px rgba(11, 27, 58, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-image:
                linear-gradient(45deg, rgba(255, 255, 255, 0.03) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(255, 255, 255, 0.03) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(255, 255, 255, 0.03) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, 0.03) 75%);
            background-size: 32px 32px;
            pointer-events: none;
        }

        .badge-dot-hero {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--accent-orange);
            display: inline-block;
        }

        .hero-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.15;
        }

        .hero-subtitle {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.75);
            max-width: 580px;
        }

        .btn-hero-cta {
            background-color: var(--accent-orange) !important;
            border-color: var(--accent-orange) !important;
            color: #FFFFFF !important;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 12px 24px;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.35);
            transition: all 0.2s ease-in-out;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-hero-cta:hover,
        .btn-hero-cta:focus {
            background-color: var(--accent-orange-hover) !important;
            border-color: var(--accent-orange-hover) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(249, 115, 22, 0.45);
        }

        /* Stat Cards */
        .stat-card {
            background-color: var(--card-surface);
            border: 1px solid var(--border-slate);
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--shadow-navy);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--shadow-navy-lg);
        }

        .stat-total {
            border-left: 4px solid var(--brand-navy) !important;
        }

        .stat-rpl {
            border-left: 4px solid var(--major-rpl) !important;
        }

        .stat-broadcast {
            border-left: 4px solid var(--major-bc) !important;
        }

        .stat-animation {
            border-left: 4px solid var(--major-anim) !important;
        }

        .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .stat-icon-total {
            background-color: #F1F5F9;
            color: var(--brand-navy);
        }

        .stat-icon-rpl {
            background-color: var(--major-rpl-bg);
            color: var(--major-rpl);
        }

        .stat-icon-broadcast {
            background-color: var(--major-bc-bg);
            color: var(--major-bc);
        }

        .stat-icon-animation {
            background-color: var(--major-anim-bg);
            color: var(--major-anim);
        }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: var(--text-slate);
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .stat-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1.1;
            color: var(--text-navy);
        }

        /* Flash Messages */
        .flash-card {
            border-radius: 10px;
            box-shadow: 0 4px 12px var(--shadow-navy);
        }

        .flash-success {
            background-color: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-left: 4px solid var(--color-success);
            color: #14532D;
        }

        .flash-error {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            border-left: 4px solid var(--color-danger);
            color: #7F1D1D;
        }

        /* Main Table Card */
        .table-card {
            background-color: var(--card-surface);
            border: 1px solid var(--border-slate);
            border-radius: 14px;
            box-shadow: 0 4px 20px var(--shadow-navy);
            overflow: hidden;
        }

        /* Toolbar Controls */
        #dropdownSorting {
            border-radius: 10px;
            border: 1px solid var(--border-slate);
            background-color: #FFFFFF !important;
            color: var(--text-navy) !important;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 8px 14px;
            transition: all 0.15s ease;
            max-width: 100%;
            box-shadow: none !important;
        }

        #dropdownSorting .sort-toggle-icon {
            color: var(--text-slate) !important;
            transition: color 0.15s ease;
        }

        #dropdownSorting:hover {
            background-color: #F8FAFC !important;
            border-color: #94A3B8 !important;
            color: var(--text-navy) !important;
        }

        #dropdownSorting:hover .sort-toggle-icon {
            color: var(--brand-navy) !important;
        }

        #dropdownSorting:active,
        #dropdownSorting.show,
        #dropdownSorting.active {
            background-color: #F1F5F9 !important;
            border-color: #94A3B8 !important;
            color: var(--text-navy) !important;
            box-shadow: none !important;
        }

        #dropdownSorting:active .sort-toggle-icon,
        #dropdownSorting.show .sort-toggle-icon,
        #dropdownSorting.active .sort-toggle-icon {
            color: var(--brand-navy) !important;
        }

        /* Mouse click focus: neutral, no blue glow */
        #dropdownSorting:focus {
            box-shadow: none !important;
            outline: none !important;
            background-color: #FFFFFF;
            border-color: var(--border-slate);
            color: var(--text-navy);
        }

        /* Keyboard-only focus ring (accessibility) */
        #dropdownSorting:focus-visible {
            outline: 2px solid var(--text-slate) !important;
            outline-offset: 2px !important;
            box-shadow: none !important;
        }

        .sort-dropdown-menu {
            width: 260px;
            min-width: 260px;
            max-width: calc(100vw - 32px);
            border: 1px solid var(--border-slate);
            border-radius: 10px;
            box-shadow: 0 12px 28px -6px rgba(11, 27, 58, 0.18);
            padding: 6px;
            font-size: 0.875rem;
        }

        .sort-dropdown-menu .dropdown-header {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: #94A3B8;
            padding: 6px 10px 3px;
            margin: 0;
        }

        .sort-dropdown-menu .dropdown-item {
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 0.84rem;
            font-weight: 500;
            color: var(--text-navy);
            transition: background-color 0.15s ease, color 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sort-dropdown-menu .dropdown-item:hover,
        .sort-dropdown-menu .dropdown-item:focus {
            background-color: #F1F5F9;
            color: var(--primary-blue);
            outline: none;
        }

        .sort-dropdown-menu .dropdown-item.active-sort {
            background-color: #EBF3FE !important;
            color: var(--brand-navy) !important;
            font-weight: 700 !important;
        }

        .sort-dropdown-menu .dropdown-item.active-sort .sort-check-icon {
            display: inline-block !important;
            color: var(--primary-blue);
            font-size: 1.05rem;
            line-height: 1;
            font-weight: 700;
        }

        .sort-dropdown-menu .dropdown-item .sort-check-icon {
            display: none;
        }

        .sort-dropdown-menu .dropdown-item.disabled-reset {
            opacity: 0.45;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Sort Chip */
        .sort-chip {
            background-color: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: var(--brand-navy);
            border-radius: 50rem;
            padding: 4px 10px 4px 12px;
            font-size: 0.8rem;
            font-weight: 500;
            line-height: 1.3;
            transition: all 0.2s ease;
        }

        .sort-chip strong {
            font-weight: 700;
            color: var(--primary-blue);
        }

        .btn-sort-chip-close {
            background: transparent;
            border: none;
            padding: 0;
            margin: 0;
            color: #64748B;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .btn-sort-chip-close:hover {
            background-color: #DBEAFE;
            color: var(--brand-navy);
        }

        .btn-sort-chip-close:focus-visible {
            outline: 2px solid var(--primary-blue);
        }

        .dropdown-menu {
            border: 1px solid var(--border-slate);
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(11, 27, 58, 0.12);
            padding: 6px;
            font-size: 0.875rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-navy);
            transition: background-color 0.15s ease;
        }

        .dropdown-item:hover {
            background-color: #F1F5F9;
            color: var(--primary-blue);
        }

        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 280px;
        }

        .search-wrapper .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            pointer-events: none;
            font-size: 0.9rem;
        }

        .search-wrapper .search-input {
            padding-left: 36px;
            padding-right: 14px;
            padding-top: 8px;
            padding-bottom: 8px;
            border-radius: 10px;
            border: 1px solid var(--border-slate);
            background-color: #FFFFFF;
            font-size: 0.9rem;
            color: var(--text-navy);
            transition: all 0.15s ease-in-out;
        }

        .search-wrapper .search-input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        /* Table Design */
        #tabelSiswa {
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0 !important;
        }

        #tabelSiswa thead th {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.08em !important;
            border: none !important;
            padding: 14px 16px !important;
        }

        #tabelSiswa tbody td {
            border-top: none !important;
            border-bottom: 1px solid #E2E8F0 !important;
            padding: 14px 16px !important;
            color: var(--text-navy);
            font-size: 0.925rem;
            background-color: #FFFFFF;
        }

        #tabelSiswa tbody tr {
            transition: background-color 0.15s ease;
        }

        #tabelSiswa tbody tr:hover td {
            background-color: #F0F4F9 !important;
        }

        /* Accent bar on row hover */
        #tabelSiswa tbody tr.row-major-rpl:hover td:first-child {
            border-left: 3px solid var(--major-rpl) !important;
        }

        #tabelSiswa tbody tr.row-major-broadcast:hover td:first-child {
            border-left: 3px solid var(--major-bc) !important;
        }

        #tabelSiswa tbody tr.row-major-animation:hover td:first-child {
            border-left: 3px solid var(--major-anim) !important;
        }

        /* NIS column font */
        .nis-code {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--brand-navy);
            letter-spacing: -0.01em;
        }

        /* Student Initials Avatar */
        .student-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .student-name {
            color: var(--text-navy);
            font-weight: 600;
            font-size: 0.925rem;
        }

        /* Major Pill Badges */
        .major-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 50rem;
            letter-spacing: 0.02em;
        }

        .major-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .badge-rpl {
            background-color: var(--major-rpl-bg) !important;
            color: var(--major-rpl-text) !important;
        }

        .badge-rpl .major-badge-dot {
            background-color: var(--major-rpl);
        }

        .badge-broadcast {
            background-color: var(--major-bc-bg) !important;
            color: var(--major-bc-text) !important;
        }

        .badge-broadcast .major-badge-dot {
            background-color: var(--major-bc);
        }

        .badge-animation {
            background-color: var(--major-anim-bg) !important;
            color: var(--major-anim-text) !important;
        }

        .badge-animation .major-badge-dot {
            background-color: var(--major-anim);
        }

        .badge-default {
            background-color: #F1F5F9 !important;
            color: #475569 !important;
        }

        .badge-default .major-badge-dot {
            background-color: #475569;
        }

        /* Action Icon Buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid var(--border-slate);
            background-color: #F8FAFC;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-action-edit {
            color: var(--primary-blue);
        }

        .btn-action-edit:hover {
            background-color: var(--primary-blue) !important;
            border-color: var(--primary-blue) !important;
            color: #FFFFFF !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.25);
        }

        .btn-action-delete {
            color: var(--color-danger);
        }

        .btn-action-delete:hover {
            background-color: var(--color-danger) !important;
            border-color: var(--color-danger) !important;
            color: #FFFFFF !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.25);
        }

        /* Disable DataTables default sorting icons */
        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:before,
        table.dataTable thead .sorting_desc:after {
            display: none !important;
        }

        table.dataTable thead th {
            background-image: none !important;
            padding-right: 12px !important;
            cursor: default !important;
        }

        /* Pagination & Info Controls */
        .pagination {
            margin-bottom: 0;
            gap: 4px;
        }

        .page-item .page-link {
            border: 1px solid var(--border-slate) !important;
            border-radius: 8px !important;
            color: var(--text-slate) !important;
            background-color: #FFFFFF !important;
            padding: 6px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.15s ease-in-out;
            box-shadow: none !important;
        }

        .page-item .page-link:hover {
            color: var(--primary-blue) !important;
            background-color: #F1F5F9 !important;
            border-color: #CBD5E1 !important;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--brand-navy) 0%, var(--brand-royal) 100%) !important;
            border-color: var(--brand-navy) !important;
            color: #FFFFFF !important;
        }

        .page-item.disabled .page-link {
            color: #94A3B8 !important;
            background-color: #F8FAFC !important;
            border-color: #E2E8F0 !important;
            opacity: 0.6;
        }

        .dataTables_info {
            color: var(--text-muted) !important;
            font-size: 0.85rem;
            font-weight: 500;
            padding-top: 0 !important;
        }

        /* Modals: Dark Backdrop & Crisp Surface */
        .modal-backdrop {
            background-color: #070F1E;
        }

        .modal-backdrop.show {
            opacity: 0.75;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            border: 1px solid var(--border-slate);
            border-radius: 14px;
            box-shadow: 0 25px 50px -12px rgba(11, 27, 58, 0.25);
            overflow: hidden;
        }

        .modal-header {
            background-color: #FFFFFF;
            border-bottom: 1px solid var(--border-slate);
            padding: 18px 24px;
        }

        .modal-footer {
            border-top: 1px solid var(--border-slate);
            padding: 14px 24px;
            background-color: #F8FAFC;
        }

        .modal-header-badge {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .modal-badge-blue {
            background-color: var(--major-rpl-bg);
            color: var(--primary-blue);
        }

        .modal-badge-teal {
            background-color: var(--major-anim-bg);
            color: var(--major-anim);
        }

        .modal-badge-red {
            background-color: #FEE2E2;
            color: var(--color-danger);
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-navy);
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-slate);
            border-radius: 10px;
            padding: 9px 13px;
            font-size: 0.925rem;
            color: var(--text-navy);
            transition: all 0.15s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .btn-primary {
            background-color: var(--primary-blue) !important;
            border-color: var(--primary-blue) !important;
            color: #FFFFFF !important;
            font-weight: 600;
            border-radius: 10px;
            padding: 9px 18px;
            transition: all 0.15s ease-in-out;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--primary-blue-hover) !important;
            border-color: var(--primary-blue-hover) !important;
        }

        .btn-outline-secondary {
            border-color: var(--border-slate) !important;
            color: var(--text-slate) !important;
            background-color: #FFFFFF !important;
            font-weight: 600;
            border-radius: 10px;
            padding: 9px 18px;
            transition: all 0.15s ease-in-out;
        }

        .btn-outline-secondary:hover {
            background-color: #F8FAFC !important;
            border-color: #94A3B8 !important;
            color: var(--text-navy) !important;
        }

        .btn-danger {
            background-color: var(--color-danger) !important;
            border-color: var(--color-danger) !important;
            color: #FFFFFF !important;
            font-weight: 600;
            border-radius: 10px;
            padding: 9px 18px;
            transition: all 0.15s ease-in-out;
        }

        .btn-danger:hover,
        .btn-danger:focus {
            background-color: var(--color-danger-hover) !important;
            border-color: var(--color-danger-hover) !important;
        }

        /* Delete Modal specific */
        .delete-icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #FEE2E2;
            color: var(--color-danger);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        /* Empty state icon */
        .empty-icon-box {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background-color: #F1F5F9;
            color: var(--brand-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>

<body>

    <!-- Navbar: Dark Navy -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#">
                <div class="logo-box">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <div class="brand-title">SMK Budi Luhur</div>
                    <div class="brand-subtitle">Student Information System</div>
                </div>
            </a>
            <div class="d-flex align-items-center">
                <div class="admin-pill d-flex align-items-center gap-2 px-3 py-1 rounded-pill">
                    <div class="position-relative">
                        <div class="admin-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <span class="online-dot"></span>
                    </div>
                    <span class="admin-label">Admin</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Container Utama -->
    <div class="container my-4 my-md-5">

        <!-- Flash Message Alerts -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert flash-card flash-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 p-3 alert-auto-dismiss" role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="fw-semibold"><?= esc(session()->getFlashdata('success')); ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert flash-card flash-error alert-dismissible fade show d-flex align-items-center gap-2 mb-4 p-3 alert-auto-dismiss" role="alert">
                <i class="bi bi-exclamation-octagon-fill fs-5 text-danger"></i>
                <div class="fw-semibold"><?= esc(session()->getFlashdata('error')); ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Hero Banner: Navy to Royal Blue with Orange CTA -->
        <div class="hero-card p-4 p-md-5 mb-4 animate-fade-in-up" style="animation-delay: 0.05s;">
            <div class="row align-items-center position-relative z-1 g-3">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 text-white small fw-bold mb-3 border border-white border-opacity-10">
                        <span class="badge-dot-hero"></span>
                        <span style="letter-spacing: 0.06em;">ADMINISTRATION DASHBOARD</span>
                    </div>
                    <h1 class="hero-title mb-2">Student Data</h1>
                    <p class="hero-subtitle mb-0">
                        Manage all registered students of SMK Budi Luhur across RPL, Broadcast, and Animation majors.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <button type="button" class="btn btn-hero-cta" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg"></i>
                        <span>Add Student Data</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stat Cards: Row of 4 -->
        <div class="row g-3 mb-4 animate-fade-in-up" style="animation-delay: 0.12s;">
            <!-- Stat: Total -->
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card stat-total p-3 d-flex align-items-center gap-3">
                    <div class="stat-icon-box stat-icon-total">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total Students</div>
                        <div class="stat-value count-up" data-target="<?= (int) $totalCount; ?>">0</div>
                    </div>
                </div>
            </div>

            <!-- Stat: RPL -->
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card stat-rpl p-3 d-flex align-items-center gap-3">
                    <div class="stat-icon-box stat-icon-rpl">
                        <i class="bi bi-code-slash"></i>
                    </div>
                    <div>
                        <div class="stat-label">RPL Department</div>
                        <div class="stat-value count-up" data-target="<?= (int) $rplCount; ?>">0</div>
                    </div>
                </div>
            </div>

            <!-- Stat: Broadcast -->
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card stat-broadcast p-3 d-flex align-items-center gap-3">
                    <div class="stat-icon-box stat-icon-broadcast">
                        <i class="bi bi-camera-video-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Broadcast</div>
                        <div class="stat-value count-up" data-target="<?= (int) $broadcastCount; ?>">0</div>
                    </div>
                </div>
            </div>

            <!-- Stat: Animation -->
            <div class="col-xl-3 col-sm-6">
                <div class="stat-card stat-animation p-3 d-flex align-items-center gap-3">
                    <div class="stat-icon-box stat-icon-animation">
                        <i class="bi bi-palette-fill"></i>
                    </div>
                    <div>
                        <div class="stat-label">Animation</div>
                        <div class="stat-value count-up" data-target="<?= (int) $animationCount; ?>">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card & Data Table -->
        <div class="card table-card animate-fade-in-up" style="animation-delay: 0.18s;">
            <!-- Toolbar -->
            <div class="card-header bg-white p-3 p-md-4 border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <!-- Left: Sorting Dropdown & Active Chip -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-2" type="button" id="dropdownSorting" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-arrow-down-up sort-toggle-icon"></i>
                                <span id="sortButtonLabel" class="text-truncate" style="max-width: 170px;">Sort Data</span>
                            </button>
                            <ul class="dropdown-menu sort-dropdown-menu shadow" aria-labelledby="dropdownSorting">
                                <li>
                                    <h6 class="dropdown-header">NAME</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item sort-item" href="#" id="sortNamaAZ" data-sort="sortNamaAZ">
                                        <span class="d-flex align-items-center text-truncate">
                                            <i class="bi bi-sort-alpha-down me-2" style="color: var(--primary-blue);"></i>Name (A → Z)
                                        </span>
                                        <i class="bi bi-check2 sort-check-icon"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item sort-item" href="#" id="sortNamaZA" data-sort="sortNamaZA">
                                        <span class="d-flex align-items-center text-truncate">
                                            <i class="bi bi-sort-alpha-down-alt me-2" style="color: var(--primary-blue);"></i>Name (Z → A)
                                        </span>
                                        <i class="bi bi-check2 sort-check-icon"></i>
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>

                                <li>
                                    <h6 class="dropdown-header">NIS</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item sort-item" href="#" id="sortNisAsc" data-sort="sortNisAsc">
                                        <span class="d-flex align-items-center text-truncate">
                                            <i class="bi bi-sort-numeric-down me-2" style="color: var(--accent-orange);"></i>NIS (Low → High)
                                        </span>
                                        <i class="bi bi-check2 sort-check-icon"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item sort-item" href="#" id="sortNisDesc" data-sort="sortNisDesc">
                                        <span class="d-flex align-items-center text-truncate">
                                            <i class="bi bi-sort-numeric-down-alt me-2" style="color: var(--accent-orange);"></i>NIS (High → Low)
                                        </span>
                                        <i class="bi bi-check2 sort-check-icon"></i>
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>

                                <li>
                                    <h6 class="dropdown-header">CLASS</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item sort-item" href="#" id="sortClass" data-sort="sortClass">
                                        <span class="d-flex align-items-center text-truncate">
                                            <i class="bi bi-layers-fill me-2" style="color: var(--major-anim);"></i>Class / Major
                                        </span>
                                        <i class="bi bi-check2 sort-check-icon"></i>
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>

                                <li>
                                    <h6 class="dropdown-header">DATE ADDED</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item sort-item" href="#" id="sortNewest" data-sort="sortNewest">
                                        <span class="d-flex align-items-center text-truncate">
                                            <i class="bi bi-clock-history me-2" style="color: var(--text-slate);"></i>Newest first
                                        </span>
                                        <i class="bi bi-check2 sort-check-icon"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item sort-item" href="#" id="sortOldest" data-sort="sortOldest">
                                        <span class="d-flex align-items-center text-truncate">
                                            <i class="bi bi-calendar-event me-2" style="color: var(--text-slate);"></i>Oldest first
                                        </span>
                                        <i class="bi bi-check2 sort-check-icon"></i>
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>

                                <li>
                                    <a class="dropdown-item disabled-reset" href="#" id="sortReset">
                                        <span class="d-flex align-items-center text-truncate text-muted">
                                            <i class="bi bi-arrow-counterclockwise me-2" style="color: var(--text-slate);"></i>Reset to default
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Active Sort Chip (hidden in default state) -->
                        <div id="sortActiveChip" class="sort-chip d-none align-items-center gap-2">
                            <span class="sort-chip-text text-truncate" style="max-width: 220px;">
                                Sorted by: <strong id="sortChipLabel"></strong>
                            </span>
                            <button type="button" class="btn-sort-chip-close" id="btnResetSortChip" aria-label="Reset sorting">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Right: Search Bar -->
                    <div class="search-wrapper position-relative">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="customSearch" class="form-control search-input" placeholder="Search NIS, name, class...">
                    </div>
                </div>
            </div>

            <!-- Table Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="tabelSiswa" class="table table-hover align-middle w-100 mb-0">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 6%;">NO</th>
                                <th scope="col" style="width: 18%;">NIS</th>
                                <th scope="col" style="width: 42%;">STUDENT NAME</th>
                                <th scope="col" style="width: 20%;">CLASS / MAJOR</th>
                                <th scope="col" style="width: 14%;" class="text-end pe-3">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($siswa)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($siswa as $row): ?>
                                    <?php
                                    $majorClass = 'row-major-rpl';
                                    $badgeClass = 'badge-rpl';
                                    if (stripos($row['kelas'], 'RPL') !== false) {
                                        $majorClass = 'row-major-rpl';
                                        $badgeClass = 'badge-rpl';
                                    } elseif (stripos($row['kelas'], 'Broadcast') !== false || stripos($row['kelas'], 'BC') !== false) {
                                        $majorClass = 'row-major-broadcast';
                                        $badgeClass = 'badge-broadcast';
                                    } elseif (stripos($row['kelas'], 'Animation') !== false || stripos($row['kelas'], 'ANM') !== false) {
                                        $majorClass = 'row-major-animation';
                                        $badgeClass = 'badge-animation';
                                    }

                                    // Compute initials from first two words of the name
                                    $nameParts = preg_split('/\s+/', trim($row['nama']));
                                    $initials = '';
                                    if (!empty($nameParts[0])) $initials .= mb_substr($nameParts[0], 0, 1);
                                    if (!empty($nameParts[1])) $initials .= mb_substr($nameParts[1], 0, 1);
                                    $initials = strtoupper($initials);
                                    ?>
                                    <tr class="<?= $majorClass; ?>">
                                        <td class="text-muted fw-semibold ps-3" data-order="<?= sprintf('%04d', $no); ?>"><?= $no++; ?></td>
                                        <td class="nis-code" data-order="<?= esc($row['nis']); ?>"><?= esc($row['nis']); ?></td>
                                        <td data-order="<?= esc(strtolower($row['nama'])); ?>">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="student-avatar <?= $badgeClass; ?>">
                                                    <?= esc($initials); ?>
                                                </div>
                                                <span class="student-name"><?= esc($row['nama']); ?></span>
                                            </div>
                                        </td>
                                        <td data-search="<?= esc($toRomanClass($row['kelas']) . ' ' . $row['kelas']); ?>" data-order="<?= esc($getClassOrderKey($row['kelas'])); ?>">
                                            <span class="badge major-badge <?= $badgeClass; ?>">
                                                <span class="major-badge-dot"></span>
                                                <?= esc($toRomanClass($row['kelas'])); ?>
                                            </span>
                                            <span class="visually-hidden"><?= esc($row['kelas']); ?></span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <!-- Edit Button -->
                                                <button type="button"
                                                    class="btn-action btn-action-edit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEdit<?= esc($row['id']); ?>"
                                                    title="Edit student"
                                                    aria-label="Edit student">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <a href="<?= base_url('home/hapus/' . esc($row['id'])); ?>"
                                                    class="btn-action btn-action-delete btn-delete-trigger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDelete"
                                                    data-id="<?= esc($row['id']); ?>"
                                                    data-nama="<?= esc($row['nama']); ?>"
                                                    data-nis="<?= esc($row['nis']); ?>"
                                                    data-href="<?= base_url('home/hapus/' . esc($row['id'])); ?>"
                                                    title="Delete student"
                                                    aria-label="Delete student">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Shared Modal Konfirmasi Hapus Data Siswa -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content text-center p-3">
                <div class="modal-body pt-3 pb-2">
                    <div class="delete-icon-circle mx-auto">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h5 class="modal-title fw-bold mb-2" id="modalDeleteLabel" style="color: var(--text-navy);">Delete student?</h5>
                    <p class="text-muted small mb-0">
                        You are about to delete <strong id="deleteStudentNama" class="text-dark"></strong> (NIS <span id="deleteStudentNis" class="text-dark fw-semibold"></span>). This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center gap-2 pt-3 pb-2 bg-transparent">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" id="btnCancelDelete">Cancel</button>
                    <a href="#" id="btnConfirmDelete" class="btn btn-danger d-inline-flex align-items-center gap-2 px-4">
                        <i class="bi bi-trash"></i> Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals Edit Data Siswa (Rendered outside table for valid HTML) -->
    <?php if (!empty($siswa)): ?>
        <?php foreach ($siswa as $row): ?>
            <?php
            $currentMajor = '';
            if (stripos($row['kelas'], 'RPL') !== false) {
                $currentMajor = 'RPL';
            } elseif (stripos($row['kelas'], 'Broadcast') !== false || stripos($row['kelas'], 'BC') !== false) {
                $currentMajor = 'Broadcast';
            } elseif (stripos($row['kelas'], 'Animation') !== false || stripos($row['kelas'], 'ANM') !== false) {
                $currentMajor = 'Animation';
            }
            ?>
            <div class="modal fade" id="modalEdit<?= esc($row['id']); ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= esc($row['id']); ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header d-flex align-items-center gap-3">
                            <div class="modal-header-badge modal-badge-teal">
                                <i class="bi bi-pencil-fill"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold m-0" id="modalEditLabel<?= esc($row['id']); ?>" style="color: var(--text-navy);">Edit Student Data</h5>
                                <div class="text-muted small">Update student information or transfer major</div>
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?= base_url('home/edit/' . esc($row['id'])); ?>" method="POST">
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label">NIS (National Student ID)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-end-0">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="text" class="form-control bg-light text-muted border-start-0 nis-code" value="<?= esc($row['nis']); ?>" readonly disabled>
                                    </div>
                                    <div class="form-text text-muted small">NIS will change automatically if the major is transferred.</div>
                                    <div class="form-text text-warning d-none major-change-warning small mt-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Major changed: NIS will be regenerated when you save.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="nama" class="form-control" value="<?= esc($row['nama']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Class / Major</label>
                                    <select class="form-select edit-kelas-select" data-original-major="<?= esc($currentMajor); ?>" name="kelas" required>
                                        <option value="" disabled>Select class...</option>
                                        <?php foreach ($classList as $group => $classes): ?>
                                            <optgroup label="<?= esc($group); ?>">
                                                <?php foreach ($classes as $cls): ?>
                                                    <option value="<?= esc($cls); ?>" <?= ($row['kelas'] === $cls) ? 'selected' : ''; ?>>
                                                        <?= esc($toRomanClass($cls)); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Modal Form Tambah Data Siswa -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center gap-3">
                    <div class="modal-header-badge modal-badge-blue">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0" id="modalTambahLabel" style="color: var(--text-navy);">Add Student Data</h5>
                        <div class="text-muted small">Register a new student into the system</div>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('home/tambah'); ?>" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="text" class="form-control bg-light text-muted border-start-0" value="Auto-generated by system" readonly disabled>
                            </div>
                            <div class="form-text text-muted small">NIS will be automatically generated upon saving based on the chosen major.</div>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Full Name</label>
                            <input type="text" name="nama" id="nama" class="form-control" placeholder="Enter Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="kelas" class="form-label">Class / Major</label>
                            <select class="form-select" name="kelas" id="kelas" required>
                                <option value="" disabled selected>Select class...</option>
                                <?php foreach ($classList as $group => $classes): ?>
                                    <optgroup label="<?= esc($group); ?>">
                                        <?php foreach ($classes as $cls): ?>
                                            <option value="<?= esc($cls); ?>"><?= esc($toRomanClass($cls)); ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CDN JS jQuery, Bootstrap 5, & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Inisialisasi DataTables, Counters & Dropdown Sorting Script -->
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            var table = $('#tabelSiswa').DataTable({
                "dom": 'rt<"d-flex flex-column flex-md-row justify-content-between align-items-center p-3 p-md-4 gap-2 border-top"ip>',
                "pageLength": 5,
                "bLengthChange": false,
                "columnDefs": [{
                    "orderable": false,
                    "targets": "_all"
                }],
                "language": {
                    "zeroRecords": '<div class="py-5 text-center"><div class="empty-icon-box mx-auto mb-3"><i class="bi bi-search"></i></div><h6 class="fw-bold text-navy mb-1">No students found</h6><p class="text-muted small mb-0">Try adjusting your search query or sorting options.</p></div>',
                    "info": "Showing page _PAGE_ of _PAGES_",
                    "infoEmpty": '<div class="py-3 text-center text-muted small">No records available</div>',
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "paginate": {
                        "first": '<i class="bi bi-chevron-double-left"></i>',
                        "last": '<i class="bi bi-chevron-double-right"></i>',
                        "next": '<i class="bi bi-chevron-right"></i>',
                        "previous": '<i class="bi bi-chevron-left"></i>'
                    }
                }
            });

            // Hubungkan Custom Search Input dengan DataTables
            $('#customSearch').on('keyup input', function() {
                table.search(this.value).draw();
            });

            // Helper untuk mengecek jurusan dari nama kelas
            function getMajorFromClass(className) {
                if (!className) return '';
                if (className.indexOf('RPL') !== -1) return 'RPL';
                if (className.indexOf('Broadcast') !== -1 || className.indexOf('BC') !== -1) return 'Broadcast';
                if (className.indexOf('Animation') !== -1 || className.indexOf('ANM') !== -1) return 'Animation';
                return '';
            }

            // Peringatan perubahan jurusan di Modal Edit
            $(document).on('change', '.edit-kelas-select', function() {
                var originalMajor = $(this).data('original-major');
                var newClass = $(this).val();
                var newMajor = getMajorFromClass(newClass);
                var warningEl = $(this).closest('.modal-content').find('.major-change-warning');

                if (originalMajor && newMajor && originalMajor !== newMajor) {
                    warningEl.removeClass('d-none');
                } else {
                    warningEl.addClass('d-none');
                }
            });

            // Event delegation untuk Delete Trigger agar href tidak langsung redirect
            $(document).on('click', '.btn-delete-trigger', function(e) {
                e.preventDefault();
            });

            // Handle data attributes saat modalDelete dibuka
            var modalDeleteEl = document.getElementById('modalDelete');
            if (modalDeleteEl) {
                modalDeleteEl.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    if (!button) return;

                    var nama = button.getAttribute('data-nama') || '';
                    var nis = button.getAttribute('data-nis') || '';
                    var href = button.getAttribute('data-href') || button.getAttribute('href') || '#';

                    // Gunakan textContent untuk menghindari XSS
                    var namaEl = document.getElementById('deleteStudentNama');
                    var nisEl = document.getElementById('deleteStudentNis');
                    var confirmBtn = document.getElementById('btnConfirmDelete');

                    if (namaEl) namaEl.textContent = nama;
                    if (nisEl) nisEl.textContent = nis;
                    if (confirmBtn) confirmBtn.setAttribute('href', href);
                });

                // Fokus pada tombol Cancel saat modal terbuka (safer default)
                modalDeleteEl.addEventListener('shown.bs.modal', function() {
                    var cancelBtn = document.getElementById('btnCancelDelete');
                    if (cancelBtn) cancelBtn.focus();
                });
            }

            // --- Sorting State & Handlers ---
            var currentSort = 'default';

            var sortConfigs = {
                'sortNamaAZ': {
                    col: 2,
                    dir: 'asc',
                    buttonText: 'Sort: Name (A → Z)',
                    chipText: 'Name (A → Z)'
                },
                'sortNamaZA': {
                    col: 2,
                    dir: 'desc',
                    buttonText: 'Sort: Name (Z → A)',
                    chipText: 'Name (Z → A)'
                },
                'sortNisAsc': {
                    col: 1,
                    dir: 'asc',
                    buttonText: 'Sort: NIS (Low → High)',
                    chipText: 'NIS (Low → High)'
                },
                'sortNisDesc': {
                    col: 1,
                    dir: 'desc',
                    buttonText: 'Sort: NIS (High → Low)',
                    chipText: 'NIS (High → Low)'
                },
                'sortClass': {
                    col: 3,
                    dir: 'asc',
                    buttonText: 'Sort: Class / Major',
                    chipText: 'Class / Major'
                },
                'sortNewest': {
                    col: 0,
                    dir: 'desc',
                    buttonText: 'Sort: Newest first',
                    chipText: 'Newest first'
                },
                'sortOldest': {
                    col: 0,
                    dir: 'asc',
                    buttonText: 'Sort: Oldest first',
                    chipText: 'Oldest first'
                }
            };

            function updateSortUI(sortKey) {
                if (sortKey === 'default') {
                    // Button label
                    $('#sortButtonLabel').text('Sort Data');

                    // Chip
                    $('#sortActiveChip').removeClass('d-inline-flex').addClass('d-none');
                    $('#sortChipLabel').text('');

                    // Menu items
                    $('.sort-dropdown-menu .sort-item').removeClass('active-sort');

                    // Reset button disabled
                    $('#sortReset').addClass('disabled-reset');
                } else {
                    var config = sortConfigs[sortKey];
                    if (!config) return;

                    // Button label
                    $('#sortButtonLabel').text(config.buttonText);

                    // Chip
                    $('#sortChipLabel').text(config.chipText);
                    $('#sortActiveChip').removeClass('d-none').addClass('d-inline-flex');

                    // Menu items active state
                    $('.sort-dropdown-menu .sort-item').removeClass('active-sort');
                    $('#' + sortKey).addClass('active-sort');

                    // Reset button enabled
                    $('#sortReset').removeClass('disabled-reset');
                }
            }

            function closeSortDropdown() {
                var dropdownEl = document.getElementById('dropdownSorting');
                if (dropdownEl) {
                    var bsDropdown = bootstrap.Dropdown.getInstance(dropdownEl);
                    if (bsDropdown) {
                        bsDropdown.hide();
                    }
                }
            }

            function applySort(sortKey) {
                var config = sortConfigs[sortKey];
                if (!config) return;

                currentSort = sortKey;
                // Go back to page 1 and apply sort
                table.order([config.col, config.dir]).page(0).draw();
                updateSortUI(sortKey);
                closeSortDropdown();
            }

            function resetSort() {
                currentSort = 'default';
                // Go back to page 1 and default sort (No column 0, asc)
                table.order([0, 'asc']).page(0).draw();
                updateSortUI('default');
                closeSortDropdown();
            }

            // Click handlers for each sort option
            $('.sort-dropdown-menu').on('click', '.sort-item', function(e) {
                e.preventDefault();
                var sortKey = $(this).attr('data-sort') || $(this).attr('id');
                applySort(sortKey);
            });

            // Reset sort click handler
            $('#sortReset').on('click', function(e) {
                e.preventDefault();
                if (currentSort === 'default') return;
                resetSort();
            });

            // Chip close (x) button click handler
            $('#btnResetSortChip').on('click', function(e) {
                e.preventDefault();
                resetSort();
            });

            // Animasi Count-Up untuk Stat Cards
            var counters = document.querySelectorAll('.count-up');
            counters.forEach(function(counter) {
                var target = parseInt(counter.getAttribute('data-target'), 10) || 0;
                if (target === 0) {
                    counter.textContent = '0';
                    return;
                }
                var count = 0;
                var duration = 600;
                var stepTime = Math.max(Math.floor(duration / target), 25);
                var timer = setInterval(function() {
                    count += 1;
                    counter.textContent = count;
                    if (count >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    }
                }, stepTime);
            });

            // Auto-dismiss alert setelah 5 detik
            setTimeout(function() {
                var alertEl = document.querySelector('.alert-auto-dismiss');
                if (alertEl) {
                    var alert = bootstrap.Alert.getOrCreateInstance(alertEl);
                    alert.close();
                }
            }, 5000);
        });
    </script>
</body>

</html>