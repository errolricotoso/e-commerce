<?php
session_start();
include('dbcon/connect.php'); // Include the database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: seller_login.php'); // Redirect to login if not logged in
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
    <title>Seller Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
      /* Navbar Customization */
      .navbar {
        background-color: #4CAF50 !important; /* Dark background for the navbar */
      }
      .navbar-brand, .navbar-nav .nav-link {
        color: #ffffff !important; /* White text color */
      }
      .navbar-brand {
        font-weight: 600;
        font-size: 1.7rem; /* Slightly larger font for branding */
      }
      .navbar-nav .nav-link {
        font-size: 1.1rem;
        font-family: 'Roboto', sans-serif;
      }
      .nav-item.dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0;
      }
      .dropdown-item:hover {
        background-color: #007bff;
        color: #fff;
      }
      .navbar-toggler-icon {
        background-color: #fff;
      }

      /* Profile image style */
      .profile-image {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
      }

      /* Custom background and typography */
      body {
        background-color: #f5f5f5; /* Light background color */
        font-family: 'Poppins', sans-serif; /* Change to Poppins font for body */
      }

      /* Sidebar and navbar hover effect */
      .nav-link:hover {
        background-color: #007bff;
        color: white !important;
        border-radius: 5px;
      }

      /* General container for better padding */
      .container {
        padding: 20px;
      }

      .card {
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      /* Responsive Design for smaller screens */
      @media (max-width: 768px) {
        .navbar-brand {
          font-size: 1.5rem; /* Adjust brand size for mobile */
        }
      }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <!-- Logo -->
    <a class="navbar-brand" href="index.php">My Panel</a>
    
    <!-- Toggler for mobile view -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- Orders Link -->
        <li class="nav-item">
          <a class="nav-link" href="orders.php">Orders</a>
        </li>
        
        <!-- Products Link -->
        <li class="nav-item">
          <a class="nav-link" href="product_seller.php">Products</a>
        </li>

        <!-- Profile Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Seller
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="seller_logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container">
  <div class="card">
    <div class="card-body">
      <h4>Welcome, <?= htmlspecialchars($username); ?>!</h4>
      <p>Your dashboard allows you to manage your orders, products, and account.</p>
      
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
