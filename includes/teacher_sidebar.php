<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Africa/Lagos');
$hour = (int) date('H');

if ($hour < 12)      { $greeting = "Good Morning";   $greet_icon = "🌅"; $greet_color = "#f97316"; }
elseif ($hour < 16)  { $greeting = "Good Afternoon"; $greet_icon = "☀️"; $greet_color = "#f4a916"; }
else                 { $greeting = "Good Evening";   $greet_icon = "🌙"; $greet_color = "#818cf8"; }

$teacher_name = isset($_SESSION['name'])
    ? ucwords(strtolower($_SESSION['name']))
    : (isset($_SESSION['teacher_name']) ? ucwords(strtolower($_SESSION['teacher_name'])) : 'Facilitator');

$teacher_id = $_SESSION['teacher_id'] ?? $_SESSION['user_id'] ?? '---';
$current    = basename($_SERVER['PHP_SELF']);

$nav_items = [
    ['file' => 'dashboard.php',        'icon' => 'fa-gauge-high',    'label' => 'Dashboard'],
    ['file' => 'add_question.php',     'icon' => 'fa-circle-plus',   'label' => 'Add Question'],
    ['file' => 'add_course.php',       'icon' => 'fa-book-open',     'label' => 'Register Course'],
    ['file' => 'manage_exam.php',      'icon' => 'fa-clipboard-list','label' => 'Manage Exam'],
    ['file' => 'manage_questions.php', 'icon' => 'fa-pen-to-square', 'label' => 'Manage Questions'],
];
?>
<!-- ══ FONT AWESOME 6 ══════════════════════════════════════════════════ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
/* ══ RESET / VARS ══════════════════════════════════════════════════════ */
:root {
    --navy:        #0a1628;
    --navy-mid:    #112240;
    --navy-light:  #1d3461;
    --gold:        #f4a916;
    --gold-light:  #ffc94a;
    --gold-pale:   #fff8e7;
    --white:       #ffffff;
    --off-white:   #f4f7fb;
    --muted:       #8899aa;
    --border:      rgba(255,255,255,0.08);
    --sidebar-w:   260px;
    --topbar-h:    60px;
    --radius:      12px;
}

/* ══ TOPBAR ═══════════════════════════════════════════════════════════ */
.teacher-topbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    height: var(--topbar-h);
    z-index: 200;
    background: var(--navy);
    border-bottom: 1px solid rgba(244,169,22,0.18);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px 0 calc(var(--sidebar-w) + 28px);
    box-shadow: 0 2px 24px rgba(0,0,0,0.25);
    gap: 16px;
}

/* — Brand (visible on mobile) — */
.brand-section {
    display: flex;
    align-items: center;
    gap: 10px;
}
.menu-toggle {
    display: none;
    background: rgba(244,169,22,0.12);
    border: 1px solid rgba(244,169,22,0.25);
    color: var(--gold);
    width: 38px; height: 38px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: background .2s;
}
.menu-toggle:hover { background: rgba(244,169,22,0.22); }
.brand-logo {
    display: none; /* hidden on desktop — shown in sidebar */
    font-size: 22px;
}
.brand-title-text {
    display: none;
    font-size: 15px; font-weight: 700;
    color: var(--white);
    font-family: 'Sora', sans-serif;
}
.brand-title-text .brand-subtext { color: var(--muted); font-weight: 400; font-size: 13px; }

/* — Greeting block — */
.user-greeting-section {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-left: auto;
}

.greeting-container {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
}

.greeting-top {
    display: flex;
    align-items: center;
    gap: 8px;
}

.greet-icon-wrap {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}

.greet-texts { display: flex; flex-direction: column; gap: 1px; }

.time-msg {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: var(--muted);
    font-family: 'Sora', sans-serif;
}

.user-name {
    font-size: 15px;
    font-weight: 700;
    color: var(--white);
    font-family: 'Sora', sans-serif;
    letter-spacing: -0.2px;
    white-space: nowrap;
}

/* Divider line */
.greet-sep {
    width: 1px; height: 36px;
    background: rgba(255,255,255,0.1);
}

/* ID badge */
.teacher-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(244,169,22,0.1);
    border: 1px solid rgba(244,169,22,0.25);
    border-radius: 30px;
    padding: 6px 14px;
    white-space: nowrap;
}
.badge-avatar {
    width: 28px; height: 28px;
    background: linear-gradient(135deg, var(--navy-light), var(--gold));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800;
    color: var(--white);
    font-family: 'Sora', sans-serif;
    flex-shrink: 0;
}
.badge-info { display: flex; flex-direction: column; gap: 1px; }
.badge-role {
    font-size: 9px; font-weight: 700; letter-spacing: 1.2px;
    text-transform: uppercase; color: var(--gold);
    font-family: 'Sora', sans-serif;
}
.badge-id {
    font-size: 12px; font-weight: 600;
    color: rgba(255,255,255,0.7);
    font-family: 'JetBrains Mono', monospace;
}

