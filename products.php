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
    body {
      background-color: #f4f7fc;
      color: #333;
      font-family: 'Arial', sans-serif;
    }

    .hero {
      position: relative;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-size: cover;
      background-position: center;
      overflow: hidden;
      background-color: #1a1a1a;
    }

    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6); /* Darker overlay for contrast */
      z-index: 2;
    }

    .text-overlay {
      position: relative;
      z-index: 3;
      color: white;
      text-align: center;
      padding: 0 20px;
    }

    .text-overlay h1 {
      font-size: 3.5rem;
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
      background-color: #fff;
      text-align: center;
    }

    .card-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #333;
    }

    .card-text {
      font-size: 1.1rem;
      color: #555;
    }

    .btn-outline-dark {
      border: 2px solid #026670;
      color: #026670;
      font-weight: 600;
      transition: background-color 0.3s, color 0.3s;
    }

    .btn-outline-dark:hover {
      background-color: #026670;
      color: white;
    }

    .form-select, .form-control {
      border-radius: 0.5rem;
    }

    .form-control-sm {
      height: 30px;
      font-size: 0.875rem;
    }

    .w-25 {
      width: 80px;
    }

    .card {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease-in-out;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .row-cols-md-3 .col {
      margin-bottom: 2rem;
    }

    .container {
      max-width: 1200px;
    }

    footer {
      background-color: #026670;
      color: white;
      text-align: center;
      padding: 2rem 0;
      font-size: 0.9rem;
    }

    footer p {
      margin: 0;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Product Section -->
  <section id="products" class="container py-5">
    <h2 class="text-center mb-5">Products</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php
      if ($products) {
        foreach ($products as $product) {
          $colors = explode(',', $product['color']);
          $sizes = explode(',', $product['size']);
          echo '
            <div class="col">
              <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                <img src="' . $product['image'] . '" class="card-img-top product-img" alt="' . $product['name'] . '">
                <div class="card-body">
                  <h5 class="card-title">' . $product['name'] . '</h5>
                  <p class="card-text">₱' . number_format($product['price'], 2) . '</p>
                  <form method="POST">
                    <div class="mb-3">
                      <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="1" max="10" required>
                    </div>
                    <input type="hidden" name="product_id" value="' . $product['id'] . '">
                    <button type="submit" name="add_to_cart" class="btn btn-outline-dark w-100">Add to Cart</button>
                  </form>
                </div>
              </div>
            </div>';
        }
      } else {
        echo '<p class="text-center">No products available at the moment.</p>';
      }
      ?>
    </div>
  </section>

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
