<?php
session_start();
include 'db.php';

if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    if (($key = array_search($remove_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <img src="logo.png" alt="Logo">
    </nav>
    <div class="container">
        <h1>Keranjang Belanja</h1>
        <a href="index.php"><button>Back</button></a>
        <div class="cart">
            <?php
            $total_price = 0.0;
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $product_ids = implode(',', $_SESSION['cart']);
                $sql = "SELECT * FROM products WHERE id IN ($product_ids)";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $total_price += $row['price'];
                        echo "<div class='cart-item'>";
                        echo "<p>" . $row['name'] . " - Rp" . number_format($row['price'], 0, ',', '.') . "</p>";
                        echo "<a href='cart.php?remove=" . $row['id'] . "' class='remove-button'>Hapus</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Keranjang kosong</p>";
                }
            } else {
                echo "<p>Keranjang kosong</p>";
            }
            ?>
            <h3>Total: Rp<?php echo number_format($total_price, 0, ',', '.'); ?></h3>
            <a href="checkout.php"><button>Checkout</button></a>
        </div>
    </div>
</body>
</html>
