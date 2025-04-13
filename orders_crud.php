<?php
session_start();
include_once 'database.php';

// ✅ 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ 连接数据库
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ✅ 生成唯一 `order_id`
function generateOrderID() {
    return 'O' . uniqid() . '.' . mt_rand(10000000, 99999999);
}

// ✅ 处理 `Create` 订单（`Staff` 和 `Admin` 均可）
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create'])) {
    try {
        $orderID = generateOrderID();
        $stmt = $conn->prepare("INSERT INTO tbl_orders_a185125_pt2 (fld_order_id, fld_customer_name, fld_staff_name, fld_order_date) 
                                VALUES (:orderid, :customer, :staff, :orderdate)");
        $stmt->bindParam(':orderid', $orderID, PDO::PARAM_STR);
        $stmt->bindParam(':customer', $_POST['customer'], PDO::PARAM_STR);
        $stmt->bindParam(':staff', $_POST['staff'], PDO::PARAM_STR);
        $stmt->bindParam(':orderdate', $_POST['orderdate'], PDO::PARAM_STR);
        $stmt->execute();
        header("Location: orders.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// ✅ 处理 `Edit` 订单（仅限 `Admin`）
if ($_SESSION['user_level'] === 'Admin' && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    try {
        $stmt = $conn->prepare("UPDATE tbl_orders_a185125_pt2 
                                SET fld_customer_name = :customer, fld_staff_name = :staff, fld_order_date = :orderdate 
                                WHERE fld_order_id = :oid");
        $stmt->bindParam(':customer', $_POST['customer'], PDO::PARAM_STR);
        $stmt->bindParam(':staff', $_POST['staff'], PDO::PARAM_STR);
        $stmt->bindParam(':orderdate', $_POST['orderdate'], PDO::PARAM_STR);
        $stmt->bindParam(':oid', $_POST['oid'], PDO::PARAM_STR);
        $stmt->execute();
        header("Location: orders.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// ✅ 处理 `Delete` 订单（仅限 `Admin`）
if ($_SESSION['user_level'] === 'Admin' && isset($_GET['action']) && $_GET['action'] == 'delete') {
    try {
        $stmt = $conn->prepare("DELETE FROM tbl_orders_a185125_pt2 WHERE fld_order_id = :oid");
        $stmt->bindParam(':oid', $_GET['oid'], PDO::PARAM_STR);
        $stmt->execute();
        header("Location: orders.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// ✅ 处理 `Edit` 订单的回显数据
$editrow = null;
if ($_SESSION['user_level'] === 'Admin' && isset($_GET['action']) && $_GET['action'] == 'edit') {
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_orders_a185125_pt2 WHERE fld_order_id = :oid");
        $stmt->bindParam(':oid', $_GET['oid'], PDO::PARAM_STR);
        $stmt->execute();
        $editrow = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Orders</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include_once 'nav_bar.php'; ?>

    <div class="container">
        <h2>Manage Orders</h2>

        <form action="orders_crud.php" method="post">
            <label>Order ID:</label>
            <input type="text" name="oid" class="form-control" value="<?php echo isset($editrow['fld_order_id']) ? $editrow['fld_order_id'] : generateOrderID(); ?>" readonly>

            <label>Order Date:</label>
            <input type="date" name="orderdate" class="form-control" value="<?php echo isset($editrow['fld_order_date']) ? date('Y-m-d', strtotime($editrow['fld_order_date'])) : date('Y-m-d'); ?>">

            <label>Staff:</label>
            <select name="staff" class="form-control">
                <?php
                try {
                    $stmt = $conn->prepare("SELECT fld_name FROM tbl_staffs_a185125_pt2");
                    $stmt->execute();
                    $staffs = $stmt->fetchAll();
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }

                foreach ($staffs as $staff) {
                    echo "<option value='{$staff['fld_name']}'";
                    if (isset($editrow['fld_staff_name']) && $editrow['fld_staff_name'] == $staff['fld_name']) {
                        echo " selected";
                    } elseif (!isset($editrow) && $_SESSION['user_name'] == $staff['fld_name']) {
                        echo " selected"; // 默认选当前员工
                    }
                    echo ">{$staff['fld_name']}</option>";
                }
                ?>
            </select>

            <label>Customer:</label>
            <select name="customer" class="form-control">
                <?php
                try {
                    $stmt = $conn->prepare("SELECT fld_name FROM tbl_customers_a185125_pt2");
                    $stmt->execute();
                    $customers = $stmt->fetchAll();
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }

                foreach ($customers as $customer) {
                    echo "<option value='{$customer['fld_name']}'";
                    if (isset($editrow['fld_customer_name']) && $editrow['fld_customer_name'] == $customer['fld_name']) {
                        echo " selected";
                    }
                    echo ">{$customer['fld_name']}</option>";
                }
                ?>
            </select>
            <br>

            <!-- ✅ `Staff` 只能 `Create` -->
            <?php if ($_SESSION['user_level'] === 'Admin' && isset($editrow)) { ?>
                <button type="submit" name="update" class="btn btn-warning">Update Order</button>
            <?php } else { ?>
                <button type="submit" name="create" class="btn btn-success">Create Order</button>
            <?php } ?>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
