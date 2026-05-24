<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

date_default_timezone_set('Africa/Lagos');
$hour = (int) date('H');
if ($hour < 12)     { $greeting = "Good Morning";   $greet_icon = "🌅"; }
elseif ($hour < 16) { $greeting = "Good Afternoon"; $greet_icon = "☀️"; }
else                { $greeting = "Good Evening";   $greet_icon = "🌙"; }

$admin_name = isset($_SESSION['name'])
    ? ucwords(strtolower($_SESSION['name']))
    : 'Administrator';

$msg = ""; $msg_type = "success";

/* ── ACTIONS ──────────────────────────────────────────────── */
if (isset($_GET['delete_student'])) {
    $id  = intval($_GET['delete_student']);
    $msg = mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role='student'")
         ? "Student account deleted successfully." : "Error deleting student.";
    if (strpos($msg,'Error') !== false) $msg_type = "error";
}
if (isset($_GET['delete_teacher'])) {
    $id  = intval($_GET['delete_teacher']);
    $msg = mysqli_query($conn, "DELETE FROM teachers WHERE id=$id")
         ? "Teacher account removed successfully." : "Error removing teacher.";
    if (strpos($msg,'Error') !== false) $msg_type = "error";
}
if (isset($_GET['approve_teacher'])) {
    $id  = intval($_GET['approve_teacher']);
    $msg = mysqli_query($conn, "UPDATE teachers SET status='approved' WHERE id=$id")
         ? "Teacher approved successfully." : "Error approving teacher.";
    if (strpos($msg,'Error') !== false) $msg_type = "error";
}

/* ── METRICS ──────────────────────────────────────────────── */
$students_total  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM users WHERE role='student'"))['t'] ?? 0;
$pending_total   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM teachers WHERE status='pending'"))['t'] ?? 0;
$approved_total  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM teachers WHERE status='approved'"))['t'] ?? 0;
$exams_total     = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM courses"))['t'] ?? 0;
$results_total   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM results"))['t'] ?? 0;
$questions_total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM questions"))['t'] ?? 0;

