<?php
session_start();
include 'auth.php';
include_once 'database.php'; 


if (isset($_GET['edit'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_products_a185125_pt2 WHERE fld_product_id = :pid");
        $stmt->bindParam(':pid', $_GET['edit'], PDO::PARAM_INT);
        $stmt->execute();
        $editrow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$editrow) {
            $editrow = [];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
   
    $editrow = [];
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$isAdmin = $_SESSION['user_level'] === 'Admin';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SkyTech Aviation Supplies : Products</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 900px;
            margin-top: 20px;
        }
        .table th, .table td {
            text-align: center;
        }
    </style>
</head>
<body>

    <?php include_once 'nav_bar.php'; ?>

    <div class="container">
        <h2 class="text-center">Manage Products</h2>
        <hr>

        <?php if ($isAdmin): ?>
        <div class="card p-4 mb-4 border">
            <h4 class="text-center"><?php echo isset($_GET['edit']) ? "Edit Product" : "Add New Product"; ?></h4>
            <form action="products_crud.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Product ID</label>
                    <input name="pid" type="text" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_product_id']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Name</label>
                    <input name="name" type="text" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_product_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Price (RM)</label>
                    <input name="price" type="number" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_price']; ?>" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <input name="type" type="text" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_type']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Brand</label>
                    <input name="brand" type="text" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_brand']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Warranty Period</label>
                    <input name="warranty" type="text" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_warranty_period']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Quantity</label>
                    <input name="quantity" type="number" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_quantity']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Product Image</label>
                    <input name="image" type="file" class="form-control" accept="image/png, image/jpeg">
                </div>

                <div class="text-center">
                    <?php if (isset($_GET['edit'])) { ?>
                        <button type="submit" name="update" class="btn btn-warning">Update</button>
                    <?php } else { ?>
                        <button type="submit" name="create" class="btn btn-success">Create</button>
                    <?php } ?>
                    <button type="reset" class="btn btn-secondary">Clear</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

     
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Brand</th>
                    <th>Warranty</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            try {
                $stmt = $conn->prepare("SELECT * FROM tbl_products_a185125_pt2");
                $stmt->execute();
                $result = $stmt->fetchAll();
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

            foreach($result as $readrow) {
            ?>
                <tr>
                    <td><?php echo $readrow['fld_product_id']; ?></td>
                    <td><?php echo $readrow['fld_product_name']; ?></td>
                    <td><?php echo number_format($readrow['fld_price'], 2); ?> RM</td>
                    <td><?php echo $readrow['fld_type']; ?></td>
                    <td><?php echo $readrow['fld_brand']; ?></td>
                    <td><?php echo $readrow['fld_warranty_period']; ?></td>
                    <td><?php echo $readrow['fld_quantity']; ?></td>
                    <td>
                        <a href="products_details.php?pid=<?php echo $readrow['fld_product_id']; ?>" class="btn btn-info btn-sm">Details</a>
                        <?php if ($isAdmin): ?>
                        <a href="products.php?edit=<?php echo $readrow['fld_product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="products_crud.php?delete=<?php echo $readrow['fld_product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete?');">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php
            }
            $conn = null;
            ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
