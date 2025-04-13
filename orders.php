<?php
session_start();
include_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $stmt = $conn->prepare("SELECT * FROM tbl_orders_a185125_pt2");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include_once 'nav_bar.php'; ?>

    <div class="container">
        <h2>Orders</h2>

        <?php if ($_SESSION['user_level'] == 'Admin' || $_SESSION['user_level'] == 'Staff') { ?>
            <a href="orders_crud.php?action=create" class="btn btn-success">Create New Order</a>
        <?php } ?>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order date</th>
                    <th>Staff Name</th>
                    <th>Customer Name</th>
                    <th>Details</th>
                    <?php if ($_SESSION['user_level'] == 'Admin') { ?>
                    <th>Actions</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) { ?>
                <tr>
                    <td><?php echo $order['fld_order_id']; ?></td>
                    <td><?php echo $order['fld_order_date']; ?></td>
                    <td><?php echo $order['fld_staff_name']; ?></td>
                    <td><?php echo $order['fld_customer_name']; ?></td>
                    <td><a href="orders_details.php?oid=<?php echo $order['fld_order_id']; ?>" class="btn btn-info btn-sm">View</a></td>
                    <?php if ($_SESSION['user_level'] == 'Admin') { ?>
                    <td>
                        <a href="orders_crud.php?action=edit&oid=<?php echo $order['fld_order_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="orders_crud.php?action=delete&oid=<?php echo $order['fld_order_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirm delete?');">Delete</a>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
