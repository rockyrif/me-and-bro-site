<?php
// Database configuration (Replace with your own credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "products_db";  // Change this to your actual database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the product details
    $product_code = $_POST['product_code'];
    $product_name = $_POST['product_name'];
    $product_qty = $_POST['product_qty'];
    $price = $_POST['price'];
    $product_discount = $_POST['product_discount'];
    $product_exceed = $_POST['product_exceed'];
    $product_category = $_POST['product_category']; // Get category

    // Ensure category folder exists
    $category_folder = 'uploads/' . $product_category;
    if (!file_exists($category_folder)) {
        mkdir($category_folder, 0777, true);
    }

    // Handle the image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $image = $_FILES['product_image'];
        $image_name = time() . '_' . basename($image['name']);
        $image_path = $category_folder . '/' . $image_name; // Save in category folder
    
        // Check if the file is an image and get dimensions
        $image_info = getimagesize($image['tmp_name']);
        
        if ($image_info === false) {
            header("Location: admin-dashboard.php?status=error&message=Uploaded file is not a valid image");
            exit();
        }
    
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        $image_file_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
        $allowed_file_types = ['jpg', 'jpeg', 'png', 'gif'];
    
        // Validate file type and dimensions
        if (!in_array($image_file_type, $allowed_file_types)) {
            header("Location: admin-dashboard.php?status=error&message=Only JPG, JPEG, PNG, and GIF files are allowed");
            exit();
        }
    
        if ($image_width != 1080 || $image_height != 1080) {
            header("Location: admin-dashboard.php?status=error&message=Image must be exactly 1080px × 1080px");
            exit();
        }
    
        // Move the uploaded file to the category directory
        if (move_uploaded_file($image['tmp_name'], $image_path)) {
            // Insert product data into the database
            $stmt = $conn->prepare("INSERT INTO products (code, name, quantity, price, discount, exceed_qty, category, image_url) VALUES (?, ?, ?, ?, ?, ?, ?,?)");
            $stmt->bind_param("ssiiiiss", $product_code, $product_name, $product_qty, $price, $product_discount, $product_exceed, $product_category, $image_path);
    
            if ($stmt->execute()) {
                header("Location: admin-dashboard.php?status=success&message=Product added successfully");
                exit();
            } else {
                header("Location: admin-dashboard.php?status=error&message=" . urlencode($stmt->error));
                exit();
            }
            $stmt->close();
        } else {
            header("Location: admin-dashboard.php?status=error&message=Sorry, there was an error uploading your file");
            exit();
        }
    } else {
        header("Location: admin-dashboard.php?status=error&message=Please select an image to upload");
        exit();
    }
    
}