/* ── RECENT RESULTS ───────────────────────────────────────── */
$recent_results = mysqli_query($conn,
    "SELECT r.student_name, r.score, r.total_questions, r.date_taken,
            c.course_code, c.course_name
     FROM results r JOIN courses c ON r.course_id = c.id
     ORDER BY r.date_taken DESC LIMIT 5");

$current_view = $_GET['view'] ?? 'pending';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — RECTEM CBT</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel=" icon" href="../assets/images/launcher_iconn.png" type="image/png">
    <style>
    :root {
        --navy:       #0a1628;
        --navy-mid:   #112240;
        --navy-light: #1d3461;
        --gold:       #f4a916;
        --gold-light: #ffc94a;
        --gold-pale:  #fff8e7;
        --white:      #ffffff;
        --bg:         #f4f7fb;
        --border:     #e2e8f0;
        --text:       #1e293b;
        --muted:      #64748b;
        --success:    #22c55e;
        --warning:    #f59e0b;
        --danger:     #ef4444;
        --info:       #3b82f6;
        --sidebar-w:  264px;
        --topbar-h:   68px;
    }
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior:smooth; }
    body { font-family:'Sora',sans-serif; background:var(--bg); color:var(--text); display:flex; min-height:100vh; }

    /* ── SIDEBAR ───────────────────────────────────────────── */
    .sidebar {
        width: var(--sidebar-w);
        background: var(--navy-mid);
        position: fixed; top:0; left:0; bottom:0;
        z-index: 300;
        display: flex; flex-direction: column;
        border-right: 1px solid rgba(244,169,22,0.1);
        transition: transform .3s cubic-bezier(.4,0,.2,1);
    }
    .sb-head {
        padding: 22px 22px 18px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        flex-shrink: 0;
    }
    .sb-brand { display:flex; align-items:center; gap:10px; }
    .sb-logo { width:40px; height:40px; background:var(--gold); border-radius:10px;
        display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
    .sb-brand-name { font-size:14px; font-weight:800; color:var(--white); font-family:'Sora',sans-serif; }
    .sb-brand-name span { color:var(--gold); }
    .sb-brand-sub { font-size:10px; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:#8899aa; }

    .sb-section-label {
        font-size:10px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase;
        color:rgba(136,153,170,0.55); padding:18px 22px 8px;
    }
    .sidebar ul { list-style:none; padding:0 12px; flex:1; overflow-y:auto; }
    .sidebar ul li { margin-bottom:3px; }
    .sidebar ul li a {
        display:flex; align-items:center; gap:12px; padding:11px 14px;
        border-radius:10px; color:#94a3b8; text-decoration:none;
        font-size:13.5px; font-weight:500; font-family:'Sora',sans-serif;
        transition:background .18s,color .18s,padding-left .18s; position:relative;
    }
    .sidebar ul li a:hover { background:rgba(255,255,255,0.05); color:var(--white); padding-left:18px; }
    .sidebar ul li a.active {
        background:linear-gradient(90deg,rgba(244,169,22,0.18),rgba(244,169,22,0.05));
        color:var(--gold); font-weight:600; border-left:3px solid var(--gold);
    }
    .sidebar ul li a.active:hover { padding-left:14px; }
    .nav-icon { width:32px; height:32px; border-radius:8px; background:rgba(255,255,255,0.04);
        display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; transition:background .18s; }
    .sidebar ul li a.active .nav-icon { background:rgba(244,169,22,0.18); color:var(--gold); }
    .sidebar ul li a:hover .nav-icon { background:rgba(255,255,255,0.08); }
    .nav-badge { margin-left:auto; background:var(--danger); color:#fff; font-size:10px;
        font-weight:700; padding:2px 7px; border-radius:20px; font-family:'Sora',sans-serif; }
    .nav-badge.green { background:var(--success); }

    .sidebar-logout { padding:0 12px 12px; flex-shrink:0; }
    .sidebar-logout a { display:flex; align-items:center; gap:12px; padding:11px 14px;
        border-radius:10px; color:#f87171; text-decoration:none; font-size:13.5px; font-weight:500;
        transition:background .18s; }
    .sidebar-logout a:hover { background:rgba(248,113,113,0.1); }
    .sidebar-logout .nav-icon { background:rgba(248,113,113,0.08); color:#f87171; }

    .sb-foot { padding:14px 22px; border-top:1px solid rgba(255,255,255,0.07);
        display:flex; align-items:center; gap:10px; flex-shrink:0; }
    .sf-av { width:34px; height:34px; background:linear-gradient(135deg,var(--navy-light),var(--gold));
        border-radius:50%; display:flex; align-items:center; justify-content:center;
        font-size:14px; font-weight:800; color:var(--white); flex-shrink:0; }
    .sf-info { overflow:hidden; }
    .sf-name { font-size:13px; font-weight:700; color:var(--white); white-space:nowrap;
        overflow:hidden; text-overflow:ellipsis; }
    .sf-role { font-size:10px; color:var(--gold); font-weight:600; letter-spacing:.8px; text-transform:uppercase; }
    .sf-dot { width:7px; height:7px; background:var(--success); border-radius:50%;
        margin-left:auto; flex-shrink:0; box-shadow:0 0 6px var(--success); animation:pdot 2.5s infinite; }
    @keyframes pdot { 0%,100%{box-shadow:0 0 4px var(--success);}50%{box-shadow:0 0 12px var(--success),0 0 20px rgba(34,197,94,.3);} }

    /* ── TOPBAR ────────────────────────────────────────────── */
    .topbar {
        position: fixed; top:0; left:var(--sidebar-w); right:0; height:var(--topbar-h);
        z-index:200; background:var(--navy);
        border-bottom:1px solid rgba(244,169,22,0.15);
        display:flex; align-items:center; justify-content:space-between;
        padding:0 32px;
        box-shadow:0 2px 20px rgba(0,0,0,0.2);
    }
    .topbar-left { display:flex; flex-direction:column; gap:2px; }
    .topbar-greeting-row { display:flex; align-items:center; gap:8px; }
    .topbar-icon-box { width:34px; height:34px; border-radius:8px;
        background:rgba(244,169,22,0.12); display:flex; align-items:center;
        justify-content:center; font-size:17px; }
    .topbar-greet { font-size:11px; font-weight:700; letter-spacing:.8px;
        text-transform:uppercase; color:var(--muted); }
    .topbar-name { font-size:17px; font-weight:800; color:var(--white); letter-spacing:-.3px; }
    .topbar-right { display:flex; align-items:center; gap:14px; }
    .topbar-time {
        font-family:'JetBrains Mono',monospace; font-size:13px; font-weight:500;
        color:var(--muted); background:rgba(255,255,255,0.05);
        padding:7px 14px; border-radius:8px; border:1px solid rgba(255,255,255,0.07);
        min-width:150px; text-align:center;
    }
    .admin-badge {
        display:flex; align-items:center; gap:8px;
        background:rgba(244,169,22,0.1); border:1px solid rgba(244,169,22,0.25);
        padding:7px 14px; border-radius:30px;
    }
    .admin-av { width:28px; height:28px; background:linear-gradient(135deg,var(--navy-light),var(--gold));
        border-radius:50%; display:flex; align-items:center; justify-content:center;
        font-size:12px; font-weight:800; color:var(--white); }
    .badge-role { font-size:9px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:var(--gold); }
    .badge-name-sm { font-size:12px; font-weight:600; color:rgba(255,255,255,.75); white-space:nowrap; }
    .online-dot { width:8px; height:8px; background:var(--success); border-radius:50;
        box-shadow:0 0 6px var(--success); animation:pdot 2.5s infinite; }

    /* ── MAIN CONTENT ──────────────────────────────────────── */
    .main-content { margin-left:var(--sidebar-w); padding-top:var(--topbar-h); flex:1; padding-left:36px; padding-right:36px; padding-bottom:60px; }
    .content-inner { max-width:1200px; margin:0 auto; padding-top:36px; }

    /* page title row */
    .page-title-row { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:32px; }
    .page-title { font-size:22px; font-weight:800; color:var(--navy); letter-spacing:-.3px; }
    .page-sub   { font-size:13px; color:var(--muted); margin-top:4px; line-height:1.5; }
    .page-date  { font-size:13px; color:var(--muted); font-family:'JetBrains Mono',monospace;
        background:var(--white); border:1px solid var(--border); padding:8px 16px; border-radius:8px; }

    /* alert */
    .alert { padding:13px 18px; border-radius:10px; font-size:13px; font-weight:600;
        margin-bottom:24px; display:flex; align-items:center; gap:10px; }
    .alert-success { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .alert-error   { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }

    /* ── STAT CARDS ────────────────────────────────────────── */
    .stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:28px; }
    .stats-grid-bottom { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:36px; }

    .stat-card {
        background:var(--white); border:1px solid var(--border); border-radius:16px;
        padding:22px 24px; cursor:pointer; text-decoration:none; display:block;
        transition:transform .2s,box-shadow .2s; position:relative; overflow:hidden;
    }
    .stat-card::before {
        content:''; position:absolute; top:0; left:0; right:0; height:4px;
        border-radius:16px 16px 0 0;
    }
    .stat-card.c-warning::before { background:var(--warning); }
    .stat-card.c-success::before { background:var(--success); }
    .stat-card.c-info::before    { background:var(--info); }
    .stat-card.c-gold::before    { background:var(--gold); }
    .stat-card.c-navy::before    { background:var(--navy-light); }
    .stat-card.c-purple::before  { background:#8b5cf6; }

    .stat-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(10,22,40,0.1); }
    .stat-card.is-active { box-shadow:0 12px 30px rgba(10,22,40,0.12); transform:translateY(-4px); }
    .stat-card.is-active.c-warning { border-color:#fde68a; }
    .stat-card.is-active.c-success { border-color:#86efac; }
    .stat-card.is-active.c-info    { border-color:#93c5fd; }

    .stat-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px; }
    .stat-icon { width:44px; height:44px; border-radius:11px; display:flex; align-items:center;
        justify-content:center; font-size:19px; flex-shrink:0; }
    .si-warning { background:#fef3c7; } .si-success { background:#dcfce7; }
    .si-info    { background:#dbeafe; } .si-gold    { background:var(--gold-pale); }
    .si-navy    { background:#e0e7ff; } .si-purple  { background:#f5f3ff; }
    .stat-arrow { font-size:18px; color:var(--border); transition:color .2s; }
    .stat-card:hover .stat-arrow, .stat-card.is-active .stat-arrow { color:var(--gold); }
    .stat-val { font-size:32px; font-weight:800; color:var(--navy); letter-spacing:-1px;
        font-family:'JetBrains Mono',monospace; margin-bottom:4px; }
    .stat-label { font-size:13px; font-weight:600; color:var(--text); margin-bottom:6px; }
    .stat-sub { font-size:11px; color:var(--muted); }
    .stat-pill { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; margin-top:8px; }
    .pill-warning { background:#fef3c7; color:#b45309; }
    .pill-success { background:#dcfce7; color:#15803d; }
    .pill-info    { background:#dbeafe; color:#1e40af; }
    .pill-gold    { background:var(--gold-pale); color:#92400e; }
    .pill-navy    { background:#e0e7ff; color:#3730a3; }
    .pill-purple  { background:#f5f3ff; color:#5b21b6; }

    /* ── TWO COLUMN ROW ────────────────────────────────────── */
    .two-col { display:grid; grid-template-columns:1fr 380px; gap:24px; margin-bottom:28px; }

    /* ── PANEL ─────────────────────────────────────────────── */
    .panel { background:var(--white); border:1px solid var(--border); border-radius:16px; overflow:hidden; }
    .panel-head {
        padding:18px 24px; display:flex; align-items:center; justify-content:space-between;
        border-bottom:1px solid var(--border);
        background:linear-gradient(90deg,var(--white),#fafbfc);
    }
    .panel-title { font-size:15px; font-weight:700; color:var(--navy); display:flex; align-items:center; gap:8px; }
    .panel-badge { font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px;
        background:var(--gold-pale); color:#92400e; border:1px solid #fde68a; }
    .panel-body { padding:24px; }

    /* ── TABLE ─────────────────────────────────────────────── */
    .data-table { width:100%; border-collapse:collapse; }
    .data-table th {
        padding:11px 16px; font-size:11px; font-weight:700; letter-spacing:.8px;
        text-transform:uppercase; color:var(--muted); background:#f8fafc;
        border-bottom:1px solid var(--border); text-align:left;
    }
    .data-table td { padding:14px 16px; font-size:13.5px; border-bottom:1px solid var(--border); vertical-align:middle; }
    .data-table tr:last-child td { border-bottom:none; }
    .data-table tr:hover td { background:#fafbfc; }

    .td-name { font-weight:700; color:var(--navy); }
    .td-email { color:var(--muted); font-size:12.5px; font-family:'JetBrains Mono',monospace; }
    .td-code { font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:600;
        color:var(--gold); text-transform:uppercase; letter-spacing:1px; }

    .user-cell { display:flex; align-items:center; gap:10px; }
    .user-av { width:32px; height:32px; border-radius:50%; display:flex; align-items:center;
        justify-content:center; font-size:13px; font-weight:800; color:var(--white); flex-shrink:0; }
    .ua-student  { background:linear-gradient(135deg,var(--navy),var(--info)); }
    .ua-teacher  { background:linear-gradient(135deg,var(--navy),var(--success)); }
    .ua-pending  { background:linear-gradient(135deg,var(--navy),var(--warning)); }

    .score-bar-wrap { display:flex; align-items:center; gap:8px; }
    .score-bar { flex:1; height:6px; background:#f1f5f9; border-radius:10px; overflow:hidden; min-width:60px; }
    .score-fill { height:100%; border-radius:10px; }
    .score-text { font-size:12px; font-weight:700; min-width:40px; text-align:right; font-family:'JetBrains Mono',monospace; }

    .status-dot { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:600; }
    .dot { width:7px; height:7px; border-radius:50%; }
    .dot-success { background:var(--success); box-shadow:0 0 5px var(--success); }
    .dot-warning { background:var(--warning); }
    .dot-danger  { background:var(--danger); }

    /* action buttons */
    .btn { padding:6px 12px; border-radius:8px; font-size:12px; font-weight:700;
        text-decoration:none; display:inline-flex; align-items:center; gap:5px; transition:all .2s; border:none; cursor:pointer; }
    .btn-sm { padding:5px 10px; font-size:11px; }
    .btn-approve { background:#dcfce7; color:#15803d; border:1px solid #86efac; }
    .btn-approve:hover { background:var(--success); color:var(--white); }
    .btn-danger  { background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; }
    .btn-danger:hover  { background:var(--danger); color:var(--white); }
    .btn-view    { background:#dbeafe; color:#1e40af; border:1px solid #93c5fd; }
    .btn-view:hover    { background:var(--info); color:var(--white); }
    .actions { display:flex; gap:6px; justify-content:flex-end; }

    .empty-state { text-align:center; padding:48px 20px; color:var(--muted); }
    .empty-state .ei { font-size:40px; margin-bottom:12px; }
    .empty-state p { font-size:14px; }

    /* ── QUICK STATS SIDEBAR ───────────────────────────────── */
    .quick-stat-item { display:flex; align-items:center; gap:14px; padding:14px 0;
        border-bottom:1px solid var(--border); }
    .quick-stat-item:last-child { border-bottom:none; }
    .qs-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center;
        justify-content:center; font-size:17px; flex-shrink:0; }
    .qs-label { font-size:12px; color:var(--muted); font-weight:500; }
    .qs-val { font-size:18px; font-weight:800; color:var(--navy); font-family:'JetBrains Mono',monospace; }
    .qs-ml { margin-left:auto; text-align:right; }

    /* ── MOBILE ────────────────────────────────────────────── */
    .menu-toggle { display:none; background:rgba(244,169,22,0.12); border:1px solid rgba(244,169,22,0.25);
        color:var(--gold); width:38px; height:38px; border-radius:8px; cursor:pointer; font-size:16px; }
    @media(max-width:960px){
        .stats-grid,.stats-grid-bottom { grid-template-columns:1fr 1fr; }
        .two-col { grid-template-columns:1fr; }
        .topbar { left:0; padding:0 18px; }
        .main-content { margin-left:0; }
        .sidebar { transform:translateX(-100%); }
        .sidebar.open { transform:translateX(0); box-shadow:8px 0 40px rgba(0,0,0,0.4); }
        .menu-toggle { display:flex; align-items:center; justify-content:center; }
    }
    @media(max-width:600px){ .stats-grid,.stats-grid-bottom { grid-template-columns:1fr; } }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══════════════════════════════════════════════ -->
<aside class="sidebar" id="adminSidebar">
    <div class="sb-head">
        <div class="sb-brand">
            <div class="sb-logo">🏢</div>
            <div>
                <div class="sb-brand-name">RECTEM <span>CBT</span></div>
                <div class="sb-brand-sub">Admin Panel</div>
            </div>
        </div>
    </div>

    <div class="sb-section-label">Main Menu</div>
    <ul>
        <li>
            <a href="dashboard.php" class="<?= $current_page==='dashboard.php' ? 'active':'' ?>">
                <span class="nav-icon"><i class="fas fa-gauge-high"></i></span> Dashboard
            </a>
        </li>
        <li>
            <a href="dashboard.php?view=pending" class="<?= $current_view==='pending' ? 'active':'' ?>">
                <span class="nav-icon"><i class="fas fa-user-clock"></i></span> Pending Teachers
                <?php if($pending_total > 0): ?>
                <span class="nav-badge"><?= $pending_total ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="dashboard.php?view=teachers" class="<?= $current_view==='teachers' ? 'active':'' ?>">
                <span class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></span> Approved Teachers
            </a>
        </li>
        <li>
            <a href="dashboard.php?view=students" class="<?= $current_view==='students' ? 'active':'' ?>">
                <span class="nav-icon"><i class="fas fa-users"></i></span> Students
            </a>
        </li>
        <li>
            <a href="verify_teachers.php">
                <span class="nav-icon"><i class="fas fa-shield-check"></i></span> Verifications
            </a>
        </li>
    </ul>

    <div class="sb-section-label">System</div>
    <ul>
        <li>
            <a href="admin_profile.php">
                <span class="nav-icon"><i class="fas fa-gear"></i></span> Settings
            </a>
        </li>
    </ul>

    <div class="sidebar-logout">
        <a href="../logout.php">
            <span class="nav-icon"><i class="fas fa-arrow-right-from-bracket"></i></span> Sign Out
        </a>
    </div>

    <div class="sb-foot">
        <div class="sf-av"><?= strtoupper(substr($admin_name,0,1)) ?></div>
        <div class="sf-info">
            <div class="sf-name"><?= htmlspecialchars($admin_name) ?></div>
            <div class="sf-role">Administrator</div>
        </div>
        <div class="sf-dot"></div>
    </div>
</aside>

<!-- ══ TOPBAR ════════════════════════════════════════════════ -->
<header class="topbar">
    <div style="display:flex;align-items:center;gap:14px;">
        <button class="menu-toggle" id="menuToggle" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-left">
            <div class="topbar-greeting-row">
                <div class="topbar-icon-box"><?= $greet_icon ?></div>
                <div>
                    <div class="topbar-greet"><?= $greeting ?></div>
                    <div class="topbar-name"><?= htmlspecialchars($admin_name) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-time" id="liveClock">—</div>
        <div class="admin-badge">
            <div class="admin-av"><?= strtoupper(substr($admin_name,0,1)) ?></div>
            <div>
                <div class="badge-role">Admin</div>
                <div class="badge-name-sm"><?= htmlspecialchars($admin_name) ?></div>
            </div>
            <div class="online-dot"></div>
        </div>
    </div>
</header>

<!-- ══ MAIN ══════════════════════════════════════════════════ -->
<main class="main-content">
<div class="content-inner">

    <!-- Page title -->
    <div class="page-title-row">
        <div>
            <div class="page-title">System Dashboard</div>
            <div class="page-sub">Full control panel — manage teachers, students, and monitor exam activity.</div>
        </div>
        <div class="page-date"><?= date('l, d M Y') ?></div>
    </div>

    <!-- Alert -->
    <?php if (!empty($msg)): ?>
    <div class="alert alert-<?= $msg_type ?>">
        <?= $msg_type==='success' ? '✅' : '⚠️' ?> <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>

    <!-- ── STAT CARDS ROW 1 ── -->
    <div class="stats-grid">
        <a href="dashboard.php?view=pending" class="stat-card c-warning <?= $current_view==='pending'?'is-active':'' ?>">
            <div class="stat-top">
                <div class="stat-icon si-warning">⏳</div>
                <div class="stat-arrow"><i class="fas fa-chevron-right"></i></div>
            </div>
            <div class="stat-val"><?= $pending_total ?></div>
            <div class="stat-label">Pending Teachers</div>
            <div class="stat-sub">Awaiting admin verification</div>
            <div class="stat-pill pill-warning">Requires Action</div>
        </a>
        <a href="dashboard.php?view=teachers" class="stat-card c-success <?= $current_view==='teachers'?'is-active':'' ?>">
            <div class="stat-top">
                <div class="stat-icon si-success">🧑‍🏫</div>
                <div class="stat-arrow"><i class="fas fa-chevron-right"></i></div>
            </div>
            <div class="stat-val"><?= $approved_total ?></div>
            <div class="stat-label">Approved Teachers</div>
            <div class="stat-sub">Active faculty members</div>
            <div class="stat-pill pill-success">Active Faculty</div>
        </a>
        <a href="dashboard.php?view=students" class="stat-card c-info <?= $current_view==='students'?'is-active':'' ?>">
            <div class="stat-top">
                <div class="stat-icon si-info">🎓</div>
                <div class="stat-arrow"><i class="fas fa-chevron-right"></i></div>
            </div>
            <div class="stat-val"><?= $students_total ?></div>
            <div class="stat-label">Total Students</div>
            <div class="stat-sub">Registered on the platform</div>
            <div class="stat-pill pill-info">Registered Users</div>
        </a>
    </div>

    <!-- ── STAT CARDS ROW 2 ── -->
    <div class="stats-grid-bottom">
        <div class="stat-card c-gold">
            <div class="stat-top"><div class="stat-icon si-gold">📚</div></div>
            <div class="stat-val"><?= $exams_total ?></div>
            <div class="stat-label">Total Courses</div>
            <div class="stat-sub">Active exam courses</div>
            <div class="stat-pill pill-gold">Course Bank</div>
        </div>
        <div class="stat-card c-navy">
            <div class="stat-top"><div class="stat-icon si-navy">📝</div></div>
            <div class="stat-val"><?= $questions_total ?></div>
            <div class="stat-label">Total Questions</div>
            <div class="stat-sub">In the question bank</div>
            <div class="stat-pill pill-navy">Question Bank</div>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-top"><div class="stat-icon si-purple">📊</div></div>
            <div class="stat-val"><?= $results_total ?></div>
            <div class="stat-label">Exams Submitted</div>
            <div class="stat-sub">Total submissions recorded</div>
            <div class="stat-pill pill-purple">All Time</div>
        </div>
    </div>

    <!-- ── TWO COLUMN: table + quick stats ── -->
    <div class="two-col">

        <!-- Main Management Table -->
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">
                    <?php
                    if ($current_view==='students')      { echo '<i class="fas fa-users"></i> Enrolled Students'; }
                    elseif ($current_view==='teachers')  { echo '<i class="fas fa-chalkboard-teacher"></i> Approved Faculty'; }
                    else                                 { echo '<i class="fas fa-user-clock"></i> Pending Verifications'; }
                    ?>
                </div>
                <span class="panel-badge">
                    <?php
                    if ($current_view==='students')     echo $students_total.' records';
                    elseif ($current_view==='teachers') echo $approved_total.' records';
                    else                                echo $pending_total.' pending';
                    ?>
                </span>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            <?= $current_view==='students' ? 'Student' : 'Teacher' ?>
                        </th>
                        <?php if ($current_view==='students'): ?>
                        <th>Matric No.</th>
                        <?php else: ?>
                        <th>Email</th>
                        <?php endif; ?>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $row_num = 1;

                if ($current_view==='students') {
                    $records = mysqli_query($conn,"SELECT * FROM users WHERE role='student' ORDER BY id DESC");
                    if ($records && mysqli_num_rows($records) > 0):
                        while ($row = mysqli_fetch_assoc($records)):
                ?>
                    <tr>
                        <td style="color:var(--muted);font-size:12px;font-family:'JetBrains Mono',monospace;"><?= $row_num++ ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="user-av ua-student"><?= strtoupper(substr($row['NAME']??'S',0,1)) ?></div>
                                <div>
                                    <div class="td-name"><?= htmlspecialchars($row['NAME']??'') ?></div>
                                    <div class="td-email"><?= htmlspecialchars($row['email']??'') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--muted);"><?= htmlspecialchars($row['matric']??'—') ?></span></td>
                        <td><span class="status-dot"><span class="dot dot-success"></span> Active</span></td>
                        <td>
                            <div class="actions">
                                <a href="dashboard.php?view=students&delete_student=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this student permanently?')">
                                   <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php   endwhile; else: ?>
                    <tr><td colspan="5"><div class="empty-state"><div class="ei">🎓</div><p>No students registered yet.</p></div></td></tr>
                <?php endif;

                } elseif ($current_view==='teachers') {
                    $records = mysqli_query($conn,"SELECT * FROM teachers WHERE status='approved' ORDER BY id DESC");
                    if ($records && mysqli_num_rows($records) > 0):
                        while ($row = mysqli_fetch_assoc($records)):
                ?>
                    <tr>
                        <td style="color:var(--muted);font-size:12px;font-family:'JetBrains Mono',monospace;"><?= $row_num++ ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="user-av ua-teacher"><?= strtoupper(substr($row['name']??'T',0,1)) ?></div>
                                <div>
                                    <div class="td-name"><?= htmlspecialchars($row['name']??'') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="td-email"><?= htmlspecialchars($row['email']??'') ?></span></td>
                        <td><span class="status-dot"><span class="dot dot-success"></span> Approved</span></td>
                        <td>
                            <div class="actions">
                                <a href="dashboard.php?view=teachers&delete_teacher=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Remove this teacher access?')">
                                   <i class="fas fa-trash"></i> Remove
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php   endwhile; else: ?>
                    <tr><td colspan="5"><div class="empty-state"><div class="ei">🧑‍🏫</div><p>No approved teachers found.</p></div></td></tr>
                <?php endif;

                } else {
                    $records = mysqli_query($conn,"SELECT * FROM teachers WHERE status='pending' ORDER BY id DESC");
                    if ($records && mysqli_num_rows($records) > 0):
                        while ($row = mysqli_fetch_assoc($records)):
                ?>
                    <tr>
                        <td style="color:var(--muted);font-size:12px;font-family:'JetBrains Mono',monospace;"><?= $row_num++ ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="user-av ua-pending"><?= strtoupper(substr($row['name']??'T',0,1)) ?></div>
                                <div>
                                    <div class="td-name"><?= htmlspecialchars($row['name']??'') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="td-email"><?= htmlspecialchars($row['email']??'') ?></span></td>
                        <td><span class="status-dot"><span class="dot dot-warning"></span> Pending</span></td>
                        <td>
                            <div class="actions">
                                <a href="dashboard.php?view=pending&approve_teacher=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-approve">
                                   <i class="fas fa-check"></i> Approve
                                </a>
                                <a href="dashboard.php?view=pending&delete_teacher=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Deny and delete this teacher?')">
                                   <i class="fas fa-times"></i> Deny
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php   endwhile; else: ?>
                    <tr><td colspan="5"><div class="empty-state"><div class="ei">🎉</div><p>No pending teacher verifications!</p></div></td></tr>
                <?php endif; } ?>
                </tbody>
            </table>
        </div><!-- end main panel -->

        <!-- Quick Stats + Recent Activity -->
        <div style="display:flex;flex-direction:column;gap:24px;">

            <!-- System Overview -->
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title"><i class="fas fa-chart-pie"></i> System Overview</div>
                </div>
                <div class="panel-body">
                    <div class="quick-stat-item">
                        <div class="qs-icon si-info">🎓</div>
                        <div><div class="qs-label">Students</div><div class="qs-val"><?= $students_total ?></div></div>
                        <div class="qs-ml"><span class="stat-pill pill-info">Registered</span></div>
                    </div>
                    <div class="quick-stat-item">
                        <div class="qs-icon si-success">🧑‍🏫</div>
                        <div><div class="qs-label">Teachers</div><div class="qs-val"><?= $approved_total ?></div></div>
                        <div class="qs-ml"><span class="stat-pill pill-success">Approved</span></div>
                    </div>
                    <div class="quick-stat-item">
                        <div class="qs-icon si-gold">📚</div>
                        <div><div class="qs-label">Courses</div><div class="qs-val"><?= $exams_total ?></div></div>
                        <div class="qs-ml"><span class="stat-pill pill-gold">Active</span></div>
                    </div>
                    <div class="quick-stat-item">
                        <div class="qs-icon si-navy">📝</div>
                        <div><div class="qs-label">Questions</div><div class="qs-val"><?= $questions_total ?></div></div>
                        <div class="qs-ml"><span class="stat-pill pill-navy">In Bank</span></div>
                    </div>
                    <div class="quick-stat-item">
                        <div class="qs-icon si-purple">📊</div>
                        <div><div class="qs-label">Submissions</div><div class="qs-val"><?= $results_total ?></div></div>
                        <div class="qs-ml"><span class="stat-pill pill-purple">All Time</span></div>
                    </div>
                </div>
            </div>

            <!-- Recent Submissions -->
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title"><i class="fas fa-clock-rotate-left"></i> Recent Submissions</div>
                </div>
                <div class="panel-body" style="padding:0;">
                    <?php if ($recent_results && mysqli_num_rows($recent_results) > 0):
                        while ($r = mysqli_fetch_assoc($recent_results)):
                            $pct = $r['total_questions'] > 0 ? round(($r['score']/$r['total_questions'])*100) : 0;
                            $bar_color = $pct >= 70 ? '#22c55e' : ($pct >= 50 ? '#f4a916' : '#ef4444');
                    ?>
                    <div style="padding:14px 18px;border-bottom:1px solid var(--border);">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                            <div>
                                <div style="font-size:13px;font-weight:700;color:var(--navy);"><?= htmlspecialchars($r['student_name']) ?></div>
                                <div class="td-code"><?= htmlspecialchars($r['course_code']) ?></div>
                            </div>
                            <div style="font-size:11px;color:var(--muted);font-family:'JetBrains Mono',monospace;white-space:nowrap;">
                                <?= date('d M, H:i', strtotime($r['date_taken'])) ?>
                            </div>
                        </div>
                        <div class="score-bar-wrap">
                            <div class="score-bar"><div class="score-fill" style="width:<?= $pct ?>%;background:<?= $bar_color ?>;"></div></div>
                            <span class="score-text" style="color:<?= $bar_color ?>;"><?= $pct ?>%</span>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="empty-state"><div class="ei">📭</div><p>No submissions yet.</p></div>
                    <?php endif; ?>
                </div>
            </div>

        </div><!-- end right column -->
    </div><!-- end two-col -->

</div>
</main>

<script>
// Live clock
function updateClock() {
    const now  = new Date();
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const d    = String(now.getDate()).padStart(2,'0');
    const m    = String(now.getMonth()+1).padStart(2,'0');
    const y    = now.getFullYear();
    const h    = String(now.getHours()).padStart(2,'0');
    const min  = String(now.getMinutes()).padStart(2,'0');
    const s    = String(now.getSeconds()).padStart(2,'0');
    const el   = document.getElementById('liveClock');
    if (el) el.textContent = days[now.getDay()] + ' ' + d+'/'+m+'/'+y + ' · ' + h+':'+min+':'+s;
}
updateClock(); setInterval(updateClock, 1000);

// Mobile sidebar
const menuBtn = document.getElementById('menuToggle');
const sidebar = document.getElementById('adminSidebar');
if (menuBtn && sidebar) {
    menuBtn.onclick = function(e) { e.stopPropagation(); sidebar.classList.toggle('open'); };
    document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) sidebar.classList.remove('open');
    });
}
</script>
</body>
</html>