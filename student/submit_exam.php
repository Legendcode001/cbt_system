<?php
include("../includes/auth.php");
include("../config/database.php");

$student_id = $_SESSION['user_id'];
$exam_id = $_GET['exam_id'] ?? 1;

$score = 0;

// get questions
$questions = $conn->query("SELECT * FROM questions WHERE exam_id='$exam_id'");

$total = $questions->num_rows;

while ($q = $questions->fetch_assoc()) {

    $qid = $q['id'];

    if (isset($_POST["q$qid"])) {

        $answer = $_POST["q$qid"];

        $conn->query("INSERT INTO answers(student_id, exam_id, question_id, answer)
VALUES('$student_id','$exam_id','$qid','$answer')");

        if ($answer == $q['correct_answer']) {
            $score++;
        }
    }
}

// save result
$conn->query("INSERT INTO results(student_id, exam_id, score, total_questions)
VALUES('$student_id','$exam_id','$score','$total')");

header("Location: result.php");
exit();
