<?php
session_start();
include_once 'database.php'; 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 获取产品详情
if(isset($_GET['pid'])) {
    $pid = $_GET['pid'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM tbl_products_a185125_pt2 WHERE fld_product_id = :pid");
        $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Details</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 700px;
            margin-top: 20px;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            background-color: #fff;
        }
        .product-img {
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .btn-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <?php include_once 'nav_bar.php'; ?>

    <div class="container">
        <h2 class="text-center">Product Details</h2>
        <hr>

        <?php if($product): ?>
            <div class="text-center">
                <?php 
                $product_image_path = isset($product['image_path']) && !empty($product['image_path']) ? $product['image_path'] : "uploads/default.jpg"; 
                ?>
                <img src="<?php echo $product_image_path . '?t=' . time(); ?>" alt="Product Image" class="product-img">
            </div>
            <hr>

            <table class="table table-bordered">
                <tr><th>Product ID</th><td><?php echo htmlspecialchars($product['fld_product_id']); ?></td></tr>
                <tr><th>Name</th><td><?php echo htmlspecialchars($product['fld_product_name']); ?></td></tr>
                <tr><th>Price</th><td>RM <?php echo number_format($product['fld_price'], 2); ?></td></tr>
                <tr><th>Type</th><td><?php echo htmlspecialchars($product['fld_type']); ?></td></tr>
                <tr><th>Brand</th><td><?php echo htmlspecialchars($product['fld_brand']); ?></td></tr>
                <tr><th>Warranty Period</th><td><?php echo htmlspecialchars($product['fld_warranty_period']); ?></td></tr>
                <tr><th>Quantity</th><td><?php echo htmlspecialchars($product['fld_quantity']); ?></td></tr>
            </table>

        <?php else: ?>
            <div class="alert alert-danger text-center">Product not found!</div>
        <?php endif; ?>

        <div class="text-center btn-back">
            <a href="products.php" class="btn btn-secondary">Back to Products</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
