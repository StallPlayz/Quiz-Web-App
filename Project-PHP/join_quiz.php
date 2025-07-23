<?php
if (!isset($_GET['pin'])) {
    die("No PIN provided.");
}

$pin = trim($_GET['pin']);
$found = false;

$quizFiles = glob('quizzes/*.json');

foreach ($quizFiles as $file) {
    $quiz = json_decode(file_get_contents($file), true);

    // Check PIN and not created by this user
    if ($quiz['pin'] === $pin && $quiz['created_by'] !== $_SESSION['user']) {
        $found = true;
        $filename = basename($file);
        // Redirect to quiz
        header("Location: quiz.php?file=" . urlencode($filename));
        exit;
    }
}

if (!$found) {
    echo "<h3 style='color: red;'>Invalid PIN or this is your own quiz!</h3>";
    echo "<a href='home.php'>Go back</a>";
}

$_SESSION['join_error'] = "Invalid PIN or this is your own quiz!";
header("Location: home.php");
exit;
