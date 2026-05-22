<?php
session_start();
include("../config/database.php");

// 1. Strict Role Protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

$msg = "";
$msg_type = "success";

// =========================================================
// 2. HANDLE DELETE & APPROVAL ACTIONS
// =========================================================
// Delete Student
if (isset($_GET['delete_student'])) {
    $student_id = intval($_GET['delete_student']);
    $delete_sql = "DELETE FROM users WHERE id = $student_id AND role = 'student'";
    if (mysqli_query($conn, $delete_sql)) {
        $msg = "Student account deleted successfully.";
    } else {
        $msg = "Error deleting student account.";
        $msg_type = "error";
    }
}

// Delete Teacher
if (isset($_GET['delete_teacher'])) {
    $teacher_id = intval($_GET['delete_teacher']);
    $delete_sql = "DELETE FROM teachers WHERE id = $teacher_id";
    if (mysqli_query($conn, $delete_sql)) {
        $msg = "Teacher account deleted successfully.";
    } else {
        $msg = "Error deleting teacher account.";
        $msg_type = "error";
    }
}

// Quick Approve Teacher from Dashboard
if (isset($_GET['approve_teacher'])) {
    $teacher_id = intval($_GET['approve_teacher']);
    $approve_sql = "UPDATE teachers SET status = 'approved' WHERE id = $teacher_id";
    if (mysqli_query($conn, $approve_sql)) {
        $msg = "Teacher approved successfully.";
    } else {
        $msg = "Error approving teacher profile.";
        $msg_type = "error";
    }
}

// =========================================================
// 3. FETCH METRICS
// =========================================================
$students_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'student'"))['total'] ?? 0;
$pending_teachers_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM teachers WHERE status = 'pending'"))['total'] ?? 0;
$active_teachers_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM teachers WHERE status = 'approved'"))['total'] ?? 0;

