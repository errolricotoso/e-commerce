<?php
// Start a session only if none exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'dbcon/connect.php';

// Check user login status
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : null;

// Default cart count
$cart_count = 0;

// Check if user is logged in
if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT product_id) AS total_products FROM cart WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cart_count = $result['total_products'] ?? 0; // Default to 0 if no items
}
?>
<style>
  /* Navbar styling */
  .navbar {
    background-color: #4CAF50 !important; /* Updated background color (green) */
    padding: 0.8rem 1rem;
  }

  .navbar-brand {
    font-family: 'Georgia', serif; /* Updated font family */
    font-size: 1.5rem; /* Increased font size for the brand */
    color: white !important; /* Brand text color */
  }

  .nav-link {
    color: white !important;
    font-family: 'Arial', sans-serif; /* Updated font for links */
    font-size: 1rem;
    transition: color 0.3s ease-in-out;
  }

  .nav-link:hover, .nav-link.active {
    color: #FFC107 !important; /* Highlight color on hover/active */
    background-color: #087016;
  }

  .badge {
    font-size: 0.8rem;
    padding: 0.3em 0.6em;
    font-family: 'Verdana', sans-serif; /* Badge font style */
  }

  .nav-item .dropdown-menu {
    background-color: #f1f1f1; /* Light gray for dropdown menu */
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    font-family: 'Courier New', monospace; /* Dropdown font style */
  }

  .dropdown-item {
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
  }

  .dropdown-item:hover {
    background-color: #4CAF50; /* Dropdown item hover color */
    color: white;
  }

  .btn-secondary {
    background-color: #FF5722 !important; /* Custom button color (orange) */
    border: none;
    font-family: 'Tahoma', sans-serif; /* Button font style */
  }

  .btn-secondary:hover {
    background-color: #E64A19 !important; /* Darker shade on hover */
  }

  .navbar-toggler {
    border-color: white;
  }
</style>



<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand" href="index.php">
            OTOP
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/products.php') ? 'active' : ''; ?>" href="products.php">Products</a>
                </li>
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">My Cart
                            <span id="cart-count" class="badge bg-warning"><?php echo $cart_count; ?></span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <!-- Profile Button with Custom Color -->
                        <button class="btn btn-secondary dropdown-toggle" id="navbarDropdown" data-bs-toggle="dropdown">
                            Profile
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="edit_profile.php">Profile Information</a></li>
                            <li><a class="dropdown-item" href="history.php">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="dbcon/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#cartModal">Cart
                            <span id="cart-count" class="badge bg-danger"><?php echo $cart_count; ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
