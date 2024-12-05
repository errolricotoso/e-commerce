<?php
// orders.php - Admin page to view all orders with delete functionality

include('dbcon/connect.php'); // Include the PDO connection

// Handle deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];

    try {
        // SQL to delete the order by ID
        $deleteSQL = "DELETE FROM orders WHERE order_id = :deleteId";
        $deleteStmt = $pdo->prepare($deleteSQL);
        $deleteStmt->execute([':deleteId' => $deleteId]);

        // Redirect to refresh the page and prevent resubmission
        header("Location: orders.php");
        exit();
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error deleting order: " . $e->getMessage() . "</div>";
    }
}

try {
    // SQL query to fetch all orders along with the user details
    $sql = "
        SELECT orders.order_id, users.full_name, orders.order_status, orders.total_amount, orders.order_date
        FROM orders
        JOIN users ON orders.user_id = users.id
    ";

    // Prepare the query
    $stmt = $pdo->prepare($sql);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch all the results
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if there are no orders found
    $noResults = empty($orders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* Body and Font Style */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        
        /* Navbar styling */
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #ffffff !important;
        }
        .navbar-nav .nav-link {
            font-size: 1.1rem;
        }
        
        /* Container for orders list */
        .container {
            margin-top: 30px;
            max-width: 1100px;
        }

        /* Title Styling */
        h1 {
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Table Styling */
        .table {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }

        /* Status Badge Styling */
        .badge {
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .badge-warning {
            background-color: #ff9800;
            color: #fff;
        }
        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }
        .badge-success {
            background-color: #28a745;
            color: #fff;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        /* Button Styling */
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* Hover Effect for Table Rows */
        .table tbody tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        /* No Results Alert */
        .alert-warning {
            text-align: center;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<?php include 'navbar_seller.php'?>

<div class="container">
    <h1>Orders List</h1>

    <!-- Table displaying orders -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Loop through the results and display the orders
        foreach ($orders as $row) {
            // Add status badge styling
            $statusClass = '';
            switch ($row['order_status']) {
                case 'pending': $statusClass = 'badge-warning'; break;
                case 'processing': $statusClass = 'badge-info'; break;
                case 'completed': $statusClass = 'badge-success'; break;
                case 'shipped': $statusClass = 'badge-secondary'; break;
            }

            // Format the order date
            $orderDate = date("F j, Y", strtotime($row['order_date']));

            echo "<tr>
                    <td>{$row['order_id']}</td>
                    <td>{$row['full_name']}</td>
                    <td>₱" . number_format($row['total_amount'], 2) . "</td>
                    <td><span class='badge {$statusClass}'>{$row['order_status']}</span></td>
                    <td>{$orderDate}</td>
                    <td>
                        <form method='POST' action='' style='display:inline;'>
                            <input type='hidden' name='delete_id' value='{$row['order_id']}'>
                            <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this order?\");'>Delete</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- No results message -->
    <?php if ($noResults): ?>
        <div class="alert alert-warning text-center">
            No orders found.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
} catch (PDOException $e) {
    echo "Error fetching orders: " . $e->getMessage();
}
?>
