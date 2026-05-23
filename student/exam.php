<?php
// student/exam.php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// ── Pull student identity from session (all possible keys) ──
$student_name = $_SESSION['student_name']
    ?? $_SESSION['NAME']
    ?? $_SESSION['name']
    ?? '';

$student_reg = $_SESSION['student_reg_no']
    ?? $_SESSION['matric']
    ?? $_SESSION['reg']
    ?? '';

// Fallback: re-fetch from DB if session is incomplete
if (empty($student_name) || empty($student_reg) || $student_name === 'Unknown Student') {
    $uid = intval($_SESSION['user_id']);
    $uq  = mysqli_query($conn, "SELECT NAME, matric FROM users WHERE id = '$uid'");
    if ($uq && $row = mysqli_fetch_assoc($uq)) {
        $student_name = $row['NAME'];
        $student_reg  = $row['matric'] ?? $row['email'];
        // Update session too
        $_SESSION['student_name']   = $student_name;
        $_SESSION['student_reg_no'] = $student_reg;
        $_SESSION['matric']         = $row['matric'];
    }
}

$student_name = $student_name ?: 'Unknown Student';
$student_reg  = $student_reg  ?: 'N/A';

$exam_id   = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
$questions = null;
$exam_data = null;

/* ── LOAD COURSES WITH QUESTIONS ────────────────────────── */
$courses_with_questions = [];
$exam_result = mysqli_query($conn, "SELECT * FROM courses ORDER BY course_code");

if ($exam_result && mysqli_num_rows($exam_result) > 0) {
    $all_courses = [];
    while ($c = mysqli_fetch_assoc($exam_result)) $all_courses[] = $c;

    foreach ($all_courses as $course) {
        $cid   = intval($course['id']);
        $cr    = mysqli_query($conn, "SELECT COUNT(*) as total FROM questions WHERE course_id = '$cid'");
        $crow  = mysqli_fetch_assoc($cr);
        if ($crow['total'] > 0) {
            $course['question_count'] = $crow['total'];
            $courses_with_questions[] = $course;
        }
    }
}

/* ── LOAD SINGLE EXAM ───────────────────────────────────── */
if ($exam_id > 0) {
    $er = mysqli_query($conn, "SELECT * FROM courses WHERE id = '$exam_id'");
    if ($er && mysqli_num_rows($er) > 0) $exam_data = mysqli_fetch_assoc($er);

    $questions = mysqli_query($conn, "SELECT * FROM questions WHERE course_id = '$exam_id' ORDER BY id");
}

