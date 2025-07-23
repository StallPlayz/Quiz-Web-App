<?php
include 'auth.php';
require 'config.php';

// Get or create user profile
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE username = :username");
$stmt->execute(['username' => $_SESSION['user']]);
$profile = $stmt->fetch();

// Create default profile if it doesn't exist
if (!$profile) {
    $stmt = $pdo->prepare("INSERT INTO user_profiles (username, nickname, email, phone, gender, address, bio, profile_image) VALUES (:username, :nickname, '', '', '', '', '', 'default-avatar.png')");
    $stmt->execute(['username' => $_SESSION['user'], 'nickname' => $_SESSION['user']]);

    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE username = :username");
    $stmt->execute(['username' => $_SESSION['user']]);
    $profile = $stmt->fetch();
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
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

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            /* Add this line */
        }

        /* Dashboard Header Styles */
        .dashboard-header {
            background: linear-gradient(135deg, rgba(157, 78, 221, 0.1), rgba(123, 44, 191, 0.05));
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .welcome-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .greeting-text h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff, #c77dff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 10px rgba(157, 78, 221, 0.3);
        }

        .current-time {
            margin: 5px 0 0 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            font-weight: 500;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.05);
            padding: 15px 20px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .username {
            font-weight: 600;
            color: #fff;
            font-size: 1.1rem;
        }

        .status-badge {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
            width: fit-content;
        }

        .logout-button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }

        /* Stats Container */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(157, 78, 221, 0.2);
            border-color: rgba(157, 78, 221, 0.3);
        }

        .stat-icon {
            font-size: 2.5rem;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #9d4edd, #c77dff);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(157, 78, 221, 0.3);
        }

        .stat-content h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
        }

        .stat-content p {
            margin: 5px 0 0 0;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        /* Enhanced Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 25px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .action-btn.secondary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .action-btn.primary:hover {
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.4);
        }

        .action-btn.secondary:hover {
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.4);
        }

        .btn-icon {
            font-size: 2.5rem;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .btn-content {
            flex: 1;
            text-align: left;
        }

        .btn-title {
            display: block;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .btn-subtitle {
            display: block;
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .btn-arrow {
            font-size: 1.5rem;
            font-weight: bold;
            transition: transform 0.3s ease;
        }

        .action-btn:hover .btn-arrow {
            transform: translateX(5px);
        }


        .profile-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            object-fit: cover;
        }

        .profile-avatar:hover {
            transform: scale(1.1);
            border-color: #9d4edd;
            box-shadow: 0 0 15px rgba(157, 78, 221, 0.5);
        }

        .profile-button {
            background: linear-gradient(135deg, #9d4edd, #c77dff);
            color: white;
            border: none;
            width: 200px;
            /* Set width for the box */
            height: 40px;
            /* Set height for the box */
            border-radius: 8px;
            /* Rounded corners */
            cursor: pointer;
            display: flex;
            /* Center the icon */
            align-items: center;
            /* Center vertically */
            justify-content: center;
            /* Center horizontally */
            font-size: 18px;
            /* Adjust icon size */
            transition: all 0.3s ease;
            margin-left: auto;
            /* Ensure it positions to the right */
            margin-right: 20px;
            margin-top: 20px;
        }

        .profile-button:hover {
            transform: scale(1.1);
            /* Slightly enlarge on hover */
            box-shadow: 0 5px 15px rgba(157, 78, 221, 0.4);
        }

        .profile-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 3000;
            animation: fadeIn 0.3s ease;
        }

        .profile-modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #2d1b4e, #4b0082);
            border-radius: 20px;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .profile-form {
            display: grid;
            gap: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: #fff;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 12px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #9d4edd;
            box-shadow: 0 0 0 3px rgba(157, 78, 221, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .avatar-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .avatar-preview:hover {
            transform: scale(1.05);
            border-color: #9d4edd;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .modal-buttons button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .save-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .cancel-btn {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }

        .cancel-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(107, 114, 128, 0.4);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        .profile-modal.show .profile-modal-content {
            animation: modalSlideIn 0.4s ease;
        }

        #contextMenu {
            position: absolute;
            display: none;
        }

        .expand-toggle {
            padding: 8px 12px;
            background: linear-gradient(135deg, #7b2cbf, #9d4edd);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .expand-toggle:hover {
            background: linear-gradient(135deg, #9d4edd, #c77dff);
        }

        .available-quizzes-wrapper {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 14px;
            padding-bottom: 8px;
            position: relative;
        }

        .available-quizzes-wrapper.expanded {
            flex-wrap: wrap;
            overflow-x: hidden;
            max-height: none;
        }

        .available-quizzes-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        .available-quizzes-wrapper::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        .available-quizzes-wrapper::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .quiz-entry {
            padding: 18px;
            /* Increased padding */
            border-radius: 12px;
            background: linear-gradient(135deg, #3c096c, #5a189a);
            margin-bottom: 18px;
            /* Increased margin */
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.15);
            /* Increased border */
            position: relative;
            overflow: hidden;
            min-width: 190px;
            /* Make each quiz card smaller */
            max-width: 190px;
            flex: 0 0 auto;
        }

        .quiz-entry::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.1),
                    transparent);
            transition: left 0.4s ease;
        }

        .quiz-entry:hover::before {
            left: 100%;
        }

        .quiz-entry:hover {
            background: linear-gradient(135deg, #5a189a, #7209b7);
            cursor: pointer;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(60, 9, 108, 0.4);
        }

        .status-popup {
            display: none;
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #7b2cbf, #9d4edd);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            z-index: 4000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: opacity 0.3s ease;
        }

        .status-popup.show {
            display: block;
            opacity: 1;
        }


        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .profile-modal-content {
                width: 95%;
                padding: 20px;
            }

            .header-container {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .profile-section {
                justify-content: center;
            }

            .dashboard-header {
                padding: 20px;
            }

            .header-top {
                flex-direction: column;
                gap: 20px;
            }

            .welcome-section {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .greeting-text h1 {
                font-size: 2rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .profile-section {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <button class="profile-button" data-aos="fade-up" data-aos-duration="800" onclick="openProfileModal()">👤Profile</button>
    <button class="profile-button" data-aos="fade-up" data-aos-duration="800" onclick="openFriendPopup()" class="profile-action-btn">
        👥 Friendships
    </button>


    <!-- Floating Shapes Background -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container" data-aos="fade-up" data-aos-duration="800">
        <!-- Enhanced Dashboard Header -->
        <div class="dashboard-header" data-aos="fade-down" data-aos-duration="1000">
            <div class="header-top">
                <div class="welcome-section">
                    <div class="greeting-text">
                        <h1>Welcome back, <?php echo htmlspecialchars($profile['nickname'] ?: $_SESSION['user']); ?>! 🎯</h1>
                        <p class="current-time" id="currentTime"></p>
                    </div>
                    <div class="profile-section">
                        <img src="<?php echo htmlspecialchars($profile['profile_image'] ?: 'default-avatar.png'); ?>"
                            alt="Profile" class="profile-avatar" onclick="openProfileModal()">
                        <div class="user-info">
                            <span class="username"><?php echo htmlspecialchars($profile['nickname'] ?: $_SESSION['user']); ?></span>
                            <span class="status-badge">Online</span>
                        </div>
                        <a href="logout.php">
                            <button class="logout-button" data-aos="pulse" data-aos-delay="400">
                                <span>Logout</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dashboard Stats -->
            <div class="stats-container" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <h3>-</h3>
                        <p>Work In Progress</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-content">
                        <?php
                        $user = $_SESSION['user'] ?? null;
                        $files = glob('quizzes/*.json');
                        $userQuizCount = 0;

                        foreach ($files as $file) {
                            $data = json_decode(file_get_contents($file), true);
                            if ($data && isset($data['created_by']) && $data['created_by'] === $user) {
                                $userQuizCount++;
                            }
                        }
                        ?>
                        <h3><?php echo $userQuizCount; ?></h3>
                        <p>Total Quizzes</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚡</div>
                    <div class="stat-content">
                        <h3>Active</h3>
                        <p>Status</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Action Buttons -->
        <div class="action-buttons" data-aos="zoom-in" data-aos-delay="500">
            <button onclick="window.location.href='create_quiz.php'" class="action-btn primary" data-aos="fade-left" data-aos-delay="600">
                <div class="btn-icon">✨</div>
                <div class="btn-content">
                    <span class="btn-title">Create Quiz</span>
                    <span class="btn-subtitle">Make a new quiz</span>
                </div>
                <div class="btn-arrow">→</div>
            </button>


            <button onclick="openJoinPopup()" class="action-btn secondary" data-aos="fade-left" data-aos-delay="700">
                <div class="btn-icon">🎯</div>
                <div class="btn-content">
                    <span class="btn-title">Join Quiz</span>
                    <span class="btn-subtitle">Enter with PIN code</span>
                </div>
                <div class="btn-arrow">→</div>
            </button>
        </div>

        <h3 data-aos="fade-right" data-aos-delay="500">Available Quizzes 📚</h3>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="success-message" data-aos="fade-in">
                <p>✅ Quiz deleted successfully.</p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['updated'])): ?>
            <div class="success-message" data-aos="fade-in">
                <p>✅ Quiz updated successfully.</p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['profile_updated'])): ?>
            <div class="success-message" data-aos="fade-in">
                <p>✅ Profile updated successfully!</p>
            </div>
        <?php endif; ?>

        <button class="expand-toggle" onclick="toggleQuizView()">Expand View ⬇️</button>
        <div class="available-quizzes-wrapper" id="quizList" data-aos="fade-up" data-aos-delay="600">
            <?php
            $quizFiles = glob('quizzes/*.json');
            $userQuizzes = array_filter($quizFiles, function ($file) {
                $quiz = json_decode(file_get_contents($file), true);
                return $quiz['created_by'] === $_SESSION['user'];
            });

            if (empty($userQuizzes)) {
                echo "<p>No quizzes found. Create your first quiz! 🚀</p>";
            } else {
                foreach ($userQuizzes as $file) {
                    $quiz = json_decode(file_get_contents($file), true);
                    $filename = basename($file);

                    echo "<div class='quiz-entry'>
                <a href='quiz.php?file=" . urlencode($filename) . "'>
                    <strong>" . htmlspecialchars($quiz['name']) . "</strong><br>
                    <small>" . htmlspecialchars($quiz['description']) . "</small><br>
                    <small>By: " . htmlspecialchars($quiz['created_by']) . "</small><br>
                    <small>PIN: " . htmlspecialchars($quiz['pin']) . "</small>
                </a>
            </div>";
                }
            }
            ?>
        </div>

        <!-- Enhanced Context Menu -->
        <div id="contextMenu" style="display:none; position:absolute; z-index:1000; background: linear-gradient(135deg, #4b0082, #3c096c); border-radius:12px; box-shadow:0 8px 25px rgba(0,0,0,0.4); padding:15px; border: 1px solid rgba(255,255,255,0.1);">
            <button id="editBtn" style="margin-bottom:8px; width: 100%; background: linear-gradient(135deg, #7b2cbf, #9d4edd);">✏️ Edit Quiz</button><br>
            <button id="deleteBtn" style="background: linear-gradient(135deg, #ff4d4d, #dc2626); width: 100%;">🗑️ Delete Quiz</button>
        </div>
    </div>

    <!-- Join Pop Up -->
    <div id="joinPopup" class="popup">
        <div class="popup-content">
            <h3>Join Quiz</h3>
            <input type="text" id="pinInput" placeholder="Enter PIN">
            <div style="margin-top: 10px;">
                <button onclick="submitPin()">Join</button>
                <button onclick="closeJoinPopup()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="profile-modal">
        <div class="profile-modal-content">
            <button class="close-modal" onclick="closeProfileModal()">&times;</button>
            <h2 style="color: #fff; margin-bottom: 25px; text-align: center;">👤 Edit Profile</h2>

            <form id="profileForm" class="profile-form" method="POST" action="update_profile.php" enctype="multipart/form-data">
                <div class="avatar-upload">
                    <img id="avatarPreview" src="<?php echo htmlspecialchars($profile['profile_image'] ?: 'default-avatar.png'); ?>"
                        alt="Profile Avatar" class="avatar-preview" onclick="document.getElementById('avatarInput').click()">
                    <div class="file-input-wrapper">
                        <input type="file" id="avatarInput" name="profile_image" accept="image/*" onchange="previewAvatar(this)">
                        <label for="avatarInput" class="file-input-label">📷 Change Photo</label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nickname">Nickname *</label>
                        <input type="text" id="nickname" name="nickname" value="<?php echo htmlspecialchars($profile['nickname']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>" placeholder="your@email.com">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($profile['phone']); ?>" placeholder="+1 (555) 123-4567">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo $profile['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo $profile['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Home Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($profile['address']); ?>" placeholder="123 Main St, City, State, ZIP">
                </div>

                <div class="form-group">
                    <label for="bio">Bio / About Me</label>
                    <textarea id="bio" name="bio" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($profile['bio']); ?></textarea>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="closeProfileModal()">Cancel</button>
                    <button type="submit" class="save-btn">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div id="friendPopup" class="popup">
        <div class="popup-content">
            <h3>Your Friends</h3>
            <div style="display: flex; gap: 8px; margin-bottom: 10px;">
                <input type="text" id="friendSearch" placeholder="Search friend..." oninput="searchFriends()" style="flex: 1; padding: 6px;">
                <button onclick="openAddFriendPopup()">➕ Add Friend</button>
                <span>work in progress</span>
            </div>
            <ul id="friendList" style="max-height: 200px; overflow-y: auto;"></ul>
            <button onclick="closeFriendPopup()" style="margin-top: 10px;">Close</button>
        </div>
    </div>

    <!-- Add friend modal -->
    <div id="addFriendPopup" class="popup">
        <div class="popup-content">
            <h3>Add Friend</h3>
            <input type="text" id="addFriendInput" placeholder="Enter username">
            <span>work in progress</span>
            <button onclick="addFriend()">Add</button>
            <button onclick="closeAddFriendPopup()">Cancel</button>
        </div>
    </div>

    <div id="friendStatusPopup" class="status-popup"></div>


    <!-- Enhanced Delete Modal -->
    <div id="deleteModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:2000; backdrop-filter: blur(5px);">
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background: linear-gradient(135deg, #2d1b4e, #4b0082); border-radius:15px; padding:30px; box-shadow:0 15px 40px rgba(0,0,0,0.5); min-width:350px; text-align:center; border: 1px solid rgba(255,255,255,0.1);" data-aos="zoom-in">
            <h3 style="color:#fff; margin-bottom:15px; font-size:20px;">⚠️ Confirm Delete</h3>
            <p style="color:#ccc; margin-bottom:25px; font-size:14px;">Are you sure you want to delete this quiz? This action cannot be undone.</p>
            <div style="display:flex; gap:15px; justify-content:center;">
                <button id="confirmDelete" style="background: linear-gradient(135deg, #ff4d4d, #dc2626); color:white; padding:10px 20px; border:none; border-radius:8px; cursor:pointer; font-size:14px; font-weight: bold;">Delete</button>
                <button id="cancelDelete" style="background: linear-gradient(135deg, #666, #555); color:white; padding:10px 20px; border:none; border-radius:8px; cursor:pointer; font-size:14px; font-weight: bold;">Cancel</button>
            </div>
        </div>
    </div>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true
        });

        let contextTarget = null;
        let quizToDelete = null;

        // Profile Modal Functions
        function openProfileModal() {
            const modal = document.getElementById('profileModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeProfileModal() {
            const modal = document.getElementById('profileModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Close modal when clicking outside
        document.getElementById('profileModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProfileModal();
            }
        });

        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const nickname = document.getElementById('nickname').value.trim();
            if (nickname.length < 2) {
                e.preventDefault();
                alert('Nickname must be at least 2 characters long!');
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('.save-btn');
            submitBtn.innerHTML = '⏳ Saving...';
            submitBtn.disabled = true;
        });

        //friendship pop up
        function openFriendPopup() {
            document.getElementById('friendPopup').classList.add('show');
            loadFriends();
        }

        function closeFriendPopup() {
            document.getElementById('friendPopup').classList.remove('show');
        }

        function openAddFriendPopup() {
            document.getElementById('addFriendPopup').classList.add('show');
        }

        function closeAddFriendPopup() {
            document.getElementById('addFriendPopup').classList.remove('show');
        }

        function searchFriends() {
            const term = document.getElementById('friendSearch').value.toLowerCase();
            document.querySelectorAll('#friendList li').forEach(li => {
                li.style.display = li.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        }

        function loadFriends() {
            fetch('get_friends.php')
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('friendList');
                    list.innerHTML = '';
                    data.forEach(friend => {
                        const li = document.createElement('li');
                        li.textContent = friend.username;
                        list.appendChild(li);
                    });
                });
        }

        function addFriend() {
            const username = document.getElementById('addFriendInput').value.trim();
            if (!username) {
                showFriendStatus('⚠️ Please enter a username!');
                return;
            }

            fetch('add_friend.php?username=' + encodeURIComponent(username))
                .then(res => res.text())
                .then(msg => {
                    showFriendStatus(msg);
                    closeAddFriendPopup();
                    loadFriends();
                });
        }

        function showFriendStatus(message) {
            const popup = document.getElementById('friendStatusPopup');
            popup.textContent = message;
            popup.classList.add('show');

            setTimeout(() => {
                popup.classList.remove('show');
            }, 3000);
        }

        //custom context menu
        let currentQuizFile = null;

        document.addEventListener('contextmenu', function(e) {
            const quizEntry = e.target.closest('.quiz-entry');
            const contextMenu = document.getElementById('contextMenu');

            if (quizEntry) {
                e.preventDefault();
                currentQuizFile = quizEntry.querySelector('a').getAttribute('href');

                const quizRect = quizEntry.getBoundingClientRect();
                const wrapper = quizEntry.closest('.available-quizzes-wrapper');
                const wrapperRect = wrapper.getBoundingClientRect();

                const top = quizRect.bottom - wrapperRect.top + 700; // your custom offset
                const left = quizRect.left - wrapperRect.left + (quizRect.width - contextMenu.offsetWidth) / 2;

                contextMenu.style.top = `${top}px`;
                contextMenu.style.left = `${left}px`;
                contextMenu.style.display = 'block';
            } else {
                hideContextMenu();
            }
        });

        document.addEventListener('click', hideContextMenu);
        window.addEventListener('resize', hideContextMenu);

        function hideContextMenu() {
            const contextMenu = document.getElementById('contextMenu');
            contextMenu.style.display = 'none';
        }

        // === INSERTED EDIT/DELETE LOGIC ===
        document.getElementById('editBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            hideContextMenu();
            if (currentQuizFile) {
                const urlParams = new URLSearchParams(currentQuizFile.split('?')[1]);
                const file = urlParams.get('file');
                if (file) {
                    window.location.href = `edit_quiz.php?file=${encodeURIComponent(file)}`;
                }
            }
        });

        document.getElementById('deleteBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            hideContextMenu();
            document.getElementById('deleteModal').style.display = 'block';
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (currentQuizFile) {
                const urlParams = new URLSearchParams(currentQuizFile.split('?')[1]);
                const file = urlParams.get('file');
                if (file) {
                    window.location.href = `delete_quiz.php?file=${encodeURIComponent(file)}`;
                }
            }
        });

        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteModal').style.display = 'none';
        });


        // Add hover effects for quiz entries
        document.querySelectorAll('.quiz-entry').forEach(entry => {
            entry.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });

            entry.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        //join session
        function openJoinPopup() {
            document.getElementById("joinPopup").classList.add("show");
        }

        function closeJoinPopup() {
            document.getElementById("joinPopup").classList.remove("show");
        }

        function submitPin() {
            const pin = document.getElementById('pinInput').value.trim();
            if (pin) {
                window.location.href = `join_quiz.php?pin=${encodeURIComponent(pin)}`;
            } else {
                alert('Please enter a PIN!');
            }
        }


        // Real-time clock
        function updateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('currentTime').textContent = now.toLocaleDateString('en-US', options);
        }

        // Update time immediately and then every minute
        updateTime();
        setInterval(updateTime, 60000);

        // Add floating animation to stat cards
        document.querySelectorAll('.stat-card').forEach((card, index) => {
            card.style.animationDelay = `${index * 0.2}s`;
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        //expand or collapse quizzes menu
        function toggleQuizView() {
            const quizList = document.getElementById('quizList');
            const button = document.querySelector('.expand-toggle');

            if (quizList.classList.contains('expanded')) {
                quizList.classList.remove('expanded');
                button.textContent = 'Expand View ⬇️';
            } else {
                quizList.classList.add('expanded');
                button.textContent = 'Collapse View ⬆️';
            }
        }
    </script>
</body>

</html>