<?php
session_start();
include_once 'database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (empty($email) || empty($password)) {
            $error = "Please enter both email and password.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM tbl_staffs_a185125_pt2 WHERE fld_email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['fld_staff_id'];
                $_SESSION['user_name'] = $user['fld_name'];
                $_SESSION['user_level'] = $user['user_level'];

                header("Location: index.php");
                exit();
            } else {
                $error = "Incorrect email or password.";
            }
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SkyTech Aviation Supplies - Login</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 60px auto;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            background-color: #fff;
            text-align: center;
        }
        .login-logo {
            width: 120px;
            height: auto;
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="login-container">
           
            <img src="logo.jpg" class="login-logo" alt="SkyTech Aviation Logo">
            
            <h2>Login</h2>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

           
            <?php if (!empty($error)) { ?>
                <p class="text-danger text-center mt-3"><?php echo $error; ?></p>
            <?php } ?>
        </div>
    </div>

   
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
