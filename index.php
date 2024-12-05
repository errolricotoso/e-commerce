<?php
// Start the session
session_start();

// Include the database connection file
include 'dbcon/connect.php';

$is_logged_in = isset($_SESSION['user_id']);

// Fetch products from the database using PDO
$sql = "SELECT * FROM products";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Optional: Set a default user ID for guests or handle cart without login
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if the user is trying to add to cart
if (isset($_POST['add_to_cart'])) {
  if (!$user_id) {
    $_SESSION['show_modal'] = true; // Set session flag to show modal
    header("Location: " . $_SERVER['PHP_SELF']); // Reload the page
    exit;
  }

  $productId = $_POST['product_id'];
  $quantity = $_POST['quantity'];
  

  // Fetch product details based on the product ID
  $product = array_filter($products, fn($prod) => $prod['id'] == $productId);
  $product = reset($product); // Get the first match

  // If the product exists, add it to the cart in the database
  if ($product) {
    // Check if the item already exists in the user's cart
    $checkCartSql = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
    $checkCartStmt = $pdo->prepare($checkCartSql);
    $checkCartStmt->execute([
      ':user_id' => $user_id,
      ':product_id' => $productId,
    ]);
    $cartItem = $checkCartStmt->fetch(PDO::FETCH_ASSOC);

    if ($cartItem) {
      // If the item already exists, update the quantity
      $newQuantity = $cartItem['quantity'] + $quantity;
      $updateSql = "UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id";
      $updateStmt = $pdo->prepare($updateSql);
      $updateStmt->execute([':quantity' => $newQuantity, ':cart_id' => $cartItem['cart_id']]);
    } else {
      // If the item doesn't exist in the cart, insert it
      $insertSql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
      $insertStmt = $pdo->prepare($insertSql);
      $insertStmt->execute([
        ':user_id' => $user_id,
        ':product_id' => $productId,
        ':quantity' => $quantity,
      ]);
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop - One Town, One Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

  <style>
    body {      background-color: #f8f9fa;
      color: #343a40;
    }

    .hero {
  position: relative;
  height: 100vh; /* Full viewport height */
  display: flex;
  justify-content: center;
  align-items: center;
  background-size: cover;
  background-position: center;
  overflow: hidden;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4); /* Semi-transparent black overlay */
  z-index: 2;
}

.text-overlay {
  position: relative;
  z-index: 3;
  color: white;
  text-shadow: 0 2px 5px rgba(0, 0, 0, 0.7); /* Stronger shadow for better visibility */
  text-align: center;
  padding: 0 20px; /* Add padding for small screens */
}

.text-overlay h1 {
  font-size: 3rem; /* Adjust size for headings */
  font-weight: 600;
  margin-bottom: 1rem;
}

.text-overlay p {
  font-size: 1.25rem;
}


    

  .product-img {
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease-in-out;
  }

  .product-img:hover {
    transform: scale(1.1);
  }

  .card-body {
    padding: 1.25rem;
  }

  .btn-outline-dark {
    border: 1px solid #026670;
    color: #026670;
    font-weight: 600;
  }

  .btn-outline-dark:hover {
    background-color: #026670;
    color: #fff;
  }

  .form-select, .form-control {
    border-radius: 0.5rem;
  }

  .form-control-sm {
    height: 30px;
    font-size: 0.875rem;
  }

  .w-25 {
    width: 80px; /* Custom width for the quantity input */
  }

  .card:hover .product-img {
    transform: scale(1.05);
  }

  .card-body .form-select {
    font-size: 0.9rem;
  }

  .row-cols-md-3 .col {
    margin-bottom: 2rem;
  
  </style>
</head>

<body>
  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Hero Section -->
<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
  <div class="carousel-inner">
    <!-- Slide 1 -->
    <div class="carousel-item active">
      <div class="hero" style="background-image: url('https://i0.wp.com/easywoodproducts.com/wp-content/uploads/2011/05/1a-edited-copy.jpg?fit=740%2C405&ssl=1');">
        <div class="overlay"></div>
        <div class="text-overlay">
          <h1>Welcome to One Town, One Product</h1>
          <p>Explore the unique crafts, delicacies, and products that define the rich heritage of the town of San Vicente.</p>
        </div>
      </div>
    </div>
    <!-- Slide 2 -->
    <div class="carousel-item">
      <div class="hero" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/1/10/Salt_Farmers_-_Pak_Thale-edit1.jpg');">
        <div class="overlay"></div>
        <div class="text-overlay">
          <h1>Discover Our Finest Creations</h1>
          <p>Experience the craftsmanship and tradition of our unique products.</p>
        </div>
      </div>
    </div>
    <!-- Slide 3 -->
    <div class="carousel-item">
      <div class="hero" style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRonHP5U45Pu8H21BYoVzFAGsmsJDPmVEueZiSglG9_TZ0eGoCK1SX4_7bZb71E3QqRzkM&usqp=CAU');">
        <div class="overlay"></div>
        <div class="text-overlay">
          <h1>Shop Local, Support Local</h1>
          <p>Help sustain local artisans and entrepreneurs by purchasing their creations.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Carousel Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>




  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <!-- Modal for Not Logged In -->
  <?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']): ?>
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Login Required</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            You must be logged in to add items to your cart. Please log in to continue.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <a href="login.php" class="btn btn-primary">Log In</a>
          </div>
        </div>
      </div>
    </div>
    <script>
      var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
      loginModal.show();
    </script>
    <?php unset($_SESSION['show_modal']); ?>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
