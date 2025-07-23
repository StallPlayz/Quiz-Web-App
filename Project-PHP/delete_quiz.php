<?php
include 'auth.php';

if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $path = "quizzes/$file";

    if (file_exists($path)) {
        $quiz = json_decode(file_get_contents($path), true);

        // Optional: Only allow deleting if user created it
        if ($quiz['created_by'] === $_SESSION['user']) {
            unlink($path);
            header("Location: home.php?deleted=1");
            exit;
        } else {
            echo "You are not authorized to delete this quiz.";
        }
    } else {
        echo "Quiz not found.";
    }
} else {
    echo "No quiz specified.";
}
?>
