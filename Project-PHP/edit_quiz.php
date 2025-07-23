<?php
include 'auth.php';

if (!isset($_GET['file'])) {
    die("No quiz specified.");
}

$filename = basename($_GET['file']);
$filepath = "quizzes/" . $filename;

if (!file_exists($filepath)) {
    die("Quiz not found.");
}

$quiz = json_decode(file_get_contents($filepath), true);

// Optional: Prevent editing someone else's quiz
if ($quiz['created_by'] !== $_SESSION['user']) {
    die("You are not authorized to edit this quiz.");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="css/quizziz.css">
    <style>
        body {
            cursor: default;
            user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }

        button {
            cursor: pointer;
        }

        textarea,
        input {
            cursor: text;
            user-select: text;
        }

        .question-block {
            background-color: #3c096c;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }

        textarea,
        select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        hr {
            border: 0;
            border-top: 1px solid #7b2cbf;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Quiz</h2>
        <form method="post" action="update_quiz.php">
            <input type="hidden" name="filename" value="<?php echo htmlspecialchars($filename); ?>">
            <input type="text" name="quiz_name" value="<?php echo htmlspecialchars($quiz['name']); ?>" required>
            <textarea name="quiz_desc" rows="3" required><?php echo htmlspecialchars($quiz['description']); ?></textarea>

            <div id="questions">
                <?php foreach ($quiz['questions'] as $index => $q): ?>
                    <div class="question-block">
                        <input type="text" name="questions[<?php echo $index; ?>][text]" value="<?php echo htmlspecialchars($q['text']); ?>" required><br>
                        <?php foreach ($q['choices'] as $ci => $choice): ?>
                            <input type="text" name="questions[<?php echo $index; ?>][choices][]" value="<?php echo htmlspecialchars($choice); ?>" required>
                        <?php endforeach; ?>
                        <select name="questions[<?php echo $index; ?>][answer]" required>
                            <option value="">Correct Answer</option>
                            <option value="0" <?php if ($q['answer'] == 0) echo "selected"; ?>>A</option>
                            <option value="1" <?php if ($q['answer'] == 1) echo "selected"; ?>>B</option>
                            <option value="2" <?php if ($q['answer'] == 2) echo "selected"; ?>>C</option>
                            <option value="3" <?php if ($q['answer'] == 3) echo "selected"; ?>>D</option>
                        </select>
                        <hr>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit">Update Quiz</button>
        </form>
    </div>
</body>

</html>