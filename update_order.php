<?php
// update_order.php - Admin script to update the order status

include('dbcon/connect.php'); // Include the PDO connection

// Get the updated status and order ID
$order_id = $_POST['order_id'];
$new_status = $_POST['status'];

try {
    // Update the order status in the database using PDO
    $sql = "UPDATE orders SET order_status = :order_status WHERE order_id = :order_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['order_status' => $new_status, 'order_id' => $order_id]);

    // Redirect to the order details page after updating the status
    echo "Order status updated successfully!";
    header('Location: order_details.php?id=' . $order_id); // Redirect back to order details
} catch (PDOException $e) {
    echo "Error updating order status: " . $e->getMessage();
}
?>
