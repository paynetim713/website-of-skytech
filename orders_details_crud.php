<?php
session_start();
include_once 'database.php';

// ✅ 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ 处理 `Add Product`
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    try {
        // 生成唯一的 `Order Detail ID`
        $order_detail_id = uniqid('D') . '.' . mt_rand(100000, 999999);

        $order_id = $_POST['order_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        if (empty($product_id) || empty($quantity)) {
            die("Error: Product and quantity are required.");
        }

        // 插入数据库
        $stmt = $conn->prepare("INSERT INTO tbl_orders_details_a185125_pt2 
                                (fld_order_detail_id, fld_order_id, fld_product_id, fld_quantity) 
                                VALUES (:order_detail_id, :order_id, :product_id, :quantity)");
        $stmt->bindParam(':order_detail_id', $order_detail_id, PDO::PARAM_STR);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_STR);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->execute();

        // 成功后跳转回 `orders_details.php`
        header("Location: orders_details.php?oid=" . $order_id);
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// ✅ 处理 `Delete Product`
if (isset($_GET['delete']) && isset($_GET['oid'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM tbl_orders_details_a185125_pt2 WHERE fld_order_detail_id = :order_detail_id");
        $stmt->bindParam(':order_detail_id', $_GET['delete'], PDO::PARAM_STR);
        $stmt->execute();

        // 删除后返回 `orders_details.php`
        header("Location: orders_details.php?oid=" . $_GET['oid']);
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
