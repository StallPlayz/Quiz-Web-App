<?php
include 'auth.php';
require 'config.php';

// Function to generate a unique PIN
function generateUniquePin($length = 6)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pin = '';
    $charLength = strlen($characters);

    for ($i = 0; $i < $length; $i++) {
        $pin .= $characters[rand(0, $charLength - 1)];
    }
    return $pin;
}

// Function to check if a PIN is unique across all quizzes
function isPinUnique($pin, $dir = 'quizzes')
{
    foreach (glob("$dir/*.json") as $file) {
        $quizData = json_decode(file_get_contents($file), true);
        if (isset($quizData['pin']) && $quizData['pin'] === $pin) {
            return false;
        }
    }
    return true;
}

// Function to generate a unique PIN
function getUniquePin($dir = 'quizzes')
{
    do {
        $pin = generateUniquePin();
    } while (!isPinUnique($pin, $dir));

    return $pin;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Quiz Saved</title>
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
    </style>
</head>

<body>
    <div class="container">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Define the 'pin' key here
            $quizData = [
                'name' => $_POST['quiz_name'], // Quiz name from form
                'description' => $_POST['quiz_desc'], // Quiz description from form
                'questions' => $_POST['questions'], // Array of questions
                'created_by' => $_SESSION['user'], // Creator's username
                'pin' => getUniquePin() // Unique randomized PIN
            ];

            // Ensure the quizzes directory exists
            if (!file_exists('quizzes')) mkdir('quizzes');

            // Save the quiz as a JSON file
            $fileName = 'quizzes/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['quiz_name']) . '_' . time() . '.json';
            file_put_contents($fileName, json_encode($quizData, JSON_PRETTY_PRINT));

            // Output success message
            echo "<h2>Quiz saved successfully!</h2>";
            echo "<p>Share this PIN with others to join your quiz: <strong>{$quizData['pin']}</strong></p>";
            echo "<p><a href='home.php'><button>Back to Home</button></a></p>";
        } else {
            echo "<h2>Invalid request.</h2>";
            echo "<p><a href='create_quiz.php'><button>Try Again</button></a></p>";
        }
        ?>
    </div>
</body>

</html>