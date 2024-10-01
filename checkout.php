<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Data pelanggan dari form
    $customer_name = $_POST['customer_name'];
    $customer_address = $_POST['customer_address'];
    $payment_method = $_POST['payment_method'];
    $total_price = 0.0; // Inisialisasi total harga

    // Hitung total harga
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $product_ids = implode(',', $_SESSION['cart']);
        $sql = "SELECT * FROM products WHERE id IN ($product_ids)";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $total_price += $row['price'];
            }
        }
    }

    // Simpan data pesanan ke tabel orders
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_address, payment_method, total_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $customer_name, $customer_address, $payment_method, $total_price);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Simpan item pesanan ke tabel order_items
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id) {
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            $quantity = 1; // Default quantity
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $product['price']);
            $stmt->execute();
        }
    }

    // Kosongkan keranjang setelah checkout
    unset($_SESSION['cart']);

    echo "Pesanan Anda telah diproses. Terima kasih!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <img src="logo.png" alt="Logo">
    </nav>
    <div class="container">
        <h1>Menu Checkout</h1>
        <a href="javascript:history.back()"><button>Back</button></a>
        
        <div class="checkout">
            <form action="checkout.php" method="post">
                <label for="customer_name">Nama:</label>
                <input type="text" id="customer_name" name="customer_name" required>

                <label for="customer_address">No HP:</label>
                <textarea id="customer_address" name="customer_address" required></textarea>

                <label for="payment_method">Metode Pembayaran:</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="Kartu Kredit">Kartu Kredit</option>
                    <option value="COD">COD</option>
                </select>

                <h2>Produk yang Anda Beli:</h2>
                <ul>
                    <?php
                    $total_price = 0.0;
                    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                        $product_ids = implode(',', $_SESSION['cart']);
                        $sql = "SELECT * FROM products WHERE id IN ($product_ids)";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<li>" . $row['name'] . " - Rp" . number_format($row['price'], 0, ',', '.') . "</li>";
                                $total_price += $row['price'];
                            }
                        }
                    } else {
                        echo "<li>Keranjang kosong</li>";
                    }
                    ?>
                </ul>

                <h3>Total: Rp<?php echo number_format($total_price, 0, ',', '.'); ?></h3>
                <input type="submit" value="Proses Checkout">
            </form>
        </div>
    </div>
</body>
</html>
