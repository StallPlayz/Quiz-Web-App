<?php
include 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    $filename = basename($_POST['filename']);
    $filepath = "quizzes/" . $filename;

    if (!file_exists($filepath)) {
        die("Original quiz file not found.");
    }

    $existing = json_decode(file_get_contents($filepath), true);
    if ($existing['created_by'] !== $_SESSION['user']) {
        die("Unauthorized to edit this quiz.");
    }

    $quizData = [
        'name' => $_POST['quiz_name'],
        'description' => $_POST['quiz_desc'],
        'questions' => $_POST['questions'],
        'created_by' => $_SESSION['user'],
        'pin' => $existing['pin'] // Preserve the existing PIN
    ];

    file_put_contents($filepath, json_encode($quizData, JSON_PRETTY_PRINT));
    header("Location: home.php?updated=1");
    exit;
} else {
    echo "Invalid request.";
}
