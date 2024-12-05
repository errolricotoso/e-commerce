<?php
include('dbcon/connect.php'); // Include database connection

// Fetch data from admin_users
try {
    $stmt_admins = $pdo->query("SELECT id, username, full_name, profile_image, created_at FROM admin_users");
    $admin_users = $stmt_admins->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching admin users: " . $e->getMessage());
}

// Fetch data from users
try {
    $stmt_users = $pdo->query("SELECT id, username, email, created_at FROM users");
    $regular_users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching regular users: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin and User List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar_admin.php'?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Seller and User List</h1>

    <!-- Admin Users Table -->
    <h3>Sellers</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($admin_users)): ?>
                <?php foreach ($admin_users as $admin): ?>
                    <tr>
                        <td><?= htmlspecialchars($admin['id']); ?></td>
                        <td><?= htmlspecialchars($admin['username']); ?></td>
                        <td><?= htmlspecialchars($admin['full_name']); ?></td>
                       
                        <td><?= htmlspecialchars($admin['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No admin users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Regular Users Table -->
    <h3>Regular Users</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($regular_users)): ?>
                <?php foreach ($regular_users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']); ?></td>
                        <td><?= htmlspecialchars($user['username']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No regular users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
