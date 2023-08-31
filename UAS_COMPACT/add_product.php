<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cart_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_product"])) {
    $product_name = $_POST["product_name"];
    $product_description = $_POST["product_description"];
    $price = $_POST["price"];

    $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $product_name, $product_description, $price);

    if ($stmt->execute()) {
        echo "Product added successfully.";
    } else {
        echo "Error adding product: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        textarea,
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .cart-link {
            text-align: center;
            margin-top: 10px;
        }
        .cart-link a {
            color: #007bff;
            text-decoration: none;
        }
        .cart-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Others</h2>
    
        <div class="cart-link">
            <a href="cart.php">Go to Cart</a>
        </div>
    </div>
</body>
</html>
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Product</h2>
        <form method="post" action="">
            <label for="product_name">Product Name:</label>
            <input type="text" name="product_name" required>
            
            <label for="product_description">Product Description:</label>
            <textarea name="product_description" required></textarea>
            
            <label for="price">Price:</label>
            <input type="number" name="price" step="0.01" required>
            
            <input type="submit" name="add_product" value="Add Product">
        </form>
    </div>
</body>
</html>

