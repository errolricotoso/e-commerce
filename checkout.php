<?php
session_start();
include 'dbcon/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to proceed to checkout.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id']; 

if (isset($_GET['cart_ids']) && !empty($_GET['cart_ids'])) {
    $cartIds = explode(',', $_GET['cart_ids']);
} else {
    echo "<script>alert('No items selected for checkout.'); window.location.href='cart.php';</script>";
    exit;
}

$sql = "SELECT c.cart_id, c.quantity, p.id as product_id, p.name, p.image, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.cart_id IN (" . implode(',', array_map('intval', $cartIds)) . ") AND c.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalAmount = 0;
foreach ($cartItems as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

$orderSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $shippingAddress = htmlspecialchars($_POST['shipping_address']); 

    if (!empty($shippingAddress)) {
        $orderSql = "INSERT INTO orders (user_id, total_amount, shipping_address, payment_method)
                     VALUES (:user_id, :total_amount, :shipping_address, 'cash_on_delivery')";
        $orderStmt = $pdo->prepare($orderSql);
        $orderStmt->execute([
            ':user_id' => $user_id,
            ':total_amount' => $totalAmount,
            ':shipping_address' => $shippingAddress
        ]);
        $orderId = $pdo->lastInsertId();

        foreach ($cartItems as $item) {
            $orderItemSql = "INSERT INTO order_items (order_id, product_id, quantity, price, total)
                             VALUES (:order_id, :product_id, :quantity, :price, :total)";
            $orderItemStmt = $pdo->prepare($orderItemSql);
            $orderItemStmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price'],
                ':total' => $item['price'] * $item['quantity']
            ]);
        }

        $deleteSql = "DELETE FROM cart WHERE cart_id IN (" . implode(',', array_map('intval', $cartIds)) . ") AND user_id = :user_id";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute([':user_id' => $user_id]);

        $orderSuccess = true;
    } else {
        echo "<script>alert('Shipping address cannot be empty.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .checkout-header {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        .btn-danger-custom {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger-custom:hover {
            background-color: #c82333;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .shipping-address {
            background-color: #ffffff;
            border-radius: 8px;
        }

        .shipping-textarea {
            resize: none;
        }

        .product-image {
            max-width: 80px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="checkout-header">Checkout</h2>

    <?php if ($cartItems): ?>
        <form action="" method="POST">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Cart Summary</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image"></td>
                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="total-amount">
                        <strong>Total Amount: ₱<?php echo number_format($totalAmount, 2); ?></strong>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h4>Shipping Address</h4>
                </div>
                <div class="card-body">
                    <textarea id="shipping_address" name="shipping_address" class="form-control shipping-textarea" rows="4" placeholder="Enter your shipping address" required></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" name="confirm_order" class="btn btn-custom">Confirm Order</button>
                <a href="cart.php" class="btn btn-danger-custom">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">Your cart is empty. <a href="cart.php">Go back to your cart</a></div>
    <?php endif; ?>
</div>

<script>
    <?php if ($orderSuccess): ?>
        alert('Your order has been placed successfully.');
        window.location.href = 'index.php'; // Redirect to home page or desired page
    <?php endif; ?>
</script>

</body>
</html>
