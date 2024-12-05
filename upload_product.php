<?php
// Establish database connection
$host = 'localhost';  // Database host
$dbname = 'errol';  // Database name
$username = 'root';  // Database username
$password = '';  // Database password

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get product details from the form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle image upload
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Prepare the SQL query to insert the product into the database
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $description, $price, $targetFile);
            
            // Execute the query
            if ($stmt->execute()) {
                echo "Product uploaded successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "File is not an image.";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        .remove-btn {
            margin-left: 10px;
            color: red;
            cursor: pointer;
            font-weight: bold;
        }
        #image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'navbar_seller.php'?>
    <div class="container mt-5">
        <h1 class="mb-4">Add a New Product</h1>
        <form action="upload_product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group mb-3">
                <label for="name">Product Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group mb-3">
                <label for="description">Product Description:</label>
                <textarea class="form-control" id="description" name="description" ></textarea>
            </div>
            <div class="form-group mb-3">
                <label for="price">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group mb-3">
                <label for="image">Product Image:</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required onchange="previewImage(event)">
                <!-- Image Preview -->
                <img id="image-preview" src="#" alt="Image Preview" />
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>

    <script>
        // Function to preview the selected image
        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function() {
                const preview = document.getElementById("image-preview");
                preview.src = reader.result;
                preview.style.display = "block";  // Show the image preview
            }

            if (file) {
                reader.readAsDataURL(file); // Read the file as a data URL
            }
        }
    </script>
</body>
</html>
