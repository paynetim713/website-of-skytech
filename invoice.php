<?php
session_start();
include_once 'database.php';

// ✅ 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ 确保 `order_id` 存在
if (!isset($_GET['oid'])) {
    die("Error: Missing Order ID.");
}

$order_id = $_GET['oid'];

try {
    // 获取订单信息
    $stmt = $conn->prepare("SELECT * FROM tbl_orders_a185125_pt2 WHERE fld_order_id = :oid");
    $stmt->bindParam(':oid', $order_id, PDO::PARAM_STR);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // 获取订单详情及产品信息
    $stmt = $conn->prepare("SELECT od.fld_quantity, p.fld_product_name, p.fld_price 
                            FROM tbl_orders_details_a185125_pt2 od
                            JOIN tbl_products_a185125_pt2 p ON od.fld_product_id = p.fld_product_id
                            WHERE od.fld_order_id = :oid");
    $stmt->bindParam(':oid', $order_id, PDO::PARAM_STR);
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
    <title>Invoice - <?php echo htmlspecialchars($order_id); ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .invoice-logo {
            width: 80px; /* Logo 大小 */
            height: auto;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #333;
        }

        .invoice-subtitle {
            font-size: 20px;
            color: #666;
        }

        .invoice-table {
            width: 100%;
            margin-top: 20px;
        }

        .invoice-table th {
            background: #007bff;
            color: white;
            text-align: center;
        }

        .invoice-table td {
            text-align: center;
        }

        .grand-total {
            background: #007bff;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }

        .btn-print {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <!-- ✅ Logo 和标题 -->
        <div class="invoice-header">
            <img src="logo.jpg" class="invoice-logo" alt="SkyTech Aviation Supplies Logo">
            <div>
                <div class="invoice-title">SkyTech Aviation Supplies</div>
                <div class="invoice-subtitle">Invoice</div>
            </div>
        </div>

        <hr>

        <p><strong>Order ID:</strong> <span><?php echo htmlspecialchars($order['fld_order_id']); ?></span></p>
        <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['fld_order_date']); ?></p>
        <p><strong>Staff:</strong> <?php echo htmlspecialchars($order['fld_staff_name']); ?></p>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['fld_customer_name']); ?></p>

        <hr>

        <!-- ✅ 订单详情表格 -->
        <table class="table table-bordered invoice-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price (RM)/Unit</th>
                    <th>Total (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                $counter = 1;
                foreach ($order_details as $detail) { 
                    $total = $detail['fld_price'] * $detail['fld_quantity'];
                    $grand_total += $total;
                ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo htmlspecialchars($detail['fld_product_name']); ?></td>
                    <td><?php echo htmlspecialchars($detail['fld_quantity']); ?></td>
                    <td><?php echo number_format($detail['fld_price'], 2); ?></td>
                    <td><?php echo number_format($total, 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="grand-total">
                    <td colspan="4" class="text-right"><strong>Grand Total</strong></td>
                    <td><strong><?php echo number_format($grand_total, 2); ?> RM</strong></td>
                </tr>
            </tfoot>
        </table>

        <hr>
        <p class="text-center">This is a computer-generated invoice. No signature required.</p>

        <!-- ✅ 打印按钮 -->
        <div class="btn-print text-center">
            <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
            <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