/* Online dot */
.online-dot {
    width: 8px; height: 8px;
    background: #22c55e;
    border-radius: 50%;
    box-shadow: 0 0 6px #22c55e;
    animation: pulse-dot 2.5s infinite;
}
@keyframes pulse-dot {
    0%,100% { box-shadow: 0 0 4px #22c55e; }
    50%      { box-shadow: 0 0 10px #22c55e, 0 0 20px rgba(34,197,94,0.3); }
}

/* ══ SIDEBAR ══════════════════════════════════════════════════════════ */
.sidebar {
    position: fixed;
    top: 0; left: 0; bottom: 0;
    width: var(--sidebar-w);
    z-index: 300;
    background: var(--navy-mid);
    border-right: 1px solid rgba(244,169,22,0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform .3s cubic-bezier(.4,0,.2,1);
}

/* — Sidebar header — */
.sidebar-header {
    padding: 22px 22px 18px;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.sidebar-brand {
    display: flex; align-items: center; gap: 10px;
}
.sb-logo {
    width: 40px; height: 40px;
    background: var(--gold);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.sb-brand-text { display: flex; flex-direction: column; gap: 1px; }
.sb-brand-name {
    font-size: 14px; font-weight: 800;
    color: var(--white); letter-spacing: -0.2px;
    font-family: 'Sora', sans-serif;
}
.sb-brand-name span { color: var(--gold); }
.sb-brand-sub {
    font-size: 10px; font-weight: 600; letter-spacing: 1px;
    text-transform: uppercase; color: var(--muted);
    font-family: 'Sora', sans-serif;
}

/* — Nav label — */
.sidebar-nav-label {
    font-size: 10px; font-weight: 700; letter-spacing: 1.5px;
    text-transform: uppercase; color: rgba(136,153,170,0.6);
    padding: 18px 22px 8px;
    font-family: 'Sora', sans-serif;
}

/* — Nav list — */
.sidebar ul {
    list-style: none;
    margin: 0; padding: 0 12px;
    flex: 1;
    overflow-y: auto;
}
.sidebar ul li { margin-bottom: 3px; }
.sidebar ul li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 14px;
    border-radius: 10px;
    color: #94a3b8;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    font-family: 'Sora', sans-serif;
    transition: background .18s, color .18s, padding-left .18s;
    position: relative;
}
.sidebar ul li a:hover {
    background: rgba(255,255,255,0.05);
    color: var(--white);
    padding-left: 18px;
}
.sidebar ul li a.active {
    background: linear-gradient(90deg, rgba(244,169,22,0.18), rgba(244,169,22,0.06));
    color: var(--gold);
    font-weight: 600;
    border-left: 3px solid var(--gold);
}
.sidebar ul li a.active:hover { padding-left: 14px; }

.sidebar ul li a .nav-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    background: rgba(255,255,255,0.04);
    flex-shrink: 0;
    transition: background .18s;
}
.sidebar ul li a.active .nav-icon {
    background: rgba(244,169,22,0.2);
    color: var(--gold);
}
.sidebar ul li a:hover .nav-icon { background: rgba(255,255,255,0.08); }

/* — Logout — */
.logout-item { margin-top: auto; padding-top: 8px; border-top: 1px solid var(--border); margin-top: 12px; }
.sidebar ul li.logout-item a {
    color: #f87171;
}
.sidebar ul li.logout-item a:hover {
    background: rgba(248,113,113,0.1);
    color: #fca5a5;
}
.sidebar ul li.logout-item a .nav-icon {
    background: rgba(248,113,113,0.1);
    color: #f87171;
}

/* — Sidebar footer — */
.sidebar-footer {
    padding: 14px 22px;
    border-top: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
    flex-shrink: 0;
}
.sf-avatar {
    width: 34px; height: 34px;
    background: linear-gradient(135deg, var(--navy-light), var(--gold));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 800;
    color: var(--white); flex-shrink: 0;
    font-family: 'Sora', sans-serif;
}
.sf-info { display: flex; flex-direction: column; gap: 1px; overflow: hidden; }
.sf-name {
    font-size: 13px; font-weight: 700;
    color: var(--white); white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
    font-family: 'Sora', sans-serif;
}
.sf-role {
    font-size: 10px; letter-spacing: 0.8px; text-transform: uppercase;
    color: var(--gold); font-weight: 600;
    font-family: 'Sora', sans-serif;
}
.sf-dot {
    width: 7px; height: 7px; background: #22c55e;
    border-radius: 50%; margin-left: auto; flex-shrink: 0;
    box-shadow: 0 0 6px #22c55e;
}

/* ══ MAIN CONTENT OFFSET ══════════════════════════════════════════════ */
.main {
    margin-left: var(--sidebar-w);
    padding-top: var(--topbar-h);
    min-height: 100vh;
}

/* ══ MOBILE ═══════════════════════════════════════════════════════════ */
@media (max-width: 900px) {
    .teacher-topbar {
        padding: 0 18px;
    }
    .menu-toggle      { display: flex; align-items: center; justify-content: center; }
    .brand-logo       { display: block; }
    .brand-title-text { display: block; }

    /* Hide greeting text on very small screens */
    .greet-sep,
    .greeting-container { display: none; }

    .sidebar {
        transform: translateX(-100%);
        box-shadow: none;
    }
    .sidebar.active {
        transform: translateX(0);
        box-shadow: 8px 0 40px rgba(0,0,0,0.4);
    }
    .main { margin-left: 0; }
}
</style>

<!-- ══ TOPBAR MARKUP ════════════════════════════════════════════════════ -->
<div class="teacher-topbar">

    <!-- Left: mobile menu + brand name -->
    <div class="brand-section">
        <button class="menu-toggle" type="button" id="mobileMenuBtn" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <span class="brand-logo">📋</span>
        <div class="brand-title-text">RECTEM CBT <span class="brand-subtext">| Staff Portal</span></div>
    </div>

    <!-- Right: greeting + ID badge -->
    <div class="user-greeting-section">

        <div class="greeting-top">
            <div class="greet-icon-wrap" style="background:rgba(244,169,22,0.12);">
                <?= $greet_icon ?>
            </div>
            <div class="greet-texts">
                <span class="time-msg"><?= $greeting ?></span>
                <span class="user-name"><?= htmlspecialchars($teacher_name) ?></span>
            </div>
        </div>

        <div class="greet-sep"></div>

        <div class="teacher-badge">
            <div class="badge-avatar"><?= strtoupper(substr($teacher_name, 0, 1)) ?></div>
            <div class="badge-info">
                <span class="badge-role">Teacher</span>
                <span class="badge-id">#<?= str_pad($teacher_id, 4, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="online-dot" title="Online"></div>
        </div>

    </div>
</div>

<!-- ══ SIDEBAR MARKUP ══════════════════════════════════════════════════ -->
<div class="sidebar" id="layoutSidebar">

    <!-- Brand -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="sb-logo">📋</div>
            <div class="sb-brand-text">
                <div class="sb-brand-name">RECTEM <span>CBT</span></div>
                <div class="sb-brand-sub">Staff Dashboard</div>
            </div>
        </div>
    </div>

    <div class="sidebar-nav-label">Navigation</div>

    <ul>
        <?php foreach ($nav_items as $item): ?>
        <li>
            <a href="<?= $item['file'] ?>"
               class="<?= $current === $item['file'] ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas <?= $item['icon'] ?>"></i></span>
                <?= $item['label'] ?>
            </a>
        </li>
        <?php endforeach; ?>

        <li class="logout-item">
            <a href="../logout.php">
                <span class="nav-icon"><i class="fas fa-arrow-right-from-bracket"></i></span>
                Logout
            </a>
        </li>
    </ul>

    <!-- Sidebar footer with teacher info -->
    <div class="sidebar-footer">
        <div class="sf-avatar"><?= strtoupper(substr($teacher_name, 0, 1)) ?></div>
        <div class="sf-info">
            <div class="sf-name"><?= htmlspecialchars($teacher_name) ?></div>
            <div class="sf-role">Teacher &nbsp;·&nbsp; #<?= str_pad($teacher_id, 4, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div class="sf-dot"></div>
    </div>

</div>

<!-- ══ MOBILE MENU SCRIPT ══════════════════════════════════════════════ -->
<script>
(function () {
    function initMenu() {
        var btn     = document.getElementById('mobileMenuBtn');
        var sidebar = document.getElementById('layoutSidebar');
        if (!btn || !sidebar) return;
        btn.onclick = function (e) { e.stopPropagation(); sidebar.classList.toggle('active'); };
        document.addEventListener('click', function (e) {
            if (!sidebar.contains(e.target) && !btn.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initMenu);
    else initMenu();
})();
</script>