<?php
include('dbcon/connect.php'); // Include database connection

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $profile_image = "default_profile.jpg"; // Default profile image

    // Form validation
    if (empty($username) || empty($password) || empty($confirm_password) || empty($full_name)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Insert admin user into the database
            $stmt = $pdo->prepare("INSERT INTO owner_users (username, password, full_name, profile_image) 
                                   VALUES (:username, :password, :full_name, :profile_image)");
            $stmt->execute([
                'username' => $username,
                'password' => $hashed_password,
                'full_name' => $full_name,
                'profile_image' => $profile_image,
            ]);

            $success = "Admin account created successfully! You can now log in.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error code
                $error = "Username already exists.";
            } else {
                $error = "Error: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .signup-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .btn-primary {
            width: 100%;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>

<div class="signup-card">
    <h3 class="text-center">Admin Sign Up</h3>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Sign Up</button>
    </form>
    <p class="mt-3 text-center">
        Already have an account? <a href="owner_login.php">Log In</a>
    </p>
</div>

</body>
</html>
