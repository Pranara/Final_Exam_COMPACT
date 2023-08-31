<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cart_db";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cartItems = [];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $stmt = $conn->prepare("SELECT p.product_name, c.quantity, c.total_price FROM carts c JOIN products p ON c.product_id = p.id");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        die("Getting result set failed: " . $stmt->error);
    }

    $cartItems = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = isset($_POST["product_name"]) ? $_POST["product_name"] : "";
    $quantity = isset($_POST["quantity"]) ? $_POST["quantity"] : "";

    $stmt_product = $conn->prepare("SELECT id, price FROM products WHERE product_name = ?");
    $stmt_product->bind_param("s", $product_name);
    if (!$stmt_product->execute()) {
        die("Execution failed: " . $stmt_product->error);
    }
    $stmt_product->bind_result($product_id, $product_price);
    if (!$stmt_product->fetch()) {
        die("Product not found.");
    }
    $stmt_product->close();

    if (!is_numeric($product_price)) {
        die("Invalid product price.");
    }

    if (!is_numeric($quantity) || $quantity <= 0) {
        echo "Quantity must be a positive number.<br>";
    } else {
        $total_price = $product_price * $quantity;

        $stmt_add_to_cart = $conn->prepare("INSERT INTO carts (product_id, quantity, total_price) VALUES (?, ?, ?)");
        $stmt_add_to_cart->bind_param("iid", $product_id, $quantity, $total_price);
        if (!$stmt_add_to_cart->execute()) {
            die("Execution failed: " . $stmt_add_to_cart->error);
        }
        $stmt_add_to_cart->close();

        echo "Item added to cart successfully.";
    }
} else if (isset($_POST["remove_from_cart"])) {
    $product_name = $_POST["product_name"];

    $stmt_product_id = $conn->prepare("SELECT id FROM products WHERE product_name = ?");
    $stmt_product_id->bind_param("s", $product_name);
    if (!$stmt_product_id->execute()) {
        die("Execution failed: " . $stmt_product_id->error);
    }
    $stmt_product_id->bind_result($product_id);
    if (!$stmt_product_id->fetch()) {
        die("Product not found.");
    }
    $stmt_product_id->close();

    $stmt_remove_from_cart = $conn->prepare("DELETE FROM carts WHERE product_id = ?");
    $stmt_remove_from_cart->bind_param("i", $product_id);
    if (!$stmt_remove_from_cart->execute()) {
        die("Execution failed: " . $stmt_remove_from_cart->error);
    }
    $stmt_remove_from_cart->close();

    echo "Item removed from cart successfully.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
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

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
        }

        form {
            display: inline;
        }

        form input[type="submit"] {
            background-color: #ff5c5c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .add-form {
            margin-top: 20px;
        }

        .add-product-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>My Cart</h2>
    <?php
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo '<p style="color: green;">Item removed from cart successfully.</p>';
    }
    ?>
        <div>
            <ul>
                <?php foreach ($cartItems as $item) : ?>
                    <li>
                        Product: <?php echo $item['product_name']; ?>,
                        Quantity: <?php echo $item['quantity']; ?>,
                        Total Price: <?php echo $item['total_price']; ?>
                        <form method="post" action="remove_process.php">
                            <input type="hidden" name="product_name" value="<?php echo $item['product_name']; ?>">
                            <input type="submit" name="remove_from_cart" value="Remove">
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <h2>Add Item to Cart</h2>
        <form class="add-form" method="post" action="">
            Product Name: <input type="text" name="product_name" required><br>
            Quantity: <input type="number" name="quantity" required><br>
            <input type="submit" name="add_product" value="Add Product">
        </form>

        <a class="add-product-link" href="add_product.php">Add a New Product</a>
    </div>
</body>
</html>
