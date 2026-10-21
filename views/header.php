<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? $title : 'E-Cuti'; ?></title>
    <link rel="stylesheet" href="style.css?v=1.2">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="brand">
                <h2>E-Cuti</h2>
                <p>Administrator</p>
                <span class="badge-role">Admin</span>
            </div>
            
            <ul class="nav-menu">
                <li class="menu-header">MAIN MENU</li>
                <li class="<?= ($_GET['controller'] ?? '') == 'dashboard' ? 'active' : ''; ?>">
                    <a href="index.php?controller=dashboard&action=index">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="<?= ($_GET['controller'] ?? '') == 'pegawai' ? 'active' : ''; ?>">
                    <a href="index.php?controller=pegawai&action=index">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>Data Pegawai</span>
                    </a>
                </li>
                <li class="<?= (($_GET['controller'] ?? '') == 'cuti' && ($_GET['action'] ?? '') == 'jenis') ? 'active' : ''; ?>">
                    <a href="index.php?controller=cuti&action=jenis">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                        <span>Jenis Cuti</span>
                    </a>
                </li>
                
                <li class="menu-header">CUTI & LAPORAN</li>
                <li class="<?= (($_GET['controller'] ?? '') == 'cuti' && ($_GET['action'] ?? '') == 'create') ? 'active' : ''; ?>">
                    <a href="index.php?controller=cuti&action=create">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                        <span>Ajukan Cuti</span>
                    </a>
                </li>
                <li class="<?= (($_GET['controller'] ?? '') == 'cuti' && ($_GET['action'] ?? '') == 'history') ? 'active' : ''; ?>">
                    <a href="index.php?controller=cuti&action=history">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Riwayat Cuti</span>
                    </a>
                </li>
            </ul>

            <div class="logout-wrapper">
                <a href="index.php?controller=auth&action=logout" class="btn-logout">
                    <svg class="logout-icon" viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </a>
            </div>
        </aside>

        <main class="main-content">