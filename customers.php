<?php
session_start();
include_once 'database.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (isset($_POST['create']) && $_SESSION['user_level'] === 'Admin') {
    try {
        $stmt = $conn->prepare("INSERT INTO tbl_customers_a185125_pt2 (fld_name, fld_contact, fld_address) 
                                VALUES (:name, :contact, :address)");
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':contact', $_POST['contact'], PDO::PARAM_STR);
        $stmt->bindParam(':address', $_POST['address'], PDO::PARAM_STR);
        $stmt->execute();
        header("Location: customers.php?message=Customer+created+successfully");
        exit();
    } catch (PDOException $e) {
        echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
    }
}

if (isset($_POST['update']) && $_SESSION['user_level'] === 'Admin') {
    try {
        $stmt = $conn->prepare("UPDATE tbl_customers_a185125_pt2 
                                SET fld_name = :name, fld_contact = :contact, fld_address = :address 
                                WHERE fld_customer_id = :cid");
        $stmt->bindParam(':cid', $_POST['cid'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':contact', $_POST['contact'], PDO::PARAM_STR);
        $stmt->bindParam(':address', $_POST['address'], PDO::PARAM_STR);
        $stmt->execute();
        header("Location: customers.php?message=Customer+updated+successfully");
        exit();
    } catch (PDOException $e) {
        echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
    }
}


if (isset($_GET['delete']) && $_SESSION['user_level'] === 'Admin') {
    try {
        $stmt = $conn->prepare("DELETE FROM tbl_customers_a185125_pt2 WHERE fld_customer_id = :cid");
        $stmt->bindParam(':cid', $_GET['delete'], PDO::PARAM_INT);
        $stmt->execute();
        header("Location: customers.php?message=Customer+deleted+successfully");
        exit();
    } catch (PDOException $e) {
        echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
    }
}


$editrow = null;
if (isset($_GET['edit']) && $_SESSION['user_level'] === 'Admin') {
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_customers_a185125_pt2 WHERE fld_customer_id = :cid");
        $stmt->bindParam(':cid', $_GET['edit'], PDO::PARAM_INT);
        $stmt->execute();
        $editrow = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customers Management</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { margin-top: 30px; }
        .table { margin-top: 20px; }
        .form-container {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <?php include_once 'nav_bar.php'; ?>

    <div class="container">
        <h2 class="text-center">Customers Management</h2>

        <?php if ($_SESSION['user_level'] === 'Admin') { ?>
            <div class="row">
                <div class="col-md-6 col-md-offset-3 form-container">
                    <form action="customers.php" method="post">
                        <input type="hidden" name="cid" value="<?php echo isset($editrow['fld_customer_id']) ? htmlspecialchars($editrow['fld_customer_id']) : ''; ?>">

                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" type="text" class="form-control"
                                   value="<?php echo isset($editrow['fld_name']) ? htmlspecialchars($editrow['fld_name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Contact</label>
                            <input name="contact" type="text" class="form-control"
                                   value="<?php echo isset($editrow['fld_contact']) ? htmlspecialchars($editrow['fld_contact']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" required><?php echo isset($editrow['fld_address']) ? htmlspecialchars($editrow['fld_address']) : ''; ?></textarea>
                        </div>

                        <div class="text-center">
                            <?php if (isset($editrow)) { ?>
                                <button type="submit" name="update" class="btn btn-warning">Update</button>
                            <?php } else { ?>
                                <button type="submit" name="create" class="btn btn-success">Create</button>
                            <?php } ?>
                            <button type="reset" class="btn btn-secondary">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>

        <hr>

    
        <table class="table table-bordered">
            <thead>
                <tr class="active">
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <?php if ($_SESSION['user_level'] === 'Admin') { ?>
                        <th>Actions</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $conn->prepare("SELECT * FROM tbl_customers_a185125_pt2");
                    $stmt->execute();
                    $result = $stmt->fetchAll();
                } catch (PDOException $e) {
                    echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
                }

                foreach ($result as $readrow) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($readrow['fld_customer_id']); ?></td>
                        <td><?php echo htmlspecialchars($readrow['fld_name']); ?></td>
                        <td><?php echo htmlspecialchars($readrow['fld_contact']); ?></td>
                        <td><?php echo htmlspecialchars($readrow['fld_address']); ?></td>

                        <?php if ($_SESSION['user_level'] === 'Admin') { ?>
                            <td>
                                <a href="customers.php?edit=<?php echo $readrow['fld_customer_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="customers.php?delete=<?php echo $readrow['fld_customer_id']; ?>" 
                                   onclick="return confirm('Are you sure to delete?');" class="btn btn-danger btn-sm">Delete</a>
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
