<?php
session_start();
include('dbcon/connect.php'); // Include the database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php'); // Redirect to login if not logged in
    exit();
}

$admin_id = $_SESSION['admin_id']; // Use the correct session variable for admin

// Fetch the admin profile image from the database
try {
    $stmt = $pdo->prepare("SELECT profile_image, username FROM admin_users WHERE id = :admin_id");
    $stmt->execute(['admin_id' => $admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Use default image if no profile image is found
    $profileImage = $admin && $admin['profile_image'] ? $admin['profile_image'] : 'default_profile.jpg';
    $username = $admin['username']; // Fetch the username
} catch (PDOException $e) {
    echo "Error fetching profile image: " . htmlspecialchars($e->getMessage());
    $profileImage = 'default_profile.jpg'; // Default image in case of error
    $username = 'Admin'; // Default username if error occurs
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        /* Custom Navbar Styling */
        .navbar {
            background-color: #0d6efd; /* Dark background for navbar */
            padding: 1rem;
        }

        .navbar-brand {
            font-size: 1.5rem;
            
            color: #ffffff;
        }

        .navbar-nav .nav-link {
            color: #ffffff !important;
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        .navbar-nav .nav-link:hover {
            color: #ffd700 !important;
        }

        .navbar-nav .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .navbar-nav .dropdown-item {
            font-size: 1rem;
            padding: 0.75rem 1.25rem;
        }

        .navbar-nav .dropdown-item:hover {
            background-color: #343a40;
            color: #ffffff;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .dropdown-toggle::after {
            display: none !important;
        }

        .container-fluid {
            max-width: 1200px;
            margin: auto;
        }

        .dashboard-heading {
            margin-top: 2rem;
            text-align: center;
            color: #343a40;
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <!-- Logo -->
    <a class="navbar-brand" href="index.php">Admin Panel</a>
    
    <!-- Toggler for mobile view -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        
        <!-- Users Link -->
        <li class="nav-item">
          <a class="nav-link" href="users.php">Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="seller_signup.php">Add Seller</a>
        </li>

        <!-- Profile Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
           
          Admin
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
          
            <li><a class="dropdown-item" href="admin_logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Admin Dashboard Content -->
<div class="container mt-5">
  <h1 class="dashboard-heading">Welcome, <?= htmlspecialchars($username) ?>!</h1>
  <!-- Add your dashboard content here -->
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
