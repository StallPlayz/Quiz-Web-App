<?php
include 'auth.php';
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['user'];
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $bio = trim($_POST['bio']);

    // Validate nickname
    if (strlen($nickname) < 2) {
        header("Location: home.php?error=nickname_too_short");
        exit;
    }

    // Handle profile image upload
    $profileImage = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/profiles/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageInfo = getimagesize($_FILES['profile_image']['tmp_name']);
        if ($imageInfo !== false) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($imageInfo['mime'], $allowedTypes)) {
                $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $filename = $username . '_' . time() . '.' . $extension;
                $targetPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                    $profileImage = $targetPath;
                }
            }
        }
    }

    // Check if profile exists
    $stmt = $pdo->prepare("SELECT id, profile_image FROM user_profiles WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $existingProfile = $stmt->fetch();

    if ($existingProfile) {
        // Update existing profile
        $sql = "UPDATE user_profiles SET nickname = :nickname, email = :email, phone = :phone, gender = :gender, address = :address, bio = :bio";
        $params = [
            'username' => $username,
            'nickname' => $nickname,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender,
            'address' => $address,
            'bio' => $bio
        ];

        if ($profileImage) {
            // Delete old profile image if it's not the default
            if ($existingProfile['profile_image'] && $existingProfile['profile_image'] !== 'default-avatar.png' && file_exists($existingProfile['profile_image'])) {
                unlink($existingProfile['profile_image']);
            }
            $sql .= ", profile_image = :profile_image";
            $params['profile_image'] = $profileImage;
        }

        $sql .= " WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } else {
        // Create new profile
        $stmt = $pdo->prepare("INSERT INTO user_profiles (username, nickname, email, phone, gender, address, bio, profile_image) VALUES (:username, :nickname, :email, :phone, :gender, :address, :bio, :profile_image)");
        $stmt->execute([
            'username' => $username,
            'nickname' => $nickname,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender,
            'address' => $address,
            'bio' => $bio,
            'profile_image' => $profileImage ?: 'default-avatar.png'
        ]);
    }

    header("Location: home.php?profile_updated=1");
    exit;
} else {
    header("Location: home.php");
    exit;
}