// Fetch products from the database
$products = [];
$result = $conn->query("SELECT * FROM products");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-radius: 8px;
        }

        .dashboard-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
            justify-content: space-between;
        }

        .form-container,
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: 300px;
        }

        .form-container h2,
        .table-container h2 {
            margin-bottom: 20px;
        }

        /* Form Inputs */
        .form-container input,
        .form-container select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-container button:hover {
            background: linear-gradient(135deg, #ff4b2b, #ff416c);
            transform: scale(1.05);
        }

        /* Table Design */
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        button.edit,
        button.delete {
            background-color: #007bff;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        button.edit {
            background-color: #28a745;
        }

        button.delete {
            background-color: #dc3545;
        }

        button.edit:hover {
            background-color: #218838;
        }

        button.delete:hover {
            background-color: #c82333;
        }

        /* Mobile and Tablet Responsiveness */
        @media screen and (max-width: 768px) {
            header {
                font-size: 20px;
            }

            .dashboard-container {
                flex-direction: column;
                align-items: stretch;
            }

            .form-container,
            .table-container {
                width: 50%;
            }

            th,
            td {
                padding: 8px;
            }
        }

        @media screen and (max-width: 480px) {
            header {
                font-size: 18px;
            }

            .form-container input,
            .form-container select,
            .form-container button {
                font-size: 14px;
                padding: 10px;
            }

            th,
            td {
                font-size: 12px;
                padding: 8px;
            }

            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        #snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
        }

        #snackbar.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 3s;
        }

        @keyframes fadein {
            from {
                bottom: 0;
                opacity: 0;
            }

            to {
                bottom: 30px;
                opacity: 1;
            }
        }

        @keyframes fadeout {
            from {
                bottom: 30px;
                opacity: 1;
            }

            to {
                bottom: 0;
                opacity: 0;
            }
        }

        #snackbar {
            visibility: hidden;
            display: flex;
            align-items: center;
            min-width: 300px;
            max-width: 500px;
            background-color: #323232;
            color: white;
            text-align: left;
            border-radius: 8px;
            padding: 16px 20px;
            position: fixed;
            left: 50%;
            bottom: 30px;
            font-size: 16px;
            transform: translateX(-50%);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transition: opacity 0.3s ease-in-out, bottom 0.3s ease-in-out;
            margin: auto;
        }

        #snackbar.show {
            visibility: visible;
            opacity: 1;
            bottom: 50px;
        }

        #snackbar span {
            margin-left: 10px;
        }

        #snackbar-icon {
            font-size: 22px;
        }

        /* Success and Error Colors */
        .success {
            background-color: #28a745;
        }

        .error {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div id="snackbar">
        <span id="snackbar-icon"></span>
        <span id="snackbar-message"></span>
    </div>

    <header>Admin Dashboard - Product Management</header>

    <div class="dashboard-container">
        <!-- Form to Add New Product -->
        <div class="form-container">
            <h2>Add New Product</h2>
            <form action="admin-dashboard.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="product_code" placeholder="Product Code" required />
                <input type="text" name="product_name" placeholder="Item Name" required />
                <input type="number" name="product_qty" placeholder="Available Quantity" required />
                <input type="number" name="price" placeholder="price" required />
                <input type="number" name="product_discount" placeholder="Discount" required />
                <input type="number" name="product_exceed" placeholder="Exceed Quantity" required />
                <select name="product_category" required>
                    <option value="electronics">Electronics</option>
                    <option value="clothing">Clothing</option>
                    <option value="home_appliances">Home Appliances</option>
                </select>
                <input type="file" name="product_image" required />
                <button type="submit">Add Product</button>
            </form>
        </div>

        <!-- Table to Display Products -->
        <div class="table-container">
            <h2 style="display: flex; justify-content: space-between; align-items: center;">Product List
                <button class='edit-btn' onclick='editProductList()' style="display: flex; align-items: center; background-color: #ff4b2b; color: white; border: none; padding: 10px 15px; border-radius: 25px; cursor: pointer; font-weight: bold; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
                    ✏️ Edit
                </button>
            </h2>
            <script>
                function editProductList() {
                    window.location.href = 'edit_db.php';
                }
            </script>

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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($products as $product) {
                        echo "<tr>
                                <td>{$product['id']}</td>
                                <td>{$product['code']}</td>
                                <td>{$product['name']}</td>
                                <td>{$product['quantity']}</td>
                                <td>{$product['price']}</td>
                                <td>{$product['discount']}%</td>
                                <td>{$product['exceed_qty']}</td>
                                <td>{$product['category']}</td>
                                <td><img src='{$product['image_url']}' alt='Product Image' style='width: 50px; height: 50px;'></td>
                                <td>{$product['isActive']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message') || "Product added successfully!";

            if (status) {
                let snackbar = document.getElementById("snackbar");
                let snackbarMessage = document.getElementById("snackbar-message");
                let snackbarIcon = document.getElementById("snackbar-icon");

                snackbarIcon.innerText = (status === "success") ? "✔️" : "❌";
                snackbarMessage.innerText =  decodeURIComponent(message);
                snackbar.className = "show";
                

                setTimeout(() => {
                    snackbar.className = snackbar.className.replace("show", "dontShow");
                    window.history.replaceState({}, document.title, window.location.pathname); // Remove query params
                }, 3000);
            }
        };
    </script>


</body>

</html>