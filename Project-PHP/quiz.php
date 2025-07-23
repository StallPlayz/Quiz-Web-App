<?php
include 'auth.php';

// Check if both 'file' and 'pin' are missing
if (!isset($_GET['file']) && !isset($_GET['pin'])) {
    die("No quiz specified.");
}

$quizFile = null;

// Handle cases where only the PIN is provided
if (isset($_GET['pin']) && !isset($_GET['file'])) {
    $pin = $_GET['pin'];
    $quizFiles = glob('quizzes/*.json'); // Get all JSON files in the quizzes directory

    foreach ($quizFiles as $file) {
        $quizData = json_decode(file_get_contents($file), true);
        if (isset($quizData['pin']) && $quizData['pin'] === $pin) {
            $quizFile = $file;
            break;
        }
    }

    if (!$quizFile) {
        die("Invalid PIN or quiz not found.");
    }
} else {
    $quizFile = 'quizzes/' . basename($_GET['file']);
}

// Check if the quiz file exists
if (!file_exists($quizFile)) {
    die("Quiz not found.");
}

$quiz = json_decode(file_get_contents($quizFile), true);

// Validate PIN for non-creators
if ($_SESSION['user'] !== $quiz['created_by']) {
    // Check if a session variable indicates the PIN has already been validated
    if (!isset($_SESSION['validated_quizzes']) || !in_array($quizFile, $_SESSION['validated_quizzes'])) {
        // Handle POST request to check the PIN
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['pin']) && $_POST['pin'] === $quiz['pin']) {
                // Save the validated quiz in the session
                if (!isset($_SESSION['validated_quizzes'])) {
                    $_SESSION['validated_quizzes'] = [];
                }
                $_SESSION['validated_quizzes'][] = $quizFile;
            } else {
                // Invalid PIN
                echo "<p>Invalid PIN. Please try again.</p>";
?>
                <form method="post" action="">
                    <h2>Enter PIN to Join Quiz</h2>
                    <input type="text" name="pin" placeholder="Enter PIN" required>
                    <button type="submit">Join Quiz</button>
                </form>
<?php
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($quiz['name']); ?></title>
    <link rel="stylesheet" href="css/quizziz.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
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

        #question {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 25px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        #choices {
            margin-bottom: 25px;
        }

        #nextBtn {
            display: block;
            margin: 15px auto;
            font-size: 18px;
            padding: 15px 30px;
        }

        .completed {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .quiz-info {
            background: linear-gradient(135deg, #3c096c, #5a189a);
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            border: 2px solid rgba(255, 255, 255, 0.15);
            text-align: center;
        }

        .quiz-info h3 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #fff;
        }

        .quiz-info p {
            margin: 0;
            opacity: 0.8;
            font-size: 16px;
        }

        .popup {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(60, 9, 108, 0.9));
            backdrop-filter: blur(15px);
            color: white;
            border-radius: 20px;
            padding: 30px;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            z-index: 9999;
            text-align: center;
            display: none;
            width: 350px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            font-size: 18px;
            font-weight: bold;
        }

        .popup.show {
            display: block;
            transform: translate(-50%, -50%) scale(1);
        }

        .popup.correct {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.9));
            animation: correctPulse 0.6s ease;
        }

        .popup.incorrect {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.9), rgba(220, 38, 38, 0.9));
            animation: incorrectShake 0.6s ease;
        }

        @keyframes correctPulse {
            0% {
                transform: translate(-50%, -50%) scale(0.8);
            }

            50% {
                transform: translate(-50%, -50%) scale(1.1);
            }

            100% {
                transform: translate(-50%, -50%) scale(1);
            }
        }

        @keyframes incorrectShake {

            0%,
            100% {
                transform: translate(-50%, -50%) translateX(0);
            }

            25% {
                transform: translate(-50%, -50%) translateX(-10px);
            }

            75% {
                transform: translate(-50%, -50%) translateX(10px);
            }
        }

        .question-counter {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            opacity: 0.8;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(157, 78, 221, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 70%;
            right: 5%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 10%;
            left: 15%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-30px) rotate(180deg);
            }
        }
    </style>
</head>

