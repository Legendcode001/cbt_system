<?php
session_start();
include("../config/database.php");

// 1. Protection Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../login.php");
    exit();
}

$msg = "";

// 2. Handle the Approval Request
if (isset($_GET['approve_id'])) {
    $teacher_id = intval($_GET['approve_id']);

    // Update the teacher's status to active/approved
    $update_sql = "UPDATE teachers SET status = 'approved' WHERE id = '$teacher_id'";
    if (mysqli_query($conn, $update_sql)) {
        $msg = "Teacher approved successfully!";
    } else {
        $msg = "Error approving teacher profile.";
    }
}

// 3. Fetch all pending teachers
$pending_teachers = mysqli_query($conn, "SELECT * FROM teachers WHERE status = 'pending' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Teachers — RECTEM CBT</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0a1628;
            --navy-light: #1d3461;
            --gold: #f4a916;
            --white: #fff;
            --off-white: #f0f4f8;
            --border: #e2e8f0;
            --success: #22c55e;
            --text-muted: #8899aa;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--off-white);
            color: var(--navy);
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        h2 {
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--navy);
            padding-bottom: 8px;
        }

        .alert {
            padding: 12px;
            background: #dcfce7;
            color: var(--success);
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
        }

        th {
            background: var(--navy);
            color: var(--white);
            font-weight: 600;
        }

        .btn-approve {
            background: var(--success);
            color: var(--white);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .btn-approve:hover {
            opacity: 0.9;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            color: var(--navy-light);
            text-decoration: none;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card">
            <h2>Verify Registered Teachers</h2>

            <?php if (!empty($msg)): ?>
                <div class="alert"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($pending_teachers) > 0): ?>
                        <?php while ($teacher = mysqli_fetch_assoc($pending_teachers)): ?>
                            <tr>
                                <td style="font-weight: 500;"><?= htmlspecialchars($teacher['name']) ?></td>
                                <td><?= htmlspecialchars($teacher['email']) ?></td>
                                <td>
                                    <a href="verify_teachers.php?approve_id=<?= $teacher['id'] ?>" class="btn-approve" onclick="return confirm('Are you sure you want to approve this teacher?')">✅ Approve</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="empty-state">🎉 No pending teacher verifications available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <a href="dashboard.php" class="back-btn">← Back to Admin Panel</a>
        </div>
    </div>

</body>

</html>