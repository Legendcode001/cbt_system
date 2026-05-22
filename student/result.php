<?php
// student/result.php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$uid = intval($_SESSION['user_id']);

// Refresh student info from DB
$uq = mysqli_query($conn, "SELECT NAME, email FROM users WHERE id = '$uid'");
if ($uq && $row = mysqli_fetch_assoc($uq)) {
    $_SESSION['student_name']   = $row['NAME'];
    $_SESSION['student_reg_no'] = $row['matric'] ?? $row['email'];
}

$student_name = $_SESSION['student_name'] ?? 'Unknown Student';
$student_reg  = $_SESSION['student_reg_no'] ?? 'N/A';

// Load ALL of this student's results from the results table
$results_sql = "SELECT r.*, c.course_code, c.course_name
                FROM results r
                JOIN courses c ON r.course_id = c.id
                WHERE r.student_uid = '$uid'
                ORDER BY r.date_taken DESC";
$results = mysqli_query($conn, $results_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results — CBT Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel=" icon" href="assets/images/launcher_iconn.png" type="image/png">
    <style>
        :root {
            --navy: #0a1628;
            --navy-light: #1d3461;
            --gold: #f4a916;
            --gold-light: #ffc94a;
            --white: #fff;
            --off-white: #f0f4f8;
            --text-muted: #8899aa;
            --border: #e2e8f0;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f97316;
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

        .top-bar {
            background: var(--navy);
            color: #fff;
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

        .student-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--navy-light);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
        }

        .avatar {
            width: 28px;
            height: 28px;
            background: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            color: var(--navy);
        }

        .sname {
            font-weight: 600;
        }

        .sreg {
            color: var(--text-muted);
            font-size: 11px;
            font-family: 'JetBrains Mono', monospace;
        }

        .page-wrap {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }

        .page-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 6px;
        }

        .page-sub {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* SUMMARY CARDS */
        .summary-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            text-align: center;
        }

        .summary-card .val {
            font-size: 32px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 4px;
        }

        .summary-card .lbl {
            font-size: 12px;
            color: var(--text-muted);
        }

        .summary-card.gold .val {
            color: var(--gold);
        }

        /* RESULT TABLE */
        .results-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }

        .results-card table {
            width: 100%;
            border-collapse: collapse;
        }

        .results-card th {
            background: var(--navy);
            color: #fff;
            padding: 14px 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: left;
            letter-spacing: 0.5px;
        }

        .results-card td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            color: #334155;
            vertical-align: middle;
        }

        .results-card tr:last-child td {
            border-bottom: none;
        }

        .results-card tr:hover td {
            background: #fffdf5;
        }

        .code-tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 500;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .score-bar-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .score-bar {
            flex: 1;
            height: 8px;
            background: var(--off-white);
            border-radius: 10px;
            overflow: hidden;
            min-width: 80px;
        }

        .score-bar-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.8s ease;
        }

        .score-num {
            font-weight: 700;
            font-size: 14px;
            min-width: 36px;
        }

        .grade-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .grade-A {
            background: #dcfce7;
            color: #16a34a;
        }

        .grade-B {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .grade-C {
            background: #fef9c3;
            color: #ca8a04;
        }

        .grade-D {
            background: #ffedd5;
            color: #c2410c;
        }

        .grade-F {
            background: #fee2e2;
            color: #dc2626;
        }

        .date-str {
            font-size: 12px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .empty-state h4 {
            font-size: 18px;
            color: var(--navy);
            margin-bottom: 8px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            margin-top: 24px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--navy);
        }

        .btn-exam {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--gold);
            color: var(--navy);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            transition: background 0.2s;
        }

        .btn-exam:hover {
            background: var(--gold-light);
        }
    </style>
</head>

<body>

    <div class="top-bar">
        <div class="brand">
            <div class="brand-icon">📋</div>
            CBT <span>Portal</span>
        </div>
        <div class="student-badge">
            <div class="avatar"><?= strtoupper(substr($student_name, 0, 1)) ?></div>
            <div>
                <div class="sname"><?= htmlspecialchars($student_name) ?></div>
                <div class="sreg"><?= htmlspecialchars($student_reg) ?></div>
            </div>
        </div>
    </div>

    <div class="page-wrap">
        <div class="page-title">📊 My Results</div>
        <div class="page-sub">All examination results for <?= htmlspecialchars($student_name) ?></div>

        <?php
        $all_results = [];
        if ($results) while ($r = mysqli_fetch_assoc($results)) $all_results[] = $r;

        $total_exams = count($all_results);
        $total_score = 0;
        $total_max   = 0;
        foreach ($all_results as $r) {
            $total_score += $r['score'];
            $total_max   += $r['total_questions'];
        }
        $avg_pct = $total_max > 0 ? round(($total_score / $total_max) * 100) : 0;
        ?>

        <div class="summary-row">
            <div class="summary-card">
                <div class="val"><?= $total_exams ?></div>
                <div class="lbl">Exams Taken</div>
            </div>
            <div class="summary-card gold">
                <div class="val"><?= $avg_pct ?>%</div>
                <div class="lbl">Average Score</div>
            </div>
            <div class="summary-card">
                <div class="val"><?= $total_score ?>/<?= $total_max ?></div>
                <div class="lbl">Total Points</div>
            </div>
        </div>

        <?php if (count($all_results) > 0): ?>
            <div class="results-card">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_results as $i => $r):
                            $pct   = $r['total_questions'] > 0 ? round(($r['score'] / $r['total_questions']) * 100) : 0;
                            $grade = $pct >= 70 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 50 ? 'C' : ($pct >= 45 ? 'D' : 'F')));
                            $bar_color = $pct >= 70 ? '#22c55e' : ($pct >= 50 ? '#f4a916' : '#ef4444');
                        ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <div class="code-tag"><?= htmlspecialchars($r['course_code']) ?></div>
                                    <div style="font-weight:600;color:var(--navy);font-size:14px;margin-top:2px;">
                                        <?= htmlspecialchars($r['course_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="score-bar-wrap">
                                        <div class="score-bar">
                                            <div class="score-bar-fill" style="width:<?= $pct ?>%;background:<?= $bar_color ?>;"></div>
                                        </div>
                                        <span class="score-num" style="color:<?= $bar_color ?>">
                                            <?= $r['score'] ?>/<?= $r['total_questions'] ?> (<?= $pct ?>%)
                                        </span>
                                    </div>
                                </td>
                                <td><span class="grade-badge grade-<?= $grade ?>"><?= $grade ?></span></td>
                                <td><span class="date-str"><?= date('d M Y, H:i', strtotime($r['date_taken'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <h4>No Results Yet</h4>
                <p>You haven't taken any exams yet.</p>
                <br>
                <a href="exam.php" class="btn-exam">📚 Go to Exams →</a>
            </div>
        <?php endif; ?>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

</body>

</html>