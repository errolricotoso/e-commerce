<?php
// order_details.php - Admin page to view detailed order information

include('dbcon/connect.php'); // Include the PDO connection

// Get the order ID from the URL
$order_id = $_GET['id'];

try {
    // Fetch the order details from the database
    $sql = "
        SELECT orders.*, users.full_name, users.phone, 
               order_items.product_id, order_items.quantity, order_items.size, order_items.color,
               products.name AS product_name, products.image AS product_image
        FROM orders
        JOIN users ON orders.user_id = users.id
        JOIN order_items ON orders.order_id = order_items.order_id
        JOIN products ON order_items.product_id = products.id
        WHERE orders.order_id = :order_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['order_id' => $order_id]);
    $orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($orderDetails) {
        // Extract general order info from the first row
        $order = $orderDetails[0];
        ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <a href="orders.php" class="btn btn-secondary mb-3">Back to Orders</a>

        <div class="border p-4 rounded">
            <h2>Order Details</h2>
            <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']); ?></p>
            <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['full_name']); ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']); ?></p>
            <p><strong>Status:</strong>
                <span class="badge 
                <?= $order['order_status'] == 'pending' ? 'bg-warning' : ''; ?>
                <?= $order['order_status'] == 'shipped' ? 'bg-secondary' : ''; ?>
                <?= $order['order_status'] == 'completed' ? 'bg-success' : ''; ?>">
                    <?= ucfirst(htmlspecialchars($order['order_status'])); ?>
                </span>
            </p>
            <p><strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2); ?></p>
            <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['shipping_address']); ?></p>

            <h4 class="mt-4">Products</h4>
            <ul class="list-unstyled">
                <?php foreach ($orderDetails as $item): ?>
                    <li class="d-flex align-items-center mb-2">
                        <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;" class="me-3">
                        <div>
                            <p class="mb-1"><strong><?= htmlspecialchars($item['product_name']); ?></strong></p>
                            <small>Quantity: <?= htmlspecialchars($item['quantity']); ?></small>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <form action="update_order.php" method="POST" class="mt-4">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']); ?>">
                <label for="status" class="form-label">Update Status:</label>
                <select name="status" id="status" class="form-select mb-3">
                    <option value="pending" <?= $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="shipped" <?= $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                </select>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>
</body>

</html>
<?php
    } else {
        echo "<div class='container text-center mt-5'><p class='text-danger'>Order not found.</p></div>";
    }
} catch (PDOException $e) {
    echo "<div class='container text-center mt-5'><p class='text-danger'>Error fetching order details: " . htmlspecialchars($e->getMessage()) . "</p></div>";
}
?>
