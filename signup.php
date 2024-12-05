<?php
session_start();

// Display error message if set
if (isset($_SESSION['error_message'])) {
  $error_message = $_SESSION['error_message'];
  unset($_SESSION['error_message']); // Clear the error message after displaying
}
require_once 'dbcon/connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullName = $_POST['fullName'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirmPassword']; // For confirming the password

  // Validate if passwords match
  if ($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } else {
    try {
      // Check if username already exists
      $sql = "SELECT * FROM users WHERE username = :username";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':username', $username);
      $stmt->execute();

      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user) {
        // If user already exists
        $error = "Username already taken. Please choose another one.";
      } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user into database (assuming 'full_name' and 'email' columns exist)
        $insert_sql = "INSERT INTO users (full_name, username, email, password) VALUES (:full_name, :username, :email, :password)";
        $insert_stmt = $pdo->prepare($insert_sql);
        $insert_stmt->bindParam(':full_name', $fullName);
        $insert_stmt->bindParam(':username', $username);
        $insert_stmt->bindParam(':email', $email);
        $insert_stmt->bindParam(':password', $hashed_password);
        $insert_stmt->execute();

        // Set session variables
        $_SESSION['user_id'] = $pdo->lastInsertId(); // Get the ID of the last inserted row
        $_SESSION['username'] = $username;
        $_SESSION['login_success'] = true;

        // Redirect to homepage or login page after successful registration
        header("Location: index.php");
        exit();
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      $error = "Database error, please try again later.";
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa; /* Light gray background */
      font-family: 'Arial', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }

    .signup-card {
      width: 100%;
      max-width: 360px;
      background: #ffffff;
      border: 1px solid #e0e0e0; /* Light border for simplicity */
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h2 {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 15px;
      text-align: center;
    }

    .form-label {
      font-weight: 500;
      margin-bottom: 5px;
    }

    .form-control {
      border-radius: 4px;
    }

    .btn {
      background-color: #007bff; /* Bootstrap primary color */
      color: #ffffff;
      border: none;
      width: 100%;
      padding: 10px;
      font-size: 1rem;
      border-radius: 4px;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    .toggle-password {
      position: absolute;
      top: 70%;
      right: 15px;  
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }

    .text-muted {
      font-size: 0.875rem;
      text-align: center;
      margin-top: 15px;
    }

    .text-muted a {
      color: #007bff;
      text-decoration: none;
    }

    .text-muted a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="signup-card">
    <h2>Sign Up</h2>

    <!-- Display error message if exists -->
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="signup.php" method="POST">
      <div class="mb-3">
        <label for="fullName" class="form-label">Full Name</label>
        <input type="text" class="form-control" id="fullName" name="fullName" required>
      </div>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="mb-3 position-relative">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
        <span class="toggle-password" id="togglePassword">&#128065;</span>
      </div>
      <div class="mb-3 position-relative">
        <label for="confirmPassword" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
        <span class="toggle-password" id="toggleConfirmPassword">&#128065;</span>
      </div>
      <button type="submit" class="btn">Sign Up</button>
    </form>
    <div class="text-muted">
      <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>

  <script>
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

    togglePassword.addEventListener('click', function () {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
    });

    toggleConfirmPassword.addEventListener('click', function () {
      const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPasswordInput.setAttribute('type', type);
    });
  </script>
</body>

</html>