<body>
    <!-- Floating Shapes Background -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container" data-aos="fade-up" data-aos-duration="800">
        <div class="question-counter" id="questionCounter" data-aos="fade-in" data-aos-delay="400">
            Question 1 of <?php echo count($quiz['questions']); ?>
        </div>

        <div class="progress-bar" data-aos="fade-in" data-aos-delay="500">
            <div id="progressFill" class="progress-bar-fill"></div>
        </div>

        <div id="quizBox" data-aos="fade-up" data-aos-delay="600">
            <div id="question"></div>
            <div id="choices"></div>
            <button id="nextBtn" onclick="nextQuestion()" data-aos="zoom-in" data-aos-delay="800">Next Question ➡️</button>
        </div>

        <div id="resultBox" style="display: none;" data-aos="zoom-in">
            <p class="completed">🎉 Quiz Completed!</p>

            <!-- Quiz info now appears here -->
            <div class="quiz-info">
                <h3><?php echo htmlspecialchars($quiz['name']); ?> 🎯</h3>
                <p><?php echo htmlspecialchars($quiz['description']); ?></p>
            </div>

            <div id="scoreDisplay" style="font-size: 20px; margin: 20px 0;"></div>
            <a href="home.php"><button>🏠 Back to Home</button></a>
        </div>
    </div>

    <!-- Enhanced Popup Box -->
    <div id="popup" class="popup"></div>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true
        });

        const questions = <?php echo json_encode($quiz['questions']); ?>;
        let current = 0;
        let score = 0;

        function renderQuestion() {
            const q = questions[current];
            document.getElementById("question").textContent = q.text;
            document.getElementById("questionCounter").textContent = `Question ${current + 1} of ${questions.length}`;

            const choicesBox = document.getElementById("choices");
            choicesBox.innerHTML = "";

            q.choices.forEach((c, i) => {
                const wrapper = document.createElement("label");
                wrapper.className = "choice-option";
                wrapper.setAttribute('data-aos', 'slide-up');
                wrapper.setAttribute('data-aos-delay', (i * 100 + 100).toString());

                const radio = document.createElement("input");
                radio.type = "radio";
                radio.name = "choice";
                radio.value = i;

                const span = document.createElement("span");
                span.textContent = c;

                wrapper.appendChild(radio);
                wrapper.appendChild(span);
                choicesBox.appendChild(wrapper);
            });

            // Re-initialize AOS for new elements
            AOS.refresh();
            updateProgress();
        }

        function showPopup(message, isCorrect, callback = null) {
            const popup = document.getElementById("popup");
            popup.textContent = message;
            popup.className = `popup show ${isCorrect ? 'correct' : 'incorrect'}`;

            setTimeout(() => {
                popup.className = 'popup';
                if (callback) callback();
            }, 1500);
        }

        function nextQuestion() {
            const selected = document.querySelector('input[name="choice"]:checked');
            if (!selected) {
                showPopup("❗ Please select an answer.", false);
                return;
            }

            const answer = parseInt(selected.value);
            const isCorrect = answer === parseInt(questions[current].answer);

            if (isCorrect) {
                score++;
                showPopup("✅ Correct!", true, () => {
                    advance();
                });
            } else {
                showPopup("❌ Wrong!", false, () => {
                    advance();
                });
            }

            // Disable all choices temporarily
            document.querySelectorAll('input[name="choice"]').forEach(input => {
                input.disabled = true;
            });
        }

        function advance() {
            current++;
            updateProgress();

            if (current < questions.length) {
                // Add fade out animation to current question
                const quizBox = document.getElementById("quizBox");
                quizBox.style.opacity = "0";
                quizBox.style.transform = "translateY(20px)";

                setTimeout(() => {
                    renderQuestion();
                    quizBox.style.opacity = "1";
                    quizBox.style.transform = "translateY(0)";
                }, 300);
            } else {
                showResults();
            }
        }

        function showResults() {
            const percentage = Math.round((score / questions.length) * 100);
            let emoji = "";
            let message = "";

            if (percentage >= 90) {
                emoji = "🏆";
                message = "Excellent!";
            } else if (percentage >= 70) {
                emoji = "🎉";
                message = "Great job!";
            } else if (percentage >= 50) {
                emoji = "👍";
                message = "Good effort!";
            } else {
                emoji = "📚";
                message = "Keep studying!";
            }

            document.getElementById("quizBox").style.display = "none";
            document.getElementById("resultBox").style.display = "block";
            document.getElementById("scoreDisplay").innerHTML = `
                ${emoji} ${message}<br>
                Score: ${score}/${questions.length} (${percentage}%)
            `;
            document.getElementById("progressFill").style.width = "100%";
        }

        function updateProgress() {
            const progress = ((current) / questions.length) * 100;
            document.getElementById("progressFill").style.width = progress + "%";
        }

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                nextQuestion();
            }

            // Number keys for choice selection
            if (e.key >= '1' && e.key <= '4') {
                const choiceIndex = parseInt(e.key) - 1;
                const radio = document.querySelector(`input[name="choice"][value="${choiceIndex}"]`);
                if (radio) {
                    radio.checked = true;
                }
            }
        });

        window.onload = function() {
            renderQuestion();

            // Add entrance animation
            setTimeout(() => {
                document.querySelector('.container').style.transform = 'scale(1)';
                document.querySelector('.container').style.opacity = '1';
            }, 100);
        };
    </script>
</body>

</html>