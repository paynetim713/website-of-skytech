<?php
session_start();
include_once 'database.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


if (!isset($_GET['oid'])) {
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['oid'];


try {
    $stmt = $conn->prepare("SELECT fld_product_id, fld_product_name FROM tbl_products_a185125_pt2");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

try {
    $stmt = $conn->prepare("SELECT od.fld_order_detail_id, od.fld_order_id, 
                                    p.fld_product_name, od.fld_quantity 
                                    FROM tbl_orders_details_a185125_pt2 od
                                    JOIN tbl_products_a185125_pt2 p ON od.fld_product_id = p.fld_product_id
                                    WHERE od.fld_order_id = :order_id");
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_STR);
    $stmt->execute();
    $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Details</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include_once 'nav_bar.php'; ?>

    <div class="container">
        <h2>Order Details for Order ID: <?php echo htmlspecialchars($order_id); ?></h2>
        
     
        <form action="orders_details_crud.php" method="post">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">

            <label>Product:</label>
            <select name="product_id" class="form-control">
                <option value="">Please select</option>
                <?php foreach ($products as $product) { ?>
                    <option value="<?php echo htmlspecialchars($product['fld_product_id']); ?>">
                        <?php echo htmlspecialchars($product['fld_product_name']); ?>
                    </option>
                <?php } ?>
            </select>

            <label>Quantity:</label>
            <input type="number" name="quantity" class="form-control" min="1" required>

            <br>
            <button type="submit" name="add_product" class="btn btn-primary">+ Add Product</button>
        </form>

        <hr>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order Detail ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_details as $detail) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detail['fld_order_detail_id']); ?></td>
                        <td><?php echo htmlspecialchars($detail['fld_product_name']); ?></td>
                        <td><?php echo htmlspecialchars($detail['fld_quantity']); ?></td>
                        <td>
                            <a href="orders_details_crud.php?delete=<?php echo htmlspecialchars($detail['fld_order_detail_id']); ?>&oid=<?php echo htmlspecialchars($order_id); ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure to delete this item?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

<form action="invoice.php" method="GET">
    <input type="hidden" name="oid" value="<?php echo htmlspecialchars($order_id); ?>">
    <button type="submit" class="btn btn-success btn-lg btn-block">Generate Invoice</button>
</form>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
