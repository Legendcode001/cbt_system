<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — CBT Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/images/launcher_iconn.png" type="images/png">
    <style>
        :root {
            --navy: #0a1628;
            --navy-mid: #112240;
            --navy-light: #1d3461;
            --gold: #f4a916;
            --gold-light: #ffc94a;
            --white: #ffffff;
            --off-white: #f0f4f8;
            --text-muted: #8899aa;
            --border: #e2e8f0;
            --purple: #534AB7;
            --purple-light: #EEEDFE;
            --teal: #0F6E56;
            --teal-light: #E1F5EE;
            --blue: #185FA5;
            --blue-light: #E6F1FB;
            --coral: #993C1D;
            --coral-light: #FAECE7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--off-white);
            min-height: 100vh;
        }

        /* ── TOP BAR ── */
        .top-bar {
            background: var(--navy);
            color: var(--white);
            padding: 0 40px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            color: var(--white);
        }

        .brand span {
            color: var(--gold);
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--gold);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .nav-links {
            display: flex;
            gap: 6px;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--white);
            background: var(--navy-light);
        }

        .nav-link.active {
            color: var(--gold);
        }

        /* ── HERO ── */
        .hero {
            background: var(--navy);
            padding: 80px 20px 90px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: var(--gold);
            opacity: 0.04;
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--off-white);
            clip-path: ellipse(55% 100% at 50% 100%);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(244, 169, 22, 0.15);
            border: 1px solid rgba(244, 169, 22, 0.3);
            color: var(--gold);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 8px 18px;
            border-radius: 30px;
            margin-bottom: 24px;
        }

        .hero-title {
            font-size: 42px;
            font-weight: 700;
            color: var(--white);
            line-height: 1.2;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .hero-title span {
            color: var(--gold);
        }

        .hero-sub {
            font-size: 16px;
            color: var(--text-muted);
            max-width: 520px;
            margin: 0 auto 32px;
            line-height: 1.7;
        }

        .hero-divider {
            width: 60px;
            height: 3px;
            background: var(--gold);
            border-radius: 3px;
            margin: 0 auto;
        }

        /* ── PAGE WRAP ── */
        .page-wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 60px 20px 80px;
        }

        /* ── SECTION HEADERS ── */
        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 10px;
        }

        .section-sub {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ── LEAD ROW ── */
        .lead-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        /* ── SUPPORT ROW ── */
        .support-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 60px;
        }

        /* ── MEMBER CARD ── */
        .member-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 32px 24px 28px;
            text-align: center;
            position: relative;
            transition: transform 0.25s, box-shadow 0.25s;
        }

        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 48px rgba(10, 22, 40, 0.12);
        }

        .member-card.card-dev {
            border-top: 4px solid var(--purple);
        }

        .member-card.card-admin {
            border-top: 4px solid var(--gold);
        }

        .member-card.card-analyst {
            border-top: 4px solid var(--blue);
        }

        .member-card.card-qa {
            border-top: 4px solid var(--coral);
        }

        /* ROLE BADGE */
        .role-tag {
            position: absolute;
            top: 16px;
            right: 16px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .tag-dev {
            background: var(--purple-light);
            color: #3C3489;
        }

        .tag-admin {
            background: #fffbeb;
            color: #92400e;
        }

        .tag-analyst {
            background: var(--blue-light);
            color: #0C447C;
        }

        .tag-qa {
            background: var(--coral-light);
            color: #712B13;
        }

        /* AVATAR */
        .avatar-wrap {
            position: relative;
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
        }

        .avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 700;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .avatar-dev {
            background: var(--purple-light);
            color: var(--purple);
            border: 3px solid #AFA9EC;
        }

        .avatar-admin {
            background: #fffbeb;
            color: #92400e;
            border: 3px solid var(--gold);
        }

        .avatar-analyst {
            background: var(--blue-light);
            color: var(--blue);
            border: 3px solid #85B7EB;
        }

        .avatar-qa {
            background: var(--coral-light);
            color: var(--coral);
            border: 3px solid #F0997B;
        }

        .avatar-wrap img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            position: relative;
            z-index: 2;
        }

        /* Hide broken image icons if they fail to load */
        .avatar-wrap img:not([src]),
        .avatar-wrap img[src=""] {
            opacity: 0;
        }

        /* Star badge for lead dev */
        .star-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 26px;
            height: 26px;
            background: var(--gold);
            border-radius: 50%;
            border: 2.5px solid var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            z-index: 3;
        }

        .member-name {
            font-size: 17px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 4px;
        }

        .member-role-title {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .member-desc {
            font-size: 13px;
            color: #4a5568;
            line-height: 1.7;
            margin-bottom: 18px;
            text-align: left;
        }

        .skills {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
        }

        .skill {
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 20px;
            background: var(--off-white);
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        /* Lead dev highlight */
        .member-card.card-dev {
            background: linear-gradient(180deg, #fdfbff 0%, var(--white) 100%);
        }

        /* ── STATS BAR ── */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 60px;
        }

        .stat-box {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px 16px;
            text-align: center;
        }

        .stat-num {
            font-size: 30px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 4px;
        }

        .stat-num.gold {
            color: var(--gold);
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── PROJECT CARD ── */
        .project-section {
            margin-bottom: 60px;
        }

        .project-card {
            background: var(--navy);
            border-radius: 18px;
            padding: 36px;
            color: var(--white);
            display: flex;
            align-items: flex-start;
            gap: 32px;
        }

        .project-icon {
            width: 64px;
            height: 64px;
            background: var(--gold);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex-shrink: 0;
        }

        .project-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 8px;
        }

        .project-sub {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .tech-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tech-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            padding: 5px 14px;
            border-radius: 6px;
            background: var(--navy-light);
            color: var(--gold);
            border: 1px solid rgba(244, 169, 22, 0.2);
        }

        /* ── FOOTER BAND ── */
        .footer-band {
            background: var(--navy);
            border-radius: 14px;
            padding: 28px;
            text-align: center;
        }

        .footer-band p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.9;
        }

        .footer-band span {
            color: var(--gold);
            font-weight: 600;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {

            .lead-row,
            .support-row {
                grid-template-columns: 1fr;
            }

            .stats-bar {
                grid-template-columns: 1fr 1fr;
            }

            .project-card {
                flex-direction: column;
                gap: 20px;
            }

            .hero-title {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

    <!-- TOP BAR -->
    <div class="top-bar">
        <a href="index.php" class="brand">
            <div class="brand-icon">📋</div>
            CBT <span>Portal</span>
        </a>
        <nav class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="about_team.php" class="nav-link active">About</a>
            <a href="login.php" class="nav-link">Login</a>
        </nav>
    </div>

    <!-- HERO -->
    <div class="hero">
        <div class="hero-badge">🎓 Academic Project — RECTEM</div>
        <h1 class="hero-title">Meet the <span>Team</span></h1>
        <p class="hero-sub">A group of dedicated students who designed, built, tested and deployed this Computer Based Testing system from scratch.</p>
        <div class="hero-divider"></div>
    </div>

    <div class="page-wrap">

        <!-- TEAM SECTION -->
        <div class="section-header">
            <div class="section-badge">⭐ The Developers</div>
            <div class="section-title">People behind the system</div>
            <div class="section-sub">Four roles, one goal — deliver a professional, reliable CBT experience</div>
        </div>

        <!-- ROW 1: DEVELOPER + ADMIN -->
        <div class="lead-row">

            <!-- LEAD DEVELOPER -->
            <div class="member-card card-dev">
                <span class="role-tag tag-dev">Lead Developer</span>
                <div class="avatar-wrap">
                    <img src="assets/images/profile.png" alt="Adejumo Abraham" onerror="this.style.opacity='0';">
                    <div class="avatar avatar-dev">AO</div>
                    <div class="star-badge">⭐</div>
                </div>
                <div class="member-name">Abraham Opeoluwa ADEJUMO</div>
                <div class="member-role-title">Team Lead &amp; Software Developer</div>
                <p class="member-desc">
                    The architect behind the entire CBT portal. Abraham led the team, designed the database, built every module from login to exam submission, and ensured the system was deployed and working end-to-end.
                </p>
                <div class="skills">
                    <span class="skill">PHP</span>
                    <span class="skill">MySQL</span>
                    <span class="skill">HTML/CSS</span>
                    <span class="skill">JavaScript</span>
                    <span class="skill">System Design</span>
                    <span class="skill">Team Lead</span>
                </div>
            </div>

            <!-- ADMIN -->
            <div class="member-card card-admin">
                <span class="role-tag tag-admin">Administrator</span>
                <div class="avatar-wrap">
                    <img src="assets/images/oluwashina.jpg" alt="Adeshina" onerror="this.style.opacity='0';">
                    <div class="avatar avatar-admin">AS</div>
                </div>
                <div class="member-name">Adeshina</div>
                <div class="member-role-title">System Administrator</div>
                <p class="member-desc">
                    Responsible for the day-to-day management of the portal. Adeshina handles user accounts, course setup, data entry, and ensures the platform runs smoothly for both students and teachers.
                </p>
                <div class="skills">
                    <span class="skill">User Management</span>
                    <span class="skill">Data Entry</span>
                    <span class="skill">Platform Ops</span>
                    <span class="skill">Course Setup</span>
                </div>
            </div>

        </div>

        <!-- ROW 2: ANALYST + QA -->
        <div class="support-row">

            <!-- SYSTEM ANALYST -->
            <div class="member-card card-analyst">
                <span class="role-tag tag-analyst">System Analyst</span>
                <div class="avatar-wrap">
                    <img src="assets/images/demi.JPG" alt="Akinlade Demilade" onerror="this.style.opacity='0';">
                    <div class="avatar avatar-analyst">AD</div>
                </div>
                <div class="member-name">Akinlade Demilade</div>
                <div class="member-role-title">System Analyst</div>
                <p class="member-desc">
                    Gathered and documented all system requirements, mapped out user workflows, and created the system architecture that guided the development process from start to finish.
                </p>
                <div class="skills">
                    <span class="skill">Requirements</span>
                    <span class="skill">UML Diagrams</span>
                    <span class="skill">Workflow Design</span>
                    <span class="skill">Documentation</span>
                </div>
            </div>

            <!-- QA TESTER -->
            <div class="member-card card-qa">
                <span class="role-tag tag-qa">QA Engineer</span>
                <div class="avatar-wrap">
                    <img src="assets/images/usman.jpg" alt="Usman Olamilekan" onerror="this.style.opacity='0';">
                    <div class="avatar avatar-qa">UO</div>
                </div>
                <div class="member-name">Usman Olamilekan</div>
                <div class="member-role-title">Quality Assurance Engineer</div>
                <p class="member-desc">
                    Tested every feature of the system end-to-end, identified and reported bugs, verified all fixes, and confirmed the system was stable and ready for use before final submission.
                </p>
                <div class="skills">
                    <span class="skill">Bug Testing</span>
                    <span class="skill">UAT</span>
                    <span class="skill">Manual Testing</span>
                    <span class="skill">Bug Reports</span>
                </div>
            </div>

        </div>

        <!-- STATS -->
        <div class="stats-bar">
            <div class="stat-box">
                <div class="stat-num gold">4</div>
                <div class="stat-label">Team Members</div>
            </div>
            <div class="stat-box">
                <div class="stat-num">PHP</div>
                <div class="stat-label">Core Language</div>
            </div>
            <div class="stat-box">
                <div class="stat-num gold">CBT</div>
                <div class="stat-label">System Type</div>
            </div>
            <div class="stat-box">
                <div class="stat-num"><?php echo date("Y"); ?></div>
                <div class="stat-label">Year Active</div>
            </div>
        </div>

        <!-- PROJECT CARD -->
        <div class="project-section">
            <div class="section-header">
                <div class="section-badge">📁 The Project</div>
                <div class="section-title">About the CBT Portal</div>
            </div>
            <div class="project-card">
                <div class="project-icon">📋</div>
                <div>
                    <div class="project-title">RECTEM CBT Portal</div>
                    <p class="project-sub">
                        A full-featured Computer Based Testing system developed as a mandatory skill project.
                        Students can register, log in with their matric number, take timed exams, and instantly view their
                        results with grades. Teachers manage courses, upload questions, and configure exam durations —
                        all through clean, purpose-built dashboards.
                    </p>
                    <div class="tech-stack">
                        <span class="tech-badge">PHP 8</span>
                        <span class="tech-badge">MySQL</span>
                        <span class="tech-badge">HTML5 &amp; CSS3</span>
                        <span class="tech-badge">JavaScript</span>
                        <span class="tech-badge">Apache</span>
                        <span class="tech-badge">cPanel / InfinityFree</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER BAND -->
        <div class="footer-band">
            <p>
                Developed with 💛 and teamwork &nbsp;·&nbsp; <span>RECTEM CBT Portal &copy; <?php echo date("Y"); ?></span><br>
                Group Lead &amp; Developer: <span>Abraham Opeoluwa ADEJUMO</span> &nbsp;·&nbsp; Mandatory Skill Project
            </p>
        </div>

    </div>

</body>

</html>