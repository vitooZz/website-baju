<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['add_to_cart'])) {
    $product_id = intval($_GET['add_to_cart']);
    if (!in_array($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $product_id;
    }
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <img src="logo.png" alt="Vlems Project">
    </nav>
    <div class="container">
        <h1>VLEMS PROJECT</h1>
        <div class="products">
            <?php
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);
            

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product-item'>";
                    echo "<img src='" . $row['image'] . "' alt='" . $row['name'] . "'>";
                    echo "<h3>" . $row['name'] . "</h3>";
                    echo "<p>Rp" . number_format($row['price'], 0, ',', '.') . "</p>";
                    echo "<a href='index.php?add_to_cart=" . $row['id'] . "'><button>Add to Cart</button></a>";

                    echo "<a href='cart.php?add_to_cart=" . $row['id'] . "'><button>Cart</button></a>";
                    echo "</div>";
                }
            } else {
                echo "<p>Produk tidak tersedia.</p>";
            }
            ?>
        </div>
        <footer>
    <div class="social-links">
        <a href="https://www.instagram.com/vlems.project/" target="_blank">
            <img src="instagram.png" alt="Instagram">
        </a>
        <a href="https://wa.me/088902976449" target="_blank">
            <img src="whatsapp.png" alt="WhatsApp">
        </a>
    </div>
</footer>

    </div>
</body>
</html>
