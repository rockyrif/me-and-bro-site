<?php
// Database configuration (Replace with your own credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "products_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle product update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_product'])) {
    $product_id = $_POST['product_id'];
    $product_code = $_POST['product_code'];
    $product_name = $_POST['product_name'];
    $product_qty = $_POST['product_qty'];
    $price = $_POST['price'];
    $product_discount = $_POST['product_discount'];
    $product_exceed = $_POST['product_exceed'];
    $product_category = $_POST['product_category'];
    $is_active = $_POST['is_active'];

    $stmt = $conn->prepare("UPDATE products SET code=?, name=?, quantity=?, price=?, discount=?, exceed_qty=?, category=?, isActive=? WHERE id=?");
    $stmt->bind_param("ssiiiisii", $product_code, $product_name, $product_qty, $price, $product_discount, $product_exceed, $product_category,$is_active, $product_id);

    if ($stmt->execute()) {
        header("Location: admin-dashboard.php?status=success");
        exit();
    } else {
        header("Location: admin-dashboard.php?status=error&message=" . urlencode($stmt->error));
        exit();
    }
}

// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        header("Location: admin-dashboard.php?status=success&message=deleted product listing");
        exit();
    } else {
        header("Location: admin-dashboard.php?status=error&message=" . urlencode($stmt->error));
        exit();
    }
}

// Fetch products from the database
$products = [];
$result = $conn->query("SELECT * FROM products");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 20px; }
        header { background-color: #007bff; color: white; padding: 20px; text-align: center; font-size: 24px; font-weight: bold; border-radius: 8px; }
        .dashboard-container { margin-top: 20px; }
        .table-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        td { background-color: #f9f9f9; }
        input, select { padding: 8px; width: 100%; }
        .edit, .delete { padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; }
        .edit { background-color: #28a745; color: white; }
        .delete { background-color: #dc3545; color: white; }
        .edit:hover { background-color: #218838; }
        .delete:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <header>Admin Dashboard - Product Management</header>
    <div class="dashboard-container">
        <div class="table-container">
            <h2>Product List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Code</th>
                        <th>Item Name</th>
                        <th>Available Qty</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Exceed Qty</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Is Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <form action="edit_db.php" method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <td><?= $product['id'] ?></td>
                                <td><input type="text" name="product_code" value="<?= $product['code'] ?>" required></td>
                                <td><input type="text" name="product_name" value="<?= $product['name'] ?>" required></td>
                                <td><input type="number" name="product_qty" value="<?= $product['quantity'] ?>" required></td>
                                <td><input type="number" name="price" value="<?= $product['price'] ?>" required></td>
                                <td><input type="number" name="product_discount" value="<?= $product['discount'] ?>" required></td>
                                <td><input type="number" name="product_exceed" value="<?= $product['exceed_qty'] ?>" required></td>
                                <td>
                                    <select name="product_category" required>
                                        <option value="electronics" <?= $product['category'] == 'electronics' ? 'selected' : '' ?>>Electronics</option>
                                        <option value="clothing" <?= $product['category'] == 'clothing' ? 'selected' : '' ?>>Clothing</option>
                                        <option value="home_appliances" <?= $product['category'] == 'home_appliances' ? 'selected' : '' ?>>Home Appliances</option>
                                    </select>
                                </td>
                                <td><img src="<?= $product['image_url'] ?>" alt="Product Image" style="width: 50px; height: 50px;"></td>
                                <td><input type="number" name="is_active" value="<?= $product['isActive'] ?>" required></td>
                                <td>
                                    <button type="submit" name="save_product" class="edit">Save</button>
                                    <button type="submit" name="delete_product" class="delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>