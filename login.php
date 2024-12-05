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
  $username = $_POST['username'];
  $password = $_POST['password'];

  try {
    // Check if user exists
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['login_success'] = true;

      header("Location: index.php");
      exit();
    } else {
      $error = "Invalid username or password";
    }
  } catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Database error, please try again later.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Arial', sans-serif;
    }

    .login-container {
      max-width: 450px;
      margin: auto;
      margin-top: 10%;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .login-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .login-header h2 {
      margin: 0;
      font-size: 1.75rem;
      color: #343a40;
    }

    .form-control {
      border-radius: 5px;
      box-shadow: none;
      font-size: 1rem;
      padding: 12px;
    }

    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .btn-primary {
      width: 100%;
      background-color: #007bff;
      border: none;
      border-radius: 5px;
      padding: 12px;
      font-size: 1.1rem;
    }

    .btn-primary:hover {
      background-color: #0056b3;
    }

    .btn-secondary {
      width: 100%;
      margin-top: 10px;
      background-color: #6c757d;
      border: none;
      border-radius: 5px;
      padding: 12px;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
    }

    .text-center a {
      text-decoration: none;
      color: #007bff;
      font-size: 0.9rem;
    }

    .text-center a:hover {
      text-decoration: underline;
    }

    .alert {
      font-size: 0.875rem;
      padding: 10px;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="login-container">
    <div class="login-header">
      <h2>Login</h2>
    </div>

    <?php if (isset($error_message)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
      </div>

      <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <div class="text-center mt-3">
      <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
      <p>Are you a seller? <a href="seller_login.php">Click here to log in</a></p>
      <p>Are you an admin? <a href="admin_login.php">Click here to log in</a></p>
      <a href="index.php" class="btn btn-link">Back to Home</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
