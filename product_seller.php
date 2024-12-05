<?php
// Establish database connection
$host = 'localhost';
$dbname = 'errol';
$username = 'root';
$password = '';

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // Delete the product
    $deleteProductSql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($deleteProductSql);
    $stmt->bind_param("i", $deleteId);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Product deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting product: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #343a40;
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: #ffffff !important;
        }

        .card {
            margin-bottom: 30px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card img {
            border-radius: 10px 10px 0 0;
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            text-align: center;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .container {
            max-width: 1200px;
            margin-top: 50px;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php include 'navbar_seller.php'; ?>

<div class="container">
    <h1 class="text-center mb-4">Manage Products</h1>

    <a href="upload_product.php" class="btn btn-success mb-4">Add New Product</a>

    <?php
    if (isset($_GET['delete_id'])) {
        echo "<div class='alert alert-success text-center'>Product deleted successfully.</div>";
    }
    ?>

    <!-- Products Cards -->
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "
                <div class='col-md-3 col-sm-6'>
                    <div class='card'>
                        <img src='{$row['image']}' alt='{$row['name']}'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$row['name']}</h5>
                            <p class='card-text'>₱" . number_format($row['price'], 2) . "</p>
                            <a href='?delete_id={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>
                        </div>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-warning text-center'>No products found.</div></div>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