/* ── SUBMIT EXAM ────────────────────────────────────────── */
if (isset($_POST['submit_exam'])) {
    $exam_id = intval($_POST['exam_id']);

    $qr    = mysqli_query($conn, "SELECT * FROM questions WHERE course_id = '$exam_id'");
    $score = 0;
    $total = 0;

    while ($row = mysqli_fetch_assoc($qr)) {
        $qid = $row['id'];
        $total++;
        if (isset($_POST['answer'][$qid]) && $_POST['answer'][$qid] == $row['correct']) {
            $score++;
        }
    }

    $uid       = intval($_SESSION['user_id']);
    $safe_name = mysqli_real_escape_string($conn, $student_name);
    $safe_reg  = mysqli_real_escape_string($conn, $student_reg);
    $percent   = $total > 0 ? round(($score / $total) * 100) : 0;

    mysqli_query($conn, "INSERT INTO results (student_uid, student_name, student_reg_no, course_id, score, total_questions)
        VALUES ('$uid','$safe_name','$safe_reg','$exam_id','$score','$total')");

    echo "<script>
        alert('Exam Submitted!\\nScore: $score / $total ($percent%)');
        window.location='result.php';
    </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rectem CBT Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel=" icon" href="../assets/images/launcher_iconn.png" type="image/png">
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
            --success: #22c55e;
            --danger: #ef4444;
            --card-bg: #ffffff;
            --border: #e2e8f0;
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

        /* TOP BAR */
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

        .top-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* COUNTDOWN TIMER */
        .timer-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--navy-light);
            border: 2px solid var(--gold);
            padding: 7px 16px;
            border-radius: 30px;
        }

        .timer-badge .timer-label {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .timer-badge .timer-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 18px;
            font-weight: 700;
            color: var(--gold);
            min-width: 70px;
            text-align: center;
        }

        .timer-badge.warning .timer-value {
            color: #f97316;
        }

        .timer-badge.danger .timer-value {
            color: var(--danger);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
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

        .student-badge .avatar {
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
            font-size: 13px;
        }

        .sreg {
            color: var(--text-muted);
            font-size: 11px;
            font-family: 'JetBrains Mono', monospace;
        }

        /* PAGE */
        .page-wrap {
            max-width: 860px;
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

        /* EXAM CARD (list) */
        .exam-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px 28px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .exam-card:hover {
            box-shadow: 0 8px 30px rgba(10, 22, 40, 0.1);
            transform: translateY(-2px);
        }

        .exam-card .left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .exam-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .course-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 500;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .course-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 6px;
        }

        .meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .meta-pill {
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .meta-pill.highlight {
            background: #fffbeb;
            border-color: var(--gold);
            color: #92400e;
        }

        .btn-start {
            background: var(--gold);
            color: var(--navy);
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.2s, transform 0.1s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-start:hover {
            background: var(--gold-light);
            transform: scale(1.03);
        }

        /* EXAM HEADER */
        .exam-header {
            background: var(--navy);
            border-radius: 14px;
            padding: 24px 28px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--white);
        }

        .exam-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .exam-meta {
            font-size: 13px;
            color: #8899aa;
        }

        /* PROGRESS */
        .progress-bar-wrap {
            background: var(--border);
            border-radius: 30px;
            height: 6px;
            margin-bottom: 28px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
            border-radius: 30px;
            transition: width 0.4s ease;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        /* QUESTION CARD */
        .question-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px;
            margin-bottom: 20px;
            animation: fadeUp 0.3s ease both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .q-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 500;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .q-text {
            font-size: 16px;
            font-weight: 500;
            color: var(--navy);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .options-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .option-item {
            position: relative;
        }

        .option-item input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .option-label {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border: 2px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 15px;
            color: #334155;
        }

        .option-label:hover {
            border-color: var(--gold);
            background: #fffdf5;
        }

        .option-item input[type="radio"]:checked+.option-label {
            border-color: var(--gold);
            background: #fffbeb;
            color: var(--navy);
            font-weight: 600;
        }

        .opt-badge {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--off-white);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            flex-shrink: 0;
            transition: all 0.15s;
        }

        .option-item input[type="radio"]:checked+.option-label .opt-badge {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--navy);
        }

        /* SUBMIT */
        .submit-wrap {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-top: 10px;
        }

        .submit-info {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .submit-info strong {
            color: var(--navy);
            display: block;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            padding: 14px 36px;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            white-space: nowrap;
        }

        .btn-submit:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            margin-top: 20px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--navy);
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

        /* UNANSWERED WARNING */
        .unanswered-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 14px 18px;
            color: #dc2626;
            font-size: 13px;
            margin-bottom: 20px;
            display: none;
        }

        .unanswered-alert.show {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>

<body>

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="brand">
            <div class="brand-icon">📋</div>
            CBT <span>Portal</span>
        </div>
        <div class="top-right">
            <?php if ($exam_id > 0 && $exam_data): ?>
                <div class="timer-badge" id="timerBadge">
                    <div>
                        <div class="timer-label">Time Left</div>
                        <div class="timer-value" id="timerDisplay">--:--</div>
                    </div>
                    ⏱
                </div>
            <?php endif; ?>
            <div class="student-badge">
                <div class="avatar"><?= strtoupper(substr($student_name, 0, 1)) ?></div>
                <div>
                    <div class="sname"><?= htmlspecialchars($student_name) ?></div>
                    <div class="sreg"><?= htmlspecialchars($student_reg) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-wrap">

        <?php if (!$exam_id): ?>

            <!-- EXAM LIST -->
            <div class="page-title">📚 Available Exams</div>
            <div class="page-sub">Select a course below to begin your examination</div>

            <?php if (count($courses_with_questions) > 0): ?>
                <?php foreach ($courses_with_questions as $course): ?>
                    <div class="exam-card">
                        <div class="left">
                            <div class="exam-icon">📖</div>
                            <div>
                                <div class="course-code"><?= htmlspecialchars($course['course_code']) ?></div>
                                <div class="course-name"><?= htmlspecialchars($course['course_name']) ?></div>
                                <div class="meta">
                                    <span class="meta-pill">❓ <?= $course['question_count'] ?> Questions</span>
                                    <span class="meta-pill">👨‍🏫 <?= htmlspecialchars($course['teacher_name']) ?></span>
                                    <?php if (!empty($course['duration'])): ?>
                                        <span class="meta-pill highlight">⏱ <?= intval($course['duration']) ?> mins</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <a href="?exam_id=<?= $course['id'] ?>" class="btn-start">Start Exam →</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <h4>No Exams Available</h4>
                    <p>Your teacher hasn't added any questions yet. Check back later.</p>
                </div>
            <?php endif; ?>

        <?php else: ?>

            <!-- EXAM TAKING -->
            <?php $total_q = $questions ? mysqli_num_rows($questions) : 0;
            $duration_mins = intval($exam_data['duration'] ?? 30); ?>

            <div class="exam-header">
                <div>
                    <div class="exam-title">
                        <?= $exam_data ? htmlspecialchars($exam_data['course_code'] . ' — ' . $exam_data['course_name']) : 'Examination' ?>
                    </div>
                    <div class="exam-meta">
                        <?= $total_q ?> Questions &nbsp;·&nbsp; <?= $duration_mins ?> minutes &nbsp;·&nbsp; <?= htmlspecialchars($student_name) ?> (<?= htmlspecialchars($student_reg) ?>)
                    </div>
                </div>
                <div style="font-size:36px;">📝</div>
            </div>

            <div id="unansweredAlert" class="unanswered-alert">
                ⚠️ <span id="unansweredText">You have unanswered questions. Please answer all questions before submitting.</span>
            </div>

            <form method="POST" id="examForm">
                <input type="hidden" name="exam_id" value="<?= $exam_id ?>">

                <?php if ($questions && $total_q > 0): ?>

                    <div class="progress-label">
                        <span>Progress</span>
                        <span id="progressText">0 / <?= $total_q ?> answered</span>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill" id="progressBar" style="width:0%"></div>
                    </div>

                    <?php $i = 1;
                    while ($q = mysqli_fetch_assoc($questions)): ?>
                        <div class="question-card" style="animation-delay:<?= ($i - 1) * 0.04 ?>s">
                            <div class="q-number">Question <?= $i ?> of <?= $total_q ?></div>
                            <div class="q-text"><?= htmlspecialchars($q['question']) ?></div>
                            <div class="options-grid">
                                <?php foreach (['A', 'B', 'C', 'D'] as $opt): ?>
                                    <div class="option-item">
                                        <input type="radio" name="answer[<?= $q['id'] ?>]"
                                            value="<?= $opt ?>" id="q<?= $q['id'] . $opt ?>"
                                            onchange="updateProgress()">
                                        <label for="q<?= $q['id'] . $opt ?>" class="option-label">
                                            <span class="opt-badge"><?= $opt ?></span>
                                            <?= htmlspecialchars($q['opt' . $opt]) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php $i++;
                    endwhile; ?>

                    <div class="submit-wrap">
                        <div class="submit-info">
                            <strong>Ready to submit?</strong>
                            Make sure you've answered all <?= $total_q ?> questions before submitting.
                        </div>
                        <button type="submit" name="submit_exam" class="btn-submit" id="submitBtn"
                            onclick="return confirmSubmit()">Submit Exam ✓</button>
                    </div>

                <?php else: ?>
                    <div class="empty-state">
                        <div class="icon">⚠️</div>
                        <h4>No Questions Found</h4>
                        <p>This exam has no questions yet. Please contact your teacher.</p>
                    </div>
                <?php endif; ?>
            </form>

        <?php endif; ?>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>

    </div>

    <script>
        const TOTAL_Q = <?= $total_q ?? 0 ?>;
        const DURATION_SEC = <?= ($exam_id > 0 ? ($duration_mins ?? 30) * 60 : 0) ?>;
        let timeLeft = DURATION_SEC;
        let timerInterval = null;

        // ── PROGRESS ─────────────────────────────────────────────────
        function updateProgress() {
            const checked = document.querySelectorAll('input[type="radio"]:checked');
            const answered = new Set([...checked].map(r => r.name)).size;
            const pct = TOTAL_Q > 0 ? Math.round((answered / TOTAL_Q) * 100) : 0;
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('progressText').textContent = answered + ' / ' + TOTAL_Q + ' answered';
        }

        // ── TIMER ─────────────────────────────────────────────────────
        function formatTime(s) {
            const m = Math.floor(s / 60);
            const sec = s % 60;
            return String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
        }

        function startTimer() {
            const badge = document.getElementById('timerBadge');
            const display = document.getElementById('timerDisplay');
            if (!badge || !display) return;

            display.textContent = formatTime(timeLeft);

            timerInterval = setInterval(() => {
                timeLeft--;
                display.textContent = formatTime(Math.max(0, timeLeft));

                if (timeLeft <= 300) badge.classList.add('warning'); // 5 min
                if (timeLeft <= 60) {
                    badge.classList.remove('warning');
                    badge.classList.add('danger');
                }

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    alert('⏰ Time is up! Your exam will now be submitted automatically.');
                    document.getElementById('examForm').submit();
                }
            }, 1000);
        }

        // ── SUBMIT CONFIRM ────────────────────────────────────────────
        function confirmSubmit() {
            const checked = document.querySelectorAll('input[type="radio"]:checked');
            const answered = new Set([...checked].map(r => r.name)).size;
            const uAlert = document.getElementById('unansweredAlert');

            if (answered < TOTAL_Q) {
                const left = TOTAL_Q - answered;
                document.getElementById('unansweredText').textContent =
                    `You have ${left} unanswered question${left > 1 ? 's' : ''}. Please answer all before submitting.`;
                uAlert.classList.add('show');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                return false;
            }
            uAlert.classList.remove('show');
            return confirm('Submit exam? You cannot change answers after submission.');
        }

        // ── INIT ──────────────────────────────────────────────────────
        if (DURATION_SEC > 0) startTimer();
    </script>
</body>

</html>