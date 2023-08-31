    <?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cart_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_from_cart"])) {
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

    $conn->close();

    header("Location: cart.php?success=1");
    exit();
}
?>
