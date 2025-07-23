<?php
include 'auth.php';
?>

<!DOCTYPE html>
<html>

<head>
  <title>Create Quiz</title>
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

    textarea {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      margin-top: 10px;
      resize: vertical;
      min-height: 80px;
    }

    .question-block {
      background: linear-gradient(135deg, #3c096c, #5a189a);
      padding: 20px;
      border-radius: 15px;
      margin-top: 20px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.4s ease;
    }

    .question-block:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(60, 9, 108, 0.4);
    }

    select {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      margin-top: 10px;
    }

    hr {
      border: 0;
      border-top: 1px solid rgba(123, 44, 191, 0.5);
      margin: 25px 0;
    }

    .question-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .question-number {
      background: linear-gradient(135deg, #9d4edd, #c77dff);
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      font-weight: bold;
      font-size: 14px;
    }

    .remove-question {
      background: linear-gradient(135deg, #ff4d4d, #dc2626);
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12px;
      transition: all 0.3s ease;
    }

    .remove-question:hover {
      transform: scale(1.1);
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
      width: 120px;
      height: 120px;
      top: 15%;
      left: 8%;
      animation-delay: 0s;
    }

    .shape:nth-child(2) {
      width: 180px;
      height: 180px;
      top: 60%;
      right: 8%;
      animation-delay: 2s;
    }

    .shape:nth-child(3) {
      width: 90px;
      height: 90px;
      bottom: 15%;
      left: 25%;
      animation-delay: 4s;
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0px) rotate(0deg);
      }

      50% {
        transform: translateY(-25px) rotate(180deg);
      }
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #fff;
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
    <h2 data-aos="fade-down" data-aos-delay="200">✨ Create a New Quiz</h2>

    <form id="quizForm" method="post" action="save_quiz.php">
      <div class="form-group" data-aos="fade-right" data-aos-delay="300">
        <label for="quiz_name">Quiz Name:</label>
        <input type="text" id="quiz_name" name="quiz_name" placeholder="Enter an engaging quiz name" required>
      </div>

      <div class="form-group" data-aos="fade-left" data-aos-delay="400">
        <label for="quiz_desc">Quiz Description:</label>
        <textarea id="quiz_desc" name="quiz_desc" placeholder="Describe what this quiz is about..." rows="3" required></textarea>
      </div>

      <div id="questions" data-aos="fade-up" data-aos-delay="500"></div>

      <div style="display: flex; gap: 15px; margin-top: 25px;" data-aos="zoom-in" data-aos-delay="600">
        <button type="button" onclick="addQuestion()" style="flex: 1; background: linear-gradient(135deg, #10b981, #059669);">➕ Add Question</button>
        <button type="submit" style="flex: 1; background: linear-gradient(135deg, #7b2cbf, #9d4edd);">💾 Save Quiz</button>
      </div>
    </form>
  </div>

  <script>
    // Initialize AOS
    AOS.init({
      duration: 800,
      easing: 'ease-out',
      once: true
    });

    let qCount = 0;

    function addQuestion() {
      const container = document.getElementById('questions');
      const div = document.createElement('div');
      div.className = "question-block";
      div.setAttribute('data-aos', 'zoom-in');
      div.setAttribute('data-aos-delay', '200');

      // In create_quiz.php, update the addQuestion() function's innerHTML:
      div.innerHTML = `
<div class="question-header">
  <span class="question-number">Question ${qCount + 1}</span>
  <button type="button" class="remove-question" onclick="removeQuestion(this)">🗑️ Remove</button>
</div>
<input type="text" name="questions[${qCount}][text]" placeholder="Enter your question here..." required>
<div class="choice-grid">
  <input type="text" name="questions[${qCount}][choices][]" placeholder="Choice A" required>
  <input type="text" name="questions[${qCount}][choices][]" placeholder="Choice B" required>
  <input type="text" name="questions[${qCount}][choices][]" placeholder="Choice C" required>
  <input type="text" name="questions[${qCount}][choices][]" placeholder="Choice D" required>
</div>
<select name="questions[${qCount}][answer]" required>
  <option value="">Select the correct answer</option>
  <option value="0">Choice A</option>
  <option value="1">Choice B</option>
  <option value="2">Choice C</option>
  <option value="3">Choice D</option>
</select>
`;

      container.appendChild(div);
      qCount++;

      // Add entrance animation
      setTimeout(() => {
        div.style.transform = 'translateY(0)';
        div.style.opacity = '1';
      }, 100);

      // Re-initialize AOS for new elements
      AOS.refresh();

      // Scroll to new question
      div.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
    }

    function removeQuestion(button) {
      const questionBlock = button.closest('.question-block');

      // Add exit animation
      questionBlock.style.transform = 'translateX(-100%)';
      questionBlock.style.opacity = '0';

      setTimeout(() => {
        questionBlock.remove();
        updateQuestionNumbers();
      }, 300);
    }

    function updateQuestionNumbers() {
      const questionBlocks = document.querySelectorAll('.question-block');

      questionBlocks.forEach((block, index) => {
        // Update the visual question number
        const numberSpan = block.querySelector('.question-number');
        if (numberSpan) {
          numberSpan.textContent = `Question ${index + 1}`;
        }

        // Update all form field names to maintain proper indexing
        const textInput = block.querySelector('input[type="text"]:first-of-type');
        if (textInput) {
          textInput.name = `questions[${index}][text]`;
        }

        const choiceInputs = block.querySelectorAll('input[type="text"]:not(:first-of-type)');
        choiceInputs.forEach(input => {
          input.name = `questions[${index}][choices][]`;
        });

        const selectElement = block.querySelector('select');
        if (selectElement) {
          selectElement.name = `questions[${index}][answer]`;
        }
      });

      // Update the global counter to reflect current number of questions
      qCount = questionBlocks.length;
    }

    // Form validation
    document.getElementById('quizForm').addEventListener('submit', function(e) {
      const questions = document.querySelectorAll('.question-block');

      if (questions.length === 0) {
        e.preventDefault();
        alert('Please add at least one question to your quiz! 📝');
        return false;
      }

      if (questions.length < 2) {
        e.preventDefault();
        alert('Please add at least 2 questions to make a proper quiz! 🎯');
        return false;
      }

      // Show loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      submitBtn.innerHTML = '⏳ Saving Quiz...';
      submitBtn.disabled = true;
    });

    // Auto-add first question when page loads
    window.onload = function() {
      setTimeout(() => {
        addQuestion();
      }, 1000);
    };

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      if (e.ctrlKey || e.metaKey) {
        if (e.key === 'Enter') {
          e.preventDefault();
          addQuestion();
        }
      }
    });
  </script>

</body>

</html>