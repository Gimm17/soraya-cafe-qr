    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        // Brand tokens: Soraya Blue
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        bone: '#F0F4FF',
                        ink: '#0B1020',
                        muted: '#5A647A',
                        line: 'rgba(30,58,138,.12)',
                        primary: {
                            50:  '#EFF6FF',
                            100: '#DBEAFE',
                            200: '#BFDBFE',
                            300: '#93C5FD',
                            400: '#60A5FA',
                            500: '#4169E1',
                            600: '#1E3A8A',
                            700: '#1E40AF',
                            800: '#1E3A8A',
                            900: '#1E3A8A',
                        }
                    },
                    boxShadow: {
                        soft: '0 10px 30px rgba(30,58,138,.12)',
                        soft2: '0 6px 18px rgba(30,58,138,.10)',
                    }
                }
            }
        }
    </script>

    <style>
        :root{
            --bone: #F0F4FF;
            --ink: #0B1020;
            --muted: #5A647A;
            --line: rgba(30,58,138,.12);
            --soraya-blue: #1E3A8A;
            --soraya-blue-mid: #1E40AF;
        }
        html, body{ font-family: 'Inter', sans-serif; }

        /* ── Core UI Components ── */
        .ui-card{ background:#ffffff; border:1px solid var(--line); border-radius:16px; box-shadow:0 4px 16px rgba(30,58,138,.06); }
        .ui-card-flat{ background:#ffffff; border:1px solid var(--line); border-radius:14px; box-shadow:0 2px 10px rgba(30,58,138,.05); }
        .ui-chip{ border:1px solid var(--line); background:rgba(255,255,255,.80); border-radius:999px; }
        .ui-focus:focus{ outline:none; box-shadow:0 0 0 3px rgba(65,105,225,.20); border-color:rgba(65,105,225,.50); }
        .ui-btn{ border-radius:12px; }
        .ui-divider{ border-color: var(--line); }
        .tap-44{ min-height:44px; }

        /* ── Admin Page Header (large title in content body) ── */
        .admin-page-header {
            margin-bottom: 1.5rem;
        }
        .admin-page-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--soraya-blue);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .admin-page-header p {
            font-size: 0.85rem;
            color: var(--muted);
            margin-top: 0.25rem;
        }

        /* ── Admin KPI Cards ── */
        .kpi-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 2px 12px rgba(30,58,138,.06);
        }
        .kpi-card .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #EEF2FF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .kpi-card .kpi-icon svg { color: var(--soraya-blue); }
        .kpi-card .kpi-badge {
            font-size: 11px;
            color: var(--muted);
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 2px 10px;
        }
        .kpi-card .kpi-label { font-size: 12px; color: var(--muted); margin-top: 12px; }
        .kpi-card .kpi-value { font-size: 1.5rem; font-weight: 800; color: #0B1020; letter-spacing: -0.02em; }

        /* ── Admin Table Styles ── */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th {
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            padding: 10px 16px;
            border-bottom: 1px solid var(--line);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .admin-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--line);
            font-size: 14px;
            color: #0B1020;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background: #F8FAFF; }

        /* ── Kanban Column Headers (Orders page) ── */
        .kanban-header {
            background: var(--soraya-blue);
            color: #ffffff;
            padding: 12px 16px;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .kanban-header .kanban-count {
            background: rgba(255,255,255,.22);
            color: #ffffff;
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 13px;
            font-weight: 700;
        }
        .kanban-col {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(30,58,138,.06);
        }

        /* ── Primary Button ── */
        .btn-primary {
            background: var(--soraya-blue);
            color: #ffffff;
            font-weight: 600;
            border-radius: 12px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            transition: background .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-primary:hover { background: var(--soraya-blue-mid); }
        .btn-secondary {
            background: #ffffff;
            color: #374151;
            font-weight: 600;
            border-radius: 12px;
            padding: 10px 20px;
            border: 1px solid var(--line);
            cursor: pointer;
            transition: background .2s;
        }
        .btn-secondary:hover { background: #F8FAFF; }

        /* ── Select/Input Admin ── */
        .admin-select {
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #ffffff;
            color: #0B1020;
            font-size: 14px;
            min-height: 44px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%235A647A'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 36px;
        }
        .admin-select:focus { outline: none; border-color: rgba(65,105,225,.5); box-shadow: 0 0 0 3px rgba(65,105,225,.15); }

        /* ── Status Badges ── */
        .badge { display: inline-flex; align-items: center; border-radius: 999px; font-size: 11px; font-weight: 700; padding: 3px 10px; }
        .badge-blue { background: #DBEAFE; color: #1E3A8A; }
        .badge-green { background: #D1FAE5; color: #065F46; }
        .badge-amber { background: #FEF3C7; color: #92400E; }
        .badge-gray { background: #F3F4F6; color: #374151; }
        .badge-red { background: #FEE2E2; color: #991B1B; }

        /* ── Section Card with colored header strip ── */
        .section-card { background: #fff; border: 1px solid var(--line); border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(30,58,138,.06); }
        .section-card-header { padding: 14px 20px; border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; }
        .section-card-header h3 { font-weight: 700; font-size: 15px; color: #0B1020; }
        .section-card-header p { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .section-card-body { padding: 16px 20px; }

        /* ── Cafe Status Bar ── */
        .cafe-status-bar {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(30,58,138,.06);
            margin-bottom: 1.5rem;
        }

        /* Tailwind CDN line-clamp */
        .line-clamp-1, .line-clamp-2{ display:-webkit-box; -webkit-box-orient:vertical; overflow:hidden; }
        .line-clamp-1{ -webkit-line-clamp:1; }
        .line-clamp-2{ -webkit-line-clamp:2; }
    </style>
