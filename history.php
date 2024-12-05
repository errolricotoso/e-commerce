<?php
// Start the session to track user login
session_start();

// Include the database connection file
include 'dbcon/connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Set session variable to trigger an alert
    $_SESSION['show_login_alert'] = true;
    // Redirect to the current page to trigger the alert
    header("Location: index.php" );
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch user's order history from the database
$sql = "SELECT o.order_id, o.order_date, o.shipping_address, o.payment_method, o.total_amount, o.proof_payment, 
               o.order_status, oi.product_id, oi.quantity, oi.price, p.name as product_name, p.image as product_image
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = :user_id
        ORDER BY o.order_date DESC";
$stmt = $pdo->prepare($sql);    
$stmt->execute([':user_id' => $user_id]);
$orderHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>

<body>
    <?php include 'navbar.php' ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Your Order History</h2>

        <?php if ($orderHistory): ?>
            <?php
            $currentOrderId = null;
            foreach ($orderHistory as $item):
                if ($item['order_id'] !== $currentOrderId):
                    if ($currentOrderId !== null) {
                        echo '</ul></div>'; // Close previous order card
                    }
                    $currentOrderId = $item['order_id'];
            ?>
                    <!-- Order Card -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Order ID: <?= htmlspecialchars($item['order_id']); ?></h5>
                            <span class="badge 
                                <?= strtolower($item['order_status']) === 'pending' ? 'bg-warning' : ''; ?>
                                <?= strtolower($item['order_status']) === 'completed' ? 'bg-success' : ''; ?>
                                <?= strtolower($item['order_status']) === 'shipped' ? 'bg-info' : ''; ?>">
                                <?= ucfirst(htmlspecialchars($item['order_status'])); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p><strong>Order Date:</strong> <?= date('F j, Y', strtotime($item['order_date'])); ?></p>
                            <p><strong>Shipping Address:</strong> <?= htmlspecialchars($item['shipping_address']); ?></p>
                            <p><strong>Payment Method:</strong> <?= htmlspecialchars($item['payment_method']); ?></p>
                            <?php if ($item['payment_method'] === 'GCash' && $item['proof_payment']): ?>
                                <p><strong>Proof of Payment:</strong> <a href="uploads/<?= htmlspecialchars($item['proof_payment']); ?>" target="_blank">View Proof</a></p>
                            <?php endif; ?>
                            <p><strong>Total Amount:</strong> ₱<?= number_format($item['total_amount'], 2); ?></p>
                            <h6 class="mt-3">Items:</h6>
                            <ul class="list-unstyled">
                <?php endif; ?>

                <!-- Order Item -->
                <li class="d-flex align-items-center mb-2">
                    <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="<?= htmlspecialchars($item['product_name']); ?>" class="me-3" style="width: 50px; height: 50px; object-fit: cover;">
                    <div>
                        <p class="mb-1"><strong><?= htmlspecialchars($item['product_name']); ?></strong></p>
                        <small>Quantity: <?= htmlspecialchars($item['quantity']); ?> | Price: ₱<?= number_format($item['price'], 2); ?></small>
                    </div>
                </li>
            <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">No orders found.</div>
        <?php endif; ?>
    </div>

    <!-- Display an alert if the user is not logged in -->
    <?php if (isset($_SESSION['show_login_alert']) && $_SESSION['show_login_alert']): ?>
        <script>
            alert("You must be logged in.");
        </script>
        <?php unset($_SESSION['show_login_alert']); // Clear the session flag ?>
    <?php endif; ?>

</body>

</html>
