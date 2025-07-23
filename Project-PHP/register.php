<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $user = $_POST['username'];
  $pass = $_POST['password'];
  $confirm = $_POST['confirm_password'];

  if ($pass !== $confirm) {
    $error = "Passwords do not match!";
  } else {
    $hashed = password_hash($pass, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    try {
      $stmt->execute(['username' => $user, 'password' => $hashed]);
      header("Location: login.php");
      exit();
    } catch (PDOException $e) {
      $error = "Username already taken or database error.";
    }
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Register</title>
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
  <script>
    function validateForm() {
      const pass = document.forms["regForm"]["password"].value;
      const confirm = document.forms["regForm"]["confirm_password"].value;
      if (pass !== confirm) {
        alert("Passwords do not match.");
        return false;
      }
      return true;
    }
  </script>
</head>

<body>
  <div class="container">
    <h2 style="text-align: center;">Register</h2>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <form name="regForm" method="POST" onsubmit="return validateForm()">
      <input name="username" required placeholder="Username">

      <div class="password-wrapper" style="position: relative; width: 100%;">
        <input type="password" id="password" name="password" required placeholder="Password" style="width: calc(100% - 50px); padding-right: 50px;" />
        <div class="eye-icon closed" id="eye" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); z-index: 10; width: 32px; height: 32px; background: #fff; border: 2px solid #4b0082; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.2s ease-in-out, scaleY 0.3s; transform-origin: center center; overflow: hidden;">
          <div class="pupil" id="pupil" style="width: 10px; height: 10px; background: #4b0082; border-radius: 50%; transition: all 0.1s ease;"></div>
        </div>
      </div>

      <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm Password">

      <button type="submit">Register</button>
    </form>
    <p style="text-align:center; margin-top: 10px;">
      Already have an account?
      <a href="login.php" style="color: #ffddff;">Login here</a>
    </p>
  </div>

  <script>
    const eye = document.getElementById("eye");
    const pupil = document.getElementById("pupil");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");
    let isVisible = false;

    function openEye() {
      eye.classList.add("open");
      eye.classList.remove("closed");
      eye.style.transform = "translateY(-50%) scaleY(1)";
    }

    function closeEye() {
      eye.classList.add("closed");
      eye.classList.remove("open");
      eye.style.transform = "translateY(-50%) scaleY(0.2)";
    }

    function updatePupil(event) {
      const rect = eye.getBoundingClientRect();
      const centerX = rect.left + rect.width / 2;
      const centerY = rect.top + rect.height / 2;
      const dx = event.clientX - centerX;
      const dy = event.clientY - centerY;
      const distance = Math.sqrt(dx * dx + dy * dy);

      // Check if cursor is near the eye (within 100px)
      if (distance < 100) {
        // Open eye when cursor is nearby
        openEye();

        // Track pupil movement when cursor is near
        const angle = Math.atan2(dy, dx);
        const maxDistance = 5; // Maximum distance pupil can move from center
        const pupilDistance = Math.min(maxDistance, distance / 12);

        const offsetX = Math.cos(angle) * pupilDistance;
        const offsetY = Math.sin(angle) * pupilDistance;

        pupil.style.transform = `translate(${offsetX}px, ${offsetY}px)`;
      } else {
        // Reset pupil to center when cursor is far
        pupil.style.transform = `translate(0px, 0px)`;

        // Close eye when cursor is far away (unless password is visible)
        if (!isVisible) {
          closeEye();
        }
      }
    }

    document.addEventListener("mousemove", updatePupil);

    eye.addEventListener("click", () => {
      isVisible = !isVisible;
      // Toggle both password fields
      password.type = isVisible ? "text" : "password";
      confirmPassword.type = isVisible ? "text" : "password";

      // When password is visible, keep eye open
      if (isVisible) {
        openEye();
      }
      // When password is hidden, eye behavior will be handled by proximity
    });

    // Initialize eye state - start closed
    closeEye();
  </script>

</body>

</html>