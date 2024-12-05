<?php
// Start the session to track user login
session_start(); // Add this line to start the session

// Include the database connection file
include 'dbcon/connect.php';

// Check if the user is logged in by checking for the user_id in the session
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to login page if not logged in
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch cart items and product details from the database
$sql = "SELECT c.cart_id, c.quantity, p.id as product_id, p.name, p.image, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update cart item quantity
if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    // Update quantity in the cart
    $updateSql = "UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([':quantity' => $quantity, ':cart_id' => $cart_id]);

    header("Location: cart.php"); // Reload the page to reflect the changes
    exit;
}

// Remove item from the cart
if (isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];

    // Delete the cart item
    $deleteSql = "DELETE FROM cart WHERE cart_id = :cart_id";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute([':cart_id' => $cart_id]);

    header("Location: cart.php"); // Reload the page to reflect the changes
    exit;
}

// Remove selected items from the cart (bulk action)
if (isset($_POST['remove_selected_items'])) {
    if (isset($_POST['cart_ids']) && !empty($_POST['cart_ids'])) {
        // Sanitize and prepare the cart IDs for deletion
        $cart_ids = array_map('intval', $_POST['cart_ids']); // Ensure IDs are integers
        $deleteSql = "DELETE FROM cart WHERE cart_id IN (" . implode(',', $cart_ids) . ")";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute();
    }

    header("Location: cart.php"); // Reload the page to reflect the changes
    exit;
}

// Proceed to checkout when user clicks the checkout button
if (isset($_POST['proceed_to_checkout'])) {
    if (isset($_POST['cart_ids']) && !empty($_POST['cart_ids'])) {
        // Redirect to the checkout page with the selected cart IDs
        $selectedCartIds = implode(',', $_POST['cart_ids']);
        header("Location: checkout.php?cart_ids=$selectedCartIds");
        exit;
    } else {
        echo "Please select items to proceed to checkout.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .cart-item img {
            max-width: 50px;
            height: auto;
        }
        .cart-actions {
            text-align: right;
            margin-top: 10px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            color: #fff;
            cursor: pointer;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<?php include "navbar.php"?>
<h2>Your Cart</h2>
<?php if ($cartItems): ?>
    <form action="" method="POST" id="cartForm">
        <table>
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalAmount = 0;
                foreach ($cartItems as $item):
                    $totalPrice = $item['price'] * $item['quantity'];
                    $totalAmount += $totalPrice;
                ?>
                    <tr class="cart-item">
                        <td><input type="checkbox" name="cart_ids[]" value="<?php echo $item['cart_id']; ?>" class="cart-checkbox"></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <form action="" method="POST">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="10" style="width: 50px;" readonly>
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                            </form>
                        </td>
                        <td>₱<?php echo number_format($totalPrice, 2); ?></td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="submit" name="remove_item" class="btn btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-actions">
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($totalAmount, 2); ?></p>
            <button type="submit" name="proceed_to_checkout" class="btn btn-success" id="checkoutBtn" disabled>Checkout</button>
            <button type="submit" name="remove_selected_items" class="btn btn-danger">Remove Selected</button>
        </div>
    </form>
<?php else: ?>
    <p>Your cart is empty. Start shopping now!</p>
<?php endif; ?>

<script>
    // Enable "Checkout" button only if at least one checkbox is selected
    document.querySelectorAll('.cart-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const checkoutBtn = document.getElementById('checkoutBtn');
            checkoutBtn.disabled = !document.querySelector('input[name="cart_ids[]"]:checked');
        });
    });
</script>

</body>
</html>
