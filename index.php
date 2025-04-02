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

// Fetch products from the database
$products = [];
$result = $conn->query("SELECT * FROM products WHERE isActive=1");
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
    <title>Product Listing</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }


        header {
            background: #007bff;
            color: white;
            padding: 20px 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s ease-in-out;
        }

        nav ul li a:hover {
            color: #f4a261;
        }


        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 40px;
        }

        .product {
            background: white;
            /* padding: 20px; */
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 280px;
            transition: transform 0.3s ease;
        }

        .product:hover {
            transform: translateY(-5px);
        }

        .product img {
            width: 70%;
            border-radius: 10px;
        }

        .product h2 {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }

        .product p {
            color: #007bff;
            font-size: 20px;
            font-weight: bold;
            margin: 5px;
        }

        .product button {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: white;
            border: none;
            padding: 5px;
            cursor: pointer;
            border-radius: 30px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 80%;
            margin-bottom: 10px;
        }

        .product button:hover {
            background: linear-gradient(135deg, #ff4b2b, #ff416c);
            transform: scale(1.05);
        }

        .product button::before {
            content: '\1F6D2';
            /* Shopping Cart Unicode */
            font-size: 18px;
        }

        #cart-count {
            background: red;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 14px;
        }

        /* Popup Styles */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 15px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .popup-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .popup-items {
            list-style: none;
            padding: 0;
        }

        .popup-items li {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 18px;
        }

        .popup-items li:last-child {
            border-bottom: none;
        }

        .popup-items select {
            font-size: 16px;
            padding: 5px;
            width: 60px;
        }

        .remove-item {
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .remove-item:hover {
            background: darkred;
        }

        .close-popup {
            background: red;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            text-transform: uppercase;
        }

        .close-popup:hover {
            background: darkred;
        }

        .product input[type="number"] {
            font-size: 16px;
            padding: 10px;
            width: 60px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
            background-color: #fff;
            margin-top: 10px;
        }

        .product input[type="number"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .product input[type="number"]::-webkit-outer-spin-button,
        .product input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .product input[type="number"]:disabled {
            background-color: #f1f1f1;
            cursor: not-allowed;
        }

        .product label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-top: 10px;
            font-weight: 600;
        }

        /* Default styling for larger screens */
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 10px;
        }

        /* For mobile devices */
        @media (max-width: 768px) {
            .product-list {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                /* 2 items per row */
                gap: 20px;
                padding: 20px;
            }
        }

        /* For even smaller mobile devices (portrait mode) */
        @media (max-width: 480px) {
            .product-list {
                grid-template-columns: repeat(2, 1fr);
                /* 1 item per row */
                 gap: 5px;
            }

            .product {
                width: 100%;
            }
        }

        /* Fix the cart button at the bottom-right corner */
        #cart-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        #cart-link:hover {
            background: #0056b3;
            transform: scale(1.1);
        }

        /* Cart icon before the text */
        #cart-link::before {
            content: '\1F6D2';
            /* Shopping Cart Unicode */
            font-size: 24px;
        }

        /* Cart count badge */
        #cart-count {
            background: red;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            font-size: 14px;
            position: absolute;
            top: -5px;
            right: -5px;
            z-index: 2;
        }

        .delivery-form {
            display: none;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 350px;
            margin: 20px auto;
            text-align: center;
        }

        .delivery-form h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 20px;
        }

        .delivery-form input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: border 0.3s;
        }

        .delivery-form input:focus {
            border-color: #007bff;
        }

        .delivery-form button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .delivery-form button:hover {
            background: #0056b3;
        }

        .styled-input {
            width: 100px;
            padding: 8px;
            font-size: 16px;
            border: 2px solid #007bff;
            border-radius: 5px;
            background-color: white;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .styled-input:hover {
            border-color: #0056b3;
        }

        .styled-input:focus {
            outline: none;
            border-color: #004494;
            box-shadow: 0 0 5px rgba(0, 91, 187, 0.5);
        }

        section {
            padding: 50px !important;
        }
    </style>
</head>

<body>

    <header>
        <div class="container">
            <h1>ME & BROS</h1>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="services.html">Services</a></li>
                    <!-- <li><a href="contact.html">Contact</a></li> -->
                </ul>
            </nav>
        </div>
    </header>

    <section class="product-list" style="padding: 10px !important;">
        <?php foreach ($products as $product): ?>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content:space-between;" class="product" data-name="<?= $product['name'] ?>" data-price="<?= $product['price'] ?>">
                <div style="padding:10px;">
                    <img src="<?= $product['image_url'] ?>" alt="Product 1">
                    <h2><?= $product['name'] ?></h2>
                    <p>LKR<?= $product['price'] ?></p>
                    <div style="display: flex; justify-content: space-around; align-items: center; width: 100%; max-width: 400px; margin: auto; gap: 5px;">
                        <div >
                            <label for="quantity1">Quantity:</label>
                            <input style="display: inline-block; width: 60px; height: 25px; font-size: 8px; padding: 5px;" type="number" id="quantity1" value="1" min="1" max="10" class="styled-input"><br><br>
                        </div>
                        <div style="position: relative; margin-bottom:10px;">
                            <label style="margin-top: 0px; margin-bottom:10px;" for="size">Size:</label>
                            <select id="size" class="styled-input" style="display: inline-block; width: 60px; height: 38px; font-size: 8px; padding: 5px;" >
                                <option value="default">Default</option>
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                                <option value="extra-large">Extra Large</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: center; align-items: center; width: 100%;">
                        <button class="add-to-cart">Add to Cart</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <a href="javascript:void(0)" id="cart-link">Cart <span id="cart-count">0</span></a>
    <!-- Popup -->
    <div class="popup-overlay" id="popup-overlay">
        <div class="popup-content">
            <div class="popup-header">Your Cart</div>
            <ul class="popup-items" id="popup-items"></ul>

            <div id="delivery-form" class="delivery-form">
                <h3>Enter Delivery Details</h3>
                <input type="text" id="name" placeholder="Full Name" required>
                <input type="tel" id="phone" placeholder="Phone Number" required>
                <input type="text" id="address" placeholder="Address" required>
                <input type="text" id="postal" placeholder="Postal Code" required>
            </div>

            <div style="display: flex;">
                <button style="margin-right: 5px;" class="close-popup" id="close-popup">Close</button>
                <button class="close-popup" id="proceed-btn">Proceed</button>
            </div>
        </div>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        document.getElementById("cart-count").innerText = cart.length;

        // Open popup
        document.getElementById("cart-link").addEventListener("click", function() {
            const popupOverlay = document.getElementById("popup-overlay");
            const popupItems = document.getElementById("popup-items");
            popupItems.innerHTML = "";

            let totalAmount = 0; // Initialize total amount

            // Update cart display in popup
            cart.forEach((item, index) => {
                let li = document.createElement("li");
                let itemTotal = item.price * item.quantity; // Calculate total for each item
                totalAmount += itemTotal; // Add to total amount

                li.innerHTML = `${item.name}- ${item.size} - LKR${item.price} x ${item.quantity} = LKR${itemTotal.toFixed(2)}
            <button class='remove-item' data-index='${index}'>Remove</button>`;
                popupItems.appendChild(li);
            });

            // Display total amount
            let totalDiv = document.createElement("div");
            totalDiv.innerHTML = `<br><strong>Total: LKR${totalAmount.toFixed(2)}</strong>`;
            totalDiv.id = "cart-total";
            totalDiv.style.textAlign = "center";
            popupItems.appendChild(totalDiv);

            // Show the popup
            popupOverlay.style.display = "flex";
        });

        // Close popup
        document.getElementById("close-popup").addEventListener("click", function() {
            document.getElementById("popup-overlay").style.display = "none";
        });

        // Add item to cart with quantity
        document.querySelectorAll(".add-to-cart").forEach(button => {
            button.addEventListener("click", function() {
                let product = this.closest(".product");
                let productName = product.getAttribute("data-name");
                let productPrice = parseFloat(product.getAttribute("data-price")); // Convert price to float
                let quantity = parseInt(product.querySelector("input[type='number']").value); // Convert quantity to integer
                let size = product.querySelector("select").value; // Get selected size


                // Check if product is already in the cart
                let existingProduct = cart.find(item => item.name === productName);
                if (existingProduct) {
                    existingProduct.quantity += quantity;
                } else {
                    cart.push({
                        name: productName,
                        price: productPrice,
                        quantity: quantity,
                        size: size
                    });
                }

                localStorage.setItem("cart", JSON.stringify(cart));
                document.getElementById("cart-count").innerText = cart.length;
            });
        });

        // Remove item from cart and update total
        document.getElementById("popup-items").addEventListener("click", function(event) {
            if (event.target && event.target.classList.contains("remove-item")) {
                const index = event.target.getAttribute("data-index");
                cart.splice(index, 1);
                localStorage.setItem("cart", JSON.stringify(cart));
                document.getElementById("cart-count").innerText = cart.length;

                // Recalculate total
                event.target.parentElement.remove();
                updateCartTotal();
            }
        });

        // Function to update total after removing an item
        function updateCartTotal() {
            let totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            let totalDiv = document.getElementById("cart-total");
            if (totalDiv) {
                totalDiv.innerHTML = `<strong>Total: LKR${totalAmount.toFixed(2)}</strong>`;
            }
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let proceedBtn = document.getElementById("proceed-btn");
            let deliveryForm = document.getElementById("delivery-form");

            // Load saved details from local storage
            const savedDetails = JSON.parse(localStorage.getItem("deliveryDetails"));
            if (savedDetails) {
                document.getElementById("name").value = savedDetails.name || "";
                document.getElementById("phone").value = savedDetails.phone || "";
                document.getElementById("address").value = savedDetails.address || "";
                document.getElementById("postal").value = savedDetails.postal || "";
            }

            proceedBtn.addEventListener("click", function() {
                if (proceedBtn.textContent === "Proceed") {
                    // Show the delivery form and change button text
                    deliveryForm.style.display = "block";
                    proceedBtn.textContent = "Confirm Order";
                    proceedBtn.style.backgroundColor = "blue";

                } else {
                    let name = document.getElementById("name").value;
                    let phone = document.getElementById("phone").value;
                    let address = document.getElementById("address").value;
                    let postal = document.getElementById("postal").value;
                    let cart = JSON.parse(localStorage.getItem("cart")) || [];

                    if (name && phone && address && postal) {
                        let orderDetails = `Order Details:\nName: ${name}\nPhone: ${phone}\nAddress: ${address}\nPostal Code: ${postal}\n\nItems:\n`;

                        cart.forEach((item, index) => {
                            orderDetails += `${index + 1}. ${item.name}- ${item.size} - $${item.price} x ${item.quantity} = $${(item.price * item.quantity).toFixed(2)}\n`;
                        });

                        let totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                        orderDetails += `\nTotal Amount: $${totalAmount.toFixed(2)}`;

                        let whatsappNumber = "94758156579"; // WhatsApp number
                        let whatsappURL = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(orderDetails)}`;

                        window.open(whatsappURL, "_blank");
                    } else {
                        alert("Please fill in all the details.");
                    }
                }
            });
        });
    </script>
</body>

</html>