// Determine what view to show based on URL parameters (?view=...)
$current_view = $_GET['view'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — RECTEM CBT</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-dark: #0a1628;
            --navy-light: #1d3461;
            --gold: #f4a916;
            --white: #ffffff;
            --bg-gray: #f8fafc;
            --border-gray: #e2e8f0;
            --text-main: #334155;
            --text-muted: #64748b;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Sora', sans-serif;
            background-color: var(--bg-gray);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px;
            background-color: var(--navy-dark);
            color: var(--white);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
        }

        .sidebar-brand {
            padding: 24px;
            font-size: 18px;
            font-weight: 700;
            color: var(--gold);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            flex-grow: 1;
        }

        .sidebar-item a {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-item a:hover,
        .sidebar-item.active a {
            color: var(--white);
            background-color: var(--navy-light);
            border-left: 4px solid var(--gold);
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-logout {
            display: block;
            text-align: center;
            background-color: rgba(239, 68, 68, 0.1);
            color: #f87171;
            text-decoration: none;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        /* --- MAIN WORKSPACE --- */
        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 40px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-gray);
            padding-bottom: 20px;
        }

        .header-title h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy-dark);
        }

        .header-title p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* --- METRICS INTERACTIVE CARDS --- */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .card-link {
            text-decoration: none;
            display: block;
        }

        .card {
            background-color: var(--white);
            padding: 24px;
            border-radius: 10px;
            border: 1px solid var(--border-gray);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .card:hover,
        .card.active-filter {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .card.active-filter {
            border-bottom: 4px solid var(--gold) !important;
        }

        .card-title {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--text-muted);
        }

        .card-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--navy-dark);
            margin-top: 10px;
        }

        .card-badge {
            display: inline-block;
            margin-top: 12px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #d97706;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-info {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        /* --- DATATABLES PANEL --- */
        .management-panel {
            background-color: var(--white);
            border-radius: 10px;
            border: 1px solid var(--border-gray);
            padding: 24px;
        }

        .panel-heading {
            font-size: 16px;
            font-weight: 600;
            color: var(--navy-dark);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px 16px;
            text-align: left;
            font-size: 14px;
            border-bottom: 1px solid var(--border-gray);
        }

        th {
            background-color: var(--bg-gray);
            color: var(--navy-dark);
            font-weight: 600;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #15803d;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* --- BUTTONS --- */
        .btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }

        .btn-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .btn-danger:hover {
            background-color: var(--danger);
            color: var(--white);
        }

        .btn-success {
            background-color: rgba(34, 197, 94, 0.1);
            color: var(--success);
            margin-right: 5px;
        }

        .btn-success:hover {
            background-color: var(--success);
            color: var(--white);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
            font-size: 14px;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">🏢 RECTEM CBT ADMIN</div>
        <ul class="sidebar-menu">
            <li class="sidebar-item active"><a href="dashboard.php">📊 System Dashboard</a></li>
            <li class="sidebar-item"><a href="verify_teachers.php">🧑‍🏫 Teacher Verifications</a></li>
            <li class="sidebar-item"><a href="admin_profile.php">⚙️ Security Settings</a></li>
        </ul>
        <div class="sidebar-footer"><a href="../logout.php" class="btn-logout">Sign Out</a></div>
    </aside>

    <!-- MAIN FRAME -->
    <main class="main-content">
        <header class="header-section">
            <div class="header-title">
                <h1>Welcome Back, Administrator</h1>
                <p>Click on any summary card below to filter the accounts lists and perform maintenance actions.</p>
            </div>
        </header>

        <!-- STATUS MESSAGES -->
        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <!-- INTERACTIVE CARD LAYOUT -->
        <section class="metrics-grid">
            <!-- Pending Teachers Card -->
            <a href="dashboard.php?view=pending" class="card-link">
                <div class="card <?= $current_view === 'pending' ? 'active-filter' : '' ?>" style="border-top: 4px solid var(--warning);">
                    <div class="card-title">Pending Teachers</div>
                    <div class="card-value"><?= $pending_teachers_total ?></div>
                    <span class="card-badge badge-warning">Requires Verification</span>
                </div>
            </a>

            <!-- Active Teachers Card -->
            <a href="dashboard.php?view=teachers" class="card-link">
                <div class="card <?= $current_view === 'teachers' ? 'active-filter' : '' ?>" style="border-top: 4px solid var(--success);">
                    <div class="card-title">Approved Teachers</div>
                    <div class="card-value"><?= $active_teachers_total ?></div>
                    <span class="card-badge badge-success">Active Faculty</span>
                </div>
            </a>

            <!-- Total Students Card -->
            <a href="dashboard.php?view=students" class="card-link">
                <div class="card <?= $current_view === 'students' ? 'active-filter' : '' ?>" style="border-top: 4px solid var(--navy-light);">
                    <div class="card-title">Total Students</div>
                    <div class="card-value"><?= $students_total ?></div>
                    <span class="card-badge badge-info">Registered Users</span>
                </div>
            </a>
        </section>

        <!-- FILTERED DATA MANAGEMENT TABLE -->
        <section class="management-panel">
            <div class="panel-heading">
                <span>
                    ✨ Managing:
                    <strong>
                        <?php
                        if ($current_view === 'students') echo "Enrolled Students List";
                        elseif ($current_view === 'teachers') echo "Approved Faculty List";
                        else echo "Pending Teacher Verifications";
                        ?>
                    </strong>
                </span>
            </div>

            <table>
                <?php if ($current_view === 'students'): ?>
                    <!-- STUDENTS VIEW -->
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $records = mysqli_query($conn, "SELECT * FROM users WHERE role = 'student' ORDER BY id DESC");
                        if (mysqli_num_rows($records) > 0):
                            while ($row = mysqli_fetch_assoc($records)): ?>
                                <tr>
                                    <td style="font-weight: 500;"><?= htmlspecialchars($row['NAME'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                    <td style="text-align: right;">
                                        <a href="dashboard.php?view=students&delete_student=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you completely sure you want to permanently delete this student account?')">🗑️ Delete Account</a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="4" class="empty-state">No registered students found in database.</td>
                            </tr>
                        <?php endif; ?>

                    <?php elseif ($current_view === 'teachers'): ?>
                        <!-- APPROVED TEACHERS VIEW -->
                        <thead>
                            <tr>
                                <th>Teacher Name</th>
                                <th>Email Address</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php
                        $records = mysqli_query($conn, "SELECT * FROM teachers WHERE status = 'approved' ORDER BY id DESC");
                        if (mysqli_num_rows($records) > 0):
                            while ($row = mysqli_fetch_assoc($records)): ?>
                                <tr>
                                    <td style="font-weight: 500;"><?= htmlspecialchars($row['name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                    <td style="text-align: right;">
                                        <a href="dashboard.php?view=teachers&delete_teacher=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to permanently delete this teacher?')">🗑️ Remove Access</a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="3" class="empty-state">No approved faculty found.</td>
                            </tr>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- PENDING TEACHERS VIEW -->
                        <thead>
                            <tr>
                                <th>Teacher Name</th>
                                <th>Email Address</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php
                        $records = mysqli_query($conn, "SELECT * FROM teachers WHERE status = 'pending' ORDER BY id DESC");
                        if (mysqli_num_rows($records) > 0):
                            while ($row = mysqli_fetch_assoc($records)): ?>
                                <tr>
                                    <td style="font-weight: 500;"><?= htmlspecialchars($row['name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                    <td style="text-align: right;">
                                        <a href="dashboard.php?view=pending&approve_teacher=<?= $row['id'] ?>" class="btn btn-success">✅ Approve</a>
                                        <a href="dashboard.php?view=pending&delete_teacher=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Deny and remove this pending teacher?')">❌ Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="3" class="empty-state">🎉 Excellent! No pending teacher verifications waiting.</td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                    </tbody>
            </table>
        </section>
    </main>

</body>

</html>