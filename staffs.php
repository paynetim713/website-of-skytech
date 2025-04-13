<?php
session_start();
include 'auth.php'; 
include_once 'database.php';

if (!isAdmin()) {
    echo "<div class='alert alert-danger text-center'>Access Denied: You do not have permission to view this page.</div>";
    exit();
}

include_once 'staffs_crud.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SkyTech Aviation Supplies : Staffs</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 800px;
            margin-top: 20px;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            background-color: #fff;
        }
        .table {
            margin-top: 20px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
        }
        .btn-action {
            min-width: 75px;
        }
    </style>
</head>
<body>

    <?php include_once 'nav_bar.php'; ?>

    <div class="container">
        <h2 class="text-center">Manage Staff</h2>
        <hr>

        
        <?php if (isAdmin()): ?>
        <form action="staffs.php" method="post">
            <div class="form-group">
                <label>Staff ID</label>
                <input name="sid" type="text" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_staff_id']; ?>">
            </div>

            <div class="form-group">
                <label>Name</label>
                <input name="name" type="text" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_name']; ?>">
            </div>

            <div class="form-group">
                <label>Role</label>
                <input name="role" type="text" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_role']; ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input name="email" type="email" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_email']; ?>">
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input name="phone" type="text" class="form-control" value="<?php if(isset($_GET['edit'])) echo $editrow['fld_phone']; ?>">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input name="password" type="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>User Level</label>
                <select name="user_level" class="form-control">
                    <option value="Admin" <?php if(isset($_GET['edit']) && $editrow['user_level'] == 'Admin') echo 'selected'; ?>>Admin</option>
                    <option value="Staff" <?php if(isset($_GET['edit']) && $editrow['user_level'] == 'Staff') echo 'selected'; ?>>Staff</option>
                </select>
            </div>

            <?php if (isset($_GET['edit'])): ?>
                <input type="hidden" name="oldsid" value="<?php echo $editrow['fld_staff_id']; ?>">
                <button type="submit" name="update" class="btn btn-warning">Update</button>
            <?php else: ?>
                <button type="submit" name="create" class="btn btn-success">Create</button>
            <?php endif; ?>
            <button type="reset" class="btn btn-secondary">Clear</button>
        </form>
        <?php endif; ?>
        
        <hr>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>User Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt = $conn->prepare("SELECT * FROM tbl_staffs_a185125_pt2");
                    $stmt->execute();
                    $result = $stmt->fetchAll();
                } catch(PDOException $e) {
                    echo "<tr><td colspan='7' class='text-center text-danger'>Error: " . $e->getMessage() . "</td></tr>";
                }

                foreach($result as $readrow) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($readrow['fld_staff_id']); ?></td>
                    <td><?php echo htmlspecialchars($readrow['fld_name']); ?></td>
                    <td><?php echo htmlspecialchars($readrow['fld_role']); ?></td>
                    <td><?php echo htmlspecialchars($readrow['fld_email']); ?></td>
                    <td><?php echo htmlspecialchars($readrow['fld_phone']); ?></td>
                    <td><?php echo htmlspecialchars($readrow['user_level']); ?></td>
                    <td class="btn-group">
                        <a href="staffs.php?edit=<?php echo htmlspecialchars($readrow['fld_staff_id']); ?>" class="btn btn-warning btn-sm btn-action">Edit</a>
                        <a href="staffs.php?delete=<?php echo htmlspecialchars($readrow['fld_staff_id']); ?>" onclick="return confirm('Are you sure to delete?');" class="btn btn-danger btn-sm btn-action">Delete</a>
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
